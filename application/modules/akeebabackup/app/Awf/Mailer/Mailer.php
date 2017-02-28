<?php
/**
 * @package     Awf
 * @copyright   2014-2017 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license     GNU GPL version 3 or later
 *
 * This file is a heavily modified version of the JMail class found in Joomla! 3. It is a wrapper to PHPMailer.
 */

namespace Awf\Mailer;

use Awf\Application\Application;
use Awf\Container\Container;
use Awf\Text\Text;

if (!function_exists('PHPMailerAutoload'))
{
	require_once __DIR__ . '/phpmailer/PHPMailerAutoload.php';
}

class Mailer extends \PHPMailer
{

	/**
	 * @var    array[Mailer]  Mailer instances
	 */
	protected static $instances = array();

	/**
	 * @var    string  Character set of the message
	 */
	public $CharSet = 'utf-8';

	/**
	 * The container this mailer is attached to
	 *
	 * @var   Container
	 */
	protected $container;

	public function __construct($container = null)
	{
		if (!is_object($container))
		{
			$container = Application::getInstance()->getContainer();
		}

		parent::__construct();

		$config = $container->appConfig;

		$smtpauth   = ($config->get('mail.smtpauth') == 0) ? null : 1;
		$smtpuser   = $config->get('mail.smtpuser');
		$smtppass   = $config->get('mail.smtppass');
		$smtphost   = $config->get('mail.smtphost');
		$smtpsecure = $config->get('mail.smtpsecure');
		$smtpport   = $config->get('mail.smtpport');
		$mailfrom   = $config->get('mail.mailfrom');
		$fromname   = $config->get('mail.fromname');
		$mailer     = $config->get('mail.mailer');

		$this->SetFrom($mailfrom, $fromname);
		$this->container = $container;

		switch ($mailer)
		{
			case 'smtp':
				$this->useSMTP($smtpauth, $smtphost, $smtpuser, $smtppass, $smtpsecure, $smtpport);
				break;

			case 'sendmail':
				$this->IsSendmail();
				break;

			default:
				$this->IsMail();
				break;
		}
	}

	/**
	 * Send the mail
	 *
	 * @return  mixed  True if successful
	 *
	 * @throws  \RuntimeException
	 */
	public function Send()
	{
		$config = $this->container->appConfig;

		if ($config->get('mail.online', true))
		{
			if (($this->Mailer == 'mail') && !function_exists('mail'))
			{
				throw new \RuntimeException(sprintf('%s::Send mail not enabled.', get_class($this)));
			}

			@$result = parent::Send();

			if ($result == false)
			{
				throw new \RuntimeException(sprintf('%s::Send failed: "%s".', get_class($this), $this->ErrorInfo));
			}

			return $result;
		}
		else
		{
			$this->container->application->enqueueMessage(Text::_('AWF_MAIL_FUNCTION_OFFLINE'));

			return false;
		}
	}

	/**
	 * Set the email sender
	 *
	 * @param   mixed $from   email address and Name of sender
	 *                        <code>array([0] => email Address, [1] => Name)</code>
	 *                        or as a string
	 *
	 * @return  Mailer  Returns this object for chaining.
	 *
	 * @throws  \UnexpectedValueException
	 */
	public function setSender($from)
	{
		if (is_array($from))
		{
			// If $from is an array we assume it has an address and a name
			if (isset($from[2]))
			{
				// If it is an array with entries, use them
				$this->SetFrom($from[0], $from[1], (bool)$from[2]);
			}
			else
			{
				$this->SetFrom($from[0], $from[1]);
			}
		}
		elseif (is_string($from))
		{
			// If it is a string we assume it is just the address
			$this->SetFrom($from);
		}
		else
		{
			throw new \UnexpectedValueException(sprintf('Invalid email Sender: %s, Mailer::setSender(%s)', $from));
		}

		return $this;
	}

	/**
	 * Set the email subject
	 *
	 * @param   string $subject Subject of the email
	 *
	 * @return  Mailer  Returns this object for chaining.
	 */
	public function setSubject($subject)
	{
		$this->Subject = $subject;

		return $this;
	}

