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
 * List the backup records
 */
class ListBackups implements TaskInterface
{
	/**
	 * Return the JSON API task's name ("method" name). Remote clients will use it to call us.
	 *
	 * @return  string
	 */
	public function getMethodName()
	{
		return 'listBackups';
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
			'from'  => 0,
			'limit' => 50
		);

		$defConfig = array_merge($defConfig, $parameters);

		$from  = (int)$defConfig['from'];
		$limit = (int)$defConfig['limit'];

		$model = new Manage();
		$model->setState('limitstart', $from);
		$model->setState('limit', $limit);

		return $model->getStatisticsListWithMeta(false);
	}
}