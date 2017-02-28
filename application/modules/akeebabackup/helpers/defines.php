<?php
/**
 * @package     akeebabackupwp
 * @copyright   2014-2017 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license     GNU GPL version 3 or later
 */

// Do not remove this line. It is required for Akeeba Solo to work.
define('_AKEEBA', 1);

// Uncomment (remove the double slash from) the following line to enable debug mode. When the debug mode is enabled two-factor authentication with Google Authenticator or YubiKey is disabled.
// define('AKEEBADEBUG', 1);

// Uncomment (remove the double slash from) the following line to load Akeeba Backup's copy of jQuery. Necessary on old versions of WordPress (e.g. 3.4) or when a third party plugin corrupts jQuery on your site.
// define('AKEEBA_OVERRIDE_JQUERY', 1);

// Always enable Akeeba Backup for WordPress debug mode when WordPress' debug mode is enabled
if (defined('WP_DEBUG') && !defined('AKEEBADEBUG'))
{
	if (WP_DEBUG)
	{
		define('AKEEBADEBUG', 1);
	}
}

// Do not change these paths unless you know what you're doing
define('APATH_BASE',          realpath(__DIR__ . '/../app'));
define('APATH_ROOT',          APATH_BASE);

define('APATH_SITE',          APATH_BASE);
define('APATH_THEMES',        __DIR__ . '/templates');
define('APATH_TRANSLATION',   APATH_BASE . '/languages');