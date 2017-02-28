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
 * Get the version information of Akeeba Solo
 */
class GetVersion implements TaskInterface
{
	/**
	 * Return the JSON API task's name ("method" name). Remote clients will use it to call us.
	 *
	 * @return  string
	 */
	public function getMethodName()
	{
		return 'getVersion';
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

		$updateInformation = $update->getUpdateInformation();

		$edition = AKEEBABACKUP_PRO ? 'pro' : 'core';

		return (object)array(
			'api'        => AKEEBA_JSON_API_VERSION,
			'component'  => AKEEBABACKUP_VERSION,
			'date'       => AKEEBABACKUP_DATE,
			'edition'    => $edition,
			'updateinfo' => $updateInformation,
		);
	}
}