<?php
/**
 * @package     Solo
 * @copyright   2014-2017 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license     GNU GPL version 3 or later
 */

// Do not remove this line. It is required for Akeeba Solo to work.
define('_AKEEBA', 1);

// Uncomment to enable debug mode. When the debug mode is enabled two-factor authentication with Google Authenticator or YubiKey is disabled.
define('AKEEBADEBUG', 1);

// Do not change these paths unless you know what you're doing
define('APATH_BASE',          __DIR__);
define('APATH_ROOT',          APATH_BASE);

define('APATH_SITE',          APATH_BASE);
define('APATH_THEMES',        APATH_BASE . '/templates');
define('APATH_TRANSLATION',   APATH_BASE . '/languages');