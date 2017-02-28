<?php
/**
 * @package		solo
 * @copyright	2014-2017 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license		GNU GPL version 3 or later
 */

namespace Solo\Application;

use Awf\User\Authentication;

class UserAuthenticationPassword extends Authentication
{
	/**
	 * Is this user authenticated by this object? The $params array contains at least one key, 'password'.
	 *
	 * @param   array   $params    The parameters used in the authentication process
	 *
	 * @return  boolean  True if the user is authenticated (or this plugin doesn't apply), false otherwise
	 */
	public function onAuthentication($params = array())
	{
		$password = isset($params['password']) ? $params['password'] : '';
		$hashedPassword = $this->user->getPassword();

		if (substr($hashedPassword, 0, 4) == '$2y$')
		{
			return password_verify($password, $hashedPassword);
		}
		else
		{
			$parts = explode(':', $hashedPassword, 3);

			switch ($parts[0])
			{
				case 'SHA512':
					return $this->timingSafeEquals($parts[1], hash('sha512', $password . $parts[2], false));
					break;

				case 'SHA256':
					return $this->timingSafeEquals($parts[1], hash('sha256', $password . $parts[2], false));
					break;

				case 'SHA1':
					return $this->timingSafeEquals($parts[1], sha1($password . $parts[2]));
					break;

				case 'MD5':
					return $this->timingSafeEquals($parts[1], md5($password . $parts[2]));
					break;
			}
		}

		// If all else fails, we assume we can't verify this password
		return false;
	}

	public function onTfaSave($tfaParams)
	{
		$tfaMethod = isset($tfaParams['method']) ? $tfaParams['method'] : '';

		if ($tfaMethod == 'none')
		{
			// Reset other TFA options
			$this->user->getParameters()->set('tfa', null);
			// Set the TFA method to "none"
			$this->user->getParameters()->set('tfa.method', 'none');
		}

		return true;
	}

	/**
	 * A timing safe equals comparison
	 *
	 * To prevent leaking length information, it is important
	 * that user input is always used as the second parameter.
	 *
	 * @param   string  $safe  The internal (safe) value to be checked
	 * @param   string  $user  The user submitted (unsafe) value
	 *
	 * @return  boolean  True if the two strings are identical.
	 */
	protected function timingSafeEquals($safe, $user)
	{
		// Prevent issues if string length is 0
		$safe .= chr(0);
		$user .= chr(0);

		$safeLen = strlen($safe);
		$userLen = strlen($user);

		// Set the result to the difference between the lengths
		$result = $safeLen - $userLen;

		// Note that we ALWAYS iterate over the user-supplied length
		// This is to prevent leaking length information
		for ($i = 0; $i < $userLen; $i++) {
			// Using % here is a trick to prevent notices
			// It's safe, since if the lengths are different
			// $result is already non-0
			$result |= (ord($safe[$i % $safeLen]) ^ ord($user[$i]));
		}

		// They are only identical strings if $result is exactly 0...
		return $result === 0;
	}
}