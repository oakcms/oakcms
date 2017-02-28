<?php
/**
 * @package		akeebabackupwp
 * @copyright	2014-2017 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license		GNU GPL version 3 or later
 */

namespace Solo\Application;


use Awf\User\Manager;
use Awf\User\UserInterface;

class UserManager extends Manager
{
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
		if (is_null($id))
		{
			// We don't have a current user yet? Let's load it!
			if (!is_object($this->currentUser))
			{
				if (function_exists('get_current_user_id'))
				{
					$defaultId = get_current_user_id();
				}
				else
				{
					$defaultId = 0;
				}

				$id = $this->container->segment->get('user_id', $defaultId);

				if ($id == 0)
				{
					$id = $defaultId;
					$this->container->segment->set('user_id', $defaultId);
				}

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
					if (!function_exists('get_userdata'))
					{
						throw new \RuntimeException('Not inside WordPress', 600);
					}

					// Load the data from the database
					$userData = get_userdata($id);

					$data = (object)array(
						'id'			=> $userData->ID,
						'username'		=> $userData->user_login,
						'name'			=> $userData->display_name,
						'email'			=> $userData->user_email,
						'wpCaps'		=> $userData->caps,
						'wpAllCaps'		=> $userData->allcaps,
						'wpRoles'		=> $userData->roles,
					);
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
}