<?php
/**
 * Akeeba Engine
 * The modular PHP5 site backup engine
 * @copyright Copyright (c)2006-2017 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license   GNU GPL version 3 or, at your option, any later version
 * @package   akeebaengine
 *
 */

namespace Akeeba\Engine\Postproc;

// Protection against direct access
defined('AKEEBAENGINE') or die();

class None extends Base
{

	public function __construct()
	{
		// No point in breaking the step; we simply do nothing :)
		$this->break_after = false;
		$this->break_before = false;
		$this->allow_deletes = false;
	}

	public function processPart($absolute_filename, $upload_as = null)
	{
		// Really nothing to do!!
		return true;
	}
}