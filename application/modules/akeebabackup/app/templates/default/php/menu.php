<?php
use Awf\Document\Document;
use Awf\Document\Menu\Item;
use Awf\Text\Text;

function _solo_template_renderSubmenu(Document $app, Item $root, $ulClass = 'dropdown-menu')
{
	$enabled = $app->getMenu()->isEnabled('main');

	$children = $root->getChildren();

	if (empty($children))
	{
		return;
	}

	echo "<ul class=\"$ulClass\">\n";

	/** @var Item $item */
	foreach ($children as $item):
		$class = $item->isActive() ? 'class="active"' : '';
		$link = $item->getUrl();

		if (!$enabled)
		{
			$class = 'class="disabled"';
			$link = '#';
		}
	?>
	<li <?php echo $class ?>>
		<a href="<?php echo $link ?>"><?php echo Text::_($item->getTitle()) ?></a>
		<?php _solo_template_renderSubmenu($app, $item); ?>
	</li>
	<?php
	endforeach;

	echo "</ul>\n";
}