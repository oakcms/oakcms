<?php
/**
 * @package		solo
 * @copyright	2014-2017 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license		GNU GPL version 3 or later
 */

use Awf\Text\Text;

/** @var   \Solo\View\Update\Html  $this */

$router = $this->container->router;
$token = $this->container->session->getCsrfToken()->getValue();

?>
<div id="extractProgress">
	<div class="alert alert-warning">
		<span><?php echo Text::_('COM_AKEEBA_BACKUP_TEXT_BACKINGUP')?></span>
	</div>

	<div id="extractProgressBarContainer" class="progress progress-striped active">
		<div id="extractProgressBar" class="bar" role="progressbar" aria-valuemin="0" aria-valuemax="100" style="width: 0%">
			<span class="sr-only" id="extractProgressBarInfo">0%</span>
		</div>
	</div>

	<div class="alert alert-info" id="extractProgressInfo">
		<h4>
			<?php echo Text::_('SOLO_UPDATE_EXTRACT_LBL_EXTRACTPROGRESS') ?>
		</h4>
		<div class="panel-body" id="extractProgressBarText">
			<span class="icon icon-signal"></span>
			<span id="extractProgressBarTextPercent">0</span> %
			<br/>
			<span class="icon icon-folder-open"></span>
			<span id="extractProgressBarTextIn">0 KiB</span>
			<br/>
			<span class="icon icon-hdd"></span>
			<span id="extractProgressBarTextOut">0 KiB</span>
			<br/>
			<span class="icon icon-file"></span>
			<span id="extractProgressBarTextFile"></span>
		</div>
	</div>
</div>

<div id="extractPingError" style="display: none">
	<div class="alert alert-error">
		<p>
			<span class="icon icon-exclamation-sign"></span>
			<?php echo Text::_('SOLO_UPDATE_EXTRACT_ERR_CANTPING_TEXT'); ?>
		</p>
		<p>
			<?php echo Text::_('SOLO_UPDATE_EXTRACT_ERR_CANTPING_CONTACTHOST'); ?>
		</p>
	</div>
</div>

<div id="extractError" style="display: none">
	<div class="alert alert-danger">
		<h4>
			<?php echo Text::_('SOLO_UPDATE_EXTRACT_ERR_EXTRACTERROR_HEADER') ?>
		</h4>
		<div class="panel-body" id="extractErrorText"></div>
	</div>
</div>
