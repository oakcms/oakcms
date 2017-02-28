<?php
/**
 * @package        solo
 * @copyright      2014-2017 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license        GNU GPL version 3 or later
 */

use Awf\Text\Text;
use Solo\Helper\Escape;

/** @var \Solo\View\S3import\Html $this */

$router = $this->container->router;

?>
<style type="text/css">
	dl { display: none; }
</style>

<div class="progress">
	<div class="progress-bar" role="progressbar" aria-valuenow="<?php echo $this->percent ?>" aria-valuemin="0" aria-valuemax="100" style="width: <?php echo $this->percent ?>%;">
		<span class="sr-only"><?php echo $this->percent ?>% Complete</span>
	</div>
</div>

<div class="well">
	<?php echo Text::sprintf('COM_AKEEBA_REMOTEFILES_LBL_DOWNLOADEDSOFAR', $this->done, $this->total, $this->percent); ?>
</div>