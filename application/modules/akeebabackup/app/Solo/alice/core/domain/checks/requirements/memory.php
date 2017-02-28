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
 * Checks if we have enough memory to perform backup; at least 16Mb
 */
class AliceCoreDomainChecksRequirementsMemory extends AliceCoreDomainChecksAbstract
{
    public function __construct($logFile = null)
    {
        parent::__construct(30, 'COM_AKEEBA_ALICE_ANALYZE_REQUIREMENTS_MEMORY', $logFile);
    }

	public function check()
	{
		$handle     = @fopen($this->logFile, 'r');
        $limit      = false;
        $usage      = false;

		if($handle === false)
		{
			AliceUtilLogger::WriteLog(_AE_LOG_ERROR, $this->checkName.' Test error, could not open backup log file.');
			return false;
		}

		// Memory information is on a single line and it is at the beginning, so I can start reading one line at time
		while(($line = fgets($handle)) !== false)
		{
            if(!$limit)
            {
                $pos = strpos($line, '|Memory limit');

                if($pos !== false)
                {
                    $limit = trim(substr($line, strpos($line, ':', $pos) + 1));
                    $limit = str_ireplace('M', '', $limit);

                    AliceUtilLogger::WriteLog(_AE_LOG_INFO, $this->checkName.' Detected memory limit: '.$limit);
                }
            }


            if(!$usage)
            {
                $pos = strpos($line, '|Current mem. usage');

                if($pos !== false)
                {
                    $usage = trim(substr($line, strpos($line, ':', $pos) + 1));
                    // Converting to Mb for better handling
                    $usage = round($usage / 1024 / 1024, 2);

                    AliceUtilLogger::WriteLog(_AE_LOG_INFO, $this->checkName.' Detected memory usage: '.$usage);
                }
            }


            if($limit && $usage)
            {
                break;
            }
		}

        fclose($handle);

		if($limit && $usage)
		{
            $available = $limit - $usage;

            if($available >= 16)
            {
                AliceUtilLogger::WriteLog(_AE_LOG_INFO, $this->checkName.' Test passed, detected available memory: '.$available);
            }
            else
            {
                AliceUtilLogger::WriteLog(_AE_LOG_INFO, $this->checkName.' Test failed, detected available memory: '.$available);

                $this->setResult(-1);
	            $this->setErrLangKey(array('COM_AKEEBA_ALICE_ANALYZE_REQUIREMENTS_MEMORY_TOO_FEW', $available));

                throw new Exception(Text::sprintf('COM_AKEEBA_ALICE_ANALYZE_REQUIREMENTS_MEMORY_TOO_FEW', $available));
            }

		}
		else
		{
			AliceUtilLogger::WriteLog(_AE_LOG_ERROR, $this->checkName." Test error, couldn't detect available memory.");
		}

		return true;
	}

	public function getSolution()
	{
		return Text::_('COM_AKEEBA_ALICE_ANALYZE_REQUIREMENTS_MEMORY_SOLUTION');
	}
}
