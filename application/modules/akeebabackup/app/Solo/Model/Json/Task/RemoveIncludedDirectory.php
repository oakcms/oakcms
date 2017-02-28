<?php
/**
 * @package        solo
 * @copyright      2014-2017 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license        GNU GPL version 3 or later
 */

namespace Solo\Model\Json\Task;

use Akeeba\Engine\Platform;
use Solo\Application;
use Solo\Model\Extradirs;
use Solo\Model\Json\TaskInterface;

/**
 * Remove an extra directory definition
 */
class RemoveIncludedDirectory implements TaskInterface
{
	/**
	 * Return the JSON API task's name ("method" name). Remote clients will use it to call us.
	 *
	 * @return  string
	 */
	public function getMethodName()
	{
		return 'removeIncludedDirectory';
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
			'profile'       => 0,
			'uuid'          => '',
		);

		$defConfig = array_merge($defConfig, $parameters);

		$profile       = $filter->clean($defConfig['profile'], 'int');
		$uuid          = $filter->clean($defConfig['uuid'], 'string');

		// We need a valid profile ID
		if ($profile <= 0)
		{
			$profile = 1;
		}

		// We need a uuid
		if (empty($uuid))
		{
			throw new \RuntimeException('UUID is required', 500);
		}

		$session = Application::getInstance()->getContainer()->segment;
		$session->set('profile', $profile);

		// Load the configuration
		Platform::getInstance()->load_configuration($profile);

		/** @var \Solo\Model\Extradirs $model */
		$model = new Extradirs();

		return $model->remove($uuid);
	}
}