<?php
/**
 * @package		solo
 * @copyright	2014-2017 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license		GNU GPL version 3 or later
 */

namespace Solo;

use Awf\Database\Installer;

class PostUpgradeScript
{
	/** @var \Awf\Container\Container|null The container of the application we are running in */
	protected $container = null;

	/**
	 * @var array Files to remove from all versions
	 */
	protected $removeFilesAllVersions = array(
		'media/css/bootstrap-namespaced.css',
		'media/css/bootstrap-switch.css',
		'media/css/datepicker.css',
		'media/css/theme.css',
		'media/js/bootstrap-switch.js',
		'media/js/piecon.js',
		'media/js/datepicker/bootstrap-datepicker.js',
		'media/js/solo/alice.js',
		'media/js/solo/backup.js',
		'media/js/solo/configuration.js',
		'media/js/solo/dbfilters.js',
		'media/js/solo/encryption.js',
		'media/js/solo/extradirs.js',
		'media/js/solo/fsfilters.js',
		'media/js/solo/gui-helpers.js',
		'media/js/solo/multidb.js',
		'media/js/solo/regexdbfilters.js',
		'media/js/solo/regexfsfilters.js',
		'media/js/solo/restore.js',
		'media/js/solo/setup.js',
		'media/js/solo/stepper.js',
		'media/js/solo/system.js',
		'media/js/solo/update.js',
		'media/js/solo/wizard.js',
		// Removed in version 1.2 (introducing Akeeba Engine 2)
		'Solo/engine/platform/abstract.php',
		'Solo/engine/platform/interface.php',
		'Solo/engine/platform/platform.php',
		// Removed with the introduction of the new S3v4 connector for Amazon S3
		'Solo/engine/Postproc/Connector/Amazons3.php',
		'Solo/engine/Postproc/S3.php',
		'Solo/engine/Postproc/s3.ini',
		// Dropbox v1 integration
		'Solo/engine/Postproc/dropbox.ini',
		'Solo/engine/Postproc/Dropbox.php',
		'Solo/engine/Postproc/Connector/Dropbox.php',
		// Obsolete Azure files
		'Solo/engine/Postproc/Connector/Azure/Credentials/Sharedsignature.php',
		// Obsolete Mautic integration
	    'Solo/assets/installers/angie-mautic.ini',
	    'Solo/assets/installers/angie-mautic.jpa',
	    'Solo/Platform/Solo/Filter/MauticSkipDirs.php',
	    'Solo/Platform/Solo/Filter/MauticSkipFiles.php',
	    'Solo/Pythia/Oracle/Mautic.php',
	);

