<?php
/**
 * @package        solo
 * @copyright      2014-2017 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license        GNU GPL version 3 or later
 */

namespace Solo\Model;

use Akeeba\Engine\Factory;
use Akeeba\Engine\Platform;
use Akeeba\Engine\Util\PushMessages;
use Awf\Application\Application;
use Awf\Date\Date;
use Awf\Mvc\Model;
use Awf\Text\Text;
use Psr\Log\LogLevel;

class Backup extends Model
{
	/**
	 * Starts or step a backup process. Set the state variable "ajax" to the task you want to execute OR call the
	 * relevant public method directly.
	 *
	 * @return  array  An Akeeba Engine return array
	 */
	public function runBackup()
	{
		$ret_array = array();

		$ajaxTask = $this->getState('ajax');

		switch ($ajaxTask)
		{
			// Start a new backup
			case 'start':
				$ret_array = $this->startBackup();
				break;

			// Step through a backup
			case 'step':
				$ret_array = $this->stepBackup();
				break;

			// Send a push notification for backup failure
			case 'pushFail':
				$this->pushFail();
				break;

			default:
				break;
		}

		return $ret_array;
	}

	/**
	 * Starts a new backup.
	 *
	 * State variables expected
	 * backupid		The ID of the backup. If none is set up we will create a new one in the form id123
	 * tag			The backup tag, e.g. "frontend". If none is set up we'll get it through the Platform.
	 * description	The description of the backup (optional)
	 * comment      The comment of the backup (optional)
	 * jpskey       JPS password
	 * angiekey     ANGIE password
	 *
	 * @param   array  $overrides  Configuration overrides
	 *
	 * @return  array  An Akeeba Engine return array
	 */
	public function startBackup(array $overrides = array())
	{
		$tag         = $this->getState('tag', null, 'string');
		$backupId    = $this->getState('backupid', null, 'string');
		$description = $this->getState('description', '', 'string');
		$comment     = $this->getState('comment', '', 'html');
		$jpskey      = $this->getState('jpskey', null, 'raw');
		$angiekey    = $this->getState('angiekey', null, 'raw');

		// Try to get a backup ID if none is provided
		if (is_null($backupId))
		{
			$db    = $this->container->db;
			$query = $db->getQuery(true)
			            ->select('MAX(' . $db->qn('id') . ')')
			            ->from($db->qn('#__ak_stats'));

			try
			{
				$maxId = $db->setQuery($query)->loadResult();
			}
			catch (\Exception $e)
			{
				$maxId = 0;
			}

			$backupId = 'id' . ($maxId + 1);
		}

		// Use the default description if none specified
		if (empty($description))
		{
			$dateNow     = new Date();
			$description =
				Text::_('COM_AKEEBA_BACKUP_DEFAULT_DESCRIPTION') . ' ' .
				$dateNow->format(Text::_('DATE_FORMAT_LC2'), true);
		}

		// Try resetting the engine
		Factory::resetState(array(
			'maxrun' => 0
		));

		// Remove any stale memory files left over from the previous step
		if (empty($tag))
		{
			$tag = Platform::getInstance()->get_backup_origin();
		}

		$tempVarsTag = $tag;
		$tempVarsTag .= empty($backupId) ? '' : ('.' . $backupId);

		Factory::getFactoryStorage()->reset($tempVarsTag);
		Factory::nuke();
		Factory::getLog()->log(LogLevel::DEBUG, " -- Resetting Akeeba Engine factory ($tag.$backupId)");
		Platform::getInstance()->load_configuration();

		// Should I apply any configuration overrides?
		if (is_array($overrides) && !empty($overrides))
		{
			$config        = Factory::getConfiguration();
			$protectedKeys = $config->getProtectedKeys();
			$config->resetProtectedKeys();

			foreach ($overrides as $k => $v)
			{
				$config->set($k, $v);
			}

			$config->setProtectedKeys($protectedKeys);
		}

		// Check if there are critical issues preventing the backup
		if (!Factory::getConfigurationChecks()->getShortStatus())
		{
			$configChecks = Factory::getConfigurationChecks()->getDetailedStatus();

			foreach ($configChecks as $checkItem)
			{
				if ($checkItem['severity'] != 'critical')
				{
					continue;
				}

				return array(
					'HasRun' => 0,
					'Error'  => 'Failed configuration check Q' . $checkItem['code'] . ': ' . $checkItem['description'] . '. Please refer to https://www.akeebabackup.com/documentation/warnings/q' . $checkItem['code'] . '.html for more information and troubleshooting instructions.',
				);
			}
		}

		$options = array(
			'description' => $description,
			'comment'     => $comment,
			'jpskey'      => $jpskey,
			'angiekey'    => $angiekey,
		);

		if (is_null($jpskey))
		{
			unset ($options['jpskey']);
		}

		if (is_null($angiekey))
		{
			unset ($options['angiekey']);
		}

		$kettenrad = Factory::getKettenrad();
		$kettenrad->setBackupId($backupId);
		$kettenrad->setup($options);

		$this->setState('backupid', $backupId);

		// Run the first backup step. We need to run tick() twice
		/**
		 * We need to run tick() twice in the first backup step.
		 *
		 * The first tick() will reset the backup engine and start a new backup. However, no backup record is created
		 * at this point. This means that Factory::loadState() cannot find a backup record, therefore it cannot read
		 * the backup profile being used, therefore it will assume it's profile #1.
		 *
		 * The second tick() creates the backup record without doing much else, fixing this issue.
		 *
		 * However, if you have conservative settings where the min exec time is MORE than the max exec time the second
		 * tick would never run. Therefore we need to tell the first tick to ignore the time settings (since it only
		 * takes a few milliseconds to execute anyway) and then apply the time settings on the second tick (which also
		 * only takes a few milliseconds). This is why we have setIgnoreMinimumExecutionTime before and after the first
		 * tick. DO NOT REMOVE THESE.
		 *
		 * THEREFORE, DO NOT REMOVE THE SECOND tick(), IT IS THERE ON PURPOSE!
		 */
		$kettenrad->setIgnoreMinimumExecutionTime(true);
		$kettenrad->tick(); // Do not remove the first call to tick()!!!
		$kettenrad->setIgnoreMinimumExecutionTime(false);
		$kettenrad->tick(); // Do not remove the second call to tick()!!!
		$ret_array = $kettenrad->getStatusArray();

		// So as not to have duplicate warnings reports
		$kettenrad->resetWarnings();

		try
		{
			Factory::saveState($tag, $backupId);
		}
		catch (\RuntimeException $e)
		{
			$ret_array['Error'] = $e->getMessage();
		}

		return $ret_array;
	}

