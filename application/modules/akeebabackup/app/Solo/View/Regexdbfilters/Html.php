<?php
/**
 * @package        solo
 * @copyright      2014-2017 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license        GNU GPL version 3 or later
 */

namespace Solo\View\Regexdbfilters;


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
		// Get a JSON representation of the directories data
		/** @var \Solo\Model\Regexdbfilters $model */
		$model = $this->getModel();

		$root_info = $model->get_roots();
		$roots     = array();
		if (!empty($root_info))
		{
			// Loop all dir definitions
			foreach ($root_info as $def)
			{
				$roots[]   = $def->value;
				$options[] = Select::option($def->value, $def->text);
			}
		}
		$site_root         = '[SITEDB]';
		$attributes        = 'onchange="Solo.Regexdbfilters.activeRootChanged();"';
		$this->root_select = Select::genericList($options, 'root', $attributes, 'value', 'text', $site_root, 'active_root');
		$this->roots       = $roots;

		$json       = json_encode($model->get_regex_filters($site_root));
		$this->json = $json;

		// Get profile ID
		$profileid       = Platform::getInstance()->get_active_profile();
		$this->profileid = $profileid;

		// Get profile name
		$this->profilename = $this->escape(Platform::getInstance()->get_profile_name($profileid));

		// Load additional Javascript
		Template::addJs('media://js/solo/regexdbfilters.js', $this->container->application);
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
			'regextables',
			'regextabledata'
		);

		foreach ($filters as $type)
		{
			$key           = 'COM_AKEEBA_DBFILTER_TYPE_' . strtoupper($type);
			$strings[$key] = Escape::escapeJS(Text::_($key));
		}

		$ajaxURL = Escape::escapeJS($this->getContainer()->router->route('index.php?view=regexdbfilters&task=ajax&format=raw'));
		$json    = Escape::escapeJS($this->json, "'");

		$js = <<< JS
		
Solo.loadScripts.push(function() {
		Solo.Fsfilters.translations['COM_AKEEBA_FILEFILTERS_UIROOT'] = '{$strings['COM_AKEEBA_FILEFILTERS_UIROOT']}';
		Solo.Fsfilters.translations['COM_AKEEBA_FILEFILTERS_LABEL_UIERRORFILTER'] = '{$strings['COM_AKEEBA_FILEFILTERS_LABEL_UIERRORFILTER']}';

		Solo.Regexdbfilters.translations['COM_AKEEBA_FILEFILTERS_UIROOT'] = '{$strings['COM_AKEEBA_FILEFILTERS_UIROOT']}';
		Solo.Regexdbfilters.translations['COM_AKEEBA_FILEFILTERS_LABEL_UIERRORFILTER'] = '{$strings['COM_AKEEBA_FILEFILTERS_LABEL_UIERRORFILTER']}';

		Solo.Regexdbfilters.translations['COM_AKEEBA_DBFILTER_TYPE_REGEXTABLES'] = '{$strings['COM_AKEEBA_DBFILTER_TYPE_REGEXTABLES']}';
		Solo.Regexdbfilters.translations['COM_AKEEBA_DBFILTER_TYPE_REGEXTABLEDATA'] = '{$strings['COM_AKEEBA_DBFILTER_TYPE_REGEXTABLEDATA']}';

		Solo.System.params.AjaxURL = '$ajaxURL';

		Solo.Regexdbfilters.render(eval($json));
});

JS;

		$this->getContainer()->application->getDocument()->addScriptDeclaration($js);
	}

} 