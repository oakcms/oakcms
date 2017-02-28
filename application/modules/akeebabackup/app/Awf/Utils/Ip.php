<?php
/**
 * @package     Awf
 * @copyright   2014-2017 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license     GNU GPL version 3 or later
 */

namespace Awf\Utils;

/**
 * IP address helper
 *
 * Makes sure that we get the real IP of the user
 */
class Ip
{
	/**
	 * Gets the visitor's IP address. Automatically handles reverse proxies
	 * reporting the IPs of intermediate devices, like load balancers. Examples:
	 * https://www.akeebabackup.com/support/admin-tools/13743-double-ip-adresses-in-security-exception-log-warnings.html
	 * http://stackoverflow.com/questions/2422395/why-is-request-envremote-addr-returning-two-ips
	 * The solution used is assuming that the last IP address is the external one.
	 *
	 * @return  string
	 */
	public static function getUserIP()
	{
		$ip = self::_real_getUserIP();

		if ((strstr($ip, ',') !== false) || (strstr($ip, ' ') !== false))
		{
			$ip = str_replace(' ', ',', $ip);
			$ip = str_replace(',,', ',', $ip);
			$ips = explode(',', $ip);
			$ip = '';
			while (empty($ip) && !empty($ips))
			{
				$ip = array_pop($ips);
				$ip = trim($ip);
			}
		}
		else
		{
			$ip = trim($ip);
		}

		return $ip;
	}

	/**
	 * Gets the visitor's IP address
	 *
	 * @return  string
	 */
	private static function _real_getUserIP()
	{
		// Normally the $_SERVER superglobal is set
		if (isset($_SERVER))
		{
			// Do we have an x-forwarded-for HTTP header (e.g. NginX)?
			if (array_key_exists('HTTP_X_FORWARDED_FOR', $_SERVER) && !empty($_SERVER['HTTP_X_FORWARDED_FOR']))
			{
				return $_SERVER['HTTP_X_FORWARDED_FOR'];
			}

			// Do we have a client-ip header (e.g. non-transparent proxy)?
			if (array_key_exists('HTTP_CLIENT_IP', $_SERVER) && !empty($_SERVER['HTTP_CLIENT_IP']))
			{
				return $_SERVER['HTTP_CLIENT_IP'];
			}

			// Normal, non-proxied server or server behind a transparent proxy
			if (!empty($_SERVER['REMOTE_ADDR']))
			{
				return $_SERVER['REMOTE_ADDR'];
			}
		}

		// This part is executed on PHP running as CGI, or on SAPIs which do
		// not set the $_SERVER superglobal

		// If getenv() is disabled, you're screwed
		if (!function_exists('getenv'))
		{
			return '';
		}

		// Do we have an x-forwarded-for HTTP header?
		if (getenv('HTTP_X_FORWARDED_FOR'))
		{
			return getenv('HTTP_X_FORWARDED_FOR');
		}

		// Do we have a client-ip header?
		if (getenv('HTTP_CLIENT_IP'))
		{
			return getenv('HTTP_CLIENT_IP');
		}

		// Normal, non-proxied server or server behind a transparent proxy
		if (getenv('REMOTE_ADDR'))
		{
			return getenv('REMOTE_ADDR');
		}

		// Catch-all case for broken servers, apparently
		return '';
	}

	/**
	 * Works around the REMOTE_ADDR not containing the user's IP
	 *
	 * @return  void
	 */
	public static function workaroundIPIssues()
	{
		$ip = self::getUserIP();

		if (isset($_SERVER) && ($_SERVER['REMOTE_ADDR'] == $ip))
		{
			return;
		}
		elseif(!isset($_SERVER) && function_exists('getenv') && (getenv('REMOTE_ADDR') == $ip))
		{
			return;
		}

		if (isset($_SERVER) && array_key_exists('REMOTE_ADDR', $_SERVER))
		{
			$_SERVER['AWF_REMOTE_ADDR'] = $_SERVER['REMOTE_ADDR'];
		}
		elseif (function_exists('getenv'))
		{
			if (getenv('REMOTE_ADDR'))
			{
				$_SERVER['AWF_REMOTE_ADDR'] = getenv('REMOTE_ADDR');
			}
		}

		global $_SERVER;
		$_SERVER['REMOTE_ADDR'] = $ip;

		if (function_exists('putenv'))
		{
			putenv("REMOTE_ADDR=$ip");
		}
	}
}