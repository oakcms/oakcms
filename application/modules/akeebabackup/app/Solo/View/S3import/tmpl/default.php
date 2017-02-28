<?php
/**
 * @package        solo
 * @copyright      2014-2017 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license        GNU GPL version 3 or later
 */

use Awf\Text\Text;
use Solo\Helper\Escape;

/** @var \Solo\View\S3import\Html $this */

$router = $this->container->router;

?>
<form name="adminForm" id="adminForm" action="<?php echo $router->route('index.php?view=s3import') ?>" method="POST" role="form">
	<input type="hidden" id="ak_s3import_folder" name="folder" value="<?php echo $this->root ?>" />

	<div class="col-xs-12 form-inline well">
		<input type="text" size="40" name="s3access" id="s3access" class="form-control"
			   value="<?php echo $this->s3access ?>"
			   placeholder="<?php echo Text::_('COM_AKEEBA_CONFIG_S3ACCESSKEY_TITLE') ?>" />
		<input type="password" size="40" name="s3secret" id="s3secret" class="form-control"
			   value="<?php echo $this->s3secret ?>"
			   placeholder="<?php echo Text::_('COM_AKEEBA_CONFIG_S3SECRETKEY_TITLE') ?>" />
		<?php if(empty($this->buckets)): ?>
		<button class="btn btn-primary" type="submit" onclick="this.form.submit()">
			<span class="glyphicon glyphicon-cloud"></span>
			<?php echo Text::_('COM_AKEEBA_S3IMPORT_LABEL_CONNECT') ?>
		</button>
		<?php else: ?>
		<?php echo $this->bucketSelect ?>
		<button class="btn btn-primary" type="submit" onclick="this.form.submit()">
			<span class="glyphicon glyphicon-folder-open"></span>
			<?php echo Text::_('COM_AKEEBA_S3IMPORT_LABEL_CHANGEBUCKET') ?>
		</button>
		<?php endif;?>
	</div>

	<div class="col-xs-12">
		<div id="ak_crumbs_container">
			<ol class="breadcrumb">
				<li>
					<a href="<?php echo $router->route('index.php?view=s3import&task=main&folder=') ?>">
						<?php echo Text::_('SOLO_COMMON_LBL_ROOT'); ?>
					</a>
				</li>
				
				<?php $runningCrumb = '';?>
				<?php if(!empty($this->crumbs)) foreach($this->crumbs as $crumb):?>
				<?php $runningCrumb .= $crumb.'/'; ?>
				<li>
					<a href="<?php echo $router->route('index.php?view=s3import&task=main&folder=' . $runningCrumb) ?>">
						<?php echo $crumb ?>
					</a>
				</li>
				<?php endforeach; ?>
			</ol>
		</div>
	</div>
	
	<div>
		<fieldset id="ak_folder_container" class="col-sm-6 col-xs-12">
			<legend><?php echo Text::_('COM_AKEEBA_FILEFILTERS_LABEL_DIRS'); ?></legend>
			<div id="folders">
				<?php if(!empty($this->contents['folders'])) foreach($this->contents['folders'] as $name => $record): ?>
				<div>
					<a class="btn btn-link" href="<?php echo $router->route('index.php?view=s3import&task=main&folder=' . $record['prefix']) ?>">
						<span class="fa fa-folder-o"></span>
						<?php echo basename($name); ?>
					</a>
				</div>
				<?php endforeach; ?>
			</div>
		</fieldset>

		<fieldset id="ak_files_container" class="col-sm-6 col-xs-12">
			<legend><?php echo Text::_('COM_AKEEBA_FILEFILTERS_LABEL_FILES'); ?></legend>
			<div id="files">
				<?php if(!empty($this->contents['files'])) foreach($this->contents['files'] as $name => $record): ?>
				<div>
					<a class="btn btn-link" href="<?php echo $router->route('index.php?view=s3import&task=downloadToServer&part=-1&frag=-1&layout=downloading&file=' . $name) ?>">
						<span class="fa fa-file-o"></span>
						<?php echo basename($record['name']); ?>
					</a>
				</div>
				<?php endforeach; ?>
			</div>
		</fieldset>
	</div>
</form>