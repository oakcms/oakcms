<?php
/**
 * @package        solo
 * @copyright      2014-2017 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license        GNU GPL version 3 or later
 */

namespace Solo\View\Extradirs;

use Akeeba\Engine\Platform;
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
		/** @var \Solo\Model\Extradirs $model */
		$model       = $this->getModel();
		$directories = $model->get_directories();
		$json        = json_encode($directories);
		$this->json  = $json;

		// Get profile ID
		$profileid       = Platform::getInstance()->get_active_profile();
		$this->profileid = $profileid;

		// Get profile name
		$this->profilename = $this->escape(Platform::getInstance()->get_profile_name($profileid));

		// Load additional Javascript
		Template::addJs('media://js/solo/extradirs.js', $this->container->application);
		Template::addJs('media://js/solo/fsfilters.js', $this->container->application);
		Template::addJs('media://js/solo/configuration.js', $this->container->application);

		$this->loadCommonJavascript();

		return true;
	}

	/**
	 * Load the common Javascript for this feature: language strings, image locations
	 */
	protected function loadCommonJavascript()
	{
		$router                                                = $this->getContainer()->router;
		$strings                                               = array();
		$strings['COM_AKEEBA_FILEFILTERS_UIROOT']              = Escape::escapeJS(Text::_('COM_AKEEBA_FILEFILTERS_LABEL_UIROOT'));
		$strings['COM_AKEEBA_FILEFILTERS_LABEL_UIERRORFILTER'] = Escape::escapeJS(Text::_('COM_AKEEBA_FILEFILTERS_LABEL_UIERRORFILTER'));

		$loadingGif = Escape::escapeJS(Uri::base(false, $this->container) . 'media/loading.gif');
		$ajaxURL    = Escape::escapeJS($router->route('index.php?view=extradirs&task=ajax&format=raw'));
		$browserUrl = Escape::escapeJS($router->route('index.php?view=browser&tmpl=component&processfolder=1&folder='));
		$json       = Escape::escapeJS($this->json, "'");

		$js = <<< JS
		
Solo.loadScripts.push(function() {
		Solo.Fsfilters.translations['COM_AKEEBA_FILEFILTERS_UIROOT'] = '{$strings['COM_AKEEBA_FILEFILTERS_UIROOT']}';
		Solo.Fsfilters.translations['COM_AKEEBA_FILEFILTERS_LABEL_UIERRORFILTER'] = '{$strings['COM_AKEEBA_FILEFILTERS_LABEL_UIERRORFILTER']}';

		Solo.Fsfilters.loadingGif = '$loadingGif';
		Solo.System.params.AjaxURL = '$ajaxURL';
		Solo.Configuration.URLs['browser'] = '$browserUrl';
		
		Solo.Extradirs.render(eval($json));
});

JS;

		$this->getContainer()->application->getDocument()->addScriptDeclaration($js);
	}

} 