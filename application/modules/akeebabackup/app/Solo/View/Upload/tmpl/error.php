<?php
/**
 * @package        solo
 * @copyright      2014-2017 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license        GNU GPL version 3 or later
 */

use Awf\Text\Text;

/** @var \Solo\View\Upload\Html $this */

$router = $this->container->router;

?>
<div class="alert alert-danger">
	<h4>
		<?php echo Text::_('COM_AKEEBA_TRANSFER_MSG_FAILED')?>
	</h4>
	<p>
		<?php echo $this->errorMessage; ?>
	</p>
</div>