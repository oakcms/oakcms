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
 * Exclude files which we know they should not be backed up
 */
class ExcludeFiles extends FilterBase
{
	public function __construct()
	{
		$this->object = 'file';
		$this->subtype = 'all';
		$this->method = 'direct';
		$this->filter_name = 'ExcludeFiles';

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

		// We take advantage of the filter class magic to inject our custom filters
		$this->filter_data[$root] = array(
			'kickstart.php',
			$this->treatDirectory(APATH_BASE) . '/restoration.php',
		);

		parent::__construct();
	}
}