<?php

/**
 * @package    oakcms
 * @author     Hryvinskyi Volodymyr <script@email.ua>
 * @copyright  Copyright (c) 2015 - 2017. Hryvinskyi Volodymyr
 * @version    0.0.1-beta.0.1
 */
use Awf\Document\Document;
use app\modules\akeebabackup\helpers\AkeebaBackupYii;
use Awf\Uri\Uri;

?>
<script type="text/javascript">
if (typeof Solo == 'undefined') { var Solo = {}; }
if (typeof Solo.loadScripts == 'undefined') { Solo.loadScripts = []; }
</script>
<?php

$scripts = $this->getScripts();
$scriptDeclarations = $this->getScriptDeclarations();
$styles = $this->getStyles();
$styleDeclarations = $this->getStyleDeclarations();

AkeebaBackupYii::enqueueScript(Uri::base() . 'media/js/akjqnamespace.min.js');

// Scripts before the template ones
if(!empty($scripts)) foreach($scripts as $url => $params)
{
	if($params['before'])
	{
		AkeebaBackupYii::enqueueScript($url);
	}
}

$wpVersion = '4.0';

if (version_compare($wpVersion, '4.0', 'lt'))
{
	// Template scripts
//	AkeebaBackupYii::enqueueScript(content_url() . '/js/jquery/jquery.js');
	AkeebaBackupYii::enqueueScript(Uri::base() . 'media/js/akjqnamespace.min.js');
	AkeebaBackupYii::enqueueScript(content_url() . '/js/jquery/jquery-migrate.js');
	AkeebaBackupYii::enqueueScript(Uri::base() . 'media/js/bootstrap.min.js');
}
else
{
//	AkeebaBackupYii::enqueueScript(includes_url() . '/js/jquery/jquery.js');
	AkeebaBackupYii::enqueueScript(Uri::base() . 'media/js/akjqnamespace.min.js');
	AkeebaBackupYii::enqueueScript(Uri::base() . '/js/jquery/jquery-migrate.js');
	AkeebaBackupYii::enqueueScript(Uri::base() . 'media/js/bootstrap.min.js');
}

// Scripts after the template ones
if(!empty($scripts)) foreach($scripts as $url => $params)
{
	if(!$params['before'])
	{
		AkeebaBackupYii::enqueueScript($url);
	}
}

// onLoad scripts
AkeebaBackupYii::enqueueScript(Uri::base() . 'media/js/bootstrap.min.js');
AkeebaBackupYii::enqueueScript(Uri::base() . 'media/js/bootstrap-switch.min.js');
AkeebaBackupYii::enqueueScript(Uri::base() . 'media/js/solo/loadscripts.min.js');

// Script declarations
if (!empty($scriptDeclarations))
{
	foreach ($scriptDeclarations as $type => $content)
	{
		echo <<< HTML
<script type="$type">
Solo.loadScripts[Solo.loadScripts.length] = function () {
	$content
}
</script>
HTML;
	}
}

// CSS files before the template CSS
if (!empty($styles))
{
	foreach ($styles as $url => $params)
	{
		if ($params['before'])
		{
			AkeebaBackupYii::enqueueStyle($url);
		}
	}
}

AkeebaBackupYii::enqueueStyle(Uri::base() . 'media/css/bootstrap-namespaced.min.css');
AkeebaBackupYii::enqueueStyle(Uri::base() . 'media/css/bootstrap-wordpress.min.css');
AkeebaBackupYii::enqueueStyle(Uri::base() . 'media/css/font-awesome.min.css');

if (defined('AKEEBADEBUG') && AKEEBADEBUG && @file_exists(dirname(AkeebaBackupYii::$absoluteFileName) . '/app/media/css/theme.css'))
{
	AkeebaBackupYii::enqueueStyle(Uri::base() . 'media/css/theme.css');
}
else
{
	AkeebaBackupYii::enqueueStyle(Uri::base() . 'media/css/theme.min.css');
}

// CSS files before the template CSS
if (!empty($styles))
{
	foreach ($styles as $url => $params)
	{
		if (!$params['before'])
		{
			AkeebaBackupYii::enqueueStyle($url);
		}
	}
}

// Script declarations
if (!empty($styleDeclarations))
{
	foreach ($styleDeclarations as $type => $content)
	{
		echo "\t<style type=\"$type\">\n$content\n</style>";
	}
}