	/**
	 * @var array Files to remove from Core
	 */
	protected $removeFilesCore = array(
		// Pro engine features
		// -- Archivers
		'Solo/engine/Archiver/directftp.ini',
		'Solo/engine/Archiver/Directftp.php',
		'Solo/engine/Archiver/directftpcurl.ini',
		'Solo/engine/Archiver/Directftpcurl.php',
		'Solo/engine/Archiver/directsftp.ini',
		'Solo/engine/Archiver/Directsftp.php',
		'Solo/engine/Archiver/directsftpcurl.ini',
		'Solo/engine/Archiver/Directsftpcurl.php',
		'Solo/engine/Archiver/jps.ini',
		'Solo/engine/Archiver/Jps.php',
		'Solo/engine/Archiver/zipnative.ini',
		'Solo/engine/Archiver/Zipnative.php',
		// -- Filters
		'Solo/engine/Filter/Extradirs.php',
		'Solo/engine/Filter/Multidb.php',
		'Solo/engine/Filter/Regexdirectories.php',
		'Solo/engine/Filter/Regexfiles.php',
		'Solo/engine/Filter/Regexskipdirs.php',
		'Solo/engine/Filter/Regexskipfiles.php',
		'Solo/engine/Filter/Regexskiptabledata.php',
		'Solo/engine/Filter/Regexskiptables.php',
		// -- Post-processing engines
		'Solo/engine/Postproc/amazons3.ini',
		'Solo/engine/Postproc/Amazons3.ini',
		'Solo/engine/Postproc/azure.ini',
		'Solo/engine/Postproc/Azure.php',
		'Solo/engine/Postproc/cloudfiles.ini',
		'Solo/engine/Postproc/Cloudfiles.php',
		'Solo/engine/Postproc/cloudme.ini',
		'Solo/engine/Postproc/Cloudme.php',
		'Solo/engine/Postproc/dreamobjects.ini',
		'Solo/engine/Postproc/Dreamobjects.php',
		'Solo/engine/Postproc/dropbox.ini',
		'Solo/engine/Postproc/Dropbox.php',
		'Solo/engine/Postproc/dropbox2.ini',
		'Solo/engine/Postproc/Dropbox2.php',
		'Solo/engine/Postproc/ftp.ini',
		'Solo/engine/Postproc/Ftp.php',
		'Solo/engine/Postproc/ftpcurl.ini',
		'Solo/engine/Postproc/Ftpcurl.php',
		'Solo/engine/Postproc/googlestorage.ini',
		'Solo/engine/Postproc/Googlestorage.php',
		'Solo/engine/Postproc/idrivesync.ini',
		'Solo/engine/Postproc/Idrivesync.php',
		'Solo/engine/Postproc/onedrive.ini',
		'Solo/engine/Postproc/Onedrive.php',
		'Solo/engine/Postproc/s3.ini',
		'Solo/engine/Postproc/S3.php',
		'Solo/engine/Postproc/sftp.ini',
		'Solo/engine/Postproc/Sftp.php',
		'Solo/engine/Postproc/sftpcurl.ini',
		'Solo/engine/Postproc/Sftpcurl.php',
		'Solo/engine/Postproc/sugarsync.ini',
		'Solo/engine/Postproc/Sugarsync.php',
		'Solo/engine/Postproc/webdav.ini',
		'Solo/engine/Postproc/Webdav.php',
		// Pro application features
		'Solo/Controller/Discover.php',
		'Solo/Model/Discover.php',
		'Solo/Controller/Extradirs.php',
		'Solo/Model/Extradirs.php',
		'Solo/Controller/Multidb.php',
		'Solo/Model/Multidb.php',
		'Solo/Controller/Regexdbfilters.php',
		'Solo/Model/Regexdbfilters.php',
		'Solo/Controller/Regexfsfilters.php',
		'Solo/Model/Regexfsfilters.php',
		'Solo/Controller/Remotefiles.php',
		'Solo/Model/Remotefiles.php',
		'Solo/Controller/S3import.php',
		'Solo/Model/S3import.php',
		'Solo/Controller/Upload.php',
		'Solo/Model/Upload.php',
	);

	/**
	 * @var array Files to remove from Pro
	 */
	protected $removeFilesPro = array(

	);

	/**
	 * @var array Folders to remove from all versions
	 */
	protected $removeFoldersAllVersions = array(
		// Removed in version 1.2 (introducing Akeeba Engine 2)
		'Solo/engine/platform/solo',
		'Solo/engine/abstract',
		'Solo/engine/drivers',
		'Solo/engine/engines',
		'Solo/engine/filters',
		'Solo/engine/plugins',
		'Solo/engine/utils',
		// Removed with new S3v4 connector for Amazon S3
		'Solo/engine/Postproc/Connector/Amazon',
		'Solo/engine/Postproc/Connector/Amazons3',
		// Dropbox v1 integration
		'Solo/engine/Postproc/Connector/Dropbox',
	);

	/**
	 * @var array Folders to remove from Core
	 */
	protected $removeFoldersCore = array(
		// Pro engine features
		'Solo/engine/plugins',
		'Solo/engine/Postproc/Connector',
		'Solo/Platform/Solo/Config/Pro',
		// Pro application features
		'Solo/View/Discover',
		'Solo/View/Extradirs',
		'Solo/View/Multidb',
		'Solo/View/Regexdbfilters',
		'Solo/View/Regexfsfilters',
		'Solo/View/Remotefiles',
		'Solo/View/S3import',
		'Solo/View/Upload',
	);

	/**
	 * @var array Folders to remove from Pro
	 */
	protected $removeFoldersPro = array(

	);

