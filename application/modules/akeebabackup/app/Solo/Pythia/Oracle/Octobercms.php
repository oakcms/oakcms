<?php
/**
 * @package		solo
 * @copyright	2014-2017 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license		GNU GPL version 3 or later
 */

namespace Solo\Pythia\Oracle;

use Solo\Pythia\AbstractOracle;

class Octobercms extends AbstractOracle
{
	/**
	 * The name of this oracle class
	 *
	 * @var  string
	 */
	protected $oracleName = 'octobercms';

	/**
	 * Does this class recognises the CMS type as October CMS?
	 *
	 * @return  boolean
	 */
	public function isRecognised()
	{
		if (!@file_exists($this->path . '/config/app.php'))
		{
			return false;
		}

		if (!@file_exists($this->path . '/config/broadcasting.php'))
		{
			return false;
		}

		if (!@is_dir($this->path . '/vendor/october'))
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
			'driver'   => 'mysqli',
			'host'     => '',
			'port'     => '',
			'username' => '',
			'password' => '',
			'name'     => '',
			'prefix'   => '',
		);

		$configuration = $this->includeConfigFile();
		$default = $configuration['default'];

		$connection = $configuration['connections'][$default];

		$ret['driver'] = $connection['driver'];
		$ret['prefix'] = $connection['prefix'];

		// We have such info only if we're using MySQL
		if ($ret['driver'] != 'sqlite')
		{
			$ret['host']     = $connection['host'];
			$ret['username'] = $connection['username'];
			$ret['password'] = $connection['password'];
			$ret['name']     = $connection['database'];
		}
		elseif ($ret['driver'] == 'sqlite')
		{
			$ret['name'] = $this->path.'/'.$connection['database'];
		}

		return $ret;
	}

	private function includeConfigFile()
	{
		return include $this->path.'/config/database.php';
	}

}