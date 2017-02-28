<?php
/**
 * @package		solo
 * @copyright	2014-2017 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license		GNU GPL version 3 or later
 */

use Awf\Text\Text;
use Solo\Helper\Escape;

/** @var \Solo\View\Manage\Html $this */

$router = $this->container->router;
$token = $this->container->session->getCsrfToken()->getValue();

?>
<form name="adminForm" id="adminForm" action="<?php echo $router->route('index.php?view=manage') ?>" method="post" class="form-horizontal" role="form">
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="id" value="<?php echo $this->record['id'] ?>" />
	<input type="hidden" name="token" value="<?php echo $token ?>">

	<div class="form-group">
		<label class="control-label col-sm-3" for="description">
			<?php echo Text::_('COM_AKEEBA_BUADMIN_LABEL_DESCRIPTION'); ?>
		</label>
		<div class="col-sm-9">
			<input type="text" name="description" maxlength="255" size="50"
				   value="<?php echo $this->record['description'] ?>"
				   class="form-control" />
		</div>
	</div>

	<div class="form-group">
		<label class="control-label col-sm-3" for="comment">
			<?php echo Text::_('COM_AKEEBA_BUADMIN_LABEL_DESCRIPTION'); ?>
		</label>
		<div class="col-sm-9">
			<textarea id="comment" name="comment" rows="5" cols="73" class="form-control"
					  autocomplete="off"><?php echo $this->record['comment'] ?></textarea>
		</div>
	</div>
</form>