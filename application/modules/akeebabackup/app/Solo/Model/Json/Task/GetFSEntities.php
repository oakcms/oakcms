<?php
/**
 * @package        solo
 * @copyright      2014-2017 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license        GNU GPL version 3 or later
 */

namespace Solo\Model\Json\Task;

use Akeeba\Engine\Platform;
use Solo\Application;
use Solo\Model\Fsfilters;
use Solo\Model\Json\TaskInterface;

/**
 * Get the filesystem entities along with their filtering status (typically for rendering a GUI)
 */
class GetFSEntities implements TaskInterface
{
	/**
	 * Return the JSON API task's name ("method" name). Remote clients will use it to call us.
	 *
	 * @return  string
	 */
	public function getMethodName()
	{
		return 'getFSEntities';
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
			'profile'      => 0,
			'root'         => '[SITEROOT]',
			'subdirectory' => '',
		);

		$defConfig = array_merge($defConfig, $parameters);

		$profile      = $filter->clean($defConfig['profile'], 'int');
		$root         = $filter->clean($defConfig['root'], 'string');
		$subdirectory = $filter->clean($defConfig['subdirectory'], 'path');
		$crumbs       = array();

		// We need a valid profile ID
		if ($profile <= 0)
		{
			$profile = 1;
		}

		// We need a root
		if (empty($root))
		{
			throw new \RuntimeException('Unknown filesystem root', 500);
		}

		// Get the subdirectory and explode it to its parts
		if (!empty($subdirectory))
		{
			$subdirectory = trim($subdirectory, '/');
		}

		if (!empty($subdirectory))
		{
			$crumbs = explode('/', $subdirectory);
		}

		// Set the active profile
		$session = Application::getInstance()->getContainer()->segment;
		$session->set('profile', $profile);

		// Load the configuration
		Platform::getInstance()->load_configuration($profile);

		/** @var \Solo\Model\Fsfilters $model */
		$model = new Fsfilters();

		return $model->make_listing($root, $crumbs);
	}
}