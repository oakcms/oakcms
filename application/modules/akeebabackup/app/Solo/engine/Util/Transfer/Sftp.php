<?php
/**
 * Akeeba Engine
 * The modular PHP5 site backup engine
 *
 * @copyright Copyright (c)2006-2017 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license   GNU GPL version 3 or, at your option, any later version
 * @package   akeebaengine
 *
 */

namespace Akeeba\Engine\Util\Transfer;

// Protection against direct access
defined('AKEEBAENGINE') or die();

/**
 * SFTP transfer object
 */
class Sftp implements TransferInterface
{
	/**
	 * SFTP server's hostname or IP address
	 *
	 * @var  string
	 */
	private $host = 'localhost';

	/**
	 * SFTP server's port, default: 21
	 *
	 * @var  integer
	 */
	private $port = 22;

	/**
	 * Username used to authenticate to the SFTP server
	 *
	 * @var  string
	 */
	private $username = '';

	/**
	 * Password used to authenticate to the SFTP server
	 *
	 * @var  string
	 */
	private $password = '';

	/**
	 * SFTP initial directory
	 *
	 * @var  string
	 */
	private $directory = '/';

	/**
	 * The SSH2 connection handle
	 *
	 * @var  resource|null
	 */
	private $connection = null;

	/**
	 * The SFTP connection handle
	 *
	 * @var  resource|null
	 */
	private $sftpHandle = null;

	/**
	 * The absolute filesystem path to a private key file used for authentication instead of a password.
	 *
	 * @var  string
	 */
	private $privateKey = '';

	/**
	 * The absolute filesystem path to a public key file used for authentication instead of a password.
	 *
	 * @var  string
	 */
	private $publicKey = '';

	/**
	 * Public constructor
	 *
	 * @param   array       $options    Configuration options for the filesystem abstraction object
	 *
	 * @return  Sftp
	 *
	 * @throws  \RuntimeException
	 */
	public function __construct(array $options)
	{
		if (isset($options['host']))
		{
			$this->host = $options['host'];
		}

		if (isset($options['port']))
		{
			$this->port = (int)$options['port'];
		}

		if (isset($options['username']))
		{
			$this->username = $options['username'];
		}

		if (isset($options['password']))
		{
			$this->password = $options['password'];
		}

		if (isset($options['directory']))
		{
			$this->directory = '/' . ltrim(trim($options['directory']), '/');
		}

		if (isset($options['privateKey']))
		{
			$this->privateKey = $options['privateKey'];
		}

		if (isset($options['publicKey']))
		{
			$this->publicKey = $options['publicKey'];
		}

		$this->connect();
	}

	public function __destruct()
	{
		if (is_resource($this->connection))
		{
			@ssh2_exec($this->connection, 'exit;');
			$this->connection = null;
			$this->sftpHandle = null;
		}
	}

	/**
	 * Connect to the FTP server
	 *
	 * @throws  \RuntimeException
	 */
	public function connect()
	{
		// Try to connect to the SSH server
		if (!function_exists('ssh2_connect'))
		{
			throw new \RuntimeException('Your web server does not have the SSH2 PHP module, therefore can not connect to SFTP servers.', 500);
		}

		$this->connection = ssh2_connect($this->host, $this->port);

		if ($this->connection === false)
		{
			$this->connection = null;

			throw new \RuntimeException(sprintf('Cannot connect to SFTP server [host:port] = %s:%s', $this->host, $this->port), 500);
		}

		// Attempt to authenticate
		if (!empty($this->publicKey) && !empty($this->privateKey))
		{
			if (!@ssh2_auth_pubkey_file($this->connection,$this->username, $this->publicKey, $this->privateKey, $this->password))
			{
				$this->connection = null;

				throw new \RuntimeException(sprintf('Cannot log in to SFTP server using key files [username:private_key_file:public_key_file:password] = %s:%s:%s:%s', $this->username, $this->privateKey, $this->publicKey, $this->password), 500);
			}
		}
		else
		{
			if (!@ssh2_auth_password($this->connection, $this->username, $this->password))
			{
				$this->connection = null;

				throw new \RuntimeException(sprintf('Cannot log in to SFTP server [username:password] = %s:%s', $this->username, $this->password), 500);
			}
		}

		// Get an SFTP handle
		$this->sftpHandle = ssh2_sftp($this->connection);

		if ($this->sftpHandle === false)
		{
			throw new \RuntimeException('Cannot start an SFTP session with the server', 500);
		}
	}

