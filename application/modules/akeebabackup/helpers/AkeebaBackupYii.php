<?php
/**
 * @package    oakcms
 * @author     Hryvinskyi Volodymyr <script@email.ua>
 * @copyright  Copyright (c) 2015 - 2017. Hryvinskyi Volodymyr
 * @version    0.0.1-beta.0.1
 */

namespace app\modules\akeebabackup\helpers;

class AkeebaBackupYii
{
    /** @var string The name of the wp-content/plugins directory we live in */
    public static $dirName = 'akeebabackup';

    /** @var string The name of the main plugin file */
    public static $fileName = 'akeebabackup.php';

    /** @var string Absolute filename to self */
    public static $absoluteFileName = null;

    /** @var array List of all JS files we can possibly load */
    public static $jsFiles = [];

    /** @var array List of all CSS files we can possibly load */
    public static $cssFiles = [];

    /** @var bool Do we have an outdated PHP version? */
    public static $wrongPHP = false;

    /** @var string Minimum PHP version */
    public static $minimumPHP = '5.3.3';

    protected static $loadedScripts = [];

    /**
     * Store the unquoted request variables to prevent WordPress from killing JSON requests.
     */
    public static function fakeRequest()
    {
        // See http://stackoverflow.com/questions/8949768/with-magic-quotes-disabled-why-does-php-wordpress-continue-to-auto-escape-my
        global $_REAL_REQUEST;
        $_REAL_REQUEST = $_REQUEST;
    }

    /**
     * Start a session (if not already started). It also takes care of our magic trick for displaying raw views without
     * rendering WordPress' admin interface.
     */
    public static function startSession()
    {
        if (!session_id()) {
            session_start();
        }

        $page = self::$dirName . '/' . self::$fileName;

        // Is this an Akeeba Solo page?
        if (isset($_REQUEST['page']) && ($_REQUEST['page'] == $page)) {
            // Is it a format=raw, format=json or tmpl=component page?
            if (
                (isset($_REQUEST['format']) && ($_REQUEST['format'] == 'raw')) ||
                (isset($_REQUEST['format']) && ($_REQUEST['format'] == 'json')) ||
                (isset($_REQUEST['tmpl']) && ($_REQUEST['tmpl'] == 'component'))
            ) {
                define('AKEEBA_SOLOYII_OBFLAG', 1);
                @ob_start();
            }
        }
    }

    /**
     * Load template scripts with fallback to our own copies (useful for support)
     */
    public static function loadJavascript()
    {
        if (!session_id()) {
            session_start();
        }

        $page = self::$dirName . '/' . self::$fileName;

        // Is this an Akeeba Solo page?
        if (!isset($_REQUEST['page']) || !($_REQUEST['page'] == $page)) {
            return;
        }

        if (
            defined('AKEEBA_OVERRIDE_JQUERY') &&
            @file_exists(dirname(AkeebaBackupWP::$absoluteFileName) . '/app/media/js/jquery.min.js') &&
            @file_exists(dirname(AkeebaBackupWP::$absoluteFileName) . '/app/media/js/jquery-migrate.min.js')
        ) {
            AkeebaBackupWP::enqueueHeadScript(plugins_url('app/media/js/jquery.min.js', self::$absoluteFileName));
            AkeebaBackupWP::enqueueHeadScript(plugins_url('app/media/js/akjqnamespace.min.js', self::$absoluteFileName));
            AkeebaBackupWP::enqueueHeadScript(plugins_url('app/media/js/jquery-migrate.min.js', self::$absoluteFileName));
            AkeebaBackupWP::enqueueHeadScript(plugins_url('app/media/js/bootstrap.min.js', self::$absoluteFileName));
        } else {
            AkeebaBackupWP::enqueueHeadScript(plugins_url('app/media/js/akjqnamespace.min.js', self::$absoluteFileName));
            AkeebaBackupWP::enqueueHeadScript(plugins_url('app/media/js/bootstrap.min.js', self::$absoluteFileName));
        }

        $theEntireUniverseOfScripts = [
            'piecon', 'datepicker/bootstrap-datepicker', 'solo/gui-helpers',
            'solo/alice', 'solo/backup', 'solo/configuration', 'solo/dbfilters', 'solo/encryption', 'solo/extradirs',
            'solo/fsfilters', 'solo/multidb', 'solo/regexdbfilters', 'solo/regexfsfilters', 'solo/restore', 'solo/setup',
            'solo/stepper', 'solo/system', 'solo/update', 'solo/wizard',
        ];

        $relPath = __DIR__ . '/../';

        foreach ($theEntireUniverseOfScripts as $script) {
            $scriptPath = 'app/media/js/' . $script . '.min.js';

            if (file_exists($relPath . $scriptPath)) {
                AkeebaBackupWP::enqueueHeadScript(plugins_url($scriptPath, self::$absoluteFileName));
            }
        }
    }

    /**
     * Terminate a session if it's already started
     */
    public static function endSession()
    {
        if (session_id()) {
            session_destroy();
        }
    }

    /**
     * Part of our magic trick for displaying raw views without rendering WordPress' admin interface.
     */
    public static function clearBuffer()
    {
        if (defined('AKEEBA_SOLOYII_OBFLAG')) {
            @ob_end_clean();
            exit(0);
        }
    }

