<?php
/**
 * @package        solo
 * @copyright      2014-2017 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license        GNU GPL version 3 or later
 */

use Awf\Text\Text;
use Solo\Helper\Escape;

/** @var $this \Solo\View\Configuration\Html */

$router = $this->container->router;

$urls = array(
	'browser' => addslashes($router->route('index.php?view=browser&tmpl=component&processfolder=1&folder=')),
	'ftpBrowser' => addslashes($router->route('index.php?view=ftpbrowser&tmpl=component')),
	'sftpBrowser' => addslashes($router->route('index.php?view=sftpbrowser&tmpl=component')),
	'testFtp' => addslashes($router->route('index.php?view=configuration&task=testftp&format=raw')),
	'testSftp' => addslashes($router->route('index.php?view=configuration&task=testsftp&format=raw')),
	'dpeauthopen' => addslashes($router->route('index.php?view=configuration&task=dpeoauthopen&format=raw')),
	'dpecustomapi' => addslashes($router->route('index.php?view=configuration&task=dpecustomapi&format=raw')),
);
$this->json = addcslashes($this->json, "'\\");

$keys = array(
    'COM_AKEEBA_CONFIG_UI_BROWSE' => 'COM_AKEEBA_CONFIG_UI_BROWSE',
    'COM_AKEEBA_CONFIG_UI_CONFIG' => 'COM_AKEEBA_CONFIG_UI_CONFIG',
    'COM_AKEEBA_CONFIG_UI_REFRESH' => 'COM_AKEEBA_CONFIG_UI_REFRESH',
    'COM_AKEEBA_CONFIG_UI_FTPBROWSER_TITLE' => 'COM_AKEEBA_CONFIG_UI_FTPBROWSER_TITLE',
    'COM_AKEEBA_FILEFILTERS_LABEL_UIROOT' => 'SOLO_COMMON_LBL_ROOT',
    'COM_AKEEBA_CONFIG_DIRECTFTP_TEST_OK' => 'COM_AKEEBA_CONFIG_DIRECTFTP_TEST_OK',
    'COM_AKEEBA_CONFIG_DIRECTFTP_TEST_FAIL' => 'COM_AKEEBA_CONFIG_DIRECTFTP_TEST_FAIL',
    'COM_AKEEBA_CONFIG_DIRECTSFTP_TEST_OK' => 'COM_AKEEBA_CONFIG_DIRECTSFTP_TEST_OK',
    'COM_AKEEBA_CONFIG_DIRECTSFTP_TEST_FAIL' => 'COM_AKEEBA_CONFIG_DIRECTSFTP_TEST_FAIL',
);
$strings = array();

foreach ($keys as $k => $v)
{
    $strings[$k] = Escape::escapeJS(Text::_($v));
}

$js = <<< JS
Solo.loadScripts.push(function() {
    	// Initialise the translations
	Solo.Configuration.translations['COM_AKEEBA_CONFIG_UI_BROWSE'] = '{$strings['COM_AKEEBA_CONFIG_UI_BROWSE']}';
	Solo.Configuration.translations['COM_AKEEBA_CONFIG_UI_CONFIG'] = '{$strings['COM_AKEEBA_CONFIG_UI_CONFIG']}';
	Solo.Configuration.translations['COM_AKEEBA_CONFIG_UI_REFRESH'] = '{$strings['COM_AKEEBA_CONFIG_UI_REFRESH']}';
	Solo.Configuration.translations['COM_AKEEBA_CONFIG_UI_FTPBROWSER_TITLE'] = '{$strings['COM_AKEEBA_CONFIG_UI_FTPBROWSER_TITLE']}';
	Solo.Configuration.translations['COM_AKEEBA_FILEFILTERS_LABEL_UIROOT'] = '{$strings['COM_AKEEBA_FILEFILTERS_LABEL_UIROOT']}';
	Solo.Configuration.translations['COM_AKEEBA_CONFIG_DIRECTFTP_TEST_OK'] = '{$strings['COM_AKEEBA_CONFIG_DIRECTFTP_TEST_OK']}';
	Solo.Configuration.translations['COM_AKEEBA_CONFIG_DIRECTFTP_TEST_FAIL'] = '{$strings['COM_AKEEBA_CONFIG_DIRECTFTP_TEST_FAIL']}';
	Solo.Configuration.translations['COM_AKEEBA_CONFIG_DIRECTSFTP_TEST_OK'] = '{$strings['COM_AKEEBA_CONFIG_DIRECTSFTP_TEST_OK']}';
	Solo.Configuration.translations['COM_AKEEBA_CONFIG_DIRECTSFTP_TEST_FAIL'] = '{$strings['COM_AKEEBA_CONFIG_DIRECTSFTP_TEST_FAIL']}';

	// Push some custom URLs
	Solo.Configuration.URLs['browser'] = '{$urls['browser']}';
	Solo.Configuration.URLs['ftpBrowser'] = '{$urls['ftpBrowser']}';
	Solo.Configuration.URLs['sftpBrowser'] = '{$urls['sftpBrowser']}';
	Solo.Configuration.URLs['testFtp'] = '{$urls['testFtp']}';
	Solo.Configuration.URLs['testSftp'] = '{$urls['testSftp']}';
	Solo.Configuration.URLs['dpeauthopen'] = '{$urls['dpeauthopen']}';
	Solo.Configuration.URLs['dpecustomapi'] = '{$urls['dpecustomapi']}';
	Solo.System.params.AjaxURL = Solo.Configuration.URLs['dpecustomapi'];

	// Load the configuration UI data
	akeeba_ui_theme_root = '{$this->mediadir}';
	var data = JSON.parse('{$this->json}');

    // Render the configuration UI in the timeout to prevent Safari from auto-filling the password fields
    Solo.Configuration.parseConfigData(data);

    // Work around browsers which blatantly ignore autocomplete=off
    setTimeout('Solo.Configuration.restoreDefaultPasswords();', 1000);

	setTimeout(function(){
		// Enable popovers. Must obviously run after we have the UI set up.
		Solo.Configuration.enablePopoverFor(document.querySelectorAll('[rel="popover"]'));

		$(document.getElementById('var[akeeba.platform.dbdriver]')).change(function(){
			var myVal = this.value;
			
            var elHost = document.getElementById('akconfigrow.akeeba.platform.dbhost');
            var elPort = document.getElementById('akconfigrow.akeeba.platform.dbhost');
            var elUsername = document.getElementById('akconfigrow.akeeba.platform.dbhost');
            var elPassword = document.getElementById('akconfigrow.akeeba.platform.dbhost');
            var elPrefix = document.getElementById('akconfigrow.akeeba.platform.dbhost');
            var elName = document.getElementById('akconfigrow.akeeba.platform.dbhost');
            
            elHost.style.display = 'block';
            elPort.style.display = 'block';
            elUsername.style.display = 'block';
            elPassword.style.display = 'block';
            elPrefix.style.display = 'block';
            elName.style.display = 'block';

			if ((myVal == 'sqlite') || (myVal == 'none'))
			{
                elHost.style.display = 'none';
                elPort.style.display = 'none';
                elUsername.style.display = 'none';
                elPassword.style.display = 'none';
                elPrefix.style.display = 'none';

                elHost.value = '';
                elPort.value = '';
                elUsername.value = '';
                elPassword.value = '';
                elPrefix.value = '';
			}
			
			if (myVal == 'none')
            {
            	elName.value = '';
            	elName.style.display = 'none';
            }
		})
			.change();
	}, 500);
});

