<?php
/**
 * @package     Solo
 * @copyright   2014-2017 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license     GNU GPL version 3 or later
 */

use \Awf\Text\Text;
use \Awf\Html;

// Used for type hinting
/** @var  \Solo\View\Log\Html  $this */

$router = $this->container->router;

$inCMS = $this->container->segment->get('insideCMS', false);
?>
<?php if(count($this->logs)): ?>
<form name="adminForm" id="adminForm" action="<?php echo $router->route('index.php?view=log') ?>" method="POST" class="form-inline">
	<input type="hidden" name="token" value="<?php echo $this->container->session->getCsrfToken()->getValue() ?>" >
	<fieldset>
		<label for="tag">
			<?php echo Text::_('COM_AKEEBA_LOG_CHOOSE_FILE_TITLE'); ?>
		</label>
		<?php echo Html\Select::genericList($this->logs, 'tag', 'onchange=this.form.submit()', 'value', 'text', $this->tag, 'tag') ?>

		<?php if(!empty($this->tag)): ?>
		<button class="btn btn-primary" onclick="window.location='<?php echo $router->route('index.php?view=log&format=raw&task=download&tag=' . urlencode($this->tag)); ?>'; return false;">
			<span class="glyphicon glyphicon-download"></span>
			<?php echo Text::_('COM_AKEEBA_LOG_LABEL_DOWNLOAD'); ?>
		</button>

		<br/>
		<hr/>
		<div class="iframe-holder" id="frame-holder">
			<?php if ($this->logTooBig):?>
				<p class="alert alert-info">
					<?php echo Text::sprintf('COM_AKEEBA_LOG_SIZE_WARNING', number_format($this->logSize / (1024 * 1024), 2))?>
				</p>
				<span class="btn btn-sm btn-default" id="showlog">
				<?php echo Text::_('COM_AKEEBA_LOG_SHOW_LOG')?>
			</span>
			<?php else:?>
				<iframe
					src="<?php echo $router->route('index.php?view=log&task=iframe&tmpl=component&layout=raw&tag=' . urlencode($this->tag)) ?>"
					width="99%" height="500px">
				</iframe>
			<?php endif;?>
		</div>
		<?php endif; ?>

	</fieldset>
</form>
<?php else: ?>
<div class="alert alert-danger">
	<?php echo Text::_('COM_AKEEBA_LOG_NONE_FOUND') ?>
</div>
<?php endif; ?>