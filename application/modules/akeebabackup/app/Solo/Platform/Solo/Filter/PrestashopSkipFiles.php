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
 * PrestaShop Filter: Skip Directories
 *
 * Exclude files of special directories
 */
class PrestashopSkipFiles extends FilterBase
{
	function __construct()
	{
		$this->object	= 'dir';
		$this->subtype	= 'content';
		$this->method	= 'direct';
		$this->filter_name = 'PrestashopSkipFiles';

		$configuration = Factory::getConfiguration();

		if ($configuration->get('akeeba.platform.scripttype', 'generic') !== 'prestashop')
		{
			$this->enabled = false;
			return;
		}

		$root = $configuration->get('akeeba.platform.newroot', '[SITEROOT]');

        $this->filter_data[$root] = array (
            // Output & temp directory of the application
            $this->treatDirectory($configuration->get('akeeba.basic.output_directory')),
            // Upload directory
            'upload',
            // cache directories
            'cache',
            // The logs directory
            'log'
        );
	}
}