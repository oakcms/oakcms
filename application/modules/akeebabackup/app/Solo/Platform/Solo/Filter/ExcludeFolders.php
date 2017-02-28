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
 * Folder exclusion filter. Excludes certain hosting directories.
 */
class ExcludeFolders extends FilterBase
{
	public function __construct()
	{
		$this->object = 'dir';
		$this->subtype = 'all';
		$this->method = 'direct';
		$this->filter_name = 'ExcludeFolders';

		if (Factory::getKettenrad()->getTag() == 'restorepoint')
		{
			$this->enabled = false;
		}

		// Get the site's root
		$configuration = Factory::getConfiguration();

		$root = $configuration->get('akeeba.platform.newroot', '[SITEROOT]');

		// We take advantage of the filter class magic to inject our custom filters
		$this->filter_data[$root] = array(
			'awstats',
			'cgi-bin'
		);

		parent::__construct();
	}

}