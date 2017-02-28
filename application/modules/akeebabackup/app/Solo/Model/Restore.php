<?php
/**
 * @package        solo
 * @copyright      2014-2017 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license        GNU GPL version 3 or later
 */

namespace Solo\Model;

use Akeeba\Engine\Archiver\Directftp;
use Awf\Html\Select;
use Awf\Mvc\Model;
use Awf\Session\Randval;
use Awf\Text\Text;
use Solo\Helper\Escape;
use Akeeba\Engine\Factory;
use Akeeba\Engine\Platform;

class Restore extends Model
{
	/** @var   array  The backup record being restored */
	private $data = array();

	/** @var   string  The extension of the archive being restored */
	private $extension = null;

	/** @var   string  Absolute path to the archive being restored */
	private $path = null;

	/** @var   string  Random password, used to secure the restoration */
	public $password;

	/**
	 * Validates the data passed to the request.
	 *
	 * @return  void
	 *
	 * @throws  \RuntimeException
	 */
	function validateRequest()
	{
		// Is this a valid backup entry?
		$id = $this->getState('id', 0);
		$profile_id = $this->getState('profileid', 0);

		if (empty($id) && ($profile_id <= 0))
		{
			throw new \RuntimeException(Text::_('COM_AKEEBA_RESTORE_ERROR_INVALID_RECORD'), 500);
		}

		if (empty($id))
		{
			$id = $this->getLatestBackupForProfile($profile_id);
		}

		$data = Platform::getInstance()->get_statistics($id);

		if (empty($data))
		{
			throw new \RuntimeException(Text::_('COM_AKEEBA_RESTORE_ERROR_INVALID_RECORD'), 500);
		}

		if ($data['status'] != 'complete')
		{
			throw new \RuntimeException(Text::_('COM_AKEEBA_RESTORE_ERROR_INVALID_RECORD'), 500);
		}

		// Load the profile ID (so that we can find out the output directory)
		$profile_id = $data['profile_id'];
		Platform::getInstance()->load_configuration($profile_id);

		$path = $data['absolute_path'];
		$exists = @file_exists($path);

		if (!$exists)
		{
			// Let's try figuring out an alternative path
			$config = Factory::getConfiguration();
			$path = $config->get('akeeba.basic.output_directory', '') . '/' . $data['archivename'];
			$exists = @file_exists($path);
		}

		if (!$exists)
		{
			throw new \RuntimeException(Text::_('COM_AKEEBA_RESTORE_ERROR_ARCHIVE_MISSING'), 500);
		}

		$filename = basename($path);
		$lastDot = strrpos($filename, '.');
		$extension = strtoupper(substr($filename, $lastDot + 1));

		if (!in_array($extension, array('JPA', 'ZIP')))
		{
			throw new \RuntimeException(Text::_('COM_AKEEBA_RESTORE_ERROR_INVALID_TYPE'), 500);
		}

		$this->data = $data;
		$this->path = $path;
		$this->extension = $extension;

		$this->setState('extension', $extension);
	}

	/**
	 * Finds the latest backup for a given backup profile with an "OK" status (the archive file exists on your server).
	 * If none is found a RuntimeException is thrown.
	 *
	 * This method uses the code from the Transfers model for DRY reasons.
	 *
	 * @param   int  $profileID  The profile in which to locate the latest valid backup
	 *
	 * @return  int
	 *
	 * @throws  \RuntimeException
	 *
	 * @since   5.3.0
	 */
	public function getLatestBackupForProfile($profileID)
	{
		/** @var Transfers $transferModel */
		$transferModel = new Transfers($this->container);
		$latestBackup  = $transferModel->getLatestBackupInformation($profileID);

		if (empty($latestBackup))
		{
			throw new \RuntimeException(Text::sprintf('COM_AKEEBA_RESTORE_ERROR_NO_LATEST', $profileID));
		}

		return $latestBackup['id'];
	}

