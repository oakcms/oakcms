<?php
/**
 * @package     Solo
 * @copyright   2014-2017 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license     GNU GPL version 3 or later
 */

namespace Solo\Model;

use Awf\Database\Driver;
use Awf\Database\Installer;
use Awf\Mvc\Model;
use Awf\Filesystem as FS;
use Awf\Session\Exception;
use Awf\Text\Text;
use Awf\Application\Application;
use Awf\Uri\Uri;

class Setup extends Model
{
	/**
	 * Are all required settings met?
	 *
	 * @staticvar   null|bool  $ret  The cached result
	 *
	 * @return  bool
	 */
	public function isRequiredMet()
	{
		static $ret = null;

		if (is_null($ret))
		{
			$required = $this->getRequired();
			$ret = true;
			foreach ($required as $setting)
			{
				if ($setting['warning'])
				{
					continue;
				}

				$ret = $ret && $setting['current'];
				if (!$ret)
				{
					break;
				}
			}
		}

		return $ret;
	}

	/**
	 * Are all recommended settings met?
	 *
	 * @staticvar   null|bool  $ret  The cached result
	 *
	 * @return  bool
	 */
	public function isRecommendedMet()
	{
		static $ret = null;

		if (is_null($ret))
		{
			$required = $this->getRecommended();
			$ret = true;
			foreach ($required as $setting)
			{
				$ret = $ret && ($setting['current'] == $setting['recommended']);
				if (!$ret)
				{
					break;
				}
			}
		}

		return $ret;
	}

	/**
	 * Get the required settings analysis
	 *
	 * @return  array
	 */
	public function getRequired()
	{
		static $phpOptions = array();

		if (empty($phpOptions))
		{
			$minPHPVersion = '5.3.3';

			$phpOptions[] = array (
				'label'		=> Text::sprintf('SOLO_SETUP_LBL_REQ_PHP_VERSION', $minPHPVersion),
				'current'	=> version_compare(phpversion(), $minPHPVersion, 'ge'),
				'warning'	=> false,
			);

			$phpOptions[] = array (
				'label'		=> Text::_('SOLO_SETUP_LBL_REQ_MCGPCOFF'),
				'current'	=> (ini_get('magic_quotes_gpc') == false),
				'warning'	=> false,
			);

			$phpOptions[] = array (
				'label'		=> Text::_('SOLO_SETUP_LBL_REQ_REGGLOBALS'),
				'current'	=> (ini_get('register_globals') == false),
				'warning'	=> false,
			);

			$phpOptions[] = array (
				'label'		=> Text::_('SOLO_SETUP_LBL_REQ_ZLIB'),
				'current'	=> extension_loaded('zlib'),
				'warning'	=> false,
			);

			$phpOptions[] = array (
				'label'		=> Text::_('SOLO_SETUP_LBL_REQ_XML'),
				'current'	=> extension_loaded('xml'),
				'warning'	=> false,
			);

			$phpOptions[] = array (
				'label'		=> Text::_('SOLO_SETUP_LBL_REQ_DATABASE'),
				'current'	=> (
                                    // MSQL functions
                                    function_exists('mysql_connect') || function_exists('mysqli_connect') ||
                                    // PostgreSQL
                                    function_exists('pg_connect') ||
                                    // SQL Server
                                    function_exists('sqlsrv_connect') ||
                                    // SQLite
                                    (class_exists('\\PDO') && in_array('sqlite', \PDO::getAvailableDrivers()))
                ),
				'warning'	=> false,
			);

			if (extension_loaded( 'mbstring' ))
			{
				$option = array (
					'label'		=> Text::_( 'SOLO_SETUP_REQ_MBLANGISDEFAULT' ),
					'current'	=> (strtolower(ini_get('mbstring.language')) == 'neutral'),
					'warning'	=> false,
				);
				$option['notice'] = $option['current'] ? null : Text::_('SOLO_SETUP_MSG_NOTICEMBLANGNOTDEFAULT');
				$phpOptions[] = $option;

				$option = array (
					'label'		=> Text::_('SOLO_SETUP_REQ_MBSTRINGOVERLOAD'),
					'current'	=> (ini_get('mbstring.func_overload') == 0),
					'warning'	=> false,
				);
				$option['notice'] = $option['current'] ? null : Text::_('SOLO_SETUP_MSG_NOTICEMBSTRINGOVERLOAD');
				$phpOptions[] = $option;
			}

			$phpOptions[] = array (
				'label'		=> Text::_('SOLO_SETUP_LBL_REQ_INIPARSER'),
				'current'	=> $this->getIniParserAvailability(),
				'warning'	=> false,
			);

			$phpOptions[] = array (
				'label'		=> Text::_('SOLO_SETUP_LBL_REQ_JSON'),
				'current'	=> function_exists('json_encode') && function_exists('json_decode'),
				'warning'	=> false,
			);

			$appPath = $this->container->basePath;

			$cW = (@ file_exists($appPath . '/assets/private/config.php') && @is_writable($appPath . '/assets/private/config.php')) || @is_writable($appPath . '/assets/private');
			$phpOptions[] = array (
				'label'		=> Text::_('SOLO_SETUP_LBL_REQ_CONFIGJSON'),
				'current'	=> $cW,
				'notice'	=> $cW ? null : Text::_('SOLO_SETUP_MSG_CONFIGURATIONPHP'),
				'warning'	=> true
			);
		}

		return $phpOptions;
	}

