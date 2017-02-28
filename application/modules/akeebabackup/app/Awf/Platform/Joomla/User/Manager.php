<?php
/**
 * @package        awf
 * @copyright      2014-2017 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license        GNU GPL version 3 or later
 */

namespace Awf\Platform\Joomla\User;

use Awf\Application\Application;
use Awf\Container\Container;
use Awf\User\UserInterface;

/**
 * This Joomla!-specific User Manager class. It quietly interfaces the Joomla! API
 */
class Manager extends \Awf\User\Manager
{
	public function __construct(Container $container = null)
	{
		$this->user_class = '\\JUser';

		parent::__construct($container);
	}

	/**
	 * Get user by numeric ID. Skip the ID (or use null) to get the currently logged in user. Use the ID=0 to get a new,
	 * empty user instance.
	 *
	 * @param   integer $id The numeric ID of the user to load
	 *
	 * @return  User  A user object if it exists, null if it doesn't
	 */
	public function getUser($id = null)
	{
		if (is_null($id))
		{
			// We don't have a current user yet? Let's load it!
			if (!is_object($this->currentUser))
			{
				$this->currentUser = new User(\JFactory::getUser($id)->id);
			}

			return $this->currentUser;
		}

		return new User(\JFactory::getUser($id));
	}

	/**
	 * Get user by username
	 *
	 * @param   string  $username  The username of the user to load
	 *
	 * @return  \JUser|null  A user object if it exists, null if it doesn't
	 */
	public function getUserByUsername($username)
	{
		try
		{
			$id = \JUserHelper::getUserId($username);
		}
		catch (\Exception $e)
		{
			$id = null;
		}

		if (empty($id))
		{
			return null;
		}

		return $this->getUser($id);
	}

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
	 * @throws  \Exception  When the login fails
	 */
	public function loginUser($username, $password, $params = array())
	{
		$credentials = array(
			'username'	=> $username,
			'password'	=> $password,
		);

		$result = \JFactory::getApplication()->login($credentials, $params);

		if (is_object($result) && ($result instanceof \JError))
		{
			throw new \Exception($result->getError(), 403);
		}
	}

	/**
	 * Log out the current user. This also terminates the current session.
	 *
	 * @return  void
	 */
	public function logoutUser()
	{
		\JFactory::getApplication()->logout();
	}

	/**
	 * This method is not available. Use the Joomla! API instead.
	 *
	 * @param   UserInterface  $user  Ignored
	 *
	 * @return  boolean  Never true
	 *
	 * @throws  \RuntimeException  Always
	 */
	public function saveUser(UserInterface $user)
	{
		throw new \RuntimeException('Use the Joomla! API to save a user');
	}

	/**
	 * This method is not available. Use the Joomla! API instead.
	 *
	 * @param   integer  $id  Ignored
	 *
	 * @return  boolean  Never true
	 *
	 * @throws  \RuntimeException  Always
	 */
	public function deleteUser($id)
	{
		throw new \RuntimeException('Use the Joomla! API to delete a user');
	}

	/**
	 * This method is not available under Joomla!
	 *
	 * @param   string  $name       Ignored
	 * @param   string  $privilege  Ignored
	 *
	 * @return  void
	 *
	 * @throws  \RuntimeException  Always
	 */
	public function registerPrivilegePlugin($name, $privilege)
	{
		throw new \RuntimeException('registerPrivilegePlugin is not availabe under Joomla!');
	}

	/**
	 * This method is not available under Joomla!
	 *
	 * @param   string  $name       Ignored
	 *
	 * @return  mixed
	 *
	 * @throws  \RuntimeException  Always
	 */
	public function unregisterPrivilegePlugin($name)
	{
		throw new \RuntimeException('unregisterPrivilegePlugin is not availabe under Joomla!');
	}

	/**
	 * This method is not available under Joomla!
	 *
	 * @param   string  $name            Ignored
	 * @param   string  $authentication  Ignored
	 *
	 * @return  void
	 *
	 * @throws  \RuntimeException  Always
	 */
	public function registerAuthenticationPlugin($name, $authentication)
	{
		throw new \RuntimeException('registerAuthenticationPlugin is not availabe under Joomla!');
	}

	/**
	 * This method is not available under Joomla!
	 *
	 * @param   string  $name       Ignored
	 *
	 * @return  mixed
	 *
	 * @throws  \RuntimeException  Always
	 */
	public function unregisterAuthenticationPlugin($name)
	{
		throw new \RuntimeException('unregisterAuthenticationPlugin is not availabe under Joomla!');
	}
}