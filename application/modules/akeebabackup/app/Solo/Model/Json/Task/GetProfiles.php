<?php
/**
 * @package        solo
 * @copyright      2014-2017 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license        GNU GPL version 3 or later
 */

namespace Solo\Model\Json\Task;

use Solo\Model\Json\TaskInterface;
use Solo\Model\Profiles;

/**
 * Get a list of known backup profiles
 */
class GetProfiles implements TaskInterface
{
	/**
	 * Return the JSON API task's name ("method" name). Remote clients will use it to call us.
	 *
	 * @return  string
	 */
	public function getMethodName()
	{
		return 'getProfiles';
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
		$model    = new Profiles();
		$profiles = $model->get(true);
		$ret      = array();

		if (count($profiles))
		{
			foreach ($profiles as $profile)
			{
				$temp       = new \stdClass();
				$temp->id   = $profile->id;
				$temp->name = $profile->description;
				$ret[]      = $temp;
			}
		}

		return $ret;
	}
}