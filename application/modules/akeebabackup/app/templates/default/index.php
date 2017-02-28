<?php
/** @var \Awf\Document\Document $this */
?>
<?php include __DIR__ . '/php/menu.php';
$this->outputHTTPHeaders();
?>
<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">

	<?php // Favicons size reference: https://github.com/audreyr/favicon-cheat-sheet ?>
	<link rel="shortcut icon" href="<?php echo \Awf\Uri\Uri::base() ?>media/logo/favicon.ico">
	<link rel="apple-touch-icon-precomposed" href="<?php echo \Awf\Uri\Uri::base() ?>media/logo/solo-152.png">
	<meta name="msapplication-TileColor" content="#FFFFFF">
	<meta name="msapplication-TileImage" content="<?php echo \Awf\Uri\Uri::base() ?>media/logo/solo-144.png">
	<link rel="apple-touch-icon-precomposed" sizes="152x152" href="<?php echo \Awf\Uri\Uri::base() ?>media/logo/solo-152.png">
	<link rel="apple-touch-icon-precomposed" sizes="144x144" href="<?php echo \Awf\Uri\Uri::base() ?>media/logo/solo-144.png">
	<link rel="apple-touch-icon-precomposed" sizes="120x120" href="<?php echo \Awf\Uri\Uri::base() ?>media/logo/solo-120.png">
	<link rel="apple-touch-icon-precomposed" sizes="114x114" href="<?php echo \Awf\Uri\Uri::base() ?>media/logo/solo-114.png">
	<link rel="apple-touch-icon-precomposed" sizes="72x72" href="<?php echo \Awf\Uri\Uri::base() ?>media/logo/solo-72.png">
	<link rel="apple-touch-icon-precomposed" href="<?php echo \Awf\Uri\Uri::base() ?>media/logo/solo-57.png">
	<link rel="icon" href="<?php echo \Awf\Uri\Uri::base() ?>media/logo/solo-32.png" sizes="32x32">

	<title><?php echo \Awf\Text\Text::_('SOLO_APP_TITLE') ?></title>

	<script type="text/javascript">
		if (typeof Solo == 'undefined') { var Solo = {}; }
		if (typeof Solo.loadScripts == 'undefined') { Solo.loadScripts = []; }
	</script>

<?php include __DIR__ . '/php/head.php' ?>

	<!-- HTML5 shim and Respond.js IE8 support of HTML5 elements and media queries -->
	<!--[if lt IE 9]>
	<script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js?<?php echo $this->container->mediaQueryKey ?>"></script>
	<script src="https://oss.maxcdn.com/libs/respond.js/1.3.0/respond.min.js?<?php echo $this->container->mediaQueryKey ?>"></script>
	<![endif]-->
</head>
<body>
<?php if (\Awf\Application\Application::getInstance()->getContainer()->input->getCmd('tmpl', '') != 'component'): ?>
	<div class="navbar navbar-default navbar-static-top" role="navigation">
		<div class="container">
			<div class="navbar-header">
				<button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
					<span class="sr-only"><?php echo \Awf\Text\Text::_('SOLO_COMMON_TOGGLENAV') ?></span>
					<span class="icon-bar"></span>
					<span class="icon-bar"></span>
					<span class="icon-bar"></span>
				</button>
				<a class="navbar-brand navbar-brand-solo" href="<?php echo \Awf\Uri\Uri::base() ?>">
					<img src="<?php echo \Awf\Uri\Uri::base() ?>media/logo/solo-256.png" class="img-responsive">
					<span>
						<?php echo \Awf\Text\Text::_('SOLO_APP_TITLE') ?>
						<small class="text-<?php echo AKEEBABACKUP_PRO ? 'danger' : 'muted' ?>"><?php echo AKEEBABACKUP_PRO ? 'Pro' : 'Core' ?></small>
						<?php if ((substr(AKEEBABACKUP_VERSION, 0, 3) == 'rev') || (strpos(AKEEBABACKUP_VERSION, '.a') !== false)): ?>
						<sup><small><span class="label label-danger">ALPHA</span></small></sup>
						<?php elseif (strpos(AKEEBABACKUP_VERSION, '.b') !== false): ?>
						<sup><small><span class="label label-primary">BETA</span></small></sup>
						<?php elseif (strpos(AKEEBABACKUP_VERSION, '.rc') !== false): ?>
						<sup><small><span class="label label-default">RC</span></small></sup>
						<?php endif; ?>
						<?php if ($title = $this->getToolbar()->getTitle()):?>
							<small>• <?php echo \Awf\Text\Text::_($title) ?></small>
						<?php endif; ?>
					</span>
				</a>
			</div>
			<div class="navbar-collapse collapse">
				<?php if (\Awf\Application\Application::getInstance()->getContainer()->userManager->getUser()->getId()): ?>
				<form class="navbar-form navbar-right" role="logout">
					<a href="<?php echo $this->getContainer()->router->route('index.php?view=login&task=logout') ?>" class="btn btn-sm btn-default hasTooltip" title="<?php echo \Awf\Text\Text::sprintf('SOLO_LOGIN_LBL_LOGOUT', \Awf\Application\Application::getInstance()->getContainer()->userManager->getUser()->getUsername()) ?>" data-toggle="tooltip" data-placement="bottom">
						<span class="glyphicon glyphicon-log-out"></span>
						<span class="hidden-lg hidden-md hidden-sm"><?php echo \Awf\Text\Text::sprintf('SOLO_LOGIN_LBL_LOGOUT', \Awf\Application\Application::getInstance()->getContainer()->userManager->getUser()->getUsername()) ?></span>
					</a>
				</form>
				<?php endif; ?>

				<ul class="nav navbar-right">
					<?php _solo_template_renderSubmenu($this, $this->getMenu()->getMenuItems('main'), 'nav navbar-nav'); ?>
				</ul>
			</div>
		</div>
	</div>

	<div id="wrap">
		<div class="container">
			<?php include __DIR__ . '/php/toolbar.php' ?>
		</div>
		<div class="container">
<?php endif; ?>
			<?php include __DIR__ . '/php/messages.php' ?>
			<?php echo $this->getBuffer() ?>
<?php if (\Awf\Application\Application::getInstance()->getContainer()->input->getCmd('tmpl', '') != 'component'): ?>
		</div>
	</div>
	<div id="footer">
		<div class="container">
			<p class="muted credit">
				Copyright &copy;2013 &ndash; <?php echo date('Y') ?> Akeeba Ltd. All rights reserved.<br/>
				Akeeba Solo is Free Software distributed under the
				<a href="http://www.gnu.org/licenses/gpl.html">GNU GPL version 3</a> or any later version published by the FSF.
				<?php if (defined('AKEEBADEBUG')): ?>
				<br>
				<small>
					Page creation <?php echo sprintf('%0.3f', \Awf\Application\Application::getInstance()->getTimeElapsed()) ?> sec
					&bull;
					Peak memory usage <?php echo sprintf('%0.1f', memory_get_peak_usage() / 1048576) ?> Mb
				</small>
				<?php endif; ?>
			</p>
		</div>
	</div>
<?php endif; ?>
<script type="text/javascript">
	Solo.System.documentReady(function(){
		for (i = 0; i < Solo.loadScripts.length; i++)
		{
			Solo.loadScripts[i]();
		}
	});
</script>
</body>
</html>