<?php
/**
 * @package     Awf
 * @copyright   2014-2017 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license     GNU GPL version 3 or later
 */

namespace Awf\User;
use Awf\Registry\Registry;

require_once __DIR__ . '/password.php';

class User implements UserInterface
{
	/** @var  integer  The numeric ID of the user */
	protected $id = null;

	/** @var  string  The username */
	protected $username = '';

	/** @var  string  The full (real) name of the user */
	protected $name = '';

	/** @var  string  The email address of the user */
	protected $email = '';

	/** @var  string  The hashed password of the user */
	protected $password = '';

	/** @var  Registry  The parameters registry */
	protected $parameters = null;

	/** @var  array[PrivilegeInterface]  A hash array of the privilege plugin objects */
	protected $privileges = array();

	/** @var  array[AuthenticationInterface]  A hash array of the privilege plugin objects */
	protected $authentications = array();

	/**
	 * Binds the data to the object
	 *
	 * @param   array|object  $data  The data to bind
	 *
	 * @return  void
	 */
	public function bind(&$data)
	{
		// Reset the parameters storage
		$this->parameters = null;

		// Cast the data to an object, if necessary
		if (!is_object($data))
		{
			$data = (object)$data;
		}

		// Run the privileges' onBeforeLoad events
		if (!empty($this->privileges))
		{
			/** @var  PrivilegeInterface  $privilegeObject */
			foreach ($this->privileges as $name => $privilegeObject)
			{
				$privilegeObject->onBeforeLoad($data);
			}
		}

		// Switch the data back to an array
		$data = (array)$data;

		foreach ($data as $k => $v)
		{
			if (in_array($k, array('privileges')))
			{
				continue;
			}

			if ($k == 'parameters')
			{
				$this->parameters = new Registry($v);
			}
			elseif (property_exists($this, $k))
			{
				$this->$k = $v;
			}
		}

		// Make sure we always have a parameters storage
		if (empty($this->parameters))
		{
			$this->parameters = new Registry();
		}

		// Run the privileges' onAfterLoad events
		if (!empty($this->privileges))
		{
			/** @var  PrivilegeInterface  $privilegeObject */
			foreach ($this->privileges as $name => $privilegeObject)
			{
				$privilegeObject->onAfterLoad();
			}
		}
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
	 * Sets the password of the user. The password will be automatically hashed before being stored.
	 *
	 * For more information: http://blog.ircmaxell.com/2012/12/seven-ways-to-screw-up-bcrypt.html
	 *
	 * @param   string  $password  The (unhashed) password
	 *
	 * @return  void
	 *
	 * @throws  \RuntimeException  if no password hashing method is available (throw your server away!)
	 */
	public function setPassword($password)
	{
		// Check if the current PHP version supports the $2y$ prefix
		$pass = false;
		$hash = '$2y$04$usesomesillystringfore7hnbRJHxXVLeakoG8K30oukPsA.ztMG';
		if (function_exists('crypt'))
		{
			$test = crypt("password", $hash);
			$pass = $test == $hash;
		}

		if ($pass)
		{
			// On PHP 5.3.7 or later *OR* on older PHP 5.3.3 releases with the $2y$ fix backported into them we can use
			// the very secure BCrypt with a relatively high cost parameter (10)
			$this->password = password_hash($password, PASSWORD_BCRYPT, array("cost" => 10));
		}
		else
		{
			// On lower PHP versions we have to use the easier to crack salted MD5/SHA1/SHA256/SHA512 :(
			$salt = base64_encode($this->getSalt(64));

			$hash_algos = function_exists('hash_algos') ? hash_algos() : array();

			// Prefer SHA-512...
			if (in_array('sha512', $hash_algos))
			{
				$this->password = 'SHA512:' . hash('sha512', $password . $salt, false);
			}
			// ...then SHA-256...
			elseif (in_array('sha256', $hash_algos))
			{
				$this->password = 'SHA256:' . hash('sha256', $password . $salt, false);
			}
			// ...then SHA-1...
			elseif (function_exists('sha1'))
			{
				$this->password = 'SHA1:' . sha1($password . $salt);
			}
			// ...then MD5...
			elseif (function_exists('md5'))
			{
				$this->password = 'MD5:' . md5($password . $salt);
			}
			// ...and if all else fails throw an exception: your server is trash!
			else
			{
				throw new \RuntimeException('Your server does not support password hashing. Move to a decent host immediately!', 500);
			}

			$this->password .= ':' . $salt;
		}
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
	 * Checks if the provided (unhashed) password matches the password of the user record. It also triggers all
	 * authentication plugins which can implement alternative or two-factor authentication methods.
	 *
	 * @param   string  $password  The password to check
	 * @param   array   $options   [optional] Any additional information, e.g. two factor authentication
	 *
	 * @return  boolean
	 */
	public function verifyPassword($password, $options = array())
	{
		$params = array_merge($options, array('password' => $password));

		return $this->triggerAuthenticationEvent('onAuthentication', $params);
	}

	/**
	 * Gets the user's parameters. The parameters are stored in JSON format in the user record automatically. If you
	 * need to write to them you can use the returned Registry object instance.
	 *
	 * @return  Registry
	 */
	public function &getParameters()
	{
		if (empty($this->parameters) || !is_object($this->parameters) || !($this->parameters instanceof Registry))
		{
			$this->parameters = new Registry();
		}

		return $this->parameters;
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
		$this->privileges[$name] = $privilegeObject;
		$privilegeObject->setName($name);
		$privilegeObject->setUser($this);
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
		if (array_key_exists($name, $this->privileges))
		{
			unset ($this->privileges[$name]);
		}
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
		// If there is no dot in the privilege name it's an invalid privilege, so return the default value
		if (strpos($privilege, '.') === false)
		{
			return $default;
		}

		list ($name, $subPrivilege) = explode('.', $privilege);

		// Did they ask for an unknown privilege?
		if (!array_key_exists($name, $this->privileges))
		{
			return $default;
		}

		/** @var PrivilegeInterface $privilegeObject */
		$privilegeObject = $this->privileges[$name];

		return $privilegeObject->getPrivilege($subPrivilege, $default);
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
		// If there is no dot in the privilege name it's an invalid privilege
		if (strpos($privilege, '.') === false)
		{
			return false;
		}

		list ($name, $subPrivilege) = explode('.', $privilege);

		// Did they ask for an unknown privilege?
		if (!array_key_exists($name, $this->privileges))
		{
			return false;
		}

		/** @var PrivilegeInterface $privilegeObject */
		$privilegeObject = $this->privileges[$name];
		return $privilegeObject->setPrivilege($subPrivilege, $value);
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
		// Run the privileges' onAfterLoad events
		if (!empty($this->privileges))
		{
			/** @var  PrivilegeInterface  $privilegeObject */
			foreach ($this->privileges as $name => $privilegeObject)
			{
				if (method_exists($privilegeObject, $event))
				{
					$privilegeObject->$event();
				}
			}
		}
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
		$result = false;

		// Run the authentication event
		if (!empty($this->authentications))
		{
			$result = true;

			/** @var  AuthenticationInterface  $authenticationObject */
			foreach ($this->authentications as $name => $authenticationObject)
			{
				if (method_exists($authenticationObject, $event))
				{
					$result = $result && $authenticationObject->$event($params);
				}

				// Break as soon as the first authentication object returns false
				if (!$result)
				{
					break;
				}
			}
		}

		return $result;
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
		$this->authentications[$name] = $authenticationObject;
		$authenticationObject->setName($name);
		$authenticationObject->setUser($this);
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
		if (array_key_exists($name, $this->authentications))
		{
			unset ($this->authentications[$name]);
		}
	}

	/**
	 * Gets a random salt for the RELATIVELY INSECURE salted SHA-512/SHA-256/SHA-1/MD5 password hashing. We will use
	 * mcrypt_create_iv, openssl_random_pseudo_bytes or mt_rand in this order, depending on what is available on the
	 * system.
	 *
	 * For more information: http://blog.ircmaxell.com/2012/12/seven-ways-to-screw-up-bcrypt.html
	 *
	 * @param   integer  $length  How long the random salt will be
	 *
	 * @return  string
	 */
	protected function getSalt($length = 16)
	{
		if (function_exists('mcrypt_create_iv'))
		{
			$salt = mcrypt_create_iv($length, MCRYPT_DEV_URANDOM);
		}
		elseif (function_exists('openssl_random_pseudo_bytes'))
		{
			$crypto_strong = null;
			$salt = openssl_random_pseudo_bytes($length, $crypto_strong);
		}
		else
		{
			$chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ01234567890!@#$%^&*()_+-={}[]|;/?.>,<`~';
			$maxLength = strlen($chars) - 1;
			$salt = '';

			for ($i = 0; $i < $length; $i++)
			{
				$salt .= substr($chars, mt_rand(0, $maxLength), 1);
			}
		}

		return $salt;
	}
} 