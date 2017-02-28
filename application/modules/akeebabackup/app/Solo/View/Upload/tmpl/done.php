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
<div class="alert alert-success">
	<?php echo Text::_('COM_AKEEBA_TRANSFER_MSG_DONE');?>
</div>

<script type="text/javascript" language="javascript">
	window.setTimeout('closeme();', 3000);

	function closeme()
	{
		parent.Solo.System.modalDialog.modal('hide');
	}
</script>