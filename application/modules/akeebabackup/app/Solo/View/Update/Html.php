<?php
/**
 * @package        solo
 * @copyright      2014-2017 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license        GNU GPL version 3 or later
 */

namespace Solo\View\Update;

use Awf\Text\Text;
use Awf\Utils\Template;
use Solo\Model\Main;
use Solo\Model\Update;

class Html extends \Solo\View\Html
{
	public function display($tpl = null)
	{
		Template::addJs('media://js/solo/encryption.js', $this->container->application);
		Template::addJs('media://js/solo/update.js', $this->container->application);

		return parent::display($tpl);
	}

	public function onBeforeMain()
	{
		/** @var Update $model */
		$model = $this->getModel();

		/** @var Main $modelMain */
		$modelMain = $this->getModel('Main');

		$this->updateInfo      = $model->getUpdateInformation();
		$this->needsDownloadId = $modelMain->needsDownloadID();

		if ($this->updateInfo->get('stuck', 0))
		{
			$this->layout = 'stuck';
		}

		return true;
	}

	public function onBeforeDownload()
	{
		$token = $this->getContainer()->session->getCsrfToken()->getValue();
		$router = $this->getContainer()->router;
		$invalidDownloadID = \Solo\Helper\Escape::escapeJS(Text::_('SOLO_UPDATE_ERR_INVALIDDOWNLOADID'));
		$ajaxUrl = $router->route('index.php?view=update&task=downloader&format=raw');
		$nextStepUrl = $router->route('index.php?view=update&task=extract&token=' . $token);

		$js = <<< JS
Solo.loadScripts.push(function() {
	Solo.System.errorCallback = Solo.Update.downloadErrorCallback;
	Solo.Update.errorCallback = Solo.Update.downloadErrorCallback;
	Solo.Update.translations['ERR_INVALIDDOWNLOADID'] = '$invalidDownloadID';
	Solo.System.params.AjaxURL = '$ajaxUrl';
	Solo.Update.nextStepUrl = '$nextStepUrl';
	Solo.System.params.errorCallback = Solo.Update.downloadErrorCallback;
	Solo.Update.startDownload();		  
});

JS;

		$document = $this->getContainer()->application->getDocument();
		$document->addScriptDeclaration($js);

		return true;
	}

	public function onBeforeExtract()
	{
		$router = $this->getContainer()->router;
		$ajaxUrl = \Awf\Uri\Uri::base(false, $this->container) . 'restore.php';
		$finalizeUrl = $router->route('index.php?view=update&task=finalise');
		$password = $this->getModel()->getState('update_password', '');

		$js = <<< JS
Solo.loadScripts[Solo.loadScripts.length] = function () {
	Solo.System.documentReady(function() {
	    Solo.System.params.AjaxURL = '$ajaxUrl';
	    Solo.Update.finaliseUrl = '$finalizeUrl';
	    Solo.System.errorCallback = Solo.Update.extractErrorCallback;
	    Solo.Update.errorCallback = Solo.Update.extractErrorCallback;
	    Solo.System.params.password = '$password';
	    Solo.Update.pingExtract();
	});
};

JS;

		$document = $this->getContainer()->application->getDocument();
		$document->addScriptDeclaration($js);

		return true;
	}
}