	/**
	 * Creates the restoration.php file with the restoration engine parameters
	 *
	 * @return  void
	 *
	 * @throws  \RuntimeException
	 */
	function createRestorationFile()
	{
		// Get a password
		$randVal = new Randval(new \Awf\Utils\Phpfunc());
		$this->password = base64_encode($randVal->generate(32));
		$this->setState('password', $this->password);

		// Do we have to use FTP?
		$procEngine = $this->getState('procengine', 'direct');

		// Get the absolute path to site's root
		$configuration = Factory::getConfiguration();
		if ($configuration->get('akeeba.platform.override_root', 0))
		{
			$siteRoot = $configuration->get('akeeba.platform.newroot', '[SITEROOT]');
		}
		else
		{
			$siteRoot = '[SITEROOT]';
		}

		if (stristr($siteRoot, '['))
		{
			$siteRoot = Factory::getFilesystemTools()->translateStockDirs($siteRoot);
		}

		if (empty($siteRoot))
		{
			throw new \RuntimeException(Text::_('SOLO_RESTORE_ERR_NOSITEROOT'), 500);
		}

		// Get the JPS password
		$password = Escape::escapeJS($this->getState('jps_key'));

		$data = "<?php\ndefined('_AKEEBA_RESTORATION') or die();\n";
		$data .= '$restoration_setup = array(' . "\n";
		$data .= <<<ENDDATA
	'kickstart.security.password' => '{$this->password}',
	'kickstart.tuning.max_exec_time' => '5',
	'kickstart.tuning.run_time_bias' => '75',
	'kickstart.tuning.min_exec_time' => '0',
	'kickstart.procengine' => '$procEngine',
	'kickstart.setup.sourcefile' => '{$this->path}',
	'kickstart.setup.destdir' => '$siteRoot',
	'kickstart.setup.restoreperms' => '0',
	'kickstart.setup.filetype' => '{$this->extension}',
	'kickstart.setup.dryrun' => '0',
	'kickstart.jps.password' => '$password'
ENDDATA;

		if ($procEngine == 'ftp')
		{
			$ftp_host = $this->getState('ftp_host', '');
			$ftp_port = $this->getState('ftp_port', '21');
			$ftp_user = $this->getState('ftp_user', '');
			$ftp_pass = addcslashes($this->getState('ftp_pass', ''), "'\\");
			$ftp_root = $this->getState('ftp_root', '');
			$ftp_ssl = $this->getState('ftp_ssl', 0);
			$ftp_pasv = $this->getState('ftp_root', 1);
			$tempdir = $this->getState('tmp_path', '');
			$data .= <<<ENDDATA
	,
	'kickstart.ftp.ssl' => '$ftp_ssl',
	'kickstart.ftp.passive' => '$ftp_pasv',
	'kickstart.ftp.host' => '$ftp_host',
	'kickstart.ftp.port' => '$ftp_port',
	'kickstart.ftp.user' => '$ftp_user',
	'kickstart.ftp.pass' => '$ftp_pass',
	'kickstart.ftp.dir' => '$ftp_root',
	'kickstart.ftp.tempdir' => '$tempdir'
ENDDATA;
		}

		$data .= ');';

		// Remove the old file, if it's there...
		$configPath = APATH_BASE . '/restoration.php';

		$fs = $this->container->fileSystem;
		clearstatcache(true, $configPath);

		if (@file_exists($configPath))
		{
			$fs->delete($configPath);
		}

		// Write new file
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
	}

	/**
	 * Returns the default FTP parameters
	 *
	 * @return  array
	 */
	function getFTPParams()
	{
		$config = $this->container->appConfig;

		return array(
			'procengine' => $config->get('fs.driver', 'file') == 'ftp' ? 'hybrid' : 'direct',
			'ftp_host'   => $config->get('fs.host', 'localhost'),
			'ftp_port'   => $config->get('fs.port', '21'),
			'ftp_user'   => $config->get('fs.username', ''),
			'ftp_pass'   => $config->get('fs.password', ''),
			'ftp_root'   => $config->get('fs.directory', ''),
			'tempdir'    => APATH_BASE . '/tmp'
		);
	}

	/**
	 * Gets the options for the extraction mode drop-down
	 *
	 * @return  array
	 */
	function getExtractionModes()
	{
		$options = array();
		$options[] = Select::option('hybrid',	Text::_('COM_AKEEBA_RESTORE_LABEL_EXTRACTIONMETHOD_HYBRID'));
		$options[] = Select::option('direct',	Text::_('COM_AKEEBA_RESTORE_LABEL_EXTRACTIONMETHOD_DIRECT'));
		$options[] = Select::option('ftp',		Text::_('COM_AKEEBA_RESTORE_LABEL_EXTRACTIONMETHOD_FTP'));

		return $options;
	}

	/**
	 * AJAX request proxy
	 *
	 * @return  boolean
	 */
	function doAjax()
	{
		$ajax = $this->getState('ajax');

		switch ($ajax)
		{
			// FTP Connection test for DirectFTP
			case 'testftp':
				// Grab request parameters
				$config = array(
					'host'    => $this->input->get('host', '', 'none', 2),
					'port'    => $this->input->get('port', 21, 'int'),
					'user'    => $this->input->get('user', '', 'none', 2),
					'pass'    => $this->input->get('pass', '', 'none', 2),
					'initdir' => $this->input->get('initdir', '', 'none', 2),
					'usessl'  => $this->input->get('usessl', 'cmd') == 'true',
					'passive' => $this->input->get('passive', 'cmd') == 'true'
				);

				// Perform the FTP connection test
				$test = new Directftp();
				$test->initialize('', $config);
				$errors = $test->getError();

				if (empty($errors))
				{
					$result = true;
				}
				else
				{
					$result = $errors;
				}

				break;

			// Unrecognized AJAX task
			default:
				$result = false;

				break;
		}

		return $result;
	}
} 