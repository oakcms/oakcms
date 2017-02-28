<?php
/**
 * @package        solo
 * @copyright      2014-2017 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license        GNU GPL version 3 or later
 */

namespace Solo\Model;

use Akeeba\Engine\Factory;
use Akeeba\Engine\Platform;
use Akeeba\Engine\Util\Transfer;
use Awf\Download\Download;
use Awf\Mvc\Model;
use Awf\Text\Text;
use Awf\Uri\Uri;
use Solo\Model\Exception\TransferFatalError;
use Solo\Model\Exception\TransferIgnorableError;

class Transfers extends Model
{
	/**
	 * Get the information for the latest backup
	 *
	 * @return   array|null  An array of backup record information or null if there is no usable backup for site transfer
	 */
	public function getLatestBackupInformation()
	{
		// Initialise
		$ret = null;

		$db = Factory::getDatabase();

		/** @var Manage $model */
		$model = Model::getTmpInstance($this->container->application_name, 'Manage', $this->container);
		$model->savestate(0);
		$model->setState('limitstart', 0);
		$model->setState('limit', 1);
		$backups = $model->getStatisticsListWithMeta(false, null, $db->qn('id') . ' DESC');

		// No valid backups? No joy.
		if (empty($backups))
		{
			return $ret;
		}

		// Get the latest backup
		$backup = array_shift($backups);

		// If it's not stored on the server (e.g. remote backup), no joy.
		if ($backup['meta'] != 'ok')
		{
			return $ret;
		}

		// If it's not a full site backup, no joy.
		if ($backup['type'] != 'full')
		{
			return $ret;
		}

		return $backup;
	}

	/**
	 * Returns the amount of space required on the target server. The two array keys are
	 * size		In bytes
	 * string	Pretty formatted, user-friendly string
	 *
	 * @return  array
	 */
	public function getApproximateSpaceRequired()
	{
		$backup = $this->getLatestBackupInformation();

		if (is_null($backup))
		{
			return array(
				'size'   => 0,
				'string' => '0.00 Kb'
			);
		}

		$approximateSize = 2.5 * (float) $backup['size'];

		$unit = array('b', 'Kb', 'Mb', 'Gb', 'Tb', 'Pb');

		return array(
			'size'   => $approximateSize,
			'string' => @round($approximateSize / pow(1024, ($i = floor(log($approximateSize, 1024)))), 2) . ' ' . $unit[$i]
		);
	}

	/**
	 * Cleans up a URL and makes sure it is a valid-looking URL
	 *
	 * @param   string  $url  The URL to check
	 *
	 * @return  array  status [ok, invalid, same, notexists] (check status); url (the cleaned URL)
	 */
	public function checkAndCleanUrl($url)
	{
		// Initialise
		$result = array(
			'status'	=> 'ok',
			'url'		=> $url
		);

		// Am I missing the protocol?
		if (strpos($url, '://') === false)
		{
			$url = 'http://' . $url;
		}

		$result['url'] = $url;

		// Verify that it is an HTTP or HTTPS URL.
		$uri = Uri::getInstance($url);
		$protocol = $uri->getScheme();

		if (!in_array($protocol, array('http', 'https')))
		{
			$result['status'] = 'invalid';

			return $result;
		}

		// Verify we are not restoring to the same site we are backing up from
		$path = $this->simplifyPath($uri->getPath());
		$uri->setPath('/' . $path);

		$siteUri = Uri::getInstance();

		if ($siteUri->getHost() == $uri->getHost())
		{
			$sitePath = $this->simplifyPath($siteUri->getPath());

			if ($sitePath == $path)
			{
				$result['status'] = 'same';

				return $result;
			}
		}

		$result['url'] = $uri->toString(array('scheme', 'user', 'pass', 'host', 'port', 'path'));

		// Verify we can reach the domain. Since it can be an IP we check both name to IP and IP to name.
		$host = $uri->getHost();

		if (function_exists('idn_to_ascii'))
		{
			$host = idn_to_ascii($host);
		}

		$isValid = ($siteUri->getHost() == $uri->getHost()) || ($host == 'localhost') || ($host == '127.0.0.1') || (($host !== false) && checkdnsrr($host, 'A'));

		// Sometimes we have a domain name without a DNS record which *can* be accessed locally, e.g. through the hosts
		// file. We have to cater for that, just in case...
		if (!$isValid)
		{
			$download = new Download($this->container);
			$dummy = $download->getFromURL($uri->toString());

			$isValid = $dummy !== false;
		}

		if (!$isValid)
		{
			$result['status'] = 'notexists';

			return $result;
		}

		// All checks pass
		return $result;
	}

