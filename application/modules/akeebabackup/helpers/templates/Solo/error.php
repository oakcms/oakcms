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

include __DIR__ . '/php/head.php';
?>
<div class="akeeba-bootstrap">
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
</div>
</body>
</html>