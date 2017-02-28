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

use Akeeba\Engine\Factory;

/**
 * Management class for temporary storage of the serialised engine state.
 */
class FactoryStorage
{
	protected $storageEngine = '';

	public function __construct($storageEngine = null)
	{
		$this->setStorageEngine($storageEngine);
	}

	/**
	 * Sets the storage engine which will be used
	 *
	 * @param   string  $engine  The storage engine (currently only db or file can be specified)
	 */
	public function setStorageEngine($engine = null)
	{
		if (empty($engine))
		{
			$config = Factory::getConfiguration();
			$usedb = $config->get('akeeba.core.usedbstorage', 0);
			$engine = $usedb ? 'db' : 'file';
		}

		$this->storageEngine = $engine;
	}

	/**
	 * Returns the name of the storage engine
	 *
	 * @return  string
	 */
	public function getStorageEngine()
	{
		return $this->storageEngine;
	}

    /**
     * Returns the fully qualified path to the storage file
	 *
     * @param   string  $tag
     *
     * @return  string
     */
	public function get_storage_filename($tag = null)
	{
		static $basepath = null;

		if ($this->storageEngine == 'db')
		{
			return empty($tag) ? 'storage' : $tag;
		}
		else
		{
			if (is_null($basepath))
			{
				$registry = Factory::getConfiguration();
				$basepath = $registry->get('akeeba.basic.output_directory') . DIRECTORY_SEPARATOR;
			}

			if (empty($tag))
			{
				$tag = 'storage';
			}

			return $basepath . 'akeeba_' . $tag;
		}
	}

    /**
     * Resets the storage. This method removes all stored values.
     * @param   null    $tag
     *
     * @return    bool    True on success
     */
	public function reset($tag = null)
	{
		switch ($this->storageEngine)
		{
			case 'file':
				$filename = $this->get_storage_filename($tag);

				if (!is_file($filename) && !is_link($filename))
				{
					return false;
				}

				return @unlink($this->get_storage_filename($tag));

				break;

			case 'db':
				$dbtag = $this->get_storage_filename($tag);
				$db = Factory::getDatabase();
				$sql = $db->getQuery(true)
					->delete($db->qn('#__ak_storage'))
					->where($db->qn('tag') . ' = ' . $db->q($dbtag));
				$db->setQuery($sql);

				try
				{
					$result = $db->query();
				}
				catch (\Exception $exc)
				{
					return false;
				}

				return ($result !== false);

				break;
		}

		return false;
	}

	public function set($value, $tag = null)
	{
		$storage_filename = $this->get_storage_filename($tag);

		switch ($this->storageEngine)
		{
			case 'file':
				// Remove old file (if exists)
				if (file_exists($storage_filename))
				{
					@unlink($storage_filename);
				}

				// Open the new file
				$fp = @fopen($storage_filename, 'wb');

				if ($fp === false)
				{
					return false;
				}

				// Add a header
				fwrite($fp, $this->encode($value));
				fclose($fp);

				return true;

				break;

			case 'db':
				$db = Factory::getDatabase();

				// Delete any old records
				$sql = $db->getQuery(true)
					->delete($db->qn('#__ak_storage'))
					->where($db->qn('tag') . ' = ' . $db->q($storage_filename));
				$db->setQuery($sql);

				try
				{
					$result = $db->query();
				}
				catch (\Exception $exc)
				{
					return false;
				}

				// Add the new record
				$sql = $db->getQuery(true)
					->insert($db->qn('#__ak_storage'))
					->columns(array(
								   $db->qn('tag'),
								   $db->qn('data'),
							  ))->values($db->q($storage_filename) . ',' . $db->q($this->encode($value)));

				$db->setQuery($sql);

				try
				{
					$result = $db->query();
				}
				catch (\Exception $exc)
				{
					return false;
				}

				return ($result !== false);

				break;
		}
	}

	public function &get($tag = null)
	{
		$storage_filename = $this->get_storage_filename($tag);

		$ret = false;

		switch ($this->storageEngine)
		{
			case 'file':
				$data = @file_get_contents($storage_filename);

				if ($data === false)
				{
					return $ret;
				}

				break;

			case 'db':
				$db = Factory::getDatabase();
				$sql = $db->getQuery(true)
					->select($db->qn('data'))
					->from($db->qn('#__ak_storage'))
					->where($db->qn('tag') . ' = ' . $db->q($storage_filename));
				$db->setQuery($sql);

				try
				{
					$data = $db->loadResult();
				}
				catch (\Exception $e)
				{
					$data = '';
				}

				break;
		}

		$ret = $this->decode($data);
		unset($data);

		return $ret;
	}

	public function encode(&$data)
	{
		// Should I base64-encode?
		if (function_exists('base64_encode') && function_exists('base64_decode'))
		{
			return base64_encode($data);
		}
		elseif (function_exists('convert_uuencode') && function_exists('convert_uudecode'))
		{
			return convert_uuencode($data);
		}
		else
		{
			return $data;
		}
	}

	public function decode(&$data)
	{
		if (function_exists('base64_encode') && function_exists('base64_decode'))
		{
			return base64_decode($data);
		}
		elseif (function_exists('convert_uuencode') && function_exists('convert_uudecode'))
		{
			return convert_uudecode($data);
		}
		else
		{
			return $data;
		}
	}
}