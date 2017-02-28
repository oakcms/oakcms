<?php
/**
 * @package        solo
 * @copyright      2014-2017 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license        GNU GPL version 3 or later
 */

namespace Solo\Model;

use Awf\Application\Application;
use Awf\Container\Container;
use Awf\Database\Driver;
use Awf\Date\Date;
use Awf\Download\Download;
use Awf\Filesystem\Factory;
use Awf\Input\Input;
use Awf\Mvc\Model;
use Awf\Registry\Registry;
use Awf\Session\Exception;
use Awf\Text\Text;
use Awf\Uri\Uri;
use Awf\Session\Randval;

class Update extends Model
{
	/** @var   string  The URL containing the INI update stream URL */
	protected $updateStreamURL = '';

	/** @var   Registry  A registry object holding the update information */
	protected $updateInfo = null;

	/** @var   string  The table where key-valueinformation is stored */
	protected $tableName = '';

	/** @var   string  The table field which stored the key of the key-value pairs */
	protected $keyField = 'key';

	/** @var   string  The table field which stored the value of the key-value pairs */
	protected $valueField = 'value';

	/** @var   string  The key tag for the live update serialised information */
	protected $updateInfoTag = 'liveupdate';

	/** @var   string  The key tag for the last check timestamp */
	protected $lastCheckTag = 'liveupdate_lastcheck';

	/** @var   integer  The last update check UNIX timestamp */
	protected $lastCheck = null;

	/** @var   string   Currently installed version */
	protected $currentVersion = '';

	/** @var   string   Currently installed version's date stamp */
	protected $currentDateStamp = '';

	/** @var   string   Minimum stability for reporting updates */
	protected $minStability = 'alpha';

	protected $downloadId = '';

	/**
	 * How to determine if a new version is available. 'different' = if the version number is different,
	 * the remote version is newer, 'vcompare' = use version compare between the two versions, 'newest' =
	 * compare the release dates to find the newest. I suggest using 'different' on most cases.
	 *
	 * @var   string
	 */
	protected $versionStrategy = 'different';

	public function __construct(\Awf\Container\Container $container = null)
	{
		parent::__construct($container);

		$this->currentVersion = defined('AKEEBABACKUP_VERSION') ? AKEEBABACKUP_VERSION : 'dev';
		$this->currentDateStamp = defined('AKEEBABACKUP_DATE') ? AKEEBABACKUP_DATE : gmdate('Y-m-d');
		$this->minStability = $this->container->appConfig->get('options.minstability', 'stable');
		$this->downloadId = $this->container->appConfig->get('options.update_dlid', '');

		// Set the update stream URL
		if (isset($container['updateStreamURL']))
		{
			$this->updateStreamURL = $container->updateStreamURL;
		}
		else
		{
			$pro = AKEEBABACKUP_PRO ? 'pro' : 'core';

			$this->updateStreamURL = 'http://cdn.akeebabackup.com/updates/solo' . $pro . '.ini';
		}

		$this->tableName = '#__ak_storage';
		$this->keyField = 'tag';
		$this->valueField = 'data';
		$this->versionStrategy = 'newest';

		$this->load(false);
	}

