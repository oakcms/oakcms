<?php
/**
 * @package		solo
 * @copyright	2014-2017 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license		GNU GPL version 3 or later
 */

namespace Solo\Pythia\Oracle;

use Solo\Pythia\AbstractOracle;

class Prestashop extends AbstractOracle
{
	/**
	 * The name of this oracle class
	 *
	 * @var  string
	 */
	protected $oracleName = 'prestashop';

	/**
	 * Does this class recognises the CMS type as Prestashop?
	 *
	 * @return  boolean
	 */
	public function isRecognised()
	{
		if (!@file_exists($this->path . '/config/settings.inc.php'))
		{
			return false;
		}

		if (!@file_exists($this->path . '/config/smarty.config.inc.php'))
		{
			return false;
		}

		return true;
	}

	/**
	 * Return the database connection information for this CMS / script
	 *
	 * @return  array
	 */
	public function getDbInformation()
	{
		$ret = array(
			'driver'	=> 'mysqli',
			'host'		=> '',
			'port'		=> '',
			'username'	=> '',
			'password'	=> '',
			'name'		=> '',
			'prefix'	=> '',
		);

		$fileContents = file($this->path . '/config/settings.inc.php');

		foreach ($fileContents as $line)
		{
			$line    = trim($line);

			// Skip commented lines. However it will get the line between a multiline comment, but that's not a problem
			if (strpos($line, '#') === 0 || strpos($line, '//') === 0 || strpos($line, '/*') === 0)
			{
				continue;
			}

			if (strpos($line, 'define') !== false)
			{
				list($key, $value) = $this->parseDefine($line);

				if (!empty($key))
				{

					switch (strtoupper($key))
					{
						case '_DB_SERVER_':
							$ret['host'] = $value;
							break;
						case '_DB_USER_':
							$ret['username'] = $value;
							break;
						case '_DB_PASSWD_':
							$ret['password'] = $value;
							break;
						case '_DB_NAME_' :
							$ret['name'] = $value;
							break;
						case '_DB_PREFIX_':
							$ret['prefix'] = $value;
							break;
						default:
							// Do nothing, it's a variable we're not interested in
							break;
					}
				}
			}
		}

		return $ret;
	}
}