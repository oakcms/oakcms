<?php
/**
 * @package     Solo
 * @copyright   2014-2017 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license     GNU GPL version 3 or later
 *
 * @var \Solo\View\Setup\Html $this
 */

use Awf\Text\Text;
use Awf\Utils\Template;
use Solo\Helper\Escape;

$router = $this->container->router;

/** @var \Solo\View\Setup\Html $this */

$headJavascript = <<< JS
Solo.System.documentReady(function() {
   Solo.Setup.init();
});
JS;

Template::addJs('media://js/solo/gui-helpers.js', $this->container->application);
Template::addJs('media://js/solo/modal.js', $this->container->application);
Template::addJs('media://js/solo/ajax.js', $this->container->application);
Template::addJs('media://js/solo/system.js', $this->container->application);
Template::addJs('media://js/solo/tooltip.js', $this->container->application);
Template::addJs('media://js/piecon.js', $this->container->application);
Template::addJs('media://js/solo/setup.js', $this->container->application);

$this->container->application->getDocument()->addScriptDeclaration($headJavascript);

echo $this->loadAnyTemplate('Common/ftp_browser');
echo $this->loadAnyTemplate('Common/sftp_browser');
echo $this->loadAnyTemplate('Common/ftp_test');
?>

<form action="<?php echo \Awf\Uri\Uri::rebase('?view=setup&task=finish', $this->container) ?>" method="post" name="setupForm" id="setupForm" class="form-horizontal" role="form">

<div class="panel panel-default">
	<div class="panel-heading">
		<h4 class="panel-title">
			<span class="glyphicon glyphicon-cog pull-right"></span>
			<?php echo Text::_('SOLO_SETUP_LBL_APPSETUP') ?>
		</h4>
	</div>
	<div class="panel-body">
			<div class="form-group">
				<label for="timezone" class="col-sm-2 control-label">
					<?php echo Text::_('SOLO_SETUP_LBL_TIMEZONE'); ?>
				</label>
				<div class="col-sm-10">
					<?php echo \Solo\Helper\Setup::timezoneSelect($this->params['timezone']); ?>
					<div class="help-block">
						<?php echo Text::_('SOLO_SETUP_LBL_TIMEZONE_HELP') ?>
					</div>
				</div>
			</div>

			<div class="form-group">
				<label for="live_site" class="col-sm-2 control-label">
					<?php echo Text::_('SOLO_SETUP_LBL_LIVESITE'); ?>
				</label>
				<div class="col-sm-10">
					<input type="text" class="form-control" name="live_site" id="live_site" value="<?php echo $this->params['live_site'] ?>">
					<div class="help-block">
						<?php echo Text::_('SOLO_SETUP_LBL_LIVESITE_HELP') ?>
					</div>
				</div>
			</div>

			<div class="form-group">
				<label for="session_timeout" class="col-sm-2 control-label">
					<?php echo Text::_('SOLO_SETUP_LBL_SESSIONTIMEOUT'); ?>
				</label>
				<div class="col-sm-10">
					<input type="text" class="form-control" name="session_timeout" id="session_timeout" value="<?php echo $this->params['session_timeout'] ?>">
					<div class="help-block">
						<?php echo Text::_('SOLO_SETUP_LBL_SESSIONTIMEOUT_HELP') ?>
					</div>
				</div>
			</div>

			<div class="form-group">
				<label for="fs_driver" class="col-sm-2 control-label">
					<?php echo Text::_('SOLO_SETUP_LBL_FS_DRIVER'); ?>
				</label>
				<div class="col-sm-10">
					<?php echo \Solo\Helper\Setup::fsDriverSelect($this->params['fs.driver']); ?>
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
						<div class="input-group">
							<input type="text" class="form-control" name="fs_directory" id="fs_directory"
								   value="<?php echo $this->params['fs.directory'] ?>">
							<span class="input-group-btn">
								<button title="<?php echo Text::_('COM_AKEEBA_CONFIG_UI_BROWSE')?>" class="btn btn-default" type="button" id="btnBrowse" onclick="Solo.Setup.initFtpSftpBrowser(); return false;">
									<span class="glyphicon glyphicon-folder-open"></span>
								</button>
							</span>
						</div>
						<div class="help-block">
							<?php echo Text::_('SOLO_SETUP_LBL_FS_FTP_DIRECTORY_HELP') ?>
						</div>
					</div>
				</div>
			</div>
	</div>
</div>

