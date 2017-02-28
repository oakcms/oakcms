<?php
/**
 * @package		awf
 * @copyright	2014-2017 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license		GNU GPL version 3 or later
 */

namespace Awf\Platform\Joomla\User;

use Awf\Platform\Joomla\Application\Application;
use Awf\Registry\Registry;
use Awf\User\AuthenticationInterface;
use Awf\User\PrivilegeInterface;
use Awf\User\UserInterface;

class User extends \JUser implements UserInterface
{
	public function bind(&$array)
	{
		return parent::bind($array);
	}

	/**
	 * Returns the ID of the user
	 *
	 * @return  integer
	 */
	public function getId()
	{
		return $this->id;
	}

	/**
	 * Return the username of this user
	 *
	 * @return  string
	 */
	public function getUsername()
	{
		return $this->username;
	}

	/**
	 * Set the username of this user
	 *
	 * @param   string  $username  The username to set
	 *
	 * @return  void
	 */
	public function setUsername($username)
	{
		$this->username = $username;
	}

	/**
	 * Get the full name of this user
	 *
	 * @return  string
	 */
	public function getName()
	{
		return $this->name;
	}

	/**
	 * Set the name of this user
	 *
	 * @param   string  $name  The full name to set
	 *
	 * @return  void
	 */
	public function setName($name)
	{
		$this->name = $name;
	}

	/**
	 * Returns the email of the user
	 *
	 * @return  string
	 */
	public function getEmail()
	{
		return $this->email;
	}

	/**
	 * Sets the email of the user
	 *
	 * @param   string  $email  The email to set
	 *
	 * @return  void
	 */
	public function setEmail($email)
	{
		$this->email = $email;
	}

	/**
	 * Returns the hashed password of the user.
	 *
	 * @return  string
	 */
	public function getPassword()
	{
		return $this->password;
	}

	/**
	 * Sets the password of the user. The password will be automatically hashed before being stored.
	 *
	 * @param   string  $password  The (unhashed) password
	 *
	 * @return  void
	 */
	public function setPassword($password)
	{
		$data = array(
			'password'		=> $password,
			'password2'		=> $password,
		);
		$this->bind($data);
	}

	/**
	 * Checks if the provided (unhashed) password matches the password of the user record
	 *
	 * @param   string  $password  The password to check
	 * @param   array   $options   [optional] Any additional information, e.g. two factor authentication
	 *
	 * @return  boolean
	 *
	 * @throws \Exception ALWAYS!
	 */
	public function verifyPassword($password, $options = array())
	{
		throw new \Exception('verifyPassword is not availabe in Joomla!');
	}

	/**
	 * Gets the user's parameters. The parameters are stored in JSON format in the user record automatically. If you
	 * need to write to them you can use the returned Registry object instance.
	 *
	 * @return  Registry
	 */
	public function &getParameters()
	{
        if (is_string($this->params))
        {
            $this->params = new Registry($this->params);
        }

		return $this->params;
	}

	/**
	 * Attach a privilege management object to the user object
	 *
	 * @param   string              $name             How this privilege will be known to the user class
	 * @param   PrivilegeInterface  $privilegeObject  The privilege management object to attach
	 *
	 * @return  void
	 */
	public function attachPrivilegePlugin($name, PrivilegeInterface $privilegeObject)
	{
		throw new \RuntimeException('attachPrivilegePlugin is not availabe under Joomla!');
	}

	/**
	 * Detach a privilege management object from the user object
	 *
	 * @param   string  $name  The name of the privilege to detach
	 *
	 * @return  void
	 */
	public function detachPrivilegePlugin($name)
	{
		throw new \RuntimeException('detachPrivilegePlugin is not availabe under Joomla!');
	}

	/**
	 * Attach a user authentication object to the user object
	 *
	 * @param   string                   $name                  How this authentication object will be known to the user class
	 * @param   AuthenticationInterface  $authenticationObject  The user authentication object to attach
	 *
	 * @return  void
	 */
	public function attachAuthenticationPlugin($name, AuthenticationInterface $authenticationObject)
	{
		throw new \RuntimeException('attachAuthenticationPlugin is not availabe under Joomla!');
	}

	/**
	 * Detach a user authentication object from the user object
	 *
	 * @param   string  $name  The name of the user authentication object to detach
	 *
	 * @return  void
	 */
	public function detachAuthenticationPlugin($name)
	{
		throw new \RuntimeException('detachAuthenticationPlugin is not availabe under Joomla!');
	}

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
	public function getPrivilege($privilege, $default = false)
	{
		$option = 'com_' . Application::getInstance()->getContainer()->extension_name;

		return $this->authorise($privilege, $option);
	}

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
	public function setPrivilege($privilege, $value)
	{
		throw new \RuntimeException('setPrivilege is not availabe under Joomla!');
	}

	/**
	 * Trigger a privilege plugin event
	 *
	 * @param   string  $event
	 *
	 * @return  void
	 */
	public function triggerEvent($event)
	{
		throw new \RuntimeException('triggerEvent is not availabe under Joomla!');
	}

	/**
	 * Trigger an authentication plugin event
	 *
	 * @param   string  $event   The event to run
	 * @param   array   $params  The parameters used for this event
	 *
	 * @return  boolean  True if all authentication objects report success
	 */
	public function triggerAuthenticationEvent($event, $params = array())
	{
		throw new \RuntimeException('triggerAuthenticationEvent is not availabe under Joomla!');
	}
}