	/**
	 * Set the email body
	 *
	 * @param   string $content Body of the email
	 *
	 * @return  Mailer  Returns this object for chaining.
	 */
	public function setBody($content)
	{
		$this->Body = $content;

		return $this;
	}

	/**
	 * Add recipients to the email.
	 *
	 * @param   mixed  $recipient Either a string or array of strings [email address(es)]
	 * @param   mixed  $name      Either a string or array of strings [name(s)]
	 * @param   string $method    The parent method's name.
	 *
	 * @return  Mailer  Returns this object for chaining.
	 *
	 * @throws  \InvalidArgumentException
	 */
	protected function add($recipient, $name = '', $method = 'AddAddress')
	{
		// If the recipient is an array, add each recipient... otherwise just add the one
		if (is_array($recipient))
		{
			if (is_array($name))
			{
				$combined = array_combine($recipient, $name);

				if ($combined === false)
				{
					throw new \InvalidArgumentException("The number of elements for each array isn't equal.");
				}

				foreach ($combined as $recipientEmail => $recipientName)
				{
					call_user_func('parent::' . $method, $recipientEmail, $recipientName);
				}
			}
			else
			{
				foreach ($recipient as $to)
				{
					call_user_func('parent::' . $method, $to, $name);
				}
			}
		}
		else
		{
			call_user_func('parent::' . $method, $recipient, $name);
		}

		return $this;
	}

	/**
	 * Add recipients to the email
	 *
	 * @param   mixed $recipient Either a string or array of strings [email address(es)]
	 * @param   mixed $name      Either a string or array of strings [name(s)]
	 *
	 * @return  Mailer  Returns this object for chaining.
	 */
	public function addRecipient($recipient, $name = '')
	{
		$this->add($recipient, $name, 'AddAddress');

		return $this;
	}

	/**
	 * Add carbon copy recipients to the email
	 *
	 * @param   mixed $cc   Either a string or array of strings [email address(es)]
	 * @param   mixed $name Either a string or array of strings [name(s)]
	 *
	 * @return  Mailer  Returns this object for chaining.
	 */
	public function addCC($cc, $name = '')
	{
		// If the carbon copy recipient is an array, add each recipient... otherwise just add the one
		if (isset($cc))
		{
			$this->add($cc, $name, 'AddCC');
		}

		return $this;
	}

	/**
	 * Add blind carbon copy recipients to the email
	 *
	 * @param   mixed $bcc  Either a string or array of strings [email address(es)]
	 * @param   mixed $name Either a string or array of strings [name(s)]
	 *
	 * @return  Mailer  Returns this object for chaining.
	 */
	public function addBCC($bcc, $name = '')
	{
		// If the blind carbon copy recipient is an array, add each recipient... otherwise just add the one
		if (isset($bcc))
		{
			$this->add($bcc, $name, 'AddBCC');
		}

		return $this;
	}

	/**
	 * Add file attachments to the email
	 *
	 * @param   mixed  $attachment  Either a string or array of strings [filenames]
	 * @param   mixed  $name        Either a string or array of strings [names]
	 * @param   mixed  $encoding    The encoding of the attachment
	 * @param   mixed  $type        The mime type
	 * @param   string $disposition The disposition of the attachment (attachment, inline, etc)
	 *
	 * @return  Mailer  Returns this object for chaining.
	 *
	 * @throws  \InvalidArgumentException
	 */
	public function addAttachment($attachment, $name = '', $encoding = 'base64', $type = 'application/octet-stream', $disposition = 'attachment')
	{
		// If the file attachments is an array, add each file... otherwise just add the one
		if (isset($attachment))
		{
			if (is_array($attachment))
			{
				if (!empty($name) && count($attachment) != count($name))
				{
					throw new \InvalidArgumentException("The number of attachments must be equal with the number of name");
				}

				foreach ($attachment as $key => $file)
				{
					if (!empty($name))
					{
						parent::AddAttachment($file, $name[$key], $encoding, $type, $disposition);
					}
					else
					{
						parent::AddAttachment($file, $name, $encoding, $type, $disposition);
					}
				}
			}
			else
			{
				parent::AddAttachment($attachment, $name, $encoding, $type, $disposition);
			}
		}

		return $this;
	}

