<?php
/**
 * Akeeba Engine
 * The modular PHP5 site backup engine
 * @copyright Copyright (c)2009-2016 Nicholas K. Dionysopoulos
 * @license GNU GPL version 3 or, at your option, any later version
 * @package ALICE
 *
 */

// Protection against direct access
use Awf\Text\Text;

defined('AKEEBAENGINE') or die();

/**
 * Checks if Akeeba Backup failed to write data inside the archive (WIN hosts only)
 */
class AliceCoreDomainChecksRuntimeerrorsWincantappend extends AliceCoreDomainChecksAbstract
{
    public function __construct($logFile = null)
    {
        parent::__construct(30, 'COM_AKEEBA_ALICE_ANALYZE_RUNTIME_ERRORS_WINCANTAPPEND', $logFile);
    }

	public function check()
	{

        // Customer is not on windows, this problem happened on Windows only
        if(!$this->isWin())
        {
            return true;
        }

        $handle = @fopen($this->logFile, 'r');

        if($handle === false)
        {
            AliceUtilLogger::WriteLog(_AE_LOG_ERROR, $this->checkName.' Test error, could not open backup log file.');
            return false;
        }

        $prev_data = '';
        $buffer    = 65536;
        $error     = false;

        while (!feof($handle))
        {
            $data = $prev_data.fread($handle, $buffer);

            // Let's find the last occurrence of a new line
            $newLine = strrpos($data, "\n");

            // I didn't hit any EOL char, let's keep reading
            if($newLine === false)
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

                $data      = substr($data, 0, $newLine);

                // I have to rollback only if I read the whole buffer (ie I'm not at the end of the file)
                // Using this trick should be much more faster than calling ftell to know where we are
                if($len == $buffer)
                {
                    fseek($handle, -$rollback, SEEK_CUR);
                }
            }

            if(preg_match('#Could not open archive file.*? for append#i', $data))
            {
                $error = true;
            }
        }

        fclose($handle);

        if($error)
        {
            AliceUtilLogger::WriteLog(_AE_LOG_INFO, $this->checkName.' Test failed, could not open backup file for append');

            $this->setResult(-1);
	        $this->setErrLangKey('COM_AKEEBA_ALICE_ANALYZE_RUNTIME_ERRORS_WINCANTAPPEND_ERROR');

            throw new Exception(Text::_('COM_AKEEBA_ALICE_ANALYZE_RUNTIME_ERRORS_WINCANTAPPEND_ERROR'));
        }

        AliceUtilLogger::WriteLog(_AE_LOG_INFO, $this->checkName.' Test passed, there are no issues while creating the backup archive ');

        return true;
	}

	public function getSolution()
	{
		return Text::_('COM_AKEEBA_ALICE_ANALYZE_RUNTIME_ERRORS_WINCANTAPPEND_SOLUTION');
	}

    private function isWin()
    {
        $handle = @fopen($this->logFile, 'r');
        $OS     = '';

        while(($line = fgets($handle)) !== false)
        {
            $pos = stripos($line, '|OS Version');

            if($pos !== false)
            {
                $OS = trim(substr($line, strpos($line, ':', $pos) + 1));
                break;
            }
        }

        fclose($handle);

        if(stripos($OS, 'windows') !== false)
        {
            return true;
        }
        else
        {
            return false;
        }
    }
}
