<?php
/**
 * @package     Awf
 * @copyright   2014-2017 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license     GNU GPL version 3 or later
 */

namespace Awf\Utils;

abstract class StringHandling
{
	/**
	 * Convert a string into a slug (alias), suitable for use in URLs. Please
	 * note that transliteration support is rudimentary at this stage.
	 *
	 * @param   string  $value  A string to convert to slug
	 *
	 * @return  string  The slug
	 */
	public static function toSlug($value)
	{
		// Remove any '-' from the string they will be used as concatenator
		$value = str_replace('-', ' ', $value);

		// Convert to ascii characters
		$value = self::toASCII($value);

		// Lowercase and trim
		$value = trim(strtolower($value));

		// Remove any duplicate whitespace, and ensure all characters are alphanumeric
		$value = preg_replace(array('/\s+/', '/[^A-Za-z0-9\-_]/'), array('-', ''), $value);

		// Limit length
		if (strlen($value) > 100)
		{
			$value = substr($value, 0, 100);
		}

		return $value;
	}

	/**
	 * Convert common northern European languages' letters into plain ASCII. This
	 * is a rudimentary transliteration.
	 *
	 * @param   string  $value  The value to convert to ASCII
	 *
	 * @return  string  The converted string
	 */
	public static function toASCII($value)
	{
		$string = htmlentities(utf8_decode($value), null, 'ISO-8859-1');
		$string = preg_replace(
			array('/&szlig;/', '/&(..)lig;/', '/&([aouAOU])uml;/', '/&(.)[^;]*;/'), array('ss', "$1", "$1" . 'e', "$1"), $string
		);

		return $string;
	}

	/**
	 * Convert a string to a boolean.
	 *
	 * @param   string  $string  The string.
	 *
	 * @return  boolean  The converted string
	 */
	public static function toBool($string)
	{
		$string = trim((string)$string);
		$string = strtolower($string);

		if (in_array($string, array(1, 'true', 'yes', 'on', 'enabled')))
		{
			return true;
		}

		if (in_array($string, array(1, 'false', 'no', 'off', 'disabled')))
		{
			return false;
		}

		return (bool)$string;
	}
} 