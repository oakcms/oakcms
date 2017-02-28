<?php
/**
 * @package     Awf
 * @copyright   2014-2017 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license     GNU GPL version 3 or later
 */

namespace Awf\User;

/**
 * An abstract user authentication class, which you can extend to easily provide your custom user authentication class
 */
abstract class Authentication implements AuthenticationInterface
{
	/**
	 * The name of this user authentication method, as known by the user object we are attached to
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
	 * Sets the name of this user authentication object
	 *
	 * @param   string  $name  The name of the user authentication object
	 *
	 * @return  void
	 */
	public function setName($name)
	{
		$this->name = $name;
	}

	/**
	 * Binds a user object to this user authentication object
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
	 * Is this user authenticated by this object? The $params array contains at least one key, 'password'.
	 *
	 * @param   array   $params    The parameters used in the authentication process
	 *
	 * @return  boolean  True if the user is authenticated (or this plugin doesn't apply), false otherwise
	 */
	public function onAuthentication($params = array())
	{
		// I think PHP 5.3.5 requires this?
		return true;
	}
}