JS;

$this->getContainer()->application->getDocument()->addScriptDeclaration($js);

?>

<?php
// Configuration Wizard prompt
if (!\Akeeba\Engine\Factory::getConfiguration()->get('akeeba.flag.confwiz', 0))
{
	echo $this->loadAnyTemplate('Configuration/confwiz_modal');
}
?>

<?php
// Load modal box prototypes
echo $this->loadAnyTemplate('Common/ftp_browser');
echo $this->loadAnyTemplate('Common/sftp_browser');
echo $this->loadAnyTemplate('Common/folder_browser');
echo $this->loadAnyTemplate('Common/ftp_test');
?>

<form name="adminForm" id="adminForm" method="post"
	  action="<?php echo $router->route('index.php?view=configuration') ?>" class="form-horizontal">

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

	<div>
		<?php if ($this->secureSettings): ?>
			<div class="alert alert-success">
				<button class="close" data-dismiss="alert">×</button>
				<?php echo Text::_('COM_AKEEBA_CONFIG_UI_SETTINGS_SECURED'); ?>
			</div>
			<div class="ak_clr"></div>
		<?php elseif ($this->secureSettings == 0): ?>
			<div class="alert alert-danger">
				<button class="close" data-dismiss="alert">×</button>
				<?php echo Text::_('COM_AKEEBA_CONFIG_UI_SETTINGS_NOTSECURED'); ?>
			</div>
			<div class="ak_clr"></div>
		<?php endif; ?>

		<div class="alert alert-info">
			<button class="close" data-dismiss="alert">×</button>
			<strong><?php echo Text::_('COM_AKEEBA_CPANEL_PROFILE_TITLE'); ?></strong>:
			#<?php echo $this->profileId; ?> <?php echo $this->profileName; ?>
		</div>

		<div class="alert alert-info">
			<button class="close" data-dismiss="alert">×</button>
			<?php echo Text::_('COM_AKEEBA_CONFIG_WHERE_ARE_THE_FILTERS'); ?>
		</div>

	</div>

	<input type="hidden" name="task" value=""/>
	<input type="hidden" name="token" value="<?php echo $this->container->session->getCsrfToken()->getValue() ?>"/>

	<div class="well">
		<h4>
			<?php echo Text::_('COM_AKEEBA_PROFILES_LABEL_DESCRIPTION') ?>
		</h4>
		<div class="form-group">
			<label class="control-label col-sm-3" for="profilename" rel="popover"
				   data-original-title="<?php echo Text::_('COM_AKEEBA_PROFILES_LABEL_DESCRIPTION') ?>"
				   data-content="<?php echo Text::_('COM_AKEEBA_PROFILES_LABEL_DESCRIPTION_TOOLTIP') ?>">
				<?php echo Text::_('COM_AKEEBA_PROFILES_LABEL_DESCRIPTION') ?>
			</label>
			<div class="col-sm-9">
				<input type="text" class="form-control" name="profilename" id="profilename" value="<?php echo $this->escape($this->profileName); ?>" />
			</div>
		</div>
		<div class="form-group">
			<label class="control-label col-sm-3" for="quickicon" rel="popover"
				   data-original-title="<?php echo Text::_('COM_AKEEBA_CONFIG_QUICKICON_LABEL') ?>"
				   data-content="<?php echo Text::_('COM_AKEEBA_CONFIG_QUICKICON_DESC') ?>">
				<?php echo Text::_('COM_AKEEBA_CONFIG_QUICKICON_LABEL') ?>
			</label>
			<div class="col-sm-9">
				<input type="checkbox" name="quickicon" id="quickicon" <?php echo $this->quickicon ? 'checked="checked"' : ''; ?>/>
			</div>
		</div>
	</div>

	<!-- This div contains dynamically generated user interface elements -->
	<div id="akeebagui">
	</div>

</form>
