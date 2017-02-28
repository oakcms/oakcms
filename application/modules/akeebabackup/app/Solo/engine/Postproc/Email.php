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

namespace Akeeba\Engine\Postproc;

// Protection against direct access
defined('AKEEBAENGINE') or die();

use Akeeba\Engine\Factory;
use Akeeba\Engine\Platform;
use Psr\Log\LogLevel;

class Email extends Base
{
	public function processPart($absolute_filename, $upload_as = null)
	{
		// Retrieve engine configuration data
		$config = Factory::getConfiguration();

		$address = trim($config->get('engine.postproc.email.address', ''));
		$subject = $config->get('engine.postproc.email.subject', '0');

		// Sanity checks
		if (empty($address))
		{
			$this->setError('You have not set up a recipient\'s email address for the backup files');

			return false;
		}

		// Send the file
		$basename = empty($upload_as) ? basename($absolute_filename) : $upload_as;
		Factory::getLog()->log(LogLevel::INFO, "Preparing to email $basename to $address");
		if (empty($subject))
		{
			if (class_exists('JText'))
			{
				$subject = \JText::_('COM_AKEEBA_COMMON_EMAIL_DEAFULT_SUBJECT');
			}
			elseif (class_exists('\Awf\Text\Text'))
			{
				$subject = \Awf\Text\Text::_('COM_AKEEBA_COMMON_EMAIL_DEAFULT_SUBJECT');
			}
			else
			{
				$subject = "You have a new backup part";
			}
		}
		$body = "Emailing $basename";

		Factory::getLog()->log(LogLevel::DEBUG, "Subject: $subject");
		Factory::getLog()->log(LogLevel::DEBUG, "Body: $body");

		$result = Platform::getInstance()->send_email($address, $subject, $body, $absolute_filename);

		// Return the result
		if ($result !== true)
		{
			// An error occurred
			$this->setError($result);

			// Notify that we failed
			return false;
		}
		else
		{
			// Return success
			Factory::getLog()->log(LogLevel::INFO, "Email sent successfully");

			return true;
		}
	}
}