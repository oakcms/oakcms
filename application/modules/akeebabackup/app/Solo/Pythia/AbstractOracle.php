<?php
/**
 * @package		solo
 * @copyright	2014-2017 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license		GNU GPL version 3 or later
 */

namespace Solo\Pythia;

abstract class AbstractOracle implements OracleInterface
{
	/**
	 * The site root path the object was created with
	 *
	 * @var  string
	 */
	protected $path = null;

	/**
	 * The name of this oracle class
	 *
	 * @var  string
	 */
	protected $oracleName = 'generic';

	/**
	 * The installer name which corresponds to the CMS recognized by this Oracle
	 *
	 * @var  string
	 */
	protected $installerName = '';

	/**
	 * Creates a new oracle objects
	 *
	 * @param   string  $path  The directory path to scan
	 */
	public function __construct($path)
	{
		$this->path = $path;
	}

	/**
	 * Does this class recognises the CMS type as Wordpress?
	 *
	 * @return  boolean
	 */
	public function isRecognised()
	{
		return false;
	}

	/**
	 * Return the name of the CMS / script
	 *
	 * @return  string
	 */
	public function getName()
	{
		return $this->oracleName;
	}

	/**
	 * Return the default installer name for this CMS / script (angie)
	 *
	 * @return  string
	 */
	public function getInstaller()
	{
		if (empty($this->installerName))
		{
			$this->installerName = 'angie-' . $this->oracleName;
		}

		return $this->installerName;
	}

	/**
	 * Return the database connection information for this CMS / script
	 *
	 * @return  array
	 */
	public function getDbInformation()
	{
		return array(
			'driver'   => 'mysqli',
			'host'     => '',
			'port'     => '',
			'username' => '',
			'password' => '',
			'name'     => '',
			'prefix'   => '',
		);
	}

	/**
	 * Return extra directories required by the CMS / script
	 *
	 * @return array
	 */
	public function getExtradirs()
	{
		return array();
	}

	/**
	 * Return extra databases required by the CMS / script (ie Drupal multi-site)
	 *
	 * @return array
	 */
	public function getExtraDb()
	{
		return array();
	}

	/**
	 * Parse a PHP file line with a define statement and return the constant name and its value
	 *
	 * @param   string  $line  The line to parse
	 *
	 * @return  array  array($key, $value)
	 */
	protected function parseDefine($line)
	{
		$pattern = '#define\s*\(\s*(["\'][A-Z_]*["\'])\s*,\s*(["\'].*["\'])\s*\)\s*;#u';
		$numMatches = preg_match($pattern, $line, $matches);

		if ($numMatches < 1)
		{
			return array('', '');
		}

		$key = trim($matches[1], '"\'');
		$value = $matches[2];

		$value = $this->parseStringDefinition($value);

		if (is_null($value))
		{
			return array('', '');
		}

		return array($key, $value);
	}

	/**
	 * Parses a string definition, surrounded by single or double quotes, removing any comments which may be left tucked
	 * to its end, reducing escaped characters to their unescaped equivalent and returning the clean string.
	 *
	 * @param   string  $value
	 *
	 * @return  null|string  Null if we can't parse $value as a string.
	 */
	protected function parseStringDefinition($value)
	{
		// At this point the value may be in the form 'foobar');#comment'gargh" if the original line was something like
		// define('DB_NAME', 'foobar');#comment'gargh");

		$quote = $value[0];

		// The string ends in a different quote character. Backtrack to the matching quote.
		if (substr($value, -1) != $quote)
		{
			$lastQuote = strrpos($value, $quote);

			// WTF?!
			if ($lastQuote <= 1)
			{
				return null;
			}

			$value = substr($value, 0, $lastQuote + 1);
		}

		// At this point the value may be cleared but still in the form 'foobar');#comment'
		// We need to parse the string like PHP would. First, let's trim the quotes
		$value = trim($value, $quote);

		$pos = 0;

		while ($pos !== false)
		{
			$pos = strpos($value, $quote, $pos);

			if ($pos === false)
			{
				break;
			}

			if (substr($value, $pos - 1, 1) == '\\')
			{
				$pos++;

				continue;
			}

			$value = substr($value, 0, $pos);
		}

		// Finally, reduce the escaped characters.

		if ($quote == "'")
		{
			// Single quoted strings only escape single quotes and backspaces
			$value = str_replace(array("\\'", "\\\\",), array("'", "\\"), $value);
		}
		else
		{
			// Double quoted strings just need stripslashes.
			$value = stripslashes($value);
		}

		return $value;
	}
}