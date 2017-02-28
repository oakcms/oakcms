<?php
/**
 * @package		solo
 * @copyright	2014-2017 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license		GNU GPL version 3 or later
 */

namespace Solo\Pythia\Oracle;

use Solo\Pythia\AbstractOracle;

class Wordpress extends AbstractOracle
{
	/**
	 * The name of this oracle class
	 *
	 * @var  string
	 */
	protected $oracleName = 'wordpress';

	/**
	 * Does this class recognises the CMS type as Wordpress?
	 *
	 * @return  boolean
	 */
	public function isRecognised()
	{
		if (!@file_exists($this->path . '/wp-config.php') && !@file_exists($this->path . '/../wp-config.php'))
		{
			return false;
		}

		if (!@file_exists($this->path . '/wp-login.php'))
		{
			return false;
		}

		if (!@file_exists($this->path . '/xmlrpc.php'))
		{
			return false;
		}

		if (!@is_dir($this->path . '/wp-admin'))
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

		$filePath = $this->path . '/wp-config.php';

		if (!@file_exists($filePath))
		{
			$filePath = $this->path . '/../wp-config.php';
		}

		$fileContents = file($filePath);

		foreach ($fileContents as $line)
		{
			$line = trim($line);

			if (strpos($line, 'define') !== false)
			{
				list ($key, $value) = $this->parseDefine($line);

				switch (strtoupper($key))
				{
					case 'DB_NAME':
						$ret['name'] = $value;
						break;

					case 'DB_USER':
						$ret['username'] = $value;
						break;

					case 'DB_PASSWORD':
						$ret['password'] = $value;
						break;

					case 'DB_HOST':
						$ret['host'] = $value;
						break;

				}
			}
			elseif (strpos($line, '$table_prefix') === 0)
			{
				$parts = explode('=', $line, 2);
				$prefixData = trim($parts[1]);
				$ret['prefix'] = $this->parseStringDefinition($prefixData);
			}
		}

		return $ret;
	}
}