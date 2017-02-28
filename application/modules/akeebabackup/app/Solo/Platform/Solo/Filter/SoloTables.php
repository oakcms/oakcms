<?php
/**
 * Akeeba Engine
 * The modular PHP5 site backup engine
 * @copyright Copyright (c)2009-2016 Nicholas K. Dionysopoulos
 * @license   GNU GPL version 3 or, at your option, any later version
 * @package   akeebaengine
 *
 */

namespace Akeeba\Engine\Filter;

use Akeeba\Engine\Filter\Base as FilterBase;
use Akeeba\Engine\Factory;
use Akeeba\Engine\Platform;

// Protection against direct access
defined('AKEEBAENGINE') or die();

/**
 * Database table exclusion filter
 */
class SoloTables extends FilterBase
{
	function __construct()
	{
		$this->object = 'dbobject';
		$this->subtype = 'all';
		$this->method = 'regex';
		$this->filter_name = 'SoloTables';

		if (Factory::getKettenrad()->getTag() == 'restorepoint')
		{
			$this->enabled = false;
		}

		$configuration = Factory::getConfiguration();

		if (!$configuration->get('akeeba.platform.addsolo', 0))
		{
			// Site database connection information
			$siteDbHost = $configuration->get('akeeba.platform.dbhost', '');
			$siteDbName = $configuration->get('akeeba.platform.dbname', '');
			$siteDbPrefix = $configuration->get('akeeba.platform.dbprefix', '');

			// Akeeba Solo connection information
			$appConfig = \Awf\Application\Application::getInstance()->getContainer()->appConfig;
			$soloDbHost = $appConfig->get('dbhost', '');
			$soloDbName = $appConfig->get('dbname', '');
			$soloDbPrefix = $appConfig->get('prefix', '');

			// If Solo is installed in the same db as the site...
			if (($soloDbHost == $siteDbHost) && ($soloDbName == $siteDbName))
			{
				// ...check which prefix is being used...
				$soloPrefix = ($siteDbPrefix == $soloDbPrefix) ? '#__' : $soloDbPrefix;

				// ...and exclude Solo's tables with a RegEx
				$this->filter_data['[SITEDB]'] = array(
					'/^' . $soloPrefix . 'ak_/',
				);
			}
		}

		parent::__construct();
	}
}