	/**
	 * Is this transfer method blocked by a server firewall?
	 *
	 * @param   array  $params  Any additional parameters you might need to pass
	 *
	 * @return  boolean  True if the firewall blocks connections to a known host
	 */
	public static function isFirewalled(array $params = array())
	{
		try
		{
			$connector = new static(array(
				'host'			=> 'test.rebex.net',
				'port'			=> 22,
				'username'		=> 'demo',
				'password'		=> 'password',
				'directory'		=> '',
			));

			$data = $connector->read('readme.txt');

			if (empty($data))
			{
				return true;
			}
		}
		catch (\Exception $e)
		{
			return true;
		}

		return false;
	}

	/**
	 * Write the contents into the file
	 *
	 * @param   string  $fileName  The full path to the file
	 * @param   string  $contents  The contents to write to the file
	 *
	 * @return  boolean  True on success
	 */
	public function write($fileName, $contents)
	{
		$fp = @fopen("ssh2.sftp://{$this->sftpHandle}/$fileName", 'w');

		if ($fp === false)
		{
			return false;
		}

		$ret = @fwrite($fp, $contents);

		@fclose($fp);

		return $ret;
	}

	/**
	 * Uploads a local file to the remote storage
	 *
	 * @param   string  $localFilename   The full path to the local file
	 * @param   string  $remoteFilename  The full path to the remote file
	 *
	 * @return  boolean  True on success
	 */
	public function upload($localFilename, $remoteFilename)
	{
		$fp = @fopen("ssh2.sftp://{$this->sftpHandle}/$remoteFilename", 'w');

		if ($fp === false)
		{
            throw new \RuntimeException("Could not open remote SFTP file $remoteFilename for writing");
		}

		$localFp = @fopen($localFilename, 'rb');

		if ($localFp === false)
		{
			fclose($fp);

            throw new \RuntimeException("Could not open local file $localFilename for reading");
		}

		while (!feof($localFp))
		{
			$data = fread($localFp, 131072);
			$ret = @fwrite($fp, $data);

			if ($ret < strlen($data))
			{
				fclose($fp);
				fclose($localFp);

                throw new \RuntimeException("An error occurred while copying file $localFilename to $remoteFilename");
			}
		}

		@fclose($fp);
		@fclose($localFp);

		return true;
	}

	/**
	 * Read the contents of a remote file into a string
	 *
	 * @param   string  $fileName  The full path to the remote file
	 *
	 * @return  string  The contents of the remote file
	 */
	public function read($fileName)
	{
		$fp = @fopen("ssh2.sftp://{$this->sftpHandle}/$fileName", 'r');

		if ($fp === false)
		{
			throw new \RuntimeException("Can not download remote file $fileName");
		}

		$ret = '';

		while (!feof($fp))
		{
			$ret .= fread($fp, 131072);
		}

		@fclose($fp);

		return $ret;
	}

	/**
	 * Download a remote file into a local file
	 *
	 * @param   string  $remoteFilename
	 * @param   string  $localFilename
	 *
	 * @return  boolean  True on success
	 */
	public function download($remoteFilename, $localFilename)
	{
		$fp = @fopen("ssh2.sftp://{$this->sftpHandle}/$remoteFilename", 'r');

		if ($fp === false)
		{
			throw new \RuntimeException("Could not open remote SFTP file $remoteFilename for reading");
		}

		$localFp = @fopen($localFilename, 'w');

		if ($localFp === false)
		{
			fclose($fp);

            throw new \RuntimeException("Could not open local file $localFilename for writing");
		}

		while (!feof($fp))
		{
			$chunk = fread($fp, 131072);

			if ($chunk === false)
			{
				fclose($fp);
				fclose($localFp);

                throw new \RuntimeException("An error occurred while copying file $remoteFilename to $localFilename");
			}

			fwrite($localFp, $chunk);
		}

		@fclose($fp);
		@fclose($localFp);

		return true;
	}

	/**
	 * Delete a file (remove it from the disk)
	 *
	 * @param   string  $fileName  The full path to the file
	 *
	 * @return  boolean  True on success
	 */
	public function delete($fileName)
	{
		try
		{
			$ret = @ssh2_sftp_unlink($this->sftpHandle, $fileName);
		}
		catch (\Exception $e)
		{
			$ret = false;
		}

		return $ret;
	}