	public function load($force = false)
	{
		// Clear the update information and last update check timestamp
		$this->lastCheck = null;
		$this->updateInfo = null;

		// Get a reference to the database
		$db = $this->container->db;

		// Get the last update timestamp
		$query = $db->getQuery(true)
			->select($db->qn($this->valueField))
			->from($db->qn($this->tableName))
			->where($db->qn($this->keyField) . '=' . $db->q($this->lastCheckTag));
		$this->lastCheck = $db->setQuery($query)->loadResult();

		if (is_null($this->lastCheck))
		{
			$this->lastCheck = 0;
		}

		// Do I have to forcible reload from a URL?
		if (!$force)
		{
			// Force reload if more than 6 hours have elapsed
			if (abs(time() - $this->lastCheck) >= 21600)
			{
				$force = true;
			}
		}

		// Try to load from cache
		if (!$force)
		{
			$query = $db->getQuery(true)
				->select($db->qn($this->valueField))
				->from($db->qn($this->tableName))
				->where($db->qn($this->keyField) . '=' . $db->q($this->updateInfoTag));
			$rawInfo = $db->setQuery($query)->loadResult();

			if (empty($rawInfo))
			{
				$force = true;
			}
			else
			{
				$this->updateInfo = new Registry();
				$this->updateInfo->loadString($rawInfo, 'JSON');
			}
		}

		// If it's stuck and we are not forcibly retrying to reload, bail out
		if (!$force && !empty($this->updateInfo) && $this->updateInfo->get('stuck', false))
		{
			return;
		}

		// Maybe we are forced to load from a URL?
		// NOTE: DO NOT MERGE WITH PREVIOUS IF AS THE $force VARIABLE MAY BE MODIFIED THERE!
		if ($force)
		{
			$this->updateInfo = new Registry();
			$this->updateInfo->set('stuck', 1);
			$this->lastCheck = time();

			// Store last update check timestamp
			$o = (object)array(
				$this->keyField   => $this->lastCheckTag,
				$this->valueField => $this->lastCheck
			);

			$result = false;

			try
			{
				$result = $db->insertObject($this->tableName, $o, $this->keyField);
			}
			catch (\Exception $e)
			{
				$result = false;
			}

			if (!$result)
			{
				try
				{
					$result = $db->updateObject($this->tableName, $o, $this->keyField);
				}
				catch (\Exception $e)
				{
					$result = false;
				}
			}

			// Store update information
			$o = (object)array(
				$this->keyField   => $this->updateInfoTag,
				$this->valueField => $this->updateInfo->toString('JSON')
			);

			$result = false;

			try
			{
				$result = $db->insertObject($this->tableName, $o, $this->keyField);
			}
			catch (\Exception $e)
			{
				$result = false;
			}

			if (!$result)
			{
				try
				{
					$result = $db->updateObject($this->tableName, $o, $this->keyField);
				}
				catch (\Exception $e)
				{
					$result = false;
				}
			}

			// Try to fetch from URL
			try
			{
				$download = new Download($this->container);
				$rawInfo = $download->getFromURL($this->updateStreamURL);
				$this->updateInfo->loadString($rawInfo, 'INI');
				$this->updateInfo->set('stuck', 0);
			}
			catch (\Exception $e)
			{
				// We are stuck. Darn.

				return;
			}

			// Since we had to load from a URL, commit the update information to db
			$o = (object)array(
				$this->keyField   => $this->updateInfoTag,
				$this->valueField => $this->updateInfo->toString('JSON')
			);

			$result = false;

			try
			{
				$result = $db->insertObject($this->tableName, $o, $this->keyField);
			}
			catch (\Exception $e)
			{
				$result = false;
			}

			if (!$result)
			{
				try
				{
					$result = $db->updateObject($this->tableName, $o, $this->keyField);
				}
				catch (\Exception $e)
				{
					$result = false;
				}
			}
		}

		// Check if an update is available and push it to the update information registry
		$this->updateInfo->set('hasUpdate', $this->hasUpdate());

		// Post-process the download URL, appending the Download ID (if defined)
		$link = $this->updateInfo->get('link', '');

		if (!empty($link) && !empty($this->downloadId))
		{
			$link = new Uri($link);
			$link->setVar('dlid', $this->downloadId);
			$this->updateInfo->set('link', $link->toString());
		}
	}

