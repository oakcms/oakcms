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
 * Automatically exclude the OS-specific thumbnail cache files added in directories by Windows and Mac OS X
 */
class OSThumbnailCacheFiles extends FilterBase
{
	function __construct()
	{
		$this->object = 'file';
		$this->subtype = 'all';
		$this->method = 'regex';
		$this->filter_name = 'OSThumbnailCacheFiles';

		if (empty($this->filter_name))
		{
			$this->filter_name = strtolower(basename(__FILE__, '.php'));
		}

		if (Factory::getKettenrad()->getTag() == 'restorepoint')
		{
			$this->enabled = false;
		}

		parent::__construct();

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

		$this->filter_data[$root] = array(
			'#/Thumbs.db$#',
			'#^Thumbs.db$#',
			'#/.DS_Store$#i',
			'#^.DS_Store$#i'
		);
	}
}