	/**
	 * Class constructor
	 *
	 * @param \Awf\Container\Container $container The container of the application we are running in
	 */
	public function __construct(\Awf\Container\Container $container)
	{
		$this->container = $container;
	}

	/**
	 * Execute the post-upgrade actions
	 */
	public function execute()
	{
		if ($this->container->segment->get('insideCMS', false))
		{
			if (defined('WPINC'))
			{
				$this->_WordPressActions();
			}

			if (defined('_JEXEC'))
			{
				$this->_JoomlaActions();
			}
		}

		// Remove obsolete files
		$this->processRemoveFiles();

		// Remove obsolete folders
		$this->processRemoveFolders();

		// Migrate profiles
		$this->migrateProfiles();
	}

	/**
	 * Removes obsolete files, depending on the edition (core or pro)
	 */
	protected function processRemoveFiles()
	{
		$removeFiles = $this->removeFilesAllVersions;

		if (defined('AKEEBABACKUP_PRO') && AKEEBABACKUP_PRO)
		{
			$removeFiles = array_merge($removeFiles, $this->removeFilesPro);
		}
		else
		{
			$removeFiles = array_merge($removeFiles, $this->removeFilesCore);
		}

		$this->_removeFiles($removeFiles);
	}

	/**
	 * Removes obsolete folders, depending on the edition (core or pro)
	 */
	protected function processRemoveFolders()
	{
		$removeFolders = $this->removeFoldersAllVersions;

		if (defined('AKEEBABACKUP_PRO') && AKEEBABACKUP_PRO)
		{
			$removeFolders = array_merge($removeFolders, $this->removeFoldersPro);
		}
		else
		{
			$removeFolders = array_merge($removeFolders, $this->removeFoldersCore);
		}

		$this->_removeFolders($removeFolders);
	}

	/**
	 * Specific actions to execute when we are running inside WordPress
	 */
	private function _WordPressActions()
	{
		$this->_WordPressUpgradeToUtf8mb4();
		$this->_WordPressRemoveFolders();
	}

	/**
	 * Remove obsolete folders from the WordPress installation
	 *
	 * @return  void
	 */
	private function _WordPressRemoveFolders()
	{
		$removeFolders = array(
			// Obsolete folders after the introduction of Akeeba Engine 2
			'helpers/platform/solowp',
		);

		$fsBase = rtrim($this->container->filesystemBase, '/' . DIRECTORY_SEPARATOR) . '/../';
		$fs = $this->container->fileSystem;

		foreach($removeFolders as $folder)
		{
			$fs->rmdir($fsBase . $folder, true);
		}
	}

	/**
	 * Update WordPress tables to utf8mb4 if required
	 */
	private function _WordPressUpgradeToUtf8mb4()
	{
		/** @var  wpdb $wpdb */
		global $wpdb;

		// Is it really WordPress?
		if (!is_object($wpdb))
		{
			return;
		}

		// Is it really WordPress?
		if (!method_exists($wpdb, 'has_cap'))
		{
			return;
		}

		// Does the database support utf8mb4 at all?
		if (!$wpdb->has_cap('utf8mb4'))
		{
			return;
		}

		// Is the actual charset set to utf8mb4?
		$charset = strtolower($wpdb->charset);

		if ($charset != 'utf8mb4')
		{
			return;
		}

		// OK, all conditions met, let's upgrade the tables to utf8mb4
		$dbInstaller = new Installer($this->container);
		$dbInstaller->setForcedFile($this->container->basePath . '/assets/sql/xml/utf8mb4_update.xml');
		$dbInstaller->updateSchema();

		return;
	}

	/**
	 * Specific actions to execute when we are running inside Joomla
	 */
	private function _JoomlaActions()
	{

	}

	/**
	 * Removes obsolete files given on a list
	 *
	 * @param array $removeFiles List of files to remove
	 *
	 * @return void
	 */
	private function _removeFiles(array $removeFiles)
	{
		if (empty($removeFiles))
		{
			return;
		}

		$fsBase = rtrim($this->container->filesystemBase, '/' . DIRECTORY_SEPARATOR) . '/';
		$fs = $this->container->fileSystem;

		foreach($removeFiles as $file)
		{
			$fs->delete($fsBase . $file);
		}
	}

