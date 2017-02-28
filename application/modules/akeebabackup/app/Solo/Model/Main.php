<?php
/**
 * @package     Solo
 * @copyright   2014-2017 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license     GNU GPL version 3 or later
 */

namespace Solo\Model;

use Awf\Database\Installer;
use Awf\Html\Select;
use Awf\Mvc\Model;
use Awf\Uri\Uri;
use Solo\Application;
use Akeeba\Engine\Platform;
use Akeeba\Engine\Factory;

class Main extends Model
{
	/**
	 * Checks the database for missing / outdated tables and runs the appropriate SQL scripts if necessary.
	 *
	 * @throws  \RuntimeException    If the previous database update is stuck
	 *
	 * @return  $this
	 */
	public function checkAndFixDatabase()
	{
		$params = $this->container->appConfig;

		// First of all let's check if we are already updating
		$stuck = $params->get('updatedb', 0);

		if ($stuck)
		{
			throw new \RuntimeException('Previous database update is flagged as stuck');
		}

		// Then set the flag
		$params->set('updatedb', 1);
		$params->saveConfiguration();

		// Update the database, if necessary
		$dbInstaller = new Installer($this->container);
		$dbInstaller->updateSchema();

		// And finally remove the flag if everything went fine
		$params->set('updatedb', null);
		$params->saveConfiguration();

		return $this;
	}

	/**
	 * Returns a list of Akeeba Engine backup profiles in a format suitable for use with Html\Select::genericList
	 *
	 * @param   bool  $includeId  Should I include the profile ID in front of the name?
	 *
	 * @return  array
	 */
	public function getProfileList($includeId = true)
	{
		$db = $this->container->db;

		$query = $db->getQuery(true)
			->select(array(
				$db->qn('id') . ' as ' . $db->qn('value'),
				$db->qn('description') . ' as ' . $db->qn('text'),
			))->from($db->qn('#__ak_profiles'));
		$db->setQuery($query);

		$records = $db->loadAssocList();

		$ret = array();

		if (!empty($records))
		{
			foreach ($records as $profile)
			{
				$description = $profile['text'];

				if ($includeId)
				{
					$description = '#' . $profile['value'] . '. ' . $description;
				}

				$ret[] = Select::option($profile['value'], $description);
			}
		}

		return $ret;
	}

	/**
	 * Gets a list of profiles which will be displayed as quick icons in the interface
	 *
	 * @return  \stdClass[]  Array of objects; each has the properties `id` and `description`
	 */
	public function getQuickIconProfiles()
	{
		$db = $this->container->db;

		$query = $db->getQuery(true)
					->select(array(
						$db->qn('id'),
						$db->qn('description')
					))->from($db->qn('#__ak_profiles'))
					->where($db->qn('quickicon') . ' = ' . $db->q(1))
					->order($db->qn('id') . " ASC");
		$db->setQuery($query);

		$ret = $db->loadObjectList();

		if (empty($ret))
		{
			$ret = array();
		}

		return $ret;
	}

	/**
	 * Read a changelog file and returns its HTML beautified rendering
	 *
	 * @param   string  $file     The file to read the changelog from
	 * @param   boolean $onlyLast If true we'll only show the last version
	 *
	 * @return  string  The HTML beautified rendering of the changelog
	 */
	public function coloriseChangelog($file, $onlyLast = false)
	{
		$ret = '';

		$lines = @file($file);

		if (empty($lines))
		{
			return $ret;
		}

		array_shift($lines);

		foreach ($lines as $line)
		{
			$line = trim($line);

			if (empty($line))
			{
				continue;
			}

			$type = substr($line, 0, 1);
			$safeTrimmed = htmlentities(trim(substr($line, 2)));

			switch ($type)
			{
				case '=':
					continue;
					break;

				case '+':
					$ret .= <<< HTML
	<li>
		<span class="fa fa-plus-square"></span> $safeTrimmed
	</li>

HTML;

					break;

				case '-':
					$ret .= <<< HTML
	<li>
		<span class="fa fa-minus-square"></span> $safeTrimmed
	</li>

HTML;

					break;

				case '~':
					$ret .= <<< HTML
	<li>
		<span class="fa fa-random"></span> $safeTrimmed
	</li>

HTML;

					break;

				case '!':
					$ret .= <<< HTML
	<li>
		<span class="fa fa-exclamation-circle"></span> $safeTrimmed
	</li>

HTML;

					break;

				case '#':
					$ret .= <<< HTML
	<li>
		<span class="fa fa-bug"></span> $safeTrimmed
	</li>

HTML;
					break;

				default:
					if (!empty($ret))
					{
						$ret .= "</ul>";

						if ($onlyLast)
						{
							return $ret;
						}
					}

					if (!$onlyLast)
					{
						$ret .= "<h3>$line</h3>\n";
					}

					$ret .= "<ul>\n";
					break;
			}
		}

		return $ret;
	}

