<?php
/**
 * @package		akeebabackupwp
 * @copyright	2014-2017 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license		GNU GPL version 3 or later
 */

/**
 * Make sure we are being called from WordPress itself
 */
defined('WPINC') or die;

class akeeba_solo_wpinstall
{
	protected $sqlPath = '';

	public function __construct()
	{
		$this->sqlPath = __DIR__ . '/../app/Solo/assets/sql/install/mysql';
	}

	/**
	 * Installs the database tables
	 */
	public function installTables()
	{
		/** @var wpdb $wpdb */
		global $wpdb;

		// Parse the SQL file
		$sqlDefs = $this->parseSql('install.sql');

		// Require the upgrade code which includes the dbDelta function
		require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );

		// Have I created any tables yet?
		$haveCreatedTables = false;

		foreach ($sqlDefs as $def)
		{
			$table = str_replace('#__', $wpdb->prefix, $def['table']);
			$sql = str_replace('#__', $wpdb->prefix, $def['sql']);

			if ($def['type'] == 'create')
			{
				// we use dbDelta to install new tables
				$forUpdate = dbDelta($sql);
				$haveCreatedTables = !empty($forUpdate);
			}
			elseif ($def['type'] == 'drop')
			{
				// we always run DROP statements
				$wpdb->query($sql);
			}
			else
			{
				// Everything else only runs if we are installing new tables
				if ($haveCreatedTables)
				{
					$wpdb->query($sql);
				}
			}
		}
	}

	/**
	 * Uninstalls the database tables
	 */
	public function uninstallTables()
	{
		/** @var wpdb $wpdb */
		global $wpdb;

		// Parse the SQL file
		$sqlDefs = $this->parseSql('install.sql');

		foreach ($sqlDefs as $def)
		{
			$table = str_replace('#__', $wpdb->prefix, $def['table']);

			// We only need to process CREATE commands to remove tables
			if ($def['type'] == 'create')
			{
				$sql = 'DROP TABLE IF EXISTS `' . $table . '`';
				$wpdb->query($sql);
			}
		}
	}

	/**
	 * Parses a SQL file to a hash array telling us the "type" of the SQL command, the "sql" command itself and
	 * the "table" it operates against.
	 *
	 * @param string $filename The filename to parse, absolute or relative to the $sqlPath
	 *
	 * @return array
	 */
	public function parseSql($filename)
	{
		// Initialise
		$array = array();

		// Make sure we can read from the installation SQL file
		if (!@file_exists($filename))
		{
			$filename = $this->sqlPath . '/' . $filename;
		}

		if (!@file_exists($filename))
		{
			return $array;
		}

		// Get the lines in the file
		$lines = file($filename);
		$chunk = '';

		foreach ($lines as $line)
		{
			$line = trim($line);

			if (!empty($line))
			{
				$chunk .= $line . "\n";
			}
			else
			{
				$sqlDef = array(
					'type'	=> 'none',
					'sql'	=> $chunk,
					'table'	=> ''
				);

				if (substr($chunk, 0, 7) == 'CREATE ')
				{
					$sqlDef['type'] = 'create';
				}
				elseif (substr($chunk, 0, 5) == 'DROP ')
				{
					$sqlDef['type'] = 'drop';
				}
				elseif (substr($chunk, 0, 7) == 'INSERT ')
				{
					$sqlDef['type'] = 'insert';
				}
				elseif (substr($chunk, 0, 7) == 'UPDATE ')
				{
					$sqlDef['type'] = 'update';
				}
				elseif (substr($chunk, 0, 8) == 'REPLACE ')
				{
					$sqlDef['type'] = 'replace';
				}
				else
				{
					$chunk = '';
					continue;
				}

				list($lineOne, $theRest) = explode("\n", $chunk, 2);
				$mark = strpos('`', $lineOne);
				$lineOne = substr($lineOne, $mark + 1);
				$mark = strpos('`', $lineOne);
				$sqlDef['table'] = substr($lineOne, 0, $mark - 1);

				$array[] = $sqlDef;

				$chunk = '';
			}
		}

		return $array;
	}
}