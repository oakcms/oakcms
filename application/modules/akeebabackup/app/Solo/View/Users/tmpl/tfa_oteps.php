<?php
/**
 * @package		solo
 * @copyright	2014-2017 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license		GNU GPL version 3 or later
 */

use Awf\Text\Text;

/** @var \Solo\View\Users\Html $this */

?>
<div id="tfa_oteps">
	<h4><?php echo Text::_('SOLO_USERS_TFA_OTEPS_HEAD'); ?></h4>

	<p class="alert alert-info">
		<?php echo Text::_('SOLO_USERS_TFA_OTEPS_INTRO'); ?>
	</p>

	<pre>
<?php
foreach($tfa['otep'] as $otep)
{
	echo substr($otep, 0, 3) . '-' . substr($otep, 3, 3) . '-' . substr($otep, 6)  . "\n";
}
?></pre>
</div>