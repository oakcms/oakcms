<?php
/**
 * @package		solo
 * @copyright	2014-2017 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license		GNU GPL version 3 or later
 */

namespace Solo\Pythia\Oracle;

use Solo\Pythia\AbstractOracle;

class Magento2 extends AbstractOracle
{
	/**
	 * The name of this oracle class
	 *
	 * @var  string
	 */
	protected $oracleName = 'magento2';

	/**
	 * Does this class recognises the site as a Moodle installation?
	 *
	 * @return  boolean
	 */
	public function isRecognised()
	{
        if (!@file_exists($this->path . '/bin/magento'))
        {
            return false;
        }

        if (!@file_exists($this->path . '/app/etc/env.php'))
        {
            return false;
        }

        if (!@is_dir($this->path . '/app'))
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

        $config = include_once $this->path . '/app/etc/env.php';

        $ret['host']     = $config['db']['connection']['default']['host'];
        $ret['username'] = $config['db']['connection']['default']['username'];
        $ret['password'] = $config['db']['connection']['default']['password'];
        $ret['name']     = $config['db']['connection']['default']['dbname'];
        $ret['prefix']   = $config['db']['table_prefix'];

        return $ret;
	}

}