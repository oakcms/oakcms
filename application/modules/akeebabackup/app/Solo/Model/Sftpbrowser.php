<?php
/**
 * @package		solo
 * @copyright	2014-2017 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license		GNU GPL version 3 or later
 */

namespace Solo\Model;


use Awf\Filesystem\Sftp;
use Awf\Mvc\Model;

class Sftpbrowser extends Model
{
	public function doBrowse()
	{
		$dir = $this->getState('directory');

        // Remove the trailing slash and commit it in the state
        $this->setState('directory', rtrim($dir, '/'));

		// Parse directory to parts
		$parsed_dir = trim($dir,'/');
		$parts = empty($parsed_dir) ? array() : explode('/', $parsed_dir);

		// Find the path to the parent directory
		if (!empty($parts))
		{
			$copy_of_parts = $parts;
			array_pop($copy_of_parts);

			if (!empty($copy_of_parts))
			{
				$parent_directory = '/' . implode('/', $copy_of_parts);
			}
			else
			{
				$parent_directory = '/';
			}
		}
		else
		{
			$parent_directory = '/';
		}

		$options = array(
			'host'		=> $this->getState('host'),
			'port'		=> $this->getState('port'),
			'username'	=> $this->getState('username'),
			'password'	=> $this->getState('password'),
			'directory'	=> $this->getState('directory'),
			'privKey'	=> $this->getState('privKey'),
			'pubKey'	=> $this->getState('pubKey'),
		);

		$list = false;
		$error = '';

		try
		{
			$sftp = new Sftp($options);
			$list = $sftp->listFolders();
		}
		catch (\RuntimeException $e)
		{
            $error = $e->getMessage();

            // Did I get an error while fetching the list of folders? Let's try using the working dir
            if(isset($sftp))
            {
                try
                {
                    $dir   = rtrim($sftp->cwd(), '/').'/'.trim($dir, '/');
                    $dir   = rtrim($dir, '/');
                    $this->setState('directory', $dir);
                    $list  = $sftp->listFolders($dir);
                    $error = '';

                    $parsed_dir = trim($dir,'/');
                    $parts = empty($parsed_dir) ? array() : explode('/', $parsed_dir);

                    // Find the path to the parent directory
                    if (!empty($parts))
                    {
                        $copy_of_parts = $parts;
                        array_pop($copy_of_parts);

                        if (!empty($copy_of_parts))
                        {
                            $parent_directory = '/' . implode('/', $copy_of_parts);
                        }
                        else
                        {
                            $parent_directory = '/';
                        }
                    }
                    else
                    {
                        $parent_directory = '/';
                    }
                }
                catch(\RuntimeException $e)
                {
                    $error = $e->getMessage();
                }
            }
		}

		$response_array = array(
			'error'			=> $error,
			'list'			=> $list,
			'breadcrumbs'	=> $parts,
			'directory'		=> $this->getState('directory'),
			'parent'		=> $parent_directory
		);

		return $response_array;
	}
}