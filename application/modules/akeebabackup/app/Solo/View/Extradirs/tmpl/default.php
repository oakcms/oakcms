<?php
/**
 * @package        solo
 * @copyright      2014-2017 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license        GNU GPL version 3 or later
 */

use Awf\Text\Text;
use Solo\Helper\Escape;

/** @var \Solo\View\Extradirs\Html $this */

$router = $this->container->router;

echo $this->loadAnyTemplate('Common/error_modal');
echo $this->loadAnyTemplate('Common/folder_browser');
?>

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
				<th>&nbsp;</th>
				<!-- Edit -->
				<th>&nbsp;</th>
				<!-- Directory path -->
				<th rel="popover" data-original-title="<?php echo Text::_('COM_AKEEBA_INCLUDEFOLDER_LABEL_DIRECTORY') ?>"
					data-content="<?php echo Text::_('COM_AKEEBA_INCLUDEFOLDER_LABEL_DIRECTORY_HELP') ?>">
					<?php echo Text::_('COM_AKEEBA_INCLUDEFOLDER_LABEL_DIRECTORY') ?>
				</th>
				<!-- Directory path -->
				<th rel="popover" data-original-title="<?php echo Text::_('COM_AKEEBA_INCLUDEFOLDER_LABEL_VINCLUDEDIR') ?>"
					data-content="<?php echo Text::_('COM_AKEEBA_INCLUDEFOLDER_LABEL_VINCLUDEDIR_HELP') ?>">
					<?php echo Text::_('COM_AKEEBA_INCLUDEFOLDER_LABEL_VINCLUDEDIR') ?>
				</th>
			</tr>
			</thead>
			<tbody id="ak_list_contents">
			</tbody>
		</table>
	</div>
</fieldset>