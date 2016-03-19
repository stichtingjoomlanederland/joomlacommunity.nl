<?php die();?>
Akeeba Backup 4.5.5
================================================================================
! The file integrity check feature would fail on some hosts due to Joomla!'s installer being broken. The feature was removed.

Akeeba Backup 4.5.4
================================================================================
# [HIGH] The Quickicon plugin was missing a folder, making it impossible to configure which backup profile it should use for one click backups.
# [HIGH] The icon module for Joomla! 1.6/1.7 was causing problems in Joomla! 3. This module is now removed.
# [MEDIUM] Built-in update would fail under Joomla! 1.5/1.6/1.7/2.5
# [LOW] Under Joomla! 1.5/1.6/1.7/2.5 the "Update available" message would be displayed even if running the latest version

Akeeba Backup 4.5.3
================================================================================
! The System – Akeeba Backup Update Check plugin caused Admin Tools to fail with a white page

Akeeba Backup 4.5.2
================================================================================
! Joomla! 3.5 breaks CLI scripts, workaround applied
+ Backup check quick icon module for Joomla! 1.6/1.7
+ The backup check quick icon now launches a single click backup
+ Added option to automatically exclude hosting web stats (e.g. Webalyzer)
~ Improved display of backup check quick icon module
~ Disable CLI script workaround necessary only under Joomla! 3.4.7 on all other versions of Joomla!
# [HIGH] Static Initialization Vector (derived from the encryption key) for Rijndael-128 encryption in JPS files and app settings weakened the encryption strength
# [HIGH] ANGIE for Drupal 7: Fixed fatal error in setup page
# [MEDIUM] Some servers with problematic libcurl implementations are unable to use Dropbox API v2, always receiving an error message.
# [MEDIUM] Upload to OneDrive occasionally fails when their API doesn't report the folder creation correctly
# [MEDIUM] ANGIE for Wordpress: Fixed data replacement in case-sensitive filesystems
# [LOW] A fatal error is recorded in the PHP error log if you try to access a front-end view which doesn't exist

Akeeba Backup 4.5.1
================================================================================
# [HIGH] Joomla! 3.4.7 has a major bug which prevents CLI (CRON) scripts from executing
# [HIGH] Possible backup failure when the database being backed up is of a different type (e.g. PostgreSQL vs MySQL) than the one containing the backup profile configuration
# [HIGH] The Configuration Wizard popup is illegible under Joomla! 3.x
# [HIGH] MySQL error on Joomla! 1.6 when updating statistics
# [MEDIUM] Automatic update CLI script throws fatal error
# [MEDIUM] Test FTP Connection results didn't display properly under Joomla! 3.x
# [MEDIUM] Test SFTP Connection results didn't display properly under Joomla! 1.x / 2.5
# [LOW] "Copy" label untranslated in Profiles page under Joomla! 1.6

Akeeba Backup 4.5.0
================================================================================
! Front-end and remote backup features will be DISABLED if we detect an insecure Secret Word
+ Automatically run ALICE if an error occurs during the last domain of a backup
+ Added textual output to ALICE so it could be included in support tickets
+ Support for Amazon S3's Standard- Infrequent Access storage type
+ Integrated updater (optional for Joomla! 3.2+, mandatory for Joomla! 1.x/2.x/3.0/3.1)
~ More stable Site Transfer Wizard thanks to improved transfer chunk size calculations
~ Now compatible with Joomla! 1.7, 2.5, 3.0, 3.1, 3.2, 3.3, 3.4 and 3.5 running on PHP 5.3.03+, 5.4, 5.5 and 5.6.
# [HIGH] ANGIE for Drupal: Fixed endless loop while trying to read the configuration
# [LOW] Site Transfer Wizard, bad performance of the test FTP/SFTP servers could lead to an instant error when accessing this feature
# [LOW] Notice thrown by the auto-update CLI script
# [LOW] Low encoding of media folder permissions check could show an erroneous message on some sites (thanks Angel!)
# [LOW] Someone who already knows your Secret Word can store XSS in the database if the remote backup is enabled and you're not using Joomla!'s or Admin Tools' .htaccess file (discovered by Calum Hutton, NCC Group)
# [LOW] Open redirection in back-end backups (discovered by Calum Hutton, NCC Group)
# [LOW] ANGIE for Wordpress: Fixed missing version number
# [LOW] ANGIE for Wordpress: Fixed missing email address in site setup

