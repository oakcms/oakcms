<?php
/**
 * @package        solo
 * @copyright      2014-2017 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license        GNU GPL version 3 or later
 */

namespace Solo\Controller;

use Awf\Application\Application;
use Awf\Date\Date;
use Awf\Mvc\Model;
use Awf\Router\Router;
use Awf\Text\Text;
use Akeeba\Engine\Platform;
use Akeeba\Engine\Factory;
use Solo\Model\Backup;

class Remote extends ControllerDefault
{
	public function execute($task)
	{
		$this->checkPermissions();
		define('AKEEBA_BACKUP_ORIGIN', 'frontend');

		return parent::execute($task);
	}

	public function main()
	{
		// Set the profile
		$this->setProfile();

		// Get the backup ID
		$backupId = $this->input->get('backupid', null, 'cmd');

		if (empty($backupId))
		{
			$backupId = null;
		}

		/** @var Backup $model */
		$model = Model::getTmpInstance($this->container->application_name, 'Backup', $this->container);

		$dateNow = new Date();

		$model->setState('tag', AKEEBA_BACKUP_ORIGIN);
		$model->setState('backupid', $backupId);
		$model->setState('description', Text::_('COM_AKEEBA_BACKUP_DEFAULT_DESCRIPTION') . ' ' . $dateNow->format(Text::_('DATE_FORMAT_LC2'), true));
		$model->setState('comment', '');

		$array = $model->startBackup();

		$backupId = $model->getState('backupid', null, 'cmd');

		$this->processEngineReturnArray($array, $backupId);
	}

	public function step()
	{
		// Set the profile
		$this->setProfile();

		// Get the backup ID
		$backupId = $this->input->get('backupid', null, 'cmd');

		if (empty($backupId))
		{
			$backupId = null;
		}

		/** @var Backup $model */
		$model = Model::getTmpInstance($this->container->application_name, 'Backup', $this->container);

		$model->setState('tag', AKEEBA_BACKUP_ORIGIN);
		$model->setState('backupid', $backupId);

		$array = $model->stepBackup();

		$backupId = $model->getState('backupid', null, 'cmd');

		$this->processEngineReturnArray($array, $backupId);
	}

	/**
	 * Used by the tasks to process Akeeba Engine's return array. Depending on the result and the component options we
	 * may throw text output or send an HTTP redirection header.
	 *
	 * @param   array   $array     The return array to process
	 * @param   string  $backupId  The backup ID (used to step the backup process)
	 */
	private function processEngineReturnArray($array, $backupId)
	{
		$noredirect = $this->input->get('noredirect', 0, 'int');

		if ($array['Error'] != '')
		{
			// An error occured
			if ($noredirect)
			{
				@ob_end_clean();
				header('Content-type: text/plain');
				header('Connection: close');
				echo '500 ERROR -- ' . $array['Error'];
				flush();
				$this->container->application->close();
			}

			throw new \RuntimeException($array['Error'], 500);
		}

		if ($array['HasRun'] == 1)
		{
			// All done
			Factory::nuke();
			Factory::getFactoryStorage()->reset();

			@ob_end_clean();
			header('Content-type: text/plain');
			header('Connection: close');
			echo '200 OK';
			flush();

			$this->container->application->close();
		}

		if ($noredirect != 0)
		{
			@ob_end_clean();
			header('Content-type: text/plain');
			header('Connection: close');
			echo "301 More work required -- BACKUPID ###$backupId###";
			flush();

			$this->container->application->close();
		}

		$router = $this->container->router;
		$url = 'index.php?view=remote&task=step&key=' . $this->input->get('key', '', 'none', 2) . '&profile=' . $this->input->get('profile', 1, 'int');

		if (!empty($backupId))
		{
			$url .= '&backupid=' . $backupId;
		}

		$this->setRedirect($router->route($url));
	}

	/**
	 * Check that the user has sufficient permissions, or die in error
	 *
	 * @return  void
	 */
	private function checkPermissions()
	{
		// Is frontend backup enabled?
		$febEnabled = Platform::getInstance()->get_platform_configuration_option('frontend_enable', 0);
		$febEnabled = in_array($febEnabled, array('on', 'checked', 'true', 1, 'yes'));

		$validKey = Platform::getInstance()->get_platform_configuration_option('frontend_secret_word', '');

		if (!\Akeeba\Engine\Util\Complexify::isStrongEnough($validKey, false))
		{
			$febEnabled = false;
		}

		$validKeyTrim = trim($validKey);

		if (!$febEnabled || empty($validKey))
		{
			throw new \RuntimeException(Text::_('SOLO_REMOTE_ERROR_NOT_ENABLED'), 403);
		}

		// Is the key good?
		$key = $this->input->get('key', '', 'none', 2);

		if (($key != $validKey) || (empty($validKeyTrim)))
		{
			throw new \RuntimeException(Text::_('SOLO_REMOTE_ERROR_INVALID_KEY'), 403);
		}
	}

	/**
	 * Set the active profile from the input parameters
	 */
	private function setProfile()
	{
		// Set profile
		$profile = $this->input->get('profile', 1, 'int');

		if (empty($profile))
		{
			$profile = 1;
		}

		$session = Application::getInstance()->getContainer()->segment;
		$session->profile = $profile;

		/**
		 * DO NOT REMOVE!
		 *
		 * The Model will only try to load the configuration after nuking the factory. This causes Profile 1 to be
		 * loaded first. Then it figures out it needs to load a different profile and it does – but the protected keys
		 * are NOT replaced, meaning that certain configuration parameters are not replaced. Most notably, the chain.
		 * This causes backups to behave weirdly. So, DON'T REMOVE THIS UNLESS WE REFACTOR THE MODEL.
		 */
		Platform::getInstance()->load_configuration($profile);
	}
} 