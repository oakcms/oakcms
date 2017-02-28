<?php
/**
 * @package		solo
 * @copyright	2014-2017 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license		GNU GPL version 3 or later
 */

use Awf\Text\Text;

/** @var   \Solo\View\Update\Html  $this */

$router = $this->container->router;

$releaseNotes = $this->updateInfo->get('releasenotes');
$infoUrl = $this->updateInfo->get('infourl');
$requirePlatformName = $this->container->segment->get('platformNameForUpdates', 'php');

?>

<?php if (!empty($releaseNotes)): ?>
<div class="modal fade" id="releaseNotesPopup" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-header">
        <h4 class="modal-title" id="myModalLabel">
			<?php echo Text::_('SOLO_UPDATE_RELEASENOTES'); ?>
        </h4>
    </div>
    <div class="modal-body">
		<?php echo $releaseNotes; ?>
    </div>
</div>
<?php endif; ?>

<?php if ($this->needsDownloadId): ?>
<p class="alert alert-danger">
	<?php echo Text::_('SOLO_UPDATE_ERROR_NEEDSAUTH'); ?>
</p>
<?php endif; ?>

<?php if ($this->updateInfo->get('hasUpdate', 0)): ?>
	<div class="alert alert-warning">
		<h4>
			<span class="glyphicon glyphicon-warning-sign"></span>
			<?php echo Text::_('SOLO_UPDATE_HASUPDATES_HEAD') ?>
		</h4>
	</div>
<?php elseif (!$this->updateInfo->get('minstabilityMatch', 0)): ?>
	<div class="alert alert-danger">
		<h4>
			<span class="glyphicon glyphicon-ban-circle"></span>
			<?php echo Text::_('SOLO_UPDATE_MINSTABILITY_HEAD') ?>
		</h4>
	</div>
<?php elseif (!$this->updateInfo->get('platformMatch', 0)): ?>
	<div class="alert alert-danger">
		<h4>
			<span class="glyphicon glyphicon-ban-circle"></span>
			<?php if (empty($requirePlatformName) || ($requirePlatformName == 'php')): ?>
			<?php echo Text::_('SOLO_UPDATE_PLATFORM_HEAD') ?>
			<?php elseif (empty($requirePlatformName) || ($requirePlatformName == 'wordpress')): ?>
			<?php echo Text::_('SOLO_UPDATE_WORDPRESS_PLATFORM_HEAD') ?>
			<?php elseif (empty($requirePlatformName) || ($requirePlatformName == 'joomla')): ?>
			<?php echo Text::_('SOLO_UPDATE_JOOMLA_PLATFORM_HEAD') ?>
			<?php endif; ?>
		</h4>
	</div>
<?php else: ?>
	<div class="alert alert-success">
		<h4>
			<span class="glyphicon glyphicon-ok-sign"></span>
			<?php echo Text::_('SOLO_UPDATE_NOUPDATES_HEAD') ?>
		</h4>
	</div>
<?php endif; ?>

<table class="liveupdate-infotable table table-striped">
	<tr>
		<td><?php echo Text::_('SOLO_UPDATE_CURRENTVERSION') ?></td>
		<td>
			<span class="label label-info">
				<?php echo AKEEBABACKUP_VERSION ?>
			</span>
		</td>
	</tr>
	<tr>
		<td><?php echo Text::_('SOLO_UPDATE_LATESTVERSION') ?></td>
		<td>
			<span class="label label-success">
				<?php echo $this->updateInfo->get('version') ?>
			</span>
		</td>
	</tr>
	<tr>
		<td><?php echo Text::_('SOLO_UPDATE_LATESTRELEASED') ?></td>
		<td><?php echo $this->updateInfo->get('date') ?></td>
	</tr>
	<tr>
		<td><?php echo Text::_('SOLO_UPDATE_DOWNLOADURL') ?></td>
		<td>
			<a href="<?php echo $this->updateInfo->get('link') ?>">
				<?php echo $this->escape($this->updateInfo->get('link')) ?>
			</a>
		</td>
	</tr>
	<?php if (!empty($releaseNotes) || !empty($infoUrl)): ?>
		<tr>
			<td><?php echo Text::_('SOLO_UPDATE_RELEASEINFO') ?></td>
			<td>
				<?php if (!empty($releaseNotes)): ?>
					<button href="#" id="btnLiveUpdateReleaseNotes" data-toggle="modal" data-target="#releaseNotesPopup" class="btn btn-link">
						<?php echo Text::_('SOLO_UPDATE_RELEASENOTES') ?>
					</button>
				<?php endif; ?>
				<?php if (!empty($releaseNotes) && !empty($infoUrl)): ?>
					&nbsp;&bull;&nbsp;
				<?php endif; ?>
				<?php if (!empty($infoUrl)): ?>
					<a href="<?php echo $infoUrl ?>" target="_blank" class="btn btn-link">
						<?php echo Text::_('SOLO_UPDATE_READMOREINFO') ?>
					</a>
				<?php endif; ?>
			</td>
		</tr>
	<?php endif; ?>
</table>

<p>
	<?php if ($this->updateInfo->get('hasUpdate', 0)): ?>
		<?php $disabled = $this->needsDownloadId ? 'disabled="disabled"' : '' ?>
		<a <?php echo $disabled ?>
			href="<?php echo $router->route('index.php?view=update&task=download') ?>"
			class="btn btn-lg btn-primary">
			<span class="glyphicon glyphicon-arrow-right"></span>
			<?php echo Text::_('SOLO_UPDATE_DO_UPDATE') ?>
		</a>
	<?php endif; ?>
	<a href="<?php echo $router->route('index.php?view=update&force=1') ?>"
		class="btn btn-default">
		<span class="glyphicon glyphicon-refresh"></span>
		<?php echo Text::_('SOLO_UPDATE_REFRESH_INFO') ?>
	</a>
</p>