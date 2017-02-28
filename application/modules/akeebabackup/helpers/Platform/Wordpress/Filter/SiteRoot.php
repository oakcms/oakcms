<?php
/**
 * Akeeba Engine
 * The modular PHP5 site backup engine
 *
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
 * Add the site's root to the backup set.
 */
class SiteRoot extends FilterBase
{
	public function __construct()
	{
		// This is a directory inclusion filter.
		$this->object = 'dir';
		$this->subtype = 'inclusion';
		$this->method = 'direct';
		$this->filter_name = 'SiteRoot';

		// Directory inclusion format:
		// array(real_directory, add_path)
		$add_path = null; // A null add_path means that we dump this dir's contents in the archive's root

		// We take advantage of the filter class magic to inject our custom filters
		$configuration = Factory::getConfiguration();

		if ($configuration->get('akeeba.platform.override_root', 0))
		{
			$root = $configuration->get('akeeba.platform.newroot', '[SITEROOT]');
		}
		else
		{
			$root = '[SITEROOT]';
		}

		$this->filter_data[] = array(
			$root,
			$add_path
		);

		parent::__construct();
	}
}