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

class AliceCoreDomainChecksRuntimeerrorsCorruptedinstall extends AliceCoreDomainChecksAbstract
{
    public function __construct($logFile = null)
    {
        parent::__construct(50, 'COM_AKEEBA_ALICE_ANALYZE_RUNTIME_ERRORS_CORRUPTED_INSTALL', $logFile);
    }

	public function check()
	{
		$handle = @fopen($this->logFile, 'r');
		$error  = false;

		if($handle === false)
		{
			AliceUtilLogger::WriteLog(_AE_LOG_ERROR, $this->checkName.' Test error, could not open backup log file.');
			return false;
		}

		while(($line = fgets($handle)) !== false)
		{
			$pos = strpos($line, '|Loaded profile');

			if($pos !== false)
			{
				// Ok, I just passed the "Loaded profile" line, let's see if it's a broken install
				$line = fgets($handle);

				$logline = trim(substr($line, 24));

				// Empty line?? Most likely it's a broken install
				if($logline == '|')
				{
					$error = true;
				}

				break;
			}
		}

		fclose($handle);

		if($error)
		{
			AliceUtilLogger::WriteLog(_AE_LOG_ERROR, $this->checkName." Test error, most likely this installation is broken");

			$this->setResult(-1);
			$this->setErrLangKey('COM_AKEEBA_ALICE_ANALYZE_RUNTIME_ERRORS_CORRUPTED_INSTALL_ERROR');

			throw new Exception(Text::_('COM_AKEEBA_ALICE_ANALYZE_RUNTIME_ERRORS_CORRUPTED_INSTALL_ERROR'));
		}

		AliceUtilLogger::WriteLog(_AE_LOG_ERROR, $this->checkName." Test passed, installation seems ok.");

		return true;
	}

	public function getSolution()
	{
		return Text::_('COM_AKEEBA_ALICE_ANALYZE_RUNTIME_ERRORS_CORRUPTED_INSTALL_SOLUTION');
	}
}