	/**
	 * Returns the details for the latest backup, for use in the "Latest backup" cell in the control panel
	 *
	 * @return  array  The latest backup information. Empty if there is no latest backup (of course!)
	 */
	public function getLatestBackupDetails()
	{
		$db = $this->container->db;
		$query = $db->getQuery(true)
			->select('MAX(' . $db->qn('id') . ')')
			->from($db->qn('#__ak_stats'))
			->where('NOT(' . $db->qn('origin') . ' = ' . $db->q('restorepoint') . ')');
		$db->setQuery($query);
		$id = $db->loadResult();

		$backup_types = Factory::getEngineParamsProvider()->loadScripting();

		if (empty($id))
		{
			return array();
		}

		$record = Platform::getInstance()->get_statistics($id);

		if (array_key_exists($record['type'], $backup_types['scripts']))
		{
			$record['type_translated'] = Platform::getInstance()->translate($backup_types['scripts'][$record['type']]['text']);
		}
		else
		{
			$record['type_translated'] = '';
		}

		return $record;
	}

	/**
	 * Returns the URL to the config.json file
	 *
	 * @return  string
	 */
	public function getConfigUrl()
	{
		$configPath = $this->container->basePath . '/assets/private/config.php';

		if (!@file_exists($configPath))
		{
			$configPath = $this->container->basePath . '/config.php';
		}

		$configPath = $this->translatePath($configPath);

		if (empty($configPath))
		{
			return '';
		}

		return Uri::base(false, $this->container) . '/' . $configPath;
	}

	/**
	 * Returns the URL to the backup directory for the current profile. We actually create an index.html file in it if
	 * none exists and give the URL to it.
	 *
	 * @return  string
	 */
	public function getBackupOutputUrl()
	{
		$stock_dirs = Platform::getInstance()->get_stock_directories();

		$registry = Factory::getConfiguration();
		$backupPath = $registry->get('akeeba.basic.output_directory');

		foreach ($stock_dirs as $macro => $replacement)
		{
			$backupPath = str_replace($macro, $replacement, $backupPath);
		}

		$backupPath .= '/index.html';

		if (!@file_exists($backupPath))
		{
			$fs = $this->container->fileSystem;
			try
			{
				$fs->write($backupPath, '<html><head><title></title></head><body></body></html>');
				$fs->chmod($backupPath, 0644);
			}
			catch (\RuntimeException $e)
			{
				return '';
			}
		}

		$backupPath = $this->translatePath($backupPath);

		if (empty($backupPath))
		{
			return '';
		}

		return Uri::base(false, $this->container) . '/' . $backupPath;
	}

	/**
	 * Translate an absolute filesystem path into a relative URL
	 *
	 * @param   string $fileName The full filesystem path of a file or directory
	 *
	 * @return  string  The relative URL (or empty string if it's outside the site's root)
	 */
	protected function translatePath($fileName)
	{
		$fileName = str_replace('\\', '/', $fileName);

		$appRoot = str_replace('\\', '/', APATH_BASE);
		$appRoot = rtrim($appRoot, '/');

		if (strpos($fileName, $appRoot) === 0)
		{
			$fileName = substr($fileName, strlen($appRoot) + 1);

			$fileName = trim($fileName, '/');
		}
		else
		{
			return '';
		}

		return $fileName;
	}

