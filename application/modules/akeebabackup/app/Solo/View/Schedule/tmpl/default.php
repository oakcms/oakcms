<?php
/**
 * @package		solo
 * @copyright	2014-2017 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license		GNU GPL version 3 or later
 */

use Awf\Text\Text;

/** @var   \Solo\View\Schedule\Html  $this */
?>

<?php if (!AKEEBABACKUP_PRO): ?>
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

<ul id="runCheckTabs" class="nav nav-tabs">
        <li>
            <a href="#absTabRunBackups" data-toggle="tab">
                <?php echo Text::_('COM_AKEEBA_SCHEDULE_LBL_RUN_BACKUPS'); ?>
            </a>
        </li>
        <li>
            <a href="#absTabCheckBackups" data-toggle="tab">
                <?php echo Text::_('COM_AKEEBA_SCHEDULE_LBL_CHECK_BACKUPS'); ?>
            </a>
        </li>
    </ul>

    <div id="runCheckTabsContent" class="tab-content">
        <?php
        echo $this->loadTemplate('runbackups');
        echo $this->loadTemplate('checkbackups');
        ?>
    </div>
<?php
$this->container->application->getDocument()->addScriptDeclaration( <<<JS
Solo.loadScripts.push(function() {
    akeeba.jQuery('#runCheckTabs a:first').tab('show');
});

JS
);
?>