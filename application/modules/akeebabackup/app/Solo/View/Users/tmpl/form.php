<?php
/**
 * @package		solo
 * @copyright	2014-2017 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license		GNU GPL version 3 or later
 */

use Awf\Text\Text;

/** @var \Solo\View\Users\Html $this */

$router = $this->container->router;
$token = $this->container->session->getCsrfToken()->getValue();

/** @var \Solo\Model\Users $model */
$model = $this->getModel();

$permissions = array(
	'backup'	=> false,
	'configure'	=> false,
	'download'	=> false,
);

$tfa = array(
	'method'	=> 'none',
	'yubikey'	=> '',
	'google'	=> '',
	'otep'		=> array(),
);

if ($model->id)
{
	$user = $this->container->userManager->getUser($model->id);
	$permissions = array(
		'backup'	=> $user->getPrivilege('akeeba.backup', false),
		'configure'	=> $user->getPrivilege('akeeba.configure', false),
		'download'	=> $user->getPrivilege('akeeba.download', false),
	);
	$tfa = array(
		'method'	=> $user->getParameters()->get('tfa.method', 'none'),
		'yubikey'	=> $user->getParameters()->get('tfa.yubikey', ''),
		'google'	=> $user->getParameters()->get('tfa.google', ''),
		'otep'		=> $user->getParameters()->get('tfa.otep', array()),
	);

	if (empty($tfa['google']))
	{
		$totp = new \Awf\Encrypt\Totp(30, 6, 10);
		$tfa['google'] = $totp->generateSecret();
	}
}

?>
<form name="adminForm" id="adminForm" action="<?php echo $router->route('index.php?view=users') ?>" method="POST" class="form-horizontal" role="form">
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="id" value="<?php echo $model->id ?>" />
	<input type="hidden" name="<?php echo $token ?>" value="1">

	<fieldset>
		<legend><?php echo Text::_('SOLO_USERS_HEAD_BASIC'); ?></legend>

		<div class="form-group">
			<label class="control-label col-sm-3" for="username">
				<?php echo Text::_('SOLO_USERS_FIELD_USERNAME'); ?> *
			</label>
			<div class="col-sm-9">
				<input type="text" name="username" maxlength="255" size="50"
					   value="<?php echo $this->escape($model->username) ?>"
					   class="form-control" required />
			</div>
		</div>

		<div class="form-group">
			<label class="control-label col-sm-3" for="password">
				<?php echo Text::_('SOLO_USERS_FIELD_PASSWORD'); ?>
			</label>
			<div class="col-sm-9">
				<input type="password" name="password" maxlength="255" size="50"
					   value=""
					   class="form-control" />
			</div>
		</div>

		<div class="form-group">
			<label class="control-label col-sm-3" for="repeatpassword">
				<?php echo Text::_('SOLO_USERS_FIELD_PASSWORDREPEAT'); ?>
			</label>
			<div class="col-sm-9">
				<input type="password" name="repeatpassword" maxlength="255" size="50"
					   value=""
					   class="form-control" />
			</div>
		</div>

		<div class="form-group">
			<label class="control-label col-sm-3" for="email">
				<?php echo Text::_('SOLO_USERS_FIELD_EMAIL'); ?> *
			</label>
			<div class="col-sm-9">
				<input type="email" name="email" maxlength="255" size="50"
					   value="<?php echo $this->escape($model->email) ?>"
					   class="form-control" required />
			</div>
		</div>

		<div class="form-group">
			<label class="control-label col-sm-3" for="name">
				<?php echo Text::_('SOLO_USERS_FIELD_NAME'); ?>
			</label>
			<div class="col-sm-9">
				<input type="text" name="name" maxlength="255" size="50"
					   value="<?php echo $this->escape($model->name) ?>"
					   class="form-control" />
			</div>
		</div>

		<div class="form-group">
			<label class="control-label col-sm-3" for="name">
				<?php echo Text::_('SOLO_USERS_FIELDSET_PERMISSIONS'); ?>
			</label>
			<div class="col-sm-9">
				<div class="checkbox">
					<label>
						<input type="checkbox" name="permissions[backup]" <?php echo $permissions['backup'] ? 'checked' : ''?>>
						<?php echo Text::_('SOLO_USERS_FIELD_PERMISSIONS_BACKUP') ?>
					</label>
				</div>

				<div class="checkbox">
					<label>
						<input type="checkbox" name="permissions[configure]"  <?php echo $permissions['configure'] ? 'checked' : ''?>>
						<?php echo Text::_('SOLO_USERS_FIELD_PERMISSIONS_CONFIGURE') ?>
					</label>
				</div>

				<div class="checkbox">
					<label>
						<input type="checkbox" name="permissions[download]"  <?php echo $permissions['download'] ? 'checked' : ''?>>
						<?php echo Text::_('SOLO_USERS_FIELD_PERMISSIONS_DOWNLOAD') ?>
					</label>
				</div>
			</div>
		</div>
	</fieldset>

	<fieldset>
		<legend><?php echo Text::_('SOLO_USERS_HEAD_TFA'); ?></legend>

		<p>
			<?php echo Text::_('SOLO_USERS_LBL_ABOUTTFA'); ?>
		</p>
	</fieldset>

<?php if (empty($tfa['method']) || ($tfa['method'] == 'none')): ?>
	<div class="form-group">
		<label class="control-label col-sm-3" for="tfa[method]">
			<?php echo Text::_('SOLO_USERS_LBL_TFAMETHOD'); ?>
		</label>
		<div class="col-sm-9">
			<?php echo \Solo\Helper\Setup::tfaMethods('tfa[method]', $tfa['method']) ?>
		</div>
	</div>

	<div id="tfa_containers" class="col-sm-9 col-sm-push-3">
		<?php echo $this->loadAnyTemplate('Users/tfa_none', array('tfa' => $tfa)) ?>
		<?php echo $this->loadAnyTemplate('Users/tfa_yubikey', array('tfa' => $tfa)) ?>
		<?php echo $this->loadAnyTemplate('Users/tfa_google', array('tfa' => $tfa)) ?>
	</div>
<?php else: ?>
	<div class="form-group">
		<label class="control-label col-sm-3" for="tfa[keep]">
			<?php echo Text::_('SOLO_USERS_LBL_TFAENABLE'); ?>
		</label>
		<div class="col-sm-9">
			<select class="form-control" name="tfa[keep]" id="tfa[keep]">
				<option value="1" checked="checked"><?php echo Text::_('SOLO_YES') ?></option>
				<option value="0"><?php echo Text::_('SOLO_NO') ?></option>
			</select>
		</div>

		<div id="tfa_containers" class="col-sm-9 col-sm-push-3">
		<?php echo $this->loadAnyTemplate('Users/tfa_oteps', array('tfa' => $tfa)) ?>
		</div>
	</div>
<?php endif; ?>

</form>