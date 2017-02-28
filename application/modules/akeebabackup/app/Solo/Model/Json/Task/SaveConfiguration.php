<?php
/**
 * @package        solo
 * @copyright      2014-2017 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license        GNU GPL version 3 or later
 */

namespace Solo\Model\Json\Task;

use Akeeba\Engine\Factory;
use Akeeba\Engine\Platform;
use Solo\Model\Json\TaskInterface;

/**
 * Save the configuration for a given profile
 */
class SaveConfiguration implements TaskInterface
{
	/**
	 * Return the JSON API task's name ("method" name). Remote clients will use it to call us.
	 *
	 * @return  string
	 */
	public function getMethodName()
	{
		return 'saveConfiguration';
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
			'profile'      => -1,
			'engineconfig' => array()
		);

		$defConfig = array_merge($defConfig, $parameters);

		$profile = (int)$defConfig['profile'];
		$data    = $defConfig['engineconfig'];

		if (empty($profile))
		{
			throw new \RuntimeException('Invalid profile ID', 404);
		}

		// Forbid stupidly selecting the site's root as the output or temporary directory
		if (array_key_exists('akeeba.basic.output_directory', $data))
		{
			$folder = $data['akeeba.basic.output_directory'];
			$folder = Factory::getFilesystemTools()->translateStockDirs($folder, true, true);

			$check = Factory::getFilesystemTools()->translateStockDirs('[SITEROOT]', true, true);

			if ($check == $folder)
			{
				$data['akeeba.basic.output_directory'] = '[DEFAULT_OUTPUT]';
			}
		}

		// Merge it
		$config        = Factory::getConfiguration();
		$protectedKeys = $config->getProtectedKeys();
		$config->resetProtectedKeys();
		$config->mergeArray($data, false, false);
		$config->setProtectedKeys($protectedKeys);

		// Save configuration
		return Platform::getInstance()->save_configuration($profile);
	}
}