	/**
	 * Get the recommended settings analysis. Akeeba Solo can work even if these conditions are not met, but not all
	 * features will be available.
	 *
	 * @return  array
	 */
	public function getRecommended()
	{
		static $phpOptions = array();

		if (empty($phpOptions))
		{
			$phpOptions[] = array(
				'label'			=> Text::_('SOLO_SETUP_REC_SAFEMODE'),
				'current'		=> (bool) ini_get('safe_mode'),
				'recommended'	=> false,
			);

			$phpOptions[] = array(
				'label'			=> Text::_('SOLO_SETUP_REC_DISPERRORS'),
				'current'		=> (bool) ini_get('display_errors'),
				'recommended'	=> false,
			);

			$phpOptions[] = array(
				'label'			=> Text::_('SOLO_SETUP_REC_MCR'),
				'current'		=> (bool) ini_get('magic_quotes_runtime'),
				'recommended'	=> false,
			);

			$phpOptions[] = array(
				'label'			=> Text::_('SOLO_SETUP_REC_MCGPC'),
				'current'		=> (bool) ini_get('magic_quotes_gpc'),
				'recommended'	=> false,
			);

			$phpOptions[] = array(
				'label'			=> Text::_('SOLO_SETUP_REC_OUTBUF'),
				'current'		=> (bool) ini_get('output_buffering'),
				'recommended'	=> false,
			);

			$phpOptions[] = array(
				'label'			=> Text::_('SOLO_SETUP_REC_SESSIONAUTO'),
				'current'		=> (bool) ini_get('session.auto_start'),
				'recommended'	=> false,
			);

			$phpOptions[] = array(
				'label'			=> Text::_('SOLO_SETUP_REC_CURL'),
				'current'		=> function_exists('curl_init'),
				'recommended'	=> true,
			);

			$phpOptions[] = array(
				'label'			=> Text::_('SOLO_SETUP_REC_FTP'),
				'current'		=> function_exists('ftp_connect'),
				'recommended'	=> true,
			);

			$phpOptions[] = array (
				'label'			=> Text::_('SOLO_SETUP_REC_SSH2'),
				'current'		=> extension_loaded('ssh2'),
				'recommended'	=> true,
			);

		}

		return $phpOptions;
	}

	/**
	 * Checks the availability of the parse_ini_file and parse_ini_string functions.
	 *
	 * @return	boolean
	 */
	public function getIniParserAvailability()
	{
		$disabled_functions = ini_get('disable_functions');

		if (!empty($disabled_functions))
		{
			// Attempt to detect them in the disable_functions black list
			$disabled_functions = explode(',', trim($disabled_functions));
			$number_of_disabled_functions = count($disabled_functions);

			for ($i = 0; $i < $number_of_disabled_functions; $i++)
			{
				$disabled_functions[$i] = trim($disabled_functions[$i]);
			}

			$result = !in_array('parse_ini_string', $disabled_functions);
		} else {
			// Attempt to detect their existence; even pure PHP implementation of them will trigger a positive response, though.
			$result = function_exists('parse_ini_string');
		}

		return $result;
	}

