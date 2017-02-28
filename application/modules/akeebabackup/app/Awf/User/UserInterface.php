<?php
/**
 * @package     Awf
 * @copyright   2014-2017 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license     GNU GPL version 3 or later
 */

namespace Awf\User;
use Awf\Registry\Registry;

/**
 * The interface to the user class
 *
 * @codeCoverageIgnore
 */
interface UserInterface
{
	/**
	 * Binds the data to the object
	 *
	 * @param   array|object  $data  The data to bind
	 *
	 * @return  void
	 */
	public function bind(&$data);

	/**
	 * Returns the ID of the user
	 *
	 * @return  integer
	 */
	public function getId();

	/**
	 * Return the username of this user
	 *
	 * @return  string
	 */
	public function getUsername();

	/**
	 * Set the username of this user
	 *
	 * @param   string  $username  The username to set
	 *
	 * @return  void
	 */
	public function setUsername($username);

	/**
	 * Get the full name of this user
	 *
	 * @return  string
	 */
	public function getName();

	/**
	 * Set the name of this user
	 *
	 * @param   string  $name  The full name to set
	 *
	 * @return  void
	 */
	public function setName($name);

	/**
	 * Returns the email of the user
	 *
	 * @return  string
	 */
	public function getEmail();

	/**
	 * Sets the email of the user
	 *
	 * @param   string  $email  The email to set
	 *
	 * @return  void
	 */
	public function setEmail($email);

	/**
	 * Returns the hashed password of the user.
	 *
	 * @return  string
	 */
	public function getPassword();

	/**
	 * Sets the password of the user. The password will be automatically hashed before being stored.
	 *
	 * @param   string  $password  The (unhashed) password
	 *
	 * @return  void
	 */
	public function setPassword($password);

	/**
	 * Checks if the provided (unhashed) password matches the password of the user record
	 *
	 * @param   string  $password  The password to check
	 * @param   array   $options   [optional] Any additional information, e.g. two factor authentication
	 *
	 * @return  boolean
	 */
	public function verifyPassword($password, $options = array());

	/**
	 * Gets the user's parameters. The parameters are stored in JSON format in the user record automatically. If you
	 * need to write to them you can use the returned Registry object instance.
	 *
	 * @return  Registry
	 */
	public function &getParameters();

	/**
	 * Attach a privilege management object to the user object
	 *
	 * @param   string              $name             How this privilege will be known to the user class
	 * @param   PrivilegeInterface  $privilegeObject  The privilege management object to attach
	 *
	 * @return  void
	 */
	public function attachPrivilegePlugin($name, PrivilegeInterface $privilegeObject);

	/**
	 * Detach a privilege management object from the user object
	 *
	 * @param   string  $name  The name of the privilege to detach
	 *
	 * @return  void
	 */
	public function detachPrivilegePlugin($name);

	/**
	 * Attach a user authentication object to the user object
	 *
	 * @param   string                   $name                  How this authentication object will be known to the user class
	 * @param   AuthenticationInterface  $authenticationObject  The user authentication object to attach
	 *
	 * @return  void
	 */
	public function attachAuthenticationPlugin($name, AuthenticationInterface $authenticationObject);

	/**
	 * Detach a user authentication object from the user object
	 *
	 * @param   string  $name  The name of the user authentication object to detach
	 *
	 * @return  void
	 */
	public function detachAuthenticationPlugin($name);

	/**
	 * Get the value of a privilege. Privileges are two string separated by a dot, e.g. foo.bar which tells the user
	 * object to look for a privilege management object call "foo" and ask it to return the value of privilege "bar".
	 * If the object is not found or has no record of the privilege requested it will return the $default value.
	 *
	 * @param   string  $privilege  The privilege to check, e.g. foo.bar
	 * @param   mixed   $default    The default privilege value (true = give access, false = forbid access)
	 *
	 * @return  mixed  True if access is granted, false if access is not granted, null if undefined (avoid using null)
	 */
	public function getPrivilege($privilege, $default = false);

	/**
	 * Set the value of a privilege. Privileges are two string separated by a dot, e.g. foo.bar which tells the user
	 * object to look for a privilege management object call "foo" and ask it to return the value of privilege "bar".
	 * Not all privilege objects are supposed to implement the setPrivilege functionality. Then they return false.
	 *
	 * @param   string  $privilege  The privilege to check, e.g. foo.bar
	 * @param   mixed   $value      The privilege value (true = give access, false = forbid access)
	 *
	 * @return  boolean  False if setting the privilege is not supported
	 */
	public function setPrivilege($privilege, $value);

	/**
	 * Trigger a privilege plugin event
	 *
	 * @param   string  $event
	 *
	 * @return  void
	 */
	public function triggerEvent($event);

	/**
	 * Trigger an authentication plugin event
	 *
	 * @param   string  $event   The event to run
	 * @param   array   $params  The parameters used for this event
	 *
	 * @return  boolean  True if all authentication objects report success
	 */
	public function triggerAuthenticationEvent($event, $params = array());
}