	/**
	 * Is there an update available?
	 *
	 * @return  boolean
	 */
	public function hasUpdate()
	{
		$this->updateInfo->set('minstabilityMatch', 1);
		$this->updateInfo->set('platformMatch', 0);

		// Validate the minimum stability
		$stability = strtolower($this->updateInfo->get('stability'));

		switch ($this->minStability)
		{
			case 'alpha':
			default:
				// Reports any stability level as an available update
				break;

			case 'beta':
				// Do not report alphas as available updates
				if (in_array($stability, array('alpha')))
				{
					$this->updateInfo->set('minstabilityMatch', 0);

					return false;
				}
				break;

			case 'rc':
				// Do not report alphas and betas as available updates
				if (in_array($stability, array('alpha', 'beta')))
				{
					$this->updateInfo->set('minstabilityMatch', 0);

					return false;
				}
				break;

			case 'stable':
				// Do not report alphas, betas and rcs as available updates
				if (in_array($stability, array('alpha', 'beta', 'rc')))
				{
					$this->updateInfo->set('minstabilityMatch', 0);

					return false;
				}
				break;
		}

		// Validate the platform compatibility
		$platforms = explode(',', $this->updateInfo->get('platforms', ''));

		if (!empty($platforms))
		{
			$phpVersionParts = explode('.', PHP_VERSION, 3);
			$currentPHPVersion = $phpVersionParts[0] . '.' . $phpVersionParts[1];

			$platformFound = false;

			$requirePlatformName = $this->container->segment->get('platformNameForUpdates', 'php');
			$currentPlatform = $this->container->segment->get('platformVersionForUpdates', $currentPHPVersion);

			// Check for the platform
			foreach ($platforms as $platform)
			{
				$platform = trim($platform);
				$platform = strtolower($platform);
				$platformParts = explode('/', $platform, 2);

				if ($platformParts[0] != $requirePlatformName)
				{
					continue;
				}

				if ((substr($platformParts[1], -1) == '+') && version_compare($currentPlatform, substr($platformParts[1], 0, -1), 'ge'))
				{
					$this->updateInfo->set('platformMatch', 1);
					$platformFound = true;
				}
				elseif ($platformParts[1] == $currentPlatform)
				{
					$this->updateInfo->set('platformMatch', 1);
					$platformFound = true;
				}
			}

			// If we are running inside a CMS perform a second check for the PHP version
			if ($platformFound && ($requirePlatformName != 'php'))
			{
				$this->updateInfo->set('platformMatch', 0);
				$platformFound = false;

				foreach ($platforms as $platform)
				{
					$platform = trim($platform);
					$platform = strtolower($platform);
					$platformParts = explode('/', $platform, 2);

					if ($platformParts[0] != 'php')
					{
						continue;
					}

					if ($platformParts[1] == $currentPHPVersion)
					{
						$this->updateInfo->set('platformMatch', 1);
						$platformFound = true;
					}
				}
			}

			if (!$platformFound)
			{
				return false;
			}
		}

		// If the user had the Core version but has entered a Download ID we will always display an update as being
		// available
		if (!AKEEBABACKUP_PRO && !empty($this->downloadId))
		{
			return true;
		}

		// Apply the version strategy
		$version = $this->updateInfo->get('version', null);
		$date = $this->updateInfo->get('date', null);

		if (empty($version) || empty($date))
		{
			return false;
		}

		switch ($this->versionStrategy)
		{
			case 'newest':
				if (empty($this->currentDateStamp))
				{
					$mine = new Date('2000-01-01 00:00:00');
				}
				else
				{
					try
					{
						$mine = new Date($this->currentDateStamp);
					}
					catch (\Exception $e)
					{
						$mine = new Date('2000-01-01 00:00:00');
					}
				}

				$theirs = new Date($date);

				// Do we have the same time? This happens when we release two versions in the same day
				// In such cases we have to check vs the version number
				if($mine->toUnix() == $theirs->toUnix())
				{
					$mine = $this->currentVersion;

					if (empty($mine))
					{
						$mine = '0.0.0';
					}

					if (empty($version))
					{
						$version = '0.0.0';
					}

					return (version_compare($version, $mine, 'gt'));
				}

				return ($theirs->toUnix() > $mine->toUnix());

				break;

			case 'vcompare':
				$mine = $this->currentVersion;

				if (empty($mine))
				{
					$mine = '0.0.0';
				}

				if (empty($version))
				{
					$version = '0.0.0';
				}

				return (version_compare($version, $mine, 'gt'));

				break;

			case 'different':
				$mine = $this->currentVersion;

				if (empty($mine))
				{
					$mine = '0.0.0';
				}

				if (empty($version))
				{
					$version = '0.0.0';
				}

				return ($version != $mine);

				break;
		}

		return false;
	}

    /**
     * Returns the update information
     *
     * @param bool $force   Should we force the fetch of new information?
     *
     * @return \Awf\Registry\Registry
     */
	public function getUpdateInformation($force = false)
	{
		if (is_null($this->updateInfo))
		{
			$this->load($force);
		}

		return $this->updateInfo;
	}

