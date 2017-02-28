<?php
/**
 * Akeeba Engine
 * The modular PHP5 site backup engine
 *
 * @copyright Copyright (c)2006-2017 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license   GNU GPL version 3 or, at your option, any later version
 * @package   akeebaengine
 *
 */

namespace Akeeba\Engine\Filter\Stack;

use Akeeba\Engine\Filter\Base;

// Protection against direct access
defined('AKEEBAENGINE') or die();


/**
 * Exclude folders and files belonging to the host web stat (ie Webalizer)
 */
class StackHoststats extends Base
{
    public function __construct()
    {
        $this->object  = 'dir';
        $this->subtype = 'all';
        $this->method  = 'api';

        if (empty($this->filter_name))
        {
            $this->filter_name = strtolower(basename(__FILE__, '.php'));
        }

        parent::__construct();
    }

    protected function is_excluded_by_api($test, $root)
    {
        if($test == 'stats')
        {
            return true;
        }

        // No match? Just include the file!
        return false;
    }

}