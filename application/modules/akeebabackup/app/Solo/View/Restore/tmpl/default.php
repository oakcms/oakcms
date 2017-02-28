<?php
/**
 * @package        solo
 * @copyright      2014-2017 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license        GNU GPL version 3 or later
 */

use Awf\Text\Text;
use Solo\Helper\Escape;

/** @var   \Solo\View\Restore\Html $this */

$router = $this->container->router;
$token = $this->container->session->getCsrfToken()->getValue();

echo $this->loadAnyTemplate('Common/ftp_browser');
echo $this->loadAnyTemplate('Common/ftp_test');
echo $this->loadAnyTemplate('Common/folder_browser');
?>

<form action="<?php echo $router->route('index.php?view=restore&task=start&id=' . $this->id)?>" method="POST" name="adminForm" id="adminForm" class="form-horizontal" role="form">
	<input type="hidden" name="token" value="<?php echo $token ?>">

	<fieldset>
		<legend><?php echo Text::_('COM_AKEEBA_RESTORE_LABEL_EXTRACTIONMETHOD'); ?></legend>

		<div class="form-group">
			<label class="control-label col-sm-3 col-xs-12" for="procengine">
				<?php echo Text::_('COM_AKEEBA_RESTORE_LABEL_EXTRACTIONMETHOD'); ?>
			</label>

			<div class="col-sm-9 col-xs-12">
				<?php echo \Awf\Html\Select::genericList($this->extractionmodes, 'procengine', array('class' => 'form-control'), 'value', 'text', $this->ftpparams['procengine']); ?>
				<p class="help-block">
					<?php echo Text::_('COM_AKEEBA_RESTORE_LABEL_REMOTETIP'); ?>
				</p>
			</div>
		</div>
	</fieldset>

	<fieldset <?php echo $this->getModel()->getState('extension', '') == 'jps' ? '' : 'style="display: none;"'?>>
		<legend><?php echo Text::_('COM_AKEEBA_RESTORE_LABEL_JPSOPTIONS'); ?></legend>

		<div class="form-group">
			<label class="control-label col-sm-3 col-xs-12">
				<?php echo Text::_('COM_AKEEBA_CONFIG_JPS_KEY_TITLE') ?>
			</label>

			<div class="col-sm-9 col-xs-12">
				<input value="" type="password" class="form-control" id="jps_key" name="jps_key" placeholder="<?php echo Text::_('COM_AKEEBA_CONFIG_JPS_KEY_TITLE') ?>" autocomplete="off">
			</div>
		</div>
	</fieldset>

	<fieldset id="ftpOptions">
		<legend><?php echo Text::_('COM_AKEEBA_RESTORE_LABEL_FTPOPTIONS'); ?></legend>

		<input id="var[ftp.passive_mode]" type="checkbox" checked autocomplete="off" style="display: none">
		<input id="var[ftp.ftps]" type="checkbox" autocomplete="off" style="display: none">

		<div class="form-group">
			<label class="control-label col-sm-3 col-xs-12" for="ftp_host">
				<?php echo Text::_('COM_AKEEBA_CONFIG_DIRECTFTP_HOST_TITLE') ?>
			</label>

			<div class="col-sm-9 col-xs-12">
				<input id="var[ftp.host]" name="ftp_host" value="<?php echo $this->ftpparams['ftp_host']; ?>" type="text" class="form-control">
			</div>
		</div>

		<div class="form-group">
			<label class="control-label col-sm-3 col-xs-12" for="ftp_port">
				<?php echo Text::_('COM_AKEEBA_CONFIG_DIRECTFTP_PORT_TITLE') ?>
			</label>

			<div class="col-sm-9 col-xs-12">
				<input id="var[ftp.port]" name="ftp_port" value="<?php echo $this->ftpparams['ftp_port']; ?>" type="text" class="form-control">
			</div>
		</div>

		<div class="form-group">
			<label class="control-label col-sm-3 col-xs-12" for="ftp_user">
				<?php echo Text::_('COM_AKEEBA_CONFIG_DIRECTFTP_USER_TITLE') ?>
			</label>

			<div class="col-sm-9 col-xs-12">
				<input id="var[ftp.user]" name="ftp_user" value="<?php echo $this->ftpparams['ftp_user']; ?>" type="text" class="form-control">
			</div>
		</div>

		<div class="form-group">
			<label class="control-label col-sm-3 col-xs-12" for="ftp_pass">
				<?php echo Text::_('COM_AKEEBA_CONFIG_DIRECTFTP_PASSWORD_TITLE') ?>
			</label>

			<div class="col-sm-9 col-xs-12">
				<input id="var[ftp.pass]" name="ftp_pass" value="<?php echo $this->ftpparams['ftp_pass']; ?>"
					   type="password" class="form-control">
			</div>
		</div>

		<div class="form-group">
			<label class="control-label col-sm-3 col-xs-12" for="ftp_root">
				<?php echo Text::_('COM_AKEEBA_CONFIG_DIRECTFTP_INITDIR_TITLE') ?>
			</label>

			<div class="col-sm-9 col-xs-12">
				<div class="input-group">
					<input id="var[ftp.initial_directory]" name="ftp_root" value="<?php echo $this->ftpparams['ftp_root']; ?>" type="text" class="form-control">
					<!--
					<div class="input-group-btn">
						<button class="btn btn-default" id="ftp-browse" onclick="return false;">
							<span class="glyphicon glyphicon-folder-open"></span>
							<?php echo Text::_('COM_AKEEBA_CONFIG_UI_BROWSE') ?>
						</button>
					</div>
					-->
				</div>
			</div>
		</div>
	</fieldset>

	<div class="col-sm-9 col-sm-push-3 col-xs-12">
		<button class="btn btn-primary btn-lg" id="backup-start">
			<span class="glyphicon glyphicon-repeat"></span>
			<?php echo Text::_('COM_AKEEBA_RESTORE_LABEL_START') ?>
		</button>
		<button class="btn btn-default" id="var[ftp.test]" onclick="return false;">
			<?php echo Text::_('COM_AKEEBA_CONFIG_DIRECTFTP_TEST_TITLE') ?>
		</button>
	</div>

</form>