	/**
	 * Tries to simplify a server path to get the site's root. It can handle most forms on non-SEF and non-rewrite SEF
	 * URLs (as in index.php?foo=bar, something.php/this/is?completely=nuts#ok). It can't fix stupid but it tries really
	 * bloody hard to.
	 *
	 * @param   string  $path  The path to simplify. We *expect* this to contain nonsense.
	 *
	 * @return  string  The scrubbed clean URL, hopefully leading to the site's root.
	 */
	private function simplifyPath($path)
	{
		$path = ltrim($path, '/');

		if (empty($path))
		{
			return $path;
		}

		// Trim out anything after a .php file (including the .php file itself)
		if (substr($path, -1) != '/')
		{
			$parts = explode('/', $path);
			$newParts = array();

			foreach ($parts as $part)
			{
				if (substr($part, -4) == '.php')
				{
					break;
				}

				$newParts[] = $part;
			}

			$path = implode('/', $newParts);
		}

		if (substr($path, -13) == 'administrator')
		{
			$path = substr($path, 0, -13);
		}

		return $path;
	}

	/**
	 * Determines the status of FTP, FTPS and SFTP support. The returned array has two keys 'supported' and 'firewalled'
	 * each one being an array. You want the protocol to has its 'supported' value set to true and its 'firewalled'
	 * value set to false. This would mean that the server supports this protocol AND does not block outbound
	 * connections over this protocol.
	 *
	 * @return array
	 */
	public function getFTPSupport()
	{
		// Initialise
		$result = array(
			'supported'  => array(
				'ftpcurl'  => false,
				'ftpscurl' => false,
				'sftpcurl' => false,
				'ftp'      => false,
				'ftps'     => false,
				'sftp'     => false,
			),
			'firewalled' => array(
				'ftpcurl'  => false,
				'ftpscurl' => false,
				'sftpcurl' => false,
				'ftp'      => false,
				'ftps'     => false,
				'sftp'     => false
			)
		);

		// Necessary functions for each connection method
		$supportChecks = array(
			'ftpcurl'	=> array('curl_init', 'curl_exec', 'curl_setopt', 'curl_errno', 'curl_error'),
			'ftpscurl'	=> array('curl_init', 'curl_exec', 'curl_setopt', 'curl_errno', 'curl_error'),
			'sftpcurl'	=> array('curl_init', 'curl_exec', 'curl_setopt', 'curl_errno', 'curl_error'),
			'ftp'	    => array('ftp_connect', 'ftp_login', 'ftp_close', 'ftp_chdir', 'ftp_mkdir', 'ftp_pasv', 'ftp_put', 'ftp_delete'),
			'ftps'	    => array('ftp_ssl_connect', 'ftp_login', 'ftp_close', 'ftp_chdir', 'ftp_mkdir', 'ftp_pasv', 'ftp_put', 'ftp_delete'),
			'sftp'	    => array('ssh2_connect', 'ssh2_auth_password', 'ssh2_auth_pubkey_file', 'ssh2_sftp', 'ssh2_exec', 'ssh2_sftp_unlink', 'ssh2_sftp_stat', 'ssh2_sftp_mkdir')
		);

		// Determine which connection methods are supported
		$supported = array();

		foreach ($supportChecks as $protocol => $functions)
		{
			$supported[$protocol] = true;

			foreach ($functions as $function)
			{
				if (!function_exists($function))
				{
					$supported[$protocol] = false;

					break;
				}
			}
		}

		$result['supported'] = $supported;

		// Check firewall settings -- Disabled because the 3PD test server got clogged :(
		/**
		$result['firewalled'] = array(
			'ftp'      => !$result['supported']['ftp'] ? false : Transfer\Ftp::isFirewalled(),
			'ftpcurl'  => !$result['supported']['ftp'] ? false : Transfer\FtpCurl::isFirewalled(),
			'ftps'     => !$result['supported']['ftps'] ? false : Transfer\Ftp::isFirewalled(array('ssl' => true)),
			'ftpscurl' => !$result['supported']['ftps'] ? false : Transfer\FtpCurl::isFirewalled(array('ssl' => true)),
			'sftp'     => !$result['supported']['sftp'] ? false : Transfer\Sftp::isFirewalled(),
			'sftpcurl' => !$result['supported']['sftp'] ? false : Transfer\SftpCurl::isFirewalled(),
		);
		/**/

		return $result;
	}