	/**
	 * Check the Akeeba Engine's settings encryption status and proceed to enabling / disabling encryption if necessary.
	 *
	 * @return  void
	 */
	public function checkEngineSettingsEncryption()
	{
		$secretKeyFile = $this->container->basePath . Application::secretKeyRelativePath;
		// We have to look inside the application config, not  the platform options
		$encryptionEnabled = $this->container->appConfig->get('useencryption', -1);
		$fileExists = @file_exists($secretKeyFile);

		if ($fileExists && ($encryptionEnabled == 0))
		{
			// We have to disable the encryption
			$this->disableEngineSettingsEncryption($secretKeyFile);
		}
		elseif (!$fileExists && ($encryptionEnabled != 0))
		{
			// We have to enable the encryption
			$this->enableEngineSettingsEncryption($secretKeyFile);
		}
	}

	/**
	 * Disables the Akeeba Engine settings encryption. It will load the settings for each profile, decrypt the settings,
	 * commit them to database and finally remove the secret key file.
	 *
	 * @param   string $secretKeyFile The path to the secret key file
	 *
	 * @return  void
	 */
	protected function disableEngineSettingsEncryption($secretKeyFile)
	{
		$key = Factory::getSecureSettings()->getKey();

		// Loop all profiles and decrypt their settings
		$db = $this->container->db;
		$query = $db->getQuery(true)
			->select('*')
			->from($db->qn('#__ak_profiles'));
		$profiles = $db->setQuery($query)->loadObjectList();

		foreach ($profiles as $profile)
		{
			$id = $profile->id;
			$config = Factory::getSecureSettings()->decryptSettings($profile->configuration, $key);
			$sql = $db->getQuery(true)
				->update($db->qn('#__ak_profiles'))
				->set($db->qn('configuration') . ' = ' . $db->q($config))
				->where($db->qn('id') . ' = ' . $db->q($id));
			$db->setQuery($sql);
			$db->execute();
		}

		// Finally, remove the key file
		$fs = $this->container->fileSystem;
		try
		{
			$fs->delete($secretKeyFile);
		}
		catch (\Exception $e)
		{

		}
	}

	/**
	 * Enables the Akeeba Engine settings encryption. It will first try to create a new crypto-safe secret key, load the
	 * settings for each profile, encrypt the settings, then commit them to the database.
	 *
	 * @param   string $secretKeyFile The path to the secret key file
	 *
	 * @return  void
	 */
	protected function enableEngineSettingsEncryption($secretKeyFile)
	{
		$key = $this->createEngineSettingsKeyFile($secretKeyFile);

		if (empty($key) || ($key == false))
		{
			return;
		}

		// Loop all profiles and encrypt their settings
		$db = $this->container->db;
		$query = $db->getQuery(true)
			->select('*')
			->from($db->qn('#__ak_profiles'));
		$profiles = $db->setQuery($query)->loadObjectList();

		if (!empty($profiles))
		{
			foreach ($profiles as $profile)
			{
				$id = $profile->id;
				$config = Factory::getSecureSettings()->encryptSettings($profile->configuration, $key);
				$sql = $db->getQuery(true)
					->update($db->qn('#__ak_profiles'))
					->set($db->qn('configuration') . ' = ' . $db->q($config))
					->where($db->qn('id') . ' = ' . $db->q($id));
				$db->setQuery($sql);
				$db->execute();
			}
		}
	}

	/**
	 * Create the key file for the engine settings and return the crypto-safe radom key
	 *
	 * @param   string $secretKeyFile The location of the secret key file
	 *
	 * @return  boolean|string  The key, or false if we could not create it
	 */
	protected function createEngineSettingsKeyFile($secretKeyFile)
	{
		$randValue = new \Awf\Session\Randval(new \Awf\Utils\Phpfunc());
		$key = $randValue->generate(32);

		$encodedKey = base64_encode($key);

		$fileContents = "<?php defined('AKEEBAENGINE') or die(); define('AKEEBA_SERVERKEY', '$encodedKey'); ?>";

		$fs = $this->container->fileSystem;

		try
		{
			$fs->write($secretKeyFile, $fileContents);

			return $key;
		}
		catch (\Exception $e)
		{
			return false;
		}
	}

