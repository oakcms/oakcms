<?php
/**
 * @package     Awf
 * @copyright   2014-2017 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license     GNU GPL version 3 or later
 */

namespace Awf\User;

/**
 * The interface to the user management class
 *
 * @codeCoverageIgnore
 */
interface ManagerInterface
{
	/**
	 * Get user by numeric ID
	 *
	 * @param   integer  $id  The numeric ID of the user to load
	 *
	 * @return  UserInterface|null  A user object if it exists, null if it doesn't
	 */
	public function getUser($id = null);

	/**
	 * Get user by username
	 *
	 * @param   string  $username  The username of the user to load
	 *
	 * @return  UserInterface|null  A user object if it exists, null if it doesn't
	 */
	public function getUserByUsername($username);

	/**
	 * Try to log in a user given the username, password and any additional parameters which may be required by the
	 * user class
	 *
	 * @param   string  $username  The username of the user to log in
	 * @param   string  $password  The (unhashed) password of the user to log in
	 * @param   array   $params    [optional] Any additional parameters you may want to pass to the user object, e.g. 2FA
	 *
	 * @return  boolean  True on success
	 *
	 * @throws  \AuthenticationException  When the login fails
	 */
	public function loginUser($username, $password, $params = array());

	/**
	 * Log out the current user
	 *
	 * @return  void
	 */
	public function logoutUser();

	/**
	 * Save the provided user record
	 *
	 * @param   UserInterface  $user  The user to save
	 *
	 * @return  boolean  True on success
	 *
	 * @throws  \RuntimeException  If an error occurs when saving the user
	 */
	public function saveUser(UserInterface $user);

	/**
	 * Delete the user given their ID
	 *
	 * @param   integer  $id  The numeric ID of the user record to delete
	 *
	 * @return  boolean  True on success
	 *
	 * @throws  \RuntimeException  If an error occurs when saving the user
	 */
	public function deleteUser($id);

	/**
	 * Register a privilege plugin class with this user manager
	 *
	 * @param   string  $name       The name of the privilege management object
	 * @param   string  $privilege  The privilege management class name we will be attaching to user objects
	 *
	 * @return  void
	 */
	public function registerPrivilegePlugin($name, $privilege);

	/**
	 * Unregister a privilege plugin class from this user manager
	 *
	 * @param   string  $name       The name of the privilege management object to unregister
	 *
	 * @return  mixed
	 */
	public function unregisterPrivilegePlugin($name);

	/**
	 * Register a user authentication class with this user manager
	 *
	 * @param   string  $name            The name of the user authentication object
	 * @param   string  $authentication  The user authentication class name we will be attaching to user objects
	 *
	 * @return  void
	 */
	public function registerAuthenticationPlugin($name, $authentication);

	/**
	 * Unregister a user authentication class from this user manager
	 *
	 * @param   string  $name       The name of the user authentication object to unregister
	 *
	 * @return  mixed
	 */
	public function unregisterAuthenticationPlugin($name);
}