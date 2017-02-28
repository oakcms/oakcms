<?php
/**
 * @package		solo
 * @copyright	2014-2017 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license		GNU GPL version 3 or later
 */

namespace Solo\Model;


use Awf\Filesystem\Ftp;
use Awf\Mvc\Model;

class Ftpbrowser extends Model
{
	public function doBrowse()
	{
		$dir = $this->getState('directory');

		// Parse directory to parts
		$parsed_dir = trim($dir,'/');
		$parts      = empty($parsed_dir) ? array() : explode('/', $parsed_dir);

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
			$parent_directory = '';
		}


		$options = array(
			'host'		=> $this->getState('host'),
			'port'		=> $this->getState('port'),
			'username'	=> $this->getState('username'),
			'password'	=> $this->getState('password'),
			'ssl'		=> $this->getState('ssl'),
			'passive'	=> $this->getState('passive'),
			'directory'	=> $this->getState('directory'),
		);

		$list = false;
		$error = '';

		try
		{
			$ftp = new Ftp($options);

            if(!$dir)
            {
                $dir = $ftp->cwd();

                $parsed_dir = trim($dir, '/');
                $parts      = empty($parsed_dir) ? array() : explode('/', $parsed_dir);
                $parent_directory = $dir;
            }

			$list = $ftp->listFolders();
		}
		catch (\RuntimeException $e)
		{
			$error = $e->getMessage();
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