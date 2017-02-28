<?php
/**
 * Akeeba Engine
 * The modular PHP5 site backup engine
 *
 * @copyright Copyright (c)2006-2017 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license   GNU GPL version 3 or, at your option, any later version
 * @package   akeebaengine
 *
 */

namespace Akeeba\Engine\Filter\Stack;

use Akeeba\Engine\Filter\Base;

// Protection against direct access
defined('AKEEBAENGINE') or die();

/**
 * Files exclusion filter based on regular expressions
 */
class StackErrorlogs extends Base
{
	function __construct()
	{
		$this->object = 'file';
		$this->subtype = 'all';
		$this->method = 'api';

		if (empty($this->filter_name))
		{
			$this->filter_name = strtolower(basename(__FILE__, '.php'));
		}

		parent::__construct();
	}

	protected function is_excluded_by_api($test, $root)
	{
		// Is it an error log? Exclude the file.
		if (in_array(basename($test), array(
			'php_error',
			'php_errorlog',
			'error_log',
			'error.log'
		))) {
			return true;
		}

		// No match? Just include the file!
		return false;
	}

}