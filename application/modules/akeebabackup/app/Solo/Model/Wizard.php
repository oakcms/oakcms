<?php
/**
 * @package        solo
 * @copyright      2014-2017 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license        GNU GPL version 3 or later
 */

namespace Solo\Model;

use Awf\Database\Driver;
use Awf\Filesystem\Hybrid;
use Awf\Mvc\Model;
use Awf\Text\Text;
use Awf\Uri\Uri;
use Solo\Pythia\Pythia;
use Akeeba\Engine\Factory;
use Akeeba\Engine\Platform;

/**
 * Configuration wizard's model class
 */
class Wizard extends Model
{
	public function guessSiteParams()
	{
		$config = Factory::getConfiguration();
		$siteURL = $config->get('akeeba.platform.site_url', '');
		$siteRoot = $config->get('akeeba.platform.newroot', '');

		if (empty($siteURL) && empty($siteRoot))
		{
			$uri = Uri::getInstance();
			$path = $uri->getPath();
			$path = dirname($path);
			$path = trim($path, '/');

			if (!empty($path))
			{
				// Get the new URL
				$pathParts = explode('/', $path);
				$path = '/';

				if (count($pathParts) > 1)
				{
					$pathParts = array_slice($pathParts, 0, -1);
					$path .= implode('/', $pathParts);
				}

				$uri->setPath($path);
				$siteURL = $uri->toString(array('scheme', 'user', 'pass', 'host', 'port', 'path'));

				// Get the new site root
				$root = str_replace('\\', '/', APATH_ROOT);
				$rootParts = explode('/', $root);
				$rootParts = array_slice($rootParts, 0, -1);

				$siteRoot = implode('/', $rootParts);
			}
		}

		return (object)array(
			'url'	=> $siteURL,
			'root'	=> $siteRoot,
		);
	}

	/**
	 * Test whether the site parameters we got are valid
	 *
	 * @param   array $siteParams The site parameters to test
	 *
	 * @return  void
	 *
	 * @throws  \RuntimeException  On invalid data
	 */
	public function testSiteParams($siteParams)
	{
		$siteRoot = $siteParams['akeeba.platform.newroot'];
		$stock_directories = Platform::getInstance()->get_stock_directories();

		foreach ($stock_directories as $tag => $content)
		{
			$siteRoot = str_replace($tag, $content, $siteRoot);
		}

		if (!@is_dir($siteRoot) || !@is_readable($siteRoot))
		{
			throw new \RuntimeException(Text::_('SOLO_WIZARD_ERR_DIRNOTEXIST'), 500);
		}

		$dbParams = array(
			'driver'   => $siteParams['akeeba.platform.dbdriver'],
			'host'     => $siteParams['akeeba.platform.dbhost'],
			'port'     => $siteParams['akeeba.platform.dbport'],
			'user'     => $siteParams['akeeba.platform.dbusername'],
			'password' => $siteParams['akeeba.platform.dbpassword'],
			'database' => $siteParams['akeeba.platform.dbname'],
			'prefix'   => $siteParams['akeeba.platform.dbprefix'],
			'select'   => 1
		);

		$class = "\\Awf\\Database\\Driver\\" . ucfirst($dbParams['driver']);

		/** @var Driver $driver */
		$driver = new $class($dbParams);
		$driver->connect();
		$driver->select($dbParams['database']);
	}

	/**
	 * Save the site parameters we got to the profile
	 *
	 * @param   array $siteParams The site parameters to save
	 *
	 * @return  void
	 *
	 * @throws  \RuntimeException  On invalid data
	 */
	public function saveSiteParams($siteParams)
	{
		$config = Factory::getConfiguration();

		$protectedKeys = $config->getProtectedKeys();
		$config->setProtectedKeys(array());

		foreach ($siteParams as $k => $v)
		{
			$config->set($k, $v);
		}

		Platform::getInstance()->save_configuration();

		$config->setProtectedKeys($protectedKeys);
	}

