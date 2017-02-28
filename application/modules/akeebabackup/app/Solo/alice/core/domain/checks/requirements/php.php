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
 * Checks if the user is using a too old or too new PHP version
 */
class AliceCoreDomainChecksRequirementsPhp extends AliceCoreDomainChecksAbstract
{
    public function __construct($logFile = null)
    {
        parent::__construct(10, 'COM_AKEEBA_ALICE_ANALYZE_REQUIREMENTS_PHP_VERSION', $logFile);
    }

	public function check()
	{
		$handle = @fopen($this->logFile, 'r');
		$found  = false;

		if($handle === false)
		{
			AliceUtilLogger::WriteLog(_AE_LOG_ERROR, $this->checkName.' Test error, could not open backup log file.');
			return false;
		}

		// PHP information is on a single line, so I can start reading one line at time
		while(($line = fgets($handle)) !== false)
		{
			$pos = strpos($line, '|PHP Version');

			if($pos !== false)
			{
				$found   = true;
				$version = trim(substr($line, strpos($line, ':', $pos) + 1));

				// PHP too old (well, this should never happen)
				if(version_compare($version, '5.3', 'lt'))
				{
                    fclose($handle);
					AliceUtilLogger::WriteLog(_AE_LOG_INFO, $this->checkName.' Test failed, detected version: '.$version);

                    $this->setResult(-1);
					$this->setErrLangKey(array('COM_AKEEBA_ALICE_ANALYZE_REQUIREMENTS_PHP_VERSION_ERR_TOO_NEW', $version));

					throw new Exception(Text::sprintf('COM_AKEEBA_ALICE_ANALYZE_REQUIREMENTS_PHP_VERSION_ERR_TOO_NEW', $version));
				}
				/*
				elseif(version_compare($version, '5.5', 'ge'))
				{
                    fclose($handle);
					AliceUtilLogger::WriteLog(_AE_LOG_INFO, $this->checkName.' Test failed, detected version: '.$version);

                    $this->setResult(-1);
					throw new Exception(Text::sprintf('COM_AKEEBA_ALICE_ANALYZE_REQUIREMENTS_PHP_VERSION_ERR_TOO_OLD', $version));
				}
				*/

				break;
			}
		}

		if($found)
		{
			AliceUtilLogger::WriteLog(_AE_LOG_INFO, $this->checkName.' Test passed, detected version: '.$version);
		}
		else
		{
			AliceUtilLogger::WriteLog(_AE_LOG_ERROR, $this->checkName." Test error, couldn't detect PHP version.");
		}

		fclose($handle);

		return true;
	}

	public function getSolution()
	{
		return Text::_('COM_AKEEBA_ALICE_ANALYZE_REQUIREMENTS_PHP_VERSION_SOLUTION');
	}
}
