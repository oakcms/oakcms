<?php
/**
 * @package        solo
 * @copyright      2014-2017 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license        GNU GPL version 3 or later
 */

namespace Solo\Model\Json\Task;

use Akeeba\Engine\Platform;
use Solo\Application;
use Solo\Model\Multidb;
use Solo\Model\Json\TaskInterface;

/**
 * Set up or edit an extra database definition
 */
class SetIncludedDB implements TaskInterface
{
	/**
	 * Return the JSON API task's name ("method" name). Remote clients will use it to call us.
	 *
	 * @return  string
	 */
	public function getMethodName()
	{
		return 'setIncludedDB';
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
			'profile'    => 0,
			'name'       => '',
			'connection' => array(),
			'test'       => true,
		);

		$defConfig = array_merge($defConfig, $parameters);

		$profile    = $filter->clean($defConfig['profile'], 'int');
		$name       = $filter->clean($defConfig['name'], 'string');
		$connection = $filter->clean($defConfig['connection'], 'array');
		$test       = $filter->clean($defConfig['test'], 'bool');

		// We need a valid profile ID
		if ($profile <= 0)
		{
			$profile = 1;
		}

		if (
			empty($connection) || !isset($connection['host']) || !isset($connection['driver'])
			|| !isset($connection['database']) || !isset($connection['user'])
			|| !isset($connection['password'])
		)
		{
			throw new \RuntimeException('Connection information missing or incomplete', 500);
		}

		$session = Application::getInstance()->getContainer()->segment;
		$session->set('profile', $profile);

		// Load the configuration
		Platform::getInstance()->load_configuration($profile);

		/** @var \Solo\Model\Multidb $model */
		$model = new Multidb();

		if ($test)
		{
			$result = $model->test($connection);

			if (!$result['status'])
			{
				throw new \RuntimeException('Connection test failed: ' . $result['message'], 500);
			}
		}

		return $model->setFilter($name, $connection);
	}
}