	/**
	 * Checks the FTP connection parameters
	 *
	 * @param   array  $config  FTP/SFTP connection details
	 *
	 * @throws  \RuntimeException
	 */
	public function testConnection(array $config)
	{
		/** @var Transfer\TransferInterface $connector */
		$connector = $this->getConnector($config);

		// Is it the same site we are restoring from? It is if the configuration.php exists and has the same contents as
		// the one I read from our server.
		$this->checkIfSameSite($connector);

		// Only perform those checks if I'm not forcing the transfer
		if (!$config['force'])
		{
			// Check if there's a special file in this directory, e.g. .htaccess, php.ini, .user.ini or web.config.
			$this->checkIfHasSpecialFile($connector);

			// Check if there's another site present in this directory
			$this->checkIfExistingSite($connector);
		}

		// Does it match the URL to the site?
		$this->checkIfMatchesUrl($connector);
	}

    /**
     * Upload Kickstart, our extra script and check that the target server fullfills our criteria
     *
     * @param   array $config FTP/SFTP connection details
     *
     * @throws \RuntimeException
     * @throws \Exception
     */
	public function initialiseUpload(array $config)
	{
		/** @var Transfer\TransferInterface $connector */
		$connector = $this->getConnector($config);

		// Can I upload Kickstart and my extra script?
		$files = array(
			APATH_ROOT . '/Solo/assets/installers/kickstart.txt'  => 'kickstart.php',
			APATH_ROOT . '/Solo/assets/installers/kickstart.transfer.php' => 'kickstart.transfer.php'
		);

		$createdFiles = array();
		$transferredSize = 0;
		$transferTime = 0;

		try
		{
			foreach ($files as $localFile => $remoteFile)
			{
				$start = microtime(true);
				$connector->upload($localFile, $connector->getPath($remoteFile));
				$end = microtime(true);
				$createdFiles[] = $remoteFile;
				$transferredSize += filesize($localFile);
				$transferTime += $end - $start;
			}
		}
		catch (\Exception $e)
		{
			// An upload failed. Remove existing files.
			$this->removeRemoteFiles($connector, $createdFiles, true);

			throw new \RuntimeException(Text::_('COM_AKEEBA_TRANSFER_ERR_CANNOTUPLOADKICKSTART'));
		}

		// Get the transfer speed between the two servers in bytes / second
		$transferSpeed = $transferredSize / $transferTime;

		try
		{
			$connector->mkdir($connector->getPath('kicktemp'), 0777);
		}
		catch (\Exception $e)
		{
			// Don't sweat if we can't create our temporary directory.
		}

		// Can I run Kickstart and my extra script?
		try
		{
			$this->checkRemoteServerEnvironment($connector);
		}
		catch (\Exception $e)
		{
			$this->removeRemoteFiles($connector, $createdFiles, true);

			throw $e;
		}

		// Get the lowest maximum execution time between our local and remote server
		$remoteTimeout = $this->container->segment->get('transfer.remoteTimeLimit', 5);
		$localTimeout  = 5;

		if (function_exists('ini_get'))
		{
			$localTimeout = ini_get("max_execution_time");
		}

		$timeout = min($localTimeout, $remoteTimeout);

		if ($localTimeout == 0)
		{
			$timeout = $remoteTimeout;
		}
		elseif ($remoteTimeout == 0)
		{
			$timeout = $localTimeout;
		}

		if ($timeout == 0)
		{
			$timeout = 5;
		}

		// Get the maximimum transfer size, rounded down to 512K
		$maxTransferSize = $transferSpeed * $timeout;
		$maxTransferSize = floor($maxTransferSize / 524288) * 524288;

		if ($maxTransferSize == 0)
		{
			$maxTransferSize = 524288;
		}

		/**
		 * We never go above a maximum transfer size that depends on the server memory setting and the maximum remote
		 * upload size (minus 10Kb for overhead data)
		 */
		$chunkSizeLimit = $this->getMaxChunkSize();
		$maxUploadLimit = $this->container->segment->get('transfer.uploadLimit', 1048576) - 10240;
		$maxTransferSize = min($maxUploadLimit, $maxTransferSize, $chunkSizeLimit);

		// Save the optimal transfer size in the session
		$this->container->segment->set('transfer.fragSize', $maxTransferSize);
	}

