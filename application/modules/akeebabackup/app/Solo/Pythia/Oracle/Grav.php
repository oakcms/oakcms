<?php
/**
 * @package		solo
 * @copyright	2014-2017 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license		GNU GPL version 3 or later
 */

namespace Solo\Pythia\Oracle;

use Solo\Pythia\AbstractOracle;

class Grav extends AbstractOracle
{
	/**
	 * The name of this oracle class
	 *
	 * @var  string
	 */
	protected $oracleName = 'grav';

	/**
	 * Does this class recognises the CMS type as Grav?
	 *
	 * @return  boolean
	 */
	public function isRecognised()
	{
		if (!@file_exists($this->path . '/user/config/system.yaml'))
		{
			return false;
		}

		if (!@file_exists($this->path . '/system/config/system.yaml'))
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
		// Grav is a flat file CMS (no database required)
		$ret = array(
			'driver'   => 'none',
			'host'     => '',
			'port'     => '',
			'username' => '',
			'password' => '',
			'name'     => '',
			'prefix'   => '',
		);

		return $ret;
	}
}