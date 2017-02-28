<?php
/**
 * @package        solo
 * @copyright      2014-2017 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license        GNU GPL version 3 or later
 */

namespace Solo\Model\Json\Task;

use Akeeba\Engine\Platform;
use Solo\Application;
use Solo\Model\Regexdbfilters;
use Solo\Model\Json\TaskInterface;

/**
 * Set or unset a Regex database filter
 */
class SetRegexDBFilter implements TaskInterface
{
	/**
	 * Return the JSON API task's name ("method" name). Remote clients will use it to call us.
	 *
	 * @return  string
	 */
	public function getMethodName()
	{
		return 'setRegexDBFilter';
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
			'regex'   => '',
			'type'    => 'tables',
			'status'  => 1
		);

		$defConfig = array_merge($defConfig, $parameters);

		$profile = $filter->clean($defConfig['profile'], 'int');
		$root    = $filter->clean($defConfig['root'], 'string');
		$regex   = $filter->clean($defConfig['regex'], 'string');
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

		// We need a regex name
		if (empty($regex))
		{
			throw new \RuntimeException('Regex is mandatory', 500);
		}

		// We need a regex name
		if (empty($type))
		{
			throw new \RuntimeException('Filter type is mandatory', 500);
		}

		$session = Application::getInstance()->getContainer()->segment;
		$session->set('profile', $profile);

		// Load the configuration
		Platform::getInstance()->load_configuration($profile);

		/** @var \Solo\Model\Regexdbfilters $model */
		$model = new Regexdbfilters();

		if ($status)
		{
			$ret = $model->setFilter($type, $root, $regex);
		}
		else
		{
			$ret = $model->remove($type, $root, $regex);
		}

		return $ret;
	}
}