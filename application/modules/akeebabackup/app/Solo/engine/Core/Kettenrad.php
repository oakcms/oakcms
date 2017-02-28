<?php
/**
 * Akeeba Engine
 * The modular PHP5 site backup engine
 *
 * @copyright Copyright (c)2006-2017 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license   GNU GPL version 3 or, at your option, any later version
 * @package   akeebaengine
 *
 */

namespace Akeeba\Engine\Core;

// Protection against direct access
defined('AKEEBAENGINE') or die();

use Akeeba\Engine\Base\Part;
use Akeeba\Engine\Factory;
use Akeeba\Engine\Platform;
use Psr\Log\LogLevel;

/**
 * This is Akeeba Engine's heart. Kettenrad is reponsible for launching the
 * domain chain of a backup job.
 */
class Kettenrad extends Part
{
	/** @var bool Set to true when deadOnTimeout is registered as a shutdown function */
	public static $registeredShutdownCallback = false;

	/** @var bool Set to true when akeebaBackupErrorHandler is registered as an error handler */
	public static $registeredErrorHandler = false;

	/** @var array Cached copy of the response array */
	private $array_cache = null;

	/** @var array The list of remaining steps */
	private $domain_chain = array();

	/** @var string The current domain's name */
	private $domain = '';

	/**@ var string The active domain's class name */
	private $class = '';

	/** @var string The current backup's tag (actually: the backup's origin) */
	private $tag = null;

	/** @var int How many steps the domain_chain array contained when the backup began. Used for percentage calculations. */
	private $total_steps = 0;

	/** @var string A unique backup ID which allows us to run multiple parallel backups using the same backup origin (tag) */
	private $backup_id = '';

	/**
	 * Set to true when there are warnings available when getStatusArray() is called. This is used at the end of the
	 * backup to send a different push message depending on whether the backup completed with or without warnings.
	 *
	 * @var  bool
	 */
	private $warnings_issued = false;

	/**
	 * Returns the unique Backup ID
	 *
	 * @return string
	 */
	public function getBackupId()
	{
		return $this->backup_id;
	}

	/**
	 * Sets the unique backup ID.
	 *
	 * @param string $backup_id
	 */
	public function setBackupId($backup_id = null)
	{
		$this->backup_id = $backup_id;
	}

	/**
	 * Returns the current backup tag. If none is specified, it sets it to be the
	 * same as the current backup origin and returns the new setting.
	 *
	 * @return string
	 */
	public function getTag()
	{
		if (empty($this->tag))
		{
			// If no tag exists, we resort to the pre-set backup origin
			$tag = Platform::getInstance()->get_backup_origin();
			$this->tag = $tag;
		}

		return $this->tag;
	}

	protected function _prepare()
	{
		// Intialize the timer class
		$timer = Factory::getTimer();

		// Do we have a tag?
		if (!empty($this->_parametersArray['tag']))
		{
			$this->tag = $this->_parametersArray['tag'];
		}

		// Make sure a tag exists (or create a new one)
		$this->tag = $this->getTag();

		// Reset the log
		$logTag = $this->getLogTag();
		Factory::getLog()->open($logTag);
		Factory::getLog()->reset($logTag);

		if (!static::$registeredErrorHandler)
		{
			static::$registeredErrorHandler = true;
			set_error_handler('\\Akeeba\\Engine\\Core\\akeebaBackupErrorHandler');
		}

		// Reset the storage
		$factoryStorageTag = $this->tag . (empty($this->backup_id) ? '' : ('.' . $this->backup_id));
		Factory::getFactoryStorage()->reset($factoryStorageTag);

		// Apply the configuration overrides
		$overrides = Platform::getInstance()->configOverrides;

		if (is_array($overrides) && @count($overrides))
		{
			$registry = Factory::getConfiguration();
			$protected_keys = $registry->getProtectedKeys();
			$registry->resetProtectedKeys();

			foreach ($overrides as $k => $v)
			{
				$registry->set($k, $v);
			}

			$registry->setProtectedKeys($protected_keys);
		}

		// Get the domain chain
		$this->domain_chain = Factory::getEngineParamsProvider()->getDomainChain();
		$this->total_steps = count($this->domain_chain) - 1; // Init shouldn't count in the progress bar

		// Mark this engine for Nesting Logging
		$this->nest_logging = true;

		// Preparation is over
		$this->array_cache = null;
		$this->setState('prepared');

		// Send a push message to mark the start of backup
		$platform    = Platform::getInstance();
		$timeStamp = date($platform->translate('DATE_FORMAT_LC2'));
		$pushSubject = sprintf($platform->translate('COM_AKEEBA_PUSH_STARTBACKUP_SUBJECT'), $platform->get_site_name(), $platform->get_host());
		$pushDetails = sprintf($platform->translate('COM_AKEEBA_PUSH_STARTBACKUP_BODY'), $platform->get_site_name(), $platform->get_host(), $timeStamp, $this->getLogTag());
		Factory::getPush()->message($pushSubject, $pushDetails);

		//restore_error_handler();
	}

