<?php
/**
 * @package        solo
 * @copyright      2014-2017 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license        GNU GPL version 3 or later
 */

namespace Solo\View\Fsfilters;


use Akeeba\Engine\Factory;
use Akeeba\Engine\Platform;
use Awf\Html\Select;
use Awf\Router\Router;
use Awf\Text\Text;
use Awf\Uri\Uri;
use Awf\Utils\Template;
use Solo\Helper\Escape;

class Html extends \Solo\View\Html
{
	/**
	 * Prepare the view data for the main task
	 *
	 * @return  boolean
	 */
	public function onBeforeMain()
	{
		$model = $this->getModel();
		$task  = $model->getState('browse_task', 'normal');

		$router = $this->container->router;

		// Add custom submenus
		$toolbar = $this->container->application->getDocument()->getToolbar();
		$toolbar->addSubmenuFromDefinition(array(
			'name'  => 'normal',
			'title' => Text::_('COM_AKEEBA_FILEFILTERS_LABEL_NORMALVIEW'),
			'url'   => $router->route('index.php?view=fsfilters&task=normal'),
		));
		$toolbar->addSubmenuFromDefinition(array(
			'name'  => 'tabular',
			'title' => Text::_('COM_AKEEBA_FILEFILTERS_LABEL_TABULARVIEW'),
			'url'   => $router->route('index.php?view=fsfilters&task=tabular'),
		));

		// Get a JSON representation of the available roots
		$filters   = Factory::getFilters();
		$root_info = $filters->getInclusions('dir');
		$roots     = array();
		$options   = array();

		if (!empty($root_info))
		{
			// Loop all dir definitions
			foreach ($root_info as $dir_definition)
			{
				if (is_null($dir_definition[1]))
				{
					// Site root definition has a null element 1. It is always pushed on top of the stack.
					array_unshift($roots, $dir_definition[0]);
				}
				else
				{
					$roots[] = $dir_definition[0];
				}

				$options[] = Select::option($dir_definition[0], $dir_definition[0]);
			}
		}

		$site_root         = $roots[0];
		$attributes        = 'onchange="Solo.Fsfilters.activeRootChanged();"';
		$this->root_select = Select::genericList($options, 'root', $attributes, 'value', 'text', $site_root, 'active_root');
		$this->roots       = $roots;

		switch ($task)
		{
			case 'normal':
			default:
				$this->setLayout('default');

				// Get a JSON representation of the directory data
				$model      = $this->getModel();
				$json       = json_encode($model->make_listing($site_root, array(), ''));
				$this->json = $json;
				break;

			case 'tabular':
				$this->setLayout('tabular');

				// Get a JSON representation of the tabular filter data
				$model      = $this->getModel();
				$json       = json_encode($model->get_filters($site_root));
				$this->json = $json;

				break;
		}

		// Get profile ID
		$profileid       = Platform::getInstance()->get_active_profile();
		$this->profileid = $profileid;

		// Get profile name
		$this->profilename = $this->escape(Platform::getInstance()->get_profile_name($profileid));

		// Load additional Javascript
		Template::addJs('media://js/solo/fsfilters.js', $this->container->application);

		// Load the Javascript language strings
		$this->loadCommonJavascript();

		return true;
	}

	/**
	 * The normal task simply calls the method for the main task
	 *
	 * @return  boolean
	 */
	public function onBeforeNormal()
	{
		return $this->onBeforeMain();
	}

	/**
	 * The tabular task simply calls the method for the main task
	 *
	 * @return  boolean
	 */
	public function onBeforeTabular()
	{
		return $this->onBeforeMain();
	}

