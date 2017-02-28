<?php
/**
 * @package        solo
 * @copyright      2014-2017 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license        GNU GPL version 3 or later
 */

namespace Solo\Model;

use Awf\Database\Driver;
use Awf\Mvc\Model;
use Awf\Text\Text;
use Akeeba\Engine\Factory;
use Akeeba\Engine\Platform;

class Discover extends Model
{
	/**
	 * List the archive files in a directory. It only lists the files which do not belong to an existing Akeeba Solo
	 * backup attempt record.
	 *
	 * @return  array
	 */
	public function getFiles()
	{
		$ret = array();

		$directory = $this->getState('directory', '');
		$directory = Factory::getFilesystemTools()->translateStockDirs($directory);

		// Get all archive files
		$allFiles = Factory::getFileLister()->getFiles($directory, true);
		$files = array();

		if (!empty($allFiles))
		{
			foreach ($allFiles as $file)
			{
				$ext = strtoupper(substr($file, -3));

				if (in_array($ext, array('JPA', 'JPS', 'ZIP')))
				{
					$files[] = $file;
				}
			}
		}

		// If nothing found, bail out
		if (empty($files))
		{
			return $ret;
		}

		// Make sure these files do not already exist in another backup record
		$db = $this->container->db;

		$sql = $db->getQuery(true)
			->select($db->qn('absolute_path'))
			->from($db->qn('#__ak_stats'))
			->where($db->qn('absolute_path') . ' LIKE ' . $db->q($directory . '%'))
			->where($db->qn('filesexist') . ' = ' . $db->q('1'));
		$db->setQuery($sql);
		$existingfiles = $db->loadColumn();

		foreach ($files as $file)
		{
			if (!in_array($file, $existingfiles))
			{
				$ret[] = $file;
			}
		}

		return $ret;
	}

	/**
	 * Imports a backup archive file
	 *
	 * @param   string  $file  The relative path of the file to import
	 *
	 * @return  void
	 */
	public function import($file)
	{
		$directory = $this->getState('directory', '');
		$directory = Factory::getFilesystemTools()->translateStockDirs($directory);

		// Find out how many parts there are
		$multipart = 0;
		$base = substr($file, 0, -4);
		$ext = substr($file, -3);
		$found = true;

		$total_size = @filesize($directory . '/' . $file);

		while ($found)
		{
			$multipart++;
			$newExtension = substr($ext, 0, 1) . sprintf('%02u', $multipart);
			$newFile = $directory . '/' . $base . '.' . $newExtension;
			$found = file_exists($newFile);

			if ($found)
			{
				$total_size += @filesize($newFile);
			}
		}

		$filetime = @filemtime($directory . '/' . $file);

		if (empty($filetime))
		{
			$filetime = time();
		}

		// Create a new backup record
		$record = array(
			'description'     => Text::_('COM_AKEEBA_DISCOVER_LABEL_IMPORTEDDESCRIPTION'),
			'comment'         => '',
			'backupstart'     => date('Y-m-d H:i:s', $filetime),
			'backupend'       => date('Y-m-d H:i:s', $filetime + 1),
			'status'          => 'complete',
			'origin'          => 'backend',
			'type'            => 'full',
			'profile_id'      => 1,
			'archivename'     => $file,
			'absolute_path'   => $directory . '/' . $file,
			'multipart'       => $multipart,
			'tag'             => 'backend',
			'filesexist'      => 1,
			'remote_filename' => '',
			'total_size'      => $total_size
		);
		$id = null;
		$dummy = null;
		$id = Platform::getInstance()->set_or_update_statistics($id, $record, $dummy);
	}
} 