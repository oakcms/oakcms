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
 * Directory exclusion filter
 */
class ExcludeDirectories extends FilterBase
{
	function __construct()
	{
		$this->object = 'dir';
		$this->subtype = 'all';
		$this->method = 'direct';
		$this->filter_name = 'ExcludeDirectories';

		if (Factory::getKettenrad()->getTag() == 'restorepoint')
		{
			$this->enabled = false;
		}

		// We take advantage of the filter class magic to inject our custom filters
		$configuration = Factory::getConfiguration();

		// Get the site's root
		$root = $configuration->get('akeeba.platform.newroot', '[SITEROOT]');

		$this->filter_data[$root] = array(
			// This folder would collide with the installer
			'installation',
		);

		parent::__construct();
	}
}