<?php
/**
 * @package     Awf
 * @copyright   2014-2017 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license     GNU GPL version 3 or later
 */

namespace Awf\User;

/**
 * The interface to the user privilege management classes
 */
interface AuthenticationInterface
{
	/**
	 * Sets the name of this user authentication object
	 *
	 * @param   string  $name  The name of the user authentication object
	 *
	 * @return  void
	 */
	public function setName($name);

	/**
	 * Binds a user object to this user authentication object
	 *
	 * @param   UserInterface  $user  The user object to bind
	 *
	 * @return  mixed
	 */
	public function setUser(UserInterface &$user);

	/**
	 * Is this user authenticated by this object?
	 *
	 * @param   array   $params    The parameters used in the authentication process
	 *
	 * @return  array  True if the user is authenticated (or this plugin doesn't apply), false otherwise
	 */
	public function onAuthentication($params = array());
} 