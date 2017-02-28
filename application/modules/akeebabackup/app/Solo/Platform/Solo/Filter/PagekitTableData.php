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
class PagekitTableData extends FilterBase
{
	public function __construct()
	{
		$this->object = 'dbobject';
		$this->subtype = 'content';
		$this->method = 'direct';
		$this->filter_name = 'PagekitTableData';

		$configuration = Factory::getConfiguration();

		if ($configuration->get('akeeba.platform.scripttype', 'generic') !== 'pagekit')
		{
			$this->enabled = false;

			return;
		}

		$this->filter_data['[SITEDB]'] = array(
			'#__system_session'
		);

		parent::__construct();
	}

}