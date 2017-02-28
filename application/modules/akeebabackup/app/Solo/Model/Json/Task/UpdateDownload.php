<?php
/**
 * @package        solo
 * @copyright      2014-2017 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license        GNU GPL version 3 or later
 */

namespace Solo\Model\Json\Task;

use Solo\Model\Json\TaskInterface;
use Solo\Model\Update;

/**
 * Download the update package
 */
class UpdateDownload implements TaskInterface
{
	/**
	 * Return the JSON API task's name ("method" name). Remote clients will use it to call us.
	 *
	 * @return  string
	 */
	public function getMethodName()
	{
		return 'updateDownload';
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
		$update = new Update();

		$update->prepareDownload();

		$ret = $update->stepDownload(false);

		if (!$ret['status'])
		{
			throw new \RuntimeException($ret['errorCode'] . ': ' . $ret['error'], 500);
		}

		return true;
	}
}