<?php
/**
 * @package        solo
 * @copyright      2014-2017 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license        GNU GPL version 3 or later
 */

namespace Solo\Model\Exception;

use RuntimeException;

// Protect from unauthorized access
defined('_JEXEC') or die();

class TransferIgnorableError extends RuntimeException
{

}