<?php
/**
 * @package        solo
 * @copyright      2014-2017 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license        GNU GPL version 3 or later
 */

use Awf\Text\Text;
use Awf\Uri\Uri;

/** @var \Solo\View\Browser\Html $this */

$rootDirWarning = \Solo\Helper\Escape::escapeJS(Text::_('COM_AKEEBA_CONFIG_UI_ROOTDIR'));

$this->container->application->getDocument()->addScriptDeclaration(
	<<<JS
	function akeeba_browser_useThis()
	{
		var rawFolder = document.forms.adminForm.folderraw.value;
		if( rawFolder == '[SITEROOT]' )
		{
			alert('$rootDirWarning');
			rawFolder = '[SITETMP]';
		}
		window.parent.Solo.Configuration.onBrowserCallback( rawFolder );
	}

JS

);

$router = $this->container->router;
?>

<?php if (empty($this->folder)): ?>
	<form action="<?php echo $router->route('index.php?view=browser&tmpl=component&processfolder=0') ?>" method="post"
		  name="adminForm" id="adminForm">
		<input type="hidden" name="folder" id="folder" value=""/>
		<input type="hidden" name="token"
			   value="<?php echo $this->container->session->getCsrfToken()->getValue() ?>"/>
	</form>
<?php else: ?>

	<form action="<?php echo $router->route('index.php?view=browser&tmpl=component') ?>" method="post"
		  name="adminForm" id="adminForm">
		<input type="hidden" name="token"
			   value="<?php echo $this->container->session->getCsrfToken()->getValue() ?>"/>
		<input type="hidden" name="folderraw" id="folderraw" value="<?php echo $this->folder_raw ?>"/>
		<input type="hidden" name="token" value="<?php echo $this->container->session->getCsrfToken()->getValue() ?>"/>

		<div class="col-xs-1">
			<span class="badge"
				  alt="<?php echo $this->writable ? Text::_('COM_AKEEBA_CPANEL_LBL_WRITABLE') : Text::_('COM_AKEEBA_CPANEL_LBL_UNWRITABLE'); ?>"
				  title="<?php echo $this->writable ? Text::_('COM_AKEEBA_CPANEL_LBL_WRITABLE') : Text::_('COM_AKEEBA_CPANEL_LBL_UNWRITABLE'); ?>"
				>
				<span class="fa fa-<?php echo $this->writable ? 'check-circle' : 'ban' ?>"></span>
			</span>
		</div>

		<div class="col-xs-7">
			<input class="form-control" type="text" name="folder" id="folder" size="40"
				   value="<?php echo $this->folder; ?>"/>
		</div>

		<div class="col-xs-4">
			<button class="btn btn-primary" onclick="this.form.submit(); return false;">
				<span class="fa fa-folder-open"></span>
				<?php echo Text::_('COM_AKEEBA_BROWSER_LBL_GO'); ?>
			</button>
			<button class="btn btn-success" onclick="akeeba_browser_useThis(); return false;">
				<span class="fa fa-check-square-o"></span>
				<?php echo Text::_('COM_AKEEBA_BROWSER_LBL_USE'); ?>
			</button>
		</div>
	</form>

	<?php if (count($this->breadcrumbs) > 0): ?>
	<ol class="breadcrumb">
		<?php $i = 0 ?>
		<?php foreach ($this->breadcrumbs as $crumb):
			$link = $router->route("index.php?view=browser&tmpl=component&folder=" . urlencode($crumb['folder']));
			$label = htmlentities($crumb['label']);
			$i++;
			$bull = $i < count($this->breadcrumbs) ? '&bull;' : '';
			?>
			<li class="<?php echo $bull ? '' : 'active' ?>">
				<?php if ($bull): ?>
					<a href="<?php echo $link ?>">
						<?php echo $label ?>
					</a>
				<?php else: ?>
					<?php echo $label ?>
				<?php endif; ?>
			</li>
		<?php endforeach; ?>
	</ol>
	<?php endif; ?>

	<div class="row-fluid">
		<div class="span12">
			<?php if (count($this->subfolders) > 0): ?>
				<table class="table table-striped">
					<tr>
						<td>
							<?php $linkbase = $router->route("index.php?&view=browser&tmpl=component&folder="); ?>
							<a class="btn btn-sm btn-default"
							   href="<?php echo $linkbase . urlencode($this->parent); ?>">
								<span class="fa fa-arrow-up"></span>
								<?php echo Text::_('COM_AKEEBA_BROWSER_LBL_GOPARENT') ?>
							</a>
						</td>
					</tr>
					<?php foreach ($this->subfolders as $subfolder): ?>
						<tr>
							<td>
								<a href="<?php echo $linkbase . urlencode($this->folder . '/' . $subfolder); ?>"><?php echo htmlentities($subfolder) ?></a>
							</td>
						</tr>
					<?php endforeach; ?>
				</table>
			<?php else: ?>
				<?php if (!$this->exists): ?>
					<div class="alert alert-danger">
						<?php echo Text::_('COM_AKEEBA_BROWSER_ERR_NOTEXISTS'); ?>
					</div>
				<?php elseif (!$this->inRoot): ?>
					<div class="alert alert-warning">
						<?php echo Text::_('COM_AKEEBA_BROWSER_ERR_NONROOT'); ?>
					</div>
				<?php
				elseif ($this->openbasedirRestricted): ?>
					<div class="alert alert-danger">
						<?php echo Text::_('COM_AKEEBA_BROWSER_ERR_BASEDIR'); ?>
					</div>
				<?php
				else: ?>
					<table class="table table-striped">
						<tr>
							<td>
								<?php $linkbase = $router->route("index.php?&view=browser&tmpl=component&folder="); ?>
								<a class="btn btn-sm btn-default"
								   href="<?php echo $linkbase . urlencode($this->parent); ?>">
									<span class="fa fa-arrow-up"></span>
									<?php echo Text::_('COM_AKEEBA_BROWSER_LBL_GOPARENT') ?>
								</a>
							</td>
						</tr>
					</table>
				<?php endif; ?>
			<?php endif; ?>
		</div>
	</div>
<?php endif; ?>