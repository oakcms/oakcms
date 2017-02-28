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
class SoloDatabase extends FilterBase
{
	public function __construct()
	{
		// This is a directory inclusion filter.
		$this->object = 'db';
		$this->subtype = 'inclusion';
		$this->method = 'direct';
		$this->filter_name = 'SoloDatabase';

		if (Factory::getKettenrad()->getTag() == 'restorepoint')
		{
			$this->enabled = false;
		}

		$configuration = Factory::getConfiguration();

		if ($configuration->get('akeeba.platform.addsolo', 0))
		{
			$appConfig = \Awf\Application\Application::getInstance()->getContainer()->appConfig;
			$options = array(
				'host'     => $appConfig->get('dbhost', ''),
				'user'     => $appConfig->get('dbuser', ''),
				'password' => $appConfig->get('dbpass', ''),
				'database' => $appConfig->get('dbname', ''),
				'prefix'   => $appConfig->get('prefix', ''),
			);
			$driver = '\\Akeeba\\Engine\\Driver\\' . ucfirst($appConfig->get('dbdriver', 'mysqli'));

			// Site database connection information
			$siteDbHost = $configuration->get('akeeba.platform.dbhost', '');
			$siteDbName = $configuration->get('akeeba.platform.dbname', '');

			// If Solo is not installed in the same db as the site...
			if (($options['host'] != $siteDbHost) || ($options['database'] != $siteDbName))
			{
				$host = $options['host'];
				$port = null;
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
					'dumpFile' => 'solo.sql',
					'driver'   => $driver
				);

				$this->filter_data['Solo'] = $entry;
			}
		}

		parent::__construct();
	}
}