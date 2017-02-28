<?php
/**
 * @package        solo
 * @copyright      2014-2017 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license        GNU GPL version 3 or later
 */

use Awf\Text\Text;

/** @var \Solo\View\Remotefiles\Html $this */

$router = $this->container->router;

?>
	<style type="text/css">
		dt.message {
			display: none;
		}

		dd.message {
			list-style: none;
		}
	</style>

	<h2>
		<?php echo Text::_('COM_AKEEBA_REMOTEFILES') ?>
	</h2>

<?php if (empty($this->actions)): ?>
	<div class="alert alert-danger">
		<h3 class="alert-heading">
			<?php echo Text::_('COM_AKEEBA_REMOTEFILES_ERR_NOTSUPPORTED_HEADER') ?>
		</h3>

		<p>
			<?php echo Text::_('COM_AKEEBA_REMOTEFILES_ERR_NOTSUPPORTED'); ?>
		</p>
	</div>
<?php else: ?>

	<div id="cpanel">
		<?php foreach ($this->actions as $action): ?>
			<?php if ($action['type'] == 'button'): ?>
				<button class="btn <?php echo $action['class'] ?>"
						onclick="window.location = '<?php echo $router->route($action['link']) ?>'; return false;">
					<span class="<?php echo $action['icon'] ?>"></span>
					<?php echo $action['label']; ?>
				</button>
			<?php endif; ?>
		<?php endforeach; ?>
	</div>
	<div style="clear: both;"></div>

	<h3>
		<?php echo Text::_('COM_AKEEBA_REMOTEFILES_LBL_DOWNLOADLOCALLY') ?>
	</h3>
	<?php $items = 0;
	foreach ($this->actions as $action): ?>
		<?php if ($action['type'] == 'link'): ?>
			<?php $items++ ?>
			<a href="<?php echo $router->route($action['link']) ?>" class="btn btn-sm">
				<span class="<?php echo $action['icon'] ?>"></span>
				<?php echo $action['label'] ?>
			</a>
		<?php endif; ?>
	<?php endforeach; ?>

	<?php if (!$items): ?>
		<p class="alert">
			<?php echo Text::_('COM_AKEEBA_REMOTEFILES_LBL_NOTSUPPORTSLOCALDL') ?>
		</p>
	<?php endif; ?>

<?php endif; ?>