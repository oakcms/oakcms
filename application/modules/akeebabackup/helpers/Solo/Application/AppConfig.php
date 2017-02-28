<?php
/**
 * @package		akeebabackupwp
 * @copyright	2014-2017 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license		GNU GPL version 3 or later
 */

namespace Solo\Application;


use Awf\Application\Configuration;

class AppConfig extends Configuration
{
	public function __construct(\Awf\Container\Container $container, $data = null)
	{
		parent::__construct($container, $data);

		$this->defaultPath = __DIR__ . '/../../private/config.php';
	}

	/**
	 * Save the application configuration
	 *
	 * @param   string $filePath The path to the JSON file (optional)
	 *
	 * @return  void
	 *
	 * @throws  \RuntimeException  When saving fails
	 */
	public function saveConfiguration($filePath = null)
	{
		if (empty($filePath))
		{
			$filePath = $this->getDefaultPath();
			$filePath = realpath(dirname($filePath)) . '/' . basename($filePath);
		}

		$clone = clone $this;
		$clone->set('dbdriver', null);
		$clone->set('dbhost', null);
		$clone->set('dbuser', null);
		$clone->set('dbpass', null);
		$clone->set('dbname', null);
		$clone->set('dbselect', null);
		$clone->set('connection', null);
		$clone->set('prefix', null);
		$clone->set('live_site', null);
		$clone->set('base_url', null);

		$fileData = $clone->toString('JSON', array('pretty_print' => true));
		$fileData = "<?php die; ?>\n" . $fileData;

		$res = $this->container->fileSystem->write($filePath, $fileData);

        if (!$res)
        {
            throw new \RuntimeException('Can not save ' . $filePath, 500);
        }
	}

	/**
	 * Loads the configuration off a JSON file
	 *
	 * @param   string $filePath The path to the JSON file (optional)
	 *
	 * @return  void
	 */
	public function loadConfiguration($filePath = null, \Awf\Utils\Phpfunc $phpfunc = null)
	{
		if (!class_exists('wpdb'))
		{
			global $table_prefix;
		}
		else
		{
			global $wpdb;
			$table_prefix = $wpdb->prefix;
		}

		if (empty($filePath))
		{
			$filePath = $this->getDefaultPath();
		}

		// Reset the class
		$this->data = new \stdClass();

		// Try to open the file
		if (file_exists($filePath))
		{
			$fileData = @file_get_contents($filePath);

			if ($fileData !== false)
			{
				$fileData = explode("\n", $fileData, 2);
				$fileData = $fileData[1];
				$this->loadString($fileData);
			}
		}


		$driver = 'Mysqli';

		if (!isset($wpdb) || !is_object($wpdb->dbh) || !($wpdb->dbh instanceof \mysqli))
		{
			$driver = function_exists('mysql_connect') ? 'Mysql' : 'Mysqli';
		}

		$this->set('dbdriver', $driver);

		if (isset($wpdb))
		{
			$this->set('connection', $wpdb->dbh);
		}
		$this->set('dbselect', false);

		if (!isset($wpdb) || empty($wpdb->dbh))
		{
			$this->set('dbhost', DB_HOST);
			$this->set('dbuser', DB_USER);
			$this->set('dbpass', DB_PASSWORD);
			$this->set('dbname', DB_NAME);
			$this->set('dbselect', true);
		}

		$this->set('prefix', $table_prefix);

		if (defined('AKEEBA_SOLO_WP_SITEURL'))
		{
			$this->set('live_site', AKEEBA_SOLO_WP_SITEURL);
		}

		if (defined('AKEEBA_SOLO_WP_URL'))
		{
			$this->set('base_url', AKEEBA_SOLO_WP_URL);
		}

		if (defined('AKEEBA_SOLO_WP_ROOTURL'))
		{
			$this->set('cms_url', AKEEBA_SOLO_WP_ROOTURL);
		}

		$timezone = function_exists('get_option') ? get_option('timezone_string') : 'UTC';
		$timezone = empty($timezone) ? 'UTC' : $timezone;
		$this->set('timezone', $timezone);
	}
}