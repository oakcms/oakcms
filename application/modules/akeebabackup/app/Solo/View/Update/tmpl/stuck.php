<?php
/**
 * @package		solo
 * @copyright	2014-2017 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license		GNU GPL version 3 or later
 */

use Awf\Text\Text;

/** @var   \Solo\View\Update\Html  $this */

$router = $this->container->router;

?>
<div class="alert alert-danger">
	<h3>
		<?php echo Text::_('SOLO_UPDATE_STUCK_HEAD') ?>
	</h3>

	<p><?php echo Text::_('SOLO_UPDATE_STUCK_INFO'); ?></p>

	<p><?php echo Text::sprintf('SOLO_UPDATE_NOTSUPPORTED_ALTMETHOD', $this->escape($this->updateInfo->extInfo->title)); ?></p>

	<p class="liveupdate-buttons">
		<a href="<?php echo $router->route('index.php?view=update&force=1') ?>" class="btn btn-default">
			<span class="glyphicon glyphicon-refresh"></span>
			<?php echo Text::_('SOLO_UPDATE_REFRESH_INFO') ?>
		</a>
	</p>
</div> 