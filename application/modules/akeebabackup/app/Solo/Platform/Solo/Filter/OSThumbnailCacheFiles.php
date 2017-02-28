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
 * Files exclusion filter based on regular expressions
 */
class OSThumbnailCacheFiles extends FilterBase
{
	function __construct()
	{
		$this->object	= 'file';
		$this->subtype	= 'all';
		$this->method	= 'regex';
		$this->filter_name = 'OSThumbnailCacheFiles';

		if(empty($this->filter_name)) $this->filter_name = strtolower(basename(__FILE__,'.php'));

		if(Factory::getKettenrad()->getTag() == 'restorepoint') $this->enabled = false;

		parent::__construct();

		// Get the site's root
		$configuration = Factory::getConfiguration();

		$root = $configuration->get('akeeba.platform.newroot', '[SITEROOT]');

		$this->filter_data[$root] = array(
			'#/Thumbs.db$#',
			'#^Thumbs.db$#',
			'#/.DS_Store$#i',
			'#^.DS_Store$#i'
		);
	}
}