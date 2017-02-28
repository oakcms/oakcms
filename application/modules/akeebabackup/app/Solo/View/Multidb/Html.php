<?php
/**
 * @package		solo
 * @copyright	2014-2017 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license		GNU GPL version 3 or later
 */

namespace Solo\View\Multidb;

use Akeeba\Engine\Platform;
use Awf\Text\Text;
use Awf\Uri\Uri;
use Awf\Utils\Template;
use Solo\Helper\Escape;
use Solo\Model\Multidb;

class Html extends \Solo\View\Html
{
	public function onBeforeMain()
	{
		// Get a JSON representation of the database connection data
		/** @var Multidb $model */
		$model = $this->getModel();
		$databases = $model->get_databases();
		$json = json_encode($databases);
		$this->json =  $json;

		// Get profile ID
		$profileid = Platform::getInstance()->get_active_profile();
		$this->profileid =  $profileid;

		// Get profile name
		$this->profilename = $this->escape(Platform::getInstance()->get_profile_name($this->profileid));

		// Get the possible database drivers
		$this->dbDriversOptions = $model->getDatabaseDriverOptions();

		// Load additional Javascript
		Template::addJs('media://js/solo/multidb.js', $this->container->application);
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

		$keys = array('COM_AKEEBA_MULTIDB_GUI_LBL_HOST', 'COM_AKEEBA_MULTIDB_GUI_LBL_PORT', 'COM_AKEEBA_MULTIDB_GUI_LBL_USERNAME', 'COM_AKEEBA_MULTIDB_GUI_LBL_PASSWORD',
		              'COM_AKEEBA_MULTIDB_GUI_LBL_DATABASE', 'COM_AKEEBA_MULTIDB_GUI_LBL_PREFIX', 'COM_AKEEBA_MULTIDB_GUI_LBL_TEST', 'COM_AKEEBA_MULTIDB_GUI_LBL_SAVE',
		              'COM_AKEEBA_MULTIDB_GUI_LBL_CANCEL', 'COM_AKEEBA_MULTIDB_GUI_LBL_LOADING', 'COM_AKEEBA_MULTIDB_GUI_LBL_CONNECTOK',
		              'COM_AKEEBA_MULTIDB_GUI_LBL_CONNECTFAIL', 'COM_AKEEBA_MULTIDB_GUI_LBL_SAVEFAIL', 'COM_AKEEBA_MULTIDB_GUI_LBL_DRIVER');

		$translations = "";

		foreach ($keys as $key)
		{
			$t = Escape::escapeJS(Text::_($key));
			$translations .= "\tSolo.Multidb.translations['$key'] = '$t';\n";
		}

		$loadingGif = Escape::escapeJS(Uri::base(false, $this->container) . 'media/loading.gif');
		$ajaxURL = Escape::escapeJS($this->getContainer()->router->route('index.php?view=multidb&task=ajax&format=raw'));
		$json = Escape::escapeJS($this->json,"'");

		$js = <<< JS

Solo.loadScripts.push(function() {
		Solo.Fsfilters.translations['COM_AKEEBA_FILEFILTERS_UIROOT'] = '{$strings['COM_AKEEBA_FILEFILTERS_UIROOT']}';
		Solo.Fsfilters.translations['COM_AKEEBA_FILEFILTERS_LABEL_UIERRORFILTER'] = '{$strings['COM_AKEEBA_FILEFILTERS_LABEL_UIERRORFILTER']}';
		Solo.Fsfilters.loadingGif = '$loadingGif';

$translations
		
		Solo.System.params.AjaxURL = '$ajaxURL';
		Solo.Multidb.render(eval($json));
});

JS;

		$this->getContainer()->application->getDocument()->addScriptDeclaration($js);
	}

} 