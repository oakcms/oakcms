<?php
if (!isset($exc))
{
	die();
}
switch ($exc->getCode())
{
	case 400:
		header('HTTP/1.1 400 Bad Request');
		$appError = 'Bad Request';
		break;
	case 401:
		header('HTTP/1.1 401 Unauthorized');
		$appError = 'Unauthorised';
		break;
	case 403:
		header('HTTP/1.1 403 Forbidden');
		$appError = 'Access Denied';
		break;
	case 404:
		header('HTTP/1.1 404 Not Found');
		$appError = 'Not Found';
		break;
	case 501:
		header('HTTP/1.1 501 Not Implemented');
		$appError = 'Not Implemented';
		break;
	case 503:
		header('HTTP/1.1 503 Service Unavailable');
		$appError = 'Service Unavailable';
		break;
	case 500:
	default:
		header('HTTP/1.1 500 Internal Server Error');
		$appError = 'Application Error';
		break;
}
?>
<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">

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

	<title><?php echo \Awf\Text\Text::_('SOLO_APP_TITLE_ERROR') ?></title>

	<script type="text/javascript" src="<?php echo \Awf\Uri\Uri::base(); ?>media/js/jquery.min.js"></script>
	<script type="text/javascript" src="<?php echo \Awf\Uri\Uri::base(); ?>media/js/jquery-migrate.min.js"></script>
	<script type="text/javascript" src="<?php echo \Awf\Uri\Uri::base(); ?>media/js/bootstrap.min.js"></script>
	<link rel="stylesheet" type="text/css" href="<?php echo \Awf\Uri\Uri::base(); ?>/media/css/bootstrap.min.css" />
	<link rel="stylesheet" type="text/css" href="<?php echo \Awf\Uri\Uri::base(); ?>/media/css/bootstrap-theme.min.css" />
	<?php if (defined('AKEEBADEBUG') && AKEEBADEBUG && @file_exists(APATH_BASE . '/media/css/theme.css')): ?>
	<link rel="stylesheet" type="text/css" href="<?php echo \Awf\Uri\Uri::base(); ?>/media/css/theme.css" />
	<?php else: ?>
	<link rel="stylesheet" type="text/css" href="<?php echo \Awf\Uri\Uri::base(); ?>/media/css/theme.min.css" />
	<?php endif; ?>

	<!-- HTML5 shim and Respond.js IE8 support of HTML5 elements and media queries -->
	<!--[if lt IE 9]>
	<script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
	<script src="https://oss.maxcdn.com/libs/respond.js/1.3.0/respond.min.js"></script>
	<![endif]-->

	<script type="text/javascript">
		if (typeof Solo == 'undefined') { var Solo = {}; }
		if (typeof Solo.loadScripts == 'undefined') { Solo.loadScripts = []; }
	</script>
</head>
<body>
<div id="error-wrap">
	<div id="container">
		<div class="col-sm-2 hidden-xs"></div>
		<div class="col-sm-8 col-xs-12">
			<h2><span class="label label-danger"><?php echo $exc->getCode() ?></span> <?php echo $appError ?></h2>
			<?php if (defined('AKEEBADEBUG')): ?>
			<p>&nbsp;</p>
			<p>
				Please submit the following error message and trace in its entirety when requesting support
			</p>
			<h4 class="text-info">
				<?php echo $exc->getCode() . ' :: ' . $exc->getMessage(); ?>
				in
				<?php echo $exc->getFile() ?>
				<span class="label label-info">L <?php echo $exc->getLine(); ?></span>
			</h4>
			<p>Debug backtrace</p>
			<pre class="bg-info"><?php echo $exc->getTraceAsString(); ?></pre>
			<?php else: ?>
			<p>
				<?php echo $exc->getMessage(); ?>
			</p>
			<?php endif; ?>
		</div>
		<div class="col-sm-2 hidden-xs"></div>
	</div>
</div>
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