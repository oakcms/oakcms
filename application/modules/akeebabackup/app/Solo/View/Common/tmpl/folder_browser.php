<?php
/**
 * @package        solo
 * @copyright      2014-2017 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license        GNU GPL version 3 or later
 */

use Awf\Text\Text;

/* Filesystem browser */
?>
<div class="modal" id="folderBrowserDialog" tabindex="-1" role="dialog" aria-labelledby="folderBrowserDialogLabel"
     aria-hidden="true" style="display: none;">
    <div class="modal-header">
        <h4 class="modal-title" id="folderBrowserDialogLabel">
			<?php echo Text::_('COM_AKEEBA_CONFIG_UI_BROWSER_TITLE'); ?>
        </h4>
    </div>
    <div class="modal-body" id="folderBrowserDialogBody">
    </div>
</div>