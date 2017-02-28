<?php
/**
 * @package        solo
 * @copyright      2014-2017 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license        GNU GPL version 3 or later
 */

namespace Solo\Model\Json\Task;

use Akeeba\Engine\Platform;
use Solo\Model\Json\TaskInterface;

/**
 * Get the update information
 */
class UpdateGetInformation implements TaskInterface
{
	/**
	 * Return the JSON API task's name ("method" name). Remote clients will use it to call us.
	 *
	 * @return  string
	 */
	public function getMethodName()
	{
		return 'updateGetInformation';
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
			'force' => 0
		);

		$defConfig = array_merge($defConfig, $parameters);

		$force = $filter->clean($defConfig['force'], 'bool');

		$update = new \Solo\Model\Update;

		$updateInformation = $update->getUpdateInformation($force);

		return (object)$updateInformation;
	}
}