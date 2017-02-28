<?php
/**
 * Akeeba Engine
 * The modular PHP5 site backup engine
 * @copyright Copyright (c)2009-2016 Nicholas K. Dionysopoulos
 * @license   GNU GPL version 3 or, at your option, any later version
 * @package   akeebaengine
 *
 */

namespace Akeeba\Engine\Filter;

use Akeeba\Engine\Filter\Base as FilterBase;
use Akeeba\Engine\Factory;
use Akeeba\Engine\Platform;

// Protection against direct access
defined('AKEEBAENGINE') or die();

/**
 * Subdirectories exclusion filter
 */
class PlatformSkipDirectories extends FilterBase
{
	function __construct()
	{
		$this->object = 'dir';
		$this->subtype = 'children';
		$this->method = 'direct';
		$this->filter_name = 'PlatformSkipDirectories';

		if (Factory::getKettenrad()->getTag() == 'restorepoint')
		{
			$this->enabled = false;
		}

		// We take advantage of the filter class magic to inject our custom filters
		$configuration = Factory::getConfiguration();

		// Get the site's root
		$root = $configuration->get('akeeba.platform.newroot', '[SITEROOT]');

		$this->filter_data[$root] = array(
			// Output & temp directory of the application
			$this->treatDirectory($configuration->get('akeeba.basic.output_directory')),
			// Default backup output directory
			$this->treatDirectory(APATH_BASE . '/backups'),
		);

		if (!$configuration->get('akeeba.platform.addsolo', 0))
		{
			$this->filter_data[$root][] = $this->treatDirectory(APATH_BASE);
		}
		else
		{
			$soloRoot = APATH_BASE;
			$this->filter_data[$soloRoot] = array(
				$this->treatDirectory($configuration->get('akeeba.basic.output_directory'), $soloRoot),
				'backups',
				$this->treatDirectory(APATH_BASE . '/backups', $soloRoot),
			);
		}

		parent::__construct();
	}
}