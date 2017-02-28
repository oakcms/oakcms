<?php
/**
 * @package     Awf
 * @copyright   2014-2017 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license     GNU GPL version 3 or later
 */

namespace Awf\User;

use Awf\Application\Application;
use Awf\Container\Container;
use Awf\Database\Driver;
use Awf\Text\Text;

/**
 * The User Manager class allows you to load, save, log in and log out users
 */
class Manager implements ManagerInterface
{
	/**
	 * An array of the instances we have already created
	 *
	 * @var  array[ManagerInterface]
	 */
	protected static $instances = array();

	/**
	 * The container this instance of User Manager is attached to
	 *
	 * @var  Container
	 */
	protected $container;

	/**
	 * The name of the table where user accounts are stored
	 *
	 * @var  string
	 */
	protected $user_table = '#__users';

	/**
	 * The name of the class we'll use to create new user objects
	 *
	 * @var  string
	 */
	protected $user_class = '\\Awf\\User\\User';

	/**
	 * The current user's object
	 *
	 * @var  UserInterface
	 */
	protected $currentUser = null;

	/**
	 * The list of privilege plugins to load on each user object
	 *
	 * @var  array
	 */
	protected $privileges = array();

	/**
	 * The list of authentication plugins to load on each user object
	 *
	 * @var  array
	 */
	protected $authentications = array();

	/**
	 * Public constructor. Creates a new User Manager. Do not call this directly. It's best to call getInstance()
	 * instead.
	 *
	 * @param   Container   $container
	 */
	public function __construct(Container $container = null)
	{
		if (!is_object($container))
		{
			$container = Application::getInstance()->getContainer();
		}

		$this->user_table = $container->appConfig->get('user_table', '#__users');
		$this->user_class = $container->appConfig->get('user_class', '\\Awf\\User\\User');

		$this->container = $container;
	}

	/**
	 * Get user by numeric ID. Skip the ID (or use null) to get the currently logged in user. Use the ID=0 to get a new,
	 * empty user instance.
	 *
	 * @param   integer  $id  The numeric ID of the user to load
	 *
	 * @return  UserInterface|null  A user object if it exists, null if it doesn't
	 */
	public function getUser($id = null)
	{
		// If we're not given an ID get the current user
		if (is_null($id))
		{
			// We don't have a current user yet? Let's load it!
			if (!is_object($this->currentUser))
			{
				// Get the ID from the session. If nobody is logged in we get 0 (create a new, not logged in user)
				$id = $this->container->segment->get('user_id', 0);
				// Load the current user
				$this->currentUser = $this->getUser($id);
			}

			$user = $this->currentUser;
		}
		else
		{
			// Create a new user
			/** @var UserInterface $user */
			$user = new $this->user_class;

			// Create and attach the privilege objects
			if (!empty($this->privileges))
			{
				foreach ($this->privileges as $name => $privilegeClass)
				{
					$privilegeObject = new $privilegeClass();
					$user->attachPrivilegePlugin($name, $privilegeObject);
				}
			}

			// Create and attach the authentication objects
			if (!empty($this->authentications))
			{
				foreach ($this->authentications as $name => $authenticationClass)
				{
					$authenticationObject = new $authenticationClass();
					$user->attachAuthenticationPlugin($name, $authenticationObject);
				}
			}

			$data = null;

			if (!empty($id))
			{
				try
				{
					// Load the data from the database
					$db = $this->container->db;
					$query = $db->getQuery(true)
						->select('*')
						->from($db->qn($this->user_table))
						->where($db->qn('id') . ' = ' . $db->q($id));
					$db->setQuery($query);
					$data = $db->loadObject();
				}
				catch (\Exception $e)
				{
					$data = new \stdClass();
				}

				if (!is_object($data))
				{
					return null;
				}
			}

			// Bind the data to the user object
			if (is_object($data))
			{
				$user->bind($data);
			}
		}

		// Finally, return the user object
		return $user;
	}

