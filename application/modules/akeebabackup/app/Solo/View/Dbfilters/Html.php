<?php
/**
 * @package        solo
 * @copyright      2014-2017 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license        GNU GPL version 3 or later
 */

namespace Solo\View\Dbfilters;

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
			'url'   => $router->route('index.php?view=dbfilters&task=normal'),
		));
		$toolbar->addSubmenuFromDefinition(array(
			'name'  => 'tabular',
			'title' => Text::_('COM_AKEEBA_FILEFILTERS_LABEL_TABULARVIEW'),
			'url'   => $router->route('index.php?view=dbfilters&task=tabular'),
		));

		// Get a JSON representation of the available roots
		/** @var \Solo\Model\Dbfilters $model */
		$model     = $this->getModel();
		$root_info = $model->get_roots();
		$roots     = array();
		$options   = array();

		if (!empty($root_info))
		{
			// Loop all db definitions
			foreach ($root_info as $def)
			{
				$roots[]   = $def->value;
				$options[] = Select::option($def->value, $def->text);
			}
		}

		$site_root         = $roots[0];
		$attributes        = 'onchange="Solo.Dbfilters.activeRootChanged();"';
		$this->root_select = Select::genericList($options, 'root', $attributes, 'value', 'text', $site_root, 'active_root');
		$this->roots       = $roots;

		switch ($task)
		{
			case 'normal':
			default:
				$this->setLayout('default');

				// Get a JSON representation of the directory data
				$model      = $this->getModel();
				$json       = json_encode($model->make_listing($site_root));
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
		Template::addJs('media://js/solo/dbfilters.js', $this->container->application);

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
		$strings['COM_AKEEBA_FILEFILTERS_LABEL_UIROOT']        = Escape::escapeJS(Text::_('COM_AKEEBA_FILEFILTERS_LABEL_UIROOT'));
		$strings['COM_AKEEBA_FILEFILTERS_LABEL_UIERRORFILTER'] = Escape::escapeJS(Text::_('COM_AKEEBA_FILEFILTERS_LABEL_UIERRORFILTER'));

		$filters = array('tables', 'tabledata');
		foreach ($filters as $type)
		{
			$key           = 'COM_AKEEBA_DBFILTER_TYPE_' . strtoupper($type);
			$strings[$key] = Escape::escapeJS(Text::_($key));
		}

		$types = array('misc', 'table', 'view', 'procedure', 'function', 'trigger');
		foreach ($types as $type)
		{
			$key           = 'COM_AKEEBA_DBFILTER_TABLE_' . strtoupper($type);
			$strings[$key] = Escape::escapeJS(Text::_($key));
		}

		$loadingGif = Escape::escapeJS(Uri::base(false, $this->container) . 'media/loading.gif');
		$ajaxURL    = Escape::escapeJS($this->getContainer()->router->route('index.php?view=dbfilters&task=ajax&format=raw'));
		$json       = Escape::escapeJS($this->json, "'");

		$js = <<< JS
		
var akeeba_dbfilter_data = eval($json);

Solo.loadScripts.push(function() {
		Solo.Dbfilters.translations['COM_AKEEBA_FILEFILTERS_LABEL_UIROOT'] = '{$strings['COM_AKEEBA_FILEFILTERS_LABEL_UIROOT']}';
		Solo.Dbfilters.translations['COM_AKEEBA_FILEFILTERS_LABEL_UIERRORFILTER'] = '{$strings['COM_AKEEBA_FILEFILTERS_LABEL_UIERRORFILTER']}';
		Solo.Fsfilters.translations['COM_AKEEBA_FILEFILTERS_LABEL_UIERRORFILTER'] = '{$strings['COM_AKEEBA_FILEFILTERS_LABEL_UIERRORFILTER']}';

		Solo.Dbfilters.translations['COM_AKEEBA_DBFILTER_TYPE_TABLES'] = '{$strings['COM_AKEEBA_DBFILTER_TYPE_TABLES']}';
		Solo.Dbfilters.translations['COM_AKEEBA_DBFILTER_TYPE_TABLEDATA'] = '{$strings['COM_AKEEBA_DBFILTER_TYPE_TABLEDATA']}';

		Solo.Dbfilters.translations['COM_AKEEBA_DBFILTER_TABLE_MISC'] = '{$strings['COM_AKEEBA_DBFILTER_TABLE_MISC']}';
		Solo.Dbfilters.translations['COM_AKEEBA_DBFILTER_TABLE_TABLE'] = '{$strings['COM_AKEEBA_DBFILTER_TABLE_TABLE']}';
		Solo.Dbfilters.translations['COM_AKEEBA_DBFILTER_TABLE_VIEW'] = '{$strings['COM_AKEEBA_DBFILTER_TABLE_VIEW']}';
		Solo.Dbfilters.translations['COM_AKEEBA_DBFILTER_TABLE_PROCEDURE'] = '{$strings['COM_AKEEBA_DBFILTER_TABLE_PROCEDURE']}';
		Solo.Dbfilters.translations['COM_AKEEBA_DBFILTER_TABLE_FUNCTION'] = '{$strings['COM_AKEEBA_DBFILTER_TABLE_FUNCTION']}';
		Solo.Dbfilters.translations['COM_AKEEBA_DBFILTER_TABLE_TRIGGER'] = '{$strings['COM_AKEEBA_DBFILTER_TABLE_TRIGGER']}';

		Solo.Dbfilters.loadingGif = '$loadingGif';
		Solo.System.params.AjaxURL = '$ajaxURL';
});

JS;

		$this->getContainer()->application->getDocument()->addScriptDeclaration($js);
	}

}