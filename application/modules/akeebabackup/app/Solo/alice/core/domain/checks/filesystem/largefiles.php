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
 * Checks if the user is trying to backup too big files
 */
class AliceCoreDomainChecksFilesystemLargefiles extends AliceCoreDomainChecksAbstract
{
    public function __construct($logFile = null)
    {
        parent::__construct(10, 'COM_AKEEBA_ALICE_ANALYZE_FILESYSTEM_LARGE_FILES', $logFile);
    }

	public function check()
	{
        $handle = @fopen($this->logFile, 'r');

        if($handle === false)
        {
            AliceUtilLogger::WriteLog(_AE_LOG_ERROR, $this->checkName.' Test error, could not open backup log file.');
            return false;
        }

        $prev_data = '';
        $buffer    = 65536;
        $bigfiles  = array();

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


            preg_match_all('#(_before_|\*after\*) large file: (<root>.*?) \- size: (\d+)#i', $data, $tmp_matches);

            // Record valid matches only (ie with a filesize)
            if(isset($tmp_matches[3]) && $tmp_matches[3])
            {
                for($i = 0; $i < count($tmp_matches[2]); $i++)
                {
                    // Get flagged files only once; I could have a breaking step after, before or BOTH a large file
                    $key = md5($tmp_matches[2][$i]);

                    if(!isset($bigfiles[$key]))
                    {
                        $bigfiles[$key] = array(
                            'filename' => $tmp_matches[2][$i],
                            'size'     => round($tmp_matches[3][$i] / 1024 / 1024, 2)
                        );
                    }
                }
            }
        }

        fclose($handle);

        // Let's log all the results
        foreach($bigfiles as $file)
        {
            AliceUtilLogger::WriteLog(_AE_LOG_INFO, $this->checkName.' Large file detected, position: '.$file['filename'].' size: '.$file['size'].' Mb');
        }

        $badfiles = array();

        // Now let's throw Exceptions if something is wrong
        foreach($bigfiles as $file)
        {
            $badfiles[] = $file;

            // More than 10 Mb? Always set the result to error, no matter what
            if($file['size'] >= 10)
            {
                $this->setResult(-1);
            }
            // Warning for "smaller" files, set the warn only if we don't already have a failure state
            elseif($file['size'] > 2 && $file['size'] < 10 && $this->getResult() >= 0)
            {
                $this->setResult(0);
            }
        }

        if($badfiles)
        {
            $errorMsg = array();

            foreach($badfiles as $bad)
            {
                $errorMsg[] = 'File: '.$bad['filename'].' '.$bad['size'].' Mb';
            }

            AliceUtilLogger::WriteLog(_AE_LOG_INFO, $this->checkName.' Test failed, found the following bad files:'."\n".implode("\n", $errorMsg));

	        $this->setErrLangKey(array('COM_AKEEBA_ALICE_ANALIZE_FILESYSTEM_LARGE_FILES_ERROR', "\n" . implode("\n", $errorMsg)));
            throw new Exception(Text::sprintf('COM_AKEEBA_ALICE_ANALIZE_FILESYSTEM_LARGE_FILES_ERROR', '<br/>'.implode('<br/>', $errorMsg)));
        }

        AliceUtilLogger::WriteLog(_AE_LOG_INFO, $this->checkName.' Test passed, no large files issue detected.');

        return true;
	}

	public function getSolution()
	{
		return Text::_('COM_AKEEBA_ALICE_ANALIZE_FILESYSTEM_LARGE_FILES_SOLUTION');
	}
}
