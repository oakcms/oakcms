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
 * FTP filesystem abstraction layer
 */
class Ftp implements FilesystemInterface
{
    /** @var  Container Application container */
    protected $container;

	/**
	 * FTP server's hostname or IP address
	 *
	 * @var  string
	 */
	private $host = 'localhost';

	/**
	 * FTP server's port, default: 21
	 *
	 * @var  integer
	 */
	private $port = 21;

	/**
	 * Username used to authenticate to the FTP server
	 *
	 * @var  string
	 */
	private $username = '';

	/**
	 * Password used to authenticate to the FTP server
	 *
	 * @var  string
	 */
	private $password = '';

	/**
	 * FTP initial directory
	 *
	 * @var  string
	 */
	private $directory = '/';

	/**
	 * Should I use SSL to connect to the server (FTP over explicit SSL, a.k.a. FTPS)?
	 *
	 * @var  boolean
	 */
	private $ssl = false;

	/**
	 * Should I use FTP passive mode?
	 *
	 * @var bool
	 */
	private $passive = true;

	/**
	 * The FTP connection handle
	 *
	 * @var  resource|null
	 */
	private $connection = null;

	/**
	 * Public constructor
	 *
	 * @param   array       $options    Configuration options for the filesystem abstraction object
     * @param   Container   $container  Application container
	 *
	 * @return  Ftp
	 *
	 * @throws  \RuntimeException
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

		if (isset($options['ssl']))
		{
			$this->ssl = $options['ssl'];
		}

		if (isset($options['passive']))
		{
			$this->passive = $options['passive'];
		}

		$this->connect();
	}

	/**
	 * Connect to the FTP server
	 *
	 * @throws  \RuntimeException
	 */
	public function connect()
	{
		// Try to connect to the server
		if($this->ssl)
		{
			if(function_exists('ftp_ssl_connect'))
			{
				$this->connection = @ftp_ssl_connect($this->host, $this->port);
			}
			else
			{
				$this->connection = false;
				throw new \RuntimeException('ftp_ssl_connect not available on this server', 500);
			}
		}
		else
		{
			$this->connection = @ftp_connect($this->host, $this->port);
		}

		if ($this->connection === false)
		{
			throw new \RuntimeException(sprintf('Cannot connect to FTP server [host:port] = %s:%s', $this->host, $this->port), 500);
		}

		// Attempt to authenticate
		if (!@ftp_login($this->connection, $this->username, $this->password))
		{
			@ftp_close($this->connection);
			$this->connection = null;

			throw new \RuntimeException(sprintf('Cannot log in to FTP server [username:password] = %s:%s', $this->username, $this->password), 500);
		}

		// Attempt to change to the initial directory
		if (!@ftp_chdir($this->connection, $this->directory))
		{
			@ftp_close($this->connection);
			$this->connection = null;

			throw new \RuntimeException(sprintf('Cannot change to initial FTP directory "%s" – make sure the folder exists and that you have adequate permissions to it', $this->directory), 500);
		}

		// Apply the passive mode preference
		@ftp_pasv($this->connection, $this->passive);
	}