	/**
	 * Get the database connection parameters stored in the session
	 *
	 * @return  array
	 */
	public function getDatabaseParameters()
	{
		$session = $this->container->segment;

		$dbParameters = array(
			'driver'		=> $session->get('db_driver', 'mysqli'),
			'host'			=> $session->get('db_host', 'localhost'),
			'user'			=> $session->get('db_user', ''),
			'pass'			=> $session->get('db_pass', ''),
			'name'			=> $session->get('db_name', ''),
			'prefix'		=> $session->get('db_prefix', 'solo_'),
		);

		$queryDbParameters = array(
			'driver'		=> $this->input->get('driver', null, 'cmd'),
			'host'			=> $this->input->get('host', null, 'raw'),
			'user'			=> $this->input->get('user', null, 'raw'),
			'pass'			=> $this->input->get('pass', null, 'raw'),
			'name'			=> $this->input->get('name', null, 'raw'),
			'prefix'		=> $this->input->get('prefix', null, 'raw'),
		);

		foreach ($queryDbParameters as $k => $v)
		{
			if (is_null($v))
			{
				continue;
			}

			$dbParameters[$k] = $v;
			$session->set('db_' . $k, $v);
		}

		return $dbParameters;
	}

	/**
	 * Apply the database connection parameters from the session to the application configuration
	 *
	 * @return  void
	 */
	public function applyDatabaseParameters()
	{
		$config = $this->container->appConfig;

		$dbParameters = $this->getDatabaseParameters();

		foreach ($dbParameters as $k => $v)
		{
			if ($k != 'prefix')
			{
				$k = 'db' . $k;
				$config->set($k, $v);
			}
			else
			{
				$config->set($k, $v);
			}
		}
	}

	/**
	 * Set the database connection parameters from the input
	 */
	public function setDatabaseParametersFromInput()
	{
		$session = $this->container->segment;

		$dbParameters = $this->getDatabaseParameters();

		foreach ($dbParameters as $k => $default)
		{
			$session->set('db_' . $k, $this->input->get($k, $default, 'raw'));
		}
	}

	/**
	 * Installs or uninstalls the database tables of Akeeba Solo
	 *
	 * @param   boolean  $uninstall  Should I uninstall instead?
	 *
	 * @return  void
	 *
	 * @throws  \RuntimeException  When any kind of db error occurs.
	 */
	public function installDatabase($uninstall = false)
	{
		$dbInstaller = new Installer($this->container);
		$dbInstaller->updateSchema();
	}

	/**
	 * Get the application setup parameters from the application configuration or the session (session overrides
	 * configuration)
	 *
	 * @return   array
	 */
	public function getSetupParameters()
	{
		$live_site = Uri::base(false, $this->container);

		return array(
			'timezone'			=> $this->getSetupParameter('timezone', 'UTC'),
			'live_site'			=> $this->getSetupParameter('live_site', $live_site),
			'session_timeout'	=> $this->getSetupParameter('session_timeout', '1440'),
			'fs.driver'			=> $this->getSetupParameter('fs.driver', 'file'),
			'fs.host'			=> $this->getSetupParameter('fs.host', '127.0.0.1'),
			'fs.port'			=> $this->getSetupParameter('fs.port', '21'),
			'fs.username'		=> $this->getSetupParameter('fs.username', ''),
			'fs.password'		=> $this->getSetupParameter('fs.password', ''),
			'fs.directory'		=> $this->getSetupParameter('fs.directory', '/'),
			'fs.ssl'			=> $this->getSetupParameter('fs.ssl', false),
			'fs.passive'		=> $this->getSetupParameter('fs.passive', true),
			'user.username'		=> $this->getSetupParameter('user.username', 'admin'),
			'user.password'		=> $this->getSetupParameter('user.password', ''),
			'user.password2'	=> $this->getSetupParameter('user.password2', ''),
			'user.email'		=> $this->getSetupParameter('user.email', ''),
			'user.name'			=> $this->getSetupParameter('user.name', ''),
		);
	}

