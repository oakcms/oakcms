<?php
/**
 * @package        solo
 * @copyright      2014-2017 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license        GNU GPL version 3 or later
 */

namespace Solo\View\Discover;


use Akeeba\Engine\Factory;
use Awf\Text\Text;
use Awf\Uri\Uri;
use Awf\Utils\Template;
use Solo\Helper\Escape;

class Html extends \Solo\View\Html
{
	/**
	 * Push state variables before showing the main page
	 *
	 * @return  boolean
	 */
	public function onBeforeMain()
	{
		// Load the necessary Javascript
		Template::addJs('media://js/solo/configuration.js', $this->container->application);

		$model = $this->getModel();

		$directory = $model->getState('directory', '');

		if (empty($directory))
		{
			$config          = Factory::getConfiguration();
			$this->directory = $config->get('akeeba.basic.output_directory', '[DEFAULT_OUTPUT]');
		}
		else
		{
			$this->directory = $directory;
		}

		$this->loadCommonJavascript();

		return true;
	}

	/**
	 * Push state variables before showing the discovery page
	 *
	 * @return  boolean
	 */
	public function onBeforeDiscover()
	{
		$model = $this->getModel();

		$directory = $model->getState('directory', '');
		$this->setLayout('discover');

		$files = $model->getFiles();

		$this->files     = $files;
		$this->directory = $directory;

		return true;
	}

	/**
	 * Load the common Javascript for this feature: language strings, image locations
	 */
	protected function loadCommonJavascript()
	{
		$strings                                   = array();
		$strings['COM_AKEEBA_CONFIG_UI_BROWSE']    = Escape::escapeJS(Text::_('SOLO_COMMON_LBL_ROOT'));

		$browserURL = Escape::escapeJS($this->getContainer()->router->route('index.php?view=browser&tmpl=component&processfolder=1&folder='));

		$js = <<< JS
		
var akeeba_browser_callback = null;

Solo.loadScripts.push(function() {
		Solo.Configuration.translations['COM_AKEEBA_CONFIG_UI_BROWSE'] = '{$strings['COM_AKEEBA_CONFIG_UI_BROWSE']}';

		Solo.Configuration.URLs['browser'] = '$browserURL';
		
		Solo.System.addEventListener('btnBrowse', 'click', function(e){
			var element = document.getElementById('directory');
			var folder = element.value;
			Solo.Configuration.onBrowser(folder, element);
			e.preventDefault();
			return false;
		});
});

JS;

		$this->getContainer()->application->getDocument()->addScriptDeclaration($js);
	}

} 