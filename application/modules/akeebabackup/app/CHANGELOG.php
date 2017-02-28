<?php die();?>
Akeeba Solo 2.1.0.b1
================================================================================
+ Add support for Canada (Montreal) Amazon S3 region
+ Add support for EU (London) Amazon S3 region
+ Support for JPS format v2.0 with improved password security
+ Hide action icons based on the user's permissions
~ Permissions are now more reasonably assigned to different views
~ Now using the Reverse Engineering database dump engine when a Native database dump engine is not available (PostgreSQL, Microsoft SQL Server, SQLite)
# [MEDIUM] Infinite recursion if the current profile doesn't exist
# [MEDIUM] Views defined against fully qualified tables (i.e. including the database name) could not be restored on a database with a different name

Akeeba Solo 2.0.4
================================================================================
+ Alternative FTP post-processing engine and DirectFTP engine using cURL providing better compatibility with misconfigured and broken FTP servers
+ Alternative SFTP post-processing engine and DirectSFTP engine using cURL providing compatibility with a wide range of servers
~ Anticipate and report database errors in more places while backing up MySQL databases
# [HIGH] Site Transfer Wizard does not work with single part backup archives
# [HIGH] Running the Configuration Wizard would result in the wrong database driver being displayed in the Configuration page
# [HIGH] Trailing slashes in the Directory name would cause donwloading a backup back from Dropbox to fail
# [HIGH] Outdated, end-of-life PHP 5.4.4 in old Debian distributions has a MAJOR bug resulting in file data not being backed up (zero length files). We've rewritten our code to work around the bug in this OLD, OUTDATED, END-OF-LIFE AND INSECURE version of PHP. PLEASE UPGRADE YOUR SERVERS. OLD PHP VERSIONS ARE **DANGEROUS**!!!
# [MEDIUM] Dumping VIEW definers in newer MySQL versions can cause restoration issues when restoring to a new host
# [MEDIUM] Dropbox: error while fetching the archive back from the server
# [MEDIUM] Error restoring procedures, functions or triggers originally defined with inline MySQL comments
# [LOW] Folders not added to archive when both their subdirectories and all their files are filtered.
# [LOW] The Import button was shown in the Manage Backups page of the Core version where said feature doesn't exist
# [LOW] The database name could be included in PROCEDUREs, FUNCTIONs and TRIGGERs, making it impossible to restore the backup on a new server

Akeeba Solo 2.0.3
================================================================================
+ JSON API: export and import a profile's configuration
# [HIGH] Joomla restoration: changes in Joomla 3.6.3 and 3.6.4 regarding Two Factor Authentication setup handling could lead to disabling TFA when restoring a site
# [HIGH] Setting up Google Drive required manually copying the tokens due to a Javascript error
# [HIGH] Site Transfer Wizard fails on sites with too much memory or very fast connection speeds to the target site
# [HIGH] Site Transfer Wizard is missing a file required to transfer very large files to the remote server
# [MEDIUM] In several instances there was a typo declaring 1Mb = 1048756 bytes instead of the correct 1048576 bytes (1024 tiems 1024). This threw off some size calculations which, in extreme cases, could lead to backup failure.
# [MEDIUM] Obsolete records quota was applied to all backup records, not just the ones from the currently active backup profile
# [MEDIUM] Obsolete records quota did not delete the associated log file when removing an obsolete backup record
# [MEDIUM] Fixed typo in Site Transfer Wizard page, preventing the actual transfer
# [MEDIUM] Infinite loop creating part size in rare cases where the space left in the part is one byte or less
# [LOW] The changelog was not displayed when clicking on the "Changelog" button
# [LOW] ANGIE: Restoring WordPress 4.2+ from MySQL 5.6 to MySQL 5.5 or earlier fails due to WP using the utf8mb4_unicode_520_ci collation which is not available on older MySQL versions

Akeeba Solo 2.0.2
================================================================================
+ ANGIE: Prevent direct web access to the installation/sql directory
~ PHP 5.6.3 (and possibly other old 5.6 versions) are buggy. We rearranged the order of some code to work around these PHP bugs.