    /**
     * Upload the next fragment
     *
     * @param   array $config FTP/SFTP connection details
     *
     * @return  array
     *
     * @throws \RuntimeException
     */
	public function uploadChunk(array $config)
	{
		$ret = array(
			'result'    => true,
			'done'      => false,
			'message'   => '',
			'totalSize' => 0,
			'doneSize'  => 0
		);

		// Get information from the session
		$session    = $this->container->segment;
		$fragSize   = $session->get('transfer.fragSize', 5242880);
		$backup     = $session->get('transfer.lastBackup', array());
		$totalSize  = $session->get('transfer.totalSize', 0);
		$doneSize   = $session->get('transfer.doneSize', 0);
		$part       = $session->get('transfer.part', -1);
		$frag       = $session->get('transfer.frag', -1);

		// Do I need to update the total size?
		if (!$totalSize)
		{
			$totalSize = $backup['total_size'];
			$session->set('transfer.totalSize', $totalSize);
		}

		$ret['totalSize'] = $totalSize;

		// First fragment of a new part
		if ($frag == -1)
		{
			$frag = 0;
			$part++;
		}

		/**
		 * If the backup is single part then $backup['multipart'] is 0. This means that the next if-block will report
		 * that the transfer is done. In these cases we have to convert $backup['multipart'] to 1 to let the upload
		 * actually run at all.
		 */
		if ($backup['multipart'] == 0)
		{
			$backup['multipart'] = 1;
		}

		// If I'm past the last part I'm done
		if ($part >= $backup['multipart'])
		{
			// We are done
			$ret['done'] = true;
			return $ret;
		}

		// Get the information for this part
		$fileName = $this->getPartFilename($backup['absolute_path'], $part);
		$fileSize  = filesize($fileName);

		$intendedSeekPosition = $fragSize * $frag;

		// I am trying to seek past EOF. Oops. Upload the next part.
		if ($intendedSeekPosition >= $fileSize)
		{
			$session->set('transfer.frag', -1);

			return $this->uploadChunk($config);
		}

		// Open the part
		$fp = @fopen($fileName, 'rb');

		if ($fp === false)
		{
			$ret['result'] = false;
			$ret['message'] = Text::sprintf('COM_AKEEBA_TRANSFER_ERR_CANNOTREADLOCALFILE', $fileName);

			return $ret;
		}

		// Seek to position
		if (fseek($fp, $intendedSeekPosition) == -1)
		{
			@fclose($fp);

			$ret['result'] = false;
			$ret['message'] = Text::sprintf('COM_AKEEBA_TRANSFER_ERR_CANNOTREADLOCALFILE', $fileName);

			return $ret;
		}

		// Read the data
		$data = fread($fp, $fragSize);
		$doneSize += strlen($data);
		$ret['doneSize'] = $doneSize;
		$session->set('transfer.doneSize', $doneSize);

		// Upload the data
		$session = $this->container->segment;
		$url = $session->get('transfer.url', '');
		$directory = $session->get('transfer.targetPath', '');

		$url = rtrim($url, '/') . '/kickstart.php';
		$uri = Uri::getInstance($url);
		$uri->setVar('task', 'uploadFile');
		$uri->setVar('file', basename($fileName));
		$uri->setVar('directory', $directory);
		$uri->setVar('frag', $frag);
		$uri->setVar('fragSize', $fragSize);

		$downloader = new Download($this->container);
		$downloader->setAdapterOptions(array(
			CURLOPT_CUSTOMREQUEST => 'POST',
			CURLOPT_POSTFIELDS => array(
				'data' => $data
			)
		));
		$dataLength = strlen($data);
		unset($data);
		$rawData = $downloader->getFromURL($uri->toString());

		// Close the part
		fclose($fp);

		// Try to get the raw JSON data
		$pos = strpos($rawData, '###');

		if ($pos === false)
		{
			// Invalid AJAX data, no leading ###
			throw new \RuntimeException(Text::sprintf('COM_AKEEBA_TRANSFER_ERR_CANNOTUPLOADARCHIVE', basename($fileName)));
		}

		// Remove the leading ###
		$rawData = substr($rawData, $pos + 3);

		$pos = strpos($rawData, '###');

		if ($pos === false)
		{
			// Invalid AJAX data, no trailing ###
			throw new \RuntimeException(Text::sprintf('COM_AKEEBA_TRANSFER_ERR_CANNOTUPLOADARCHIVE', basename($fileName)));
		}

		// Remove the trailing ###
		$rawData = substr($rawData, 0, $pos);

		// Get the JSON response
		$data = @json_decode($rawData, true);

		if (empty($data))
		{
			// Invalid AJAX data, can't decode this stuff
			throw new \RuntimeException(Text::sprintf('COM_AKEEBA_TRANSFER_ERR_CANNOTUPLOADARCHIVE', basename($fileName)));
		}

		if (!$data['status'])
		{
			throw new \RuntimeException(Text::sprintf('COM_AKEEBA_TRANSFER_ERR_ERRORFROMREMOTE', $data['message']));
		}

		// Update the session data
		$session->set('transfer.fragSize', $fragSize);
		$session->set('transfer.totalSize', $totalSize);
		$session->set('transfer.doneSize', $doneSize);
		$session->set('transfer.part', $part);
		$session->set('transfer.frag', ++$frag);

		// Did I go past EOF? Then on to the next part
		$intendedSeekPosition += $dataLength;

		if ($intendedSeekPosition >= $fileSize)
		{
			$session->set('transfer.frag', -1);
			$session->set('transfer.part', ++$part);
		}

		// Did I reach the last part? Then I'm done
		if ($part >= $backup['multipart'])
		{
			// We are done
			$ret['done'] = true;
		}

		return $ret;
	}

