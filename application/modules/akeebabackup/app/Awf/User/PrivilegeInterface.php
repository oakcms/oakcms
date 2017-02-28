<?php
/**
 * @package     Awf
 * @copyright   2014-2017 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license     GNU GPL version 3 or later
 */

namespace Awf\User;

/**
 * The interface to the user privilege management classes
 *
 * @codeCoverageIgnore
 */
interface PrivilegeInterface
{
	/**
	 * Sets the name of this privilege management object
	 *
	 * @param   string  $name  The name of the privilege object
	 *
	 * @return  void
	 */
	public function setName($name);

	/**
	 * Binds a user object to this privilege management object
	 *
	 * @param   UserInterface  $user  The user object to bind
	 *
	 * @return  mixed
	 */
	public function setUser(UserInterface &$user);

	/**
	 * Get an array of supported privilege names by this class
	 *
	 * @return  mixed
	 */
	public function getPrivilegeNames();

	/**
	 * Get the value of a privilege: true if access is granted, false if it is denied
	 *
	 * @param   string   $privilege  The privilege to check
	 * @param   mixed    $default    The default privilege value if it's unspecified
	 *
	 * @return  boolean  true if access is granted, false if it is denied
	 */
	public function getPrivilege($privilege, $default = false);

	/**
	 * Set the value of a privilege. Not all implementations may support this.
	 *
	 * @param   string  $privilege  The name of the privilege to set
	 * @param   mixed   $value      The privilege's value: true to give it, false to deny it
	 *
	 * @return  boolean  False if it is not supported
	 */
	public function setPrivilege($privilege, $value);

	/**
	 * It's called before the user record we are attached to is saved
	 *
	 * @return  void
	 */
	public function onBeforeSave();

	/**
	 * It's called after the user record we are attached to is saved
	 *
	 * @return  void
	 */
	public function onAfterSave();

	/**
	 * It's called before the user record we are attached to is loaded
	 *
	 * @param   object  $data  The raw data we are going to bind to the user object
	 *
	 * @return  void
	 */
	public function onBeforeLoad(&$data);

	/**
	 * It's called after the user record we are attached to is loaded
	 *
	 * @return  void
	 */
	public function onAfterLoad();
} 