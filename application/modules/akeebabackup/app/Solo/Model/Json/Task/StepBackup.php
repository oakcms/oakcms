<?php
/**
 * @package        solo
 * @copyright      2014-2017 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license        GNU GPL version 3 or later
 */

namespace Solo\Model\Json\Task;

use Akeeba\Engine\Factory;
use Akeeba\Engine\Platform;
use Awf\Application\Application;
use Awf\Mvc\Model;
use Solo\Model\Backup;
use Solo\Model\Json\TaskInterface;

/**
 * Step through a backup job
 */
class StepBackup implements TaskInterface
{
	/**
	 * Return the JSON API task's name ("method" name). Remote clients will use it to call us.
	 *
	 * @return  string
	 */
	public function getMethodName()
	{
		return 'stepBackup';
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
			'profile'  => null,
			'tag'      => AKEEBA_BACKUP_ORIGIN,
			'backupid' => null,
		);

		$defConfig = array_merge($defConfig, $parameters);

		$profile  = $filter->clean($defConfig['profile'], 'int');
		$tag      = $filter->clean($defConfig['tag'], 'cmd');
		$backupid = $filter->clean($defConfig['backupid'], 'cmd');

		$container = Application::getInstance()->getContainer();
		$session   = $container->segment;

		if (!empty($profile))
		{
			$profile = max(1, $profile); // Make sure $profile is a positive integer >= 1
			$session->set('profile', $profile);
			define('AKEEBA_PROFILE', $profile);
		}

		/** @var Backup $model */
		$model = Model::getTmpInstance($container->application_name, 'Backup', $container);

		$model->setState('tag', $tag);
		$model->setState('backupid', $backupid);
		$array = $model->stepBackup(false);

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