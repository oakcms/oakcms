<?php
/**
 * @package        solo
 * @copyright      2014-2017 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license        GNU GPL version 3 or later
 */

namespace Solo\Model;

use Awf\Mvc\Model;
use Awf\Filesystem\Factory;
use Akeeba\Engine\Platform;

class Schedule extends Model
{
	public function getPaths()
	{
		$ret = (object)array(
			'cli'      => (object)array(
					'supported' => false,
					'path'      => false
				),
			'altcli'   => (object)array(
					'supported' => false,
					'path'      => false
				),
			'frontend' => (object)array(
					'supported' => false,
					'path'      => false,
				),
			'info'     => (object)array(
					'windows'   => false,
					'php_path'  => false,
					'root_url'  => false,
					'secret'    => '',
					'feenabled' => false,
				)
		);

		// Get the profile ID
		$profileid = Platform::getInstance()->get_active_profile();

		// Get the absolute path to the site's root
		$absolute_root = rtrim(realpath(APATH_BASE), DIRECTORY_SEPARATOR);

		// Is this Windows?
		$ret->info->windows = (DIRECTORY_SEPARATOR == '\\') || (substr(strtoupper(PHP_OS), 0, 3) == 'WIN');

		// Get the pseudo-path to PHP CLI
		if ($ret->info->windows)
		{
			$ret->info->php_path = 'c:\path\to\php.exe';
		}
		else
		{
			$ret->info->php_path = '/path/to/php';
		}

		// Get front-end backup secret key
		$ret->info->secret = Platform::getInstance()->get_platform_configuration_option('frontend_secret_word', '');
		$ret->info->feenabled = Platform::getInstance()->get_platform_configuration_option('frontend_enable', false);

		// Get root URL
		$ret->info->root_url = rtrim(Platform::getInstance()->get_platform_configuration_option('siteurl', ''), '/');

		// Get information for CLI CRON script
		$ret->cli->supported = true;
		$ret->cli->path = $absolute_root . DIRECTORY_SEPARATOR . 'cli' . DIRECTORY_SEPARATOR . 'backup.php';

		if ($profileid != 1)
		{
			$ret->cli->path .= ' --profile=' . $profileid;
		}

		// Get information for alternative CLI CRON script
		$ret->altcli->supported = true;

		if (trim($ret->info->secret) && $ret->info->feenabled)
		{
			$ret->altcli->path = $absolute_root . DIRECTORY_SEPARATOR . 'cli' . DIRECTORY_SEPARATOR . 'altbackup.php';

			if ($profileid != 1)
			{
				$ret->altcli->path .= ' --profile=' . $profileid;
			}
		}

		// Get information for front-end backup
		$ret->frontend->supported = true;

		if (trim($ret->info->secret) && $ret->info->feenabled)
		{
			$option = defined('_JEXEC') ? 'option=com_akeebabackup&' : '';

			$ret->frontend->path = "index.php?{$option}view=remote&key="
				. urlencode($ret->info->secret);

			if ($profileid != 1)
			{
				$ret->frontend->path .= '&profile=' . $profileid;
			}
		}

		return $ret;
	}

    public function getCheckPaths()
    {
        $ret = (object)array(
            'cli'		=> (object)array(
                    'supported'	=> false,
                    'path'		=> false
                ),
            'altcli'	=> (object)array(
                    'supported'	=> false,
                    'path'		=> false
                ),
            'frontend'	=> (object)array(
                    'supported'	=> false,
                    'path'		=> false,
                ),
            'info'		=> (object)array(
                    'windows'	=> false,
                    'php_path'	=> false,
                    'root_url'	=> false,
                    'secret'	=> '',
                    'feenabled' => false,
                )
        );

        // Get the absolute path to the site's root
        $absolute_root = rtrim(realpath(APATH_BASE), DIRECTORY_SEPARATOR);

        // Is this Windows?
        $ret->info->windows = (DIRECTORY_SEPARATOR == '\\') || (substr(strtoupper(PHP_OS),0,3) == 'WIN');

        // Get the pseudo-path to PHP CLI
        if($ret->info->windows)
        {
            $ret->info->php_path = 'c:\path\to\php.exe';
        }
        else
        {
            $ret->info->php_path = '/path/to/php';
        }

        // Get front-end backup secret key
        $ret->info->secret    = Platform::getInstance()->get_platform_configuration_option('frontend_secret_word', '');
        $ret->info->feenabled = Platform::getInstance()->get_platform_configuration_option('frontend_enable', false);
        // Get root URL
        $ret->info->root_url = rtrim(Platform::getInstance()->get_platform_configuration_option('siteurl', ''), '/');

        // Get information for CLI CRON script
		$ret->cli->supported = true;
		$ret->cli->path = $absolute_root.DIRECTORY_SEPARATOR.'cli'.DIRECTORY_SEPARATOR.'check-failed.php';

        // Get information for alternative CLI CRON script
		$ret->altcli->supported = true;
		if(trim($ret->info->secret) && $ret->info->feenabled)
		{
			$ret->altcli->path = $absolute_root.DIRECTORY_SEPARATOR.'cli'.DIRECTORY_SEPARATOR.'altcheck-failed.php';
		}

        // Get information for front-end backup
        $ret->frontend->supported = true;

        if(trim($ret->info->secret) && $ret->info->feenabled)
        {
	        $option = defined('_JEXEC') ? 'option=com_akeebabackup&' : '';

            $ret->frontend->path = "index.php?{$option}view=check&key=" . urlencode($ret->info->secret);
        }

        return $ret;
    }
} 