<?php
/**
 * @package		solo
 * @copyright	2014-2017 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license		GNU GPL version 3 or later
 */

namespace Solo\Controller;
use Awf\Date\Date;
use Akeeba\Engine\Platform;
use Akeeba\Engine\Factory;

/**
 * The controller for the Backup view
 */
class Backup extends ControllerDefault
{
	/**
	 * Default task; shows the initial page where the user selects a profile
	 * and enters description and comment
	 *
	 * @return  void
	 */
	public function main()
	{
		// Push models to view
		$model = $this->getModel();

		$newProfile = (int)$this->input->get('profile', -10, 'int');

		if ($newProfile > 0)
		{
			$this->csrfProtection();
			$this->applyProfile();
		}

		$srpinfo = array(
			'tag'				=> $this->input->get('tag', 'backend', 'cmd'),
			'type'				=> $this->input->get('type', '', 'cmd'),
			'name'				=> $this->input->get('name', '', 'cmd'),
			'group'				=> $this->input->get('group', '', 'cmd'),
			'customdirs'		=> $this->input->get('customdirs', array(), 'array'),
			'extraprefixes'		=> $this->input->get('extraprefixes', array(), 'array'),
			'customtables'		=> $this->input->get('customtables', array(), 'array'),
			'skiptables'		=> $this->input->get('skiptables', array(), 'array'),
			'xmlname'			=> $this->input->get('xmlname', '', 'string')
		);

		$model->setState('srpinfo',	$srpinfo);

		$model->setState('description',	$this->input->get('description', null, 'raw'));
		$model->setState('comment',		$this->input->get('comment', null, 'raw'));

		$model->setState('jpskey',		$this->input->get('jpskey', '', 'raw'));
		$model->setState('angiekey',	$this->input->get('angiekey', '', 'raw'));
		$model->setState('returnurl',	$this->input->get('returnurl', '', 'raw'));
		$model->setState('backupid',	$this->input->get('backupid', null, 'cmd'));

		$this->display();
	}

	/**
	 * Handle an AJAX request
	 *
	 * @return  void
	 */
	public function ajax()
	{
		$model = $this->getModel();

		$model->setState('profile',			$this->input->get('profile', Platform::getInstance()->get_active_profile(), 'int'));
		$model->setState('ajax',			$this->input->get('ajax', '', 'cmd'));
		$model->setState('description',		$this->input->get('description', '', 'raw'));
		$model->setState('comment',			$this->input->get('comment', '','default', 'raw'));
		$model->setState('jpskey',			$this->input->get('jpskey', '', 'raw'));
		$model->setState('angiekey',		$this->input->get('angiekey', '', 'raw'));
		$model->setState('backupid',		$this->input->get('backupid', null, 'cmd'));
		$model->setState('errorMessage', 	$this->input->get('errorMessage', '', 'string'));

		$model->setState('tag',				$this->input->get('tag', 'backend', 'cmd'));
		$model->setState('type',			strtolower($this->input->get('type', '', 'cmd')));
		$model->setState('name',			strtolower($this->input->get('name', '', 'cmd')));
		$model->setState('group',			strtolower($this->input->get('group', '', 'cmd')));

		$model->setState('customdirs',		$this->input->get('customdirs', array(),'array'));
		$model->setState('customfiles',		$this->input->get('customfiles', array(),'array'));
		$model->setState('extraprefixes',	$this->input->get('extraprefixes', array(),'array'));
		$model->setState('customtables',	$this->input->get('customtables', array(),'array'));
		$model->setState('skiptables',		$this->input->get('skiptables', array(),'array'));
		$model->setState('langfiles',		$this->input->get('langfiles', array(),'array'));
		$model->setState('xmlname',			$this->input->getString('xmlname', ''));

		define('AKEEBA_BACKUP_ORIGIN',		$this->input->get('tag', 'backend', 'cmd'));

		$ret_array = $model->runBackup();

		@ob_end_clean();
		header('Content-type: text/plain');
		header('Connection: close');
		echo '###' . json_encode($ret_array) . '###';
		flush();

		$this->container->application->close();
	}

	/**
	 * Applies a profile change based on the request's "profile" parameter
	 */
	private function applyProfile()
	{
		// Get the currently active profile
		$current_profile = Platform::getInstance()->get_active_profile();

		// Get the profile from the request
		$profile = (int)$this->input->get('profile', $current_profile, 'int');

		// Sanity check
		if (!is_numeric($profile) || ($profile <= 0))
		{
			$profile = $current_profile;
		}

		// Change and reload the profile if necessary
		if ($profile != $current_profile)
		{
			$session = \Awf\Application\Application::getInstance()->getContainer()->segment;
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
} 