Akeeba Solo 2.0.1
================================================================================
! The ZIP archiver was not working properly

Akeeba Solo 2.0.0
================================================================================
! mcrypt is deprecated in PHP 7.1. Replacing it with OpenSSL.
! Missing files from the backup on some servers, especially in CLI mode
- Removing obsolete Mautic integration
+ ALICE raw output now is always in English
+ Added warning if database updates are stuck due to table corruption
+ Support the newer Microsoft Azure API version 2015-04-05
+ Support uploading files larger than 64Mb to Microsoft Azure
+ Added warning if CloudFlare Rocket Loader is enabled on the site
+ Added option to force Site Transfer even if some sanity checks are failing
+ You can now choose whether to display GMT or local time in the Manage Backups page
+ Sort the log files from newest to oldest in the View Log page (based on the backup ID)
+ View Log after successful backup now takes you to this backup's log file, not the generic View Log page
~ Improve detection of preferred MySQL driver in the Configuration Wizard
# [MEDIUM] Cannot download archives from S3 to browser when using the Amazon S3 v4 API
# [LOW] Reverse Engineering database dump engine must be available in Core version, required for backing up PostgreSQL and MS SQL Server
# [LOW] Wordpress restoration: Better handling of edge cases in wp-config file
# [LOW] Archive integrity check refused to run when you are using the "No post-processing" option but the "Process each part immediately" option was previously selected in a different post-processing engine
# [LOW] Total archive size would be doubled when you are using the "Process each part immediately" option but not the "Delete archive after processing" option (or when the post-processing engine is "No post-processing")
# [LOW] Warnings from the database dump engine were not propagated to the interface

Akeeba Solo 1.9.3
================================================================================
+ Automatically handle unsupported database storage engines when restoring MySQL databases
# [HIGH] Failure to upload to newly created Amazon S3 buckets
# [MEDIUM] Import from S3 didn't work with API v4-only regions (Frankfurt, São Paulo)
# [LOW] The [WEEKDAY] variable in archive name templates returned the weekday number (e.g 1) instead of text (e.g. Sunday)
# [LOW] Deleting the currently active profile would cause a white page / internal server error
# [LOW] Chrome and other isbehaving browsers autofill the database username/password, leading to restoration failure if you're not paying very close attention. We are now working around these browsers.
# [LOW] WebDAV prost-processing: Fixed handling of URLs containing spaces

Akeeba Solo 1.9.2
================================================================================
- Removed Dropbox API v1 integration. The v1 API is going to be discontinued by Dropbox, see https://blogs.dropbox.com/developers/2016/06/api-v1-deprecated/ Please use the Dropbox API v2 integration instead.
+ WordPress restoration: the default replacement data now includes the JSON-encoded versions of the data as well
+ WordPress restoration: all tables with the WP prefix are now pre-selected in the Replace Data page to minimise post-restoration issues from partial data replacement
+ Restoration: a warning is displayed if the database table name prefix contains uppercase characters
~ Workaround for sites with upper- or mixed-case prefixes / table names on MySQL servers running on case-insensitive filesystems and lower_case_table_names = 1 (default on Windows)
~ The backup engine will now warn you if you are using a really old PHP version
~ You will get a warning if you try to backup sites with upper- or mixed-case prefixes as this can cause major restoration issues.
# [HIGH] Conservative time settings (minimum execution time greated than the maximum execution time) cause the backup to fail
# [HIGH] Infinite loop if the #__ak_profiles table does not exist
# [MEDIUM] Restoration: The PDOMySQL driver would always crash with an error
# [LOW] WordPress restoration: the absolute path restoration missed the leading / for sites originating from non-Windows systems
# [LOW] Restoration: Database error information does not contain the actual error message generated by the database
# [LOW] Integrated Restoration: The last response timer jumped between values

Akeeba Solo 1.9.1
================================================================================
+ Added optional filter to automatically exclude MyJoomla database tables
~ Removed dependency on jQuery timers
~ Taking into account Joomla! 3.6's changes for the log directory location
# [LOW] Core version: Added a warning in Configuration Wizard when external directiories are required by the platform under backup

