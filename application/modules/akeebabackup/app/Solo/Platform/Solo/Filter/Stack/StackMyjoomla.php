<?php
/**
 * Akeeba Engine
 * The modular PHP5 site backup engine
 *
 * @copyright Copyright (c)2006-2017 Nicholas K. Dionysopoulos
 * @license   GNU GPL version 3 or, at your option, any later version
 * @package   akeebaengine
 *
 */

namespace Akeeba\Engine\Filter\Stack;

use Akeeba\Engine\Filter\Base;

// Protection against direct access
defined('AKEEBAENGINE') or die();

/**
 * Exclude MyJoomla tables
 */
class StackMyjoomla extends Base
{
    public function __construct()
    {
        $this->object  = 'dbobject';
        $this->subtype = 'content';
        $this->method  = 'api';

        parent::__construct();
    }

    protected function is_excluded_by_api($test, $root)
    {
	    static $myjoomlaTables = array(
		    'bf_core_hashes',
		    'bf_files',
		    'bf_files_last',
		    'bf_folders',
		    'bf_folders_to_scan'
	    );

	    // Is it one of the blacklisted tables?
	    if(in_array($test, $myjoomlaTables))
	    {
	    	return true;
	    }

	    // No match? Just include the file!
	    return false;
    }

}