	/**
	 * Attempts to automatically figure out where the output directory should point, adjusting the permissions should
	 * it be necessary.
	 *
	 * @param   integer $dontRecurse Used internally. Always skip this parameter when calling this method.
	 *
	 * @return  boolean  True if we could fix the directories
	 */
	public function autofixDirectories($dontRecurse = 0)
	{
		// Get the profile ID
		$profile_id = Platform::getInstance()->get_active_profile();

		// Get the output and temporary directory
		$aeconfig = Factory::getConfiguration();
		$outdir = $aeconfig->get('akeeba.basic.output_directory', '');

		$fixTemp = false;
		$fixOut = false;

		if (is_dir($outdir))
		{
			// Test the writability of the directory
			$filename = $outdir . '/test.dat';
			$fixOut = !@file_put_contents($filename, 'test');

			if (!$fixOut)
			{
				// Directory writable, remove the temp file
				@unlink($filename);
			}
			else
			{
				// Try to chmod the directory
				$this->chmod($outdir, 511);
				// Repeat the test
				$fixOut = !@file_put_contents($filename, 'test');

				if (!$fixOut)
				{
					// Directory writable, remove the temp file
					@unlink($filename);
				}
			}
		}
		else
		{
			$fixOut = true;
		}

		// Do I have to fix the output directory?
		if ($fixOut && ($dontRecurse < 1))
		{
			$aeconfig->set('akeeba.basic.output_directory', '[DEFAULT_OUTPUT]');
			Platform::getInstance()->save_configuration($profile_id);

			// After fixing the directory, run ourselves again
			return $this->autofixDirectories(1);
		}
		elseif ($fixOut)
		{
			// If we reached this point after recursing, we can't fix the permissions
			// and the user has to RTFM and fix the issue!
			return false;
		}

		return true;
	}

	/**
	 * Creates a temporary file of a specific size
	 *
	 * @param   integer  $blocks   How many 128Kb blocks to write. Common values: 1, 2, 4, 16, 40, 80, 81
	 * @param   string   $tempdir  Path to temporary directory
	 *
	 * @return  boolean
	 */
	public function createTempFile($blocks = 1, $tempdir = null)
	{
		if (empty($tempdir))
		{
			$aeconfig = Factory::getConfiguration();
			$tempdir = $aeconfig->get('akeeba.basic.output_directory', '');
		}

		$sixtyfourBytes = '012345678901234567890123456789012345678901234567890123456789ABCD';
		$oneKilo = '';
		$oneBlock = '';

		for ($i = 0; $i < 16; $i++)
		{
			$oneKilo .= $sixtyfourBytes;
		}

		for ($i = 0; $i < 128; $i++)
		{
			$oneBlock .= $oneKilo;
		}

		$filename = tempnam($tempdir, 'confwiz') . '.jpa';
		@unlink($filename);
		$fp = @fopen($filename, 'w');

		if ($fp !== false)
		{
			for ($i = 0; $i < $blocks; $i++)
			{
				if (!@fwrite($fp, $oneBlock))
				{
					@fclose($fp);
					@unlink($filename);

					return false;
				}
			}
			@fclose($fp);
			@unlink($filename);
		}
		else
		{
			return false;
		}

		return true;
	}

