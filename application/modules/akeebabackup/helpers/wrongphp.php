<?php
/**
 * @package		akeebabackupwp
 * @copyright	2014-2017 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license		GNU GPL version 3 or later
 */
?>

<div style="margin: 1em">
	<h1>Outdated PHP version <?php echo PHP_VERSION ?> detected</h1>
	<hr/>
	<p style="font-size: 180%; margin: 2em 1em;">
		Akeeba Backup for WordPress requires PHP <?php echo AkeebaBackupWP::$minimumPHP ?> or any later version to work.
	</p>
	<p>
		We <b>strongly</b> urge you to update to PHP 5.5 or later. If unsure how to do this, please ask your host.
	</p>
	<p>
		<a href="https://www.akeebabackup.com/how-do-version-numbers-work.html">Version numbers don't make sense?</a>
	</p>

	<hr/>

	<h3>Security advice</h3>
	<p>
		Your version of PHP, <?php echo PHP_VERSION ?>, <a href="http://php.net/eol.php">has reached the end
		of its life</a>. You are strongly urged to upgrade to a current version, as using older versions may expose you
		to security vulnerabilities and bugs that have been fixed in more recent versions of PHP.
	</p>
</div>