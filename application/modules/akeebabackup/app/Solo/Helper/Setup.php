<?php
/**
 * @package     Solo
 * @copyright   2014-2017 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license     GNU GPL version 3 or later
 */

namespace Solo\Helper;

use Awf\Database\Driver;
use Awf\Date\Date;
use Awf\Html\Select;
use Awf\Text\Text;
use Akeeba\Engine\Factory;

/**
 * Setup helper class
 */
abstract class Setup
{
	/**
	 * Get a dropdown list for database drivers
	 *
	 * @param   string  $selected  Selected value
	 * @param   string  $name      The name (also used for id) of the field, default: driver
	 *
	 * @return  string  HTML
	 */
	public static function databaseTypesSelect($selected = '', $name = 'driver')
	{
		$connectors = Driver::getConnectors();

		$html = '<select class="form-control" name="' . $name . '" id="' . $name . '">' . "\n";

		foreach($connectors as $connector)
		{
			// Unsupported driver types
			if (in_array(strtoupper($connector), array('PDO')))
			{
				continue;
			}

			$checked   = (strtoupper($selected) == strtoupper($connector)) ? 'selected="selected"' : '';

			$html .= "\t<option value=\"$connector\" $checked>" . Text::_('SOLO_SETUP_LBL_DATABASE_DRIVER_' . $connector) . "</option>\n";
		}

		$html .= "</select>";

		return $html;
	}

	/**
	 * Get a dropdown list for script types
	 *
	 * @param   string  $selected  Selected value
	 * @param   string  $name      The name (also used for id) of the field, default: installer
	 *
	 * @return  string  HTML
	 */
	public static function restorationScriptSelect($selected = '', $name = 'installer')
	{
		$installers = Factory::getEngineParamsProvider()->getInstallerList(true);

		$options = array();

		foreach($installers as $key => $installer)
		{
			$options[] = Select::option($key, $installer['name']);
		}

		return Select::genericList($options, $name, array('class' => 'form-control'), 'value', 'text', $selected, $name, false);
	}

	/**
	 * Get a dropdown list for restoration scripts
	 *
	 * @param   string  $selected  Selected value
	 * @param   string  $name      The name (also used for id) of the field, default: scripttype
	 *
	 * @return  string  HTML
	 */
	public static function scriptTypesSelect($selected = '', $name = 'scripttype')
	{
		$scriptTypes = array('generic', 'wordpress', 'joomla', 'phpbb', 'prestashop', 'moodle', 'magento', 'magento2', 'drupal7', 'drupal8', 'mautic', 'pagekit', 'grav', 'octobercms');

		$options = array();

		foreach($scriptTypes as $scriptType)
		{
			$options[] = Select::option($scriptType, Text::_('SOLO_CONFIG_PLATFORM_SCRIPTTYPE_' . $scriptType));
		}

		return Select::genericList($options, $name, array('class' => 'form-control'), 'value', 'text', $selected, $name, false);
	}

	/**
	 * Get a dropdown list for mailer engines
	 *
	 * @param   string  $selected  Selected value
	 * @param   string  $name      The name (also used for id) of the field, default: mailer
	 *
	 * @return  string  HTML
	 */
	public static function mailerSelect($selected = '', $name = 'mailer')
	{

		$scriptTypes = array('mail', 'smtp', 'sendmail');

		$options = array();

		foreach($scriptTypes as $scriptType)
		{
			$options[] = Select::option($scriptType, Text::_('SOLO_SYSCONFIG_EMAIL_MAILER_' . $scriptType));
		}

		return Select::genericList($options, $name, array('class' => 'form-control'), 'value', 'text', $selected, $name, false);
	}

	/**
	 * Get a dropdown list for SMTP security settings
	 *
	 * @param   string  $selected  Selected value
	 * @param   string  $name      The name (also used for id) of the field, default: smtpsecure
	 *
	 * @return  string  HTML
	 */
	public static function smtpSecureSelect($selected = '', $name = 'smtpsecure')
	{
		$options = array();
		$options[] = Select::option(0, Text::_('SOLO_SYSCONFIG_EMAIL_SMTPSECURE_NONE'));
		$options[] = Select::option(1, Text::_('SOLO_SYSCONFIG_EMAIL_SMTPSECURE_SSL'));
		$options[] = Select::option(2, Text::_('SOLO_SYSCONFIG_EMAIL_SMTPSECURE_TLS'));

		return Select::genericList($options, $name, array('class' => 'form-control'), 'value', 'text', $selected, $name, false);
	}