	/**
	 * Try to prepare a world-writeable update.zip file in the temporary directory,
	 * or throw an exception if it's not possible
	 *
	 * @return  void
	 *
	 * @throws  \Exception
	 */
	public function prepareDownload()
	{
		$tmpDir = APATH_BASE . '/tmp';
		$tmpFile = $tmpDir . '/update.zip';

		$fs = $this->container->fileSystem;

		if (!is_dir($tmpDir))
		{
			throw new \Exception(Text::sprintf('SOLO_UPDATE_ERR_DOWNLOAD_INVALIDTMPDIR', $tmpDir), 500);
		}

		$fs->delete($tmpFile);

		$fp = @fopen($tmpFile, 'wb');

		if ($fp === false)
		{
			$nada = '';
			$fs->write($tmpFile, $nada);
		}
		else
		{
			@fclose($fp);
		}

		$fs->chmod($tmpFile, 0777);
	}

	/**
	 * Step through the download of the update archive
	 *
	 * @param   boolean $staggered Should I try a staggered (multi-step) download? Default is true.
	 *
	 * @return  array  A return array giving the status of the staggered download
	 */

	public function stepDownload($staggered = true)
	{
		$this->load();

        // The restore script expects to find the update inside the temp directory
        $tmpDir = $this->container['temporaryPath'];
        $tmpDir = rtrim($tmpDir, '/\\');
        $localFilename = $tmpDir . '/update.zip';

		$params = array(
			'file'          => $this->updateInfo->get('link', ''),
			'frag'          => $this->getState('frag', -1),
			'totalSize'     => $this->getState('totalSize', -1),
			'doneSize'      => $this->getState('doneSize', -1),
			'localFilename' => $localFilename,
		);

		$download = new Download($this->container);

		if ($staggered)
		{
            // importFromURL expects the remote URL in the 'url' index
            $params['url'] = $params['file'];
            $retArray = $download->importFromURL($params);

            // Better it
            unset($params['url']);
		}
		else
		{
			$retArray = array(
				"status"    => true,
				"error"     => '',
				"frag"      => 1,
				"totalSize" => 0,
				"doneSize"  => 0,
				"percent"   => 0,
				"errorCode"	=> 0,
			);

			try
			{
				$result = $download->getFromURL($params['file']);

				if ($result === false)
				{
					throw new Exception(Text::sprintf('AWF_DOWNLOAD_ERR_LIB_COULDNOTDOWNLOADFROMURL', $params['file']), 500);
				}

				$tmpDir = APATH_ROOT . '/tmp';
				$tmpDir = rtrim($tmpDir, '/\\');
				$localFilename = $tmpDir . '/update.zip';

				$fs = $this->container->fileSystem;

				$fs->write($localFilename, $result);

				$retArray['status'] = true;
				$retArray['totalSize'] = strlen($result);
				$retArray['doneSize'] = $retArray['totalSize'];
				$retArray['percent'] = 100;
			}
			catch (\Exception $e)
			{
				$retArray['status'] = false;
				$retArray['error'] = $e->getMessage();
				$retArray['errorCode'] = $e->getCode();
			}
		}

		return $retArray;
	}

