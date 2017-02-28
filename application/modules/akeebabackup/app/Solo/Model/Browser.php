<?php
/**
 * @package        solo
 * @copyright      2014-2017 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license        GNU GPL version 3 or later
 */

namespace Solo\Model;

use Awf\Mvc\Model;
use Akeeba\Engine\Factory;
use Akeeba\Engine\Platform;

class Browser extends Model
{
	/**
	 * Make a directory listing and push all relevant information back into the model state
	 *
	 * @return  void
	 */
	function makeListing()
	{
		// Get the folder to browse
		$folder = $this->getState('folder', '');
		$processFolder = $this->getState('processfolder', 0);
		$siteRoot = Factory::getFilesystemTools()->TranslateWinPath(APATH_BASE);

		if (empty($folder))
		{
			$folder = $siteRoot;
		}

		$stock_dirs = Platform::getInstance()->get_stock_directories();
		arsort($stock_dirs);

		if ($processFolder == 1)
		{
			foreach ($stock_dirs as $find => $replace)
			{
				$folder = str_replace($find, $replace, $folder);
			}
		}

		// Normalise name, but only if realpath() really, REALLY works...
		$folder = Factory::getFilesystemTools()->TranslateWinPath($folder);
		$old_folder = $folder;
		$folder = @realpath($folder);

		if ($folder === false)
		{
			$folder = $old_folder;
		}

		if (@is_dir($folder))
		{
			$isFolderThere = true;
		}
		else
		{
			$isFolderThere = false;
		}

		// Check if it's a subdirectory of the site's root
		$isInRoot = (strpos($folder, $siteRoot) === 0);

		// Check open_basedir restrictions
		$isOpenbasedirRestricted = Factory::getConfigurationChecks()->checkOpenBasedirs($folder);

		// -- Get the meta form of the directory name, if applicable
		$folder_raw = $folder;

		foreach ($stock_dirs as $replace => $find)
		{
			$folder_raw = str_replace($find, $replace, $folder_raw);
		}

		// Writable check and contents listing if it's in site root and not restricted
		if ($isFolderThere && !$isOpenbasedirRestricted)
		{
			// Get writability status
			$isWritable = is_writable($folder);

			// Get contained folders
			$subFolders = array();
			try
			{
				$di = new \DirectoryIterator($folder);
				/** @var \DirectoryIterator $item */
				foreach ($di as $item)
				{
					if (!$item->isDir())
					{
						continue;
					}

					if ($item->isDot())
					{
						continue;
					}

					$subFolders[] = $item->getFilename();
				}
			}
			catch (\UnexpectedValueException $e)
			{
				$isWritable = false;
			}
		}
		else
		{
			if ($isFolderThere && !$isOpenbasedirRestricted)
			{
				$isWritable = is_writable($folder);
			}
			else
			{
				$isWritable = false;
			}

			$subFolders = array();
		}

		// Get parent directory
		$breadcrumbs = array();
		$pathParts = explode('/', $folder);

		if (is_array($pathParts))
		{
			$path = '';

			foreach ($pathParts as $part)
			{
				$path .= empty($path) ? $part : '/' . $part;

				if (empty($part))
				{
					if (DIRECTORY_SEPARATOR != '\\')
					{
						$path = '/';
					}

					$part = '/';
				}

				$crumb['label'] = $part;
				$crumb['folder'] = $path;
				$breadcrumbs[] = $crumb;
			}

			$junk = array_pop($pathParts);
			$parent = implode('/', $pathParts);
		}
		else
		{
			// Can't identify parent dir, use ourselves.
			$parent = $folder;
			$breadcrumbs = array();
		}

		$this->setState('folder', $folder);
		$this->setState('folder_raw', $folder_raw);
		$this->setState('parent', $parent);
		$this->setState('exists', $isFolderThere);
		$this->setState('inRoot', $isInRoot);
		$this->setState('openbasedirRestricted', $isOpenbasedirRestricted);
		$this->setState('writable', $isWritable);
		$this->setState('subfolders', $subFolders);
		$this->setState('breadcrumbs', $breadcrumbs);
	}
} 