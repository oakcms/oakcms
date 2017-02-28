<?php
/**
 * @package        solo
 * @copyright      2014-2017 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license        GNU GPL version 3 or later
 */

namespace Solo\Model\Json\Task;

use Akeeba\Engine\Factory;
use Akeeba\Engine\Platform;
use Solo\Model\Profiles;
use Solo\Model\Json\TaskInterface;

/**
 * Saves a backup profile
 */
class SaveProfile implements TaskInterface
{
	/**
	 * Return the JSON API task's name ("method" name). Remote clients will use it to call us.
	 *
	 * @return  string
	 */
	public function getMethodName()
	{
		return 'saveProfile';
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
			'profile'     => 0,
			'description' => null,
			'quickicon'   => null,
			'source'      => 0,
		);

		$defConfig = array_merge($defConfig, $parameters);

		$profile     = (int)$defConfig['profile'];
		$description = $defConfig['description'];
		$quickicon   = $defConfig['quickicon'];
		$source      = (int)$defConfig['source'];

		if ($profile <= 0)
		{
			$profile = null;
		}

		// At least one of these parameters is required
		if (empty($profile) && empty($source) && empty($description))
		{
			throw new \RuntimeException('Invalid profile ID', 404);
		}

		// Get a profile model
		$profileModel = new Profiles();

		// Load the profile
		$sourceId = empty($profile) ? $source : $profile;

		if (!empty($sourceId))
		{
			$profileModel->findOrFail($sourceId);
		}
		else
		{
			$profileModel->reset(true);
		}

		$profileModel->setFieldValue('id', $profile);

		if ($description)
		{
			$profileModel->setFieldValue('description', $description);
		}

		if (!is_null($quickicon))
		{
			$profileModel->setFieldValue('quickicon', (int)$quickicon);
		}

		$profileModel->save();

		return true;
	}
}