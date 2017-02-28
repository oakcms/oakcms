<?php
/**
 * @package		solo
 * @copyright	2014-2017 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license		GNU GPL version 3 or later
 */

namespace Solo\Pythia\Oracle;

use Solo\Pythia\AbstractOracle;

class Pagekit extends AbstractOracle
{
	/**
	 * The name of this oracle class
	 *
	 * @var  string
	 */
	protected $oracleName = 'pagekit';

	/**
	 * Does this class recognises the CMS type as Pagekit?
	 *
	 * @return  boolean
	 */
	public function isRecognised()
	{
		if (!@file_exists($this->path . '/config.php'))
		{
			return false;
		}

		if (!@file_exists($this->path . '/pagekit'))
		{
			return false;
		}

		if (!@is_dir($this->path . '/packages/pagekit'))
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

		$ret['driver'] = $configuration['database']['default'];

		$connection = $configuration['database']['connections'][$ret['driver']];

		$ret['prefix'] = $connection['prefix'];

		// We have such info only if we're using MySQL
		if ($ret['driver'] != 'sqlite')
		{
			$ret['host']     = $connection['host'];
			$ret['username'] = $connection['user'];
			$ret['password'] = $connection['password'];
			$ret['name']     = $connection['dbname'];
		}
		elseif ($ret['driver'] == 'sqlite')
		{
			if (file_exists($this->path.'/pagekit.db'))
			{
				$ret['name'] = $this->path.'/pagekit.db';
			}
		}

		return $ret;
	}

	private function includeConfigFile()
	{
		return include $this->path.'/config.php';
	}

}