	protected function _run()
	{
		$logTag = $this->getLogTag();
		$logger = Factory::getLog();
		$logger->open($logTag);

		if (!static::$registeredErrorHandler)
		{
			static::$registeredErrorHandler = true;
			set_error_handler('\\Akeeba\\Engine\\Core\\akeebaBackupErrorHandler');
		}

		// Maybe we're already done or in an error state?
		if (($this->getError()) || ($this->getState() == 'postrun'))
		{
			return;
		}

		// Set running state
		$this->setState('running');

		// Initialize operation counter
		$registry = Factory::getConfiguration();
		$registry->set('volatile.operation_counter', 0);

		// Advance step counter
		$stepCounter = $registry->get('volatile.step_counter', 0);
		$registry->set('volatile.step_counter', ++$stepCounter);

		// Log step start number
		$logger->log(LogLevel::DEBUG, '====== Starting Step number ' . $stepCounter . ' ======');

		if (defined('AKEEBADEBUG'))
		{
			$root = Platform::getInstance()->get_site_root();
			$logger->log(LogLevel::DEBUG, 'Site root: ' . $root);
		}

		$timer = Factory::getTimer();
		$finished = false;
		$error = false;
		$breakFlag = false; // BREAKFLAG is optionally passed by domains to force-break current operation

		// Apply an infinite time limit if required
		if ($registry->get('akeeba.tuning.settimelimit', 0))
		{
			if (function_exists('set_time_limit'))
			{
				set_time_limit(0);
			}
		}

		// Loop until time's up, we're done or an error occurred, or BREAKFLAG is set
		$this->array_cache = null;
		while (($timer->getTimeLeft() > 0) && (!$finished) && (!$error) && (!$breakFlag))
		{
			// Reset the break flag
			$registry->set('volatile.breakflag', false);

			// Do we have to switch domains? This only happens if there is no active
			// domain, or the current domain has finished
			$have_to_switch = false;
			$object = null;

			if ($this->class == '')
			{
				$have_to_switch = true;
			}
			else
			{
				$object = Factory::getDomainObject($this->class);

				if (!is_object($object))
				{
					$have_to_switch = true;
				}
				else
				{
					if (!in_array('getState', get_class_methods($object)))
					{
						$have_to_switch = true;
					}
					elseif ($object->getState() == 'finished')
					{
						$have_to_switch = true;
					}
				}
			}

			// Switch domain if necessary
			if ($have_to_switch)
			{
				$logger->debug('Kettenrad :: Switching domains');

				if (!Factory::getConfiguration()->get('akeeba.tuning.nobreak.domains', 0))
				{
					$logger->log(LogLevel::DEBUG, "Kettenrad :: BREAKING STEP BEFORE SWITCHING DOMAIN");
					$registry->set('volatile.breakflag', true);
				}

				// Free last domain
				$object = null;

				if (empty($this->domain_chain))
				{
					// Aw, we're done! No more domains to run.
					$this->setState('postrun');
					$logger->log(LogLevel::DEBUG, "Kettenrad :: No more domains to process");
					$logger->log(LogLevel::DEBUG, '====== Finished Step number ' . $stepCounter . ' ======');
					$this->array_cache = null;

					//restore_error_handler();
					return;
				}

				// Shift the next definition off the stack
				$this->array_cache = null;
				$new_definition = array_shift($this->domain_chain);

				if (array_key_exists('class', $new_definition))
				{
					$logger->debug("Switching to domain {$new_definition['domain']}, class {$new_definition['class']}");
					$this->domain = $new_definition['domain'];
					$this->class = $new_definition['class'];
					// Get a working object
					$object = Factory::getDomainObject($this->class);
					$object->setup($this->_parametersArray);
				}
				else
				{
					$logger->log(LogLevel::WARNING, "Kettenrad :: No class defined trying to switch domains. The backup will crash.");
					$this->domain = null;
					$this->class = null;
				}
			}
			else
			{
				if (!is_object($object))
				{
					$logger->debug("Kettenrad :: Getting domain object of class {$this->class}");
					$object = Factory::getDomainObject($this->class);
				}
			}

			// Tick the object
			$logger->debug('Kettenrad :: Ticking the domain object');
			$result = $object->tick();

			// Propagate errors
			$logger->debug('Kettenrad :: Domain object returned; propagating');
			$this->propagateFromObject($object);

			// Advance operation counter
			$currentOperationNumber = $registry->get('volatile.operation_counter', 0);
			$currentOperationNumber++;
			$registry->set('volatile.operation_counter', $currentOperationNumber);

			// Process return array
			$this->setDomain($this->domain);
			$this->setStep($result['Step']);
			$this->setSubstep($result['Substep']);

			// Check for BREAKFLAG
			$breakFlag = $registry->get('volatile.breakflag', false);
			$logger->debug("Kettenrad :: Break flag status: " . ($breakFlag ? 'YES' : 'no'));

			// Process errors
			$error = false;

			if ($this->getError())
			{
				$error = true;
			}

			// Check if the backup procedure should finish now
			$finished = $error ? true : !($result['HasRun']);

			// Log operation end
			$logger->log(LogLevel::DEBUG, '----- Finished operation ' . $currentOperationNumber . ' ------');
		}

		// Log the result
		if (!$error)
		{
			$logger->log(LogLevel::DEBUG, "Successful Smart algorithm on " . get_class($object));
		}
		else
		{
			$logger->log(LogLevel::ERROR, "Failed Smart algorithm on " . get_class($object));
		}

		// Log if we have to do more work or not
		if (!is_object($object))
		{
			$logger->log(LogLevel::WARNING, "Kettenrad :: Empty object found when processing domain '" . $this->domain . "'. This should never happen.");
		}
		else
		{
			if ($object->getState() == 'running')
			{
				$logger->log(LogLevel::DEBUG, "Kettenrad :: More work required in domain '" . $this->domain . "'");
				// We need to set the break flag for the part processing to not batch successive steps
				$registry->set('volatile.breakflag', true);
			}
			elseif ($object->getState() == 'finished')
			{
				$logger->log(LogLevel::DEBUG, "Kettenrad :: Domain '" . $this->domain . "' has finished.");
				$registry->set('volatile.breakflag', false);
			}
		}

		// Log step end
		$logger->log(LogLevel::DEBUG, '====== Finished Step number ' . $stepCounter . ' ======');

		if (!$registry->get('akeeba.tuning.nobreak.domains', 0))
		{
			// Force break between steps
			$logger->debug('Kettenrad :: Setting the break flag between domains');
			$registry->set('volatile.breakflag', true);
		}
		//restore_error_handler();
	}

