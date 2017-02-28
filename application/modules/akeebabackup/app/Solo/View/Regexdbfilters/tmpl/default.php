<?php
/**
 * @package        solo
 * @copyright      2014-2017 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license        GNU GPL version 3 or later
 */

use Awf\Text\Text;
use Solo\Helper\Escape;

/** @var \Solo\View\Regexdbfilters\Html $this */

$router = $this->container->router;

echo $this->loadAnyTemplate('Common/error_modal');
?>

<div class="alert alert-info">
	<strong><?php echo Text::_('COM_AKEEBA_CPANEL_PROFILE_TITLE'); ?></strong>
	#<?php echo $this->profileid; ?> <?php echo $this->profilename; ?>
</div>

<div class="form-inline well">
	<div>
		<label><?php echo Text::_('COM_AKEEBA_DBFILTER_LABEL_ROOTDIR') ?></label>
		<span><?php echo $this->root_select; ?></span>
	</div>
</div>

<div>
	<div id="ak_list_container">
		<table id="table-container" class="adminlist table table-striped">
			<thead>
			<tr>
				<td width="80px">&nbsp;</td>
				<td width="250px"><?php echo Text::_('COM_AKEEBA_FILEFILTERS_LABEL_TYPE') ?></td>
				<td><?php echo Text::_('COM_AKEEBA_FILEFILTERS_LABEL_FILTERITEM') ?></td>
			</tr>
			</thead>
			<tbody id="ak_list_contents" class="table-container">
			</tbody>
		</table>
	</div>
</div>