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

namespace Akeeba\Engine\Platform\Exception;

// Protection against direct access
use Akeeba\Engine\Platform;
use Exception;
use RuntimeException;

defined('AKEEBAENGINE') or die();

/**
 * Thrown when the settings cannot be decrypted, e.g. when the server no longer has encyrption enabled or the key has
 * changed.
 */
class DecryptionException extends RuntimeException
{
	public function __construct($message = null, $code = 500, Exception $previous = null)
	{
		if (empty($message))
		{
			$message = Platform::getInstance()->translate('COM_AKEEBA_CONFIG_ERR_DECRYPTION');
		}

		parent::__construct($message, $code, $previous);
	}

}