	/**
	 * Get user by username
	 *
	 * @param   string  $username  The username of the user to load
	 *
	 * @return  UserInterface|null  A user object if it exists, null if it doesn't
	 */
	public function getUserByUsername($username)
	{
		try
		{
			$db = $this->container->db;
			$query = $db->getQuery(true)
				->select($db->qn('id'))
				->from($db->qn($this->user_table))
				->where($db->qn('username') . ' = ' . $db->q($username));
			$db->setQuery($query);
			$id = $db->loadResult();
		}
		catch (\Exception $e)
		{
			$id = null;
		}

		if (is_null($id))
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
		$user = $this->getUserByUsername($username);

		if (is_null($user))
		{
			throw new \RuntimeException(Text::_('AWF_USER_ERROR_AUTHERROR'), 403);
		}

		if (!$user->verifyPassword($password, $params))
		{
			throw new \RuntimeException(Text::_('AWF_USER_ERROR_AUTHERROR'), 403);
		}

		$this->container->segment->set('user_id', $user->getId());
		$this->currentUser = $user;
	}

	/**
	 * Log out the current user. Logging out a user immediately clears the session storage.
	 *
	 * @return  void
	 */
	public function logoutUser()
	{
		$this->currentUser = null;
		$this->container->segment->clear();
	}

	/**
	 * Save the provided user record
	 *
	 * @param   UserInterface  $user  The user to save
	 *
	 * @return  boolean  True on success
	 *
	 * @throws  \RuntimeException  If an error occurs when saving the user
	 */
	public function saveUser(UserInterface $user)
	{
		$user->triggerEvent('onBeforeSave');

		$db = $this->container->db;

		if ($user->getId())
		{
			$query = $db->getQuery(true)
				->update($db->qn($this->user_table))
				->set($db->qn('username') . ' = ' . $db->q($user->getUsername()))
				->set($db->qn('name') . ' = ' . $db->q($user->getName()))
				->set($db->qn('email') . ' = ' . $db->q($user->getEmail()))
				->set($db->qn('password') . ' = ' . $db->q($user->getPassword()))
				->set($db->qn('parameters') . ' = ' . $db->q($user->getParameters()->toString('JSON')))
				->where($db->qn('id') . ' = ' . $db->q($user->getId()));
		}
		else
		{
			$query = $db->getQuery(true)
				->insert($db->qn($this->user_table))
				->columns(array(
					$db->qn('username'),
					$db->qn('name'),
					$db->qn('email'),
					$db->qn('password'),
					$db->qn('parameters'),
				))->values(
					$db->q($user->getUsername()) . ', ' .
					$db->q($user->getName()) . ', ' .
					$db->q($user->getEmail()) . ', ' .
					$db->q($user->getPassword()) . ', ' .
					$db->q($user->getParameters()->toString('JSON'))
				);
		}

		$db->setQuery($query);
		$db->execute();

		$user->triggerEvent('onAfterSave');
	}

	/**
	 * Delete the user given their ID
	 *
	 * @param   integer  $id  The numeric ID of the user record to delete
	 *
	 * @return  boolean  True on success
	 *
	 * @throws  \RuntimeException  If an error occurs when saving the user
	 */
	public function deleteUser($id)
	{
		if (empty($id))
		{
			return null;
		}

		$db = $this->container->db;

		$query = $db->getQuery(true)
			->delete($db->qn($this->user_table))
			->where($db->qn('id') . ' = ' . $db->q($id));

		$db->setQuery($query);
		$db->execute();

        return true;
	}

	/**
	 * Register a privilege plugin class with this user manager
	 *
	 * @param   string  $name       The name of the privilege management object
	 * @param   string  $privilege  The privilege management class name we will be attaching to user objects
	 *
	 * @return  void
	 */
	public function registerPrivilegePlugin($name, $privilege)
	{
		$this->privileges[$name] = $privilege;
	}

	/**
	 * Unregister a privilege plugin class from this user manager
	 *
	 * @param   string  $name       The name of the privilege management object to unregister
	 *
	 * @return  void
	 */
	public function unregisterPrivilegePlugin($name)
	{
		if (isset($this->privileges[$name]))
		{
			unset($this->privileges[$name]);
		}
	}

	/**
	 * Register a user authentication class with this user manager
	 *
	 * @param   string  $name            The name of the user authentication object
	 * @param   string  $authentication  The user authentication class name we will be attaching to user objects
	 *
	 * @return  void
	 */
	public function registerAuthenticationPlugin($name, $authentication)
	{
		$this->authentications[$name] = $authentication;
	}

	/**
	 * Unregister a user authentication class from this user manager
	 *
	 * @param   string  $name       The name of the user authentication object to unregister
	 *
	 * @return  mixed
	 */
	public function unregisterAuthenticationPlugin($name)
	{
		if (isset($this->authentications[$name]))
		{
			unset($this->authentications[$name]);
		}
	}
}