	/**
	 * Create a copy of the file. Actually, we have to read it in memory and upload it again.
	 *
	 * @param   string  $from  The full path of the file to copy from
	 * @param   string  $to    The full path of the file that will hold the copy
	 *
	 * @return  boolean  True on success
	 */
	public function copy($from, $to)
	{
		$contents = @file_get_contents($from);

		return $this->write($to, $contents);
	}

	/**
	 * Move or rename a file. Actually, we have to read it, upload it again and then delete the original.
	 *
	 * @param   string  $from  The full path of the file to move
	 * @param   string  $to    The full path of the target file
	 *
	 * @return  boolean  True on success
	 */
	public function move($from, $to)
	{
		$ret = $this->copy($from, $to);

		if ($ret)
		{
			$ret = $this->delete($from);
		}

		return $ret;
	}

	/**
	 * Change the permissions of a file
	 *
	 * @param   string   $fileName     The full path of the file whose permissions will change
	 * @param   integer  $permissions  The new permissions, e.g. 0644 (remember the leading zero in octal numbers!)
	 *
	 * @return  boolean  True on success
	 */
	public function chmod($fileName, $permissions)
	{
		// Prefer the SFTP way, if available
		if (function_exists('ssh2_sftp_chmod'))
		{
			return @ssh2_sftp_chmod($this->sftpHandle, $fileName, $permissions);
		}
		// Otherwise fall back to the (likely to fail) raw command mode
		else
		{
			$cmd = 'chmod ' . decoct($permissions) . ' ' . escapeshellarg($fileName);
			return @ssh2_exec($this->connection, $cmd);
		}
	}

	/**
	 * Create a directory if it doesn't exist. The operation is implicitly recursive, i.e. it will create all
	 * intermediate directories if they do not already exist.
	 *
	 * @param   string   $dirName      The full path of the directory to create
	 * @param   integer  $permissions  The permissions of the created directory
	 *
	 * @return  boolean  True on success
	 */
	public function mkdir($dirName, $permissions = 0755)
	{
		$targetDir = rtrim($dirName, '/');

		$ret = @ssh2_sftp_mkdir($this->sftpHandle, $targetDir, $permissions, true);

		return $ret;
	}

    /**
     * Checks if the given directory exists
     *
     * @param   string   $path         The full path of the remote directory to check
     *
     * @return  boolean  True if the directory exists
     */
    public function isDir($path)
    {
        return @ssh2_sftp_stat($this->sftpHandle, $path);
    }

	/**
	 * Get the current working directory
	 *
	 * @return  string
	 */
	public function cwd()
	{
		return ssh2_sftp_realpath($this->sftpHandle, ".");
	}

    /**
	 * Returns the absolute remote path from a path relative to the initial directory configured when creating the
	 * transfer object.
	 *
	 * @param   string  $fileName  The relative path of a file or directory
	 *
	 * @return  string  The absolute path for use by the transfer object
	 */
	public function getPath($fileName)
	{
		$fileName = str_replace('\\', '/', $fileName);
		$fileName = rtrim($this->directory, '/') . '/' . $fileName;

		return $fileName;
	}

	/**
	 * Lists the subdirectories inside an SFTP directory
	 *
	 * @param   null|string  $dir  The directory to scan. Skip to use the current directory.
	 *
	 * @return  array|bool  A list of folders, or false if we could not get a listing
	 *
	 * @throws  \RuntimeException  When the server is incompatible with our SFTP folder scanner
	 */
	public function listFolders($dir = null)
	{
		if (empty($dir))
		{
			$dir = $this->directory;
		}

		// Get a raw directory listing (hoping it's a UNIX server!)
		$list = array();
		$dir  = ltrim($dir, '/');

		try
		{
			$di = new \DirectoryIterator("ssh2.sftp://" . $this->sftpHandle . "/$dir");
		}
		catch (\Exception $e)
		{
			throw new \RuntimeException(sprintf('Cannot change to SFTP directory "%s" – make sure the folder exists and that you have adequate permissions to it', $dir), 500);
		}

		if (!$di->valid())
		{
			throw new \RuntimeException(sprintf('Cannot change to SFTP directory "%s" – make sure the folder exists and that you have adequate permissions to it', $dir), 500);
		}

		/** @var \DirectoryIterator $entry */
		foreach ($di as $entry)
		{
			if ($entry->isDot())
			{
				continue;
			}

			if (!$entry->isDir())
			{
				continue;
			}

			$list[] = $entry->getFilename();
		}

		unset($di);

		if (!empty($list))
		{
			asort($list);
		}

		return $list;
	}
}