<?php
/**
 * @package		solo
 * @copyright	2014-2017 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license		GNU GPL version 3 or later
 */

// Protect from unauthorized access
use Awf\Text\Text;

/** @var  $this  Solo\View\Transfer\Html */

?>
<fieldset id="akeeba-transfer-upload" style="display: none;">
	<legend>
		<?php echo Text::_('COM_AKEEBA_TRANSFER_HEAD_UPLOAD'); ?>
	</legend>

	<div class="alert alert-danger" style="display: none" id="akeeba-transfer-upload-error">
	</div>

	<div id="akeeba-transfer-upload-area-upload" style="display: none">
		<div id="backup-steps">
			<div class="label label-warning" id="akeeba-transfer-upload-lbl-kickstart">
				<?php echo Text::_('COM_AKEEBA_TRANSFER_LBL_UPLOAD_KICKSTART'); ?>
			</div>
			<div class="label label-default" id="akeeba-transfer-upload-lbl-archive">
				<?php echo Text::_('COM_AKEEBA_TRANSFER_LBL_UPLOAD_BACKUP'); ?>
			</div>
		</div>
		<div id="backup-status" class="well">
			<div id="backup-step">
				&#9729; <span id="akeeba-transfer-upload-percent"></span>
			</div>
			<div id="backup-substep">
				&#128190; <span id="akeeba-transfer-upload-size"></span>
			</div>
		</div>
	</div>

	<div id="akeeba-transfer-upload-area-kickstart" style="display: none">
		<p>
			<a class="btn btn-success btn-large" id="akeeba-transfer-upload-btn-kickstart" href="" target="_blank">
				<span class="icon icon-arrow-right"></span>
				<?php echo Text::_('COM_AKEEBA_TRANSFER_BTN_OPEN_KICKSTART'); ?>
			</a>
		</p>

		<div class="help-block">
			<?php echo Text::_('COM_AKEEBA_TRANSFER_LBL_OPEN_KICKSTART_INFO'); ?>
		</div>
	</div>

</fieldset>