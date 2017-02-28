<?php
/**
 * @package		awf
 * @copyright	2014-2017 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license		GNU GPL version 3 or later
 */

namespace Awf\Application;
use Awf\Container\Container;
use Awf\Encrypt\Aes;
use Awf\Encrypt\Totp;

/**
 * Provides support for transparent authentication
 */
class TransparentAuthentication
{
	/** Use HTTP Basic Authentication with time-based one time passwords */
	const Auth_HTTPBasicAuth_TOTP = 1;

	/** Use Query String Parameter authentication with time-based one time passwords */
	const Auth_QueryString_TOTP = 2;

	/** Use HTTP Basic Authentication with plain text username and password */
	const Auth_HTTPBasicAuth_Plaintext = 3;

	/** Use single query string parameter authentication with JSON-encoded, plain text username and password */
	const Auth_QueryString_Plaintext = 4;

	/** Use two query string parameters for plain text username and password authentication */
	const Auth_SplitQueryString_Plaintext = 5;

	/** @var int The time step for TOTP authentication */
	protected $timeStep = 6;

	/** @var string The TOTP secret key */
	protected $totpKey = '';

	/** @var string Internal variable */
	private $cryptoKey = '';

	/** @var array Enabled authentication methods, see the class constants */
	protected $authenticationMethods = array(3, 4, 5);

	/** @var string The username required for the Auth_HTTPBasicAuth_TOTP method */
	protected $basicAuthUsername = '_AwfAuth';

	/** @var string The query parameter for the Auth_QueryString_Plaintext method */
	protected $queryParam = '_AwfAuthentication';

	/** @var string The query parameter for the username in the Auth_SplitQueryString_Plaintext method */
	protected $queryParamUsername = '_AwfUsername';

	/** @var string The query parameter for the password in the Auth_SplitQueryString_Plaintext method */
	protected $queryParamPassword = '_AwfPassword';

	/** @var Container The container we are attached to */
	protected $container = null;

	function __construct(Container $container)
	{
		$this->container = $container;
	}

	/**
	 * Set the enabled authentication methods
	 *
	 * @param   array  $authenticationMethods
	 *
	 * @codeCoverageIgnore
	 */
	public function setAuthenticationMethods($authenticationMethods)
	{
		$this->authenticationMethods = $authenticationMethods;
	}

	/**
	 * Get the enabled authentication methods
	 *
	 * @return   array
	 *
	 * @codeCoverageIgnore
	 */
	public function getAuthenticationMethods()
	{
		return $this->authenticationMethods;
	}

	/**
	 * Enable an authentication method
	 *
	 * @param   integer  $method
	 */
	public function addAuthenticationMethod($method)
	{
		if (!in_array($method, $this->authenticationMethods))
		{
			$this->authenticationMethods[] = $method;
		}
	}

	/**
	 * Disable an authentication method
	 *
	 * @param   integer  $method
	 */
	public function removeAuthenticationMethod($method)
	{
		if (in_array($method, $this->authenticationMethods))
		{
			$key = array_search($method, $this->authenticationMethods);
			unset($this->authenticationMethods[$key]);
		}
	}

	/**
	 * Set the required username for the HTTP Basic Authentication with TOTP method
	 *
	 * @param string $basicAuthUsername
	 *
	 * @codeCoverageIgnore
	 */
	public function setBasicAuthUsername($basicAuthUsername)
	{
		$this->basicAuthUsername = $basicAuthUsername;
	}

	/**
	 * Get the required username for the HTTP Basic Authentication with TOTP method
	 *
	 * @return string
	 *
	 * @codeCoverageIgnore
	 */
	public function getBasicAuthUsername()
	{
		return $this->basicAuthUsername;
	}

	/**
	 * Set the query parameter for the Auth_QueryString_TOTP method
	 *
	 * @param string $queryParam
	 *
	 * @codeCoverageIgnore
	 */
	public function setQueryParam($queryParam)
	{
		$this->queryParam = $queryParam;
	}

	/**
	 * Get the query parameter for the Auth_QueryString_TOTP method
	 *
	 * @return string
	 *
	 * @codeCoverageIgnore
	 */
	public function getQueryParam()
	{
		return $this->queryParam;
	}

	/**
	 * Set the query string for the password in the Auth_SplitQueryString_Plaintext method
	 *
	 * @param string $queryParamPassword
	 *
	 * @codeCoverageIgnore
	 */
	public function setQueryParamPassword($queryParamPassword)
	{
		$this->queryParamPassword = $queryParamPassword;
	}

	/**
	 * Get the query string for the password in the Auth_SplitQueryString_Plaintext method
	 *
	 * @return string
	 *
	 * @codeCoverageIgnore
	 */
	public function getQueryParamPassword()
	{
		return $this->queryParamPassword;
	}

	/**
	 * Set the query string for the username in the Auth_SplitQueryString_Plaintext method
	 *
	 * @param string $queryParamUsername
	 *
	 * @codeCoverageIgnore
	 */
	public function setQueryParamUsername($queryParamUsername)
	{
		$this->queryParamUsername = $queryParamUsername;
	}

	/**
	 * Get the query string for the username in the Auth_SplitQueryString_Plaintext method
	 *
	 * @return string
	 *
	 * @codeCoverageIgnore
	 */
	public function getQueryParamUsername()
	{
		return $this->queryParamUsername;
	}

