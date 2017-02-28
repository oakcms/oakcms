<?php
/**
 * @package		akeebabackupwp
 * @copyright	2014-2017 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license		GNU GPL version 3 or later
 */
use Awf\Document\Document;
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

AkeebaBackupWP::enqueueScript(Uri::base() . 'media/js/akjqnamespace.min.js');

// Scripts before the template ones
if(!empty($scripts)) foreach($scripts as $url => $params)
{
	if($params['before'])
	{
		AkeebaBackupWP::enqueueScript($url);
	}
}

$wpVersion = get_bloginfo('version', 'raw');

if (version_compare($wpVersion, '4.0', 'lt'))
{
	// Template scripts
//	AkeebaBackupWP::enqueueScript(content_url() . '/js/jquery/jquery.js');
	AkeebaBackupWP::enqueueScript(Uri::base() . 'media/js/akjqnamespace.min.js');
	AkeebaBackupWP::enqueueScript(content_url() . '/js/jquery/jquery-migrate.js');
	AkeebaBackupWP::enqueueScript(Uri::base() . 'media/js/bootstrap.min.js');
}
else
{
//	AkeebaBackupWP::enqueueScript(includes_url() . '/js/jquery/jquery.js');
	AkeebaBackupWP::enqueueScript(Uri::base() . 'media/js/akjqnamespace.min.js');
	AkeebaBackupWP::enqueueScript(includes_url() . '/js/jquery/jquery-migrate.js');
	AkeebaBackupWP::enqueueScript(Uri::base() . 'media/js/bootstrap.min.js');
}

// Scripts after the template ones
if(!empty($scripts)) foreach($scripts as $url => $params)
{
	if(!$params['before'])
	{
		AkeebaBackupWP::enqueueScript($url);
	}
}

// onLoad scripts
AkeebaBackupWP::enqueueScript(Uri::base() . 'media/js/bootstrap.min.js');
AkeebaBackupWP::enqueueScript(Uri::base() . 'media/js/bootstrap-switch.min.js');
AkeebaBackupWP::enqueueScript(Uri::base() . 'media/js/solo/loadscripts.min.js');

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
			AkeebaBackupWP::enqueueStyle($url);
		}
	}
}

AkeebaBackupWP::enqueueStyle(Uri::base() . 'media/css/bootstrap-namespaced.min.css');
AkeebaBackupWP::enqueueStyle(Uri::base() . 'media/css/bootstrap-wordpress.min.css');
AkeebaBackupWP::enqueueStyle(Uri::base() . 'media/css/font-awesome.min.css');

if (defined('AKEEBADEBUG') && AKEEBADEBUG && @file_exists(dirname(AkeebaBackupWP::$absoluteFileName) . '/app/media/css/theme.css'))
{
	AkeebaBackupWP::enqueueStyle(Uri::base() . 'media/css/theme.css');
}
else
{
	AkeebaBackupWP::enqueueStyle(Uri::base() . 'media/css/theme.min.css');
}

// CSS files before the template CSS
if (!empty($styles))
{
	foreach ($styles as $url => $params)
	{
		if (!$params['before'])
		{
			AkeebaBackupWP::enqueueStyle($url);
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