	/**
	 * Sleeps for a given amount of time. Returns false if the sleep time requested is over the maximum execution time.
	 *
	 * @param   integer  $secondsDelay  Seconds to sleep
	 *
	 * @return  boolean
	 */
	public function doNothing($secondsDelay = 1)
	{
		// Try to get the maximum execution time and PHP memory limit
		if (function_exists('ini_get'))
		{
			$maxexec = ini_get("max_execution_time");
			$memlimit = ini_get("memory_limit");
		}
		else
		{
			$maxexec = 14;
			$memlimit = 16777216;
		}

		if (!is_numeric($maxexec) || ($maxexec == 0))
		{
			// Unknown time limit; suppose 10s
			$maxexec = 10;
		}

		if ($maxexec > 180)
		{
			// Some servers report silly values, i.e. 30000, which Do Not Work :(
			$maxexec = 10;
		}

		// Sometimes memlimit comes with the M or K suffixes. Parse them.
		if (is_string($memlimit))
		{
			$memlimit = strtoupper(trim(str_replace(' ', '', $memlimit)));

			if (substr($memlimit, -1) == 'K')
			{
				$memlimit = 1024 * substr($memlimit, 0, -1);
			}
			elseif (substr($memlimit, -1) == 'M')
			{
				$memlimit = 1024 * 1024 * substr($memlimit, 0, -1);
			}
			elseif (substr($memlimit, -1) == 'G')
			{
				$memlimit = 1024 * 1024 * 1024 * substr($memlimit, 0, -1);
			}
		}

		if (!is_numeric($memlimit) || ($memlimit === 0))
		{
			// Unknown limit; suppose 16M
			$memlimit = 16777216;
		}

		if ($memlimit === -1)
		{
			// No limit; suppose 128M
			$memlimit = 134217728;
		}

		// Get the current memory usage (or assume one if the metric is not available)
		if (function_exists('memory_get_usage'))
		{
			$usedram = memory_get_usage();
		}
		else
		{
			$usedram = 7340032; // Suppose 7M of RAM usage if the metric isn't available;
		}

		// If we have less than 12M of RAM left, we have to limit ourselves to 6 seconds of
		// total execution time (emperical value!) to avoid deadly memory outages
		if (($memlimit - $usedram) < 12582912)
		{
			$maxexec = 5;
		}

		// If the requested delay is over the $maxexec limit (minus one second
		// for application initialization), return false
		if ($secondsDelay > ($maxexec - 1))
		{
			return false;
		}

		// And now, run the silly loop to simulate the CPU usage pattern during backup
		$start = microtime(true);
		$loop = true;

		while ($loop)
		{
			// Waste some CPU power... Because if we just sleep() it won't count towards the CPU time that PHP is
			// actually monitoring
			for ($i = 1; $i < 1000; $i++)
			{
				$j = exp(($i * $i / 123 * 864) >> 2);
			}

			// ... then sleep for a millisec
			usleep(1000);

			// Are we done yet?
			$end = microtime(true);

			if (($end - $start) >= $secondsDelay)
			{
				$loop = false;
			}
		}

		return true;
	}

