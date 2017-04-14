<?php
/**
 * @package    oakcms
 * @author     Hryvinskyi Volodymyr <script@email.ua>
 * @copyright  Copyright (c) 2015 - 2017. Hryvinskyi Volodymyr
 * @version    0.0.1-beta.0.1
 */

/**
 * Make sure we are being called from WordPress itself
 */

function akeebabackupwp_boot()
{
	require_once dirname(__FILE__) . '/helpers/AkeebaBackupYii.php';

	$baseUrlParts = explode('/', plugins_url('', __FILE__));

	AkeebaBackupWP::$dirName = end($baseUrlParts);
	AkeebaBackupWP::$fileName = basename(__FILE__);
	AkeebaBackupWP::$absoluteFileName = __FILE__;
	AkeebaBackupWP::$wrongPHP = version_compare(PHP_VERSION, AkeebaBackupWP::$minimumPHP, 'lt');

	$aksolowpPath = plugin_dir_path(__FILE__);
	define('AKEEBA_SOLOYII_PATH', $aksolowpPath);
}

akeebabackupwp_boot();

/**
 * Register public plugin hooks
 */
register_activation_hook(__FILE__, array('AkeebaBackupWP', 'install'));

/**
 * Register administrator plugin hooks
 */
if (is_admin() && (!defined('DOING_AJAX') || !DOING_AJAX))
{
	add_action('admin_menu', array('AkeebaBackupWP', 'adminMenu'));
	add_action('network_admin_menu', array('AkeebaBackupWP', 'networkAdminMenu'));

	if (!AkeebaBackupWP::$wrongPHP)
	{
		add_action('init', array('AkeebaBackupWP', 'startSession'), 1);
		add_action('init', array('AkeebaBackupWP', 'loadJavascript'), 1);
		add_action('plugins_loaded', array('AkeebaBackupWP', 'fakeRequest'), 1);
		add_action('wp_logout', array('AkeebaBackupWP', 'endSession'));
		add_action('wp_login', array('AkeebaBackupWP', 'endSession'));
		add_action('in_admin_footer', array('AkeebaBackupWP', 'clearBuffer'));
	}
}
