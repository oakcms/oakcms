<?php
/**
 * @package        solo
 * @copyright      2014-2017 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license        GNU GPL version 3 or later
 */

use Awf\Text\Text;

/* Error modal */
?>
<div id="errorDialog" tabindex="-1" role="dialog" aria-labelledby="errorDialogLabel" aria-hidden="true"
     style="display:none;">
    <div class="modal-header">
        <h4 class="modal-title" id="errorDialogLabel">
			<?php echo Text::_('COM_AKEEBA_CONFIG_UI_AJAXERRORDLG_TITLE'); ?>
        </h4>
    </div>
    <div class="modal-body" id="errorDialogBody">
        <p>
			<?php echo Text::_('COM_AKEEBA_CONFIG_UI_AJAXERRORDLG_TEXT'); ?>
        </p>
        <pre id="errorDialogPre">
        </pre>
    </div>
</div>
