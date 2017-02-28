<?php
/**
 * @package     Awf
 * @copyright   2014-2017 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license     GNU GPL version 3 or later
 */

namespace Awf\User;

/**
 * An abstract privilege class, which you can extend to easily provide your custom privilege management class
 */
abstract class Privilege implements PrivilegeInterface
{
	/**
	 * The name of these privileges, as known by the user object we are attached to
	 *
	 * @var  string
	 */
	protected $name = '';

	/**
	 * The user object we are attached to
	 *
	 * @var  UserInterface
	 */
	protected $user = null;

	/**
	 * A hash array. The key is the privilege name, the value is the privilege setting (true to grant access, false
	 * otherwise)
	 *
	 * @var  array
	 */
	protected $privileges = array();

	/**
	 * Sets the name of this privilege management object
	 *
	 * @param   string  $name  The name of the privilege object
	 *
	 * @return  void
	 */
	public function setName($name)
	{
		$this->name = $name;
	}

	/**
	 * Binds a user object to this privilege management object
	 *
	 * @param   UserInterface  $user  The user object to bind
	 *
	 * @return  mixed
	 */
	public function setUser(UserInterface &$user)
	{
		$this->user = $user;
	}

	/**
	 * Get an array of supported privilege names by this class
	 *
	 * @return  mixed
	 */
	public function getPrivilegeNames()
	{
		return array_keys($this->privileges);
	}

	/**
	 * Get the value of a privilege: true if access is granted, false if it is denied
	 *
	 * @param   string   $privilege  The privilege to check
	 * @param   mixed    $default    The default privilege value if it's unspecified
	 *
	 * @return  boolean  true if access is granted, false if it is denied
	 */
	public function getPrivilege($privilege, $default = false)
	{
		if (!array_key_exists($privilege, $this->privileges))
		{
			$this->privileges[$privilege] = $default;
		}

		return $this->privileges[$privilege];
	}

	/**
	 * Set the value of a privilege. Not all implementations may support this.
	 *
	 * @param   string  $privilege  The name of the privilege to set
	 * @param   mixed   $value      The privilege's value: true to give it, false to deny it
	 *
	 * @return  boolean  False if it is not supported
	 */
	public function setPrivilege($privilege, $value)
	{
		$this->privileges[$privilege] = $value;
	}

	/**
	 * It's called before the user record we are attached to is saved. We add our privileges under the acl key of
	 * the user object's parameters
	 *
	 * @return  void
	 */
	public function onBeforeSave()
	{
		if (!empty($this->privileges))
		{
			foreach ($this->privileges as $key => $value)
			{
				$this->user->getParameters()->set('acl.' . $this->name . '.' . $key, $value);
			}
		}
	}

	/**
	 * It's called after the user record we are attached to is saved. NOT USED.
	 *
	 * @return  void
	 */
	public function onAfterSave()
	{
		// Nothing to do
	}

	/**
	 * It's called before the user record we are attached to is loaded. NOT USED.
	 *
	 * @param   object  $data  The raw data we are going to bind to the user object
	 *
	 * @return  void
	 */
	public function onBeforeLoad(&$data)
	{
		// Nothing to do
	}

	/**
	 * It's called after the user record we are attached to is loaded. We read the privileges from the acl key of the
	 * user object's parameters
	 *
	 * @return  void
	 */
	public function onAfterLoad()
	{
		if (!empty($this->privileges))
		{
			foreach ($this->privileges as $key => $value)
			{
				$this->privileges[$key] = $this->user->getParameters()->get('acl.' . $this->name . '.' . $key, $value);
			}
		}
	}
} 