	/**
	 * Removes obsolete folders given on a list
	 *
	 * @param array $removeFolders List of folders to remove
	 *
	 * @return void
	 */
	private function _removeFolders(array $removeFolders)
	{
		if (empty($removeFolders))
		{
			return;
		}

		$fsBase = rtrim($this->container->filesystemBase, '/' . DIRECTORY_SEPARATOR) . '/';
		$fs = $this->container->fileSystem;

		foreach($removeFolders as $folder)
		{
			$fs->rmdir($fsBase . $folder, true);
		}
	}

	/**
	 * Migrates existing backup profiles. The changes currently made are:
	 * – Change post-processing from "s3" (legacy) to "amazons3" (current version)
	 * – Fix profiles with invalid embedded installer settings
	 *
	 * @return  void
	 */
	private function migrateProfiles()
	{
		// Get a list of backup profiles
		$db = $this->container->db;
		$query = $db->getQuery(true)
					->select($db->qn('id'))
					->from($db->qn('#__ak_profiles'));
		$profiles = $db->setQuery($query)->loadColumn();

		// Normally this should never happen as we're supposed to have at least profile #1
		if (empty($profiles))
		{
			return;
		}

		// Migrate each profile
		foreach ($profiles as $profile)
		{
			// Initialization
			$dirty = false;

			// Load the profile configuration
			\Akeeba\Engine\Platform::getInstance()->load_configuration($profile);
			$config = \Akeeba\Engine\Factory::getConfiguration();

			// -- Migrate obsolete "s3" engine to "amazons3"
			$postProcType = $config->get('akeeba.advanced.postproc_engine', '');

			if ($postProcType == 's3')
			{
				$config->setKeyProtection('akeeba.advanced.postproc_engine', false);
				$config->setKeyProtection('engine.postproc.amazons3.signature', false);
				$config->setKeyProtection('engine.postproc.amazons3.accesskey', false);
				$config->setKeyProtection('engine.postproc.amazons3.secretkey', false);
				$config->setKeyProtection('engine.postproc.amazons3.usessl', false);
				$config->setKeyProtection('engine.postproc.amazons3.bucket', false);
				$config->setKeyProtection('engine.postproc.amazons3.directory', false);
				$config->setKeyProtection('engine.postproc.amazons3.rrs', false);
				$config->setKeyProtection('engine.postproc.amazons3.customendpoint', false);
				$config->setKeyProtection('engine.postproc.amazons3.legacy', false);

				$config->set('akeeba.advanced.postproc_engine', 'amazons3');
				$config->set('engine.postproc.amazons3.signature', 's3');
				$config->set('engine.postproc.amazons3.accesskey', $config->get('engine.postproc.s3.accesskey'));
				$config->set('engine.postproc.amazons3.secretkey', $config->get('engine.postproc.s3.secretkey'));
				$config->set('engine.postproc.amazons3.usessl', $config->get('engine.postproc.s3.usessl'));
				$config->set('engine.postproc.amazons3.bucket', $config->get('engine.postproc.s3.bucket'));
				$config->set('engine.postproc.amazons3.directory', $config->get('engine.postproc.s3.directory'));
				$config->set('engine.postproc.amazons3.rrs', $config->get('engine.postproc.s3.rrs'));
				$config->set('engine.postproc.amazons3.customendpoint', $config->get('engine.postproc.s3.customendpoint'));
				$config->set('engine.postproc.amazons3.legacy', $config->get('engine.postproc.s3.legacy'));

				$dirty = true;
			}

			// Fix profiles with invalid embedded installer settings
			$embeddedInstaller = $config->get('akeeba.advanced.embedded_installer');

			if (empty($embeddedInstaller) || ($embeddedInstaller == 'angie-joomla') || (
					(substr($embeddedInstaller, 0, 5) != 'angie') && ($embeddedInstaller != 'none')
				))
			{
				$config->setKeyProtection('akeeba.advanced.embedded_installer', false);
				$config->set('akeeba.advanced.embedded_installer', 'angie');
				$dirty = true;
			}

			// Save dirty records
			if ($dirty)
			{
				\Akeeba\Engine\Platform::getInstance()->save_configuration($profile);
			}
		}
	}
}