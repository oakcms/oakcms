<?php
/**
 * @package		solo
 * @copyright	2014-2017 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license		GNU GPL version 3 or later
 */

use Awf\Text\Text;
use Solo\Helper\Escape;

/** @var \Solo\View\Sysconfig\Html $this */

$config = $this->container->appConfig;
$router = $this->container->router;
$inCMS = $this->container->segment->get('insideCMS', false);

echo $this->loadAnyTemplate('Common/ftp_browser');
echo $this->loadAnyTemplate('Common/sftp_browser');
echo $this->loadAnyTemplate('Common/ftp_test');

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

<form action="<?php echo $router->route('index.php?view=sysconfig') ?>" method="POST" id="adminForm" class="form-horizontal" role="form">
	<input type="hidden" name="task" value=""/>
	<input type="hidden" name="token" value="<?php echo $this->container->session->getCsrfToken()->getValue()?>">

	<ul class="nav nav-tabs">
		<li class="active">
			<a href="#sysconfigAppSetup" data-toggle="tab">
				<span class="glyphicon glyphicon-cog"></span>
				<?php echo Text::_('SOLO_SETUP_LBL_APPSETUP') ?>
			</a>
		</li>
		<li>
			<a href="#sysconfigBackupChecks" data-toggle="tab">
				<span class="glyphicon glyphicon-list-alt"></span>
				<?php echo Text::_('SOLO_SYSCONFIG_BACKUP_CHECKS')?>
			</a>
		</li>
		<li>
			<a href="#sysconfigPublicAPI" data-toggle="tab">
				<span class="glyphicon glyphicon-globe"></span>
				<?php echo Text::_('SOLO_SYSCONFIG_FRONTEND') ?>
			</a>
		</li>
		<li>
			<a href="#sysconfigPushNotifications" data-toggle="tab">
				<span class="glyphicon glyphicon-comment"></span>
				<?php echo Text::_('SOLO_SYSCONFIG_PUSH') ?>
			</a>
		</li>
		<li>
			<a href="#sysconfigUpdate" data-toggle="tab">
				<span class="glyphicon glyphicon-refresh"></span>
				<?php echo Text::_('SOLO_SYSCONFIG_UPDATE') ?>
			</a>
		</li>
		<li>
			<a href="#sysconfigEmail" data-toggle="tab">
				<span class="glyphicon glyphicon-envelope"></span>
				<?php echo Text::_('SOLO_SYSCONFIG_EMAIL') ?>
			</a>
		</li>
		<?php if (!$inCMS): ?>
		<li>
			<a href="#sysconfigDatabase" data-toggle="tab">
				<span class="glyphicon glyphicon-th-large"></span>
				<?php echo Text::_('SOLO_SETUP_SUBTITLE_DATABASE') ?>
			</a>
		</li>
		<?php endif; ?>
	</ul>

	<div class="tab-content">
		<div id="sysconfigAppSetup" class="tab-pane active">

            <div class="form-group">
                <label for="timezone" class="col-sm-2 control-label">
                    <?php echo Text::_('COM_AKEEBA_CONFIG_SECURITY_USEENCRYPTION_LABEL'); ?>
                </label>
                <div class="col-sm-10">
                    <?php echo \Awf\Html\Select::booleanList('useencryption', array(), $config->get('useencryption', 1))?>
                    <div class="help-block">
                        <?php echo Text::_('COM_AKEEBA_CONFIG_SECURITY_USEENCRYPTION_DESCRIPTION') ?>
                    </div>
                </div>
            </div>

			<div class="form-group">
				<label for="timezone" class="col-sm-2 control-label">
					<?php echo Text::_('SOLO_SETUP_LBL_TIMEZONE'); ?>
				</label>
				<div class="col-sm-10">
					<?php echo \Solo\Helper\Setup::timezoneSelect($config->get('timezone')); ?>
					<div class="help-block">
						<?php echo Text::_('SOLO_SETUP_LBL_TIMEZONE_HELP') ?>
					</div>
				</div>
			</div>

			<div class="form-group">
				<label for="localtime" class="col-sm-2 control-label">
					<?php echo Text::_('COM_AKEEBA_CONFIG_BACKEND_LOCALTIME_LABEL'); ?>
				</label>
				<div class="col-sm-10">
					<?php echo \Awf\Html\Select::booleanList('localtime', array(), $config->get('localtime', 1))?>
					<div class="help-block">
						<?php echo Text::_('COM_AKEEBA_CONFIG_BACKEND_LOCALTIME_DESC') ?>
					</div>
				</div>
			</div>

			<div class="form-group">
				<label for="timezonetext" class="col-sm-2 control-label">
					<?php echo Text::_('COM_AKEEBA_CONFIG_BACKEND_TIMEZONETEXT_LABEL'); ?>
				</label>
				<div class="col-sm-10">
					<?php echo \Solo\Helper\Setup::timezoneFormatSelect($config->get('timezonetext', 'T')); ?>
					<div class="help-block">
						<?php echo Text::_('COM_AKEEBA_CONFIG_BACKEND_TIMEZONETEXT_DESC') ?>
					</div>
				</div>
			</div>

			<?php if (!$inCMS): ?>

			<div class="form-group">
				<label for="live_site" class="col-sm-2 control-label">
					<?php echo Text::_('SOLO_SETUP_LBL_LIVESITE'); ?>
				</label>
				<div class="col-sm-10">
					<input type="text" class="form-control" name="live_site" id="live_site" value="<?php echo $config->get('live_site') ?>">
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
					<input type="text" class="form-control" name="session_timeout" id="session_timeout" value="<?php echo $config->get('session_timeout') ?>">
					<div class="help-block">
						<?php echo Text::_('SOLO_SETUP_LBL_SESSIONTIMEOUT_HELP') ?>
					</div>
				</div>
			</div>
			<?php endif; ?>

			<div class="form-group">
				<label for="dateformat" class="col-sm-2 control-label">
					<?php echo Text::_('COM_AKEEBA_CONFIG_DATEFORMAT_LABEL'); ?>
				</label>
				<div class="col-sm-10">
					<input type="text" class="form-control" name="dateformat" id="dateformat" value="<?php echo $config->get('dateformat') ?>">
					<div class="help-block">
						<?php echo Text::_('COM_AKEEBA_CONFIG_DATEFORMAT_DESC') ?>
					</div>
				</div>
			</div>

            <div class="form-group">
                <label for="stats_enabled" class="col-sm-2 control-label">
                    <?php echo Text::_('COM_AKEEBA_CONFIG_USAGESTATS_SOLO_LABEL'); ?>
                </label>
                <div class="col-sm-10">
                    <?php echo \Awf\Html\Select::booleanList('stats_enabled', array(), $config->get('stats_enabled', 1))?>
                    <div class="help-block">
                        <?php echo Text::_('COM_AKEEBA_CONFIG_USAGESTATS_SOLO_DESC') ?>
                    </div>
                </div>
            </div>

			<div class="form-group">
				<label for="fs_driver" class="col-sm-2 control-label">
					<?php echo Text::_('SOLO_SETUP_LBL_FS_DRIVER'); ?>
				</label>
				<div class="col-sm-10">
					<?php echo \Solo\Helper\Setup::fsDriverSelect($config->get('fs.driver')); ?>
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
						<input type="text" class="form-control" name="fs_host" id="fs_host" value="<?php echo $config->get('fs.host') ?>">
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
						<input type="text" class="form-control" name="fs_port" id="fs_port" value="<?php echo $config->get('fs.port') ?>">
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
						<input type="text" class="form-control" name="fs_username" id="fs_username" value="<?php echo $config->get('fs.username') ?>">
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
						<input type="password" class="form-control" name="fs_password" id="fs_password" value="<?php echo $config->get('fs.password') ?>">
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
							<input type="text" class="form-control" name="fs_directory" id="fs_directory" value="<?php echo $config->get('fs.directory') ?>">
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

		<div id="sysconfigBackupChecks" class="tab-pane">
			<div class="form-group">
				<label for="failure_timeout" class="col-sm-2 control-label">
					<?php echo Text::_('COM_AKEEBA_CONFIG_FAILURE_TIMEOUT_LABEL'); ?>
				</label>
				<div class="col-sm-10">
					<input type="text" class="form-control" name="options[failure_timeout]" id="failure_timeout" placeholder="<?php echo Text::_('COM_AKEEBA_CONFIG_FAILURE_TIMEOUT_LABEL'); ?>" value="<?php echo $config->get('options.failure_timeout', 180)?>">
					<div class="help-block">
						<?php echo Text::_('COM_AKEEBA_CONFIG_FAILURE_TIMEOUT_DESC') ?>
					</div>
				</div>
			</div>

			<div class="form-group">
				<label for="failure_email_address" class="col-sm-2 control-label">
					<?php echo Text::_('COM_AKEEBA_CONFIG_FAILURE_EMAILADDRESS_LABEL'); ?>
				</label>
				<div class="col-sm-10">
					<input type="text" class="form-control" name="options[failure_email_address]" id="failure_email_address" placeholder="<?php echo Text::_('COM_AKEEBA_CONFIG_FAILURE_EMAILADDRESS_LABEL'); ?>" value="<?php echo $config->get('options.failure_email_address')?>">
					<div class="help-block">
						<?php echo Text::_('COM_AKEEBA_CONFIG_FAILURE_EMAILADDRESS_DESC') ?>
					</div>
				</div>
			</div>

			<div class="form-group">
				<label for="failure_email_subject" class="col-sm-2 control-label">
					<?php echo Text::_('COM_AKEEBA_CONFIG_FAILURE_EMAILSUBJECT_LABEL'); ?>
				</label>
				<div class="col-sm-10">
					<input type="text" class="form-control" name="options[failure_email_subject]" id="failure_email_subject" placeholder="<?php echo Text::_('COM_AKEEBA_CONFIG_FAILURE_EMAILSUBJECT_LABEL'); ?>" value="<?php echo $config->get('options.failure_email_subject')?>">
					<div class="help-block">
						<?php echo Text::_('COM_AKEEBA_CONFIG_FAILURE_EMAILSUBJECT_DESC') ?>
					</div>
				</div>
			</div>

			<div class="form-group">
				<label for="failure_email_body" class="col-sm-2 control-label">
					<?php echo Text::_('COM_AKEEBA_CONFIG_FAILURE_EMAILBODY_LABEL'); ?>
				</label>
				<div class="col-sm-10">
					<input type="text" class="form-control" name="options[failure_email_body]" id="failure_email_body" placeholder="<?php echo Text::_('COM_AKEEBA_CONFIG_FAILURE_EMAILBODY_LABEL'); ?>" value="<?php echo $config->get('options.failure_email_body')?>">
					<div class="help-block">
						<?php echo Text::_('COM_AKEEBA_CONFIG_FAILURE_EMAILBODY_DESC') ?>
					</div>
				</div>
			</div>
		</div>

		<div id="sysconfigEmail" class="tab-pane">
			<div class="form-group">
				<label for="mail_online" class="col-sm-2 control-label">
					<?php echo Text::_('SOLO_SYSCONFIG_LBL_EMAIL_ONLINE'); ?>
				</label>
				<div class="col-sm-10">
					<div style="height: 2em">
						<input type="hidden" name="mail_online" value="0" />
						<input type="checkbox" name="mail_online" value="1" id="mail_online" <?php echo $config->get('mail.online', 1) ? 'checked' : '' ?>
							   class="toggleSwitch" data-on-color="success" data-off-color="danger">
					</div>
					<div class="help-block">
						<?php echo Text::_('SOLO_SYSCONFIG_LBL_EMAIL_ONLINE_HELP') ?>
					</div>
				</div>
			</div>

			<div class="form-group">
				<label for="options_mail_mailer" class="col-sm-2 control-label">
					<?php echo Text::_('SOLO_SYSCONFIG_LBL_EMAIL_MAILER'); ?>
				</label>
				<div class="col-sm-10">
					<?php echo \Solo\Helper\Setup::mailerSelect($config->get('mail.mailer'), 'mail_mailer');?>
					<div class="help-block">
						<?php echo Text::_('SOLO_SYSCONFIG_LBL_EMAIL_MAILER_HELP') ?>
					</div>
				</div>
			</div>

			<div class="form-group">
				<label for="mail_mailfrom" class="col-sm-2 control-label">
					<?php echo Text::_('SOLO_SYSCONFIG_LBL_EMAIL_MAILFROM'); ?>
				</label>
				<div class="col-sm-10">
					<input type="email" name="mail_mailfrom" id="mail_mailfrom" value="<?php echo $config->get('mail.mailfrom')?>">
					<div class="help-block">
						<?php echo Text::_('SOLO_SYSCONFIG_LBL_EMAIL_MAILFROM_HELP') ?>
					</div>
				</div>
			</div>

			<div class="form-group">
				<label for="mail_fromname" class="col-sm-2 control-label">
					<?php echo Text::_('SOLO_SYSCONFIG_LBL_EMAIL_FROMNAME'); ?>
				</label>
				<div class="col-sm-10">
					<input type="text" name="mail_fromname" id="mail_fromname" value="<?php echo $config->get('mail.fromname')?>">
					<div class="help-block">
						<?php echo Text::_('SOLO_SYSCONFIG_LBL_EMAIL_FROMNAME_HELP') ?>
					</div>
				</div>
			</div>

			<div class="form-group">
				<label for="mail_smtpauth" class="col-sm-2 control-label">
					<?php echo Text::_('SOLO_SYSCONFIG_LBL_EMAIL_SMTPAUTH'); ?>
				</label>
				<div class="col-sm-10">
					<div style="height: 2em">
						<input type="hidden" name="mail_smtpauth" value="0" />
						<input type="checkbox" name="mail_smtpauth" value="1" id="mail_smtpauth" <?php echo $config->get('mail.smtpauth', 0) ? 'checked' : '' ?>
							   class="toggleSwitch" data-on-color="success" data-off-color="danger">
					</div>
					<div class="help-block">
						<?php echo Text::_('SOLO_SYSCONFIG_LBL_EMAIL_SMTPAUTH_HELP') ?>
					</div>
				</div>
			</div>

			<div class="form-group">
				<label for="mail_smtpsecure" class="col-sm-2 control-label">
					<?php echo Text::_('SOLO_SYSCONFIG_LBL_EMAIL_SMTPSECURE'); ?>
				</label>
				<div class="col-sm-10">
					<?php echo \Solo\Helper\Setup::smtpSecureSelect($config->get('mail.smtpsecure'), 'mail_smtpsecure');?>
					<div class="help-block">
						<?php echo Text::_('SOLO_SYSCONFIG_LBL_EMAIL_SMTPSECURE_HELP') ?>
					</div>
				</div>
			</div>

			<div class="form-group">
				<label for="mail_smtpport" class="col-sm-2 control-label">
					<?php echo Text::_('SOLO_SYSCONFIG_LBL_EMAIL_SMTPPORT'); ?>
				</label>
				<div class="col-sm-10">
					<input type="number" name="mail_smtpport" id="mail_smtpport" value="<?php echo $config->get('mail.smtpport', 25)?>">
					<div class="help-block">
						<?php echo Text::_('SOLO_SYSCONFIG_LBL_EMAIL_SMTPPORT_HELP') ?>
					</div>
				</div>
			</div>

			<div class="form-group">
				<label for="mail_smtpuser" class="col-sm-2 control-label">
					<?php echo Text::_('SOLO_SYSCONFIG_LBL_EMAIL_SMTPUSER'); ?>
				</label>
				<div class="col-sm-10">
					<input type="text" name="mail_smtpuser" id="mail_smtpuser" value="<?php echo $config->get('mail.smtpuser', '')?>">
					<div class="help-block">
						<?php echo Text::_('SOLO_SYSCONFIG_LBL_EMAIL_SMTPUSER_HELP') ?>
					</div>
				</div>
			</div>

			<div class="form-group">
				<label for="mail_smtppass" class="col-sm-2 control-label">
					<?php echo Text::_('SOLO_SYSCONFIG_LBL_EMAIL_SMTPPASS'); ?>
				</label>
				<div class="col-sm-10">
					<input type="password" name="mail_smtppass" id="mail_smtppass" value="<?php echo $config->get('mail.smtppass', '')?>">
					<div class="help-block">
						<?php echo Text::_('SOLO_SYSCONFIG_LBL_EMAIL_SMTPPASS_HELP') ?>
					</div>
				</div>
			</div>

			<div class="form-group">
				<label for="mail_smtphost" class="col-sm-2 control-label">
					<?php echo Text::_('SOLO_SYSCONFIG_LBL_EMAIL_SMTPHOST'); ?>
				</label>
				<div class="col-sm-10">
					<input type="text" name="mail_smtphost" id="mail_smtphost" value="<?php echo $config->get('mail.smtphost', 'localhost')?>">
					<div class="help-block">
						<?php echo Text::_('SOLO_SYSCONFIG_LBL_EMAIL_SMTPHOST_HELP') ?>
					</div>
				</div>
			</div>

            <div class="form-group">
                <label class="col-sm-2 control-label"></label>
                <div class="col-sm-10">
                    <button class="btn btn-primary" onclick="Solo.System.submitForm('adminForm', 'testemail')">
                        <?php echo Text::_('SOLO_SYSCONFIG_LBL_SEND_TEST_EMAIL')?>
                    </button>
                </div>
            </div>

		</div>

		<?php if (!$inCMS): ?>
		<div id="sysconfigDatabase" class="tab-pane">
			<div class="alert alert-warning alert-dismissable">
				<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
				<span class="glyphicon glyphicon-warning-sign"></span>
				<?php echo Text::_('SOLO_SYSCONFIG_WARNDB'); ?>
			</div>

			<div class="form-group">
				<label for="driver" class="col-sm-2 control-label">
					<?php echo Text::_('SOLO_SETUP_LBL_DATABASE_DRIVER'); ?>
				</label>
				<div class="col-sm-10">
					<?php echo \Solo\Helper\Setup::databaseTypesSelect($config->get('dbdriver'));?>
					<div class="help-block">
						<?php echo Text::_('SOLO_SETUP_LBL_DATABASE_DRIVER_HELP') ?>
					</div>
				</div>
			</div>

			<div class="form-group">
				<label for="host" class="col-sm-2 control-label">
					<?php echo Text::_('SOLO_SETUP_LBL_DATABASE_HOST'); ?>
				</label>
				<div class="col-sm-10">
					<input type="text" class="form-control" id="host" name="host" placeholder="<?php echo Text::_('SOLO_SETUP_LBL_DATABASE_HOST'); ?>" value="<?php echo $config->get('dbhost')?>">
					<div class="help-block">
						<?php echo Text::_('SOLO_SETUP_LBL_DATABASE_HOST_HELP') ?>
					</div>
				</div>
			</div>

			<div class="form-group">
				<label for="user" class="col-sm-2 control-label">
					<?php echo Text::_('SOLO_SETUP_LBL_DATABASE_USER'); ?>
				</label>
				<div class="col-sm-10">
					<input type="text" class="form-control" id="user" name="user" placeholder="<?php echo Text::_('SOLO_SETUP_LBL_DATABASE_USER'); ?>" value="<?php echo $config->get('dbuser')?>">
					<div class="help-block">
						<?php echo Text::_('SOLO_SETUP_LBL_DATABASE_USER_HELP') ?>
					</div>
				</div>
			</div>

			<div class="form-group">
				<label for="pass" class="col-sm-2 control-label">
					<?php echo Text::_('SOLO_SETUP_LBL_DATABASE_PASS'); ?>
				</label>
				<div class="col-sm-10">
					<input type="password" class="form-control" id="pass" name="pass" placeholder="<?php echo Text::_('SOLO_SETUP_LBL_DATABASE_PASS'); ?>" value="<?php echo $config->get('dbpass')?>">
					<div class="help-block">
						<?php echo Text::_('SOLO_SETUP_LBL_DATABASE_PASS_HELP') ?>
					</div>
				</div>
			</div>

			<div class="form-group">
				<label for="name" class="col-sm-2 control-label">
					<?php echo Text::_('SOLO_SETUP_LBL_DATABASE_NAME'); ?>
				</label>
				<div class="col-sm-10">
					<input type="text" class="form-control" id="name" name="name" placeholder="<?php echo Text::_('SOLO_SETUP_LBL_DATABASE_NAME'); ?>" value="<?php echo $config->get('dbname')?>">
					<div class="help-block">
						<?php echo Text::_('SOLO_SETUP_LBL_DATABASE_NAME_HELP') ?>
					</div>
				</div>
			</div>

			<div class="form-group">
				<label for="prefix" class="col-sm-2 control-label">
					<?php echo Text::_('SOLO_SETUP_LBL_DATABASE_PREFIX'); ?>
				</label>
				<div class="col-sm-10">
					<input type="text" class="form-control" id="prefix" name="prefix" placeholder="<?php echo Text::_('SOLO_SETUP_LBL_DATABASE_PREFIX'); ?>" value="<?php echo $config->get('prefix')?>">
					<div class="help-block">
						<?php echo Text::_('SOLO_SETUP_LBL_DATABASE_PREFIX_HELP') ?>
					</div>
				</div>
			</div>
		</div>
		<?php endif; ?>

		<div id="sysconfigPublicAPI" class="tab-pane">
			<div class="form-group">
				<label for="options_frontend_enable" class="col-sm-2 control-label">
					<?php echo Text::_('COM_AKEEBA_CONFIG_FEBENABLE_LABEL'); ?>
				</label>
				<div class="col-sm-10">
					<div style="height: 2em">
						<input type="hidden" name="options[frontend_enable]" value="0">
						<input type="checkbox" name="options[frontend_enable]" value="1" <?php echo $config->get('options.frontend_enable', 0) ? 'checked' : '' ?>
							   class="toggleSwitch" data-on-color="success" data-off-color="danger">
					</div>
					<div class="help-block">
						<?php echo Text::_('COM_AKEEBA_CONFIG_FEBENABLE_DESC') ?>
					</div>
				</div>
			</div>

			<div class="form-group">
				<label for="frontend_secret_word" class="col-sm-2 control-label">
					<?php echo Text::_('COM_AKEEBA_CONFIG_SECRETWORD_LABEL'); ?>
				</label>
				<div class="col-sm-10">
					<input type="text" class="form-control" name="options[frontend_secret_word]" id="frontend_secret_word" placeholder="<?php echo Text::_('COM_AKEEBA_CONFIG_SECRETWORD_LABEL'); ?>" value="<?php echo $config->get('options.frontend_secret_word')?>">
					<div class="help-block">
						<?php echo Text::_('COM_AKEEBA_CONFIG_SECRETWORD_DESC') ?>
					</div>
				</div>
			</div>

			<div class="form-group">
				<label for="frontend_email_on_finish" class="col-sm-2 control-label">
					<?php echo Text::_('COM_AKEEBA_CONFIG_FRONTENDEMAIL_LABEL'); ?>
				</label>
				<div class="col-sm-10">
					<div style="height: 2em">
						<input type="hidden" name="options[frontend_email_on_finish]" value="0">
						<input type="checkbox" name="options[frontend_email_on_finish]" value="1" <?php echo $config->get('options.frontend_email_on_finish', 1) ? 'checked' : '' ?>
							   class="toggleSwitch" data-on-color="success" data-off-color="danger">
					</div>
					<div class="help-block">
						<?php echo Text::_('COM_AKEEBA_CONFIG_FRONTENDEMAIL_DESC') ?>
					</div>
				</div>
			</div>

			<div class="form-group">
				<label for="frontend_email_address" class="col-sm-2 control-label">
					<?php echo Text::_('COM_AKEEBA_CONFIG_ARBITRARYFEEMAIL_LABEL'); ?>
				</label>
				<div class="col-sm-10">
					<input type="email" class="form-control" name="options[frontend_email_address]" id="frontend_email_address" placeholder="<?php echo Text::_('COM_AKEEBA_CONFIG_ARBITRARYFEEMAIL_LABEL'); ?>" value="<?php echo $config->get('options.frontend_email_address')?>">
					<div class="help-block">
						<?php echo Text::_('COM_AKEEBA_CONFIG_ARBITRARYFEEMAIL_DESC') ?>
					</div>
				</div>
			</div>

			<div class="form-group">
				<label for="frontend_email_subject" class="col-sm-2 control-label">
					<?php echo Text::_('COM_AKEEBA_CONFIG_FEEMAILSUBJECT_LABEL'); ?>
				</label>
				<div class="col-sm-10">
					<input type="text" class="form-control" name="options[frontend_email_subject]" id="frontend_email_subject" placeholder="<?php echo Text::_('COM_AKEEBA_CONFIG_FEEMAILSUBJECT_DESC'); ?>" value="<?php echo $config->get('options.frontend_email_subject')?>">
					<div class="help-block">
						<?php echo Text::_('COM_AKEEBA_CONFIG_FEEMAILSUBJECT_DESC') ?>
					</div>
				</div>
			</div>

			<div class="form-group">
				<label for="frontend_email_body" class="col-sm-2 control-label">
					<?php echo Text::_('COM_AKEEBA_CONFIG_FEEMAILBODY_LABEL'); ?>
				</label>
				<div class="col-sm-10">
					<textarea rows="10" class="form-control" name="options[frontend_email_body]" id="frontend_email_body" ><?php echo $config->get('options.frontend_email_body')?></textarea>
					<div class="help-block">
						<?php echo Text::_('COM_AKEEBA_CONFIG_FEEMAILBODY_DESC') ?>
					</div>
				</div>
			</div>

		</div>

		<div id="sysconfigPushNotifications" class="tab-pane">
			<div class="form-group">
				<label for="desktop_notifications" class="col-sm-2 control-label">
					<?php echo Text::_('COM_AKEEBA_CONFIG_DESKTOP_NOTIFICATIONS_LABEL'); ?>
				</label>
				<div class="col-sm-10">
					<div style="height: 2em">
						<input type="hidden" name="options[desktop_notifications]" value="0">
						<input type="checkbox" name="options[desktop_notifications]" value="1" <?php echo $config->get('options.desktop_notifications', 0) ? 'checked' : '' ?>
							   class="toggleSwitch" data-on-color="success" data-off-color="danger">
					</div>
					<div class="help-block">
						<?php echo Text::_('COM_AKEEBA_CONFIG_DESKTOP_NOTIFICATIONS_DESC') ?>
					</div>
				</div>
			</div>

			<div class="form-group">
				<label for="push_preference" class="col-sm-2 control-label">
					<?php echo Text::_('COM_AKEEBA_CONFIG_PUSH_PREFERENCE_LABEL'); ?>
				</label>
				<div class="col-sm-10">
					<div style="height: 2em">
						<input type="hidden" name="options[push_preference]" value="0">
						<input type="checkbox" name="options[push_preference]" value="1" <?php echo $config->get('options.push_preference', 0) ? 'checked' : '' ?>
							   class="toggleSwitch" data-on-color="success" data-off-color="danger">
					</div>
					<div class="help-block">
						<?php echo Text::_('COM_AKEEBA_CONFIG_PUSH_PREFERENCE_DESC') ?>
					</div>
				</div>
			</div>

			<div class="form-group">
				<label for="push_apikey" class="col-sm-2 control-label">
					<?php echo Text::_('COM_AKEEBA_CONFIG_PUSH_APIKEY_LABEL'); ?>
				</label>
				<div class="col-sm-10">
					<input type="text" class="form-control" name="options[push_apikey]" id="push_apikey" placeholder="<?php echo Text::_('COM_AKEEBA_CONFIG_PUSH_APIKEY_LABEL'); ?>" value="<?php echo $config->get('options.push_apikey')?>">
					<div class="help-block">
						<?php echo Text::_('COM_AKEEBA_CONFIG_PUSH_APIKEY_DESC') ?>
					</div>
				</div>
			</div>

		</div>

		<div id="sysconfigUpdate" class="tab-pane">
			<div class="form-group" style="display: none">
				<label for="options_usesvnsource" class="col-sm-2 control-label">
					<?php echo Text::_('CONFIG_LIVEUPDATE_USESVN_LABEL'); ?>
				</label>
				<div class="col-sm-10">
					<div style="height: 2em">
						<input type="hidden" name="options[usesvnsource]" value="0">
						<input type="checkbox" name="options[usesvnsource]" value="1" <?php echo $config->get('options.usesvnsource', 0) ? 'checked' : '' ?>
							   class="toggleSwitch" data-on-color="success" data-off-color="danger">
					</div>
					<div class="help-block">
						<?php echo Text::_('CONFIG_LIVEUPDATE_USESVN_DESC') ?>
					</div>
				</div>
			</div>

			<?php if (defined('AKEEBABACKUP_PRO') && AKEEBABACKUP_PRO): ?>
			<div class="form-group">
				<label for="update_dlid" class="col-sm-2 control-label">
					<?php echo Text::_('COM_AKEEBA_CONFIG_DOWNLOADID_LABEL'); ?>
				</label>
				<div class="col-sm-10">
					<input type="text" class="form-control" name="options[update_dlid]" id="update_dlid" placeholder="<?php echo Text::_('COM_AKEEBA_CONFIG_DOWNLOADID_LABEL'); ?>" value="<?php echo $config->get('options.update_dlid')?>">
					<div class="help-block">
						<?php echo Text::_('COM_AKEEBA_CONFIG_DOWNLOADID_DESC') ?>
					</div>
				</div>
			</div>
			<?php endif; ?>

			<div class="form-group">
				<label for="minstability" class="col-sm-2 control-label">
					<?php echo Text::_('SOLO_CONFIG_MINSTABILITY_LABEL'); ?>
				</label>
				<div class="col-sm-10">
					<?php echo \Solo\Helper\Setup::minstabilitySelect($config->get('options.minstability', 'stable')); ?>
					<div class="help-block">
						<?php echo Text::_('SOLO_CONFIG_MINSTABILITY_DESC') ?>
					</div>
				</div>
			</div>

		</div>

	</div>
</form>

<script type="text/javascript">
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

	// Fancy switches require jQuery (they are rendered by a jQuery plugin)
	akeeba.jQuery(".toggleSwitch").bootstrapSwitch();
});

</script>

<?php
$document = $this->container->application->getDocument();
$document->addScript(\Awf\Uri\Uri::base(false, $this->container) . '/media/js/bootstrap-switch.min.js');
$document->addStyleSheet(\Awf\Uri\Uri::base(false, $this->container) . '/media/css/bootstrap-switch.min.css');
?>