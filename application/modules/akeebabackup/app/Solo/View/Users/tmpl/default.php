<?php
/**
 * @package     Solo
 * @copyright   2014-2017 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license     GNU GPL version 3 or later
 */

use \Awf\Text\Text;

// Used for type hinting
/** @var  \Solo\View\Users\Html  $this */

$router = $this->container->router;
$token = $this->container->session->getCsrfToken()->getValue();

/** @var \Solo\Model\Users $model */
$model = $this->getModel();
?>

<form action="<?php echo $router->route('index.php?view=users')?>" method="post" name="adminForm" id="adminForm" role="form">
	<input type="hidden" name="boxchecked" id="boxchecked" value="0">
	<input type="hidden" name="task" id="task" value="browse">
	<input type="hidden" name="filter_order" id="filter_order" value="<?php echo $this->lists->order ?>">
	<input type="hidden" name="filter_order_Dir" id="filter_order_Dir" value="<?php echo $this->lists->order_Dir ?>">
	<input type="hidden" name="token" value="<?php echo $token ?>">

	<table class="table table-striped" id="adminList">
		<thead>
			<tr>
				<th width="20px">&nbsp;</th>
				<th width="50px">
					<?php echo \Awf\Html\Grid::sort('SOLO_USERS_FIELD_ID', 'id', $this->lists->order_Dir, $this->lists->order, 'browse'); ?>
				</th>
				<th>
					<?php echo \Awf\Html\Grid::sort('SOLO_USERS_FIELD_USERNAME', 'username', $this->lists->order_Dir, $this->lists->order, 'browse'); ?>
				</th>
				<th>
					<?php echo \Awf\Html\Grid::sort('SOLO_USERS_FIELD_NAME', 'name', $this->lists->order_Dir, $this->lists->order, 'browse'); ?>
				</th>
				<th>
					<?php echo \Awf\Html\Grid::sort('SOLO_USERS_FIELD_EMAIL', 'email', $this->lists->order_Dir, $this->lists->order, 'browse'); ?>
				</th>
				<th width="80">
					<abbr title="<?php echo Text::_('SOLO_USERS_HEAD_TFA')?>"><?php echo Text::_('SOLO_USERS_FIELD_TFA'); ?></abbr>
				</th>
			</tr>
			<tr>
				<td></td>
				<td></td>
				<td>
					<input type="text" name="username" value="<?php echo $model->getState('username', '');?>"
							placeholder="<?php echo Text::_('SOLO_USERS_FIELD_USERNAME')?>"
							onchange="this.form.submit();">
				</td>
				<td>
					<input type="text" name="name" value="<?php echo $model->getState('name', '');?>"
							placeholder="<?php echo Text::_('SOLO_USERS_FIELD_NAME')?>"
							onchange="this.form.submit();">
				</td>
				<td>
					<input type="text" name="email" value="<?php echo $model->getState('email', ''); ?>"
							placeholder="<?php echo Text::_('SOLO_USERS_FIELD_EMAIL')?>"
							onchange="this.form.submit();">
				</td>
				<td></td>
			</tr>
		</thead>
		<tfoot>
			<tr>
				<td colspan="20" class="center"><?php echo $this->pagination->getListFooter(); ?></td>
			</tr>
		</tfoot>
		<tbody>
		<?php if (empty($this->items)): ?>
			<tr>
				<td colspan="20" class="center">
					<?php echo Text::_('AWF_PAGINATION_LBL_NO_RESULTS'); ?>
				</td>
			</tr>
		<?php else: ?>
		<?php $i = 0; foreach($this->items as $user):
			/** @var \Solo\Model\Users $user */
			$url = $router->route('index.php?view=users&task=edit&id=' . $user->id);
			$params = new \Awf\Registry\Registry($user->parameters);
			$tfaMethod = $params->get('tfa.method', 'none');
			$tfaMethod = empty($tfaMethod) ? 'none' : $tfaMethod;
		?>
			<tr>
				<td>
					<?php echo \Awf\Html\Grid::id($i++, $user->id); ?>
				</td>
				<td>
					<a href="<?php echo $url?>">
						<?php echo (int)$user->id ?>
					</a>
				</td>
				<td>
					<a href="<?php echo $url?>">
						<?php echo $this->escape($user->username) ?>
					</a>
				</td>
				<td>
					<a href="<?php echo $url?>">
						<?php echo $this->escape($user->getFieldValue('name')) ?>
					</a>
				</td>
				<td>
					<a href="<?php echo $url?>">
						<?php echo $this->escape($user->email) ?>
					</a>
				</td>
				<td>
					<img src="media/image/tfa-<?php echo $tfaMethod ?>.png" width="16" height="16" title="<?php echo Text::_('SOLO_USERS_TFA_' . $tfaMethod) ?>" />
				</td>
			</tr>
		<?php endforeach; ?>
		<?php endif; ?>
		</tbody>
	</table>
</form>

<script type="application/javascript">
	Solo.System.orderTable = function ()
	{
		table = document.getElementById("sortTable");
		direction = document.getElementById("directionTable");
		order = table.options[table.selectedIndex].value;
		if (order != '<?php echo $this->escape($this->lists->order); ?>')
		{
			dirn = 'asc';
		}
		else
		{
			dirn = direction.options[direction.selectedIndex].value;
		}

		Solo.System.tableOrdering(order, dirn, '');
	}
</script>