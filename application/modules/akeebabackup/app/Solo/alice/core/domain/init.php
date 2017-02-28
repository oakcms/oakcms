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
use Awf\Text\Text;

defined('AKEEBAENGINE') or die();

use Akeeba\Engine\Platform;

class AliceCoreDomainInit extends AliceCoreDomainAbstract
{
	public function __construct()
	{
		parent::__construct(10, '', Text::_('COM_AKEEBA_ALICE_ANALYZE_INIT'));
	}

	protected function _prepare()
	{
		// Initialize counters
		$registry = AliceFactory::getConfiguration();
		$registry->set('volatile.step_counter', 0);
		$registry->set('volatile.operation_counter', 0);

		// Initialize temporary storage
		AliceUtilTempvars::reset();

		// Force load the tag
		$kettenrad = AliceFactory::getKettenrad();
		$tag = $kettenrad->getTag();

		$this->setState('prepared');
	}

	protected function _run()
	{
		if( $this->getState() == 'postrun' )
		{
			AliceUtilLogger::WriteLog(_AE_LOG_DEBUG, __CLASS__." :: Already finished");
			$this->setStep('');
			$this->setSubstep('');

			return;
		}
		else
		{
			$this->setState('running');
		}

		// Load the version defines
		Platform::getInstance()->load_version_defines();

		$registry = AliceFactory::getConfiguration();

		// Write log file's header
		AliceUtilLogger::WriteLog(_AE_LOG_INFO, "--------------------------------------------------------------------------------");
		AliceUtilLogger::WriteLog(_AE_LOG_INFO, "Alice Log Inspector and Correction of Errors ".AKEEBABACKUP_VERSION.' ('.AKEEBABACKUP_DATE.')');
		AliceUtilLogger::WriteLog(_AE_LOG_INFO, "What went wrong?");
		AliceUtilLogger::WriteLog(_AE_LOG_INFO, "--------------------------------------------------------------------------------");

		AliceUtilLogger::WriteLog(_AE_LOG_INFO, "--- System Information ---" );
		AliceUtilLogger::WriteLog(_AE_LOG_INFO, "PHP Version        :" . PHP_VERSION );
		AliceUtilLogger::WriteLog(_AE_LOG_INFO, "PHP OS             :" . PHP_OS );
		AliceUtilLogger::WriteLog(_AE_LOG_INFO, "PHP SAPI           :" . PHP_SAPI );

		if(function_exists('php_uname'))
		{
			AliceUtilLogger::WriteLog(_AE_LOG_INFO, "OS Version         :" . php_uname('s') );
		}

		if (isset($_SERVER['SERVER_SOFTWARE']))
		{
			$server = $_SERVER['SERVER_SOFTWARE'];
		}
		elseif (($sf = getenv('SERVER_SOFTWARE')))
		{
			$server = $sf;
		}
		else
		{
			$server = 'n/a';
		}

		AliceUtilLogger::WriteLog(_AE_LOG_INFO, "Web Server         :" . $server );

		$platformData = Platform::getInstance()->getPlatformVersion();
		AliceUtilLogger::WriteLog(_AE_LOG_INFO, $platformData['name']." version    :" . $platformData['version'] );

		if(isset($_SERVER['HTTP_USER_AGENT']))
		{
			AliceUtilLogger::WriteLog(_AE_LOG_INFO, "User agent         :" . phpversion() <= "4.2.1" ? getenv( "HTTP_USER_AGENT" ) : $_SERVER['HTTP_USER_AGENT'] );
		}

		AliceUtilLogger::WriteLog(_AE_LOG_INFO, "Safe mode          :" . ini_get("safe_mode") );
		AliceUtilLogger::WriteLog(_AE_LOG_INFO, "Display errors     :" . ini_get("display_errors") );
		AliceUtilLogger::WriteLog(_AE_LOG_INFO, "Error reporting    :" . self::error2string() );
		AliceUtilLogger::WriteLog(_AE_LOG_INFO, "Error display      :" . self::errordisplay() );
		AliceUtilLogger::WriteLog(_AE_LOG_INFO, "Disabled functions :" . ini_get("disable_functions") );
		AliceUtilLogger::WriteLog(_AE_LOG_INFO, "open_basedir restr.:" . ini_get('open_basedir') );
		AliceUtilLogger::WriteLog(_AE_LOG_INFO, "Max. exec. time    :" . ini_get("max_execution_time") );
		AliceUtilLogger::WriteLog(_AE_LOG_INFO, "Memory limit       :" . ini_get("memory_limit") );

		if(function_exists("memory_get_usage"))
		{
			AliceUtilLogger::WriteLog(_AE_LOG_INFO, "Current mem. usage :" . memory_get_usage() );
		}

		AliceUtilLogger::WriteLog(_AE_LOG_INFO, "--------------------------------------------------------------------------------");

		if(!version_compare(PHP_VERSION, '5.3.0', 'ge'))
		{
			AliceUtilLogger::WriteLog(_AE_LOG_WARNING, "You are using an outdated version of PHP. Akeeba Engine may not work properly. Please upgrade to PHP 5.3 or later.");
		}

		$this->setState('postrun');
	}

	protected function _finalize()
	{
		$this->setState('finished');
	}

	public function getProgress()
	{
		return 1;
	}

	public static function error2string()
	{
		if(function_exists('error_reporting'))
		{
			$value = error_reporting();
		} else {
			return "Not applicable; host too restrictive";
		}
		$level_names = array(
		E_ERROR => 'E_ERROR', E_WARNING => 'E_WARNING',
		E_PARSE => 'E_PARSE', E_NOTICE => 'E_NOTICE',
		E_CORE_ERROR => 'E_CORE_ERROR', E_CORE_WARNING => 'E_CORE_WARNING',
		E_COMPILE_ERROR => 'E_COMPILE_ERROR', E_COMPILE_WARNING => 'E_COMPILE_WARNING',
		E_USER_ERROR => 'E_USER_ERROR', E_USER_WARNING => 'E_USER_WARNING',
		E_USER_NOTICE => 'E_USER_NOTICE' );
		if(defined('E_STRICT')) $level_names[E_STRICT]='E_STRICT';
		$levels=array();
		if(($value&E_ALL)==E_ALL)
		{
			$levels[]='E_ALL';
			$value&=~E_ALL;
		}
		foreach($level_names as $level=>$name)
		if(($value&$level)==$level) $levels[]=$name;
		return implode(' | ',$levels);
	}

	public static function errordisplay()
	{
		if(!function_exists('ini_get')) {
			return "Not applicable; host too restrictive";
		}

		return ini_get('display_errors') ? 'on' : 'off';
	}
}