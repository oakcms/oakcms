<?php
/**
 * @package        solo
 * @copyright      2014-2017 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license        GNU GPL version 3 or later
 */

namespace Solo\View\Backup;

use Akeeba\Engine\Factory;
use Akeeba\Engine\Platform;
use Awf\Date\Date;
use Awf\Mvc\Model;
use Awf\Text\Text;
use Awf\Uri\Uri;
use Awf\Utils\Template;
use Solo\Helper\Escape;
use Solo\Model\Main;

/**
 * The view class for the Backup view
 */
class Html extends \Solo\View\Html
{
	public $default_descr;

	public $description;

	public $comment;

	public $returnURL;

	public $profileId;

	public $profileName;

	public $isSTW;

	public $domains;

	public $maxexec;

	public $bias;

	public $useIframe;

	public $showJPSKey = 0;

	public $jpsKey;

	public $showANGIEKey;

	public $angieKey;

	public $autoStart;

	public $srpInfo;

	public $unwritableOutput;

	public $hasQuirks;

	public $hasErrors = false;

	public $hasCriticalErrors = false;

	public $quirks;

	public $subtitle;

	public $profileList;

	public $desktop_notifications;

	public function onBeforeMain()
	{
		// Load the necessary Javascript
		Template::addJs('media://js/solo/backup.js', $this->container->application);

		/** @var \Solo\Model\Backup $model */
		$model = $this->getModel();

		// Get the backup description and comment
		$tz      = $this->container->appConfig->get('timezone', 'UTC');
		$user    = $this->container->userManager->getUser();
		$user_tz = $user->getParameters()->get('timezone', null);

		if (!empty($user_tz))
		{
			$tz = $user_tz;
		}

		$date = new Date('now', $tz);

		$default_description = Text::_('COM_AKEEBA_BACKUP_DEFAULT_DESCRIPTION') . ' ' . $date->format(Text::_('DATE_FORMAT_LC2'), true);

		$this->default_descr = Escape::escapeJS($default_description);
		$this->description   = Escape::escapeJS($model->getState('description', $default_description));
		$this->comment       = $model->getState('comment', '');

		// Push the return URL
		$returnURL       = $model->getState('returnurl', '');
		$this->returnURL = empty($returnURL) ? '' : $returnURL;

		// Push the profile ID and name
		$this->profileId   = Platform::getInstance()->get_active_profile();
		$this->profileName = $this->escape(Platform::getInstance()->get_profile_name($this->profileId));

		// If a return URL is set *and* the profile's name is "Site Transfer
		// Wizard", we are running the Site Transfer Wizard
		$this->isSTW = ($this->profileName == 'Site Transfer Wizard (do not rename)') && !empty($this->returnURL);

		// Get the domain details from scripting facility
		$config    = Factory::getConfiguration();
		$script    = $config->get('akeeba.basic.backup_type', 'full');
		$scripting = Factory::getEngineParamsProvider()->loadScripting();
		$domains   = array();

		if (!empty($scripting))
		{
			foreach ($scripting['scripts'][$script]['chain'] as $domain)
			{
				$this->description = Text::_($scripting['domains'][$domain]['text']);
				$domain_key        = $scripting['domains'][$domain]['domain'];

				if ($this->isSTW && ($domain_key == 'Packing'))
				{
					$this->description = Text::_('COM_AKEEBA_BACKUP_LABEL_DOMAIN_PACKING_STW');
				}

				$domains[] = array($domain_key, $this->description);
			}
		}

		$this->domains = Escape::escapeJS(json_encode($domains), '"\\');

		// Push some engine parameters
		$this->maxexec   = $config->get('akeeba.tuning.max_exec_time', 14) * 1000;
		$this->bias      = $config->get('akeeba.tuning.run_time_bias', 75);
		$this->useIframe = $config->get('akeeba.basic.useiframe', 0) ? 'true' : 'false';

		$this->showJPSKey = 1;
		$this->jpsKey     = $config->get('engine.archiver.jps.key', '');

		$this->showANGIEKey = 1;
		$this->angieKey     = $config->get('engine.installer.angie.key', '');

		$this->autoStart = $model->getState('autostart', 0);

		$this->srpInfo = $model->getState('srpinfo', array());

		// Check if the output directory is writable
		$this->quirks           = Factory::getConfigurationChecks()->getDetailedStatus(true);
		$this->unwritableOutput = array_key_exists('001', $this->quirks);
		$this->hasQuirks        = !empty($this->quirks);

		if (!empty($this->quirks))
		{
			foreach ($this->quirks as $quirk)
			{
				if ($quirk['severity'] == 'high')
				{
					$this->hasErrors = true;
				}
				elseif ($quirk['severity'] == 'critical')
				{
					$this->hasErrors         = true;
					$this->hasCriticalErrors = true;
				}
			}
		}

		// Set the toolbar title
		$this->subtitle = Text::_('COM_AKEEBA_BACKUP');

		if (isset($this->srpInfo['tag']) && $this->srpInfo['tag'] == 'restorepoint')
		{
			$this->subtitle = Text::_('AKEEBASRP');
		}
		elseif ($this->isSTW)
		{
			$this->subtitle = Text::_('SITETRANSFERWIZARD');
		}

		// Push the list of profiles
		/** @var Main $cpanelModel */
		$cpanelModel       = Model::getInstance($this->container->application_name, 'Main', $this->container);
		$this->profileList = $cpanelModel->getProfileList();

		if (!$this->hasCriticalErrors)
		{
			$this->container->application->getDocument()->getMenu()->disableMenu('main');
		}

		$this->desktop_notifications = Platform::getInstance()
		                                       ->get_platform_configuration_option('desktop_notifications', '0') ? 1 : 0;

		$this->injectJavascript();


		// All done, show the page!
		return true;
	}

