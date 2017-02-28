<?php
/**
 * Akeeba Engine
 * The modular PHP5 site backup engine
 *
 * @copyright Copyright (c)2009-2016 Nicholas K. Dionysopoulos
 * @license   GNU GPL version 3 or, at your option, any later version
 * @package   akeebaengine
 *
 */

namespace Akeeba\Engine\Filter;

// Protection against direct access
defined('AKEEBAENGINE') or die();

use Akeeba\Engine\Factory;

/**
 * Folder exclusion filter based on regular expressions
 */
class Cvsfolders extends Base
{
	function __construct()
	{
		$this->object      = 'dir';
		$this->subtype     = 'all';
		$this->method      = 'regex';
		$this->filter_name = 'Cvsfolders';

		if (empty($this->filter_name))
		{
			$this->filter_name = strtolower(basename(__FILE__, '.php'));
		}

		parent::__construct();

		// Get the site's root
		$configuration = Factory::getConfiguration();

        $root = $configuration->get('akeeba.platform.newroot', '[SITEROOT]');

		$this->filter_data[$root] = array(
			'#/\.git$#',
			'#^\.git$#',
			'#/\.svn$#',
			'#^\.svn$#'
		);
	}
}