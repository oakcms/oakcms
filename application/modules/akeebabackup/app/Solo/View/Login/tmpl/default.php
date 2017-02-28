<?php
/**
 * @package     Solo
 * @copyright   2014-2017 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license     GNU GPL version 3 or later
 */

use Awf\Text\Text;

// Used for type hinting
/** @var  \Solo\View\Login\Html  $this */

$router = $this->container->router;

?>
<form class="form-signin" role="form" action="<?php echo $router->route('index.php?view=login&task=login') ?>" method="POST" id="loginForm">
	<h2 class="form-signin-heading">
		<?php echo Text::_('SOLO_LOGIN_PLEASELOGIN'); ?>
	</h2>
	<input type="hidden" name="token" value="<?php echo $this->container->session->getCsrfToken()->getValue() ?>">
	<input type="text" name="username" class="form-control" placeholder="<?php echo Text::_('SOLO_LOGIN_LBL_USERNAME'); ?>" required autofocus value="<?php echo $this->escape($this->username) ?>">
	<input type="password" name="password" class="form-control" placeholder="<?php echo Text::_('SOLO_LOGIN_LBL_PASSWORD'); ?>" required value="<?php echo $this->escape($this->password) ?>">
	<?php if (!defined('AKEEBADEBUG')): ?>
	<input type="text" name="secret" class="form-control" placeholder="<?php echo Text::_('SOLO_LOGIN_LBL_SECRETCODE'); ?>" value="<?php echo $this->escape($this->secret) ?>">
	<?php endif; ?>
	<div class="buttonContainer">
		<button class="btn btn-lg btn-primary btn-block" type="submit">
			<span class="glyphicon glyphicon-log-in"></span>
			<?php echo Text::_('SOLO_LOGIN_LBL_LOGIN'); ?>
		</button>
	</div>
</form>

<?php if ($this->autologin): ?>
<script type="text/javascript">
Solo.System.documentReady({
    document.getElementById('loginForm').submit();
})
</script>
<?php endif; ?>