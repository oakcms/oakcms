<?php
/**
 * @package        solo
 * @copyright      2014-2017 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license        GNU GPL version 3 or later
 */

namespace Solo\Model;


use Awf\Html\Select;
use Awf\Mvc\Model;
use Awf\Text\Text;
use Akeeba\Engine\Factory;
use Akeeba\Engine\Platform;

class Log extends Model
{
	/**
	 * Finds the available log files in this backup profile's log directory
	 *
	 * @return  array
	 */
	function getLogFiles()
	{
		$configuration = Factory::getConfiguration();
		$outputDirectory = $configuration->get('akeeba.basic.output_directory');

		$files = Factory::getFileLister()->getFiles($outputDirectory);
		$ret = array();

		if (!empty($files) && is_array($files))
		{
			foreach ($files as $filename)
			{
				$basename = basename($filename);

				if ((substr($basename, 0, 7) == 'akeeba.') && (substr($basename, -4) == '.log') && ($basename != 'akeeba.log'))
				{
					$tag = str_replace('akeeba.', '', str_replace('.log', '', $basename));

					if (!empty($tag))
					{
						$parts = explode('.', $tag);
						$key = array_pop($parts);
						$key = str_replace('id', '', $key);
						$key = is_numeric($key) ? sprintf('%015u', $key) : $key;

						if (empty($parts))
						{
							$key = str_repeat('0', 15) . '.' . $key;
						}
						else
						{
							$key .= '.' . implode('.', $parts);
						}

						$ret[$key] = $tag;
					}
				}
			}
		}

		krsort($ret);

		return $ret;
	}

	/**
	 * Returns the options for the backup origin dropdown box in the log file display page
	 *
	 * @return  array
	 */
	function getLogList()
	{
		$options = array();

		$list = $this->getLogFiles();

		if (!empty($list))
		{
			$options[] = Select::option(null, Text::_('COM_AKEEBA_LOG_CHOOSE_FILE_VALUE'));

			foreach ($list as $item)
			{
				$text = Text::_('COM_AKEEBA_BUADMIN_LABEL_ORIGIN_' . strtoupper($item));

				if (strstr($item, '.') !== false)
				{
					list($origin, $backupId) = explode('.', $item, 2);

					$text = Text::_('COM_AKEEBA_BUADMIN_LABEL_ORIGIN_' . strtoupper($origin)) . ' (' . $backupId . ')';
				}

				$options[] = Select::option($item, $text);
			}
		}

		return $options;
	}

	/**
	 * Outputs the raw log file with a big fat warning
	 *
	 * @return  void
	 */
	public function echoRawLog()
	{
		$tag = $this->getState('tag', '');

		echo "WARNING: Do not copy and paste lines from this file!\r\n";
		echo "You are supposed to ZIP and attach it in your support ticket.\r\n";
		echo "If you fail to do so we may not be able to reply to your ticket in a timely manner.\r\n";
		echo "\r\n";
		echo "--- START OF RAW LOG --\r\n";
		// The at sign is necessary to skip showing PHP errors if the file doesn't exist or isn't readable for some reason
		@readfile(Factory::getLog()->getLogFilename($tag));
		echo "--- END OF RAW LOG ---\r\n";
	}
} 