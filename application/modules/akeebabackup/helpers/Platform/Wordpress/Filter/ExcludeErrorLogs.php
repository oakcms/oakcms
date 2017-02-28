<?php
/**
 * Akeeba Engine
 * The modular PHP5 site backup engine
 * @copyright Copyright (c)2014-2017 Nicholas K. Dionysopoulos
 * @license   GNU GPL version 3 or, at your option, any later version
 * @package   akeebabackupwp
 *
 */

namespace Akeeba\Engine\Filter;

use Akeeba\Engine\Filter\Base as FilterBase;
use Akeeba\Engine\Factory;
use Akeeba\Engine\Platform;

// Protection against direct access
defined('AKEEBAENGINE') or die();

/**
 * Automatically exclude error_log files from the backup set
 */
class ExcludeErrorLogs extends FilterBase
{
	public function __construct()
	{
		$this->object = 'file';
		$this->subtype = 'all';
		$this->method = 'regex';
		$this->filter_name = 'ExcludeErrorLogs';

		if (Factory::getKettenrad()->getTag() == 'restorepoint')
		{
			$this->enabled = false;
		}

		// Get the site's root
		$configuration = Factory::getConfiguration();

		if ($configuration->get('akeeba.platform.override_root', 0))
		{
			$root = $configuration->get('akeeba.platform.newroot', '[SITEROOT]');
		}
		else
		{
			$root = '[SITEROOT]';
		}

		if (stristr($root, '['))
		{
			$root = Factory::getFilesystemTools()->translateStockDirs($root);
		}

		// We take advantage of the filter class magic to inject our custom filters
		$this->filter_data[$root] = array(
			'#^error_log$#',
			'#/error_log$#',
		);

		parent::__construct();
	}

}