	/**
	 * Reset the upload information. Required to start over.
	 *
	 * @return  void
	 */
	public function resetUpload()
	{
		$session = $this->container->segment;

		$session->set('transfer.totalSize', 0);
		$session->set('transfer.doneSize', 0);
		$session->set('transfer.part', -1);
		$session->set('transfer.frag', -1);
	}

	/**
	 * Gets the TransferInterface connector object based on the $config configuration parameters array
	 *
	 * @param   array  $config  The configuration array with the FTP/SFTP connection information
	 *
	 * @return  Transfer\TransferInterface
	 *
	 * @throws  \RuntimeException
	 */
	private function getConnector(array $config)
	{
		switch ($config['method'])
		{
			case 'sftp':
				$connector = new Transfer\Sftp($config);
				break;

			case 'sftpcurl':
				$connector = new Transfer\SftpCurl($config);
				break;

			case 'ftpcurl':
			case 'ftpscurl':
				$connector = new Transfer\FtpCurl($config);
				break;

			default:
				$connector = new Transfer\Ftp($config);
				break;
		}

		return $connector;
	}

    /**
     * Checks if the remote site is the same as the site we are running the wizard from.
     *
     * @param   Transfer\TransferInterface $connector
     */
	private function checkIfSameSite(Transfer\TransferInterface $connector)
	{
        // TODO Is really possible to check if the site is the same?
		$myConfiguration = @file_get_contents(APATH_ROOT . '/version.php');

		if ($myConfiguration === false)
		{
			return;
		}

		try
		{
			$otherConfiguration = $connector->read($connector->getPath('version.php'));
		}
		catch (\Exception $e)
		{
			// File not found. No harm done.

			return;
		}

		if ($otherConfiguration == $myConfiguration)
		{
			throw new \RuntimeException(Text::_('COM_AKEEBA_TRANSFER_ERR_SAMESITE'));
		}
	}

