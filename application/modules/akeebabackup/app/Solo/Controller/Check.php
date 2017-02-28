<?php
/**
 * @package        solo
 * @copyright      2014-2017 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license        GNU GPL version 3 or later
 */

namespace Solo\Controller;

use Awf\Application\Application;
use Awf\Mvc\Model;
use Awf\Text\Text;
use Akeeba\Engine\Platform;
use Akeeba\Engine\Factory;

class Check extends ControllerDefault
{
	public function execute($task)
	{
		$this->checkPermissions();
		define('AKEEBA_BACKUP_ORIGIN', 'frontend');

		return parent::execute('main');
	}

	public function main()
	{
        $cpanelModel = Model::getInstance('Solo', 'Main', $this->container);
        $result = $cpanelModel->notifyFailed();

        $message  = $result['result'] ? '200 ' : '500 ';
        $message .= implode(', ', $result['message']);

        @ob_end_clean();
		header('Content-type: text/plain');
		header('Connection: close');
        echo $message;
        flush();

        $this->container->application->close();
	}

	/**
	 * Check that the user has sufficient permissions, or die in error
	 *
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

		Platform::getInstance()->load_configuration($profile);
	}
} 