	protected function _finalize()
	{
		// Open the log
		$logTag = $this->getLogTag();
		Factory::getLog()->open($logTag);

		if (!static::$registeredErrorHandler)
		{
			static::$registeredErrorHandler = true;
			set_error_handler('\\Akeeba\\Engine\\Core\\akeebaBackupErrorHandler');
		}

		// Kill the cached array
		$this->array_cache = null;

		// Remove the memory file
		$tempVarsTag = $this->tag . (empty($this->backup_id) ? '' : ('.' . $this->backup_id));
		Factory::getFactoryStorage()->reset($tempVarsTag);

		// All done.
		Factory::getLog()->log(LogLevel::DEBUG, "Kettenrad :: Just finished");
		$this->setState('finished');

		// Send a push message to mark the end of backup
		$pushSubjectKey = $this->warnings_issued ? 'COM_AKEEBA_PUSH_ENDBACKUP_WARNINGS_SUBJECT' : 'COM_AKEEBA_PUSH_ENDBACKUP_SUCCESS_SUBJECT';
		$pushBodyKey = $this->warnings_issued ? 'COM_AKEEBA_PUSH_ENDBACKUP_WARNINGS_BODY' : 'COM_AKEEBA_PUSH_ENDBACKUP_SUCCESS_BODY';
		$platform    = Platform::getInstance();
		$timeStamp = date($platform->translate('DATE_FORMAT_LC2'));
		$pushSubject = sprintf($platform->translate($pushSubjectKey), $platform->get_site_name(), $platform->get_host());
		$pushDetails = sprintf($platform->translate($pushBodyKey), $platform->get_site_name(), $platform->get_host(), $timeStamp);
		Factory::getPush()->message($pushSubject, $pushDetails);

		//restore_error_handler();
	}