	/**
	 * Do I have to warn the user about putting a Download ID in the Core version?
	 *
	 * @return  boolean
	 */
	public function mustWarnAboutDownloadIdInCore()
	{
		$ret = false;
		$isPro = AKEEBABACKUP_PRO;

		if ($isPro)
		{
			return $ret;
		}

		$downloadId = Platform::getInstance()->get_platform_configuration_option('update_dlid', '');

		if (preg_match('/^([0-9]{1,}:)?[0-9a-f]{32}$/i', $downloadId))
		{
			$ret = true;
		}

		return $ret;
	}

	/**
	 * Do I need to tell the user to set up a Download ID?
	 *
	 * @return  boolean
	 */
	public function needsDownloadID()
	{
		// Do I need a Download ID?
		$ret = true;
		$isPro = AKEEBABACKUP_PRO;

		if (!$isPro)
		{
			$ret = false;
		}
		else
		{
			$dlid = Platform::getInstance()->get_platform_configuration_option('update_dlid', '');

			if (preg_match('/^([0-9]{1,}:)?[0-9a-f]{32}$/i', $dlid))
			{
				$ret = false;
			}
		}

		return $ret;
	}

	/**
	 * Update the cached live site's URL for the front-end backup feature (altbackup.php)
	 * and the detected Joomla! libraries path
	 *
	 * @return void
	 */
	public function updateMagicParameters()
	{
		$dirtyFlag = false;
		$baseURL = Uri::base(false, $this->container);
		$storedBaseURL = $this->container->appConfig->get('options.siteurl');

		if ($storedBaseURL != $baseURL)
		{
			$this->container->appConfig->set('options.siteurl', $baseURL);
			$dirtyFlag = true;
		}

		if (!$this->container->appConfig->get('options.confwiz_upgrade', 0))
		{
			$this->markOldProfilesConfigured();
			$this->container->appConfig->set('options.confwiz_upgrade', 1);
			$dirtyFlag = true;
		}

		if ($dirtyFlag)
		{
			try
			{
				$this->container->appConfig->saveConfiguration();
			}
			catch (\RuntimeException $e)
			{
				// Do nothing; if the magic parameters are missing nobody dies
			}
		}
	}

	/**
	 * Flags stuck backups as invalid
	 *
	 * @return void
	 */
	public function flagStuckBackups()
	{
		// Invalidate stale backups
		Factory::resetState(array(
			'global' => true,
			'log'    => false,
			'maxrun' => $this->container->appConfig->get('options.failure_timeout', 180)
			));
	}

