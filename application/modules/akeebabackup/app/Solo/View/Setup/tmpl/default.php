<?php
/**
 * @package     Solo
 * @copyright   2014-2017 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license     GNU GPL version 3 or later
 *
 * @var \Solo\View\Setup\Html $this
 */

use Awf\Text\Text;

/** @var \Solo\View\Setup\Html $this */

?>

<h1>
	<?php echo Text::_('SOLO_SETUP_LBL_WELCOME_HEAD'); ?>
</h1>
<p>
	<?php echo Text::_('SOLO_SETUP_LBL_WELCOME'); ?>
</p>
<p></p>

<!--[if IE]>
<div class="margin: 20px; padding: 20px; background-color: yellow; border: 5px solid red; font-size: 14pt;">
	<?php echo Text::sprintf('SOLO_SETUP_LBL_ANCIENTIENOTICE', 'http://windows.microsoft.com/en-us/internet-explorer/download-ie', 'http://www.google.com/chrome') ?>
</div>
<![endif]-->

<?php if (!$this->reqMet): ?>
	<div class="alert alert-danger">
		<button type="button" class="close" data-dismiss="alert">&times;</button>
		<?php echo Text::_('SOLO_SETUP_LBL_REQUIREDREDTEXT'); ?>
	</div>
<?php endif; ?>

<div class="panel-group" id="accordion">
	<div class="panel panel-<?php echo $this->reqMet ? 'success' : 'danger'?>">
		<div class="panel-heading">
			<h4 class="panel-title">
				<a data-toggle="collapse" data-parent="#accordion" href="#collapseRequired">
					<span class="glyphicon glyphicon-<?php echo $this->reqMet ? 'ok' : 'exclamation' ?>-sign pull-right"></span>
					<?php echo Text::_('SOLO_SETUP_HEADER_REQUIRED') ?>
					<small>&mdash; <?php echo Text::_('SOLO_SETUP_LBL_CLICK_TO_SHOW_HIDE'); ?></small>
				</a>
			</h4>
		</div>
		<div id="collapseRequired" class="panel-collapse collapse <?php echo $this->reqMet ? '' : 'in'?>">
			<div class="panel-body">
				<p><?php echo Text::_('SOLO_SETUP_LBL_REQUIRED') ?></p>
				<table class="table table-striped" width="100%">
					<thead>
					<tr>
						<th><?php echo Text::_('SOLO_SETUP_LBL_SETTING') ?></th>
						<th><?php echo Text::_('SOLO_SETUP_LBL_CURRENT_SETTING') ?></th>
					</tr>
					</thead>
					<tbody>
					<?php foreach ($this->reqSettings as $option): ?>
						<tr>
							<td>
								<?php echo $option['label']; ?>
								<?php if (array_key_exists('notice',$option)): ?>
									<div class="help-block">
										<?php echo $option['notice']; ?>
									</div>
								<?php endif; ?>
							</td>
							<td>
								<span class="label label-<?php echo $option['current'] ? 'success' : ($option['warning'] ? 'warning' : 'danger'); ?>">
									<?php echo $option['current'] ? Text::_('SOLO_YES') : Text::_('SOLO_NO'); ?>
								</span>
							</td>
						</tr>
					<?php endforeach; ?>
					</tbody>
				</table>
			</div>
		</div>
	</div>

	<div class="panel panel-<?php echo $this->recMet ? 'success' : 'warning'?>">
		<div class="panel-heading">
			<h4 class="panel-title">
				<a data-toggle="collapse" data-parent="#accordion" href="#collapseReccomended">
					<span class="glyphicon glyphicon-<?php echo $this->recMet ? 'ok' : 'warning' ?>-sign pull-right"></span>
					<?php echo Text::_('SOLO_SETUP_HEADER_RECOMMENDED') ?>
					<small>&mdash; <?php echo Text::_('SOLO_SETUP_LBL_CLICK_TO_SHOW_HIDE'); ?></small>
				</a>
			</h4>
		</div>
		<div id="collapseReccomended" class="panel-collapse collapse <?php echo $this->recMet ? '' : 'in'?>">
			<div class="panel-body">
				<p><?php echo Text::_('SOLO_SETUP_LBL_RECOMMENDED') ?></p>
				<table class="table table-striped" width="100%">
					<thead>
					<tr>
						<th><?php echo Text::_('SOLO_SETUP_LBL_SETTING') ?></th>
						<th><?php echo Text::_('SOLO_SETUP_LBL_RECOMMENDED_VALUE') ?></th>
						<th><?php echo Text::_('SOLO_SETUP_LBL_CURRENT_SETTING') ?></th>
					</tr>
					</thead>
					<tbody>
					<?php foreach ($this->recommendedSettings as $option): ?>
						<tr>
							<td>
								<?php echo $option['label']; ?>
							</td>
							<td>
								<span class="label label-info">
									<?php echo $option['recommended'] ? Text::_('SOLO_ON') : Text::_('SOLO_OFF'); ?>
								</span>
							</td>
							<td>
								<span class="label label-<?php echo ($option['current'] == $option['recommended']) ? 'success' : 'warning'; ?>">
									<?php echo $option['current'] ? Text::_('SOLO_ON') : Text::_('SOLO_OFF'); ?>
								</span>
							</td>
						</tr>
					<?php endforeach; ?>
					</tbody>
				</table>
			</div>
		</div>
	</div>

</div>