	/**
	 * Add Reply to email address(es) to the email
	 *
	 * @param   mixed $replyTo Either a string or array of strings [email address(es)]
	 * @param   mixed $name    Either a string or array of strings [name(s)]
	 *
	 * @return  Mailer  Returns this object for chaining.
	 */
	public function addReplyTo($replyTo, $name = '')
	{
		$this->add($replyTo, $name, 'AddReplyTo');

		return $this;
	}

	/**
	 * Sets message type to HTML
	 *
	 * @param   boolean $isHTML Boolean true or false.
	 *
	 * @return  Mailer  Returns this object for chaining.
	 */
	public function isHtml($isHTML = true)
	{
		parent::IsHTML($isHTML);

		return $this;
	}

	/**
	 * Use sendmail for sending the email
	 *
	 * @param   string $sendmail Path to sendmail [optional]
	 *
	 * @return  boolean  True on success
	 */
	public function useSendmail($sendmail = null)
	{
		$this->Sendmail = $sendmail;

		if (!empty($this->Sendmail))
		{
			$this->IsSendmail();

			return true;
		}
		else
		{
			$this->IsMail();

			return false;
		}
	}

	/**
	 * Use SMTP for sending the email
	 *
	 * @param   string  $auth   SMTP Authentication [optional]
	 * @param   string  $host   SMTP Host [optional]
	 * @param   string  $user   SMTP Username [optional]
	 * @param   string  $pass   SMTP Password [optional]
	 * @param   string  $secure Use secure methods
	 * @param   integer $port   The SMTP port
	 *
	 * @return  boolean  True on success
	 */
	public function useSMTP($auth = null, $host = null, $user = null, $pass = null, $secure = null, $port = 25)
	{
		$this->SMTPAuth = $auth;
		$this->Host     = $host;
		$this->Username = $user;
		$this->Password = $pass;
		$this->Port     = $port;

		if ($secure == 'ssl' || $secure == 'tls')
		{
			$this->SMTPSecure = $secure;
		}

		if (($this->SMTPAuth !== null && $this->Host !== null && $this->Username !== null && $this->Password !== null)
			|| ($this->SMTPAuth === null && $this->Host !== null)
		)
		{
			$this->IsSMTP();

			return true;
		}
		else
		{
			$this->IsMail();

			return false;
		}
	}

	/**
	 * Function to send an email
	 *
	 * @param   string  $from        From email address
	 * @param   string  $fromName    From name
	 * @param   mixed   $recipient   Recipient email address(es)
	 * @param   string  $subject     email subject
	 * @param   string  $body        Message body
	 * @param   boolean $mode        false = plain text, true = HTML
	 * @param   mixed   $cc          CC email address(es)
	 * @param   mixed   $bcc         BCC email address(es)
	 * @param   mixed   $attachment  Attachment file name(s)
	 * @param   mixed   $replyTo     Reply to email address(es)
	 * @param   mixed   $replyToName Reply to name(s)
	 *
	 * @return  boolean  True on success
	 */
	public function sendMail($from, $fromName, $recipient, $subject, $body, $mode = false, $cc = null, $bcc = null, $attachment = null,
							 $replyTo = null, $replyToName = null)
	{
		$this->setSubject($subject);
		$this->setBody($body);

		// Are we sending the email as HTML?
		if ($mode)
		{
			$this->IsHTML(true);
		}

		$this->addRecipient($recipient);
		$this->addCC($cc);
		$this->addBCC($bcc);
		$this->addAttachment($attachment);

		// Take care of reply email addresses
		if (is_array($replyTo))
		{
			$numReplyTo = count($replyTo);

			for ($i = 0; $i < $numReplyTo; $i++)
			{
				$this->addReplyTo(array($replyTo[$i], $replyToName[$i]));
			}
		}
		elseif (isset($replyTo))
		{
			$this->addReplyTo(array($replyTo, $replyToName));
		}

		// Add sender to replyTo only if no replyTo received
		$autoReplyTo = (empty($this->ReplyTo)) ? true : false;
		$this->setSender(array($from, $fromName, $autoReplyTo));

		return $this->Send();
	}
}