	/**
	 * This method will analyze your database tables and try to figure out the optimal
	 * batch row count value so that its select doesn't return excessive amounts of data.
	 * The only drawback is that it only accounts for the core tables, but that is usually
	 * a good metric.
	 */
	public function analyzeDatabase()
	{
		// Try to get the PHP memory limit
		if (function_exists('ini_get'))
		{
			$memlimit = ini_get("memory_limit");
		}
		else
		{
			$memlimit = 16777216;
		}

		if (!is_numeric($memlimit) || ($memlimit === 0))
		{
			$memlimit = 16777216; // Unknown limit; suppose 16M
		}

		if ($memlimit === -1)
		{
			$memlimit = 134217728; // No limit; suppose 128M
		}

		// Get the current memory usage (or assume one if the metric is not available)
		if (function_exists('memory_get_usage'))
		{
			$usedram = memory_get_usage();
		}
		else
		{
			$usedram = 7340032; // Suppose 7M of RAM usage if the metric isn't available;
		}

		// How much RAM can I spare? It's the max memory minus the current memory usage and an extra
		// 5Mb to cater for Akeeba Engine's peak memory usage
		$max_mem_usage = $usedram + 5242880;
		$ram_allowance = $memlimit - $max_mem_usage;

		// If the RAM allowance is too low, assume 2Mb (emperical value)
		if ($ram_allowance < 2097152)
		{
			$ram_allowance = 2097152;
		}

		$rowCount = 100;

		// Get the table statistics
		$db = $this->container->db;

		$dbClass = get_class($db);
		$dbTech = $dbClass::$dbtech;

		if (strtolower(substr($dbTech, 0, 5)) == 'mysql')
		{
			// The table analyzer only works with MySQL
			$db->setQuery("SHOW TABLE STATUS");

			try
			{
				$metrics = $db->loadAssocList();
			}
			catch (\Exception $exc)
			{
				$metrics = null;
			}

			if (is_null($metrics))
			{
				// SHOW TABLE STATUS is not supported. I'll assume a safe-ish value of 100 rows
				$rowCount = 100;
			}
			else
			{
				$rowCount = 1000; // Start with the default value

				if (!empty($metrics))
				{
					foreach ($metrics as $table)
					{
						// Get row count and average row length
						$rows = $table['Rows'];
						$avg_len = $table['Avg_row_length'];

						// Calculate RAM usage with current settings
						$max_rows = min($rows, $rowCount);
						$max_ram_current = $max_rows * $avg_len;

						if ($max_ram_current > $ram_allowance)
						{
							// Hm... over the allowance. Let's try to find a sweet spot.
							$max_rows = (int)($ram_allowance / $avg_len);
							// Quantize to multiple of 10 rows
							$max_rows = 10 * floor($max_rows / 10);

							// Can't really go below 10 rows / batch
							if ($max_rows < 10)
							{
								$max_rows = 10;
							}

							// If the new setting is less than the current $rowCount, use the new setting
							if ($rowCount > $max_rows)
							{
								$rowCount = $max_rows;
							}
						}
					}
				}
			}
		}

		$profile_id = Platform::getInstance()->get_active_profile();
		$config = Factory::getConfiguration();

		// Use the correct database dump engine
		if (strtolower(substr($dbTech, 0, 5)) == 'mysql')
		{
			$config->set('akeeba.advanced.dump_engine', 'native');
		}
		else
		{
			$config->set('akeeba.advanced.dump_engine', 'reverse');
		}

		// Save the row count per batch
		$config->set('engine.dump.common.batchsize', $rowCount);

		// Enable SQL file splitting - default is 512K unless the part_size is less than that!
		$splitsize = 524288;
		$partsize = $config->get('engine.archiver.common.part_size', 0);

		if (($partsize < $splitsize) && !empty($partsize))
		{
			$splitsize = $partsize;
		}

		$config->set('engine.dump.common.splitsize', $splitsize);

		// Enable extended INSERTs
		$config->set('engine.dump.common.extended_inserts', '1');

		// Determine optimal packet size (must be at most two fifths of the split size and no more than 256K)
		$packet_size = (int)$splitsize * 0.4;

		if ($packet_size > 262144)
		{
			$packet_size = 262144;
		}

		$config->set('engine.dump.common.packet_size', $packet_size);

		Platform::getInstance()->save_configuration($profile_id);
	}

	/**
	 * Changes the permissions of a file or directory using the hybrid filesystem layer
	 *
	 * @param   string   $path  Absolute path to the file/dir to chmod
	 * @param   integer  $mode  The permissions mode to apply
	 *
	 * @return  boolean True on success
	 */
	private function chmod($path, $mode)
	{
		$fs = $this->container->fileSystem;

		try
		{
			return $fs->chmod($path, $mode);
		}
		catch (\Exception $e)
		{
			return false;
		}
	}

	/**
	 * Run a specific step via AJAX
	 *
	 * @return  boolean
	 */
	public function runAjax()
	{
		$act = $this->getState('act');

		if (method_exists($this, $act))
		{
			$result = $this->$act();
		}
		else
		{
			$result = false;
		}

		return $result;
	}

	/**
	 * Just returns true. Used to ping the engine.
	 *
	 * @return  boolean
	 */
	private function ping()
	{
		$profile_id = Platform::getInstance()->get_active_profile();
		$config = Factory::getConfiguration();

		// If this is Akeeba Backup for WordPress set the embedded installer to ANGIE for WordPress
		if (defined('WPINC'))
		{
			$config->set('akeeba.advanced.embedded_installer', 'angie-wordpress');
		}

		// Mark this profile as already configured
		$config->set('akeeba.flag.confwiz', 1);

		Platform::getInstance()->save_configuration($profile_id);

		return true;
	}

	/**
	 * Try different values of minimum execution time
	 */
	private function minexec()
	{
		$seconds = $this->input->get('seconds', '0.5', 'float');

		if ($seconds < 1)
		{
			usleep($seconds * 1000000);
		}
		else
		{
			sleep($seconds);
		}

		return true;
	}

