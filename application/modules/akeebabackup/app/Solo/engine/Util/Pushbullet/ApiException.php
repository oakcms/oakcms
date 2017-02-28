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

namespace Akeeba\Engine\Util\Pushbullet;

// Protection against direct access
defined('AKEEBAENGINE') or die();

class ApiException extends \Exception
{
	// Exception thrown by Pushbullet
}