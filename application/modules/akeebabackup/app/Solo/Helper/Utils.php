<?php
/**
 * @package        solo
 * @copyright      2014-2017 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license        GNU GPL version 3 or later
 */

namespace Solo\Helper;
use Akeeba\Engine\Factory;
use Awf\Text\Text;

/**
 * Various utility methods
 */
class Utils
{
	/**
	 * Get the relative path of a directory ($to) against a base directory ($from). Both directories are given as
	 * absolute paths.
	 *
	 * @param   string $from The base directory
	 * @param   string $to   The directory to convert to a relative path
	 *
	 * @return  string  The path of $to relative to $from
	 */
	public static function getRelativePath($from, $to)
	{
		// Some compatibility fixes for Windows paths
		$from = is_dir($from) ? rtrim($from, '\/') . '/' : $from;
		$to   = is_dir($to) ? rtrim($to, '\/') . '/' : $to;
		$from = str_replace('\\', '/', $from);
		$to   = str_replace('\\', '/', $to);

		$from    = explode('/', $from);
		$to      = explode('/', $to);
		$relPath = $to;

		foreach ($from as $depth => $dir)
		{
			// find first non-matching dir
			if ($dir === $to[ $depth ])
			{
				// ignore this directory
				array_shift($relPath);
			}
			else
			{
				// get number of remaining dirs to $from
				$remaining = count($from) - $depth;
				if ($remaining > 1)
				{
					// add traversals up to first matching dir
					$padLength = (count($relPath) + $remaining - 1) * - 1;
					$relPath   = array_pad($relPath, $padLength, '..');
					break;
				}
				else
				{
					$relPath[0] = './' . $relPath[0];
				}
			}
		}

		return implode('/', $relPath);
	}

	/**
	 * Get a dropdown list for database drivers
	 *
	 * @param   string  $selected  Selected value
	 * @param   string  $name      The name (also used for id) of the field, default: driver
	 *
	 * @return  string  HTML
	 */
	public static function engineDatabaseTypesSelect($selected = '', $name = 'driver')
	{
		$connectors = array('mysql', 'mysqli', 'none', 'pdomysql', 'postgresql', 'sqlazure', 'sqlite', 'sqlsrv');

		$html = '<select class="form-control" name="' . $name . '" id="' . $name . '">' . "\n";

		foreach($connectors as $connector)
		{
			$checked   = (strtoupper($selected) == strtoupper($connector)) ? 'selected="selected"' : '';

			$html .= "\t<option value=\"$connector\" $checked>" . Text::_('SOLO_SETUP_LBL_DATABASE_DRIVER_' . $connector) . "</option>\n";
		}

		$html .= "</select>";

		return $html;
	}
} 