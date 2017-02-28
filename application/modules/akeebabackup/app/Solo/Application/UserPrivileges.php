<?php
/**
 * @package     Solo
 * @copyright   2014-2017 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license     GNU GPL version 3 or later
 */

namespace Solo\Application;

use Awf\User\Privilege;

class UserPrivileges extends Privilege
{
	public function __construct()
	{
		$this->name = 'akeeba';
		// Set up the privilege names and their default values
		$this->privileges = array(
			'backup'	=> false,
			'configure'	=> false,
			'download'	=> false,
		);
	}
} 