	/**
	 * Returns a copy of the class's status array
	 *
	 * @return array
	 */
	public function getStatusArray()
	{
		if (empty($this->array_cache))
		{
			// Get the default table
			$array = $this->_makeReturnTable();

			// Did we have warnings?
			$warnings = $this->getWarnings();

			if (count($warnings))
			{
				$this->warnings_issued = true;
			}

			// Get the current step number
			$stepCounter = Factory::getConfiguration()->get('volatile.step_counter', 0);

			// Add the archive name
			$statistics = Factory::getStatistics();
			$record = $statistics->getRecord();
			$array['Archive'] = isset($record['archivename']) ? $record['archivename'] : '';

			// Translate HasRun to what the rest of the suite expects
			$array['HasRun'] = ($this->getState() == 'finished') ? 1 : 0;

			// Translate no errors
			$array['Error'] = ($array['Error'] == false) ? '' : $array['Error'];

			$array['tag'] = $this->tag;
			$array['Progress'] = $this->getProgress();
			$array['backupid'] = $this->getBackupId();
			$array['sleepTime'] = $this->waitTimeMsec;
			$array['stepNumber'] = $stepCounter;
			$array['stepState'] = $this->getState();

			$this->array_cache = $array;
		}

		return $this->array_cache;
	}

	/**
	 * Gets the percentage of the backup process done so far.
	 *
	 * @return string
	 */
	public function getProgress()
	{
		// Get the overall percentage (based on domains complete so far)
		$remaining_steps = count($this->domain_chain);
		$remaining_steps++;
		$overall = 1 - ($remaining_steps / $this->total_steps);

		// How much is this step worth?
		$this_max = 1 / $this->total_steps;

		// Get the percentage done of the current object
		if (!empty($this->class))
		{
			$object = Factory::getDomainObject($this->class);
		}
		else
		{
			$object = null;
		}

		if (!is_object($object))
		{
			$local = 0;
		}
		else
		{
			$local = $object->getProgress();
		}

		$percentage = (int)(100 * ($overall + $local * $this_max));

		if ($percentage < 0)
		{
			$percentage = 0;
		}
		elseif ($percentage > 100)
		{
			$percentage = 100;
		}

		return $percentage;
	}

	/**
	 * Returns the tag used to open the correct log file
	 *
	 * @return string
	 */
	protected function getLogTag()
	{
		$tag = $this->getTag();

		if (!empty($this->backup_id))
		{
			$tag .= '.' . $this->backup_id;
		}

		return $tag;
	}
}

/**
 * Timeout error handler
 */
function deadOnTimeOut()
{
	if (connection_status() == 1)
	{
		Factory::getLog()->log(LogLevel::ERROR, 'The process was aborted on user\'s request');
	}
	elseif (connection_status() >= 2)
	{
		Factory::getLog()->log(LogLevel::ERROR, Platform::getInstance()->translate('COM_AKEEBA_BACKUP_ERR_KETTENRAD_TIMEOUT'));
	}
}

if (!Kettenrad::$registeredShutdownCallback)
{
	Kettenrad::$registeredShutdownCallback = true;
	register_shutdown_function("\\Akeeba\\Engine\\Core\\deadOnTimeOut");
}

/**
 * Nifty trick to track and log PHP errors to Akeeba Backup's log
 *
 * @param int    $errno
 * @param string $errstr
 * @param string $errfile
 * @param int    $errline
 *
 * @return bool|null
 */
function akeebaBackupErrorHandler($errno, $errstr, $errfile, $errline)
{
	// Sanity check
	if (!function_exists('error_reporting'))
	{
		return false;
	}

	// Do not proceed if the error springs from an @function() construct, or if
	// the overall error reporting level is set to report no errors.
	$error_reporting = error_reporting();

	if ($error_reporting == 0)
	{
		return false;
	}

	switch ($errno)
	{

		case E_ERROR:
		case E_USER_ERROR:
			// Can I really catch fatal errors? It doesn't seem likely...
			Factory::getLog()->log(LogLevel::ERROR, "PHP FATAL ERROR on line $errline in file $errfile:");
			Factory::getLog()->log(LogLevel::ERROR, $errstr);
			Factory::getLog()->log(LogLevel::ERROR, "Execution aborted due to PHP fatal error");
			break;

		case E_WARNING:
		case E_USER_WARNING:
			// Log as debug messages so that we don't spook the user with warnings
			Factory::getLog()->log(LogLevel::DEBUG, "PHP WARNING (not an error; you can ignore) on line $errline in file $errfile:");
			Factory::getLog()->log(LogLevel::DEBUG, $errstr);
			break;

		case E_NOTICE:
		case E_USER_NOTICE:
			// Log as debug messages so that we don't spook the user with notices
			Factory::getLog()->log(LogLevel::DEBUG, "PHP NOTICE (not an error; you can ignore) on line $errline in file $errfile:");
			Factory::getLog()->log(LogLevel::DEBUG, $errstr);
			break;

		default:
			// These are E_DEPRECATED, E_STRICT etc. Ignore that.
			break;
	}

	// Uncomment to prevent the execution of PHP's internal error handler
	//return true;
}