Akeeba Solo 1.9.0
================================================================================
+ Do not automatically display the backup log if it's too big
+ Halt the backup if the encrypted settings cannot be decrypted when using the native CLI backup script
+ Better display of tooltips: you now have to click on the label to make them static
+ Enable the View Log button on failed, pending and remote backups
~ Site Transfer Wizard is now available on PHP 5.3
# [HIGH] PHP's INI parsing "undocumented features" (read: bugs) cause parsing errors when certain characters are contained in a password.
# [LOW] Database dump could fail on servers where the get_magic_quotes_runtime function is not available

Akeeba Solo 1.9.0.b2
================================================================================
# [LOW] The integrated restoration would appear to be in an infinite loop if an HTTP error occurred
# [HIGH] CLI scripts do not respect configuration overrides (bug in engine's initialization)
# [HIGH] Backups taken with JSON API would always be full site backups, ignoring your preferences

Akeeba Solo 1.9.0.b1
================================================================================
! Restoration was broken for anything but WordPress backups
+ ANGIE for Joomla!: Option to set up Force SSL in Site Setup step
~ Less confusing buttons in integrated restoration
# [HIGH] Remote JSON API backups always using profile #1 despite reporting otherwise
# [LOW] Missing language keys in the Update section of System Configuration
# [LOW] Automatic description for command line (CLI) backups included a mangled date

Akeeba Solo 1.8.2
================================================================================
# [HIGH] Remote JSON API backups always using profile #1
# [LOW] Backup download confirmation message had escaped \n instead of newlines

Akeeba Solo 1.8.1
================================================================================
! Multipart backups are broken
# [HIGH] Remote JSON API backups would result in an error
# [HIGH] Front-end (remote) backups would always result in an error