	/**
	 * Creates the restoration.ini file which is used during the update
	 * package's extraction. This file tells Akeeba Restore which package to
	 * read and where and how to extract it.
	 *
	 * @return  boolean  True on success
	 */
	public function createRestorationINI()
	{
		// Get a password
		$randVal = new Randval(new \Awf\Utils\Phpfunc());
		$password = base64_encode($randVal->generate(32));

		$fs = $this->container->fileSystem;

		$this->setState('update_password', $password);

		// Also save the update_password in the session, we'll need it if this page is reloaded
		$this->container->segment->set('update_password', $password);

		// Get the absolute path to site's root
		$siteRoot = (isset($this->container['filesystemBase'])) ? $this->container['filesystemBase'] : APATH_BASE;
		$siteRoot = str_replace('\\', '/', $siteRoot);
		$siteRoot = str_replace('//', '/', $siteRoot);

		// On WordPress we need to go one level up
		if (defined('WPINC'))
		{
			$parts = explode('/', $siteRoot);
			array_pop($parts);
			$siteRoot = implode('/', $parts);
		}

        $tempdir = $this->container['temporaryPath'];

		$data  = "<?php\ndefined('_AKEEBA_RESTORATION') or die();\n";
		$data .= '$restoration_setup = array(' . "\n";

		$ftpOptions = $this->getFTPOptions();
		$engine = $ftpOptions['enable'] ? 'hybrid' : 'direct';

		$data .= <<<ENDDATA
	'kickstart.security.password' => '$password',
	'kickstart.tuning.max_exec_time' => '5',
	'kickstart.tuning.run_time_bias' => '75',
	'kickstart.tuning.min_exec_time' => '0',
	'kickstart.procengine' => '$engine',
	'kickstart.setup.sourcefile' => '{$tempdir}/update.zip',
	'kickstart.setup.destdir' => '$siteRoot',
	'kickstart.setup.restoreperms' => '0',
	'kickstart.setup.filetype' => 'zip',
	'kickstart.setup.dryrun' => '0',
ENDDATA;

		// On WordPress we need to remove the akeebabackupwp prefix from the package
		if (defined('WPINC'))
		{
			$data .= "\n\t'kickstart.setup.removepath' => 'akeebabackupwp',\n";
		}

		if ($ftpOptions['enable'])
		{
			// Is the tempdir really writable?
			$writable = @is_writeable($tempdir);

			if ($writable)
			{
				// Let's be REALLY sure
				$fp = @fopen($tempdir . '/test.txt', 'w');
				if ($fp === false)
				{
					$writable = false;
				}
				else
				{
					fclose($fp);
					unlink($tempdir . '/test.txt');
				}
			}

			// If the tempdir is not writable, create a new writable subdirectory
			if (!$writable)
			{
				$newTemp = APATH_BASE . '/tmp/update_tmp';
				$fs->mkdir($newTemp, 0777);

				$tempdir = $newTemp;
			}

			// If we still have no writable directory, we'll try /tmp and the system's temp-directory
			$writable = @is_writeable($tempdir);

			if (!$writable && function_exists('sys_get_temp_dir'))
			{
				$tempdir = sys_get_temp_dir();
			}

			$data .= <<<ENDDATA
	'kickstart.ftp.ssl' => '0',
	'kickstart.ftp.passive' => '1',
	'kickstart.ftp.host' => '{$ftpOptions['host']}',
	'kickstart.ftp.port' => '{$ftpOptions['port']}',
	'kickstart.ftp.user' => '{$ftpOptions['user']}',
	'kickstart.ftp.pass' => '{$ftpOptions['pass']}',
	'kickstart.ftp.dir' => '{$ftpOptions['root']}',
	'kickstart.ftp.tempdir' => '$tempdir',
ENDDATA;
		}

		$data .= ');';


		$configPath = $siteRoot . '/restoration.php';

		if (defined('WPINC'))
		{
			$configPath = $siteRoot . '/app/restoration.php';
		}

		clearstatcache(true, $configPath);

		// Remove the old file, if it's there...
		if (file_exists($configPath))
		{
			$fs->delete($configPath);
		}

		// Write the new file
		$fs->write($configPath, $data);

		// Clear opcode caches for the generated .php file
		if (function_exists('opcache_invalidate'))
		{
			opcache_invalidate($configPath);
		}

		if (function_exists('apc_compile_file'))
		{
			apc_compile_file($configPath);
		}

		if (function_exists('wincache_refresh_if_changed'))
		{
			wincache_refresh_if_changed(array($configPath));
		}
		
		if (function_exists('xcache_asm'))
		{
			xcache_asm($configPath);
		}

		return true;
	}

	/**
	 * Returns an array with the configured FTP options
	 *
	 * @return  array
	 */
	public function getFTPOptions()
	{
		// Initialise from Joomla! Global Configuration
		$config = $this->container->appConfig;

		$retArray = array(
			'enable'  => $config->get('fs.driver', 'file') == 'ftp',
			'host'    => $config->get('fs.host', 'localhost'),
			'port'    => $config->get('fs.port', '21'),
			'user'    => $config->get('fs.username', ''),
			'pass'    => $config->get('fs.password', ''),
			'root'    => $config->get('fs.directory', ''),
			'tempdir' => APATH_BASE . '/tmp',
		);

		return $retArray;
	}

	/**
	 * Finalises the update
	 */
	public function finalise()
	{
		// @todo Reserved for future use
	}
}