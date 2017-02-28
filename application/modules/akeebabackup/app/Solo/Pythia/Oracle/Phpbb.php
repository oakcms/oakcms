<?php
/**
 * @package		solo
 * @copyright	2014-2017 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license		GNU GPL version 3 or later
 */

namespace Solo\Pythia\Oracle;

use Solo\Pythia\AbstractOracle;

class Phpbb extends AbstractOracle
{
	/**
	 * The name of this oracle class
	 *
	 * @var  string
	 */
	protected $oracleName = 'phpbb';

	/**
	 * Does this class recognises the site type as phpBB?
	 *
	 * @return  boolean
	 */
	public function isRecognised()
	{
		if (!@file_exists($this->path . '/config.php'))
		{
			return false;
		}

		if (
            !@file_exists($this->path . '/styles/subsilver2/style.cfg') &&
            !@file_exists($this->path . '/styles/prosilver/style.cfg')
        )
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

		$fileContents = file($this->path . '/config.php');

		foreach ($fileContents as $line)
		{
			$line = trim($line);

            $matches = array();

            // Skip commented lines. However it will get the line between a multiline comment, but that's not a problem
            if(strpos($line, '#') === 0 || strpos($line, '//') === 0 || strpos($line, '/*') === 0)
            {
                // simply do nothing, we will add the line later
            }
            else
			{
				preg_match('#\$(.*?)=\s*([\'"](.*)[\'"])#', $line, $matches);

				if (isset($matches[1]))
				{
					$key   = trim($matches[1]);
					$value = trim($matches[2]);
					$value = $this->parseStringDefinition($value);

					switch (strtolower($key))
					{
						case 'dbms':
							$ret['driver'] = $value;
							break;
						case 'dbhost':
							$ret['host'] = $value;
							break;
						case 'dbport':
							$ret['port'] = $value;
							break;
						case 'dbuser':
							$ret['username'] = $value;
							break;
						case 'dbpasswd':
							$ret['password'] = $value;
							break;
						case 'dbname' :
							$ret['name'] = $value;
							break;
						case 'table_prefix' :
							$ret['prefix'] = $value;
							break;
						default:
							// Do nothing, it's a variable we're not insterested in
							break;
					}
				}
			}
		}

		return $ret;
	}
}