Akeeba Solo 1.8.0
================================================================================
+ Automatic detection and working around of temporary data load failure
+ Direct download link to Akeeba Kickstart in the Manage Backups page
+ Working around PHP opcode cache issues occurring right before the restoration starts if the old restoration.php configuration file was not removed
+ Schedule Automatic backups button is shown after the Configuration Wizard completes
+ Schedule Automatic backups button in the Configuration page's toolbar
+ Download log button from ALICE page
~ Chrome won't let developers restore the values of password fields it ERRONEOUSLY auto-fills with random passwords. We worked around Google's atrocious bugs with extreme prejudice. You're welcome.
~ ANGIE (restoration script): Reset APC cache after the restoration is complete (NB! Only if you use ANGIE's Remove Installation Directory feature)
- Google Drive: Removed "Download to browser" feature since it's not supported
# [HIGH] ALICE would fail on PHP 7 due to use of obsolete language construct
# [MEDIUM] Fixed Rackspace CloudFiles when using a region different then London
# [MEDIUM] Remote backup could fail on WordPress when you have certain weird comments in your wp-config.php

Akeeba Solo 1.7.1
================================================================================
! SQL error could prevent update

Akeeba Solo 1.7.0
================================================================================
+ Google Drive integration
+ Added support for SQLite driver. Now you can use Akeeba Solo even if there's no database available
+ ANGIE: Warning message when you're trying to use a complex database password which may cause database restoration failure.
+ ANGIE: Improved UTF8MB4 support detection
+ ANGIE: Ability to downgrade UTF8MB4 data to UTF8
+ Added warning if the mbstring PHP extension is not installed
# [MEDIUM] Fixed recording of backup end time when a post-processing engine is used
# [LOW] JSON API failed to delete the backups
# [LOW] ANGIE for Drupal: Fixed restoration on a different domain when an multi-installation of Drupal is used
# [LOW] Multiple database definition: Fixed adding more databases at once

Akeeba Solo 1.6.4
================================================================================
# [HIGH] Language files processed by Transifex caused the language parser to throw errors

Akeeba Solo 1.6.3
================================================================================
# [HIGH] Static Initialization Vector (derived from the encryption key) for Rijndael-128 encryption in JPS files and app settings weakened the encryption strength
# [MEDIUM] Some servers with problematic libcurl implementations are unable to use Dropbox API v2, always receiving an error message.
# [MEDIUM] Upload to OneDrive occasionally fails when their API doesn't report the folder creation correctly

Akeeba Solo 1.6.2
================================================================================
! This version was skipped to keep in sync with Akeeba Backup for WordPress' versioning

Akeeba Solo 1.6.1
================================================================================
# [HIGH] Possible backup failure when the database being backed up is of a different type (e.g. PostgreSQL vs MySQL) than the one containing the backup profile configuration
# [LOW] PHP Warning during session sav

Akeeba Solo 1.6.0
================================================================================
! Front-end and remote backup features will be DISABLED if we detect an insecure Secret Word
+ Added textual output to ALICE so it could be included in support tickets
+ Automatically run ALICE if an error occurs during the last domain of a backup
+ Added support for Magento 2
+ Support for Amazon S3's Standard- Infrequent Access storage type
~ More stable Site Transfer Wizard thanks to improved transfer chunk size calculations
# [HIGH] Could not save configuration without enabling the One-click backup icon
# [HIGH] Drupal: Fixed endless loop while trying to read the configuration
# [MEDIUM] The "Include Akeeba Solo in the backup" feature would cause restoration issues
# [MEDIUM] One Click Backup buttons wouldn't actually switch the backup profile
# [MEDIUM] Fixed routing issue in Site Transfer Wizard
# [LOW] No Status displayed in the Latest Backup module for successful backups
# [LOW] Added workaround for untranslated text caused by disabled parse_ini_* functions
# [LOW] Fixed typo in ALICE
# [LOW] Site Transfer Wizard, bad performance of the test FTP/SFTP servers could lead to an instant error when accessing this feature
# [LOW] Fixed PhpBB platform recognition
# [LOW] Someone who already knows your Secret Word can store XSS in the database if the remote backup is enabled and you're not using the security enhanced .htaccess file (discovered by NCC Group)
# [LOW] The Configuration Wizard would fail to correctly detect configuration settings of Joomla! sites whose configuration.php file contains comments (e.g. using a copy of the configuration.php-dist file)

Akeeba Solo 1.5.3
================================================================================
+ One-click backup icons. Select which profiles to display as one-click icons in Akeeba Solo's control panel
+ More prominent site restoration and transfer instructions in the Manage Backups page
+ ANGIE: Added support for PrestaShop 1.4.x
# [HIGH] Chunked upload to OneDrive would result in an error
# [HIGH] If the PDO extension isn't loaded you get an immediate fatal error trying to list database drivers
# [HIGH] Only 5Mb would be transferred to Amazon S3 on some hosts with PHP compiled against a broken libcurl library; workaround applied.
# [HIGH] Missing file from Core edition may cause immediate backup failure
# [HIGH] Multipart upload to S3 fails when the file size is an integer multiple of the chunk size
# [HIGH] Amazon S3 has a major bug with Content-Length on some hosts when using single part uploads. We are now working around it.
# [MEDIUM] Using "Manage remote files" in the Manage Backups page could mangle backup profiles under some circumstances
# [LOW] Extra databases definition page, trying to validate the connection without any information entered results in a success message
# [LOW] Fixed supplying additional Download ID directly from the Control Panel page
# [LOW] Fixed backing up multiple external folders

Akeeba Solo 1.5.2
================================================================================
# [HIGH] Under rare circumstances temporary data could be committed to the database and break future backups.
# [HIGH] Amazon S3 has a major bug with Content-Length on some hosts when using multipart uploads. We are now working around it.
# [HIGH] Google Storage and DreamObjects would not work
# [HIGH] Upload to S3 would fail due to cacert.pem issues with some hosts and versions of PHP
# [HIGH] Upload to S3 would fail if a file was an integer multiple of the Amazon S3 chunk size (5242880)

Akeeba Solo 1.5.2.b1
================================================================================
- Removed the "Upload to Amazon S3 (legacy API)" post-processing engine. Existing backup profiles will be automatically migrated to the "Upload to Amazon S3" engine.
+ Akeeba Solo will ask you to enter your Download ID before showing you available updates.
+ Akeeba Solo will ask you to run the Configuration Wizard when it detects it's necessary. One less thing for you to remember!
+ JSON API: Return an error if preflight configuration checks indicate a critical error (e.g. output directory not writeable)
+ Added option to test email settings
+ "Exclude error logs" filter, enabled by default, to prevent broken backup archives when error logs change their size / are rotated while the backup is in progress
+ Automatically exclude folders related to control version systems
~ Desktop notifications are now optional. If you have already enabled them don't worry, they won't be disabled (your browser remembers the previous setting).
~ Improved layout for the Control Panel page
~ New icon colour palette for easier identification of the Control Panel icons
~ The profile ID is displayed in the profile selection drop-down
~ Manage Backups: "Part 00" would be displayed when there is just one backup part present. Button label changed to "Download".
~ Manage Backups: Simplified the page layout
~ ANGIE: Removed the confusing messages about restoring to a different site or PHP version
~ ANGIE: Simplified the final page ("Finished")
~ Remove the title from the popover tips
# [HIGH] No error thrown when the engine state cannot be saved while the backup is in progress
# [HIGH] The database storage option for the temporary data didn't work
# [MEDIUM] Updated Dropbox post processing engine due to changes on their servers
# [LOW] The backup size would be inflated if "Upload each part immediately" is enabled and it takes multiple step to post process a single archive part

Akeeba Solo 1.5.0 and 1.5.1
================================================================================
~ These versions were removed shortly after release due to high priority issues in the Amazon S3 API.

Akeeba Solo 1.4.2
================================================================================
# [LOW] The Size in Manage Backups would be wrong as the last part's size was counted twice

Akeeba Solo 1.4.1
================================================================================
! Problem with the display of language strings

Akeeba Solo 1.4.0
================================================================================
! The minimum required PHP version is now 5.4.0
+ Automatically check the integrity of the backup archive (opt-in, please read the documentation)
+ ANGIE: Added option to enable/disable mail system while restoring a Joomla site
# [HIGH] Multipart ZIP archives were broken
# [MEDIUM] ANGIE: You could not override the ANGIE password in the Backup Now page
# [LOW] The SQL query would not be printed on database errors

Akeeba Solo 1.3.4
================================================================================
~ ANGIE: Improve memory efficiency of the database engine
~ Switching the default log level to All Information and Debug
+ Support utf8mb4 in CRON jobs
# [LOW] Desktop notifications for backup resume showed "%d" instead of the time to wait before resume
# [LOW] Push notifications should not be enabled by default
# [MEDIUM] Dropbox integration would not work under many PHP 5.5 and 5.6 servers due to a PHP issue. Workaround applied.
# [HIGH] Restoring on MySQL would be impossible unless you used the MySQL PDO driver

Akeeba Solo 1.3.3
================================================================================
! Packaging error leading to immediate backup failure

Akeeba Solo 1.3.2
================================================================================
+ Push notifications with Pushbullet
# [HIGH] ANGIE: Restoration may fail or corrupt text on some servers due to UTF8MB4 support

Akeeba Solo 1.3.1
================================================================================
~ Updated Import from S3 to use the official Amazon AWS SDK for PHP
~ ANGIE (restoration script): Reset OPcache after the restoration is complete (NB! Only if you use ANGIE's Remove Installation Directory feature)
+ You can set the backup profile name directly from the Configuration page
+ You can create new backup profiles from the Configuration page using the Save & New button
+ Desktop notifications for backup start, finish, warning and error on compatible browsers (Chrome, Safari, Firefox)
+ UTF8MB4 (UTF-8 multibyte) support in restoration scripts, allows you to correctly restore content using multibyte Unicode characters (Emoji, Traditional Chinese, etc)
# [HIGH] Restoration might be impossible if your database passwords contains a double quote character
# [MEDIUM] Dropbox and iDriveSync: could not upload under PHP 5.6 and some versions of PHP 5.5
# [MEDIUM] Immediate crash when the legacy MySQL driver is not available
# [MEDIUM] OneDrive upload could fail in CLI CRON jobs if the upload of all parts takes more than 3600 seconds
# [MEDIUM] Reupload from Manage Backups failed when the post-processing engine is configured to use chunked uploads
# [MEDIUM] Fixed update script
# [LOW] White page when the ak_stats database table is broken

Akeeba Solo 1.3.0
================================================================================
+ Warning with information and instructions when you have PHP 5.3.3 or earlier instead of a cryptic error message
+ Warning if you have an outdated PHP version which we'll stop supporting soon
+ Added support for uploading archives in subdirectories while using FTP post-processing engine
+ gh-28 Native Microsoft Live OneDrive support
+ gh-29 Profile description tooltip in Manage Backups page when hovering over the profile ID
+ gh-29 Profile filter in Manage Backups page
+ gh-30 Added option to enable/disable profile encryption
+ gh-31 Added page for displaying phpinfo() dump
# [HIGH] The ANGIE script selected in Configuration Wizard is not applied to the configuration
# [MEDIUM] Cancelling the creation of a new user account or backup profile could lead to server error
# [LOW] Fixed javascript error while using an ANGIE password containing single quotes
# [LOW] gh-26 Fixed white page when an error occurs and we're inside Wordpress
# [LOW] Prevent usage of negative profile ID in CLI backups
# [MEDIUM] Import from S3 didn't work correctly

Akeeba Solo 1.2.2
================================================================================
+ Added "Apply to all" button in Files and Directories Exclusion page
# [HIGH] Missing interface options on bad hosts which disable the innocent parse_ini_file PHP function
# [HIGH] ANGIE (restoration): Some bad hosts disable the innocent parse_ini_file PHP function resulting in translation and functional issues during the restoration
# [MEDIUM] ANGIE for Wordpress: Site url was not replaced when moving to a different server
# [MEDIUM] Update notification not displaying on some sites
# [MEDIUM] Frontend check for failed backups was not working
# [MEDIUM] Extradirs folders where not saved
# [LOW] Clicking the Configure button in the Profiles page can lead to error 500 on hosts with GET query parameter name length limits
# [LOW] ANGIE for Wordpress: fixed changing Admin access details while restoring
# [LOW] Updater doesn't work properly on PHP 5.6

Akeeba Solo 1.2.1
================================================================================
# [LOW] Username filtering doesn't work in the Users page
# [HIGH] Control Panel icons and translations not shown on some extremely low quality hosts which disable the innocuous parse_ini_file function. If you were affected SWITCH HOSTS, IMMEDIATELY!
# [HIGH] Old PHP 5.3 versions have a bug regarding Interface implementation, causing a PHP fatal error

Akeeba Solo 1.2.0
================================================================================
~ New icon set in the main page
+ Now supports WordPress Multisite for restoration on new servers (you have to keep the same subdomain or subdirectory layout for your multisites)
# [LOW] Upload to Dropbox may not work on servers without a global cacert.pem file

Akeeba Solo 1.2.0.rc5
================================================================================
! DirectoryIterator::getExtension is not compatible with PHP 5.3.4 and 5.3.5
- Removed the (broken) multipart upload from the legacy S3 post-processing engine. Please use the new "Upload to Amazon S3" option for multipart uploads.
# [HIGH] Bug in third party Guzzle library causes Amazon S3 multipart uploads of archives larger than the remaining RAM size to fail due to memory exhaustion.
# [HIGH] ANGIE for WordPress: The .htaccess was broken on restoration due to two typos in the code
# [MEDIUM] Fatal error on sites with open_basedir restrictions on the site's root

Akeeba Solo 1.2.0.rc4
================================================================================
# [LOW] 500 error on some sites after updating to version 1.2
~ There are no 1.2.0.rc3 and 1.2.0.rc4 versions; these version numbers were reserved for use by Akeeba Backup CORE for WordPress.

Akeeba Solo 1.2.0.rc1
================================================================================
+ New and improved backup engine
+ ANGIE for WordPress: Update serialised data
+ ANGIE: Add warning about Live site URL on Windows
+ You can now sort and search records in the Profile Management page
~ Workaround for magic_quotes_gpc under PHP 5.3
~ Changed the .htaccess files to be compatible with Apache 2.4
~ Improved responsive display
~ Layout tweaks in the Configuration page
# [HIGH] Update information not fetched unless you manually retry through the Update page
# [MEDIUM] ANGIE: The option "No auto value on zero" was not working
# [MEDIUM] The data file pointer can be null sometimes when using multipart archives causing backup failures
# [MEDIUM] Upload to remote storage from the Manage Backups page was broken for Amazon S3 multipart uploads
# [MEDIUM] Race condition could prevent the reliable creation of JPS (encrypted) archives
# [LOW] ANGIE: Fixed table name abstraction when no table prefix is given
# [LOW] ANGIE: Fixed loading of translations
# [LOW] ANGIE for WordPress: The blog name and tagline were empty when restoring to a new server (thanks Dimitris!)
# [LOW] SFTP post-processing engine did not mark successfully uploaded backup as Remote
# [LOW] SFTP post-processing engine could not fetch the archive back to the server
# [LOW] Tooltips not showing for engine parameters when selecting a different engine (e.g. changing the Archiver Engine from JPA to ZIP)

Akeeba Solo 1.1.5
================================================================================
# [HIGH] The integrated restoration is broken after the last security update

Akeeba Solo 1.1.4
================================================================================
! [SECURITY: Medium] Possibility of arbitrary file writing while a backup archive is being extracted by the integrated restoration feature

Akeeba Solo 1.1.3
================================================================================
! White page under certain versions of PHP

Akeeba Solo 1.1.2
================================================================================
! Backup failure on certain Windows hosts and PHP versions due to the way these versions handle file pointers
! Failure to post-process part files immediately on certain Windows hosts and PHP versions due to the way these versions handle file pointers
# [HIGH] Translations wouldn't load
# [LOW] Exclude non-core tables button not working in database table exclusion page
# [LOW] Possible white page if you have are hosting multiple Solo installations on the same (sub)domain

Akeeba Solo 1.1.1
================================================================================
! Dangling file pointer causing backup failure on certain Windows hosts
~ CloudFiles implementation changed to authentication API version 2.0, eliminating the need to choose your location
~ Old MySQL versions (5.1) would return randomly ordered rows when dumping MyISAM tables when the MySQL database is corrupt up to the kazoo and about to come crashing down in flames
# [LOW] Database table exclusion table blank and backup errors when your db user doesn't have adequate privileges to show procedures, triggers or stored procedures in MySQL
# [LOW] Could not back up triggers, procedures and functions

Akeeba Solo 1.1.0
================================================================================
+ Support for iDriveSync accounts created in 2014 or later
+ A different log file is created per backup attempt (and automatically removed when the backup archives are deleted by quotas or by using Delete Files in the interface)
+ You can now run several backups at the same time
+ The minimum execution time can now be enforced in the client side for backend backups, leading to increased stability on certain hosts
+ Back-end backups will resume after an AJAX error, allowing you to complete backups even on very hosts with very tight resource usage limits
+ The Dropbox chunked upload can now work on files smaller than 150Mb and will work across backup steps, allowing you to upload large files to Dropbox without timeout errors
+ Backups resulting in an AJAX error will be retried, in case backup failure was caused by a temporary server or network issue
+ Greatly improve the backup performance on massive tables as long as they have an auto_increment column
+ Work around the issues caused by some servers' error pages which contain DOM-modifying JavaScript
+ Work around for the overreaching password managers in so-called modern browsers which fill irrelevant passwords in the configuration page.
+ Improved Two Factor Authentication with one time emergency passwords
# [HIGH] Dropbox upload would enter an infinite loop when using chunked uploads
# [HIGH] Javascript not loaded in Setup page
# [HIGH] Potential information leak through the JSON API using a Decryption Oracle attack
# [MEDIUM] ANGIE for Joomla!, cannot detect Joomla! version, leading to Two Factor Auth data not being re-encoded with the new secret key
# [MEDIUM] ANGIE: Restoring off-site directories would lead to errors
# [MEDIUM] ANGIE (all flavours): cannot restart db restoration after a database error has occurred.
# [LOW] FTP port and directory not taken into account in the the setup page
# [LOW] ANGIE for WordPress, phpBB and PrestaShop: escape some special characters in passwords
# [LOW] Do not offer to install using the PDO or SQLite drivers as there's no schema file available for them
# [LOW] Wrong language strings (copied from the original Joomla! component's codebase)
# [LOW] ANGIE for Joomla!, array values in configuration.php were corrupted
# [LOW] Setup user interface issue when debug mode is enabled

Akeeba Solo 1.0.4
================================================================================
! [HIGH] Information disclosure through the JSON API. This is a theoretical attack since we determined it is impractical to perform outside a controlled environment.

Akeeba Solo 1.0.3
================================================================================
! [HIGH] Packaging error leading to fatal error (white page)

Akeeba Solo 1.0.2
================================================================================
# [HIGH] Sometimes no instructions shown in Setup when the config.php file cannot be saved
# [HIGH] Incorrect config.php file contents shown in Setup when the config.php file cannot be saved
# [HIGH] Fatal error under PHP 5.3.5
# [HIGH] Could not login under PHP 5.3.3 up to and including 5.3.5 due to database issue caused by unencoded binary salt
# [HIGH] [PRO] ANGIE: restoring off-site directories leads to unworkable permissions (0341) in their subdirectories due to a typo
# [MEDIUM] Error thrown when MB lang is not the default, preventing setup to continue
# [LOW] Do not complain if config.php is web accessible but it returns no contents (that's not a major security risk)
# [LOW] You were told that the config.php file wasn't written to disk even when it really was
# [LOW] You are given a blocking error when you have not defined db connection information for a files only backup and vice versa

Akeeba Solo 1.0.1
================================================================================
! Packaging error making it impossible to go past the database setup page

Akeeba Solo 1.0.0
================================================================================
~ Only users with all privileges can manage the users list (prevents users with the configuration privilege from escalating their privileges)
~ Only users with all privileges can access the system configuration
~ Only users with all privileges can access the updater
# [HIGH] Using a database password with special characters didn't allow you to proceed with the installation

Akeeba Solo 1.0.b5
================================================================================
! The CDN was serving the wrong package instead of 1.0.b4, one which didn't include the installation issue fix

Akeeba Solo 1.0.b4 - 2014/05/15
================================================================================
! Installation wouldn't get past the database page

Akeeba Solo 1.0.b3 - 2014/05/05
================================================================================
! Configuration file changed from config.json to config.php – expect some turbulence after update
+ You can now change the date/time format for the Start column in the Manage Backups page
+ Pre-fill Site's URL and Site Root if Akeeba Solo is installed in a subdirectory
+ Support restoration of "split URL" WordPress sites (site root and WordPress root being different directories)
+ Add an icon in the user manager to indicate which Two Factor Authentication method is enabled for each user account
~ Much improved database installer / updater
~ Update notifications are performed using AJAX to prevent a connection timeout from making Akeeba Solo inaccessible
# [HIGH] WordPress sites not detected when the wp-config.php file is above the site's root
# [HIGH] Fatal error when the session path is not writeable
# [HIGH] The "Include Akeeba Solo in the backup" feature was not working properly
# [MEDIUM] The entire backup output folder was excluded from the backup, breaking Solo when restored from a backup
# [MEDIUM] Configuration Wizard didn't warn you if your database settings were incorrect
# [MEDIUM] It wasn't possible to link to Dropbox
# [LOW] Sometimes the ANGIE password warning would appear without a password having been set
# [LOW] The profile selection box wouldn't show in the Backup Now page

Akeeba Solo 1.0.b2 - 2014/04/01
================================================================================
! Wrong name of Awf/Adapters/Curl.php file led to fatal error on some hosts
# [LOW] Download through browser warning showing \n instead of newlines
# [LOW] Beta version shown with an ALPHA badge

Akeeba Solo 1.0.b1 - 2014/04/01
================================================================================
! First release
