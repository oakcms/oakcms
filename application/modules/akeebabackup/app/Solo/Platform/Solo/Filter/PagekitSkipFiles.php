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

// Protection against direct access
defined('AKEEBAENGINE') or die();

/**
 * Pagekit-specific Filter: Skip Directories
 *
 * Exclude files of special directories
 */
class PagekitSkipFiles extends FilterBase
{
	public function __construct()
	{
		$this->object	= 'dir';
		$this->subtype	= 'content';
		$this->method	= 'direct';
		$this->filter_name = 'PagekitSkipFiles';

		$configuration = Factory::getConfiguration();

		if ($configuration->get('akeeba.platform.scripttype', 'generic') !== 'pagekit')
		{
			$this->enabled = false;

			return;
		}

		$root = $configuration->get('akeeba.platform.newroot', '[SITEROOT]');

		$this->filter_data[$root] = array (
			// default temp directory
			'tmp',
		);

		parent::__construct();
	}
}