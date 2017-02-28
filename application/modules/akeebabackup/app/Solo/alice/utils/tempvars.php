<?php
/**
 * Akeeba Engine
 * The modular PHP5 site backup engine
 * @copyright Copyright (c)2009-2016 Nicholas K. Dionysopoulos
 * @license GNU GPL version 3 or, at your option, any later version
 * @package akeebaengine
 *
 */

// Protection against direct access
defined('AKEEBAENGINE') or die();

/**
 * Temporary variables management class. Everything is stored serialized in an INI
 * file on the temporary directory. The code is copied from AEUtilTempvars, I just forced
 * the storage inside filesystem instead of db.
 * I couldn't extend the extisting one since there were a lot of "self" calls instead of "static",
 * and inheritance wouldn't work.
 */
class AliceUtilTempvars
{
	static $storageEngine = '';

	static public function getStorageEngine()
	{
		if(empty(self::$storageEngine)) self::setStorageEngine();
		return self::$storageEngine;
	}

	/**
	 * Always use file as storage, so we can avoid creating new tables
	 */
	static public function setStorageEngine($engine = null)
	{
		if(empty($engine)) {
			$engine = 'file';
		}
		self::$storageEngine = $engine;
	}

	/**
	 * Returns the fully qualified path to the storage file.
	 * Overrided to avoid loading from Akeeba Backup config
	 *
	 * @param null $tag
	 *
	 * @return string
	 */
	static public function get_storage_filename($tag = null)
	{
		static $basepath = null;

		if(self::getStorageEngine() == 'db')
		{
			return empty($tag) ? 'storage' : $tag;
		}
		else
		{
			if(is_null($basepath))
			{
				$basepath = APATH_ROOT.'/tmp/';
			}

			if(empty($tag))
			{
				$tag = 'storage';
			}

			return $basepath.'akeeba_'.$tag.'.php';
		}
	}

	/**
	 * Resets the storage. This method removes all stored values.
	 *
	 * @param null $tag
	 *
	 * @return    bool    True on success
	 */
	public static function reset($tag = null)
	{
		switch(self::getStorageEngine())
		{
			case 'file':
			default    :
				$filename = self::get_storage_filename($tag);
				if(!is_file($filename) && !is_link($filename)) return false;
				return @unlink(self::get_storage_filename($tag));
				break;
		}

	}

	public static function set(&$value, $tag = null)
	{
		$storage_filename = self::get_storage_filename($tag);

		switch(self::getStorageEngine())
		{
			case 'file':
			default    :
				// Remove old file (if exists)
				if(file_exists($storage_filename)) @unlink($storage_filename);

				// Open the new file
				$fp = @fopen($storage_filename, 'wb');
				if( $fp === false ) return false;

				// Add a header
				fputs($fp, "<?php die('Access denied'); ?>\n");
				fwrite($fp, self::encode($value));
				fclose($fp);

				return true;
				break;
		}
	}

	public static function &get($tag = null)
	{
		$storage_filename = self::get_storage_filename($tag);

		$ret = false;

		switch(self::getStorageEngine())
		{
			case 'file':
			default    :
				$rawdata = @file_get_contents($storage_filename);
				if($rawdata === false) return $ret;
				if(strpos($rawdata,"\n") === false) return $ret;
				list($header, $data) = explode("\n", $rawdata);
				unset($rawdata);
				unset($header);
				break;
		}

		$ret = self::decode($data);
		unset($data);
		return $ret;
	}

	public static function encode(&$data)
	{
		// Should I base64-encode?
		if( function_exists('base64_encode') && function_exists('base64_decode') ) {
			return base64_encode($data);
		} elseif( function_exists('convert_uuencode') && function_exists('convert_uudecode') ) {
			return convert_uuencode($data);
		} else return $data;
	}

	public static function decode(&$data)
	{
		if( function_exists('base64_encode') && function_exists('base64_decode') ) {
			return base64_decode($data);
		} elseif( function_exists('convert_uuencode') && function_exists('convert_uudecode') ) {
			return convert_uudecode($data);
		} else return $data;
	}
}