	/**
	 * Steps through a backup.
	 *
	 * State variables expected (MUST be set):
	 * backupid		The ID of the backup.
	 * tag			The backup tag, e.g. "frontend".
	 * profile      (optional) The profile ID of the backup.
	 *
	 * @param   bool  $requireBackupId  Should the backup ID be required?
	 *
	 * @return  array  An Akeeba Engine return array
	 */
	public function stepBackup($requireBackupId = true)
	{
		// Get the tag. If not specified use the AKEEBA_BACKUP_ORIGIN constant.
		$tag = $this->getState('tag', null, 'string');

		if (is_null($tag) && defined('AKEEBA_BACKUP_ORIGIN'))
		{
			$tag = AKEEBA_BACKUP_ORIGIN;
		}

		// Get the Backup ID. If not specified use the AKEEBA_BACKUP_ID constant.
		$backupId = $this->getState('backupid', null, 'string');

		if (is_null($backupId) && defined('AKEEBA_BACKUP_ID'))
		{
			$backupId = AKEEBA_BACKUP_ID;
		}

		// Get the profile from the session, the AKEEBA_PROFILE constant or the model state – in this order
		$session = Application::getInstance()->getContainer()->segment;
		$profile = $session->get('profile', null);
		$profile = defined('AKEEBA_PROFILE') ? AKEEBA_PROFILE : $profile;
		$profile = $this->getState('profile', $profile, 'int');
		$profile = max(0, (int) $profile);

		if (empty($profile))
		{
			$profile = $this->getLastBackupProfile($tag, $backupId);
		}

		// Set the active profile
		$session->set('profile', $profile);

		if (!defined('AKEEBA_PROFILE'))
		{
			define('AKEEBA_PROFILE', $profile);
		}

		// Run a backup step
		$ret_array = array(
			'Error' => '',
		);

		try
		{
			// Reload the configuration
			Platform::getInstance()->load_configuration($profile);

			// Load the engine from storage
			Factory::loadState($tag, $backupId, $requireBackupId);

			// Set the backup ID and run a backup step
			$kettenrad = Factory::getKettenrad();
			$kettenrad->setBackupId($backupId);

			$kettenrad->tick();
			$ret_array = $kettenrad->getStatusArray();

			// Prevent duplicate reporting of warnings
			$kettenrad->resetWarnings();
		}
		catch (\Exception $e)
		{
			$ret_array['Error'] = $e->getMessage();
		}

		try
		{
			if (empty($ret_array['Error']) && ($ret_array['HasRun'] != 1))
			{
				Factory::saveState($tag, $backupId);
			}
		}
		catch (\RuntimeException $e)
		{
			$ret_array['Error'] = $e->getMessage();
		}

		if (!empty($ret_array['Error']) || ($ret_array['HasRun'] == 1))
		{
			// Clean up
			Factory::nuke();

			$tempVarsTag = $tag;
			$tempVarsTag .= empty($backupId) ? '' : ('.' . $backupId);

			Factory::getFactoryStorage()->reset($tempVarsTag);
		}

		return $ret_array;
	}

