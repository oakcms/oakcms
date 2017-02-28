<?php
/**
 * @package        solo
 * @copyright      2014-2017 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license        GNU GPL version 3 or later
 */

namespace Solo\Model\Json\Task;

use Solo\Model\Json\TaskInterface;
use Solo\Model\Update;

define('KICKSTART', 1);
require_once APATH_ROOT . '/restore.php';

/**
 * Extract the update package
 */
class UpdateExtract implements TaskInterface
{
	/**
	 * Return the JSON API task's name ("method" name). Remote clients will use it to call us.
	 *
	 * @return  string
	 */
	public function getMethodName()
	{
		return 'updateExtract';
	}

	/**
	 * Execute the JSON API task
	 *
	 * @param   array $parameters The parameters to this task
	 *
	 * @return  mixed
	 *
	 * @throws  \RuntimeException  In case of an error
	 */
	public function execute(array $parameters = array())
	{
		$update = new Update();
		$update->createRestorationINI();

		$ini_data  = null;
		$setupFile = APATH_ROOT . '/restoration.php';

		if (!file_exists($setupFile))
		{
			throw new \RuntimeException("Could not create restoration.php for extracting the update file", 500);
		}

		// Load restoration.php. It creates a global variable named $restoration_setup
		$restoration_setup = array();

		require_once $setupFile;

		$ini_data = $restoration_setup;

		if (empty($ini_data))
		{
			// No parameters fetched. Darn, how am I supposed to work like that?!
			throw new \RuntimeException("Could not read restoration.php for extracting the update file", 500);
		}

		foreach ($ini_data as $key => $value)
		{
			\AKFactory::set($key, $value);
		}

		\AKFactory::set('kickstart.enabled', true);

		// Reinitialize $ini_data
		$ini_data = null;

		\AKFactory::nuke();

		/** @var \AKAbstractUnarchiver $engine */
		$engine   = \AKFactory::getUnarchiver(); // Get the engine
		$observer = new RestorationObserver(); // Create a new observer
		$engine->attach($observer); // Attach the observer

		do
		{
			$engine->tick();
			$ret = $engine->getStatusArray();
		}
		while ($ret['HasRun'] && empty($ret['Error']));

		if ($ret['Error'])
		{
			throw new \RuntimeException("Extraction error: " . $ret['Error'], 500);
		}

		// Remove the installation directory
		$root = \AKFactory::get('kickstart.setup.destdir');
		recursive_remove_directory($root . '/installation');

		/** @var \AKAbstractPostproc $postproc */
		$postproc = \AKFactory::getPostProc();

		// Rename htaccess.bak to .htaccess
		if (file_exists($root . '/htaccess.bak'))
		{
			if (file_exists($root . '/.htaccess'))
			{
				$postproc->unlink($root . '/.htaccess');
			}

			$postproc->rename($root . '/htaccess.bak', $root . '/.htaccess');
		}

		// Rename web.config.bak to web.config
		if (file_exists($root . '/web.config.bak'))
		{
			if (file_exists($root . '/web.config'))
			{
				$postproc->unlink($root . '/web.config');
			}

			$postproc->rename($root . '/web.config.bak', $root . '/web.config');
		}

		// Remove restoration.php
		$basepath = KSROOTDIR;
		$basepath = rtrim(str_replace('\\', '/', $basepath), '/');

		if (!empty($basepath))
		{
			$basepath .= '/';
		}

		$postproc->unlink($basepath . 'restoration.php');

		// Import a custom finalisation file
		if (file_exists(APATH_ROOT . '/restore_finalisation.php'))
		{
			include_once APATH_ROOT . '/restore_finalisation.php';
		}

		// Run a custom finalisation script
		if (function_exists('finalizeRestore'))
		{
			finalizeRestore($root, $basepath);
		}

		return true;
	}
}

// The observer class, used to report number of files and bytes processed
class RestorationObserver extends \AKAbstractPartObserver
{
	public $compressedTotal = 0;
	public $uncompressedTotal = 0;
	public $filesProcessed = 0;

	public function update($object, $message)
	{
		if (!is_object($message))
		{
			return;
		}

		if (!array_key_exists('type', get_object_vars($message)))
		{
			return;
		}

		if ($message->type == 'startfile')
		{
			$this->filesProcessed++;
			$this->compressedTotal += $message->content->compressed;
			$this->uncompressedTotal += $message->content->uncompressed;
		}
	}

	public function __toString()
	{
		return __CLASS__;
	}

}