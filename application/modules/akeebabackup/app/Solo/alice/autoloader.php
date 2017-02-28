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
defined('AKEEBAENGINE') or die();

use Akeeba\Engine\Platform;

global $Alice_Class_Map;

// Class map
if(empty($Alice_Class_Map))
{
	$Alice_Class_Map = array(
		'AliceAbstract'                       => 'abstract',
		'AliceCore'                           => 'core',
		'AliceCoreDomain'                     => 'core'.DIRECTORY_SEPARATOR.'domain',
		'AliceCoreDomainChecks'               => 'core'.DIRECTORY_SEPARATOR.'domain'.DIRECTORY_SEPARATOR.'checks',
		'AliceCoreDomainChecksRequirements'   => 'core'.DIRECTORY_SEPARATOR.'domain'.DIRECTORY_SEPARATOR.'checks'.DIRECTORY_SEPARATOR.'requirements',
		'AliceCoreDomainChecksRuntimeerrors'  => 'core'.DIRECTORY_SEPARATOR.'domain'.DIRECTORY_SEPARATOR.'checks'.DIRECTORY_SEPARATOR.'runtimeerrors',
		'AliceCoreDomainChecksFilesystem'     => 'core'.DIRECTORY_SEPARATOR.'domain'.DIRECTORY_SEPARATOR.'checks'.DIRECTORY_SEPARATOR.'filesystem',
		'AliceUtil'                           => 'utils',
	);
}

/**
 * Loads the $class from a file in the directory $path, if and only if
 * the class name starts with $prefix. Will also try the plugins path
 * if the class is not present in the regular location.
 * @param string $class The class name
 * @param string $prefix The prefix to test
 * @param string $path The path to load the class from
 * @return bool True if we loaded the class
 */
function AliceLoadIfPrefix($class, $prefix, $path)
{
	// Find the root path of Akeeba's installation. Static so that we can save some CPU time.
	static $root;
	static $platformDirs = array();
	if(empty($root))
	{
		if(defined('ALICEROOT')) {
			$root = ALICEROOT;
		} else {
			$root = dirname(__FILE__);
		}
	}

	if(empty($platformDirs)) {
		$platformDirs = Platform::getInstance()->getPlatformDirectories();
	}

	if(strpos($class, $prefix) === 0)
	{
		$filename = strtolower(substr($class, strlen($prefix))) . '.php';
		// Try the plugins path
		$filePath = $root.DIRECTORY_SEPARATOR.'plugins'.DIRECTORY_SEPARATOR.$path.DIRECTORY_SEPARATOR.$filename;
		if(file_exists($filePath)) {
			require_once $filePath;
			if(class_exists($class, false))	return true;
		}
		// Try the platform overrides
		foreach($platformDirs as $dir) {
			$filePath = $dir.'/'.$path.'/'.$filename;
			if(file_exists($filePath)) {
				require_once $filePath;
				if(class_exists($class, false))	return true;
			}
		}
		// Try the regular path
		$filePath = $root.DIRECTORY_SEPARATOR.$path.DIRECTORY_SEPARATOR.$filename;
		if(file_exists($filePath)) {
			require_once $filePath;
			if(class_exists($class, false))	return true;
		}
	}
	return false;
}

/**
 * PHP5 class autoloader for all of Akeeba's classes
 * @param string $class_name The class name to load
 */
function AliceAutoloader($class_name)
{
	global $Alice_Class_Map;
	// We can only handle Alice* class names
	if(substr($class_name,0,5) != 'Alice') return;

	// The configuration class is a special case
	if($class_name == 'AliceConfiguration') {
		if(defined('ALICEROOT')) {
			$root = ALICEROOT;
		} else {
			$root = dirname(__FILE__);
		}
		require_once $root.DIRECTORY_SEPARATOR.'configuration.php';
	}

	// Try to load the class using the prefix-to-path mapping, also handles plugin path
	foreach($Alice_Class_Map as $prefix => $path)
	{
		if( AliceLoadIfPrefix($class_name, $prefix, $path) ) return;
	}

	return;
}