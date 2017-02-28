<?php
/**
 * @package        solo
 * @copyright      2014-2017 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license        GNU GPL version 3 or later
 */

use Awf\Text\Text;

/** @var \Solo\View\Remotefiles\Html $this */

$router = $this->container->router;

?>
<style type="text/css">
	dl {
		display: none;
	}
</style>

<div id="backup-percentage" class="progress">
	<div id="progressbar-inner" class="progress-bar" role="progressbar" aria-valuenow="<?php echo $this->percent ?>" aria-valuemin="0" aria-valuemax="100" style="width: <?php echo $this->percent ?>%;">
		<span class="sr-only"><?php echo $this->percent ?>% Complete</span>
	</div>
</div>

<div class="well">
	<?php echo Text::sprintf('COM_AKEEBA_REMOTEFILES_LBL_DOWNLOADEDSOFAR', $this->done, $this->total, $this->percent); ?>
</div>

<form action="<?php echo $router->route('index.php?view=remorefiles&task=downloadToServer&tmpl=component')?>" name="adminForm" id="adminForm">
	<input type="hidden" name="id" value="<?php echo $this->id ?>"/>
	<input type="hidden" name="part" value="<?php echo $this->part ?>"/>
	<input type="hidden" name="frag" value="<?php echo $this->frag ?>"/>
</form>