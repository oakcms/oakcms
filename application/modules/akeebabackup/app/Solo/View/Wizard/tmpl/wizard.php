<?php
/**
 * @package     Solo
 * @copyright   2014-2017 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license     GNU GPL version 3 or later
 *
 * @var \Solo\View\Setup\Html $this
 */

use Awf\Text\Text;
use Solo\Helper\Escape;

/** @var \Solo\View\Wizard\Html $this */

$router = $this->container->router;
$config = \Akeeba\Engine\Factory::getConfiguration();

?>
<?php if (!AKEEBABACKUP_PRO && (rand(0, 9) == 0)): ?>
	<div style="border: thick solid green; border-radius: 10pt; padding: 1em; background-color: #f0f0ff; color: #333; font-weight: bold; text-align: center; margin: 1em 0">
		<p><?php echo Text::_('SOLO_MAIN_LBL_SUBSCRIBE_TEXT') ?></p>
		<form action="https://www.paypal.com/cgi-bin/webscr" method="post" style="text-align: center; margin: 0px;">
			<input type="hidden" name="cmd" value="_s-xclick" />
			<input type="hidden" name="hosted_button_id" value="3NTKQ3M2DYPYW" />
			<button onclick="this.form.submit(); return false;" class="btn btn-success">
				<img src="https://www.paypal.com/en_GB/i/btn/btn_donate_LG.gif" border="0">
				Donate via PayPal
			</button>
			<a class="small" style="font-weight: normal; color: #666" href="https://www.akeebabackup.com/subscribe/new/backupwp.html?layout=default">
				<?php echo Text::_('SOLO_MAIN_BTN_SUBSCRIBE_UNOBTRUSIVE'); ?>
			</a>
		</form>
	</div>
<?php endif; ?>

<div id="akeeba-confwiz">
	<div id="backup-progress-pane" style="display: none">
		<div class="alert alert-info">
			<?php echo Text::_('COM_AKEEBA_CONFWIZ_INTROTEXT'); ?>
		</div>

		<fieldset id="backup-progress-header">
			<legend><?php echo Text::_('COM_AKEEBA_CONFWIZ_PROGRESS') ?></legend>
			<div id="backup-progress-content">
				<div id="backup-steps">
					<div id="step-ajax" class="label label-default"><?php echo Text::_('COM_AKEEBA_CONFWIZ_AJAX'); ?></div>
					<div id="step-minexec" class="label label-default"><?php echo Text::_('COM_AKEEBA_CONFWIZ_MINEXEC'); ?></div>
					<div id="step-directory" class="label label-default"><?php echo Text::_('COM_AKEEBA_CONFWIZ_DIRECTORY'); ?></div>
					<div id="step-dbopt" class="label label-default"><?php echo Text::_('COM_AKEEBA_CONFWIZ_DBOPT'); ?></div>
					<div id="step-maxexec" class="label label-default"><?php echo Text::_('COM_AKEEBA_CONFWIZ_MAXEXEC'); ?></div>
					<div id="step-splitsize" class="label label-default"><?php echo Text::_('COM_AKEEBA_CONFWIZ_SPLITSIZE'); ?></div>
				</div>
				<div class="well">
					<div id="backup-substep">
					</div>
				</div>
			</div>
			<span id="ajax-worker"></span>
		</fieldset>

	</div>

	<div id="error-panel" class="alert alert-danger" style="display:none">
		<h2 class="alert-heading"><?php echo Text::_('COM_AKEEBA_CONFWIZ_HEADER_FAILED'); ?></h2>
		<div id="errorframe">
			<p id="backup-error-message">
			</p>
		</div>
	</div>

	<div id="backup-complete" style="display: none">
		<div class="alert alert-success alert-block">
			<h2 class="alert-heading"><?php echo Text::_('COM_AKEEBA_CONFWIZ_HEADER_FINISHED'); ?></h2>
			<div id="finishedframe">
				<p>
					<?php echo Text::_('COM_AKEEBA_CONFWIZ_CONGRATS') ?>
				</p>
			</div>
			<button class="btn btn-primary btn-large" onclick="window.location='<?php echo $router->route('index.php?&view=backup') ?>'; return false;">
				<span class="glyphicon glyphicon-compressed"></span>
				<?php echo Text::_('COM_AKEEBA_BACKUP'); ?>
			</button>
			<button class="btn btn-default" onclick="window.location='<?php echo $router->route('index.php?&view=configuration') ?>'; return false;">
				<span class="glyphicon glyphicon-wrench"></span>
				<?php echo Text::_('COM_AKEEBA_CONFIG'); ?>
			</button>
			<button class="btn btn-default" onclick="window.location='<?php echo $router->route('index.php?&view=schedule') ?>'; return false;">
				<span class="glyphicon glyphicon-calendar"></span>
				<?php echo Text::_('COM_AKEEBA_SCHEDULE'); ?>
			</button>
		</div>

	</div>
</div>