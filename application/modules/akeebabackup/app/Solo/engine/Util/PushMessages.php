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

namespace Akeeba\Engine\Util;

// Protection against direct access
defined('AKEEBAENGINE') or die();

use Akeeba\Engine\Factory;
use Akeeba\Engine\Platform;
use Akeeba\Engine\Util\Pushbullet\Connector;

class PushMessages
{
	/**
	 * The PushBullet connector
	 *
	 * @var Connector
	 */
	private $connector = null;

	/**
	 * Should we send push messages?
	 *
	 * @var bool
	 */
	private $enabled = true;

	/**
	 * Creates the push messaging object
	 */
	public function __construct()
	{
		$pushPreference = Platform::getInstance()->get_platform_configuration_option('push_preference', '0');
		$apiKey = Platform::getInstance()->get_platform_configuration_option('push_apikey', '');

		// No API key? No push messages are enabled, so no point continuing really...
		if (empty($apiKey))
		{
			$pushPreference = 0;
		}

		// We use a switch in case we add support for more push APIs in the future. The push_preference platform
		// option will tell us which service to use. In that case we'll have to refactor this class, but the public
		// API will remain the same.
		switch ($pushPreference)
		{
			default:
			case 0:
				$this->enabled = false;
				break;

			case 1:
				try
				{
					$this->connector = new Connector($apiKey);
					$this->connector->getDevices();
				}
				catch (\Exception $e)
				{
					Factory::getLog()->warning('Push messages cannot be sent. Error received when trying to establish PushBullet connection:' . $e->getMessage());
					$this->enabled = false;
				}
				break;
		}
	}

	/**
	 * Sends a push message to all connected devices. The intent is to provide the user with an information message,
	 * e.g. notify them about the progress of the backup.
	 *
	 * @param   string  $subject  The subject of the message, shown in the lock screen. Keep it short.
	 * @param   string  $details  Long(er) description of what the message is about. Plain text (no HTML).
	 *
	 * @return  void
	 */
	public function message($subject, $details = null)
	{
		if (!$this->enabled)
		{
			return;
		}

		try
		{
			$this->connector->pushNote('', $subject, $details);
		}
		catch (\Exception $e)
		{
			Factory::getLog()->warning('Push messages suspended. Error received when trying to send push message:' . $e->getMessage());
			$this->enabled = false;
		}
	}

	/**
	 * Sends a push message, containing a URL/URI, to all connected devices. The URL will be rendered as something
	 * clickable on most devices.
	 *
	 * @param   string  $url      The URL/URI
	 * @param   string  $subject  The subject of the message, shown in the lock screen. Keep it short.
	 * @param   string  $details  Long(er) description of what the message is about. Plain text (no HTML).
	 *
	 * @return  void
	 */
	public function link($url, $subject, $details = null)
	{
		if (!$this->enabled)
		{
			return;
		}

		try
		{
			$this->connector->pushLink('', $subject, $url, $details);
		}
		catch (\Exception $e)
		{
			Factory::getLog()->warning('Push messages suspended. Error received when trying to send push message with a link:' . $e->getMessage());
			$this->enabled = false;
		}

	}
}