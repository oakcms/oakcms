<?php
/**
 * @package     Solo
 * @copyright   2014-2017 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license     GNU GPL version 3 or later
 */

namespace Solo\View\Main;

use Akeeba\Engine\Platform;
use Awf\Mvc\Model;
use Awf\Mvc\View;
use Awf\Utils\Template;
use Solo\Model\Main;
use Solo\Model\Stats;
use Solo\Model\Update;

class Html extends \Solo\View\Html
{
	public $profile;
	public $profileList;
	public $quickIconProfiles;
	public $latestBackupDetails;
	public $configUrl;
	public $backupOutputUrl;
	public $needsDownloadId;
	public $warnCoreDownloadId;
	public $frontEndSecretWordIssue;
	public $newSecretWord;
	public $desktop_notifications;
	public $statsIframe;
	public $checkMbstring = true;
	public $aclChecks = array();

	/**
	 * Do I have stuck updates pending?
	 *
	 * @var  bool
	 */
	public $stuckUpdates = false;

	public function onBeforeMain()
	{
		/** @var Main $model */
		$model   = $this->getModel();
		$session = $this->container->segment;

		$this->profile             = Platform::getInstance()->get_active_profile();
		$this->profileList         = $model->getProfileList();
		$this->quickIconProfiles   = $model->getQuickIconProfiles();
		$this->latestBackupDetails = $model->getLatestBackupDetails();

		if (!$this->container->segment->get('insideCMS', false))
		{
			$this->configUrl = $model->getConfigUrl();
		}
		$this->backupOutputUrl = $model->getBackupOutputUrl();

		$this->needsDownloadId    = $model->needsDownloadID();
		$this->warnCoreDownloadId = $model->mustWarnAboutDownloadIdInCore();

		$this->checkMbstring           = $model->checkMbstring();
		$this->frontEndSecretWordIssue = $model->getFrontendSecretWordError();
		$this->newSecretWord           = $session->get('newSecretWord', null);
		$this->stuckUpdates            = ($this->container->appConfig->get('updatedb', 0) == 1);

		$this->desktop_notifications =
			Platform::getInstance()->get_platform_configuration_option('desktop_notifications', '0') ? 1 : 0;

		/** @var Stats $statsModel */
		$statsModel        = Model::getTmpInstance($this->container->application_name, 'Stats', $this->container);
		$this->statsIframe = $statsModel->collectStatistics(true);

		// Load the Javascript for this page
		Template::addJs('media://js/solo/main.js', $this->container->application);

		$cloudFlareTestURN  = 'CLOUDFLARE::'. \Awf\Utils\Template::parsePath('media://js/solo/system.js', false, $this->getContainer()->application);
		$updateInformationUrl = $this->getContainer()->router->route('index.php?view=main&format=raw&task=getUpdateInformation&' . $this->getContainer()->session->getCsrfToken()->getValue() . '=1');
		$js                    = <<< JS
Solo.loadScripts.push(function() {
			Solo.Main.displayCloudFlareWarning('$cloudFlareTestURN');
			Solo.Main.showReadableFileWarnings('$this->configUrl', '$this->backupOutputUrl');
			Solo.Main.getUpdateInformation("$updateInformationUrl");
			
			if ($this->desktop_notifications)
			{
				Solo.System.notification.askPermission();
			}  
});

JS;

		$document = $this->container->application->getDocument();
		$document->addScriptDeclaration($js);

		return true;
	}

	/**
	 * Performs automatic access control checks
	 *
	 * @param   string  $view  The view being considered
	 * @param   string  $task  The task being considered
	 *
	 * @return  bool  True if access is allowed
	 *
	 * @throws \RuntimeException
	 */
	public function canAccess($view, $task)
	{
		$view = strtolower($view);
		$task = strtolower($task);

		if (!isset($this->aclChecks[$view]))
		{
			return true;
		}

		if (!isset($this->aclChecks[$view][$task]))
		{
			if (!isset($this->aclChecks[$view]['*']))
			{
				return true;
			}

			$requiredPrivileges = $this->aclChecks[$view]['*'];
		}
		else
		{
			$requiredPrivileges = $this->aclChecks[$view][$task];
		}

		$user = $this->container->userManager->getUser();

		foreach ($requiredPrivileges as $privilege)
		{
			if (!$user->getPrivilege('akeeba.' . $privilege))
			{
				return false;
			}
		}

		return true;
	}
}