    /**
     * Installation hook. Creates the database tables if they do not exist and performs any post-installation work
     * required.
     */
    public static function install()
    {
        // Require WordPress 3.1 or later
        if (version_compare(get_bloginfo('version'), '3.1', 'lt')) {
            deactivate_plugins(self::$fileName);
        }

        // Register the uninstallation hook
        register_uninstall_hook(self::$absoluteFileName, ['AkeebaBackupWP', 'uninstall']);
    }

    /**
     * Uninstallation hook
     *
     * Removes database tables if they exist and performs any post-uninstallation work required.
     *
     * @return  void
     */
    public static function uninstall()
    {
        // @todo Uninstall database tables
    }

    /**
     * Create the administrator menu for Akeeba Backup
     */
    public static function adminMenu()
    {
        if (is_multisite()) {
            return;
        }

        $page_hook_suffix = add_menu_page('Akeeba Backup', 'Akeeba Backup', 'edit_others_posts', self::$absoluteFileName, ['AkeebaBackupWP', 'boot'], plugins_url('app/media/logo/solo-24-white.png', self::$absoluteFileName));

        //add_action('admin_print_scripts-' . $page_hook_suffix, array(__CLASS__, 'adminPrintScripts'));
    }

    /**
     * Create the blog network administrator menu for Akeeba Backup
     */
    public static function networkAdminMenu()
    {
        if (!is_multisite()) {
            return;
        }

        add_menu_page('Akeeba Backup', 'Akeeba Backup', 'manage_options', self::$absoluteFileName, ['AkeebaBackupWP', 'boot'], plugins_url('app/media/logo/solo-24-white.png', self::$absoluteFileName));
    }

    /**
     * Boots the Akeeba Backup application
     */
    public static function boot()
    {
        if (self::$wrongPHP) {
            include_once dirname(self::$absoluteFileName) . '/helpers/wrongphp.php';

            return;
        }

        $network = is_multisite() ? 'network/' : '';

        if (!defined('AKEEBA_SOLOYII_ROOTURL')) {
            define('AKEEBA_SOLOYII_ROOTURL', site_url());
        }

        if (!defined('AKEEBA_SOLOYII_URL')) {
            $bootstrapUrl = admin_url() . $network . 'admin.php?page=' . self::$dirName . '/' . self::$fileName;
            define('AKEEBA_SOLOYII_URL', $bootstrapUrl);
        }

        if (!defined('AKEEBA_SOLOYII_SITEURL')) {
            $baseUrl = plugins_url('app/index.php', self::$absoluteFileName);
            define('AKEEBA_SOLOYII_SITEURL', substr($baseUrl, 0, -10));
        }

        include_once dirname(self::$absoluteFileName) . '/helpers/bootstrap.php';
    }

    /**
     * Enqueues a Javascript file for loading
     *
     * @param   string $url The URL of the Javascript file to load
     */
    public static function enqueueScript($url)
    {
        if (in_array($url, self::$loadedScripts)) {
            return;
        }

        self::$loadedScripts[] = $url;

        if (!defined('AKEEBABACKUP_VERSION')) {
            @include_once dirname(self::$absoluteFileName) . '/app/version.php';
        }

        $handle = 'akjs' . md5($url);
        $dependencies = ['jquery', 'jquery-migrate'];

        // When we override the loading of jQuery do not depend on WP's jQuery being loaded
        if (defined('AKEEBA_OVERRIDE_JQUERY') && AKEEBA_OVERRIDE_JQUERY) {
            $dependencies = [];
        }

        //wp_enqueue_script($handle, $url, $dependencies, AKEEBABACKUP_VERSION, false);

        $version = AKEEBABACKUP_VERSION;
        echo "<script type=\"text/javascript\" src=\"$url?$version\"></script>\n";
    }

    /**
     * Enqueues a Javascript file for loading in the head
     *
     * @param   string $url The URL of the Javascript file to load
     */
    public static function enqueueHeadScript($url)
    {
        if (in_array($url, self::$loadedScripts)) {
            return;
        }

        self::$loadedScripts[] = $url;

        if (!defined('AKEEBABACKUP_VERSION')) {
            @include_once dirname(self::$absoluteFileName) . '/app/version.php';
        }

        $handle = 'akjs' . md5($url);
        $dependencies = ['jquery', 'jquery-migrate'];

        // When we override the loading of jQuery do not depend on WP's jQuery being loaded
        if (defined('AKEEBA_OVERRIDE_JQUERY') && AKEEBA_OVERRIDE_JQUERY) {
            $dependencies = [];
        }

        wp_enqueue_script($handle, $url, $dependencies, AKEEBABACKUP_VERSION, false);
    }

    /**
     * Enqueues a CSS file for loading
     *
     * @param   string $url The URL of the CSS file to load
     */
    public static function enqueueStyle($url)
    {
        if (!defined('AKEEBABACKUP_VERSION')) {
            @include_once dirname(self::$absoluteFileName) . '/app/version.php';
        }

        $handle = 'akcss' . md5($url);
        //wp_enqueue_style($handle, $url, [], AKEEBABACKUP_VERSION);
    }
}
