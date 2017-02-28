<?php
/**
 * @package		solo
 * @copyright	2014-2017 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license		GNU GPL version 3 or later
 */

use Awf\Text\Text;

/** @var \Solo\View\Users\Html $this */

?>
<div id="tfa_google" style="display: none">
	<p class="well well-sm">
		<?php echo Text::_('SOLO_USERS_TFA_GOOGLE_INTRO'); ?>
	</p>

	<input type="hidden" name="tfa[google]" value="<?php echo $tfa['google']?>">

	<h4><?php echo Text::_('SOLO_USERS_TFA_GOOGLE_LBL_STEP1') ?></h4>
	<p>
		<?php echo Text::_('SOLO_USERS_TFA_GOOGLE_SETUP1'); ?>
	</p>
	<ul>
		<li>
			<a href="http://support.google.com/accounts/bin/answer.py?hl=en&answer=1066447">
				Official Google Authenticator app for Android, iOS and BlackBerry
			</a>
		</li>
		<li>
			<a href="http://en.wikipedia.org/wiki/Google_Authenticator#Implementation">
				Compatible clients for other devices and OS (listed in Wikipedia)
			</a>
		</li>
	</ul>

	<div class="alert alert-warning">
		<?php echo Text::_('SOLO_USERS_TFA_GOOGLE_SETUP1_WARNING'); ?>
	</div>

	<h4><?php echo Text::_('SOLO_USERS_TFA_GOOGLE_LBL_STEP2') ?></h4>
	<div class="col-sm-6 col-xs-12">
		<?php echo Text::_('SOLO_USERS_TFA_GOOGLE_SETUP2A') ?>
		<table class="table table-striped">
			<tr>
				<td>
					<?php echo Text::_('SOLO_USERS_TFA_GOOGLE_SETUP2A_ACCOUNT') ?>
				</td>
				<td>
					Akeeba Solo
				</td>
			</tr>
			<tr>
				<td>
					<?php echo Text::_('SOLO_USERS_TFA_GOOGLE_SETUP2A_KEY') ?>
				</td>
				<td>
					<code><?php echo $tfa['google']?></code>
				</td>
			</tr>
		</table>
	</div>
	<div class="col-sm-6 col-xs-12">
		<p>
			<?php echo Text::_('SOLO_USERS_TFA_GOOGLE_SETUP2B'); ?>
		</p>
		<img src="https://chart.googleapis.com/chart?chs=200x200&chld=Q|2&cht=qr&chl=<?php echo urlencode('otpauth://totp/Akeeba%20Solo?secret=' . $tfa['google']) ?>">
	</div>

	<h4><?php echo Text::_('SOLO_USERS_TFA_GOOGLE_LBL_STEP3') ?></h4>
	<div class="form-group">
		<label class="control-label col-sm-2" for="tfa[secret]">
			<?php echo Text::_('SOLO_USERS_LBL_TFASECURITYCODE'); ?>
		</label>
		<div class="col-sm-9">
			<input type="text" name="tfa[secret]" class="form-control" value="">
		</div>
	</div>
</div>