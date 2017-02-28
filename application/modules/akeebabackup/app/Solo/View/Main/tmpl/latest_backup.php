<?php
/**
 * @package		solo
 * @copyright	2014-2017 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license		GNU GPL version 3 or later
 */

use Awf\Text\Text;

/** @var \Solo\View\Main\Html $this */

?>
<div class="panel panel-default">
	<div class="panel-heading">
		<?php echo Text::_('SOLO_MAIN_LBL_LATEST_BACKUP') ?>
	</div>
	<?php if(empty($this->latestBackupDetails)): ?>
	<div class="panel-body">
		<div class="label label-default">
			<?php echo Text::_('COM_AKEEBA_BACKUP_STATUS_NONE'); ?>
		</div>
	</div>
	<?php else: ?>
	<table class="table table-striped">
		<tr>
			<th>
				<?php echo Text::_('COM_AKEEBA_BUADMIN_LABEL_START'); ?>
			</th>
			<td>
				<?php $backupDate = new \Awf\Date\Date($this->latestBackupDetails['backupstart']); echo $backupDate->format(Text::_('DATE_FORMAT_LC4')); ?>
			</td>
		</tr>
		<tr>
			<th>
				<?php echo Text::_('COM_AKEEBA_BUADMIN_LABEL_DESCRIPTION'); ?>
			</th>
			<td>
				<?php echo $this->escape($this->latestBackupDetails['description']); ?>
			</td>
		</tr>
		<tr>
			<th>
				<?php echo Text::_('COM_AKEEBA_BUADMIN_LABEL_STATUS'); ?>
			</th>
			<td>
				<?php switch ($this->latestBackupDetails['status']):
					case 'run': ?>
				<span class="label label-warning"><?php echo Text::_('COM_AKEEBA_BUADMIN_LABEL_STATUS_PENDING') ?></span>
				<?php break;case 'fail': ?>
				<span class="label label-danger"><?php echo Text::_('COM_AKEEBA_BUADMIN_LABEL_STATUS_FAIL') ?></span>
				<?php break;case 'ok':
					case 'complete': ?>
				<span class="label label-success"><?php echo Text::_('COM_AKEEBA_BUADMIN_LABEL_STATUS_OK') ?></span>
				<?php endswitch; ?>
			</td>
		</tr>
		<tr>
			<th>
				<?php echo Text::_('COM_AKEEBA_BUADMIN_LABEL_ORIGIN'); ?>
			</th>
			<td>
				<?php echo Text::_('COM_AKEEBA_BUADMIN_LABEL_ORIGIN_' . $this->latestBackupDetails['origin']) ?>
			</td>
		</tr>
		<tr>
			<th>
				<?php echo Text::_('COM_AKEEBA_BUADMIN_LABEL_TYPE'); ?>
			</th>
			<td>
				<?php echo $this->escape($this->latestBackupDetails['type_translated']); ?>
			</td>
		</tr>
	</table>
	<?php endif; ?>
</div>