	/**
	 * Send a push notification for a failed backup
	 *
	 * State variables expected (MUST be set):
	 * errorMessage  The error message
	 *
	 * @return  void
	 */
	public function pushFail()
	{
		$errorMessage = $this->getState('errorMessage');

		$platform = Platform::getInstance();
		$key      = 'COM_AKEEBA_PUSH_ENDBACKUP_FAIL_BODY_WITH_MESSAGE';

		if (empty($errorMessage))
		{
			$key = 'COM_AKEEBA_PUSH_ENDBACKUP_FAIL_BODY';
		}

		$pushSubject = sprintf(
			$platform->translate('COM_AKEEBA_PUSH_ENDBACKUP_FAIL_SUBJECT'),
			$platform->get_site_name(),
			$platform->get_host()
		);
		$pushDetails = sprintf(
			$platform->translate($key),
			$platform->get_site_name(),
			$platform->get_host(),
			$errorMessage
		);

		$push = new PushMessages();
		$push->message($pushSubject, $pushDetails);
	}

	/**
	 * Get the profile used to take the last backup for the specified tag
	 *
	 * @param   string  $tag       The backup tag a.k.a. backup origin (backend, frontend, json, ...)
	 * @param   string  $backupId  (optional) The Backup ID
	 *
	 * @return  int  The profile ID of the latest backup taken with the specified tag / backup ID
	 */
	protected function getLastBackupProfile($tag, $backupId = null)
	{
		$filters  = array(
			array('field' => 'tag', 'value' => $tag)
		);

		if (!empty($backupId))
		{
			$filters[] = array('field' => 'backupid', 'value' => $backupId);
		}

		$statList = Platform::getInstance()->get_statistics_list(array(
				'filters'  => $filters,
				'order' => array(
					'by' => 'id', 'order' => 'DESC'
				)
			)
		);

		if (is_array($statList))
		{
			$stat = array_pop($statList);

			return (int) $stat['profile_id'];
		}

		// Backup entry not found. If backupId was specified, try without a backup ID
		if (!empty($backupId))
		{
			return $this->getLastBackupProfile($tag);
		}

		// Else, return the default backup profile
		return 1;
	}
} 