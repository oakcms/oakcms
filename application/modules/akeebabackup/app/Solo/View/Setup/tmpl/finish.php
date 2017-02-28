<?php
/**
 * @package     Solo
 * @copyright   2014-2017 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license     GNU GPL version 3 or later
 *
 * @var \Solo\View\Setup\Html $this
 */

use Awf\Text\Text;
use Awf\Uri\Uri;

/** @var \Solo\View\Setup\Html $this */

?>
<div class="alert alert-danger">
	<button type="button" class="close" data-dismiss="alert">&times;</button>
	<h4>
		<span class="glyphicon glyphicon-exclamation-sign"></span>
		<?php echo Text::_('SOLO_SETUP_MSG_CONFIGNOTWRITTEN_HEAD'); ?>
	</h4>
	<?php echo Text::_('SOLO_SETUP_MSG_CONFIGNOTWRITTEN'); ?>
</div>

<?php echo Text::_('SOLO_SETUP_LBL_MANUALCONFIGINSTRUCTIONS'); ?>

<pre>
<?php echo '&lt;?php die(); ?&gt;' . "\n" . $this->container->appConfig->toString('JSON', array('pretty_print' => true)); ?>
</pre>