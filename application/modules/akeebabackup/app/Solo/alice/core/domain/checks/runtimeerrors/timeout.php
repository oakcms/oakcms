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
 * Checks that every page load is not hitting the timeout limit.
 * Time diff is performed against the "Start step" and "Saving Kettenrad" timestamps.
 */
class AliceCoreDomainChecksRuntimeerrorsTimeout extends AliceCoreDomainChecksAbstract
{
    public function __construct($logFile = null)
    {
        parent::__construct(20, 'COM_AKEEBA_ALICE_ANALYZE_RUNTIME_ERRORS_TIMEOUT', $logFile);
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
        $starting  = array();
        $saving    = array();

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

            preg_match_all('#(\d{6}\s\d{2}:\d{2}:\d{2})\|.*?Starting Step number#i', $data, $tmp_matches);

            if(isset($tmp_matches[1]))
            {
                $starting = array_merge($starting, $tmp_matches[1]);
            }

            preg_match_all('#(\d{6}\s\d{2}:\d{2}:\d{2})\|.*?Saving Kettenrad instance#i', $data, $tmp_matches);

            if(isset($tmp_matches[1]))
            {
                $saving = array_merge($saving, $tmp_matches[1]);
            }
        }

        fclose($handle);

        // If there is an issue with starting and saving instances, I can't go on, first of all fix that
        if(count($saving) != count($starting))
        {
            AliceUtilLogger::WriteLog(_AE_LOG_INFO, $this->checkName.' Could not proceed, starting and saving steps are different.');

            $this->setResult(-1);
	        $this->setErrLangKey('COM_AKEEBA_ALICE_ANALYZE_RUNTIME_ERRORS_TIMEOUT_KETTENRAD_BROKEN');

            throw new Exception(Text::_('COM_AKEEBA_ALICE_ANALYZE_RUNTIME_ERRORS_TIMEOUT_KETTENRAD_BROKEN'));
        }

        $temp = array();

        // Let's expand the date part so I can safely work with that strings
        foreach($starting as $item)
        {
            $temp[] = '20'.substr($item, 0, 2).'-'.substr($item, 2, 2).'-'.substr($item, 4, 2).substr($item, 6);
        }

        $starting = $temp;
        $temp     = array();

        // Let's expand the date part so I can safely work with that strings
        foreach($saving as $item)
        {
            $temp[] = '20'.substr($item, 0, 2).'-'.substr($item, 2, 2).'-'.substr($item, 4, 2).substr($item, 6);
        }

        $saving = $temp;

        $maxExcution = $this->detectMaxExec();

        // Ok, did I had any timeout between the start and saving step (ie page loads)?
        for($i = 0; $i < count($starting); $i++)
        {
            $duration = strtotime($saving[$i]) - strtotime($starting[$i]);

            AliceUtilLogger::WriteLog(_AE_LOG_INFO, $this->checkName.' Detected page running time: '.$duration.' seconds');

            if($duration > $maxExcution)
            {
                $this->setResult(-1);
	            $this->setErrLangKey(array('COM_AKEEBA_ALICE_ANALYZE_RUNTIME_ERRORS_TIMEOUT_MAX_EXECUTION', $duration));

                throw new Exception(Text::sprintf('COM_AKEEBA_ALICE_ANALYZE_RUNTIME_ERRORS_TIMEOUT_MAX_EXECUTION', $duration));
            }
        }

        AliceUtilLogger::WriteLog(_AE_LOG_INFO, $this->checkName.' Test passed : '.count($starting).' starting vs '.count($saving).' savings');

        return true;
	}

	public function getSolution()
	{
		return Text::_('COM_AKEEBA_ALICE_ANALYZE_RUNTIME_ERRORS_TIMEOUT_SOLUTION');
	}

    /**
     * Detects max execution time, reading backup log. If the maximum execution time is set to 0 or it's bigger
     * than 100, it gets the default value of 100.
     *
     * @return int|string
     */
    private function detectMaxExec()
    {
        $handle = @fopen($this->logFile, 'r');
        $time   = 0;

        while(($line = fgets($handle)) !== false)
        {
            $pos = stripos($line, '|Max. exec. time');

            if($pos !== false)
            {
                $time = trim(substr($line, strpos($line, ':', $pos) + 1));
                break;
            }
        }

        fclose($handle);

        if(!$time || $time > 100)
        {
            $time = 100;
        }

        return $time;
    }
}
