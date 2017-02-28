<?php
/**
 * @package		akeebabackupwp
 * @copyright	2014-2017 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license		GNU GPL version 3 or later
 */

namespace Solo\Application;

use Awf\User\Privilege;

class WordpressUserPrivileges extends Privilege
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

	/**
	 * It's called before the user record we are attached to is loaded.
	 *
	 * @param   object  $data  The raw data we are going to bind to the user object
	 *
	 * @return  void
	 */
	public function onBeforeLoad(&$data)
	{
		// CLI mode or access outside WP itself
		if (!defined('WPINC'))
		{
			return;
		}

		$myData = (array)$data;

		$isMultisite = is_multisite();

		$isSuperAdmin = is_super_admin();
		$isEditor = isset($myData['wpAllCaps']) && in_array('edit_others_posts', $myData['wpAllCaps']) && $myData['wpAllCaps']['edit_others_posts'];
		$isAdmin = isset($myData['wpAllCaps']) && isset($myData['wpAllCaps']['activate_plugins']) && ($myData['wpAllCaps']['activate_plugins']);

		// Single site defaults:
		// -- Backup privilege: editor and above
		$this->privileges['backup'] = $isEditor;
		// -- Download privilege: administrators and above
		$this->privileges['download'] = $isAdmin;
		// -- Configure privilege: administrator and above
		$this->privileges['configure'] = $isAdmin;

		// Multisite defaults
		if ($isMultisite)
		{
			// No privileges. Only Super Admins, with access to the Blog Network Dashboard, should be allowed to deal
			// with backups of the blog network.
			$this->privileges['backup'] = false;
			$this->privileges['download'] = false;
			$this->privileges['configure'] = false;
		}

		// Super admin has access to everything
		if ($isSuperAdmin)
		{
			$this->privileges['backup'] = true;
			$this->privileges['download'] = true;
			$this->privileges['configure'] = true;
		}
	}

	/**
	 * It's called after the user record we are attached to is loaded. We override it with a blank method to prevent
	 * the default privilege setup method from executing.
	 *
	 * @return  void
	 */
	public function onAfterLoad()
	{
		// Do nothing. DO NOT REMOVE THIS METHOD!!!
	}
}