	/**
	 * Check if there's a special file which might prevent site transfer from taking place.
	 *
	 * @param   Transfer\TransferInterface  $connector
	 */
	private function checkIfHasSpecialFile(Transfer\TransferInterface $connector)
	{
		$possibleFiles = array('.htaccess', 'web.config', 'php.ini', '.user.ini');

		foreach ($possibleFiles as $file)
		{
			try
			{
				$fileContents = $connector->read($connector->getPath($file));
			}
			catch (\Exception $e)
			{
				// File not found. No harm done.
				continue;
			}

			if (empty($fileContents))
			{
				continue;
			}

			throw new TransferIgnorableError(Text::sprintf('COM_AKEEBA_TRANSFER_ERR_HTACCESS', $file));
		}
	}

	/**
	 * Check if there's an existing site
	 *
	 * @param   Transfer\TransferInterface  $connector
	 */
	private function checkIfExistingSite(Transfer\TransferInterface $connector)
	{
		$possibleFiles = array('index.php', 'wordpress/index.php');

		foreach ($possibleFiles as $file)
		{
			try
			{
				$fileContents = $connector->read($connector->getPath($file));
			}
			catch (\Exception $e)
			{
				// File not found. No harm done.
				continue;
			}

			if (empty($fileContents))
			{
				continue;
			}

			throw new TransferIgnorableError(Text::_('COM_AKEEBA_TRANSFER_ERR_EXISTINGSITE'));
		}
	}

	/**
	 * Check if the connection matches the site's stated URL
	 *
	 * @param   Transfer\TransferInterface  $connector
	 */
	private function checkIfMatchesUrl(Transfer\TransferInterface $connector)
	{
		$sourceFile = APATH_SITE . '/media/logo/solo-16.png';

		// Try to upload the file
		try
		{
			$connector->upload($sourceFile, $connector->getPath(basename($sourceFile)));
		}
		catch (\Exception $e)
		{
			throw new \RuntimeException(Text::sprintf('COM_AKEEBA_TRANSFER_ERR_CANNOTUPLOADTESTFILE', basename($sourceFile)));
		}

		// Try to fetch the file over HTTP
		$session = $this->container->segment;
		$url = $session->get('transfer.url', '');

		$url = rtrim($url, '/');

		$downloader = new Download($this->container);
		$data = $downloader->getFromURL($url . '/' . basename($sourceFile));

		// Delete the temporary file
		$connector->delete($connector->getPath(basename($sourceFile)));

		// Could we get it over HTTP?
		$originalData = file_get_contents($sourceFile);

		if ($originalData != $data)
		{
			throw new TransferFatalError(Text::_('COM_AKEEBA_TRANSFER_ERR_CANNOTACCESSTESTFILE'));
		}
	}

