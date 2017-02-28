<?php
/**
 * @package        solo
 * @copyright      2014-2017 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license        GNU GPL version 3 or later
 */

namespace Solo\Model\Json\Task;

use Akeeba\Engine\Factory;
use Akeeba\Engine\Platform;
use Awf\Date\Date;
use Awf\Mvc\Model;
use Awf\Text\Text;
use Awf\Application\Application;
use Solo\Model\Backup;
use Solo\Model\Json\TaskInterface;

/**
 * Start a backup job
 */
class StartBackup implements TaskInterface
{
	/**
	 * Return the JSON API task's name ("method" name). Remote clients will use it to call us.
	 *
	 * @return  string
	 */
	public function getMethodName()
	{
		return 'startBackup';
	}

	/**
	 * Execute the JSON API task
	 *
	 * @param   array $parameters The parameters to this task
	 *
	 * @return  mixed
	 *
	 * @throws  \RuntimeException  In case of an error
	 */
	public function execute(array $parameters = array())
	{
		$filter = \Awf\Input\Filter::getInstance();

		// Get the passed configuration values
		$defConfig = array(
			'profile'     => 1,
			'description' => '',
			'comment'     => '',
			'backupid'    => null,
			'overrides'   => array(),
		);

		$defConfig = array_merge($defConfig, $parameters);

		$profile     = (int) $defConfig['profile'];
		$profile     = max(1, $profile); // Make sure $profile is a positive integer >= 1
		$description = $filter->clean($defConfig['description'], 'string');
		$comment     = $filter->clean($defConfig['comment'], 'string');
		$backupid    = $filter->clean($defConfig['backupid'], 'cmd');
		$backupid    = empty($backupid) ? null : $backupid; // Otherwise the Engine doesn't set a backup ID
		$overrides   = $filter->clean($defConfig['overrides'], 'array');

		$container = Application::getInstance()->getContainer();
		$session   = $container->segment;
		$session->set('profile', $profile);

		define('AKEEBA_PROFILE', $profile);

		/**
		 * DO NOT REMOVE!
		 *
		 * The Model will only try to load the configuration after nuking the factory. This causes Profile 1 to be
		 * loaded first. Then it figures out it needs to load a different profile and it does – but the protected keys
		 * are NOT replaced, meaning that certain configuration parameters are not replaced. Most notably, the chain.
		 * This causes backups to behave weirdly. So, DON'T REMOVE THIS UNLESS WE REFACTOR THE MODEL.
		 */
		Platform::getInstance()->load_configuration($profile);

		/** @var Backup $model */
		$model = Model::getTmpInstance($container->application_name, 'Backup', $container);

		$model->setState('tag', AKEEBA_BACKUP_ORIGIN);
		$model->setState('backupid', $backupid);
		$model->setState('description', $description);
		$model->setState('comment', $comment);

		$array = $model->startBackup($overrides);

		if ($array['Error'] != '')
		{
			throw new \RuntimeException('A backup error has occurred: ' . $array['Error'], 500);
		}

		// BackupID contains the numeric backup record ID. backupid contains the backup id (usually in the form id123)
		$statistics        = Factory::getStatistics();
		$array['BackupID'] = $statistics->getId();

		// Remote clients expect a boolean, not an integer.
		$array['HasRun'] = ($array['HasRun'] === 0);

		return $array;
	}
}