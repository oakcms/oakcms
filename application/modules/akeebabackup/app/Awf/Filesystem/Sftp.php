<?php
/**
 * @package     Awf
 * @copyright   2014-2017 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license     GNU GPL version 3 or later
 */

namespace Awf\Filesystem;
use Awf\Application\Application;
use Awf\Container\Container;

/**
 * SFTP filesystem abstraction layer
 */
class Sftp implements FilesystemInterface
{
    /** @var  Container Application container */
    protected $container;

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
     * @param   Container   $container  Application container
	 *
	 * @return  Sftp
	 */
	public function __construct(array $options, Container $container = null)
	{
        if(!is_object($container))
        {
            $container = Application::getInstance()->getContainer();
        }

        $this->container = $container;

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
		if(!function_exists('ssh2_connect'))
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
			if (!ssh2_auth_pubkey_file($this->connection,$this->username, $this->publicKey, $this->privateKey, $this->password))
			{
				$this->connection = null;

				throw new \RuntimeException(sprintf('Cannot log in to SFTP server using key files [username:private_key_file:public_key_file:password] = %s:%s:%s:%s', $this->username, $this->privateKey, $this->publicKey, $this->password), 500);
			}
		}
		else
		{
			if (!ssh2_auth_password($this->connection, $this->username, $this->password))
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
	 * Write the contents into the file
	 *
	 * @param   string  $fileName  The full path to the file
	 * @param   string  $contents  The contents to write to the file
	 *
	 * @return  boolean  True on success
	 */
	public function write($fileName, $contents)
	{
		$targetFile = $this->translatePath($fileName);

		$fp = @fopen("ssh2.sftp://{$this->sftpHandle}$targetFile", 'w');

		if($fp === false)
		{
			return false;
		}

		$ret = @fwrite($fp, $contents);

		@fclose($fp);

		return $ret;
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
		$targetFile = $this->translatePath($fileName);

		try
		{
			$ret = @ssh2_sftp_unlink($this->sftpHandle, $targetFile);
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
		$targetFile = $this->translatePath($fileName);

		// Prefer the SFTP way, if available
		if (function_exists('ssh2_sftp_chmod'))
		{
			return @ssh2_sftp_chmod($this->sftpHandle, $targetFile, $permissions);
		}
		// Otherwise fall back to the (likely to fail) raw command mode
		else
		{
			$cmd = 'chmod ' . decoct($permissions) . ' ' . escapeshellarg($targetFile);
			return @ssh2_exec($this->connection, $cmd);
		}
	}

    /**
     * Return the current working dir
     *
     * @return  string
     */
    public function cwd()
    {
        return ssh2_sftp_realpath($this->sftpHandle, ".");
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
		$targetDir = $this->translatePath($dirName);
		$targetDir = trim($targetDir, '/');

		$ret = @ssh2_sftp_mkdir($this->sftpHandle, $targetDir, $permissions, true);

		return $ret;
	}

	/**
	 * Remove a directory if it exists.
	 *
	 * @param   string   $dirName    The full path of the directory to remove
	 * @param   boolean  $recursive  Should I remove its contents recursively? Otherwise it will fail if the directory
	 *                               is not empty.
	 *
	 * @return mixed
	 */
	public function rmdir($dirName, $recursive = true)
	{
		if (!$recursive)
		{
			$targetDir = $this->translatePath($dirName);

			return @ssh2_sftp_rmdir($this->sftpHandle, $targetDir);
		}
		else
		{
			if (!is_dir($dirName))
			{
				return $this->delete($dirName);
			}

			$ret = true;
			$di = new \DirectoryIterator($dirName);

			/** @var \DirectoryIterator $dirEntry */
			foreach ($di as $dirEntry)
			{
				if ($dirEntry->isDot())
				{
					continue;
				}

				if ($dirEntry->isFile())
				{
					$ret = $ret && $this->delete($dirEntry->getPathname());
				}
				elseif ($dirEntry->isDir())
				{
					$ret = $ret && $this->rmdir($dirEntry->getPathname(), true);
				}
			}

			$ret = $ret && $this->rmdir($dirName, false);

			return $ret;
		}
	}

	/**
	 * Translate an absolute filesystem path into an absolute SFTP path
	 *
	 * @param   string  $fileName  The full filesystem path of a file or directory
	 *
	 * @return  string  The translated path for use by the filesystem abstraction
	 */
	public function translatePath($fileName)
	{
		$fileName = str_replace('\\', '/', $fileName);

		$realDir  = rtrim($this->directory, '/');
		$realDir .= '/' . dirname($fileName);
		$realDir  = '/' . ltrim($realDir, '/');

		$fileName = $realDir . '/' . basename($fileName);

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