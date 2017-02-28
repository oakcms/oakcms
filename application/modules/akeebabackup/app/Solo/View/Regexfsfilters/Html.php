<?php
/**
 * @package        solo
 * @copyright      2014-2017 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license        GNU GPL version 3 or later
 */

namespace Solo\View\Regexfsfilters;


use Akeeba\Engine\Factory;
use Akeeba\Engine\Platform;
use Awf\Html\Select;
use Awf\Text\Text;
use Awf\Uri\Uri;
use Awf\Utils\Template;
use Solo\Helper\Escape;

class Html extends \Solo\View\Html
{
	/**
	 * Execute before displaying the main and only page of the off-site files inclusion page
	 *
	 * @return  boolean
	 */
	public function onBeforeMain()
	{
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
		$attributes        = 'onchange="Solo.Regexfsfilters.activeRootChanged();"';
		$this->root_select = Select::genericList($options, 'root', $attributes, 'value', 'text', $site_root, 'active_root');
		$this->roots       = $roots;

		// Get a JSON representation of the directories data
		/** @var \Solo\Model\Regexfsfilters $model */
		$model       = $this->getModel();
		$directories = $model->get_regex_filters($site_root);
		$json        = json_encode($directories);
		$this->json  = $json;

		// Get profile ID
		$profileid       = Platform::getInstance()->get_active_profile();
		$this->profileid = $profileid;

		// Get profile name
		$this->profilename = $this->escape(Platform::getInstance()->get_profile_name($profileid));

		// Load additional Javascript
		Template::addJs('media://js/solo/regexfsfilters.js', $this->container->application);
		Template::addJs('media://js/solo/fsfilters.js', $this->container->application);

		$this->loadCommonJavascript();

		return true;
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
			'files'
		);

		foreach ($filters as $type)
		{
			$key           = 'COM_AKEEBA_FILEFILTERS_TYPE_' . strtoupper($type);
			$strings[$key] = Escape::escapeJS(Text::_($key));
		}

		$ajaxURL = Escape::escapeJS($this->getContainer()->router->route('index.php?view=regexfsfilters&task=ajax&format=raw'));
		$json    = Escape::escapeJS($this->json, "'");

		$js = <<< JS
		
Solo.loadScripts.push(function() {
		Solo.Fsfilters.translations['COM_AKEEBA_FILEFILTERS_UIROOT'] = '{$strings['COM_AKEEBA_FILEFILTERS_UIROOT']}';
		Solo.Fsfilters.translations['COM_AKEEBA_FILEFILTERS_LABEL_UIERRORFILTER'] = '{$strings['COM_AKEEBA_FILEFILTERS_LABEL_UIERRORFILTER']}';

		Solo.Regexfsfilters.translations['COM_AKEEBA_FILEFILTERS_UIROOT'] = '{$strings['COM_AKEEBA_FILEFILTERS_UIROOT']}';
		Solo.Regexfsfilters.translations['COM_AKEEBA_FILEFILTERS_LABEL_UIERRORFILTER'] = '{$strings['COM_AKEEBA_FILEFILTERS_LABEL_UIERRORFILTER']}';

		Solo.Regexfsfilters.translations['COM_AKEEBA_FILEFILTERS_TYPE_REGEXDIRECTORIES'] = '{$strings['COM_AKEEBA_FILEFILTERS_TYPE_DIRECTORIES']}';
		Solo.Regexfsfilters.translations['COM_AKEEBA_FILEFILTERS_TYPE_REGEXSKIPFILES'] = '{$strings['COM_AKEEBA_FILEFILTERS_TYPE_SKIPFILES']}';
		Solo.Regexfsfilters.translations['COM_AKEEBA_FILEFILTERS_TYPE_REGEXSKIPDIRS'] = '{$strings['COM_AKEEBA_FILEFILTERS_TYPE_SKIPDIRS']}';
		Solo.Regexfsfilters.translations['COM_AKEEBA_FILEFILTERS_TYPE_REGEXFILES'] = '{$strings['COM_AKEEBA_FILEFILTERS_TYPE_FILES']}';

		Solo.System.params.AjaxURL = '$ajaxURL';

		Solo.Regexfsfilters.render(eval($json));
});

JS;

		$this->getContainer()->application->getDocument()->addScriptDeclaration($js);
	}

} 