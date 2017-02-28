<?php
/**
 * @package        solo
 * @copyright      2014-2017 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license        GNU GPL version 3 or later
 */

use Awf\Text\Text;

/** @var \Solo\View\Discover\Html $this */

$router = $this->container->router;

$hasFiles = !empty($this->files);
$task = $hasFiles ? 'import' : 'main';
?>
<form name="adminForm" id="adminForm" action="<?php echo $router->route('index.php?view=discover&task=' . $task) ?>" method="POST" class="form-horizontal" role="form">
	<?php if($hasFiles): ?>
	<input type="hidden" name="directory" value="<?php echo $this->directory ?>" />
	<?php endif; ?>
	<input type="hidden" name="token" value="<?php echo $this->container->session->getCsrfToken()->getValue(); ?>" />
	
	<?php if($hasFiles): ?>
	<div class="well form-inline">
		<label for="directory2"><?php echo Text::_('COM_AKEEBA_DISCOVER_LABEL_DIRECTORY') ?></label>
		<input type="text" class="form-control" name="directory2" id="directory2" value="<?php echo $this->directory ?>" disabled="disabled" size="70" />
	</div>

	<div class="form-group">
		<label class="control-label col-sm-3" for="input01">
			<?php echo Text::_('COM_AKEEBA_DISCOVER_LABEL_FILES'); ?>
		</label>
		<div class="col-sm-9">
			<select name="files[]" id="files" multiple="multiple" class="form-control" size="10">
			<?php foreach($this->files as $file): ?>
				<option value="<?php echo $this->escape(basename($file)); ?>"><?php echo $this->escape(basename($file)); ?></option>
			<?php endforeach; ?>
			</select>
			<p class="help-block"><?php echo Text::_('COM_AKEEBA_DISCOVER_LABEL_SELECTFILES'); ?></p>
		</div>
	</div>
	
	<div class="col-sm-9 col-sm-push-3">
		<button class="btn btn-lg btn-primary" onclick="this.form.submit(); return false;">
			<span class="glyphicon glyphicon-import"></span>
			<?php echo Text::_('COM_AKEEBA_DISCOVER_LABEL_IMPORT') ?>
		</button>
	</div>
	
	<?php else: ?>
	<p>
		<?php echo Text::_('COM_AKEEBA_DISCOVER_ERROR_NOFILES'); ?>
	</p>
	<p>
		<button onclick="this.form.submit(); return false;" class="btn btn-default"><?php echo Text::_('COM_AKEEBA_DISCOVER_LABEL_GOBACK') ?></button>
	</p>
	<?php endif; ?>	
</form>