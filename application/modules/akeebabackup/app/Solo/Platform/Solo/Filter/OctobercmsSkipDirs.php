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
 * Exclude subdirectories of special directories
 */
class OctobercmsSkipDirs extends FilterBase
{
	public function __construct()
	{
		$this->object	= 'dir';
		$this->subtype	= 'children';
		$this->method	= 'direct';
		$this->filter_name = 'OctobercmsSkipDirs';

		$configuration = Factory::getConfiguration();

		if ($configuration->get('akeeba.platform.scripttype', 'generic') !== 'octobercms')
		{
			$this->enabled = false;

			return;
		}

		$root = $configuration->get('akeeba.platform.newroot', '[SITEROOT]');

		$this->filter_data[$root] = array (
			'storage/temp',
			'storage/logs',
			'storage/cms/cache',
			'storage/cms/combiner',
			'storage/cms/twig',
			'storage/framework/cache',
			'storage/framework/sessions',
		);

		parent::__construct();
	}
}