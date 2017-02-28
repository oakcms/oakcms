<?php
/**
 * Akeeba Engine
 * The modular PHP5 site backup engine
 *
 * @copyright Copyright (c)2009-2016 Nicholas K. Dionysopoulos
 * @license   GNU GPL version 3 or, at your option, any later version
 * @package   ALICE
 *
 */

// Protection against direct access
use Awf\Text\Text;

defined('AKEEBAENGINE') or die();

/**
 * Checks if the user is trying to backup multiple Wordpress installations with a single backup
 */
class AliceCoreDomainChecksFilesystemMultiplewpsites extends AliceCoreDomainChecksAbstract
{
	public function __construct($logFile = null)
	{
		parent::__construct(20, 'COM_AKEEBA_ALICE_ANALYZE_FILESYSTEM_MULTIPLE_WPSITES', $logFile);
	}

	public function check()
	{
		$handle = @fopen($this->logFile, 'r');

		if ($handle === false)
		{
			AliceUtilLogger::WriteLog(_AE_LOG_ERROR, $this->checkName . ' Test error, could not open backup log file.');

			return false;
		}

		$prev_data  = '';
		$buffer     = 65536;
		$subfolders = array();

		while ( !feof($handle))
		{
			$data = $prev_data . fread($handle, $buffer);

			// Let's find the last occurrence of a new line
			$newLine = strrpos($data, "\n");

			// I didn't hit any EOL char, let's keep reading
			if ($newLine === false)
			{
				$prev_data = $data;
				continue;
			}
			else
			{
				// Gotcha! Let's roll back to its position
				$prev_data = '';
				$rollback  = strlen($data) - $newLine + 1;
				$len       = strlen($data);

				$data = substr($data, 0, $newLine);

				// I have to rollback only if I read the whole buffer (ie I'm not at the end of the file)
				// Using this trick should be much more faster than calling ftell to know where we are
				if ($len == $buffer)
				{
					fseek($handle, -$rollback, SEEK_CUR);
				}
			}

			preg_match_all('#Adding\s(.*?)/wp-config\.php to archive#i', $data, $matches);

			if ($matches[1])
			{
				$subfolders = array_merge($subfolders, $matches[1]);
			}
		}

		fclose($handle);

		if ($subfolders)
		{
			$this->setResult(0);
			AliceUtilLogger::WriteLog(_AE_LOG_INFO, $this->checkName . ' Test failed, found the following Wordpress sub-directories:' . "\n" . implode("\n", $subfolders));

			$this->setErrLangKey(array('COM_AKEEBA_ALICE_ANALYZE_FILESYSTEM_MULTIPLE_WPSITES_ERROR', "\n" . implode("\n", $subfolders)));
			throw new Exception(Text::sprintf('COM_AKEEBA_ALICE_ANALYZE_FILESYSTEM_MULTIPLE_WPSITES_ERROR', '<br/>' . implode('<br/>', $subfolders)));
		}

		AliceUtilLogger::WriteLog(_AE_LOG_INFO, $this->checkName . ' Test passed, no multiples sites detected.');

		return true;
	}

	public function getSolution()
	{
		return Text::_('COM_AKEEBA_ALICE_ANALYZE_FILESYSTEM_MULTIPLE_WPSITES_SOLUTION');
	}
}