<div class="panel panel-default">
	<div class="panel-heading">
		<h4 class="panel-title">
			<span class="glyphicon glyphicon-user pull-right"></span>
			<?php echo Text::_('SOLO_SETUP_LBL_USERSETUP') ?>
		</h4>
	</div>
	<div class="panel-body">
		<p><?php echo Text::_('SOLO_SETUP_LBL_USERSETUP_TEXT'); ?></p>

		<div class="form-group">
			<label for="user_username" class="col-sm-2 control-label">
				<?php echo Text::_('SOLO_SETUP_LBL_USER_USERNAME'); ?>
			</label>
			<div class="col-sm-10">
				<input type="text" class="form-control" name="user_username" id="user_username" value="<?php echo $this->params['user.username'] ?>">
				<div class="help-block">
					<?php echo Text::_('SOLO_SETUP_LBL_USER_USERNAME_HELP') ?>
				</div>
			</div>
		</div>

		<div class="form-group">
			<label for="user_password" class="col-sm-2 control-label">
				<?php echo Text::_('SOLO_SETUP_LBL_USER_PASSWORD'); ?>
			</label>
			<div class="col-sm-10">
				<input type="password" class="form-control" name="user_password" id="user_password" value="<?php echo $this->params['user.password'] ?>">
				<div class="help-block">
					<?php echo Text::_('SOLO_SETUP_LBL_USER_PASSWORD_HELP') ?>
				</div>
			</div>
		</div>

		<div class="form-group">
			<label for="user_password2" class="col-sm-2 control-label">
				<?php echo Text::_('SOLO_SETUP_LBL_USER_PASSWORD2'); ?>
			</label>
			<div class="col-sm-10">
				<input type="password" class="form-control" name="user_password2" id="user_password2" value="<?php echo $this->params['user.password2'] ?>">
				<div class="help-block">
					<?php echo Text::_('SOLO_SETUP_LBL_USER_PASSWORD2_HELP') ?>
				</div>
			</div>
		</div>

		<div class="form-group">
			<label for="user_email" class="col-sm-2 control-label">
				<?php echo Text::_('SOLO_SETUP_LBL_USER_EMAIL'); ?>
			</label>
			<div class="col-sm-10">
				<input type="text" class="form-control" name="user_email" id="user_email" value="<?php echo $this->params['user.email'] ?>">
				<div class="help-block">
					<?php echo Text::_('SOLO_SETUP_LBL_USER_EMAIL_HELP') ?>
				</div>
			</div>
		</div>

		<div class="form-group">
			<label for="user_name" class="col-sm-2 control-label">
				<?php echo Text::_('SOLO_SETUP_LBL_USER_NAME'); ?>
			</label>
			<div class="col-sm-10">
				<input type="text" class="form-control" name="user_name" id="user_name" value="<?php echo $this->params['user.name'] ?>">
				<div class="help-block">
					<?php echo Text::_('SOLO_SETUP_LBL_USER_NAME_HELP') ?>
				</div>
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

<script type="text/javascript" language="javascript">
	// Callback routine to close the browser dialog
	var akeeba_browser_callback = null;

	Solo.loadScripts.push(function ()
	{
		// Initialise the translations
		Solo.Setup.translations['UI-BROWSE'] = '<?php echo Escape::escapeJS(Text::_('COM_AKEEBA_CONFIG_UI_BROWSE')) ?>';
		Solo.Setup.translations['UI-REFRESH'] = '<?php echo Escape::escapeJS(Text::_('COM_AKEEBA_CONFIG_UI_REFRESH')) ?>';
		Solo.Setup.translations['UI-FTPBROWSER-TITLE'] = '<?php echo Escape::escapeJS(Text::_('COM_AKEEBA_CONFIG_UI_FTPBROWSER_TITLE')) ?>';
		Solo.Setup.translations['UI-ROOT'] = '<?php echo Escape::escapeJS(Text::_('SOLO_COMMON_LBL_ROOT')) ?>';
		Solo.Setup.translations['UI-TESTFTP-OK'] = '<?php echo Escape::escapeJS(Text::_('COM_AKEEBA_CONFIG_DIRECTFTP_TEST_OK')) ?>';
		Solo.Setup.translations['UI-TESTFTP-FAIL'] = '<?php echo Escape::escapeJS(Text::_('COM_AKEEBA_CONFIG_DIRECTFTP_TEST_FAIL')) ?>';
		Solo.Setup.translations['UI-TESTSFTP-OK'] = '<?php echo Escape::escapeJS(Text::_('COM_AKEEBA_CONFIG_DIRECTSFTP_TEST_OK')) ?>';
		Solo.Setup.translations['UI-TESTSFTP-FAIL'] = '<?php echo Escape::escapeJS(Text::_('COM_AKEEBA_CONFIG_DIRECTSFTP_TEST_FAIL')) ?>';

		// Push some custom URLs
		Solo.Setup.URLs['ftpBrowser'] = '<?php echo Escape::escapeJS($router->route('index.php?view=ftpbrowser')) ?>';
		Solo.Setup.URLs['sftpBrowser'] = '<?php echo Escape::escapeJS($router->route('index.php?view=sftpbrowser')) ?>';
		Solo.Setup.URLs['testFtp'] = '<?php echo Escape::escapeJS($router->route('index.php?view=configuration&task=testftp')) ?>';
		Solo.Setup.URLs['testSftp'] = '<?php echo Escape::escapeJS($router->route('index.php?view=configuration&task=testsftp')) ?>';
	});

</script>