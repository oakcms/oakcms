<?php
/**
 * @package        solo
 * @copyright      2014-2017 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license        GNU GPL version 3 or later
 */

namespace Solo\Model\Json\Task;

use Akeeba\Engine\Factory;
use Akeeba\Engine\Platform;
use Solo\Model\Browser;
use Solo\Model\Profiles;
use Solo\Model\Json\TaskInterface;

/**
 * Return folder browser results
 */
class Browse implements TaskInterface
{
	/**
	 * Return the JSON API task's name ("method" name). Remote clients will use it to call us.
	 *
	 * @return  string
	 */
	public function getMethodName()
	{
		return 'browse';
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
			'folder'        => '',
			'processfolder' => 0
		);

		$defConfig = array_merge($defConfig, $parameters);

		$folder        = $filter->clean($defConfig['folder'], 'string');
		$processFolder = $filter->clean($defConfig['processfolder'], 'bool');

		/** @var \Solo\Model\Browser $model */
		$model = new Browser();
		$model->setState('folder', $folder);
		$model->setState('processfolder', $processFolder);
		$model->makeListing();

		$ret = array(
			'folder'                => $model->getState('folder'),
			'folder_raw'            => $model->getState('folder_raw'),
			'parent'                => $model->getState('parent'),
			'exists'                => $model->getState('exists'),
			'inRoot'                => $model->getState('inRoot'),
			'openbasedirRestricted' => $model->getState('openbasedirRestricted'),
			'writable'              => $model->getState('writable'),
			'subfolders'            => $model->getState('subfolders'),
			'breadcrumbs'           => $model->getState('breadcrumbs'),
		);

		return $ret;
	}
}