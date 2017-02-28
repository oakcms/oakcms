<?php
/**
 * @package		solo
 * @copyright	2014-2017 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license		GNU GPL version 3 or later
 */

// Protect from unauthorized access
use Awf\Text\Text;

/** @var  $this  Solo\View\Transfer\Html */

echo $this->loadAnyTemplate('Common/ftp_browser');
echo $this->loadAnyTemplate('Common/sftp_browser');
echo $this->loadAnyTemplate('Common/ftp_test');

?>