	/**
	 * Set the time step in seconds for the TOTP in the Auth_HTTPBasicAuth_TOTP method
	 *
	 * @param int $timeStep
	 *
	 * @codeCoverageIgnore
	 */
	public function setTimeStep($timeStep)
	{
		$this->timeStep = (int)$timeStep;
	}

	/**
	 * Get the time step in seconds for the TOTP in the Auth_HTTPBasicAuth_TOTP method
	 *
	 * @return int
	 *
	 * @codeCoverageIgnore
	 */
	public function getTimeStep()
	{
		return $this->timeStep;
	}

	/**
	 * Set the secret key for the TOTP in the Auth_HTTPBasicAuth_TOTP method
	 *
	 * @param string $totpKey
	 *
	 * @codeCoverageIgnore
	 */
	public function setTotpKey($totpKey)
	{
		$this->totpKey = $totpKey;
	}

	/**
	 * Get the secret key for the TOTP in the Auth_HTTPBasicAuth_TOTP method
	 *
	 * @return string
	 *
	 * @codeCoverageIgnore
	 */
	public function getTotpKey()
	{
		return $this->totpKey;
	}

	/**
	 * Tries to get the transparent authentication credentials from the request
	 *
	 * @return  array|null
	 */
	public function getTransparentAuthenticationCredentials()
	{
		$return = null;

		// Make sure there are enabled transparent authentication methods
		if (empty($this->authenticationMethods))
		{
			return $return;
		}

		$input = $this->container->input;

		foreach ($this->authenticationMethods as $method)
		{
			switch ($method)
			{
				case self::Auth_HTTPBasicAuth_TOTP:
					if (empty($this->totpKey))
					{
						continue;
					}

					if (empty($this->basicAuthUsername))
					{
						continue;
					}

					if (!isset($_SERVER['PHP_AUTH_USER']))
					{
						continue;
					}

					if (!isset($_SERVER['PHP_AUTH_PW']))
					{
						continue;
					}

					if ($_SERVER['PHP_AUTH_USER'] != $this->basicAuthUsername)
					{
						continue;
					}

					$encryptedData = $_SERVER['PHP_AUTH_PW'];

					return $this->decryptWithTOTP($encryptedData);

					break;

				case self::Auth_QueryString_TOTP:
					if (empty($this->queryParam))
					{
						continue;
					}

					$encryptedData = $input->get($this->queryParam, '', 'raw');

					if (empty($encryptedData))
					{
						continue;
					}

					$return = $this->decryptWithTOTP($encryptedData);

					if (!is_null($return))
					{
						return $return;
					}

					break;

				case self::Auth_HTTPBasicAuth_Plaintext:
					if (!isset($_SERVER['PHP_AUTH_USER']))
					{
						continue;
					}

					if (!isset($_SERVER['PHP_AUTH_PW']))
					{
						continue;
					}

					return array(
						'username'	 => $_SERVER['PHP_AUTH_USER'],
						'password'	 => $_SERVER['PHP_AUTH_PW']
					);

					break;

				case self::Auth_QueryString_Plaintext:
					if (empty($this->queryParam))
					{
						continue;
					}

					$jsonEncoded = $input->get($this->queryParam, '', 'raw');

					if (empty($jsonEncoded))
					{
						continue;
					}

					$authInfo = json_decode($jsonEncoded, true);

					if (!is_array($authInfo))
					{
						continue;
					}

					if (!array_key_exists('username', $authInfo) || !array_key_exists('password', $authInfo))
					{
						continue;
					}

					return $authInfo;

					break;

				case self::Auth_SplitQueryString_Plaintext:
					if (empty($this->queryParamUsername))
					{
						continue;
					}

					if (empty($this->queryParamPassword))
					{
						continue;
					}

					$username = $input->get($this->queryParamUsername, '', 'raw');
					$password = $input->get($this->queryParamPassword, '', 'raw');

					if (empty($username))
					{
						continue;
					}

					if (empty($password))
					{
						continue;
					}

					return array(
						'username'	=> $username,
						'password'	=> $password
					);

					break;
			}
		}

		return $return;
	}

	/**
	 * Decrypts a transparent authentication message using a TOTP
	 *
	 * @param   string  $encryptedData  The encrypted data
	 *
	 * @return  array  The decrypted data
	 */
	private function decryptWithTOTP($encryptedData)
	{
		if (empty($this->totpKey))
		{
			$this->cryptoKey = null;

			return null;
		}

		$totp = new Totp($this->timeStep);
		$period = $totp->getPeriod();
		$period--;

		for ($i = 0; $i <= 2; $i++)
		{
			$time = ($period + $i) * $this->timeStep;
			$otp = $totp->getCode($this->totpKey, $time);
			$this->cryptoKey = hash('sha256', $this->totpKey . $otp);

			$aes = new Aes($this->cryptoKey);
			try
			{
				$ret = $aes->decryptString($encryptedData);
			}
			catch (\Exception $e)
			{
				continue;
			}
			$ret = rtrim($ret, "\000");

			$ret = json_decode($ret, true);

			if (!is_array($ret))
			{
				continue;
			}

			if (!array_key_exists('username', $ret))
			{
				continue;
			}

			if (!array_key_exists('password', $ret))
			{
				continue;
			}

			// Successful decryption!
			return $ret;
		}

		// Obviously if we're here we could not decrypt anything. Bail out.
		$this->cryptoKey = null;

		return null;
	}
}