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
 * Excludes table data from specific tables
 */
class JoomlaTableData extends FilterBase
{
	public function __construct()
	{
		$this->object = 'dbobject';
		$this->subtype = 'content';
		$this->method = 'direct';
		$this->filter_name = 'JoomlaTableData';

		$configuration = Factory::getConfiguration();

		if ($configuration->get('akeeba.platform.scripttype', 'generic') !== 'joomla')
		{
			$this->enabled = false;

			return;
		}

		if (Factory::getKettenrad()->getTag() == 'restorepoint')
		{
			$this->enabled = false;

			return;
		}

		// We take advantage of the filter class magic to inject our custom filters
		$this->filter_data['[SITEDB]'] = array(
			'#__session', // Sessions table
			'#__guardxt_runs' // Guard XT's run log (bloated to the bone)
		);

		parent::__construct();
	}

}