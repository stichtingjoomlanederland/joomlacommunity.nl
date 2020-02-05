<?php die();?>
Akeeba Backup 7.0.1
================================================================================
- pCloud: removing download to browser (cannot work properly due to undocumented API restrictions)
# [HIGH] An error about not being able to open a file with an empty name occurs when taking a SQL-only backup but there's a row over 1MB big
# [LOW] Schedule Automatic Backups shown in the Configuration page of the Core version
# [LOW] A secret work would not be proposed when one was not set or set to something insecure
# [LOW] The akeeba-altbackup.php and akeeba-altcheck-failed.php CRON scripts falsely report front-end backup is not enabled
# [LOW] Dark Mode: modal close icon was invisible both in the backup software and during restoration
# [LOW] Fixed automatically filling DropBox tokens after OAuth authentication

Akeeba Backup 7.0.0
================================================================================
+ Custom description for backups taken with the Backup on Update plugin
+ Remove TABLESPACE and DATA|INDEX DIRECTORY table options during backup
# [LOW] Fixed applying quotas for obsolete backups

Akeeba Backup 7.0.0.rc1
================================================================================
+ Upload to OVH now supports Keystone v3 authentication, mandatory starting mid-January 2020
# [HIGH] An error in an early backup domain could result in a forever-running backup
# [HIGH] DB connection errors wouldn't result in the backup failing, as it should be doing

Akeeba Backup 7.0.0.b3
================================================================================
+ Common PHP version warning scripts
+ Reinstated support for pCloud after they fixed their OAuth2 server
~ Improved Dark Mode
~ Improved PHP 7.4 compatibility
~ Improved Joomla 4 styling
~ Clearer message when setting decryption fails in CLI backup script
~ Remove JavaScript eval() from FileFilters page
# [HIGH] The database dump was broken with some versions of PCRE (e.g. the one distributed with Ubuntu 18.04)
# [HIGH] Site Transfer Wizard inaccessible on case-sensitive filesystems

Akeeba Backup 7.0.0.b2
================================================================================
- Removed pCloud support
+ ANGIE: Options to remove AddHandler lines on restoration
# [MEDIUM] Fixed OAuth authentication flow
# [MEDIUM] Fixed fatal error under Joomla 3.8.x

Akeeba Backup 7.0.0.b1
================================================================================
+ Amazon S3 now supports Bahrain and Stockholm regions
+ Amazon S3 now supports Intelligent Tiering, Glacier and Deep Archive storage classes
+ Google Storage now supports the nearline and coldline storage classes
+ Manage Backups: Improved performance of the Transfer (re-upload to remote storage) feature
+ Windows Azure BLOB Storage: download back to server and download to browser are now supported
+ New OneDrive integration supports both regular OneDrive and OneDrive for Business
+ pCloud support
+ Support for Dropbox for Business
+ Dark Mode support
+ Support for Joomla 4 Download Key management in the Update Sites page
+ Minimum required PHP version is now 5.6.0
~ All views have been converted to Blade for easier development and better future-proofing
~ The integrated restoration feature is now only available in the Professional version
~ The archive integrity check feature is now only available in the Professional version
~ The front-end legacy backup API and the Remote JSON API are now available only in the Professional version and can be enabled / disabled independently of each other
~ The Site Transfer Wizard is now only available in the Professional version
~ SugarSync integration: you now need to provide your own access keys following the documentation instructions
~ Backup error handling and reporting (to the log and to the interface) during backup has been improved.
~ The Test FTP/SFTP Connection buttons now return much more informative error messages.
~ Manage Backups: much more informative error messages if the Transfer to remote storage process fails.
~ The backup and log IDs will follow the numbering you see in the left hand column of the Manage Backups page.
~ Manage Backups: The Remote File Management page is now giving better, more accurate information.
~ Manage Backups: Fetch Back To Server was rewritten to gracefully deal with more problematic cases.
~ Joomla 4: The backup on update plugin no longer displayed correctly after J4 changed its template, again.
~ Joomla 4: The backup quick icon was displayed in the wrong place after J4 changed its template, again and also partially broke backwards compatibility to how quick icon plugins work.
~ Removed AES encapsulations from the JSON API for security reasons. We recommend you always use HTTPS with the JSON API.
# [HIGH] CLI (CRON) scripts could sometimes stop with a Joomla crash due to Joomla's mishandling of the session under CLI.
# [HIGH] Changing the database prefix would not change it in the referenced tables inside PROCEDUREs, FUNCTIONs and TRIGGERs
# [HIGH] Backing up PROCEDUREs, FUNCTIONs and TRIGGERs was broken
# [MEDIUM] Database only backup of PROCEDUREs, FUNCTIONs and TRIGGERs does not output the necessary DELIMITER commands to allow direct import
# [MEDIUM] PHP Notice at the end of each backup step due to double attempt to close the database connection.
# [MEDIUM] BackBlaze B2: upload error when chunk size is higher than the backup archive's file size
# [LOW] Manage Backups: downloading a part file from S3 beginning with text data would result in inline display of the file instead of download.
