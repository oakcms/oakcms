<?php
/**
 * Akeeba Engine
 * The modular PHP5 site backup engine
 * @copyright Copyright (c)2009-2016 Nicholas K. Dionysopoulos
 * @license GNU GPL version 3 or, at your option, any later version
 * @package akeebaengine
 *
 */

namespace Akeeba\Engine\Filter;

use Akeeba\Engine\Filter\Base as FilterBase;
use Akeeba\Engine\Factory;
use Akeeba\Engine\Platform;

// Protection against direct access
defined('AKEEBAENGINE') or die();

/**
 * Add site's root to the backup set.
 */
class SiteRoot extends FilterBase
{
	public function __construct()
	{
		// This is a directory inclusion filter.
		$this->object	= 'dir';
		$this->subtype	= 'inclusion';
		$this->method	= 'direct';
		$this->filter_name = 'SiteRoot';

		// Directory inclusion format:
		// array(real_directory, add_path)
		$add_path = null; // A null add_path means that we dump this dir's contents in the archive's root

		// We take advantage of the filter class magic to inject our custom filters
		$configuration = Factory::getConfiguration();

		$root = $configuration->get('akeeba.platform.newroot', '[SITEROOT]');

		$this->filter_data[] = array (
			$root,
			$add_path
		);

		if ($configuration->get('akeeba.platform.addsolo', 0))
		{
			$soloRoot = APATH_BASE;
			if (@realpath($soloRoot) != @realpath($root))
			{
				array_unshift($this->filter_data, array($soloRoot, 'solo'));
			}
		}

		parent::__construct();
	}
}