Akeeba Backup 4.4.3
================================================================================
+ One-click backup icons. Select which profiles to display as one-click icons in Akeeba Backup's control panel
+ More prominent site restoration and transfer instructions in the Manage Backups page
+ ANGIE: Added support for PrestaShop 1.4.x
# [HIGH] Chunked upload to OneDrive would result in an error
# [HIGH] Only 5Mb would be transferred to Amazon S3 on some hosts with PHP compiled against a broken libcurl library; workaround applied.
# [HIGH] Multipart upload to S3 fails when the file size is an integer multiple of the chunk size
# [HIGH] Amazon S3 has a major bug with Content-Length on some hosts when using single part uploads. We are now working around it.
# [MEDIUM] Using "Manage remote files" in the Manage Backups page could mangle backup profiles under some circumstances
# [LOW] Extra databases definition page, trying to validate the connection without any information entered results in a success message
# [LOW] Fixed supplying additional Download ID directly from the Control Panel page
# [LOW] Fixed backing up multiple external folders

Akeeba Backup 4.4.2
================================================================================
# [HIGH] Under rare circumstances temporary data could be committed to the database and break future backups.
# [HIGH] Amazon S3 has a major bug with Content-Length on some hosts when using multipart uploads. We are now working around it.
# [HIGH] Google Storage and DreamObjects would not work
# [HIGH] Upload to S3 would fail due to cacert.pem issues with some hosts and versions of PHP
# [HIGH] Upload to S3 would fail if a file was an integer multiple of the Amazon S3 chunk size (5242880)
# [HIGH] Obsolete jQuery Tooltip plugin could cause JavaScript errors

Akeeba Backup 4.4.2.b1
================================================================================
- Removed the "Upload to Amazon S3 (legacy API)" post-processing engine. Existing backup profiles will be automatically migrated to the "Upload to Amazon S3" engine. [PRO]
- Removed post-installation messages
+ Akeeba Backup will ask you to enter your Download ID before showing you available updates. [PRO]
+ Akeeba Backup will ask you to run the Configuration Wizard when it detects it's necessary. One less thing for you to remember!
+ JSON API: Return an error if preflight configuration checks indicate a critical error (e.g. output directory not writeable)
+ "Exclude error logs" filter, enabled by default, to prevent broken backup archives when error logs change their size / are rotated while the backup is in progress
~ CLI scripts should now print a warning instead of immediately dying if you try running them in PHP 5.2 or earlier (what are you doing with this kind of archaic PHP versions on your production servers anyway?!!) [PRO]
~ The profile ID is displayed in the profile selection drop-down
~ Desktop notifications are now optional. If you have already enabled them don't worry, they won't be disabled (your browser remembers the previous setting).
~ Improved layout for the Control Panel page
~ New icon colour palette for easier identification of the Control Panel icons
~ No more yellow flash when loading the Akeeba Backup control panel page
~ Manage Backups: "Part 00" would be displayed when there is just one backup part present. Button label changed to "Download".
~ Manage Backups: Simplified the page layout
~ ANGIE: Removed the confusing messages about restoring to a different site or PHP version
~ ANGIE: Simplified the final page ("Finished")
~ Make it more obvious that the Scheduling Information tabs actually DO work when you click on them
~ Remove the title from the popover tips
+ ANGIE: Added throttle speed for replacing data
+ Automatically exclude folders related to version control systems
# [HIGH] No error thrown when the engine state cannot be saved while the backup is in progress
# [HIGH] The database storage option for the temporary data didn't work
# [MEDIUM] Updated Dropbox post processing engine due to changes on their servers [PRO]
# [MEDIUM] FTP/SFTP browser would fail on some servers when you don't provide a starting directory [PRO]
# [LOW] Database schema self-healing was disabled
# [LOW] The backup size would be inflated if "Upload each part immediately" is enabled and it takes multiple step to post process a single archive part [PRO]

Akeeba Backup 4.4.0 and 4.4.1
================================================================================
~ These versions were removed shortly after release due to high priority issues in the Amazon S3 API.

Akeeba Backup 4.3.1
================================================================================
# [LOW] Core: Archive integrity test would not run due to a file not being installed
# [LOW] The Size in Manage Backups would be wrong as the last part's size was counted twice

Akeeba Backup 4.3.0
================================================================================
! Minimum PHP version required is now 5.4.0
+ Automatically check the integrity of the backup archive (opt-in, please read the documentation)
+ ANGIE: Added option to enable/disable mail system while restoring a Joomla site
# [LOW] The autoupdate script could not download the package in PRO versions
# [LOW] Fixed profile description tooltip
# [LOW] The SQL query would not be printed on database errors
# [MEDIUM] ANGIE: You could not override the ANGIE password in the Backup Now page
# [HIGH] Multipart ZIP archives were broken

