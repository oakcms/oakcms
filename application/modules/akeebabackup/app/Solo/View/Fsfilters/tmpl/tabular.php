<?php
/**
 * @package        solo
 * @copyright      2014-2017 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license        GNU GPL version 3 or later
 */

use Awf\Text\Text;
use Solo\Helper\Escape;

/** @var \Solo\View\Fsfilters\Html $this */

$router = $this->container->router;

$js = <<< JS

Solo.loadScripts.push(function() {
    Solo.Fsfilters.renderTab(akeeba_fsfilter_data);	
});

JS;

$this->getContainer()->application->getDocument()->addScriptDeclaration($js);

echo $this->loadAnyTemplate('CommonTemplates/ErrorModal');
?>

<div class="alert alert-info">
	<strong><?php echo Text::_('COM_AKEEBA_CPANEL_PROFILE_TITLE'); ?></strong>
	#<?php echo $this->profileid; ?> <?php echo $this->profilename; ?>
</div>

<div class="form-inline well">
	<div>
		<label><?php echo Text::_('COM_AKEEBA_FILEFILTERS_LABEL_ROOTDIR') ?></label>
		<span><?php echo $this->root_select; ?></span>
		<a class="btn btn-sm btn-default" href="<?php echo $router->route('index.php?view=fsfilters')?>">
			<span class="glyphicon glyphicon-list-alt"></span>
			<?php echo Text::_('COM_AKEEBA_FILEFILTERS_LABEL_NORMALVIEW')?>
		</a>
	</div>
	<div id="addnewfilter">
		<?php echo Text::_('COM_AKEEBA_FILEFILTERS_LABEL_ADDNEWFILTER') ?>
		<button class="btn btn-default" onclick="Solo.Fsfilters.addNew('directories'); return false;">
			<span class="fa fa-ban"></span>
			<?php echo Text::_('COM_AKEEBA_FILEFILTERS_TYPE_DIRECTORIES') ?>
		</button>
		<button class="btn btn-default" onclick="Solo.Fsfilters.addNew('skipfiles'); return false;">
			<span class="fa fa-file"></span>
			<?php echo Text::_('COM_AKEEBA_FILEFILTERS_TYPE_SKIPFILES') ?>
		</button>
		<button class="btn btn-default" onclick="Solo.Fsfilters.addNew('skipdirs'); return false;">
			<span class="fa fa-folder"></span>
			<?php echo Text::_('COM_AKEEBA_FILEFILTERS_TYPE_SKIPDIRS') ?>
		</button>
		<button class="btn btn-default" onclick="Solo.Fsfilters.addNew('files'); return false;">
			<span class="fa fa-file-text"></span>
			<?php echo Text::_('COM_AKEEBA_FILEFILTERS_TYPE_FILES') ?>
		</button>
	</div>
</div>

<fieldset id="ak_roots_container_tab">
	<div id="ak_list_container">
		<table id="ak_list_table" class="table table-striped">
			<thead>
			<tr>
				<td width="250px"><?php echo Text::_('COM_AKEEBA_FILEFILTERS_LABEL_TYPE') ?></td>
				<td><?php echo Text::_('COM_AKEEBA_FILEFILTERS_LABEL_FILTERITEM') ?></td>
			</tr>
			</thead>
			<tbody id="ak_list_contents">
			</tbody>
		</table>
	</div>
</fieldset>
