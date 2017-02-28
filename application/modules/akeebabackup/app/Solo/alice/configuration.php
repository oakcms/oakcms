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
defined('AKEEBAENGINE') or die();

use Akeeba\Engine\Configuration;

/**
 * The Akeeba Engine configuration registry class
 */
class AliceConfiguration extends Configuration
{
	/** @var string Default NameSpace */
	protected $defaultNameSpace = 'global';

	/** @var array Array keys which may contain stock directory definitions */
	protected  $directory_containing_keys = array(
		'akeeba.basic.output_directory'
	);

	/** @var array Keys whose default values should never be overridden */
	protected $protected_nodes = array();

	/** @var array The registry data */
	protected $registry = array();

	/** @var int The currently loaded profile */
	public $activeProfile = null;

	public function __construct()
	{
		// Assisted Singleton pattern
		if(function_exists('debug_backtrace'))
		{
			$caller = debug_backtrace();
			$caller = $caller[1];
			if($caller['class'] != 'AliceFactory')
			{
				trigger_error("You can't create a direct descendant of ".__CLASS__, E_USER_ERROR);
			}
		}

		// Create the default namespace
		$this->makeNameSpace($this->defaultNameSpace);

		// Create a default configuration
		$this->reset();
	}
}

