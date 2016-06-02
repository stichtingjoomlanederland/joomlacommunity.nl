<?php die();?>
Akeeba Backup 5.1.0.b2
================================================================================
# [LOW] The CHANGELOG shown in the back-end is out of date (thanks @brianteeman)
# [LOW] The integrated restoration would appear to be in an infinite loop if an HTTP error occurred
# [HIGH] CLI scripts in old versions of Joomla were not working
# [HIGH] CLI scripts do not respect configuration overrides (bug in engine's initialization)
# [HIGH] Backups taken with JSON API would always be full site backups, ignoring your preferences

Akeeba Backup 5.1.0.b1
================================================================================
! Restoration was broken
! Integrated restoration was broken
+ Integrated restoration in Akeeba Backup Core
~ If environment information collection does not exist, do not fail (apparently the file to this feature is auto-deleted by some subpar hosts)
~ Chrome and Safari autofill the JPS key field in integrated restoration, usually with the WRONG password (e.g. your login password)
~ Less confusing buttons in integrated restoration
+ ANGIE for Joomla!: Option to set up Force SSL in Site Setup step
# [HIGH] Remote JSON API backups always using profile #1 despite reporting otherwise
# [LOW] The "How do I restore" modal appeared always until you configured the backup profile, no matter your preferences

Akeeba Backup 5.0.4
================================================================================
# [HIGH] FaLang erroneously makes Joomla! report that the active database driver is mysql instead of mysqli, causing the backup to fail on PHP 7. Now we try to detect and work around this badly written extension.
# [HIGH] Remote JSON API backups always using profile #1
# [MEDIUM] Obsolete .blade.php files from 5.0.0-5.0.2 were not being removed
# [LOW] Junk akeeba_AKEEBA_BACKUP_ORIGIN and akeeba.AKEEBA_BACKUP_ORIGIN files would be created by legacy front-end backup
# [LOW] Backup download confirmation message had escaped \n instead of newlines

Akeeba Backup 5.0.3
================================================================================
! Blade templates would not work on servers where the Toeknizer extension is disabled / not installed
# [HIGH] The backup on update plugin wouldn't let you update Joomla!
# [MEDIUM] The "Upload Kickstart" feature would fail
# [MEDIUM] Site Transfer Wizard: could not set Passive Mode
# [LOW] Testing the connection in Multiple Databases Definitions would not show you a success message
# [LOW] If saving the file and directories filters failed you would not receive an error message, it would just hang

Akeeba Backup 5.0.2
================================================================================
! Multipart backups are broken
# [HIGH] Remote JSON API backups would result in an error
# [HIGH] Front-end (remote) backups would always result in an error
# [MEDIUM] Sometimes the back-end wouldn't load complaining that a class is already loaded

Akeeba Backup 5.0.1
================================================================================
# [HIGH] The update sites are sometimes not refreshed when upgrading directly from Akeeba Backup 4.6 to 5.0
# [HIGH] The Quick Icon plugin does not work and disables itself
# [MEDIUM] Profile copy wasn't working
# [MEDIUM] The update sites in the XML manifest were wrong

Akeeba Backup 5.0.0
================================================================================
+ Automatic detection and working around of temporary data load failure
+ Improved detection and removal of duplicate update sites
+ Direct download link to Akeeba Kickstart in the Manage Backups page
+ Working around PHP opcode cache issues occurring right before the restoration starts if the old restoration.php configuration file was not removed
+ Schedule Automatic backups button is shown after the Configuration Wizard completes
+ Schedule Automatic backups button in the Configuration page's toolbar
+ Download log button from ALICE page
~ Remove obsolete FOF 2.x update site if it exists
~ Chrome won't let developers restore the values of password fields it ERRONEOUSLY auto-fills with random passwords. We worked around Google's atrocious bugs with extreme prejudice. You're welcome.
# [HIGH] Joomla! "Conservative" cache bug: you could not enter the Download ID when prompted
# [HIGH] Joomla! "Conservative" cache bug: you could not apply the proposed Secret Word when prompted
# [HIGH] Joomla! "Conservative" cache bug: component Options (e.g. Download ID, Secret Word, front-end backup feature) would be forgotten on the next page load
# [HIGH] Joomla! "Conservative" cache bug: the "How do I restore" popup can never be made to not display
# [MEDIUM] Fixed Rackspace CloudFiles when using a region different then London
# [LOW] Missing language strings in ALICE