	/**
	 * Get a dropdown of available timezones
	 *
	 * @param   string  $selected  Pre-selected value
	 *
	 * @return  string  HTML
	 */
	public static function timezoneSelect($selected = '')
	{
		$timeZones = \DateTimeZone::listIdentifiers();

		$html = '<select class="form-control" name="timezone" id="timezone">' . "\n";

		foreach ($timeZones as $tz)
		{
			$checked = (strtoupper($selected) == strtoupper($tz)) ? 'selected="selected"' : '';

			$html .= "\t<option value=\"$tz\" $checked>$tz</option>\n";
		}

		$html .= "</select>";

		return $html;
	}

	/**
	 * Get a dropdown of available timezone formats
	 *
	 * @param   string  $selected  Pre-selected value
	 *
	 * @return  string  HTML
	 */
	public static function timezoneFormatSelect($selected = '')
	{
		$rawOptions = array(
			'COM_AKEEBA_CONFIG_BACKEND_TIMEZONETEXT_NONE' => '',
			'COM_AKEEBA_CONFIG_BACKEND_TIMEZONETEXT_ABBREVIATION' => 'T',
			'COM_AKEEBA_CONFIG_BACKEND_TIMEZONETEXT_GMTOFFSET' => '\\G\\M\\TP'
		);

		$html = '<select class="form-control" name="timezonetext" id="timezonetext">' . "\n";

		foreach ($rawOptions as $label => $value)
		{
			$checked = (strtoupper($selected) == strtoupper($value)) ? 'selected="selected"' : '';

			$label = Text::_($label);
			$html .= "\t<option value=\"$value\" $checked>$label</option>\n";
		}

		$html .= "</select>";

		return $html;
	}

	/**
	 * Get a dropdown for the filesystem driver selection
	 *
	 * @param   string  $selected  The pre-selected value
	 *
	 * @return  string  HTML
	 */
	public static function fsDriverSelect($selected = '', $showDirect = true)
	{
		$drivers = array();

		if ($showDirect)
		{
			$drivers[] = 'file';
		}

		if (function_exists('ftp_connect'))
		{
			$drivers[] = 'ftp';
		}

		if (extension_loaded('ssh2'))
		{
			$drivers[] = 'sftp';
		}

		$html = '<select class="form-control" name="fs_driver" id="fs_driver">' . "\n";

		foreach ($drivers as $driver)
		{
			$checked = (strtoupper($selected) == strtoupper($driver)) ? 'selected="selected"' : '';

			$html .= "\t<option value=\"$driver\" $checked>" . Text::_('SOLO_SETUP_LBL_FS_DRIVER_' . $driver) . "</option>\n";
		}

		$html .= "</select>";

		return $html;
	}

	/**
	 * Get a dropdown for the minimum update stability
	 *
	 * @param   string  $selected  The pre-selected value
	 *
	 * @return  string  HTML
	 */
	public static function minstabilitySelect($selected = '')
	{
		$levels = array('alpha', 'beta', 'rc', 'stable');

		$html = '<select class="form-control" name="options[minstability]" id="minstability">' . "\n";

		foreach ($levels as $level)
		{
			$checked = (strtoupper($selected) == strtoupper($level)) ? 'selected="selected"' : '';

			$html .= "\t<option value=\"$level\" $checked>" . Text::_('SOLO_CONFIG_MINSTABILITY_' . $level) . "</option>\n";
		}

		$html .= "</select>";

		return $html;
	}

	/**
	 * Get a dropdown for the two factor authentication methods
	 *
	 * @param   string  $name      The name of the field
	 * @param   string  $selected  The pre-selected value
	 *
	 * @return  string  HTML
	 */
	public static function tfaMethods($name = 'tfamethod', $selected = 'none')
	{
		$methods = array('none', 'yubikey', 'google');

		$html = '<select class="form-control" name="' . $name . '" id="' . $name . '">' . "\n";

		foreach ($methods as $method)
		{
			$checked = (strtoupper($selected) == strtoupper($method)) ? 'selected="selected"' : '';

			$html .= "\t<option value=\"$method\" $checked>" . Text::_('SOLO_USERS_TFA_' . $method) . "</option>\n";
		}

		$html .= "</select>";

		return $html;
	}
}