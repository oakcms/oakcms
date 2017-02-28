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

// If we're going to use SQLite let's hide all the options
$js = <<<JS
Solo.System.documentReady(function() {
	Solo.Setup.onDriverChange = function(){
		var driver = document.getElementById('driver').value.toLowerCase();
		
        if (driver == 'sqlite')
        {
            document.getElementById('host-wrapper').setAttribute('display', 'none');
            document.getElementById('user-wrapper').setAttribute('display', 'none');
            document.getElementById('pass-wrapper').setAttribute('display', 'none');
            document.getElementById('name-wrapper').setAttribute('display', 'none');
            document.getElementById('prefix-wrapper').setAttribute('display', 'none');
        
            document.getElementById('host').value = '';
            document.getElementById('user').value = '';
            document.getElementById('pass').value = '';
            document.getElementById('name').value = '';
            document.getElementById('prefix').value = 'solo_';
        }
        else
        {
            document.getElementById('host-wrapper').setAttribute('display', 'block');
            document.getElementById('user-wrapper').setAttribute('display', 'block');
            document.getElementById('pass-wrapper').setAttribute('display', 'block');
            document.getElementById('name-wrapper').setAttribute('display', 'block');
            document.getElementById('prefix-wrapper').setAttribute('display', 'block');
        }
    };
    
	Solo.System.addEventListener(document.getElementById('driver'), 'change', Solo.Setup.onDriverChange);
	Solo.Setup.onDriverChange();
	
});
JS;

$this->container->application->getDocument()->addScriptDeclaration($js);

?>

<h1><?php echo Text::_('SOLO_SETUP_SUBTITLE_DATABASE') ?></h1>

<p><?php echo Text::_('SOLO_SETUP_LBL_DATABASE_INFO') ?></p>

<form action="<?php echo Uri::rebase('?view=setup&task=installdb', $this->container) ?>" method="POST" class="form-horizontal" role="form" name="dbForm">
	<div class="form-group">
		<label for="driver" class="col-sm-2 control-label">
			<?php echo Text::_('SOLO_SETUP_LBL_DATABASE_DRIVER'); ?>
		</label>
		<div class="col-sm-10">
			<?php echo \Solo\Helper\Setup::databaseTypesSelect($this->connectionParameters['driver']); ?>
			<div class="help-block">
				<?php echo Text::_('SOLO_SETUP_LBL_DATABASE_DRIVER_HELP') ?>
			</div>
		</div>
	</div>

	<div class="form-group" id="host-wrapper">
		<label for="host" class="col-sm-2 control-label">
			<?php echo Text::_('SOLO_SETUP_LBL_DATABASE_HOST'); ?>
		</label>
		<div class="col-sm-10">
			<input type="text" class="form-control" id="host" name="host" placeholder="<?php echo Text::_('SOLO_SETUP_LBL_DATABASE_HOST'); ?>" value="<?php echo $this->connectionParameters['host']?>">
			<div class="help-block">
				<?php echo Text::_('SOLO_SETUP_LBL_DATABASE_HOST_HELP') ?>
			</div>
		</div>
	</div>

	<div class="form-group" id="user-wrapper">
		<label for="user" class="col-sm-2 control-label">
			<?php echo Text::_('SOLO_SETUP_LBL_DATABASE_USER'); ?>
		</label>
		<div class="col-sm-10">
			<input type="text" class="form-control" id="user" name="user" placeholder="<?php echo Text::_('SOLO_SETUP_LBL_DATABASE_USER'); ?>" value="<?php echo $this->connectionParameters['user']?>">
			<div class="help-block">
				<?php echo Text::_('SOLO_SETUP_LBL_DATABASE_USER_HELP') ?>
			</div>
		</div>
	</div>

	<div class="form-group" id="pass-wrapper">
		<label for="pass" class="col-sm-2 control-label">
			<?php echo Text::_('SOLO_SETUP_LBL_DATABASE_PASS'); ?>
		</label>
		<div class="col-sm-10">
			<input type="password" class="form-control" id="pass" name="pass" placeholder="<?php echo Text::_('SOLO_SETUP_LBL_DATABASE_PASS'); ?>" value="<?php echo $this->connectionParameters['pass']?>">
			<div class="help-block">
				<?php echo Text::_('SOLO_SETUP_LBL_DATABASE_PASS_HELP') ?>
			</div>
		</div>
	</div>

	<div class="form-group" id="name-wrapper">
		<label for="name" class="col-sm-2 control-label">
			<?php echo Text::_('SOLO_SETUP_LBL_DATABASE_NAME'); ?>
		</label>
		<div class="col-sm-10">
			<input type="text" class="form-control" id="name" name="name" placeholder="<?php echo Text::_('SOLO_SETUP_LBL_DATABASE_NAME'); ?>" value="<?php echo $this->connectionParameters['name']?>">
			<div class="help-block">
				<?php echo Text::_('SOLO_SETUP_LBL_DATABASE_NAME_HELP') ?>
			</div>
		</div>
	</div>

	<div class="form-group" id="prefix-wrapper">
		<label for="prefix" class="col-sm-2 control-label">
			<?php echo Text::_('SOLO_SETUP_LBL_DATABASE_PREFIX'); ?>
		</label>
		<div class="col-sm-10">
			<input type="text" class="form-control" id="prefix" name="prefix" placeholder="<?php echo Text::_('SOLO_SETUP_LBL_DATABASE_PREFIX'); ?>" value="<?php echo $this->connectionParameters['prefix']?>">
			<div class="help-block">
				<?php echo Text::_('SOLO_SETUP_LBL_DATABASE_PREFIX_HELP') ?>
			</div>
		</div>
	</div>

	<div class="col-sm-10 col-sm-push-2">
		<button type="submit" id="dbFormSubmit" class="btn btn-primary">
			<?php echo Text::_('SOLO_BTN_SUBMIT') ?>
		</button>
	</div>
</form>