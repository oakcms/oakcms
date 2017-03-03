<?php
/**
 * @package    oakcms
 * @author     Hryvinskyi Volodymyr <script@email.ua>
 * @copyright  Copyright (c) 2015 - 2016. Hryvinskyi Volodymyr
 * @version    0.0.1-alpha.0.4
 */

namespace Solo;

use Awf\Application\TransparentAuthentication;
use Awf\Filesystem\Factory;
use Awf\Router\Router;
use Awf\Text\Text;
use Awf\Uri\Uri;
use Awf\User\Manager;
use Akeeba\Engine\Platform;

class Application extends \Awf\Application\Application
{
	const secretKeyRelativePath = '/engine/secretkey.php';

	public function initialise()
	{
		$isCMS = defined('WPINC');

		// Put a small marker to indicate that we run inside another CMS
		$this->container->segment->set('insideCMS', $isCMS);

		// Get the target platform information for updates
		$platformVersion = function_exists('get_bloginfo') ? get_bloginfo('version') : '0.0';
		$this->container->segment->set('platformNameForUpdates', 'wordpress');
		$this->container->segment->set('platformVersionForUpdates', $platformVersion);

		// If the PHP session save path is not writeable we will use the 'session' subdirectory inside our tmp directory
		$sessionPath = $this->container->session->getSavePath();

		if (!@is_dir($sessionPath) || !@is_writable($sessionPath))
		{
			$sessionPath = APATH_BASE . '/tmp/session';
			$this->createOrUpdateSessionPath($sessionPath);
			$this->container->session->setSavePath($sessionPath);
		}

		// Set up the template (theme) to use
		if ($isCMS)
		{
			$this->setTemplate('wp');
		}

        // Manually load Solo text files, since we changed them in "com_akeebabackup"
        Text::loadLanguage(null, 'akeebabackup', '.com_akeebabackup.ini', false, $this->container->languagePath);
        Text::loadLanguage('en-GB', 'akeebabackup', '.com_akeebabackup.ini', false, $this->container->languagePath);

		// Load the extra language files
		Text::loadLanguage(null, 'akeeba', '.com_akeeba.ini', false, $this->container->languagePath);
		Text::loadLanguage('en-GB', 'akeeba', '.com_akeeba.ini', false, $this->container->languagePath);

		$configPath = $this->container->appConfig->getDefaultPath();

		// Load the configuration file if it's present
		$this->container->appConfig->loadConfiguration();

		// Apply cookie parameters. This fixes badly configured servers setting the Secure flag on HTTP sites.
		// This block must be AFTER the appConfig->loadConfiguration() call since we need to set the URIs from
		// the AKEEBA_SOLOYII_SITEURL and AKEEBA_SOLOYII_URL constants, set from WordPress functions during bootstrap.
		// See: Solo\Application::applySessionTimeout()
		$sessionTimeout = (int)$this->container->appConfig->get('session_timeout', 1440);
		$uri = new Uri(Uri::base(false, $this->container), $this);
		$this->container->session->setCookieParams(array(
			'lifetime'	=> $sessionTimeout * 60,
			'path'		=> $uri->getPath(),
			'domain'	=> $uri->getHost(),
			'secure'	=> $uri->getScheme() == 'https',
			'httponly'	=> true,
		));

		// Load Akeeba Engine's settings encryption preferences
		$secretKeyFile = $this->container->basePath . static::secretKeyRelativePath;

		if (@file_exists($secretKeyFile))
		{
			require_once $secretKeyFile;
		}

		// Load Akeeba Engine's configuration
		try
		{
			Platform::getInstance()->load_configuration();
		}
		catch (\Exception $e)
		{
			// Ignore database exceptions, they simply mean we need to install or update the database
		}

		// Get the view. Necessary to go through $this->getContainer()->input as it may have already changed
		$view = $this->getContainer()->input->getCmd('view', '');

		// Attach the user privileges to the user manager
		$this->container->appConfig->set('user_table', '#__ak_users');
		$manager = $this->container->userManager;
		$manager->registerPrivilegePlugin('akeeba', '\\Solo\\Application\\WordpressUserPrivileges');

		// Set up the media query key
		$this->getContainer()->mediaQueryKey = md5(microtime(false));
		$isDebug = !defined('AKEEBADEBUG');
		$hasVersion = defined('AKEEBABACKUP_VERSION') && defined('AKEEBABACKUP_DATE');
		$isDevelopment = $hasVersion ? ((strpos(AKEEBABACKUP_VERSION, 'svn') !== false) || (strpos(AKEEBABACKUP_VERSION, 'dev') !== false) || (strpos(AKEEBABACKUP_VERSION, 'rev') !== false)) : true;

		if (!$isDebug && !$isDevelopment && $hasVersion)
		{
			$this->getContainer()->mediaQueryKey = md5(AKEEBABACKUP_VERSION . AKEEBABACKUP_DATE);
		}
	}

	/**
	 * Language file processing callback. It converts _QQ_ to " and replaces the product name in the legacy INI files
	 * imported from Akeeba Backup for Joomla!.
	 *
	 * @param   string  $filename  The full path to the file being loaded
	 * @param   array   $strings   The key/value array of the translations
	 *
	 * @return  boolean|array  False to prevent loading the file, or array of processed language string, or true to
	 *                         ignore this processing callback.
	 */
	public function processLanguageIniFile($filename, $strings)
	{
		foreach ($strings as $k => $v)
		{
			$v = str_replace('_QQ_', '"', $v);
			$v = str_replace('Akeeba Solo', 'Akeeba Backup', $v);
			$v = str_replace('Akeeba Backup', 'Akeeba Backup for WordPress', $v);
			$v = str_replace('Joomla!', 'WordPress', $v);
			$v = str_replace('Joomla', 'WordPress', $v);
			$strings[$k] = $v;
		}

		return $strings;
	}

	/**
	 * Creates or updates the custom session save path
	 *
	 * @param   string   $path    The custom session save path
	 * @param   boolean  $silent  Should I suppress all errors?
	 *
	 * @return  void
	 *
	 * @throws \Exception  If $silent is set to false
	 */
	public function createOrUpdateSessionPath($path, $silent = true)
	{
		try
		{
			$fs = $this->container->fileSystem;
			$protectFolder = false;

			if (!@is_dir($path))
			{
				$fs->mkdir($path, 0777);
			}
			elseif (!is_writeable($path))
			{
				$fs->chmod($path, 0777);
				$protectFolder = true;
			}
			else
			{
				if (!@file_exists($path . '/.htaccess'))
				{
					$protectFolder = true;
				}

				if (!@file_exists($path . '/web.config'))
				{
					$protectFolder = true;
				}
			}

			if ($protectFolder)
			{
				$fs->copy($this->container->basePath . '/.htaccess', $path . '/.htaccess');
				$fs->copy($this->container->basePath . '/web.config', $path . '/web.config');

				$fs->chmod($path . '/.htaccess', 0644);
				$fs->chmod($path . '/web.config', 0644);
			}
		}
		catch (\Exception $e)
		{
			if (!$silent)
			{
				throw $e;
			}
		}
	}
}
