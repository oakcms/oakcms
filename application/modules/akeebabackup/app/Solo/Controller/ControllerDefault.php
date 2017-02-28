<?php
/**
 * @package		solo
 * @copyright	2014-2017 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license		GNU GPL version 3 or later
 */

namespace Solo\Controller;

use Awf\Text\Text;

/**
 * Common controller superclass. Reserved for future use.
 */
abstract class ControllerDefault extends \Awf\Mvc\Controller
{
	protected $aclChecks = array(
		'alice'          => array('*' => array('configure')),
		'backup'         => array('*' => array('backup')),
		'browser'        => array('*' => array('configure')),
		'configuration'  => array('*' => array('configure')),
		'dbfilters'      => array('*' => array('configure')),
		'discover'       => array('*' => array('configure')),
		'extradirs'      => array('*' => array('configure')),
		'fsfilters'      => array('*' => array('configure')),
		'log'            => array('*' => array('configure')),
		'manage'         => array(
			'manage'      => array(),
			'showComment' => array('backup'),
			'cancel'      => array('backup'),
			'download'    => array('download'),
			'restore'     => array('configure'),
			'*'           => array('download'),
		),
		'multidb'        => array('*' => array('configure')),
		'profiles'       => array('*' => array('configure')),
		'profile'        => array('*' => array('configure')),
		'regexdbfilters' => array('*' => array('configure')),
		'regexfsfilters' => array('*' => array('configure')),
		'remotefiles'    => array('*' => array('download')),
		'restore'        => array('*' => array('configure')),
		's3import'       => array('*' => array('configure')),
		'schedule'       => array('*' => array('configure')),
		'sysconfig'      => array('*' => array('configure', 'backup', 'download')),
		'transfer'       => array('*' => array('download')),
		'update'         => array('*' => array('configure', 'backup', 'download')),
		'upload'         => array('*' => array('backup')),
		'users'          => array('*' => array('configure', 'backup', 'download')),
		'wizard'         => array('*' => array('configure')),
	);

	/**
	 * Executes a given controller task. The onBefore<task> and onAfter<task>
	 * methods are called automatically if they exist.
	 *
	 * @param   string  $task The task to execute, e.g. "browse"
	 *
	 * @return  null|bool  False on execution failure
	 *
	 * @throws  \Exception  When the task is not found
	 */
	public function execute($task)
	{
		$view = $this->input->getCmd('view', 'main');

		$this->aclCheck($view, $task);

		return parent::execute($task);
	}

	/**
	 * Performs automatic access control checks
	 *
	 * @param   string  $view  The view being accessed
	 * @param   string  $task  The task being accessed
	 *
	 * @throws \RuntimeException
	 */
	protected function aclCheck($view, $task)
	{
		$view = strtolower($view);
		$task = strtolower($task);

		if (!isset($this->aclChecks[$view]))
		{
			return;
		}

		if (!isset($this->aclChecks[$view][$task]))
		{
			if (!isset($this->aclChecks[$view]['*']))
			{
				return;
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
				throw new \RuntimeException(Text::_('SOLO_ERR_ACLDENIED'), 403);
			}
		}
	}
}