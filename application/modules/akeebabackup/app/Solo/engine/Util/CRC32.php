<?php
/**
 * Akeeba Engine
 * The modular PHP5 site backup engine
 * @copyright Copyright (c)2006-2017 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license   GNU GPL version 3 or, at your option, any later version
 * @package   akeebaengine
 *
 */

namespace Akeeba\Engine\Util;

// Protection against direct access
defined('AKEEBAENGINE') or die();

use Akeeba\Engine\Base\Object;
use Akeeba\Engine\Factory;
use Psr\Log\LogLevel;

/**
 * A handy class to abstract the calculation of CRC32 of files under various
 * server conditions and versions of PHP.
 */
class CRC32 extends Object
{
	/**
	 * Returns the CRC32 of a file, selecting the more appropriate algorithm.
	 *
	 * @param string  $filename                   Absolute path to the file being processed
	 * @param integer $AkeebaPackerZIP_CHUNK_SIZE Obsoleted
	 *
	 * @return integer The CRC32 in numerical form
	 */
	public function crc32_file($filename, $AkeebaPackerZIP_CHUNK_SIZE)
	{
		static $configuration;

		if (!$configuration)
		{
			$configuration = Factory::getConfiguration();
		}

		if (function_exists("hash_file"))
		{
			$res = $this->crc32_file_php512($filename);
			Factory::getLog()->log(LogLevel::DEBUG, "File $filename - CRC32 = " . dechex($res) . " [HASH_FILE]");
		}
		else if (function_exists("file_get_contents") && (@filesize($filename) <= $AkeebaPackerZIP_CHUNK_SIZE))
		{
			$res = $this->crc32_file_getcontents($filename);
			Factory::getLog()->log(LogLevel::DEBUG, "File $filename - CRC32 = " . dechex($res) . " [FILE_GET_CONTENTS]");
		}
		else
		{
			$res = 0;
			Factory::getLog()->log(LogLevel::DEBUG, "File $filename - CRC32 = " . dechex($res) . " [FAKE - CANNOT CALCULATE]");
		}

		if ($res === false)
		{
			$res = 0;

			$this->setWarning("File $filename - NOT READABLE: CRC32 IS WRONG!");
		}

		return $res;
	}

	/**
	 * Very efficient CRC32 calculation for PHP 5.1.2 and greater, requiring
	 * the 'hash' PECL extension
	 *
	 * @param string $filename Absolute filepath
	 *
	 * @return integer The CRC32
	 */
	protected function crc32_file_php512($filename)
	{
		// Detection of buggy PHP hosts
		static $mustInvert = null;
		if (is_null($mustInvert))
		{
			$test_crc = @hash('crc32b', 'test', false);
			$mustInvert = (strtolower($test_crc) == '0c7e7fd8'); // Normally, it's D87F7E0C :)
			if ($mustInvert)
			{
				Factory::getLog()->log(LogLevel::WARNING, 'Your server has a buggy PHP version which produces inverted CRC32 values. Attempting a workaround. ZIP files may appear as corrupt.');
			}
		}

		$res = @hash_file('crc32b', $filename, false);
		if ($mustInvert)
		{
			// Workaround for buggy PHP versions (I think before 5.1.8) which produce inverted CRC32 sums
			$res2 = substr($res, 6, 2) . substr($res, 4, 2) . substr($res, 2, 2) . substr($res, 0, 2);
			$res = $res2;
		}
		$res = hexdec($res);

		return $res;
	}

	/**
	 * A compatible CRC32 calculation using file_get_contents, utilizing immense amounts of RAM
	 *
	 * @param string $filename
	 *
	 * @return integer
	 */
	protected function crc32_file_getcontents($filename)
	{
		return crc32(@file_get_contents($filename));
	}
}