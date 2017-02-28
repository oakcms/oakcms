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
 * Hybrid filesystem abstraction. It uses direct file writing. When it detects that the write failed, it switches to
 * (S)FTP
 */
class Hybrid implements FilesystemInterface
{
	/**
	 * The File adapter
	 *
	 * @var  File
	 */
	private $fileAdapter = null;

	/**
	 * The (S)FTP filesystem abstraction adapter
	 *
	 * @var  FilesystemInterface
	 */
	private $abstractionAdapter = null;

	/**
	 * Public constructor
	 *
	 * @param   array       $options    Configuration options for the filesystem abstraction object
     * @param   Container   $container  Application container
	 *
	 * @throws  \RuntimeException
	 */
	public function __construct(array $options, Container $container = null)
	{
        if(!is_object($container))
        {
            $container = Application::getInstance()->getContainer();
        }

		$this->fileAdapter = new File($options, $container);

		if (isset($options['driver']))
		{
			$class = '\\Awf\\Filesystem\\' . ucfirst($options['driver']);

			if (class_exists($class))
			{
				try
				{
					$this->abstractionAdapter = new $class($options, $container);
				}
				// If we can't instantiate the abstraction adapter we'll only use the direct file write method
				catch (\RuntimeException $e)
				{
					$this->abstractionAdapter = null;
				}
			}
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
		$ret = $this->fileAdapter->write($fileName, $contents);

		if (!$ret && is_object($this->abstractionAdapter))
		{
			return $this->abstractionAdapter->write($fileName, $contents);
		}

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
		$ret = $this->fileAdapter->delete($fileName);

		if (!$ret && is_object($this->abstractionAdapter))
		{
			return $this->abstractionAdapter->delete($fileName);
		}

		return $ret;
	}

	/**
	 * Create a copy of the file
	 *
	 * @param   string  $from  The full path of the file to copy from
	 * @param   string  $to    The full path of the file that will hold the copy
	 *
	 * @return  boolean  True on success
	 */
	public function copy($from, $to)
	{
		$ret = $this->fileAdapter->copy($from, $to);

		if (!$ret && is_object($this->abstractionAdapter))
		{
			return $this->abstractionAdapter->copy($from, $to);
		}

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
		$ret = $this->fileAdapter->move($from, $to);

		if (!$ret && is_object($this->abstractionAdapter))
		{
			return $this->abstractionAdapter->move($from, $to);
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
		$ret = $this->fileAdapter->chmod($fileName, $permissions);

		if (!$ret && is_object($this->abstractionAdapter))
		{
			return $this->abstractionAdapter->chmod($fileName, $permissions);
		}

		return $ret;
	}

    /**
     * Return the current working dir
     *
     * @return  string
     */
    public function cwd()
    {
        $ret = $this->fileAdapter->cwd();

        if (!$ret && is_object($this->abstractionAdapter))
        {
            return $this->abstractionAdapter->cwd();
        }

        return $ret;
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
		$ret = $this->fileAdapter->mkdir($dirName, $permissions);

		if (!$ret && is_object($this->abstractionAdapter))
		{
			return $this->abstractionAdapter->mkdir($dirName, $permissions);
		}

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
		$ret = $this->fileAdapter->rmdir($dirName, $recursive);

		if (!$ret && is_object($this->abstractionAdapter))
		{
			return $this->abstractionAdapter->rmdir($dirName, $recursive);
		}

		return $ret;
	}

	/**
	 * Translate an absolute filesystem path into a relative path for use by the filesystem abstraction, e.g. a relative
	 * (S)FTP path
	 *
	 * @param   string  $fileName  The full filesystem path of a file or directory
	 *
	 * @return  string  The translated path for use by the filesystem abstraction
	 */
	public function translatePath($fileName)
	{
		if (is_object($this->abstractionAdapter))
		{
			return $this->abstractionAdapter->translatePath($fileName);
		}
		else
		{
			return $this->fileAdapter->translatePath($fileName);
		}
	}

	/**
	 * Lists the subdirectories inside a directory
	 *
	 * @param   null|string  $dir  The directory to scan. Skip to use the current directory.
	 *
	 * @return  array|bool  A list of folders, or false if we could not get a listing
	 *
	 * @throws  \RuntimeException  When the server is incompatible with our folder scanner
	 */
	public function listFolders($dir = null)
	{
		try
		{
			// First try using a direct access
			$list = $this->fileAdapter->listFolders($dir);
		}
		catch (\RuntimeException $e)
		{
			if (!is_object($this->abstractionAdapter))
			{
				// If we failed and there is no abstraction adapter rethrow the exception
				throw $e;
			}
			else
			{
				// Also try using the abstraction adapter
				$list = $this->abstractionAdapter->listFolders($dir);
			}
		}

		return $list;
	}
}