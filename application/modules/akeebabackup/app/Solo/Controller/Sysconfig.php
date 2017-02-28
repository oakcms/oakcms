<?php
/**
 * @package		solo
 * @copyright	2014-2017 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license		GNU GPL version 3 or later
 */

namespace Solo\Controller;


use Awf\Router\Router;
use Awf\Text\Text;

class Sysconfig extends ControllerDefault
{
	public function save()
	{
		$this->csrfProtection();

		$urlredirect = $this->input->get('urlredirect', null, 'raw');

		$data = $this->input->getData();

		unset($data['view']);
		unset($data['task']);
		unset($data['layout']);
		unset($data['token']);

		if (isset($data['urlredirect']))
		{
			unset($data['urlredirect']);
		}

		$keys = array_keys($data);
		$checkboxKeys = array(
			'mail.online', 'mail.smtpauth', 'options.frontend_enable', 'options.frontend_email_on_finish',
			'options.usesvnsource', 'options.displayphpwarning'
		);

		foreach ($keys as $key)
		{
			if (strpos($key, 'fs_') === 0)
			{
				$data['fs.' . substr($key, 3)] = $data[$key];
				unset($data[$key]);
				$key = 'fs.' . substr($key, 3);
			}
			elseif (strpos($key, 'mail_') === 0)
			{
				$data['mail.' . substr($key, 5)] = $data[$key];
				unset($data[$key]);
				$key = 'mail.' . substr($key, 5);
			}

			if (in_array($key, $checkboxKeys))
			{
				$data[$key] = in_array($data[$key], array('on', 'yes', 'true', 1, true));
			}
			elseif ($key == 'options')
			{
				foreach ($data[$key] as $k => $v)
				{
					$check = 'options.' . $k;

					if (in_array($check, $checkboxKeys))
					{
						$data[$key][$k] = in_array($data[$key][$k], array('on', 'yes', 'true', 1, true));
					}
				}
			}
		}

		$config = $this->container->appConfig;

		foreach ($data as $k => $v)
		{
			if (is_array($v))
			{
				foreach ($v as $sk => $sv)
				{
					$config->set($k . '.' . $sk, $sv);
				}
			}
			else
			{
				$config->set($k, $v);
			}
		}

		$this->container->appConfig->saveConfiguration();

		if ($urlredirect)
		{
			$url = base64_decode($urlredirect);
		}
		else
		{
			$url = $this->container->router->route('index.php');
		}

		$this->setRedirect($url, Text::_('SOLO_SYSCONFIG_SAVE'));
	}

	public function apply()
	{
		$this->save();

		$url = $this->container->router->route('index.php?view=sysconfig');

		$this->setRedirect($url, Text::_('SOLO_SYSCONFIG_SAVE'));
	}

    public function testemail()
    {
        $config = $this->container->appConfig;
        $mailer = $this->container->mailer;
        $user   = $this->container->userManager->getUser();

        $from     = $config->get('mail.mailfrom');
        $fromName = $config->get('mail.fromname');

        $subject  = Text::sprintf('SOLO_SYSCONFIG_TESTEMAIL_SUBJECT', $this->container->appConfig->get('base_url', ''));
        $body     = Text::_('SOLO_SYSCONFIG_TESTEMAIL_BODY');

        try
        {
            $mailer->sendMail($from, $fromName, $user->getEmail(), $subject, $body);
            $type = 'info';
            $msg  = Text::_('SOLO_SYSCONFIG_TESTMEMAIL_SENT');
        }
        catch(\Exception $e)
        {
            $type = 'error';
            $msg  = $e->getMessage();
        }

        $this->setRedirect($this->container->router->route('index.php?view=sysconfig'), $msg, $type);
    }
}