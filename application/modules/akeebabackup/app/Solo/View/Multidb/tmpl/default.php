<?php
/**
 * @package     Solo
 * @copyright   2014-2017 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license     GNU GPL version 3 or later
 */

use \Awf\Text\Text;
use \Solo\Helper\Escape;

// Used for type hinting
/** @var \Solo\View\Multidb\Html $this */

$router = $this->container->router;

$token = $this->container->session->getCsrfToken()->getValue();

echo $this->loadAnyTemplate('Common/error_modal');
?>

<div class="modal fade" id="akEditorDialog" tabindex="-1" role="dialog" aria-labelledby="akEditorDialogLabel" aria-hidden="true">
    <div class="modal-header">
        <h4 class="modal-title" id="akEditorDialogLabel">
			<?php echo Text::_('COM_AKEEBA_FILEFILTERS_EDITOR_TITLE') ?>
        </h4>
    </div>
    <div class="modal-body" id="akEditorDialogBody">
        <div class="form-horizontal" id="ak_editor_table">
            <div class="form-group">
                <label class="control-label col-sm-3 col-xs-12" for="ake_driver">
					<?php echo Text::_('COM_AKEEBA_MULTIDB_GUI_LBL_DRIVER')?>
                </label>
                <div class="col-sm-9 col-xs-12">
					<?php echo \Awf\Html\Select::genericList($this->dbDriversOptions, 'ake_driver', array('class' => 'form-control'), 'value', 'text', 'mysqli', 'ake_driver'); ?>
                </div>
            </div>
            <div class="form-group">
                <label class="control-label col-sm-3 col-xs-12" for="ake_host">
					<?php echo Text::_('COM_AKEEBA_MULTIDB_GUI_LBL_HOST')?>
                </label>
                <div class="col-sm-9 col-xs-12">
                    <input type="text" name="ake_host" id="ake_host" class="form-control">
                </div>
            </div>
            <div class="form-group">
                <label class="control-label col-sm-3 col-xs-12" for="ake_port">
					<?php echo Text::_('COM_AKEEBA_MULTIDB_GUI_LBL_PORT')?>
                </label>
                <div class="col-sm-9 col-xs-12">
                    <input type="number" name="ake_port" id="ake_port" class="form-control">
                </div>
            </div>
            <div class="form-group">
                <label class="control-label col-sm-3 col-xs-12" for="ake_username">
					<?php echo Text::_('COM_AKEEBA_MULTIDB_GUI_LBL_USERNAME')?>
                </label>
                <div class="col-sm-9 col-xs-12">
                    <input type="text" name="ake_username" id="ake_username" class="form-control">
                </div>
            </div>
            <div class="form-group">
                <label class="control-label col-sm-3 col-xs-12" for="ake_password">
					<?php echo Text::_('COM_AKEEBA_MULTIDB_GUI_LBL_PASSWORD')?>
                </label>
                <div class="col-sm-9 col-xs-12">
                    <input type="password" name="ake_password" id="ake_password" class="form-control">
                </div>
            </div>
            <div class="form-group">
                <label class="control-label col-sm-3 col-xs-12" for="ake_database">
					<?php echo Text::_('COM_AKEEBA_MULTIDB_GUI_LBL_DATABASE')?>
                </label>
                <div class="col-sm-9 col-xs-12">
                    <input type="text" name="ake_database" id="ake_database" class="form-control">
                </div>
            </div>
            <div class="form-group">
                <label class="control-label col-sm-3 col-xs-12" for="ake_prefix">
					<?php echo Text::_('COM_AKEEBA_MULTIDB_GUI_LBL_PREFIX')?>
                </label>
                <div class="col-sm-9 col-xs-12">
                    <input type="text" name="ake_prefix" id="ake_prefix" class="form-control">
                </div>
            </div>
        </div>
    </div>
    <div class="modal-footer">
        <button type="button" class="btn btn-default" id="akEditorBtnDefault"><?php echo Text::_('COM_AKEEBA_MULTIDB_GUI_LBL_TEST'); ?></button>
        <button type="button" class="btn btn-primary" id="akEditorBtnSave"><?php echo Text::_('COM_AKEEBA_MULTIDB_GUI_LBL_SAVE'); ?></button>
        <button type="button" class="btn btn-warning" id="akEditorBtnCancel"><?php echo Text::_('COM_AKEEBA_MULTIDB_GUI_LBL_CANCEL'); ?></button>
    </div>
</div>

<div class="alert alert-info">
	<strong><?php echo Text::_('COM_AKEEBA_CPANEL_PROFILE_TITLE'); ?></strong>
	#<?php echo $this->profileid; ?> <?php echo $this->profilename; ?>
</div>

<fieldset>
	<div id="ak_list_container">
		<table id="ak_list_table" class="table table-striped">
			<thead>
			<tr>
				<!-- Delete -->
				<th width="2em">&nbsp;</th>
				<!-- Edit -->
				<th width="2em">&nbsp;</th>
				<!-- Database host -->
				<th><?php echo Text::_('COM_AKEEBA_MULTIDB_LABEL_HOST') ?></th>
				<!-- Database -->
				<th><?php echo Text::_('COM_AKEEBA_MULTIDB_LABEL_DATABASE') ?></th>
			</tr>
			</thead>
			<tbody id="ak_list_contents">
			</tbody>
		</table>
	</div>
</fieldset>