	/**
	 * Load the common Javascript for this feature: language strings, image locations
	 */
	protected function loadCommonJavascript()
	{
		$strings                                               = array();
		$strings['COM_AKEEBA_FILEFILTERS_UIROOT']              = Escape::escapeJS(Text::_('COM_AKEEBA_FILEFILTERS_LABEL_UIROOT'));
		$strings['COM_AKEEBA_FILEFILTERS_LABEL_UIERRORFILTER'] = Escape::escapeJS(Text::_('COM_AKEEBA_FILEFILTERS_LABEL_UIERRORFILTER'));

		$filters = array(
			'directories',
			'skipfiles',
			'skipdirs',
			'files',
			'directories_all',
			'skipfiles_all',
			'skipdirs_all',
			'files_all',
			'applytoalldirs',
			'applytoallfiles'
		);

		foreach ($filters as $type)
		{
			$key           = 'COM_AKEEBA_FILEFILTERS_TYPE_' . strtoupper($type);
			$strings[$key] = Escape::escapeJS(Text::_($key));
		}

		$loadingGif = Escape::escapeJS(Uri::base(false, $this->container) . 'media/loading.gif');
		$ajaxURL = Escape::escapeJS($this->getContainer()->router->route('index.php?view=fsfilters&task=ajax&format=raw'));
		$json = Escape::escapeJS($this->json,"'");

		$js = <<< JS
		
var akeeba_fsfilter_data = eval($json);

Solo.loadScripts.push(function() {
		Solo.Fsfilters.translations['COM_AKEEBA_FILEFILTERS_UIROOT'] = '{$strings['COM_AKEEBA_FILEFILTERS_UIROOT']}';
		Solo.Fsfilters.translations['COM_AKEEBA_FILEFILTERS_LABEL_UIERRORFILTER'] = '{$strings['COM_AKEEBA_FILEFILTERS_LABEL_UIERRORFILTER']}';

		Solo.Fsfilters.translations['COM_AKEEBA_FILEFILTERS_TYPE_DIRECTORIES'] = '{$strings['COM_AKEEBA_FILEFILTERS_TYPE_DIRECTORIES']}';
		Solo.Fsfilters.translations['COM_AKEEBA_FILEFILTERS_TYPE_SKIPFILES'] = '{$strings['COM_AKEEBA_FILEFILTERS_TYPE_SKIPFILES']}';
		Solo.Fsfilters.translations['COM_AKEEBA_FILEFILTERS_TYPE_SKIPDIRS'] = '{$strings['COM_AKEEBA_FILEFILTERS_TYPE_SKIPDIRS']}';
		Solo.Fsfilters.translations['COM_AKEEBA_FILEFILTERS_TYPE_FILES'] = '{$strings['COM_AKEEBA_FILEFILTERS_TYPE_FILES']}';
		Solo.Fsfilters.translations['COM_AKEEBA_FILEFILTERS_TYPE_DIRECTORIES_ALL'] = '{$strings['COM_AKEEBA_FILEFILTERS_TYPE_DIRECTORIES_ALL']}';
		Solo.Fsfilters.translations['COM_AKEEBA_FILEFILTERS_TYPE_SKIPFILES_ALL'] = '{$strings['COM_AKEEBA_FILEFILTERS_TYPE_SKIPFILES_ALL']}';
		Solo.Fsfilters.translations['COM_AKEEBA_FILEFILTERS_TYPE_SKIPDIRS_ALL'] = '{$strings['COM_AKEEBA_FILEFILTERS_TYPE_SKIPDIRS_ALL']}';
		Solo.Fsfilters.translations['COM_AKEEBA_FILEFILTERS_TYPE_FILES_ALL'] = '{$strings['COM_AKEEBA_FILEFILTERS_TYPE_FILES_ALL']}';
		Solo.Fsfilters.translations['COM_AKEEBA_FILEFILTERS_TYPE_APPLYTOALLDIRS'] = '{$strings['COM_AKEEBA_FILEFILTERS_TYPE_APPLYTOALLDIRS']}';
		Solo.Fsfilters.translations['COM_AKEEBA_FILEFILTERS_TYPE_APPLYTOALLFILES'] = '{$strings['COM_AKEEBA_FILEFILTERS_TYPE_APPLYTOALLFILES']}';

		Solo.Fsfilters.loadingGif = '$loadingGif';
		Solo.System.params.AjaxURL = '$ajaxURL';
});

JS;

		$this->getContainer()->application->getDocument()->addScriptDeclaration($js);
	}
}