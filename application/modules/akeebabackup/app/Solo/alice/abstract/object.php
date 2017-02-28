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

/**
 * The base class of Akeeba Engine objects. Allows for error and warnings logging
 * and propagation. Largely based on the Joomla! 1.5 JObject class.
 */

abstract class AliceAbstractObject extends \Akeeba\Engine\Base\Object
{
	/**
	 * Public constructor, makes sure we are instanciated only by the factory class
	 */
	public function __construct()
	{
        // @TODO what about removing this check?
		// Assisted Singleton pattern
		if(function_exists('debug_backtrace'))
		{
			$caller=debug_backtrace();
			if(
				($caller[1]['class'] != 'AliceFactory') &&
				($caller[2]['class'] != 'AliceFactory') &&
				($caller[3]['class'] != 'AliceFactory') &&
				($caller[4]['class'] != 'AliceFactory')
			) {
				trigger_error("You can't create direct descendants of ".__CLASS__, E_USER_ERROR);
			}
		}
	}
}