<?php
/*
Plugin Name: Akeeba Backup for WordPress
Plugin URI: https://www.akeebabackup.com
Description: The complete backup solution for WordPress
Version: 2.1.0.b1
Author: Akeeba Ltd
Author URI: https://www.akeebabackup.com
License: GPLv3
*/

/*
 * Copyright 2014-2017 Akeeba Ltd
 * 
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 * 
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * You contact Akeeba Ltd through our contact page:
 * https://www.akeebabackup.com/contact-us
 */

/**
 * @package        akeebabackupwp
 * @copyright      2014 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license        GNU GPL version 3 or later
 */

/**
 * Make sure we are being called from WordPress itself
 */
defined('WPINC') or die;

function akeebabackupwp_boot()
{
	require_once dirname(__FILE__) . '/helpers/AkeebaBackupWP.php';

	$baseUrlParts = explode('/', plugins_url('', __FILE__));

	AkeebaBackupWP::$dirName = end($baseUrlParts);
	AkeebaBackupWP::$fileName = basename(__FILE__);
	AkeebaBackupWP::$absoluteFileName = __FILE__;
	AkeebaBackupWP::$wrongPHP = version_compare(PHP_VERSION, AkeebaBackupWP::$minimumPHP, 'lt');

	$aksolowpPath = plugin_dir_path(__FILE__);
	define('AKEEBA_SOLOWP_PATH', $aksolowpPath);
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