<?php
/**
 * @package        solo
 * @copyright      2014-2017 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license        GNU GPL version 3 or later
 */

use Awf\Text\Text;

/* FTP browser */
?>
<div class="modal fade" id="ftpdialog" tabindex="-1" role="dialog" aria-labelledby="ftpdialogLabel" aria-hidden="true"
     style="display: none;">
    <div class="modal-header">
        <h4 class="modal-title" id="ftpdialogLabel">
			<?php echo Text::_('COM_AKEEBA_CONFIG_UI_FTPBROWSER_TITLE'); ?>
        </h4>
    </div>
    <div class="modal-body">
        <p class="instructions alert alert-info hidden-xs">
			<?php echo Text::_('COM_AKEEBA_FTPBROWSER_LBL_INSTRUCTIONS'); ?>
        </p>
        <div class="error alert alert-danger" id="ftpBrowserErrorContainer">
            <h2><?php echo Text::_('COM_AKEEBA_FTPBROWSER_LBL_ERROR'); ?></h2>

            <p id="ftpBrowserError"></p>
        </div>
        <ul id="ak_crumbs2" class="breadcrumb"></ul>
        <div class="folderBrowserWrapper" id="ftpBrowserWrapper">
            <table id="ftpBrowserFolderList" class="table table-striped">
            </table>
        </div>
    </div>
    <div class="modal-footer">
        <button type="button" id="ftpdialogCancelButton" class="btn btn-default">
			<?php echo Text::_('JTOOLBAR_CANCEL'); ?>
        </button>
        <button type="button" id="ftpdialogOkButton" class="btn btn-primary">
			<?php echo Text::_('COM_AKEEBA_BROWSER_LBL_USE'); ?>
        </button>
    </div>
</div>
