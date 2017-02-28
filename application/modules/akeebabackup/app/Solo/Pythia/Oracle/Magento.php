<?php
/**
 * @package		solo
 * @copyright	2014-2017 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license		GNU GPL version 3 or later
 */

namespace Solo\Pythia\Oracle;

use Solo\Pythia\AbstractOracle;

class Magento extends AbstractOracle
{
	/**
	 * The name of this oracle class
	 *
	 * @var  string
	 */
	protected $oracleName = 'magento';

	/**
	 * Does this class recognises the site as a Moodle installation?
	 *
	 * @return  boolean
	 */
	public function isRecognised()
	{
        if (!@file_exists($this->path . '/api.php'))
        {
            return false;
        }

        if (!@file_exists($this->path . '/cron.php'))
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

        $xml = new \SimpleXMLElement($this->path . '/app/etc/local.xml', 0, true);
        $resources = $xml->global->resources;

        $ret['host']     = (string) $resources->default_setup->connection->host;
        $ret['username'] = (string) $resources->default_setup->connection->username;
        $ret['password'] = (string) $resources->default_setup->connection->password;
        $ret['name']     = (string) $resources->default_setup->connection->dbname;
        $ret['prefix']   = (string) $resources->db->table_prefix;

        return $ret;
	}
}