	/**
	 * Public destructor, closes any open FTP connections
	 */
	public function __destruct()
	{
		if (!is_null($this->connection))
		{
			@ftp_close($this->connection);
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

		// Make sure the buffer:// wrapper is loaded
		class_exists('\\Awf\\Utils\\Buffer', true);

		$handle = fopen('buffer://awf_filesystem_ftp', 'r+');
		fwrite($handle, $contents);
		rewind($handle);

		$ret = @ftp_fput($this->connection, $targetFile, $handle, FTP_BINARY);

		fclose($handle);

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

		return @ftp_delete($this->connection, $targetFile);
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
		$fromFile = $this->translatePath($from);
		$toFile   = $this->translatePath($to);

		// Make sure the buffer:// wrapper is loaded
		class_exists('\\Awf\\Utils\\Buffer', true);

		$handle = fopen('buffer://awf_filesystem_ftp', 'r+');

		$ret = @ftp_fget($this->connection, $handle, $fromFile, FTP_BINARY);

		if ($ret !== false)
		{
			rewind($handle);
			$ret = @ftp_fput($this->connection, $toFile, $handle, FTP_BINARY);
		}

		fclose($handle);

		return $ret;
	}

	/**
	 * Move or rename a file
	 *
	 * @param   string  $from  The full path of the file to move
	 * @param   string  $to    The full path of the target file
	 *
	 * @return  boolean  True on success
	 */
	public function move($from, $to)
	{
		$fromFile = $this->translatePath($from);
		$toFile = $this->translatePath($to);

		return @ftp_rename($this->connection, $fromFile, $toFile);
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

		return (@ftp_chmod($this->connection, $permissions, $targetFile) !== false);
	}

    /**
     * Return the current working dir
     *
     * @return  string
     */
    public function cwd()
    {
        return @ftp_pwd($this->connection);
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

		$initialDir = str_replace('\\', '/', $this->directory);
		$initialDir = trim($initialDir, '/');

		// Of course I can't create the directory the application is located in :)
		if ($initialDir == $targetDir)
		{
			return true;
		}

		$directories = explode('/', $targetDir);
		$siteRootDir = $this->container['filesystemBase'];

		$localDir  = rtrim($siteRootDir, '/');
		$remoteDir = '/' . $initialDir;

		foreach ($directories as $dir)
		{
			$localDir  .= '/' . $dir;
			$remoteDir .= '/' . $dir;

			if (!is_dir($localDir))
			{
				$ret = @ftp_mkdir($this->connection, $remoteDir);

				if ($ret === false)
				{
					return $ret;
				}
			}
		}

		$this->chmod($dirName, $permissions);

		return true;
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

			return @ftp_rmdir($this->connection, $targetDir);
		}
		else
		{
			if (!is_dir($dirName))
			{
				return $this->delete($dirName);
			}

			$ret = true;
			$di  = new \DirectoryIterator($dirName);

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
	 * Translate an absolute filesystem path into a relative FTP path
	 *
	 * @param   string  $fileName  The full filesystem path of a file or directory
	 *
	 * @return  string  The translated path for use by the filesystem abstraction
	 */
	public function translatePath($fileName)
	{
		$fileName = str_replace('\\', '/', $fileName);

		$siteRootDir = $this->container['filesystemBase'];

		$appRoot = str_replace('\\', '/', $siteRootDir);
		$appRoot = rtrim($appRoot, '/');

		if (strpos($fileName, $appRoot) === 0)
		{
			$fileName = substr($fileName, strlen($appRoot) + 1);
			$fileName = trim($fileName, '/');
			$fileName = rtrim($this->directory, '/') . '/' . $fileName;
		}

		return $fileName;
	}

	/**
	 * Lists the subdirectories inside an FTP directory
	 *
	 * @param   null|string  $dir  The directory to scan. Skip to use the current directory.
	 *
	 * @return  array|bool  A list of folders, or false if we could not get a listing
	 *
	 * @throws  \RuntimeException  When the server is incompatible with our FTP folder scanner
	 */
	public function listFolders($dir = null)
	{
		if (!empty($dir))
		{
			$ftpDirectory = $this->translatePath($dir);

			if (!@ftp_chdir($this->connection, $ftpDirectory))
			{
				throw new \RuntimeException(sprintf('Cannot change to FTP directory "%s" – make sure the folder exists and that you have adequate permissions to it', $ftpDirectory), 500);
			}
		}

		$list = @ftp_rawlist($this->connection, '.');

		if ($list === false)
		{
			throw new \RuntimeException("Sorry, your FTP server doesn't support our FTP directory browser.");
		}

		$folders = array();

		foreach ($list as $v)
		{
			$info = array();
			$vinfo = preg_split("/[\s]+/", $v, 9);

			if ($vinfo[0] !== "total")
			{
				$perms = $vinfo[0];

				if (substr($perms,0,1) == 'd')
				{
					$folders[] = $vinfo[8];
				}
			}
		}

		asort($folders);

		return $folders;
	}
}