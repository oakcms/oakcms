<?php
/**
 * @package		solo
 * @copyright	2014-2017 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license		GNU GPL version 3 or later
 */

use \Awf\Text\Text;
use \Awf\Html;
use Akeeba\Engine\Factory;

// Used for type hinting
/** @var \Solo\View\Main\Html $this */

$router = $this->container->router;

$quirks = Factory::getConfigurationChecks()->getDetailedStatus(false);
$status = Factory::getConfigurationChecks()->getShortStatus();

if($status && empty($quirks))
{
	$panel_style = 'success';
}
elseif($status && !empty($quirks))
{
	$panel_style = 'warning';

	foreach ($quirks as $quirk)
	{
		if (($quirk['severity'] == 'high') || $quirk['severity'] == 'critical')
		{
			$panel_style = 'danger';
			break;
		}
	}
}
else
{
	$panel_style = 'danger';
}

?>

<div class="panel panel-<?php echo $panel_style ?>">
	<div class="panel-heading">
		<?php echo Text::_('COM_AKEEBA_CPANEL_LABEL_STATUSSUMMARY'); ?>
	</div>
	<div class="panel-body">
		<div class="alert alert-<?php echo $panel_style ?>">
			<?php if ($panel_style == 'success'): ?>
				<span class="glyphicon glyphicon-ok-sign"></span>
			<?php echo Text::_('SOLO_MAIN_LBL_STATUS_OK'); ?>
			<?php elseif ($panel_style == 'warning'): ?>
			<span class="glyphicon glyphicon-warning-sign"></span>
			<?php echo Text::_('SOLO_MAIN_LBL_STATUS_WARNING'); ?>
			<?php else: ?>
				<span class="glyphicon glyphicon-exclamation-sign"></span>
			<?php echo Text::_('SOLO_MAIN_LBL_STATUS_ERROR'); ?>
			<?php endif; ?>
		</div>
	</div>
	<?php if (!empty($quirks)): ?>
		<table class="table table-striped">
			<thead>
			<tr>
				<th>
					<?php echo Text::_('COM_AKEEBA_CPANEL_LABEL_STATUSDETAILS'); ?>
				</th>
			</tr>
			</thead>
			<tbody>
			<?php foreach ($quirks as $quirk):
				switch ($quirk['severity'])
				{
					case 'critical':
						$classSufix = 'danger';
						break;

					case 'high':
						$classSufix = 'warning';
						break;

					case 'medium':
						$classSufix = 'info';
						break;

					default:
						$classSufix = 'default';
						break;
				}
				?>
				<tr>
					<td>
						<a href="<?php echo $quirk['help_url']; ?>" target="_blank">
								<span class="label label-<?php echo $classSufix ?>">
									S<?php echo $quirk['code']; ?>
								</span>
							<?php echo $quirk['description']; ?>
						</a>
					</td>
				</tr>
			<?php endforeach; ?>
			</tbody>
		</table>
	<?php endif; ?>

</div>