	/**
	 * Saves the AJAX preference and the minimum execution time
	 *
	 * @return  boolean
	 */
	private function applyminexec()
	{
		// Get the user parameters
		$iframes = $this->input->get('iframes', 0, 'int');
		$minexec = $this->input->get('minecxec', 2.0, 'float');

		// Save the settings
		$profile_id = Platform::getInstance()->get_active_profile();
		$config = Factory::getConfiguration();
		$config->set('akeeba.basic.useiframe', $iframes);
		$config->set('akeeba.tuning.min_exec_time', $minexec * 1000);
		Platform::getInstance()->save_configuration($profile_id);

		// Enforce the min exec time
		$timer = Factory::getTimer();
		$timer->enforce_min_exec_time(false);

		// Done!
		return true;
	}

	/**
	 * Try to make the directories writable or provide a set of writable directories
	 *
	 * @return  boolean
	 */
	private function directories()
	{
		$timer = Factory::getTimer();
		$result = $this->autofixDirectories();
		$timer->enforce_min_exec_time(false);

		return $result;
	}

	/**
	 * Analyze the database and apply optimized database dump settings
	 *
	 * @return  boolean
	 */
	private function database()
	{
		$timer = Factory::getTimer();
		$this->analyzeDatabase();
		$timer->enforce_min_exec_time(false);

		return true;
	}

	/**
	 * Try to apply a specific maximum execution time setting
	 *
	 * @return  boolean
	 */
	private function maxexec()
	{
		$seconds = $this->input->get('seconds', 30, 'int');
		$timer = Factory::getTimer();
		$result = $this->doNothing($seconds);
		$timer->enforce_min_exec_time(false);

		return $result;
	}

	/**
	 * Save a specific maximum execution time preference to the database
	 *
	 * @return  boolean
	 */
	private function applymaxexec()
	{
		// Get the user parameters
		$maxexec = $this->input->get('seconds', 2, 'int');

		// Save the settings
		$timer = Factory::getTimer();
		$profile_id = Platform::getInstance()->get_active_profile();
		$config = Factory::getConfiguration();
		$config->set('akeeba.tuning.max_exec_time', $maxexec);
		$config->set('akeeba.tuning.run_time_bias', '75');
		$config->set('akeeba.advanced.scan_engine', 'smart');
		// @todo This should be an option (choose format, zip/jpa)
		$config->set('akeeba.advanced.archiver_engine', 'jpa');
		Platform::getInstance()->save_configuration($profile_id);

		// Enforce the min exec time
		$timer->enforce_min_exec_time(false);

		// Done!
		return true;
	}

	/**
	 * Creates a dummy file of a given size. Remember to give the filesize
	 * query parameter in bytes!
	 *
	 * @return  integer  Part size in bytes
	 */
	public function partsize()
	{
		$timer = Factory::getTimer();
		$blocks = $this->input->get('blocks', 1, 'int');

		$result = $this->createTempFile($blocks);

		if ($result)
		{
			// Save the setting
			if ($blocks > 200)
			{
				// Over 25Mb = 2Gb minus 128Kb limit (safe setting for PHP not running on 64-bit Linux)
				$blocks = 16383;
			}

			$profile_id = Platform::getInstance()->get_active_profile();
			$config = Factory::getConfiguration();
			$config->set('engine.archiver.common.part_size', $blocks * 128 * 1024);
			Platform::getInstance()->save_configuration($profile_id);
		}

		// Enforce the min exec time
		$timer->enforce_min_exec_time(false);

		return $result;
	}

	/**
	 * Guess the script / CMS type installed in $folder and return information relevant to it
	 *
	 * @return  array
	 */
	public function pythia()
	{
		$folder = $this->input->get('folder', '', 'raw');

		// Translate the folder
		/** @var Platform\Solo $platform */
		$platform = Platform::getInstance();
		$platformDirs = $platform->get_stock_directories();

		foreach ($platformDirs as $key => $value)
		{
			$folder = str_replace($key, $value, $folder);
		}

		// Call the oracle
		$pythia = new Pythia($this->container->application);
		return $pythia->getCmsInfo($folder);
	}
} 