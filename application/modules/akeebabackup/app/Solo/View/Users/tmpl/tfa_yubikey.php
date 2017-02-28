<?php
/**
 * @package		solo
 * @copyright	2014-2017 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license		GNU GPL version 3 or later
 */

use Awf\Text\Text;

/** @var \Solo\View\Users\Html $this */

?>
<div id="tfa_yubikey" style="display: none">
	<p class="well well-sm">
		<?php echo Text::_('SOLO_USERS_TFA_YUBIKEY_INTRO'); ?>
	</p>
	<p>
		<?php echo Text::_('SOLO_USERS_TFA_YUBIKEY_SETUP'); ?>
	</p>

	<div class="form-group">
		<label class="control-label col-sm-2" for="tfa[yubikey]">
			<?php echo Text::_('SOLO_USERS_LBL_TFASECURITYCODE'); ?>
		</label>
		<div class="col-sm-9">
			<input type="text" name="tfa[yubikey]" class="form-control" value="<?php echo $tfa['yubikey'] ?>">
		</div>
	</div>
</div>
