<?php
/**
 * @package        akeebabackupwp
 * @copyright      2014-2017 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license        GNU GPL version 3 or later
 */

namespace Akeeba\Engine\Driver;

use Akeeba\Engine\Base\Object;
use Akeeba\Engine\Factory;
use Akeeba\Engine\Platform;

// Protection against direct access
defined('AKEEBAENGINE') or die();

class Wordpress extends Object
{
	/** @var Base The real database connection object */
	private $dbo;

	/**
	 * Database object constructor
	 *
	 * @param    array    $options List of options used to configure the connection
	 */
	public function __construct($options = array())
	{
		global $wpdb;

		$options['connection'] = $wpdb->dbh;
		$driver = '\\Akeeba\\Engine\\Driver\\Mysqli';

		if (!is_object($wpdb->dbh) || !($wpdb->dbh instanceof \mysqli))
		{
			$driver = '\\Akeeba\\Engine\\Driver\\' .
				(function_exists('mysql_connect') ? 'Mysql' : 'Mysqli');
		}

		$this->dbo = new $driver($options);

		// Propagate errors
		$this->propagateFromObject($this->dbo);
	}

	public function close()
	{
		if (method_exists($this->dbo, 'close'))
		{
			$this->dbo->close();
		}
	}

	public function open()
	{
		if (method_exists($this->dbo, 'open'))
		{
			$this->dbo->open();
		}
	}

	/**
	 * Magic method to proxy all calls to the loaded database driver object
	 */
	public function __call($name, array $arguments)
	{
		if (is_null($this->dbo))
		{
			throw new \Exception('Akeeba Engine database driver is not loaded');
		}

		if (method_exists($this->dbo, $name) || in_array($name, array('q', 'nq', 'qn')))
		{
			// Call_user_func_array is ~3 times slower than direct method calls.
			// (thank you, Nooku Framework, for the tip!)
			switch (count($arguments))
			{
				case 0 :
					$result = $this->dbo->$name();
					break;
				case 1 :
					$result = $this->dbo->$name($arguments[0]);
					break;
				case 2:
					$result = $this->dbo->$name($arguments[0], $arguments[1]);
					break;
				case 3:
					$result = $this->dbo->$name($arguments[0], $arguments[1], $arguments[2]);
					break;
				case 4:
					$result = $this->dbo->$name($arguments[0], $arguments[1], $arguments[2], $arguments[3]);
					break;
				case 5:
					$result = $this->dbo->$name($arguments[0], $arguments[1], $arguments[2], $arguments[3], $arguments[4]);
					break;
				default:
					// Resort to using call_user_func_array for many segments
					$result = call_user_func_array(array($this->dbo, $name), $arguments);
			}
			return $result;
		}
		else
		{
			throw new \Exception('Method ' . $name . ' not found in Akeeba Platform');
		}
	}

	public function __get($name)
	{
		if (isset($this->dbo->$name) || property_exists($this->dbo, $name))
		{
			return $this->dbo->$name;
		}
		else
		{
			$this->dbo->$name = null;
			user_error('Database driver does not support property ' . $name);
		}
	}

	public function __set($name, $value)
	{
		if (isset($this->dbo->name) || property_exists($this->dbo, $name))
		{
			$this->dbo->$name = $value;
		}
		else
		{
			$this->dbo->$name = null;
			user_error('Database driver not support property ' . $name);
		}
	}
} 