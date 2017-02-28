<?php
/**
 * @package        solo
 * @copyright      2014-2017 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license        GNU GPL version 3 or later
 */

namespace Solo\Model\Json\Task;

use Solo\Application;
use Solo\Model\Json\TaskInterface;
use Solo\Model\Profiles;

/**
 * Import the profile's configuration
 */
class ImportConfiguration implements TaskInterface
{
	/**
	 * Return the JSON API task's name ("method" name). Remote clients will use it to call us.
	 *
	 * @return  string
	 */
	public function getMethodName()
	{
		return 'importConfiguration';
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
			'profile' => 0,
			'data' => null,
		);

		$defConfig = array_merge($defConfig, $parameters);

		$profile_id = (int)$defConfig['profile'];
		$data = $defConfig['data'];

		if ($profile_id <= 0)
		{
			$profile_id = 0;
		}

		/** @var Profiles $profile */
		$profile = Application::getInstance()->getContainer()->factory->model('Profiles')->tmpInstance();

		if ($profile_id)
		{
			$profile->find($profile_id);
		}

		$profile->import($data);

		return true;
	}
}