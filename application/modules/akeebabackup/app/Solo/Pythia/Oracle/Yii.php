<?php
/**
 * @package    oakcms
 * @author     Hryvinskyi Volodymyr <script@email.ua>
 * @copyright  Copyright (c) 2015 - 2016. Hryvinskyi Volodymyr
 * @version    0.0.1-alpha.0.4
 */

namespace Solo\Pythia\Oracle;

use Solo\Pythia\AbstractOracle;

class Yii extends AbstractOracle
{
	/**
	 * The name of this oracle class
	 *
	 * @var  string
	 */
	protected $oracleName = 'yii';

	/**
	 * Does this class recognises the CMS type as Oakcms?
	 *
	 * @return  boolean
	 */
	public function isRecognised()
	{
		if (!@file_exists($this->path . '/.env') && !@file_exists($this->path . '/../.env'))
		{
			return false;
		}

		if (!@file_exists($this->path . '/index.php'))
		{
			return false;
		}

		if (!@is_dir($this->path . '/application'))
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


		foreach ($_ENV as $k=>$line)
		{
            switch (strtoupper($k))
            {
                case 'DB_NAME':
                    $ret['name'] = $line;
                    break;

                case 'DB_USERNAME':
                    $ret['username'] = $line;
                    break;

                case 'DB_PASSWORD':
                    $ret['password'] = $line;
                    break;

                case 'DB_HOST':
                    $ret['host'] = $line;
                    break;

            }
            $ret['prefix'] = "solo_";
		}
		return $ret;
	}
}