	/**
	 * Gets the FTP configuration from the session
	 *
	 * @return  array
	 */
	public function getFtpConfig()
	{
		$session = $this->container->segment;
		$transferOption = $session->get('transfer.transferOption', '');

		return array(
			'method'      => $transferOption,
			'force'       => $session->get('transfer.force', 0),
			'host'        => $session->get('transfer.ftpHost', ''),
			'port'        => $session->get('transfer.ftpPort', ''),
			'username'    => $session->get('transfer.ftpUsername', ''),
			'password'    => $session->get('transfer.ftpPassword', ''),
			'directory'   => $session->get('transfer.ftpDirectory', ''),
			'ssl'         => $transferOption == 'ftps',
			'passive'     => $session->get('transfer.ftpPassive', 1),
			'passive_fix' => $session->get('transfer.ftpPassiveFix', 1),
			'privateKey'  => $session->get('transfer.ftpPrivateKey', ''),
			'publicKey'   => $session->get('transfer.ftpPubKey', ''),
		);
	}

	/**
	 * Removes files stored remotely
	 *
	 * @param   Transfer\TransferInterface  $connector         The transfer object
	 * @param   array                       $files             The list of remote files to delete (relative paths)
	 * @param   bool|true                   $ignoreExceptions  Should I ignore exceptions thrown?
	 *
	 * @return  void
	 *
	 * @throws  \Exception
	 */
	private function removeRemoteFiles(Transfer\TransferInterface $connector, array $files, $ignoreExceptions = true)
	{
		if (empty($files))
		{
			return;
		}

		foreach ($files as $file)
		{
			$remoteFile = $connector->getPath($file);

			try
			{
				$connector->delete($remoteFile);
			}
			catch (\Exception $e)
			{
				// Only let the exception bubble up if we are told not to ignore exceptions
				if (!$ignoreExceptions)
				{
					throw $e;
				}
			}
		}
	}

	/**
	 * Check if the remote server environment matches our expectations.
	 *
	 * @param   Transfer\TransferInterface  $connector  The remote transfer object
	 *
	 * @throws  \Exception
	 */
	private function checkRemoteServerEnvironment(Transfer\TransferInterface $connector)
	{
		$session = $this->container->segment;
		$baseUrl = $session->get('transfer.url', '');

		$baseUrl = rtrim($baseUrl, '/');

		$downloader = new Download($this->container);
		$rawData       = $downloader->getFromURL($baseUrl . '/kickstart.php?task=serverinfo');

		if ($rawData == false)
		{
			// Cannot access Kickstart on the remote server
			throw new \RuntimeException(Text::_('COM_AKEEBA_TRANSFER_ERR_CANNOTRUNKICKSTART'));
		}

		// Try to get the raw JSON data
		$pos = strpos($rawData, '###');

		if ($pos === false)
		{
			// Invalid AJAX data, no leading ###
			throw new \RuntimeException(Text::_('COM_AKEEBA_TRANSFER_ERR_CANNOTRUNKICKSTART'));
		}

		// Remove the leading ###
		$rawData = substr($rawData, $pos + 3);

		$pos = strpos($rawData, '###');

		if ($pos === false)
		{
			// Invalid AJAX data, no trailing ###
			throw new \RuntimeException(Text::_('COM_AKEEBA_TRANSFER_ERR_CANNOTRUNKICKSTART'));
		}

		// Remove the trailing ###
		$rawData = substr($rawData, 0, $pos);

		// Get the JSON response
		$data = @json_decode($rawData, true);

		if (empty($data))
		{
			// Invalid AJAX data, can't decode this stuff
			throw new \RuntimeException(Text::_('COM_AKEEBA_TRANSFER_ERR_CANNOTRUNKICKSTART'));
		}

		// Does the server have enough disk space?
		$freeSpace = $data['freeSpace'];

		$requiredSize = $this->getApproximateSpaceRequired();

		if ($requiredSize['size'] > $freeSpace)
		{
			$unit	 = array('b', 'Kb', 'Mb', 'Gb', 'Tb', 'Pb');
			$freeSpaceString = @round($freeSpace / pow(1024, ($i = floor(log($freeSpace, 1024)))), 2) . ' ' . $unit[$i];

			throw new \RuntimeException(Text::sprintf('COM_AKEEBA_TRANSFER_ERR_NOTENOUGHSPACE', $freeSpaceString, $requiredSize['string']));
		}

		// Can I write to remote files?
		$canWrite = $data['canWrite'];
		$canWriteTemp = $data['canWriteTemp'];

		if (!$canWrite && !$canWriteTemp)
		{
			throw new \RuntimeException(Text::_('COM_AKEEBA_TRANSFER_ERR_CANNOTWRITEREMOTEFILES'));
		}

		if ($canWrite)
		{
			$session->set('transfer.targetPath', '');
		}
		else
		{
			$session->set('transfer.targetPath', 'kicktemp');
		}

		$session->set('transfer.remoteTimeLimit', $data['maxExecTime']);

		// What is my upload limit?
		$uploadLimit = min($data['maxPost'], $data['maxUpload']);

		if (empty($data['maxPost']))
		{
			$uploadLimit = $data['maxUpload'];
		}
		elseif (empty($data['maxUpload']))
		{
			$uploadLimit = $data['maxPost'];
		}

		if (empty($uploadLimit))
		{
			$uploadLimit = 1048576;
		}

		$session->set('transfer.uploadLimit', $uploadLimit, 'akeeba');
	}

