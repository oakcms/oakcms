<?php
/**
 * @package        solo
 * @copyright      2014-2017 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license        GNU GPL version 3 or later
 */

namespace Solo\View\Restore;

use Akeeba\Engine\Factory;
use Awf\Mvc\View;
use Awf\Text\Text;
use Awf\Uri\Uri;
use Awf\Utils\Template;
use Solo\Helper\Escape;
use Solo\View\Html as BaseHtml;

class Html extends BaseHtml
{
	public function display($tpl = null)
	{
		Template::addJs('media://js/solo/encryption.js', $this->container->application);
		Template::addJs('media://js/solo/configuration.js', $this->container->application);
		Template::addJs('media://js/solo/restore.js', $this->container->application);

		$this->loadCommonJavascript();

		return parent::display($tpl);
	}

	public function onBeforeMain()
	{
		/** @var \Solo\Model\Restore $model */
		$model = $this->getModel();

		$this->id              = $model->getState('id', 0);
		$this->ftpparams       = $model->getFTPParams();
		$this->extractionmodes = $model->getExtractionModes();

		$js = <<< JS
// Callback routine to close the browser dialog
var akeeba_browser_callback = null;

Solo.loadScripts.push(function() {
	// This element doesn't exist in the current version of the code
	try 
	{
		Solo.System.addEventListener('ftp-browse', 'click', function(e) {
			Solo.Configuration.FtpBrowser.initialise('ftp.initial_directory', 'ftp');
		});		
	}
	catch (e)
	{
	}

	Solo.System.addEventListener('var[ftp.test]', 'click', function(e) {
		Solo.Configuration.FtpTest.testConnection('ftp.test', 'ftp');
	});

	Solo.System.addEventListener(document.getElementById('procengine'), 'change', Solo.Restore.onProcEngineChange);

	Solo.Restore.onProcEngineChange();  
});

JS;

		$this->getContainer()->application->getDocument()->addScriptDeclaration($js);

		return true;
	}

	public function onBeforeStart()
	{
		/** @var \Solo\Model\Restore $model */
		$model = $this->getModel();

		$inCMS = $this->container->segment->get('insideCMS', false);

		if ($inCMS)
		{
			$this->siteURL = $this->container->appConfig->get('cms_url', '');
		}
		else
		{
			$this->siteURL = Factory::getConfiguration()->get('akeeba.platform.site_url', '');
		}

		$this->siteURL = trim($this->siteURL);

		$this->setLayout('restore');

		$password     = Escape::escapeJS($model->getState('password'));
		$urlToRestore = Escape::escapeJS(Uri::base(false, $this->container) . 'restore.php');
		$mainURL      = Escape::escapeJS($this->getContainer()->router->route('index.php'));

		$js = <<< JS
// Callback routine to close the browser dialog
var akeeba_browser_callback = null;

Solo.loadScripts.push(function() {
	Solo.Restore.password = '$password';
	Solo.Restore.ajaxURL = '$urlToRestore';
	Solo.Restore.mainURL = '$mainURL';

	Solo.Restore.errorCallback = Solo.Restore.errorCallbackDefault;

	Solo.Restore.pingRestoration();
});

JS;

		$this->getContainer()->application->getDocument()->addScriptDeclaration($js);

		return true;
	}

	public function loadCommonJavascript()
	{
		$keys    = array(
			'COM_AKEEBA_CONFIG_UI_BROWSE'           => 'COM_AKEEBA_CONFIG_UI_BROWSE',
			'COM_AKEEBA_CONFIG_UI_CONFIG'           => 'COM_AKEEBA_CONFIG_UI_CONFIG',
			'COM_AKEEBA_CONFIG_UI_REFRESH'          => 'COM_AKEEBA_CONFIG_UI_REFRESH',
			'COM_AKEEBA_CONFIG_UI_FTPBROWSER_TITLE' => 'COM_AKEEBA_CONFIG_UI_FTPBROWSER_TITLE',
			'COM_AKEEBA_FILEFILTERS_LABEL_UIROOT'   => 'SOLO_COMMON_LBL_ROOT',
			'COM_AKEEBA_CONFIG_DIRECTFTP_TEST_OK'   => 'COM_AKEEBA_CONFIG_DIRECTFTP_TEST_OK',
			'COM_AKEEBA_CONFIG_DIRECTFTP_TEST_FAIL' => 'COM_AKEEBA_CONFIG_DIRECTFTP_TEST_FAIL',
			'COM_AKEEBA_BACKUP_TEXT_LASTRESPONSE'   => 'COM_AKEEBA_BACKUP_TEXT_LASTRESPONSE',
		);
		$strings = array();

		foreach ($keys as $k => $v)
		{
			$strings[$k] = Escape::escapeJS(Text::_($v));
		}

		$router        = $this->getContainer()->router;
		$browserURL    = Escape::escapeJS($router->route('index.php?view=browser&tmpl=component&processfolder=1&folder='));
		$ftpBrowserURL = Escape::escapeJS($router->route('index.php?view=ftpbrowser'));
		$testFTPURL    = Escape::escapeJS($router->route('index.php?view=configuration&task=testftp'));

		$js = <<< JS
Solo.loadScripts.push(function() {
	Solo.Configuration.translations['COM_AKEEBA_CONFIG_UI_BROWSE'] = '{$strings['COM_AKEEBA_CONFIG_UI_BROWSE']}';
	Solo.Configuration.translations['COM_AKEEBA_CONFIG_UI_CONFIG'] = '{$strings['COM_AKEEBA_CONFIG_UI_CONFIG']}';
	Solo.Configuration.translations['COM_AKEEBA_CONFIG_UI_REFRESH'] = '{$strings['COM_AKEEBA_CONFIG_UI_REFRESH']}';
	Solo.Configuration.translations['COM_AKEEBA_CONFIG_UI_FTPBROWSER_TITLE'] = '{$strings['COM_AKEEBA_CONFIG_UI_FTPBROWSER_TITLE']}';
	Solo.Configuration.translations['COM_AKEEBA_FILEFILTERS_LABEL_UIROOT'] = '{$strings['COM_AKEEBA_FILEFILTERS_LABEL_UIROOT']}';
	Solo.Configuration.translations['COM_AKEEBA_CONFIG_DIRECTFTP_TEST_OK'] = '{$strings['COM_AKEEBA_CONFIG_DIRECTFTP_TEST_OK']}';
	Solo.Configuration.translations['COM_AKEEBA_CONFIG_DIRECTFTP_TEST_FAIL'] = '{$strings['COM_AKEEBA_CONFIG_DIRECTFTP_TEST_FAIL']}';
	Solo.Configuration.translations['COM_AKEEBA_BACKUP_TEXT_LASTRESPONSE'] = '{$strings['COM_AKEEBA_BACKUP_TEXT_LASTRESPONSE']}';
	
	Solo.Restore.translations['COM_AKEEBA_BACKUP_TEXT_LASTRESPONSE'] = '{$strings['COM_AKEEBA_BACKUP_TEXT_LASTRESPONSE']}';
	
	Solo.Configuration.URLs['browser'] = '$browserURL';
	Solo.Configuration.URLs['ftpBrowser'] = '$ftpBrowserURL';
	Solo.Configuration.URLs['testFtp'] = '$testFTPURL';
});
JS;

		$this->getContainer()->application->getDocument()->addScriptDeclaration($js);
	}

} 