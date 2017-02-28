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

namespace Akeeba\Engine\Core\Domain;

// Protection against direct access
defined('AKEEBAENGINE') or die();

use Akeeba\Engine\Base\Part;
use Akeeba\Engine\Factory;
use Akeeba\Engine\Platform;
use Psr\Log\LogLevel;

/**
 * Installer deployment
 */
class Installer extends Part
{

	/** @var int Installer image file offset last read */
	private $offset;

	/** @var int How much installer data I have processed yet */
	private $runningSize = 0;

	/** @var int Installer image file index last read */
	private $xformIndex = 0;

	/** @var int Percentage of process done */
	private $progress = 0;

	/**
	 * Public constructor
	 *
	 * @return Installer
	 */
	public function __construct()
	{
		parent::__construct();

		Factory::getLog()->log(LogLevel::DEBUG, __CLASS__ . " :: New instance");
	}

	/**
	 * Implements the _prepare abstract method
	 *
	 */
	function _prepare()
	{
		$archive = Factory::getArchiverEngine();

		// Add the backup description and comment in a README.html file in the
		// installation directory. This makes it the first file in the archive.
		if ($this->installerSettings->readme)
		{
			$data = $this->createReadme();
			$archive->addVirtualFile('README.html', $this->installerSettings->installerroot, $data);
		}

		if ($this->installerSettings->extrainfo)
		{
			$data = $this->createExtrainfo();
			$archive->addVirtualFile('extrainfo.ini', $this->installerSettings->installerroot, $data);
		}

		if ($this->installerSettings->password)
		{
			$data = $this->createPasswordFile();

			if (!empty($data))
			{
				$archive->addVirtualFile('password.php', $this->installerSettings->installerroot, $data);
			}
		}

		$this->progress = 0;

		// Set our state to prepared
		$this->setState('prepared');
	}

	/**
	 * Implements the _run() abstract method
	 */
	function _run()
	{
		if ($this->getState() == 'postrun')
		{
			Factory::getLog()->log(LogLevel::DEBUG, __CLASS__ . " :: Already finished");
			$this->setStep('');
			$this->setSubstep('');
		}
		else
		{
			$this->setState('running');
		}

		// Try to step the archiver
		$archive = Factory::getArchiverEngine();
		$ret = $archive->transformJPA($this->xformIndex, $this->offset);

		// Error propagation
		$this->propagateFromObject($archive);

		if (($ret !== false) && ($archive->getError() == ''))
		{
			$this->offset = $ret['offset'];
			$this->xformIndex = $ret['index'];
			$this->setStep($ret['filename']);
		}

		// Check for completion
		if ($ret['done'])
		{
			Factory::getLog()->log(LogLevel::DEBUG, __CLASS__ . ":: archive is initialized");
			$this->setState('finished');
		}

		// Calculate percentage
		$this->runningSize += $ret['chunkProcessed'];

		if ($ret['filesize'] > 0)
		{
			$this->progress = $this->runningSize / $ret['filesize'];
		}
	}

	/**
	 * Implements the _finalize() abstract method
	 *
	 */
	function _finalize()
	{
		$this->setState('finished');
		$this->progress = 1;
	}

	/**
	 * Creates the contents of an HTML file with the description and comment of
	 * the backup. This file will be saved as README.html in the installer's root
	 * directory, as specified by the embedded installer's settings.
	 *
	 * @return string The contents of the HTML file.
	 */
	protected function createReadme()
	{
		$config = Factory::getConfiguration();

		$version = defined('AKEEBABACKUP_VERSION') ? AKEEBABACKUP_VERSION : AKEEBA_VERSION;
		$date    = defined('AKEEBABACKUP_DATE') ? AKEEBABACKUP_DATE : AKEEBA_DATE;
		$pro     = defined('AKEEBABACKUP_PRO') ? AKEEBABACKUP_PRO : AKEEBA_PRO;

		$lbl_version   = $version . ' (' . $date . ')';
		$lbl_coreorpro = ($pro == 1) ? 'Professional' : 'Core';

		$description = $config->get('volatile.core.description', '');
		$comment     = $config->get('volatile.core.comment', '');

		$config->set('volatile.core.description', null);
		$config->set('volatile.core.comment', null);

		return <<<ENDHTML
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<title>Akeeba Backup Archive Identity</title>
</head>
<body>
	<h1>Backup Description</h1>
	<p id="description"><![CDATA[$description]]></p>
	<h1>Backup Comment</h1>
	<div id="comment">
	$comment
	</div>
	<hr/>
	<p>
		Akeeba Backup $lbl_coreorpro $lbl_version
	</p>
</body>
</html>
ENDHTML;
	}

	protected function createExtrainfo()
	{
		$abversion = defined('AKEEBABACKUP_VERSION') ? AKEEBABACKUP_VERSION : AKEEBA_VERSION;
		$host = Platform::getInstance()->get_host();
		$backupdate = gmdate('Y-m-d H:i:s');
		$phpversion = PHP_VERSION;
		$rootPath = Platform::getInstance()->get_site_root();
		$ret = <<<ENDINI
; Akeeba Backup $abversion - Extra information used during restoration
host="$host"
backup_date="$backupdate"
akeeba_version="$abversion"
php_version="$phpversion"
root="$rootPath"
ENDINI;

		return $ret;
	}

	protected function createPasswordFile()
	{
		$config = Factory::getConfiguration();
		$ret = '';

		$password = $config->get('engine.installer.angie.key', '');

		if (empty($password))
		{
			return $ret;
		}

		$randVal = Factory::getRandval();

		$salt = $randVal->generateString(32);
		$passhash = md5($password . $salt) . ':' . $salt;
		$ret = "<?php\n";
		$ret .= "define('AKEEBA_PASSHASH', '" . $passhash . "');\n";

		return $ret;
	}


	/**
	 * Implements the progress calculation based on how much of the installer image
	 * archive we have processed so far.
	 */
	public function getProgress()
	{
		return $this->progress;
	}
}