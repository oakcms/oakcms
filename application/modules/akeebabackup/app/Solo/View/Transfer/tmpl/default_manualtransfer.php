<?php
/**
 * @package		solo
 * @copyright	2014-2017 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license		GNU GPL version 3 or later
 */

// Protect from unauthorized access
use Awf\Text\Text;
use Solo\Helper\Utils;

/** @var  $this  Solo\View\Transfer\Html */

$dotPos = strrpos($this->latestBackup['archivename'], '.');
$extension = substr($this->latestBackup['archivename'], $dotPos + 1);
$bareName = basename($this->latestBackup['archivename'], '.' . $extension);
// Different YouTube video URL depending on whether we're inside Akeeba Backup for WordPress or Akeeba Solo
$videoCode = $this->container->segment->get('insideCMS', false) ? 'KFI1Ys6Mv2Y' : 'xaYgxsK0-S0';

?>
<fieldset id="akeeba-transfer-manualtransfer" style="display: none;">
	<legend>
		<?php echo Text::_('COM_AKEEBA_TRANSFER_HEAD_MANUALTRANSFER'); ?>
	</legend>

	<div class="alert alert-info">
		<?php echo Text::_('COM_AKEEBA_TRANSFER_LBL_MANUALTRANSFER_INFO'); ?>
	</div>

	<p style="text-align: center">
		<iframe width="640" height="480" src="https://www.youtube.com/embed/<?php echo $videoCode ?>" frameborder="0" allowfullscreen></iframe>
	</p>

	<h3><?php echo Text::_('COM_AKEEBA_BUADMIN_LBL_BACKUPINFO') ?></h3>

	<p>
		<strong><?php echo Text::_('COM_AKEEBA_BUADMIN_LBL_ARCHIVENAME') ?></strong>
		<br/>
		<?php if ($this->latestBackup['multipart'] < 2): ?>
			<?php echo htmlentities($this->latestBackup['archivename']) ?>
		<?php else: ?>
		<?php echo Text::sprintf('COM_AKEEBA_TRANSFER_LBL_MANUALTRANSFER_MULTIPART', $this->latestBackup['multipart']); ?>
		<ul>
			<?php for ($i = 1; $i < $this->latestBackup['multipart']; $i++): ?>
				<li><?php echo htmlentities($bareName . '.' . substr($extension, 0, 1) . sprintf('%02u', $i)); ?></li>
			<?php endfor; ?>
			<li>
				<?php echo htmlentities($this->latestBackup['archivename']); ?>
			</li>
		</ul>
		<?php endif; ?>
	</p>

	<p>
		<strong><?php echo Text::_('COM_AKEEBA_BUADMIN_LBL_ARCHIVEPATH') ?></strong>
		<br/>
		<?php echo htmlentities(Utils::getRelativePath(APATH_BASE, dirname($this->latestBackup['absolute_path']))) ?>
	</p>
</fieldset>