	/**
	 * Sets the application setup parameters from the input or, if these don't exist, from the session storage
	 *
	 * @return  void
	 */
	public function setSetupParameters()
	{
		$params = $this->getSetupParameters();

		$session = $this->container->segment;
		$config = $this->container->appConfig;

		foreach ($params as $k => $v)
		{
			$altKey = str_replace('.', '_', $k);

			$v = $this->input->get($altKey, $v, 'raw');

			$session->set('setup_' . $altKey, $v);

			if (substr($k, 0, 5) == 'user.')
			{
				// Do not store user parameters in the application configuration
				continue;
			}

			if ($k == 'fs.directory')
			{
				$k = 'fs.dir';
			}

			$config->set($k, $v);
		}
	}

	/**
	 * Internal method to get an application setup parameter from the session
	 *
	 * @param   string  $key      The configuration key to retrieve
	 * @param   mixed   $default  The default value, in case it doesn't exist
	 *
	 * @return  mixed
	 */
	private function getSetupParameter($key, $default = null)
	{
		$session = $this->container->segment;
		$config = $this->container->appConfig;

		$altKey = str_replace('.', '_', $key);

		return $session->get('setup_' . $altKey, $config->get($key, $default));
	}

	/**
	 * Get a list of all available schema versions for a particular database technology. This is determined from the
	 * update SQL files' names. The last item of the array is the maximum version.
	 *
	 * @param   Driver  $driver
	 *
	 * @return  array  A list of schema versions (without the path and .sql suffix)
	 */
	public function getSchemaVersions(Driver $driver = null)
	{
		if (!is_object($driver))
		{
			$driver = $this->container->db;
		}

		$class = get_class($driver);
		$dbTech = $class::$dbtech;

		$path = $this->container->basePath . "/assets/sql/update/$dbTech";

		$versions = array();
		$di = new \DirectoryIterator($path);

		/** @var \DirectoryIterator $file */
		foreach ($di as $file)
		{
			if ($file->isDot())
			{
				continue;
			}

			if (!$file->isFile())
			{
				continue;
			}

			$fileName = $file->getBasename();

			if (substr($fileName, -4) != '.sql')
			{
				continue;
			}

			$versions[] = basename($fileName, '.sql');
		}

		@asort($versions);

		return $versions;
	}

	/**
	 * Create a new administrator user from the session state variables
	 *
	 * @return  void
	 *
	 * @throws  \RuntimeException When there is missing or mismatched user information
	 */
	public function createAdminUser()
	{
		$params = $this->getSetupParameters();

		if (empty($params['user.username']))
		{
			throw new \RuntimeException(Text::_('SOLO_SETUP_ERR_USER_EMPTYUSERNAME'), 500);
		}

		if (empty($params['user.password']))
		{
			throw new \RuntimeException(Text::_('SOLO_SETUP_ERR_USER_EMPTYPASSWORD'), 500);
		}

		if ($params['user.password'] != $params['user.password2'])
		{
			throw new \RuntimeException(Text::_('SOLO_SETUP_ERR_USER_PASSWORDSDONTMATCH'), 500);
		}

		if (empty($params['user.email']))
		{
			throw new \RuntimeException(Text::_('SOLO_SETUP_ERR_USER_EMPTYEMAIL'), 500);
		}

		if (empty($params['user.name']))
		{
			throw new \RuntimeException(Text::_('SOLO_SETUP_ERR_USER_EMPTYNAME'), 500);
		}

		$manager = $this->container->userManager;
		$user = $manager->getUserByUsername($params['user.username']);

		if (empty($user))
		{
			$user = $manager->getUser(0);
		}

		$data = array(
			'username'		=> $params['user.username'],
			'name'			=> $params['user.name'],
			'email'			=> $params['user.email'],
		);

		$user->bind($data);
		$user->setPassword($params['user.password']);
		$user->setPrivilege('akeeba.backup', true);
		$user->setPrivilege('akeeba.configure', true);
		$user->setPrivilege('akeeba.download', true);

		$manager->saveUser($user);

		$manager->loginUser($params['user.username'], $params['user.password']);
	}
}