	public function notifyFailed()
	{
		$config = $this->container->appConfig;

		// Invalidate stale backups
		$this->flagStuckBackups();

		// Get the last execution and search for failed backups AFTER that date
		$last = $this->getLastCheck();

		// Get failed backups
		$filters[] = array('field' => 'status'     , 'operand' => '=' , 'value'   => 'fail');
		$filters[] = array('field' => 'origin'     , 'operand' => '<>', 'value'   => 'restorepoint');
		$filters[] = array('field' => 'backupstart', 'operand' => '>' , 'value'   => $last);

		$failed = Platform::getInstance()->get_statistics_list(array('filters' => $filters));

		// Well, everything went ok.
		if(!$failed)
		{
			return array(
				'message' => array("No need to run: no failed backups or they were already notificated"),
				'result'  => true
			);
		}

		// Whops! Something went wrong, let's start notifing
		$emails = $config->get('options.failure_email_address', '');
		$emails = explode(',', $emails);

		if(!$emails)
		{
			$emails = Platform::getInstance()->get_administrator_emails();
		}

		if(empty($emails))
		{
			return array(
				'message' => array("WARNING! Failed backup(s) detected, but there are no configured Super Administrators to receive notifications"),
				'result'  => false
			);
		}

		$failedReport = array();

		foreach($failed as $fail)
		{
			$string  = "Description : ".$fail['description']."\n";
			$string .= "Start time  : ".$fail['backupstart']."\n";
			$string .= "Origin      : ".$fail['origin']."\n";
			$string .= "Type        : ".$fail['type']."\n";
			$string .= "Profile ID  : ".$fail['profile_id'];

			$failedReport[] = $string;
		}

		$failedReport = implode("\n#-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+#\n", $failedReport);

		$email_subject = $config->get('options.failure_email_subject', '');

		if(!$email_subject)
		{
			$email_subject	= <<<ENDSUBJECT
THIS EMAIL IS SENT FROM YOUR SITE "[SITENAME]" - Failed backup(s) detected
ENDSUBJECT;
		}

		$email_body = $config->get('options.failure_email_body', '');

		if(!$email_body)
		{
			$email_body = <<<ENDBODY
================================================================================
FAILED BACKUP ALERT
================================================================================

Your site has determined that there are failed backups.

The following backups are found to be failing:

[FAILEDLIST]

================================================================================
WHY AM I RECEIVING THIS EMAIL?
================================================================================

This email has been automatically sent by scritp you, or the person who built
or manages your site, has installed and explicitly configured. This script looks
for failed backups and sends an email notification to all Super Users.

If you do not understand what this means, please do not contact the authors of
the software. They are NOT sending you this email and they cannot help you.
Instead, please contact the person who built or manages your site.

================================================================================
WHO SENT ME THIS EMAIL?
================================================================================

This email is sent to you by your own site, [SITENAME]

ENDBODY;
		}

		$email_subject = Factory::getFilesystemTools()->replace_archive_name_variables($email_subject);
		$email_body    = Factory::getFilesystemTools()->replace_archive_name_variables($email_body);
		$email_body    = str_replace('[FAILEDLIST]', $failedReport, $email_body);

		foreach($emails as $email)
		{
			Platform::getInstance()->send_email($email, $email_subject, $email_body);
		}

		// Let's update the last time we check, so we will avoid to send
		// the same notification several times
		$this->updateLastCheck(intval($last));

		return array(
			'message' => array(
				"WARNING! Found ".count($failed)." failed backup(s)",
				"Sent ".count($emails)." notifications"
			),
			'result'  => true
		);
	}

	private function updateLastCheck($exists)
	{
		$db = $this->container->db;

		$now = Platform::getInstance()->get_timestamp_database();

		if($exists)
		{
			$query = $db->getQuery(true)
				->update($db->qn('#__ak_storage'))
				->set($db->qn('lastupdate').' = '.$db->q($now))
				->where($db->qn('tag').' = '.$db->q('akeeba_checkfailed'));
		}
		else
		{
			$query = $db->getQuery(true)
				->insert($db->qn('#__ak_storage'))
				->columns(array($db->qn('tag'), $db->qn('lastupdate')))
				->values($db->q('akeeba_checkfailed').', '.$db->q($now));
		}

		try
		{
			$db->setQuery($query)->execute();
		}
		catch (\Exception $exc)
		{

		}
	}

	private function getLastCheck()
	{
		$db = $this->container->db;

		$query = $db->getQuery(true)
					->select($db->qn('lastupdate'))
					->from($db->qn('#__ak_storage'))
					->where($db->qn('tag').' = '.$db->q('akeeba_checkfailed'));

		$datetime = $db->setQuery($query)->loadResult();

		if(!intval($datetime))
		{
			$datetime = $db->getNullDate();
		}

		return $datetime;
	}

