<?php
/**
 * @package        solo
 * @copyright      2014-2017 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license        GNU GPL version 3 or later
 */

namespace Solo\Model\Json\Task;

use Akeeba\Engine\Factory;
use Akeeba\Engine\Platform;
use Solo\Application;
use Solo\Model\Browser;
use Solo\Model\Dbfilters;
use Solo\Model\Profiles;
use Solo\Model\Json\TaskInterface;

/**
 * Set or unset a database filter
 */
class SetDBFilter implements TaskInterface
{
	/**
	 * Return the JSON API task's name ("method" name). Remote clients will use it to call us.
	 *
	 * @return  string
	 */
	public function getMethodName()
	{
		return 'setDBFilter';
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
			'profile' => 0,
			'root'    => '[SITEDB]',
			'table'   => '',
			'type'    => 'tables',
			'status'  => 1
		);

		$defConfig = array_merge($defConfig, $parameters);

		$profile = $filter->clean($defConfig['profile'], 'int');
		$root    = $filter->clean($defConfig['root'], 'string');
		$table   = $filter->clean($defConfig['table'], 'string');
		$type    = $filter->clean($defConfig['type'], 'cmd');
		$status  = $filter->clean($defConfig['status'], 'bool');

		// We need a valid profile ID
		if ($profile <= 0)
		{
			$profile = 1;
		}

		// We need a root
		if (empty($root))
		{
			throw new \RuntimeException('Unknown database root', 500);
		}

		// We need a table name
		if (empty($table))
		{
			throw new \RuntimeException('Table name is mandatory', 500);
		}

		// We need a table name
		if (empty($type))
		{
			throw new \RuntimeException('Filter type is mandatory', 500);
		}

		$session = Application::getInstance()->getContainer()->segment;
		$session->set('profile', $profile);

		// Load the configuration
		Platform::getInstance()->load_configuration($profile);

		/** @var \Solo\Model\Dbfilters $model */
		$model = new Dbfilters();

		if ($status)
		{
			$ret = $model->setFilter($root, $table, $type);
		}
		else
		{
			$ret = $model->remove($root, $table, $type);
		}

		return $ret;
	}
}