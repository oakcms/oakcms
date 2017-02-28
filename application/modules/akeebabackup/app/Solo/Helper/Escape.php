<?php
/**
 * @package        solo
 * @copyright      2014-2017 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license        GNU GPL version 3 or later
 */

namespace Solo\Helper;

/**
 * A helper class to escape JSON data
 */
class Escape
{
	/**
	 * Escapes a string returned from Text::_() for use with Javascript
	 *
	 * @param   $string  string  The string to escape
	 * @param   $extras  string  The characters to escape
	 *
	 * @return  string  The escaped string
	 */
	static function escapeJS($string, $extras = '')
	{
		static $gpc = null;

		if (is_null($gpc))
		{
			// Fetch the state of Magic Quotes GPC
			if (function_exists('magic_quotes_gpc'))
			{
				$gpc = magic_quotes_gpc();
			}
			else
			{
				$gpc = false;
			}
		}

		// Make sure we escape single quotes, slashes and brackets
		if (empty($extras))
		{
			$extras = "'\\[]";
		}

		if ($gpc)
		{
			// When Magic Quotes GPC is on, the string is already escaped, so...
			$string = stripslashes($string);
		}

		return addcslashes($string, $extras);
	}
} 