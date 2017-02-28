<?php
use Awf\Document\Document;
use Awf\Document\Toolbar\Button;
use Awf\Text\Text;

/** @var Document $this */

$buttons = $this->getToolbar()->getButtons();
$submenu = $this->getMenu()->getMenuItems('submenu')->getChildren();
if (!empty($buttons) || !empty($submenu)): ?>
<nav class="navbar" role="navigation">
	<div class="navbar-header">
		<button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
			<span class="sr-only"><?php echo \Awf\Text\Text::_('SOLO_COMMON_TOGGLENAV') ?></span>
			<span class="icon-bar"></span>
			<span class="icon-bar"></span>
			<span class="icon-bar"></span>
		</button>

	</div>
	<div class="navbar-collapse collapse">
		<?php if (($buttons = $this->getToolbar()->getButtons()) && count($buttons)):?>
		<div class="navbar-form navbar-left">
			<?php
			foreach ($buttons as $button):
				/** @var Button $button */
				if ($url = $button->getUrl())
				{
					$type = 'a';
					$action = 'href="' . $url . '"';
				}
				else
				{
					$type = 'button';
					$action = 'onclick="' . $button->getOnClick() . '"';
				}
				?>
				<<?php echo $type . ' ' . $action?> class="btn btn-sm <?php echo $button->getClass() ?>" id="<?php echo $button->getId() ?>">
				<?php if ($icon = $button->getIcon()): ?>
				<span class="<?php echo $icon ?>"></span>
			<?php endif; ?>
				<?php echo Text::_($button->getTitle()) ?>
				</<?php echo $type?>>
			<?php endforeach; ?>
		</div>
		<?php endif; ?>

		<ul class="nav navbar-right">
			<?php _solo_template_renderSubmenu($this, $this->getMenu()->getMenuItems('submenu'), 'nav navbar-nav'); ?>
		</ul>

	</div>
</nav>

<?php endif; ?>