Akeeba Backup 4.2.4
================================================================================
~ ANGIE: Improve memory efficiency of the database engine
~ Switching the default log level to All Information and Debug
+ Support utf8mb4 in CRON jobs
# [LOW] Desktop notifications for backup resume showed "%d" instead of the time to wait before resume
# [LOW] Push notifications should not be enabled by default
# [MEDIUM] Dropbox integration would not work under many PHP 5.5 and 5.6 servers due to a PHP issue. Workaround applied.
# [HIGH] Restoring on MySQL would be impossible unless you used the MySQL PDO driver

Akeeba Backup 4.2.3
================================================================================
! Packaging error leading to immediate backup failure

Akeeba Backup 4.2.2
================================================================================
+ Push notifications with Pushbullet
# [HIGH] ANGIE: Restoration may fail or corrupt text on some servers due to UTF8MB4 support

Akeeba Backup 4.2.1
================================================================================
~ Updated Import from S3 to use the official Amazon AWS SDK for PHP
~ OneDrive: refresh the tokens if they expire in the middle of an upload
+ You can set the backup profile name directly from the Configuration page
+ You can create new backup profiles from the Configuration page using the Save & New button
+ Desktop notifications for backup start, finish, warning and error on compatible browsers (Chrome, Safari, Firefox)
+ UTF8MB4 (UTF-8 multibyte) support in restoration scripts, allows you to correctly restore content using multibyte Unicode characters (Emoji, Traditional Chinese, etc)
~ The logs and log directories at the site's root are forcibly excluded if present (some developers hardcode them)
~ ANGIE (restoration script): Reset OPcache after the restoration is complete (NB! Only if you use ANGIE's Remove Installation Directory feature)
# [HIGH] Restoration might be impossible if your database passwords contains a double quote character
# [MEDIUM] Dropbox and iDriveSync: could not upload under PHP 5.6 and some versions of PHP 5.5
# [MEDIUM] OneDrive upload could fail in CLI CRON jobs if the upload of all parts takes more than 3600 seconds
# [MEDIUM] Possible crash when the legacy MySQL driver is not available
# [MEDIUM] Reupload from Manage Backups failed when the post-processing engine is configured to use chunked uploads
# [LOW] White page when the ak_stats database table is broken
# [LOW] Some installations didn't have the correct embedded installer selected. Now forcing the correct default if an invalid value is detected.

Akeeba Backup 4.2.0
================================================================================
- Removed Joomla! 2.5 support
- Removed post-setup page; these messages are now handled through Joomla!'s Post-Installation Messages feature
- Removed the Site Transfer Wizard. You can use the DirectFTP/DirectSFTP engines; or use Upload to FTP/SFTP and check the Upload Kickstart option (preferred method)
- Removed the System Restore Point feature; please make a full site backup before updating your extensions. Joomla! 3.x requires full backups due to the way ACL, Categories, Tags and Content History work; that's why the SRP feature was removed.
- Removed Extension Filters. For the same reasons we removed SRP: this cannot work as expected in Joomla! 3.x.
- Removed Lite Mode in the front-end. Smartphones are now the norm and Joomla! 3.x offers a responsive back-end. Just use it from your smartphone!
+ Check for obsolete PHP versions and printing a warning
+ gh-528 Native support for Microsoft Live OneDrive
+ gh-529 Added support for uploading archives in subdirectories while using FTP post-processing engine
+ gh-530 Show profile title in a tooltip when hovering over the profile ID in Manage Backups page
+ gh-530 Profile filter in the Manage Backups page
+ Always exclude the configured Joomla! log directory, no matter where it is
# [LOW] ANGIE would throw a memory outage error on extreme circumstances when the database server connected and disconnected immediately
# [LOW] Prevent usage of negative profile ID in CLI backups
# [MEDIUM] Import from S3 could be a byte off

Akeeba Backup 4.1.2
================================================================================
+ Added "Apply to all" button in Files and Directories Exclusion page
+ ANGIE for Wordpress: Db collation set to "utf8_general_ci" by default
# [HIGH] Missing interface options on bad hosts which disable the innocent parse_ini_file PHP function
# [HIGH] ANGIE (restoration): Some bad hosts disable the innocent parse_ini_file PHP function resulting in translation and functional issues during the restoration
# [MEDIUM] ANGIE for Wordpress: Site url was not replaced when moving to a different server
# [LOW] On some hosts you wouldn't get the correct installer included in the backup
# [LOW] ANGIE for Wordpress: fixed changing Admin access details while restoring
# [LOW] Configuration Wizard: detected minimum execution time was ignored; default value of 2 seconds always applied

Akeeba Backup 4.1.1
================================================================================
- Removed the obsolete "Upgrade profiles to ANGIE" post-installation message
# [LOW] Tooltips for backup comments not shown in Manage Backup page on Joomla! 3.x
# [HIGH] Control Panel icons not shown on some extremely low quality hosts which disable the innocuous parse_ini_file function. If you were affected SWITCH HOSTS, IMMEDIATELY!
# [HIGH] gh-523 Fatal error on Joomla! 2.5 when logging in as Administrator (NOT Super User!) and you have published our Quick Icon plugin
# [HIGH] Old PHP 5.3 versions have a bug regarding Interface implementation, causing a PHP fatal error

Akeeba Backup 4.1.0
================================================================================
+ Brand new icon set in the Control Panel page by Helvecio da Silva (http://hlvcdesign.com.br)
+ Warning added when Joomla!'s com_postinstall component is broken (with instructions to fix it)
~ Less intrusive display of the file integrity check results
# [HIGH] System Restore Points didn't really work
# [HIGH] The Quick Icon - Akeeba Backup Notification plugin was broken since 4.1.0.rc1 (thanks Camden!)
# [HIGH] Stack filters wouldn't load. As a result, voluminous and unwanted data of Joomla!'s Finder tables would always be included.
# [MEDIUM] The backup ID was not returned to JSON API requests, making download after backup with Akeeba Remote CLI impossible (thanks Mikkel)
# [LOW] Work around Joomla!'s bug causing it to not load its library translation strings while showing the installation status messages when System Restore Points are enabled
# [LOW] Piecon could throw Javascript errors on some sites
# [LOW] Upload to Dropbox may not work on servers without a global cacert.pem file

Akeeba Backup 4.1.0.rc3
================================================================================
! DirectoryIterator::getExtension is not compatible with PHP 5.3.4 and 5.3.5
! As we announced 18 months ago, we require PHP 5.3.4 or later. If your site doesn't meet this requirement a very stern warning will be issued.
- Removed the (broken) multipart upload from the legacy S3 post-processing engine. Please use the new "Upload to Amazon S3" option for multipart uploads.
~ Removing references to JParameter (it was removed in Joomla! 3.4 alpha)
# [HIGH] Some Pro features (restore SRPs, site transfer wizard) not working because their files are deleted on installation
# [HIGH] Bug in third party Guzzle library causes Amazon S3 multipart uploads of archives larger than the remaining RAM size to fail due to memory exhaustion.
# [MEDIUM] The backup would halt if the upload to S3 failed instead of simply raising a warning
# [MEDIUM] System Restore Points were throwing a fatal error while applying size quota
# [MEDIUM] Fatal error on sites with open_basedir restrictions on the site's root
# [LOW] System Restore Points throwing warnings

Akeeba Backup 4.1.0.rc2
================================================================================
! Settings would be lost when upgrading to 4.1.0.rc1 due to the loss of the settings' encryption key on upgrade
# [LOW] 500 error when a specified engine (scanner, archiver, post-processing, dump) doesn't exist

Akeeba Backup 4.1.0.rc1
================================================================================
+ New and improved backup engine
+ Installation integrity check automatically spots missing, out of date or tampered files and warns you
+ ALICE will check if Additional Database information are correct [PRO]
+ ANGIE: Add warning about Live site URL on Windows
+ You can now sort and search entries in the Profiles Management page
+ Using advanced selects (Chosen) for backup profile lists under Joomla! 3
- Removed inconsistent submenus at the top of some pages
~ The following features are deprecated and will be removed in a later version: site transfer wizard, system restore points, extension filters, lite mode, the old Amazon S3 integration (there is a new Amazon S3 integration which has replaced it)
~ Changed the .htaccess files to be compatible with Apache 2.4
~ Improved styling for detected issues
~ Set the commonly used ports for FTP/FTPS and SFTP transfers in the Site Transfer Wizard [PRO]
# [MEDIUM] ANGIE: The option "No auto value on zero" was not working
# [MEDIUM] The data file pointer can be null sometimes when using multipart archives causing backup failures
# [MEDIUM] Fixed error while trying to fetch Super Administrators email during frontend backup
# [MEDIUM] ALICE: The "Backup engine state saving issues" and "Timeout while backing up" tests returned wrong results [PRO]
# [MEDIUM] Upload to remote storage from the Manage Backups page was broken for Amazon S3 multipart uploads [PRO]
# [MEDIUM] ae/gh-11 Race condition could prevent the reliable creation of JPS (encrypted) archives [PRO]
# [MEDIUM] gh-522 "Back to standard installer" still results in a System Restore Point backup being taken [PRO]
# [LOW] Tooltips not showing for engine parameters when selecting a different engine (e.g. changing the Archiver Engine from JPA to ZIP)
# [LOW] The lib_joomla translation might not be loaded when an extension is installed and the System - System Restore Points plugin is enabled [PRO]
# [LOW] ANGIE: Fixed table name abstraction when no table prefix is given
# [LOW] ANGIE: Fixed loading of translations
# [LOW] SFTP post-processing engine did not mark successfully uploaded backup as Remote [PRO]
# [LOW] SFTP post-processing engine could not fetch the archive back to the server [PRO]
# [LOW] ANGIE: Fixed .htaccess parsing while restoring a WordPress site
# [LOW] ANGIE: Fixed removing installation directory while restoring a WordPress site

Akeeba Backup 4.0.5
================================================================================
# [HIGH] The integrated restoration is broken after the last security update

Akeeba Backup 4.0.4
================================================================================
! [SECURITY: Medium] Possibility of arbitrary file writing while a backup archive is being extracted by the integrated restoration feature

Akeeba Backup 4.0.3
================================================================================
+ Warn the user if the post-processing engine requires cURL extension and it's not enabled
! Backup failure on certain Windows hosts and PHP versions due to the way these versions handle file pointers
! Failure to post-process part files immediately on certain Windows hosts and PHP versions due to the way these versions handle file pointers

Akeeba Backup 4.0.2
================================================================================
! Dangling file pointer causing backup failure on certain Windows hosts
~ CloudFiles implementation changed to authentication API version 2.0, eliminating the need to choose your location
~ Old MySQL versions (5.1) would return randomly ordered rows when dumping MyISAM tables when the MySQL database is corrupt up to the kazoo and about to come crashing down in flames
~ Some servers seem to try and load the AkeebaUsagestats class twice
# [LOW] Database table exclusion table blank and backup errors when your db user doesn't have adequate privileges to show procedures, triggers or stored procedures in MySQL
# [LOW] Could not back up triggers, procedures and functions

Akeeba Backup 4.0.1
================================================================================
! A bug in SRP prevented updates unless you disabled it. Moreover, the data of the extensions were not saved in the SRP backup (also affects all 3.11.x versions of Akeeba Backup).

Akeeba Backup 4.0.0
================================================================================
+ Support for iDriveSync accounts created in 2014 or later
+ A different log file is created per backup attempt (and automatically removed when the backup archives are deleted by quotas or by using Delete Files in the interface)
+ You can now run several backups at the same time
+ The minimum execution time can now be enforced in the client side for backend backups, leading to increased stability on certain hosts
+ Back-end backups will resume after an AJAX error, allowing you to complete backups even on very hosts with very tight resource usage limits
+ The Dropbox chunked upload can now work on files smaller than 150Mb and will work across backup steps, allowing you to upload large files to Dropbox without timeout errors
+ Greatly improve the backup performance on massive tables as long as they have an auto_increment column
+ Work around the issues caused by some servers' error pages which contain DOM-modifying JavaScript
+ Support for the new MySQL (PDO) driver in Joomla! 3.4
+ Akeeba Backup now uses Joomla!'s Post-installation Messages on Joomla! 3.2.0 and later instead of showing its own post-installation page after a new install / upgrade to a new version
+ Anonymous reporting of PHP, MySQL and CMS versions (opt-out through the options)
- Removed obsolete ABI (Akeeba Backup Installer)
~ Even better workaround for very badly written system plugins which output Javascript without a trailing semicolon and/or newline, leading to Javascript errors. This is not a bug in our software, it's a bug in those badly written plugins and WE have to work around THEIR bad code!
~ Work around for the  overreaching password managers in so-called modern browsers which fill random, irrelevant passwords in the JPS and ANGIE password fields, without asking you and without notifying you, without letting developers tell them "no, do not autocomplete this field because you're doing it wrong and screwing with my clients, Mr. Stupid Browser".
# [HIGH] Dropbox upload would enter an infinite loop when using chunked uploads
# [MEDIUM] ANGIE: Restoring off-site directories would lead to errors
# [MEDIUM] Front-end backup failure on multilingual sites or when used in combination with certain third party plugins
# [MEDIUM] Joomla! caches plugin information, leading to unexpected behaviour in various places
# [MEDIUM] ANGIE for Joomla!, cannot detect Joomla! version, leading to Two Factor Auth data not being re-encoded with the new secret key
# [MEDIUM] ANGIE (all flavours): cannot restart db restoration after a database error has occurred.
# [LOW] ANGIE for WordPress, phpBB and PrestaShop: escape some special characters in passwords
# [LOW] ANGIE for Joomla!, array values in configuration.php were corrupted