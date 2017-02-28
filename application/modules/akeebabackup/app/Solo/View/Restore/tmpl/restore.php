<?php
/**
 * @package		solo
 * @copyright	2014-2017 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license		GNU GPL version 3 or later
 */

use Awf\Text\Text;

/** @var   \Solo\View\Restore\Html $this */

$router = $this->container->router;

?>
<div class="alert alert-warning">
	<span class="glyphicon glyphicon-warning-sign"></span>
	<?php echo Text::_('COM_AKEEBA_RESTORE_LABEL_DONOTCLOSE'); ?>
</div>

<div id="restoration-progress">
	<h3><?php echo Text::_('COM_AKEEBA_RESTORE_LABEL_INPROGRESS') ?></h3>

	<table class="table table-striped">
		<tr>
			<td width="25%">
				<?php echo Text::_('COM_AKEEBA_RESTORE_LABEL_BYTESREAD'); ?>
			</td>
			<td>
				<span id="extbytesin"></span>
			</td>
		</tr>
		<tr>
			<td width="25%">
				<?php echo Text::_('COM_AKEEBA_RESTORE_LABEL_BYTESEXTRACTED'); ?>
			</td>
			<td>
				<span id="extbytesout"></span>
			</td>
		</tr>
		<tr>
			<td width="25%">
				<?php echo Text::_('COM_AKEEBA_RESTORE_LABEL_FILESEXTRACTED'); ?>
			</td>
			<td>
				<span id="extfiles"></span>
			</td>
		</tr>
	</table>

	<div id="response-timer">
		<div class="color-overlay"></div>
		<div class="text"></div>
	</div>
</div>

<div id="restoration-error" style="display:none">
	<div class="alert alert-danger">
		<h3 class="alert-heading"><?php echo Text::_('COM_AKEEBA_RESTORE_LABEL_FAILED'); ?></h3>
		<div id="errorframe">
			<p><?php echo Text::_('COM_AKEEBA_RESTORE_LABEL_FAILED_INFO'); ?></p>
			<p id="backup-error-message">
			</p>
		</div>
	</div>
</div>

<div id="restoration-extract-ok" style="display:none">
	<div class="alert alert-success">
		<h3 class="alert-heading"><?php echo Text::_('COM_AKEEBA_RESTORE_LABEL_SUCCESS'); ?></h3>
		<?php if (empty($this->siteURL)): ?>
		<p>
			<?php echo Text::_('SOLO_RESTORE_LABEL_SUCCESS_INFO'); ?>
		</p>
		<?php else: ?>
		<p>
			<?php echo Text::sprintf('SOLO_RESTORE_LABEL_SUCCESS_INFO_HASURL', $this->siteURL, $this->siteURL); ?>
		</p>
		<?php endif; ?>
	</div>

	<?php if (!empty($this->siteURL)): ?>
		<p>
			<button class="btn btn-success btn-lg" id="restoration-runinstaller" onclick="Solo.Restore.runInstaller('<?php echo $this->siteURL?>'); return false;">
				<span class="fa fa-rocket"></span>
				<?php echo Text::_('SOLO_RESTORE_BTN_INSTALLER'); ?>
			</button>
		</p>
		<p>
			<button class="btn btn-success btn-lg" id="restoration-finalize" style="display: none;" onclick="Solo.Restore.finalize(); return false;">
				<span class="fa fa-power-off"></span>
				<?php echo Text::_('COM_AKEEBA_RESTORE_LABEL_FINALIZE'); ?>
			</button>
		</p>
	<?php else: ?>
		<button class="btn btn-danger btn-lg" id="restoration-finalize" onclick="Solo.Restore.finalize(); return false;">
			<span class="fa fa-power-off"></span>
			<?php echo Text::_('COM_AKEEBA_RESTORE_LABEL_FINALIZE'); ?>
		</button>
	<?php endif; ?>
</div>