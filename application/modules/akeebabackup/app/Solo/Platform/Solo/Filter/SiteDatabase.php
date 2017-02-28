<?php
/**
 * Akeeba Engine
 * The modular PHP5 site backup engine
 * @copyright Copyright (c)2009-2016 Nicholas K. Dionysopoulos
 * @license   GNU GPL version 3 or, at your option, any later version
 * @package   akeebaengine
 *
 */

namespace Akeeba\Engine\Filter;

use Akeeba\Engine\Filter\Base as FilterBase;
use Akeeba\Engine\Factory;
use Akeeba\Engine\Platform;

// Protection against direct access
defined('AKEEBAENGINE') or die();

/**
 * Add site's main database to the backup set.
 */
class SiteDatabase extends FilterBase
{
	public function __construct()
	{
		// This is a directory inclusion filter.
		$this->object = 'db';
		$this->subtype = 'inclusion';
		$this->method = 'direct';
		$this->filter_name = 'SiteDatabase';

		// Add a new record for the core Joomla! database
		// Get core database options
		$configuration = Factory::getConfiguration();

		$options = array(
			'port'     => $configuration->get('akeeba.platform.dbport', ''),
			'host'     => $configuration->get('akeeba.platform.dbhost', ''),
			'user'     => $configuration->get('akeeba.platform.dbusername', ''),
			'password' => $configuration->get('akeeba.platform.dbpassword', ''),
			'database' => $configuration->get('akeeba.platform.dbname', ''),
			'prefix'   => $configuration->get('akeeba.platform.dbprefix', ''),
		);

		$dbdriver = $configuration->get('akeeba.platform.dbdriver', 'mysqli');

		if (($dbdriver == 'mysql') && !function_exists('mysql_connect'))
		{
			$dbdriver = 'mysqli';
		}

		$driver = '\\Akeeba\\Engine\\Driver\\' . ucfirst($dbdriver);

		$host = $options['host'];
		$port = array_key_exists('port', $options) ? $options['port'] : null;

		if (empty($port))
		{
			$port = null;
		}

		$socket = null;
		$targetSlot = substr(strstr($host, ":"), 1);

		if (!empty($targetSlot))
		{
			// Get the port number or socket name
			if (is_numeric($targetSlot) && is_null($port))
			{
				$port = $targetSlot;
			}
			else
			{
				$socket = $targetSlot;
			}

			// Extract the host name only
			$host = substr($host, 0, strlen($host) - (strlen($targetSlot) + 1));

			// This will take care of the following notation: ":3306"
			if ($host == '')
			{
				$host = 'localhost';
			}
		}

		// This is the format of the database inclusion filters
		$entry = array(
			'host'     => $host,
			'port'     => is_null($socket) ? (is_null($port) ? '' : $port) : $socket,
			'username' => $options['user'],
			'password' => $options['password'],
			'database' => $options['database'],
			'prefix'   => $options['prefix'],
			'dumpFile' => 'site.sql',
			'driver'   => $driver
		);

		// We take advantage of the filter class magic to inject our custom filters
		$configuration = Factory::getConfiguration();

		$this->filter_data['[SITEDB]'] = $entry;

		parent::__construct();
	}
}