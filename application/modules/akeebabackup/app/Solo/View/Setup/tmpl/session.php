<?php
/**
 * @package		solo
 * @copyright	2014-2017 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license		GNU GPL version 3 or later
 */

use Awf\Text\Text;

/** @var   \Solo\View\Setup\Html  $this */

$sessionPath = $this->container->session->getSavePath();
$this->container->application->getDocument()->addScript(\Awf\Uri\Uri::base(false, $this->container) . '/media/js/solo/setup.js');
$router = $this->container->router;
$error = $this->container->input->get('error', null, 'base64');

if (!empty($error))
{
	$error = base64_decode($error);
}
else
{
	$error = null;
}

?>
<div class="alert alert-warning">
	<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
	<h4>
		<?php echo Text::_('SOLO_SETUP_SESSION_LBL_WARNING_HEADER')?>
	</h4>
	<p>
		<?php echo Text::sprintf('SOLO_SETUP_SESSION_LBL_WARNING_BODY', $sessionPath)?>
	</p>
</div>

<?php if (!is_null($error)): ?>
<div class="alert alert-danger">
	<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
	<h4>
		<?php echo Text::_('SOLO_SETUP_SESSION_LBL_ERROR_HEADER')?>
	</h4>
	<p>
		<?php echo $error ?>
	</p>
</div>
<?php endif; ?>

<form action="<?php echo $router->route('index.php?view=setup&task=savesession')?>" method="post" role="form" class="form-horizontal">
	<div class="form-group">
		<label for="fs_driver" class="col-sm-2 control-label">
			<?php echo Text::_('SOLO_SETUP_LBL_FS_DRIVER'); ?>
		</label>
		<div class="col-sm-10">
			<?php echo \Solo\Helper\Setup::fsDriverSelect($this->params['fs.driver'], false); ?>
			<div class="help-block">
				<?php echo Text::_('SOLO_SETUP_LBL_FS_DRIVER_HELP') ?>
			</div>
		</div>
	</div>

	<div id="ftp_options">
		<div class="form-group">
			<label for="fs_host" class="col-sm-2 control-label">
				<?php echo Text::_('SOLO_SETUP_LBL_FS_FTP_HOST'); ?>
			</label>
			<div class="col-sm-10">
				<input type="text" class="form-control" name="fs_host" id="fs_host" value="<?php echo $this->params['fs.host'] ?>">
				<div class="help-block">
					<?php echo Text::_('SOLO_SETUP_LBL_FS_FTP_HOST_HELP') ?>
				</div>
			</div>
		</div>

		<div class="form-group">
			<label for="fs_port" class="col-sm-2 control-label">
				<?php echo Text::_('SOLO_SETUP_LBL_FS_FTP_PORT'); ?>
			</label>
			<div class="col-sm-10">
				<input type="text" class="form-control" name="fs_port" id="fs_port" value="<?php echo $this->params['fs.port'] ?>">
				<div class="help-block">
					<?php echo Text::_('SOLO_SETUP_LBL_FS_FTP_PORT_HELP') ?>
				</div>
			</div>
		</div>

		<div class="form-group">
			<label for="fs_username" class="col-sm-2 control-label">
				<?php echo Text::_('SOLO_SETUP_LBL_FS_FTP_USERNAME'); ?>
			</label>
			<div class="col-sm-10">
				<input type="text" class="form-control" name="fs_username" id="fs_username" value="<?php echo $this->params['fs.username'] ?>">
				<div class="help-block">
					<?php echo Text::_('SOLO_SETUP_LBL_FS_FTP_USERNAME_HELP') ?>
				</div>
			</div>
		</div>

		<div class="form-group">
			<label for="fs_password" class="col-sm-2 control-label">
				<?php echo Text::_('SOLO_SETUP_LBL_FS_FTP_PASSWORD'); ?>
			</label>
			<div class="col-sm-10">
				<input type="password" class="form-control" name="fs_password" id="fs_password" value="<?php echo $this->params['fs.password'] ?>">
				<div class="help-block">
					<?php echo Text::_('SOLO_SETUP_LBL_FS_FTP_PASSWORD_HELP') ?>
				</div>
			</div>
		</div>

		<div class="form-group">
			<label for="fs_directory" class="col-sm-2 control-label">
				<?php echo Text::_('SOLO_SETUP_LBL_FS_FTP_DIRECTORY'); ?>
			</label>
			<div class="col-sm-10">
				<input type="text" class="form-control" name="fs_directory" id="fs_directory" value="<?php echo $this->params['fs.directory'] ?>">
				<div class="help-block">
					<?php echo Text::_('SOLO_SETUP_LBL_FS_FTP_DIRECTORY_HELP') ?>
				</div>
			</div>
		</div>
	</div>

	<div class="col-sm-10 col-sm-push-2">
		<button type="submit" id="setupFormSubmit" class="btn btn-primary">
			<?php echo Text::_('SOLO_BTN_SUBMIT') ?>
		</button>
	</div>

</form>