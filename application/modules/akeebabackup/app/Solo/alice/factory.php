<?php
/**
 * ALICE
 *
 * @copyright Copyright (c)2009-2016 Nicholas K. Dionysopoulos
 * @license   GNU GPL version 3 or, at your option, any later version
 * @package   alice
 */

// Protection against direct access
defined('AKEEBAENGINE') or die();

use Akeeba\Engine\Factory;

// Define the log levels
if (!defined('_AE_LOG_NONE'))
{
	define("_AE_LOG_NONE", 0);
	define("_AE_LOG_ERROR", 1);
	define("_AE_LOG_WARNING", 2);
	define("_AE_LOG_INFO", 3);
	define("_AE_LOG_DEBUG", 4);
}

// Try to kill errors display
if (function_exists('ini_set') && !defined('AKEEBADEBUG'))
{
	ini_set('display_errors', false);
}

/**
 * The Alice Factory class
 * This class is responsible for instantiating all Alice classes
 */
class AliceFactory
{
	/** @var array A list of instanciated objects */
	protected $objectlist = array();

	/** Private constructor makes sure we can't directly instantiate the class */
	private function __construct()
	{
	}

	/**
	 * Gets a single, internally used instance of the Factory
	 *
	 * @param string $serialized_data [optional] Serialized data to spawn the instance from
	 *
	 * @return AliceFactory A reference to the unique Factory object instance
	 */
	protected static function &getInstance($serialized_data = null)
	{
		static $myInstance;

		if (!is_object($myInstance) || !is_null($serialized_data))
		{
			if (!is_null($serialized_data))
			{
				$myInstance = unserialize($serialized_data);
			}
			else
			{
				$myInstance = new self();
			}
		}

		return $myInstance;
	}

	/**
	 * Internal function which instantiates a class named $class_name.
	 *
	 * @param string $class_name
	 *
	 * @return
	 */
	protected static function &getClassInstance($class_name)
	{
		$self = self::getInstance();

		if (!isset($self->objectlist[$class_name]))
		{
			if (!class_exists($class_name, true))
			{
				$self->objectlist[$class_name] = false;
			}
			else
			{
				$self->objectlist[$class_name] = new $class_name;
			}
		}

		return $self->objectlist[$class_name];
	}

	/**
	 * Internal function which removes a class named $class_name
	 *
	 * @param string $class_name
	 */
	protected static function unsetClassInstance($class_name)
	{
		$self = self::getInstance();

		if (isset($self->objectlist[$class_name]))
		{
			$self->objectlist[$class_name] = null;
			unset($self->objectlist[$class_name]);
		}
	}

	// ========================================================================
	// Public factory interface
	// ========================================================================

	/**
	 * Gets a serialized snapshot of the Factory for safekeeping (hibernate)
	 * @return string The serialized snapshot of the Factory
	 */
	public static function serialize()
	{
		$self = self::getInstance();

		// Call _onSerialize in all classes known to the factory
		if (!empty($self->objectlist))
		{
			foreach ($self->objectlist as $class_name => $object)
			{
				$o = $self->objectlist[$class_name];
				if (method_exists($o, '_onSerialize'))
				{
					call_user_func('_onSerialize', $o);
				}
			}
		}

		// Serialize the factory
		return serialize(self::getInstance());
	}

	/**
	 * Regenerates the full Factory state from a serialized snapshot (resume)
	 *
	 * @param string $serialized_data The serialized snapshot to resume from
	 */
	public static function unserialize($serialized_data)
	{
		self::getInstance($serialized_data);
	}

	/**
	 * Reset the internal factory state, freeing all previosuly created objects
	 */
	public static function nuke()
	{
		$self = self::getInstance();
		foreach ($self->objectlist as $key => $object)
		{
			$self->objectlist[$key] = null;
		}
		$self->objectlist = array();
	}

	// ========================================================================
	// Alice classes
	// ========================================================================

	/**
	 * Returns an Akeeba Configuration object
	 * @return AliceConfiguration The Akeeba Configuration object
	 */
	public static function &getConfiguration()
	{
		return self::getClassInstance('AliceConfiguration');
	}

	/**
	 * Get the a reference to the Akeeba Engine's timer
	 * @return \Awf\Timer\Timer
	 */
	public static function &getTimer()
	{
		// TODO I should create another Timer, since I could have problems with backup settings
		// ie steps too close => backup error. I can't use the same settings for find that error :)
		return Factory::getTimer();
	}

	/**
	 * Get a reference to Alice's heart, Kettenrad
	 * @return AliceCoreKettenrad
	 */
	public static function &getKettenrad()
	{
		return self::getClassInstance('AliceCoreKettenrad');
	}

	/**
	 * Loads an engine domain class and returns its associated object
	 *
	 * @param    string $domain_name The name of the domain, e.g. requirements for AliceCoreDomainRequirements
	 *
	 * @return AliceAbstractPart
	 */
	public static function &getDomainObject($domain_name)
	{
		return self::getClassInstance('AliceCoreDomain' . ucfirst($domain_name));
	}

	// ========================================================================
	// Handy functions
	// ========================================================================

	public static function getAliceRoot()
	{
		static $root = null;
		if (empty($root))
		{
			if (defined('ALICEROOT'))
			{
				$root = ALICEROOT;
			}
			else
			{
				$root = dirname(__FILE__);
			}
		}

		return $root;
	}
}

// Make sure the class autoloader is loaded
if (defined('ALICEROOT'))
{
	require_once ALICEROOT . DIRECTORY_SEPARATOR . 'autoloader.php';
}
else
{
	require_once dirname(__FILE__) . DIRECTORY_SEPARATOR . 'autoloader.php';
}

// Try to register AliceAutoloader with SPL, or fall back to making use of JLoader
// Obviously, performance is better with SPL, but not all systems support it.
if (function_exists('spl_autoload_register'))
{
	// Joomla! is using its own autoloader function which has to be registered first...
	if (function_exists('__autoload'))
	{
		spl_autoload_register('__autoload');
	}
	// ...and then register ourselves.
	spl_autoload_register('AliceAutoloader');
}
else
{
	// Guys, it's 2011 at the time of this writing. If you have a host which
	// doesn't support SPL yet, SWITCH HOSTS!
	throw new Exception('Akeeba Backup REQUIRES the SPL extension to be loaded and activated', 500);
}

// Define and register the timeout trap
function AliceTimeoutTrap()
{
	if (connection_status() >= 2)
	{
		AliceUtilLogger::WriteLog(_AE_LOG_ERROR, 'ALICE has timed out');
	}
}

register_shutdown_function("AliceTimeoutTrap");