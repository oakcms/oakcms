<?php
/**
 * @package     Solo
 * @copyright   2014-2017 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license     GNU GPL version 3 or later
 *
 * @var \Solo\View\Setup\Html $this
 */

use Awf\Text\Text;
use Solo\Helper\Escape;

/** @var \Solo\View\Wizard\Html $this */

$router = $this->container->router;
$config = \Akeeba\Engine\Factory::getConfiguration();

echo $this->loadAnyTemplate('Common/folder_browser');
?>

<div class="alert alert-info">
	<?php echo Text::_('SOLO_WIZARD_LBL_INTRO'); ?>
</div>

<form action="<?php echo $router->route('index.php?view=wizard&task=applySiteSettings') ?>" method="post" role="form" class="form-horizontal" id="adminForm">
	<input type="hidden" name="token" value="<?php echo $this->container->session->getCsrfToken()->getValue() ?>"/>

	<div class="panel panel-default">
		<div class="panel-heading">
			<h3 class="panel-title">
				<?php echo Text::_('SOLO_WIZARD_LBL_SITEROOT_TITLE') ?>
			</h3>
		</div>

		<div class="panel-body">
			<p><?php echo Text::_('SOLO_WIZARD_LBL_SITEROOT_INTRO');?></p>

			<div class="form-group">
				<label for="var[akeeba.platform.site_url]" class="col-sm-3 control-label">
					<?php echo Text::_('SOLO_CONFIG_PLATFORM_SITEURL_TITLE')?>
				</label>
				<div class="col-sm-9">
					<input type="text" class="form-control" id="var[akeeba.platform.site_url]"
						   name="var[akeeba.platform.site_url]" size="30"
						   value="<?php echo $this->siteInfo->url ?>">
					<div class="help-block">
						<?php echo Text::_('SOLO_CONFIG_PLATFORM_SITEURL_DESCRIPTION'); ?>
					</div>
				</div>
			</div>

			<div class="form-group">
				<label for="var[akeeba.platform.newroot]" class="col-sm-3 control-label">
					<?php echo Text::_('SOLO_CONFIG_PLATFORM_NEWROOT_TITLE')?>
				</label>
				<div class="col-sm-9">
					<div class="input-group">
						<input type="text" class="form-control" id="var[akeeba.platform.newroot]"
							   name="var[akeeba.platform.newroot]" size="30"
							   value="<?php echo $this->siteInfo->root ?>">
						<span class="input-group-btn">
							<button title="<?php echo Text::_('COM_AKEEBA_CONFIG_UI_BROWSE')?>" class="btn btn-default" id="btnBrowse">
								<span class="glyphicon glyphicon-folder-open"></span>
							</button>
						</span>
					</div>
					<div class="help-block">
						<?php echo Text::_('SOLO_CONFIG_PLATFORM_NEWROOT_DESCRIPTION'); ?>
					</div>
				</div>
			</div>
		</div>

		<div class="form-group">
			<div class="col-sm-push-3 col-sm-9 col-xs-12">
				<button class="btn btn-success btn-lg" id="btnPythia" onclick="return false;">
					<span class="fa fa-magic"></span>
					<?php echo Text::_('SOLO_WIZARD_BTN_PYTHIA') ?>
				</button>
				<div class="help-block">
					<?php echo Text::_('SOLO_WIZARD_BTN_PYTHIA_HELP'); ?>
				</div>
			</div>
		</div>
	</div>

	<div class="panel panel-default">
		<div class="panel-heading">
			<h3 class="panel-title">
				<?php echo Text::_('SOLO_WIZARD_LBL_DBINFO_TITLE') ?>
			</h3>
		</div>
		<div class="panel-body">
			<p><?php echo Text::_('SOLO_WIZARD_LBL_DBINFO_INTRO');?></p>

			<div class="form-group">
				<label for="var[akeeba.platform.dbdriver]" class="col-sm-3 control-label">
					<?php echo Text::_('SOLO_CONFIG_PLATFORM_DBDRIVER_TITLE')?>
				</label>
				<div class="col-sm-9">
					<?php echo \Solo\Helper\Utils::engineDatabaseTypesSelect($config->get('akeeba.platform.dbdriver', 'mysqli'), 'var[akeeba.platform.dbdriver]'); ?>
					<div class="help-block">
						<?php echo Text::_('SOLO_CONFIG_PLATFORM_DBDRIVER_DESCRIPTION'); ?>
					</div>
				</div>
			</div>

			<div class="form-group" id="host-wrapper">
				<label for="var[akeeba.platform.dbhost]" class="col-sm-3 control-label">
					<?php echo Text::_('SOLO_CONFIG_PLATFORM_DBHOST_TITLE')?>
				</label>
				<div class="col-sm-9">
					<input type="text" class="form-control" id="var[akeeba.platform.dbhost]"
						   name="var[akeeba.platform.dbhost]" size="30"
						   value="<?php echo $config->get('akeeba.platform.dbhost', 'localhost') ?>">
					<div class="help-block">
						<?php echo Text::_('SOLO_CONFIG_PLATFORM_DBHOST_DESCRIPTION'); ?>
					</div>
				</div>
			</div>

			<div class="form-group" id="port-wrapper">
				<label for="var[akeeba.platform.dbport]" class="col-sm-3 control-label">
					<?php echo Text::_('SOLO_CONFIG_PLATFORM_DBPORT_TITLE')?>
				</label>
				<div class="col-sm-9">
					<input type="text" class="form-control" id="var[akeeba.platform.dbport]"
						   name="var[akeeba.platform.dbport]" size="30"
						   value="<?php echo $config->get('akeeba.platform.dbport', '') ?>">
					<div class="help-block">
						<?php echo Text::_('SOLO_CONFIG_PLATFORM_DBPORT_DESCRIPTION'); ?>
					</div>
				</div>
			</div>

			<div class="form-group" id="user-wrapper">
				<label for="var[akeeba.platform.dbusername]" class="col-sm-3 control-label">
					<?php echo Text::_('SOLO_CONFIG_PLATFORM_DBUSERNAME_TITLE')?>
				</label>
				<div class="col-sm-9">
					<input type="text" class="form-control" id="var[akeeba.platform.dbusername]"
						   name="var[akeeba.platform.dbusername]" size="30"
						   value="<?php echo $config->get('akeeba.platform.dbusername', '') ?>">
					<div class="help-block">
						<?php echo Text::_('SOLO_CONFIG_PLATFORM_DBUSERNAME_DESCRIPTION'); ?>
					</div>
				</div>
			</div>

			<div class="form-group" id="pass-wrapper">
				<label for="var[akeeba.platform.dbpassword]" class="col-sm-3 control-label">
					<?php echo Text::_('SOLO_CONFIG_PLATFORM_DBPASSWORD_TITLE')?>
				</label>
				<div class="col-sm-9">
					<input type="password" class="form-control" id="var[akeeba.platform.dbpassword]"
						   name="var[akeeba.platform.dbpassword]" size="30"
						   value="<?php echo $config->get('akeeba.platform.dbpassword', '') ?>">
					<div class="help-block">
						<?php echo Text::_('SOLO_CONFIG_PLATFORM_DBPASSWORD_DESCRIPTION'); ?>
					</div>
				</div>
			</div>

			<div class="form-group" id="name-wrapper">
				<label for="var[akeeba.platform.dbname]" class="col-sm-3 control-label">
					<?php echo Text::_('SOLO_CONFIG_PLATFORM_DBDATABASE_TITLE')?>
				</label>
				<div class="col-sm-9">
					<input type="text" class="form-control" id="var[akeeba.platform.dbname]"
						   name="var[akeeba.platform.dbname]" size="30"
						   value="<?php echo $config->get('akeeba.platform.dbname', '') ?>">
					<div class="help-block">
						<?php echo Text::_('SOLO_CONFIG_PLATFORM_DBDATABASE_DESCRIPTION'); ?>
					</div>
				</div>
			</div>

			<div class="form-group" id="prefix-wrapper">
				<label for="var[akeeba.platform.dbprefix]" class="col-sm-3 control-label">
					<?php echo Text::_('SOLO_CONFIG_PLATFORM_DBPREFIX_TITLE')?>
				</label>
				<div class="col-sm-9">
					<input type="text" class="form-control" id="var[akeeba.platform.dbprefix]"
						   name="var[akeeba.platform.dbprefix]" size="30"
						   value="<?php echo $config->get('akeeba.platform.dbprefix', '') ?>">
					<div class="help-block">
						<?php echo Text::_('SOLO_CONFIG_PLATFORM_DBPREFIX_DESCRIPTION'); ?>
					</div>
				</div>
			</div>

		</div>
	</div>

	<div class="panel panel-default">
		<div class="panel-heading">
			<h3 class="panel-title">
				<?php echo Text::_('SOLO_WIZARD_LBL_SITEINFO_TITLE') ?>
			</h3>
		</div>
		<div class="panel-body">
			<p><?php echo Text::_('SOLO_WIZARD_LBL_SITEINFO_INTRO');?></p>

			<div class="form-group">
				<label for="var[akeeba.platform.scripttype]" class="col-sm-3 control-label">
					<?php echo Text::_('SOLO_CONFIG_PLATFORM_SCRIPTTYPE_TITLE')?>
				</label>
				<div class="col-sm-9">
					<?php echo \Solo\Helper\Setup::scriptTypesSelect($config->get('akeeba.platform.scripttype', 'generic'), 'var[akeeba.platform.scripttype]'); ?>
					<div class="help-block">
						<?php echo Text::_('SOLO_CONFIG_PLATFORM_SCRIPTTYPE_DESCRIPTION'); ?>
					</div>
				</div>
			</div>

			<div class="form-group">
				<label for="extradirs" class="col-sm-3 control-label">
					<?php echo Text::_('SOLO_CONFIG_PLATFORM_EXTRADIRS_TITLE')?>
				</label>
				<div class="col-sm-9">
					<span id="pythiaExtradirs">&nbsp;</span>
					<div class="help-block">
						<?php echo Text::_('SOLO_CONFIG_PLATFORM_EXTRADIRS_DESCRIPTION'); ?>
					</div>
				</div>
			</div>

            <div class="form-group">
                <label for="extradirs" class="col-sm-3 control-label">
                    <?php echo Text::_('SOLO_CONFIG_PLATFORM_EXTRADB_TITLE')?>
                </label>
                <div class="col-sm-9">
                    <span id="pythiaExtradb">&nbsp;</span>
                    <div class="help-block">
                        <?php echo Text::_('SOLO_CONFIG_PLATFORM_EXTRADB_DESCRIPTION'); ?>
                    </div>
                </div>
            </div>

			<div class="form-group">
				<label for="var[akeeba.advanced.embedded_installer]" class="col-sm-3 control-label">
					<?php echo Text::_('COM_AKEEBA_CONFIG_INSTALLER_TITLE')?>
				</label>
				<div class="col-sm-9">
					<?php echo \Solo\Helper\Setup::restorationScriptSelect($config->get('akeeba.advanced.embedded_installer', 'generic'), 'var[akeeba.advanced.embedded_installer]'); ?>
					<div class="help-block">
						<?php echo Text::_('COM_AKEEBA_CONFIG_INSTALLER_DESCRIPTION'); ?>
					</div>
				</div>
			</div>
		</div>
	</div>

	<button id="btnWizardSiteConfigSubmit" type="submit" class="btn btn-primary btn-lg">
		<?php echo Text::_('SOLO_BTN_SUBMIT') ?>
	</button>

</form>