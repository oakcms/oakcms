<?php
/**
 * @package        solo
 * @copyright      2014-2017 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license        GNU GPL version 3 or later
 */

use Awf\Text\Text;

/** @var \Solo\View\Upload\Html $this */

$router = $this->container->router;

?>
<form action="<?php echo $router->route('index.php?view=upload&task=upload&tmpl=component&id=' . $this->id) ?>" method="POST" name="akeebaform">
	<input type="hidden" name="part" value="<?php echo $this->part ?>" />
	<input type="hidden" name="frag" value="<?php echo $this->frag ?>" />
</form>

<?php if($this->frag == 0): ?>
<p class="well">
	<?php echo Text::sprintf('COM_AKEEBA_TRANSFER_MSG_UPLOADINGPART',$this->part+1, $this->parts); ?>
</p>
<?php else: ?>
<p class="well">
	<?php echo Text::sprintf('COM_AKEEBA_TRANSFER_MSG_UPLOADINGFRAG',$this->part+1, $this->parts); ?>
</p>
<?php endif; ?>

<script type="text/javascript" language="javascript">
Solo.loadScripts.push(function ()
{
	window.setTimeout('document.forms.akeebaform.submit();', 1000);
});
</script>