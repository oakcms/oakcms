<?php
/**
 * @package        solo
 * @copyright      2014-2017 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license        GNU GPL version 3 or later
 */

namespace Solo\Model\Json\Task;

use Akeeba\Engine\Platform;
use Solo\Model\Json\TaskInterface;
use Solo\Model\Manage;

/**
 * Delete a backup record
 */
class Delete implements TaskInterface
{
	/**
	 * Return the JSON API task's name ("method" name). Remote clients will use it to call us.
	 *
	 * @return  string
	 */
	public function getMethodName()
	{
		return 'delete';
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
		// Get the passed configuration values
		$defConfig = array(
			'backup_id' => 0,
		);

		$defConfig = array_merge($defConfig, $parameters);

		$backup_id = (int)$defConfig['backup_id'];

		$model = new Manage();
		$model->setState('id', $backup_id);

		try
		{
			$model->delete();
		}
		catch (\Exception $e)
		{
			throw new \RuntimeException($e->getMessage(), 500);
		}

		return true;
	}
}