	/**
	 * Injects the necessary Javascript to the page's header
	 */
	protected function injectJavascript()
	{
		$configuration = Factory::getConfiguration();
		$router        = $this->getContainer()->router;
		$returnURL     = Escape::escapeJS($this->returnURL);
		$isSTW         = $this->isSTW ? 'true' : 'false';
		$ajaxURL       = $router->route('index.php?view=backup&task=ajax');
		$logURL        = $router->route('index.php?view=backup&task=log');
		$aliceURL      = $router->route('index.php?view=alices');
		$srpInfo       = Escape::escapeJS(json_encode($this->srpInfo));
		$angieKey      = Escape::escapeJS($this->angieKey);
		$jpsKey        = Escape::escapeJS($this->jpsKey);
		$autoResume    = (int) $configuration->get('akeeba.advanced.autoresume', 1);
		$autoTimeout   = (int) $configuration->get('akeeba.advanced.autoresume_timeout', 10);;
		$autoMaxRetries = (int) $configuration->get('akeeba.advanced.autoresume_maxretries', 3);
		$iconURL        = Escape::escapeJS(Uri::base(false, $this->container) . '/media/logo/solo-96.png');
		$autoStart      = (!$this->unwritableOutput && ($this->autoStart || (isset($this->srpInfo['tag']) && ($this->srpInfo['tag'] == 'restorepoint')))) ? 1 : 0;
		$desktop_notifications = $this->desktop_notifications ? 'true' : 'false';

		$strings['UI-LASTRESPONSE']			= Escape::escapeJS(Text::_('COM_AKEEBA_BACKUP_TEXT_LASTRESPONSE'));
		$strings['UI-STW-CONTINUE']			= Escape::escapeJS(Text::_('STW_MSG_CONTINUE'));

		$strings['UI-BACKUPSTARTED']		= Escape::escapeJS(Text::_('COM_AKEEBA_BACKUP_TEXT_BACKUPSTARTED'));
		$strings['UI-BACKUPFINISHED']		= Escape::escapeJS(Text::_('COM_AKEEBA_BACKUP_TEXT_BACKUPFINISHED'));
		$strings['UI-BACKUPHALT']			= Escape::escapeJS(Text::_('COM_AKEEBA_BACKUP_TEXT_BACKUPHALT'));
		$strings['UI-BACKUPRESUME']			= Escape::escapeJS(Text::_('COM_AKEEBA_BACKUP_TEXT_BACKUPRESUME'));
		$strings['UI-BACKUPHALT_DESC']		= Escape::escapeJS(Text::_('COM_AKEEBA_BACKUP_TEXT_BACKUPHALT_DESC'));
		$strings['UI-BACKUPFAILED']			= Escape::escapeJS(Text::_('COM_AKEEBA_BACKUP_TEXT_BACKUPFAILED'));
		$strings['UI-BACKUPWARNING']		= Escape::escapeJS(Text::_('COM_AKEEBA_BACKUP_TEXT_BACKUPWARNING'));

		$js = <<< JS
Solo.loadScripts.push(function() {
	Solo.Backup.returnUrl = '$returnURL';
	Solo.Backup.isSTW = $isSTW;
	Solo.Backup.maxExecutionTime = '{$this->maxexec}';
	Solo.Backup.runtimeBias = '{$this->bias}';
	Solo.Backup.domains = JSON.parse("{$this->domains}");
	Solo.System.params.AjaxURL = '$ajaxURL';
	Solo.Backup.URLs.LogURL = '$logURL';
	Solo.Backup.URLs.AliceURL = '$aliceURL';
	Solo.System.params.useIFrame = $this->useIframe;
	Solo.Backup.srpInfo = JSON.parse('$srpInfo');
	Solo.Backup.default_descr = '$this->default_descr';
	Solo.Backup.config_angiekey = '$angieKey';
	Solo.Backup.jpsKey = '$jpsKey';
	
	// Auto-resume setup
	Solo.Backup.resume.enabled = $autoResume;
	Solo.Backup.resume.timeout = $autoTimeout;
	Solo.Backup.resume.maxRetries = $autoMaxRetries;
	Solo.Backup.resume.retry = 0;
	
	// Work around Safari which ignores autocomplete=off (FOR CRYING OUT LOUD!)
	setTimeout('Solo.Backup.restoreDefaultOptions();', 500);
	
	// Create a function for saving the editor's contents
	akeeba_comment_editor_save = function() {
	};
	
	// Push the icon URL
	Solo.System.notification.iconURL = '$iconURL';
	
	// Push translations
	Solo.Backup.translations['UI-LASTRESPONSE'] = '{$strings['UI-LASTRESPONSE']}'; 
	Solo.Backup.translations['UI-STW-CONTINUE'] = '{$strings['UI-STW-CONTINUE']}';	 
	
	Solo.Backup.translations['UI-BACKUPSTARTED']  = '{$strings['UI-BACKUPSTARTED']}';
	Solo.Backup.translations['UI-BACKUPFINISHED']  = '{$strings['UI-BACKUPFINISHED']}';
	Solo.Backup.translations['UI-BACKUPHALT'] = '{$strings['UI-BACKUPHALT']}';
	Solo.Backup.translations['UI-BACKUPRESUME']  = '{$strings['UI-BACKUPRESUME']}';
	Solo.Backup.translations['UI-BACKUPHALT_DESC']  = '{$strings['UI-BACKUPHALT_DESC']}';
	Solo.Backup.translations['UI-BACKUPFAILED']  = '{$strings['UI-BACKUPFAILED']}';
	Solo.Backup.translations['UI-BACKUPWARNING']  = '{$strings['UI-BACKUPWARNING']}';
	
	if ($autoStart) {
		Solo.Backup.start();
	} else {
		Solo.System.addEventListener(document.getElementById('backup-start'), 'click', Solo.Backup.start);
		Solo.System.addEventListener(document.getElementById('backup-default'), 'click', Solo.Backup.restoreDefaultOptions);
	}
	
	if ($desktop_notifications)
	{
		Solo.System.notification.askPermission();
	}
});

JS;

		$this->getContainer()->application->getDocument()->addScriptDeclaration($js);
	}
}