	/**
	 * Get the filename for a backup part file, given the base file and the part number
	 *
	 * @param   string  $baseFile  Full path to the base file (.jpa, .jps, .zip)
	 * @param   int     $part      Part number
	 *
	 * @return  string
	 */
	private function getPartFilename($baseFile, $part = 0)
	{
		if ($part == 0)
		{
			return $baseFile;
		}

		$dirname = dirname($baseFile);
		$basename = basename($baseFile);

		$pos = strrpos($basename, '.');
		$extension = substr($basename, $pos + 1);

		$newExtension = substr($baseFile, 0, 1) . sprintf('%02u', $part);

		return $dirname . '/' . basename($basename, '.' . $extension) . '.'  .$newExtension;
	}

	/**
	 * Returns the PHP memory limit. If ini_get is not available it will assume 8Mb.
	 *
	 * @return  int
	 */
	private function getServerMemoryLimit()
	{
		// Default reported memory limit: 8Mb
		$memLimit = 8388608;

		// If we can't find out how much PHP memory we have available use 8Mb by default
		if (!function_exists('ini_get'))
		{
			return $memLimit;
		}

		$iniMemLimit = ini_get("memory_limit");
		$iniMemLimit = $this->convertMemoryLimitToBytes($iniMemLimit);

		$memLimit = ($iniMemLimit > 0) ? $iniMemLimit : $memLimit;

		return (int) $memLimit;
	}

	/**
	 * Gets the maximum chunk size the server can handle safely. It does so by finding the PHP memory limit, removing
	 * the current memory usage (or at least 2Mb) and rounding down to the closest 512Kb. It can never be lower than
	 * 512Kb.
	 */
	private function getMaxChunkSize()
	{
		$memoryLimit = $this->getServerMemoryLimit();
		$usedMemory = max(memory_get_usage(), memory_get_peak_usage(), 2048);

		$maxChunkSize = max(($memoryLimit - $usedMemory) / 2, 524288);

		return floor($maxChunkSize / 524288) * 524288;
	}

	/**
	 * Convert the textual representation of PHP memory limit to an integer, e.g. convert 8M to 8388608
	 *
	 * @param   string  $val  The PHP memory limit
	 *
	 * @return  int  PHP memory limit as an integer
	 */
	private function convertMemoryLimitToBytes($val)
	{
		$val = trim($val);
		$last = strtolower($val{strlen($val) - 1});

		switch ($last)
		{
			case 't':
				$val *= 1099511627776;
				break;

			case 'g':
				$val *= 1073741824;
				break;

			case 'm':
				$val *= 1048576;
				break;

			case 'k':
				$val *= 1024;
				break;
		}

		return (int) $val;
	}
}