	/**
	 * Performs any post-upgrade actions
	 *
	 * @return bool True if we took any actions, false otherwise
	 */
	public function postUpgradeActions()
	{
		// Check the last update_version stored in the database
		$db = $this->container->db;

		$query = $db->getQuery(true)
			->select($db->qn('data'))
			->from($db->qn('#__ak_params'))
			->where($db->qn('tag') . ' = ' . $db->q('update_version'));

		try
		{
			$lastVersion = $db->setQuery($query, 0, 1)->loadResult();
		}
		catch (\Exception $e)
		{
			$lastVersion = null;
		}

		// If it's our current version we don't have to do anything, just return
		if ($lastVersion == AKEEBABACKUP_VERSION)
		{
			return false;
		}

		// Load and execute the PostUpgradeScript class
		if (class_exists('\\Solo\\PostUpgradeScript'))
		{
			$upgradeScript = new \Solo\PostUpgradeScript($this->container);
			$upgradeScript->execute();
		}

		// Remove the old update_version from the database
		$query = $db->getQuery(true)
			->delete($db->qn('#__ak_params'))
			->where($db->qn('tag') . ' = ' . $db->q('update_version'));

		try
		{
			$db->setQuery($query)->execute();
		}
		catch (\Exception $e)
		{
			// Don't panic
		}

		// Insert the new update_version to the database
		$query = $db->getQuery(true)
			->insert($db->qn('#__ak_params'))
			->columns(array($db->qn('tag'), $db->qn('data')))
			->values($db->q('update_version') . ', ' . $db->q(AKEEBABACKUP_VERSION));

		try
		{
			$db->setQuery($query)->execute();
		}
		catch (\Exception $e)
		{
			// Don't panic
		}

		return true;
	}

	/**
	 * Akeeba Backup 4.3.2 displays a popup if your profile is not already configured by Configuration Wizard, the
	 * Configuration page or imported from the Profiles page. This bit of code makes sure that existing profiles will
	 * be marked as already configured just the FIRST time you upgrade to the new version from an old version.
	 */
	public function markOldProfilesConfigured()
	{
		// Get all profiles
		$db = $this->container->db;

		$query = $db->getQuery(true)
					->select(array(
						$db->qn('id'),
					))->from($db->qn('#__ak_profiles'))
					->order($db->qn('id') . " ASC");
		$db->setQuery($query);
		$profiles = $db->loadColumn();

		// Save the current profile number
		$session = \Awf\Application\Application::getInstance()->getContainer()->segment;
		$oldProfile = $session->profile;

		// Update all profiles
		foreach ($profiles as $profile_id)
		{
			Factory::nuke();
			Platform::getInstance()->load_configuration($profile_id);
			$config = Factory::getConfiguration();
			$config->set('akeeba.flag.confwiz', 1);
			Platform::getInstance()->save_configuration($profile_id);
		}

		// Restore the old profile
		Factory::nuke();
		Platform::getInstance()->load_configuration($oldProfile);
	}

	/**
	 * Check the strength of the Secret Word for front-end and remote backups. If it is insecure return the reason it
	 * is insecure as a string. If the Secret Word is secure return an empty string.
	 *
	 * @return  string
	 */
	public function getFrontendSecretWordError()
	{
		// Is frontend backup enabled?
		$febEnabled = Platform::getInstance()->get_platform_configuration_option('frontend_enable', 0) != 0;

		if (!$febEnabled)
		{
			return '';
		}

		$secretWord = Platform::getInstance()->get_platform_configuration_option('frontend_secret_word', '');

		try
		{
			\Akeeba\Engine\Util\Complexify::isStrongEnough($secretWord);
		}
		catch (\RuntimeException $e)
		{
			// Ah, the current Secret Word is bad. Create a new one if necessary.
			$session = $this->container->segment;
			$newSecret = $session->get('newSecretWord', null);

			if (empty($newSecret))
			{
				$random = new \Akeeba\Engine\Util\RandomValue();
				$newSecret = $random->generateString(32);
				$session->set('newSecretWord', $newSecret);
			}

			return $e->getMessage();
		}

		return '';
	}

	/**
	 * Checks if the mbstring extension is installed and enabled
	 *
	 * @return  bool
	 */
	public function checkMbstring()
	{
		return function_exists('mb_strlen') && function_exists('mb_convert_encoding') &&
		function_exists('mb_substr') && function_exists('mb_convert_case');
	}

}