<?php die() ?>
Admin Tools 5.3.1
================================================================================
+ Display detected IP, country and continent inside the Geoblocking IP feature
~ Formerly unsupported Pro to Core downgrade with simultaneous version change no longer causes a site crash
# [MEDIUM] Failed backend login email always reports user as "Guest"
# [MEDIUM] Rescue mode was always disabled
# [LOW] Action log plugin would result in untranslated strings displayed in the Latest Actions backend module

Admin Tools 5.3.0
================================================================================
+ Protection of all component and plugin folders against direct web access
+ Disable access to backend users who haven't logged in for a long time (gh-188)
+ Updated default list of blocked User Agents
+ Joomla! 4 alpha 7 compatibility
~ Added PHP malware samples in the PHP File Change Scanner
# [LOW] Failed Login log entries would be deleted on upgrade even if they don't contained plaintext passwords

Admin Tools 5.2.1
================================================================================
+ Mark All as Safe button in the PHP File Change Scanner report viewer
# [MEDIUM] Super User list not cleared on deactivating the Monitor Super Users feature, requiring you to edit every Super User added in the meantime after re-enabling the feature
# [LOW] Fixed a fatal error if a security exception is triggered in the backend while using HTML templates
# [LOW] PHP File Change Scanner page: Possible Threats column included "Marked as Safe" files.
# [LOW] PHP File Change Scanner, Examine file view: pressing Cancel took you to a page with no records displayed

Admin Tools 5.2.0
================================================================================
+ Referrer Policy option for web.config Maker
+ Referrer Policy option for NginX Conf Maker
+ Rescue URL will tell you if you are using the wrong email address
+ Administrator IP whitelist, Never Block these IPs: you can now add dynamic IP domain names instead of IPs by prefixing them with @.
~ "Disable editing backend users' properties" will now let Joomla! 3.9 privacy consent to go through
~ "Forbid frontend Super Administrator login" will now let Joomla! 3.9 privacy consent to go through
~ Detect if the GeoIP Provider plugin was installed and then deleted but not uninstalled.
~ Cosmetic improvements to the PHP File Change Scanner progress modal
# [HIGH] NginX Conf Maker rules removed URL queries from requests without index.php in them
# [MEDIUM] Feature Monitor Critical Files was not working
# [MEDIUM] Monitor specific files feature was working only with the last item of the list
# [LOW] PHP 7.3 warning in the Control Panel page
# [LOW] URL Redirection required you to enter the subdirectory path when your site is located in a subdirectory instead of a path relative to the site's root URL. This made redirections non-portable.
# [LOW] Fixed JavaScript issues when Content-Security-Policy header is missing the unsafe-eval value
# [LOW] Missing filtering option in the Security Exception Logs page

Admin Tools 5.1.4
================================================================================
+ PHPShield feature now will block additional stream wrappers
- Remove IP workarounds "recommended setting" notice due to high rate of false detections
~ Removed legacy menu params that could cause issues with future versions of Joomla! (3.9 and later)
~ web.config Maker: add automatic dynamic compression of XML, XHTML and RSS documents
# [MEDIUM] The feature to check for leaked passwords was enabled for every user group, ignoring the settings
# [MEDIUM] HTTP Referer Policy language strings were untranslated because of the use of an extra "R" in the code
# [LOW] User re-activation email always displays the username as "Guest"
# [LOW] Options requiring .htaccess support shown to IIS and NginX users
# [LOW] web.config Maker: "Remove Apache and PHP version signature" causes a server error

Admin Tools 5.1.3
================================================================================
# [HIGH] IP Workarounds were always enabled
# [LOW] Control Panel graphs do not display on Safari due to Safari's quirky JavaScript date parsing
# [LOW] Admin Tools proposed secret URL parameter may start with a number
# [LOW] Prevent a security exception when the secret param is used and we're explicitly logging out

Admin Tools 5.1.2
================================================================================
! .htaccess Maker's front-end protection causes 403 errors (regression introduced in 5.1.1)

Admin Tools 5.1.1
================================================================================
# [HIGH] .htaccess Maker's front-end protection breaks sites using SEF URLs without URL rewrite, causing all URLs to report a 403 Forbidden error
# [MEDIUM] Fixed file scanning when file is inaccessible due to open_basedir restrictions
# [LOW] Grant access to robots.txt file when .htaccess Maker is used

Admin Tools 5.1.0
================================================================================
+ Added feature to manually unblock a specific IP
+ Added option to choose if Admin Tools should write the log file or not (required by GDPR)
+ Added option to allow only specific email domains during user registration
+ Added feature to check if user password is inside any dictionary of leaked passwords. This feature is disabled by default.
- Removed the option to log failed passwords. This could be a security risk since the information is stored unencrypted in the security exceptions database table.
~ Changed the debug log file type to .php. READ THE DOCUMENTATION. This may have implications on your host.
# [HIGH] .htaccess Maker does not block access to static media files for front-end directories which are not listed under "Frontend directories where file type exceptions are allowed"
# [MEDIUM] Critical Files Monitor feature was not enabled due to a SQL error
# [MEDIUM] Missing reason for Critical Files Monitor inside WAF Email Templates configuration
# [MEDIUM] Fixed blocking non authorized IPs in Emergency Offline Mode
# [LOW] Fixed default values for 404 Shield feature
# [LOW] Copy doesn't work in Redirect URL

Admin Tools 5.0.2
================================================================================
# [HIGH] .htaccess Maker causes 500 error on Apache 2.4 if mod_filter is not loaded and you enable the "Automatically compress static resources" feature
# [MEDIUM] WAF Blacklist rules were always applied, regardless if we were in the backend or the frontend
# [MEDIUM] Fixed Console Warn feature causing issues with RTL languages
# [LOW] The charts overflow their container and the legend is illegible
# [LOW] Fixed record editing in WAF Blacklist feature
# [LOW] Fixed toggling published status in list views in WAF Blacklist and WAF Templates pages
# [LOW] Feature 404 Shield was always enabled

Admin Tools 5.0.1
================================================================================
+ Warn users if either FOF or FEF is not installed
# [MEDIUM] Removed two detection rules which trigger a bug in older versions of PHP
# [LOW] Regression: "Repair & Optimize tables" button was linking to "Temp and log directory check" page
# [LOW] Fixed deleting a record from Auto IP Blocking History
# [LOW] Fixed integration between 404 Shield feature and com_redirect

Admin Tools 5.0.0
================================================================================
+ Purge Sessions now uses the Joomla! API to garbage collect expired sessions before forcibly truncating the sessions table.
# [LOW] Missing control for "Monitor Super User accounts" option in Configure WAF page
# [LOW] Regression: Administrator Password icon should reflect whether the feature is applied or not
# [LOW] Session table optimizer won't run on Joomla 3.8+

Admin Tools 5.0.0.b1
================================================================================
+ Rewritten interface using our brand new Akeeba Frontend Framework
+ Joomla! 4 compatibility
~ Simplified tab names in the Configure WAF page
# [LOW] Security exception not logged in the database if the URL is too long
# [LOW] Fixed fatal error while exporting empty list of Blacklisted Addresses

Admin Tools 4.3.2
================================================================================
+ Added 404Shield feature: accessing suspicious 404 pages (ie wp-login.php) will raise a security exception
+ Added Email PHP Exceptions feature: when a PHP exception is raised, an email will be sent with debug information
+ Display info about exceptions and PHP 7+ fatal errors in the backend
~ Disable "Monitor Super User accounts" feature by default. While it works just fine, users seem to be too confused by it. You MUST enable it manually if you need it (recommended!).
~ PHP 7.2 compatibility: renaming Object class to BaseObject in the PHP File Change Scanner engine
# [LOW] WAF Blacklist could cause login failure if you tried to upgrade Admin Tools by overwriting its files instead of going through the proper Joomla! extension update feature
# [LOW] The [RESCUEINFO] tag was not being replaced in the blocked request message
# [LOW] If you don't log a security exception reason but allow emails to be sent for it the email variables are not replaced (gh-147)
# [LOW] Whitelisted IPs can now add and edit users with manager or above permissions

Admin Tools 4.3.1
================================================================================
+ Added warning if HHVM is used instead of PHP
+ .htaccess / nginx.conf Maker: add support for WOFF2 in expiration time feature
+ Support for Joomla's task=viewName.taskName notation in the WAF Exceptions feature (gh-137)
+ Clicking on "Reload update information" will fix Joomla! erroneously reporting an update is available when you have the latest version installed
+ You can now define the timezone used when sending emails with security advice (gh-139)
+ Added Console Warning feature (inform the user to prevent Self XSS) (gh-136)
+ WAF Blacklist rules can now apply in just the frontend of your site (default), just the backend or both.
- Removed unused Options
~ Updates now use HTTPS URLs for both the XML update stream and the package download itself
# [MEDIUM] Rescue URL feature was not working
# [MEDIUM] Use of TraceEnable in .htaccess would always lead to 500 error. Now using a Rewrite rule instead.
# [LOW] Removed debug message when system plugin file is renamed
# [LOW] Fixed blocking a specific task with WAF Blacklist feature

Admin Tools 4.3.0
================================================================================
+ Rescue URL: unblock yourself easily if you get locked out of your site (you must be a Super User)
+ Workaround for Joomla! bug "Sometimes files are not copied on update"
+ Now you can send an email to multiple addresses when an IP is blocked
+ PHP File Change Scanner, option to suppress email when there are no suspicious, added or modified files gh-133
+ Notify about renamed main.php, offer to rename it back gh-131
~ Remove the component name from the backend pages' titles to prevent literal "ndash;" from appearing in the browser title
~ Workaround for INSECURE JoomlaShine plugins after they refused to fix their security issue for weeks.
~ Support for Sucuri firewall's non-standard client IP forwarding header
~ The update warning period was raised from 90 to 180 days (we release new Admin Tools feature releases approximately every four months)
# [HIGH] Missing closing tag would break generated web.config file
# [HIGH] Default WAF Blacklist rule blocks legitimate request (per ticket #28151)
# [HIGH] High resource usage by Monitor Super Users feature on high traffic sites (thank you Jaz and Anibal for the reports!)
# [LOW] Email erroneously sent after manually creating Super User in the backend Users page
# [LOW] Workaround for Joomla! bug 16147 (https://github.com/joomla/joomla-cms/issues/16147) - Cannot access component after installation when cache is enabled
# [LOW] Fixed saving log entry for "nonewfrontendadmins" and "nonewadmins" reasons
# [LOW] Fixed sending too many emails if multiple addresses are used to notify security exceptions
# [LOW] Fixed tooltips in WAF Configuration page

Admin Tools 4.2.0
================================================================================
! Yet another BIG Joomla! 3.7.0 bug throws an exception when using the CLI backup under some circumstances.
! Joomla! 3.7.0 has a broken System - Page Cache plugin leading to white pages and wrong redirections
- Removing the automatic update CLI script. Joomla! 3.7.0 can no longer execute extension installation under a CLI application.
# [HIGH] Follow symlinks option would not work in the NginX Maker due to a missing newline

Admin Tools 4.1.3
================================================================================
! The workaround to Joomla! 3.7's date bugs could cause a blank / error page (with an error about double timezone) under some circumstances.

Admin Tools 4.1.2
================================================================================
! Joomla! 3.7.0 broke backwards compatibility again, making CLI scripts fail.
! Joomla! 3.7.0 broke the JDate package, effectively ignoring timezones, causing grave errors in date / time calculations and display
+ gh-115 Monitor critical files and receive an email when they change
+ gh-116 Monitor Super Users, receive an email when Super Users are added outside of Joomla's Users page and automatically block them
~ Record creation date of .htaccess / nginx.conf / web.config in the user's local timezone instead of GMT in the file's header
~ PHP File Change Scanners now display dates and times in the user's local timezone
~ Show local timezone in the security exceptions and auto-blocked IP addresses views
~ Suppress misleading login failure emails for blacklisted IPs.
~ Suppress the Joomla mail system's error message when trying to send an email to an email which is empty or invalid
~ Disable RFIShield when it's not necessary. See https://www.akeebabackup.com/home/news/1674-not-a-vulnerability-in-admin-tools.html
~ Workaround for badly configured servers which print out notices before we have the chance to set the error reporting
~ Workaround for NginX choking on CRLF line endings if they are accidentally present in the NginX Conf Maker generated file
# [LOW] Work around Joomla bugs which prevented the CHANGELOG from being rendered correctly in the Control Panel page
# [LOW] www to non-www redirection sometimes fails if the site is in a subdirectory
# [LOW] Joomla! 3.7 added a fixed width to specific button classes in the toolbar, breaking the page layout
# [LOW] Fixed detecting if IP workarounds are needed during QuickStart

Admin Tools 4.1.1
================================================================================
! PHP fatal error if the blocked IP was from a private network and you didn't have any plugin loading FOF 3 already installed

Admin Tools 4.1.0
================================================================================
+ Warning (with documentation link) when you have too many blacklisted IPs
+ gh-109 Include the whole request in the file log while using the WAF Blacklist feature
+ gh-111 Suggest turning on Enable IP Workarounds when we see local network IPs being automatically blocked
+ gh-112 Prevent backend account creation from the frontend of the site
+ gh-114 Email when Global Configuration and / or component Options changes take place
+ gh-117 Prevent the plugin from being disabled through the Plugins Manager
+ gh-118 Block requests using PHP Wrappers
# [MEDIUM] gh-120 Fixed creating scan diffs while running the scanner from CLI
# [LOW] Fixed ordering in Scan results page
# [LOW] gh-124 Do not enable link migration if we don't have a list of old domains

Admin Tools 4.0.2
================================================================================
+ Added warning if database updates are stuck due to table corruption
+ Added font files to the expiration optimization features of NignX and Htaccess Maker
+ Added a Published field to the WAF Blacklist records, allowing you to enable/disable rules
+ Added mitigation for user registration exploit in Joomla! 3.4.4 to 3.6.3 (inclusive). The mitigation rule is added in the WAF Blacklist feature.
~ .htaccess Maker: Remove default expiration time for HTML documents because it conflicts with back-end editing
# [HIGH] NginX Conf Maker generates invalid code for allowed PHP files
# [HIGH] WAF Blacklist was incompatible with SEF URLs as it was being triggered onAfterInitialize instead of onAfterRoute
# [MEDIUM] Web.config Maker: Block access from specific user agents feature causes a 500 Internal Server Error
# [MEDIUM] Fixed custom HTML template used to display the block message
# [LOW] The automatic redirection to HTTP when you're using the HSTS header in the .htaccess Maker would end up in an invalid URL on some servers with a bad configuration
# [LOW] The Security Exceptions page had the wrong default ordering
# [LOW] Fixed modal windows when graphs are not displayed in the Control Panel
# [LOW] NginX Conf Maker and Web.config Maker claim they are not supported when they actually are
# [LOW] NginX Conf Maker: “Optimise file handle cache” and “Optimise output buffering” options were mixed up
# [LOW] Removed unused option in “Admin Tools Joomla! Update Email” system plugin
# [LOW] Could not preview generated nginx.conf and web.config

Admin Tools 4.0.1
================================================================================
! YOU MUST UPDATE TO THIS RELEASE MANUALLY. The Download ID was not registered with Joomla, making updates from 4.0.0.b1, 4.0.0.b2, 4.0.0.rc1 and 4.0.0 impossible.
# [MEDIUM] Master password does not work with passwords containing special characters
# [MEDIUM] Configure Permissions does not work with folder names containing special characters
# [MEDIUM] Administrator Password Protection does not work with passwords containing special characters
# [MEDIUM] PHP File Scanner's front-end scanning feature does not work with secret keys containing special characters
# [LOW] If you were living under a rock and hadn't updated to Admin Tools 3.6 or later the last 2 years you may get a fatal error after update to 4.0
# [LOW] Layout / Javascript issues with IE11 on the Configure WAF page

Admin Tools 4.0.0
================================================================================
+ Improved performance while importing settings with thousands of IP addresses
+ Added option to display hidden files in Permissions Configuration page
# [LOW] Missing language key for user signup notes

Admin Tools 4.0.0.rc1
================================================================================
~ Changing the #__akeeba_common table schema
~ Exceptions from WAF are not available on broken servers which don't set the HTTP_HOST environment variable
# [HIGH] The PHP File Change Scanner front-end scheduling feature was broken
# [HIGH] The Professional package was missing the CLI scripts
# [MEDIUM] The Quick Setup Wizard would claim to have already run even on fresh installations (thanks @brianteeman)
# [LOW] Missing language strings
# [LOW] PHP File Change Scanner emails (from CLI) displayed the HTML markup instead of formatted text
# [LOW] The “Reload update information” button in the control panel page resulted in an error
# [LOW] Incorrect language strings, thank you @brianteeman
# [LOW] The warnings about the IP white/blacklist being disabled were displayed under the wrong conditions
# [LOW] PHP notices on installation from missing method arguments

Admin Tools 4.0.0.b1
================================================================================
+ Rewritten using FOF 3.0
+ Improved detection and removal of duplicate update sites
+ You can export/import WAF Blacklist and Exceptions (always forbidden and always allowed component/view/task access)
+ Highlight the suspicious and malicious matches on the file source in the PHP File Change Scanner results
+ IP whitelist will warn you when the feature is not enabled
+ IP blacklist will warn you when the feature is not enabled
+ .htaccess / nginx.conf / web.config maker: added .well-known to the default list of allowed access folders for non-PHP files
+ Enabling HSTS in .htaccess Maker will now also avoid unsafe (HTTP) redirections wherever possible and not send the HSTS header over plain HTTP. Does not apply to NginX Conf and web.config Makers. PLEASE READ THE DOCUMENTATION!
+ URL Redirection is now available in the free of charge Core release
~ Extremely conservative .htaccess Maker settings applied by the Quick Setup Wizard because people don't bother reading the big, fat warning above the apply button
~ Joomla! 3.6 has moved the logs folder inside /administrator. Our software is now adjusted for this change.
~ Warn about eAccelerator
~ Warn about end of life PHP versions
~ Remove obsolete FOF 2.x update site if it exists
# [LOW] Joomla bug would cause URL redirections to issue HTTP 303 See other (temporary) redirection instead of 301 Moved (permanent) redirection
# [HIGH] WAF Blacklist with RegEx matching would block all requests all the time
# [HIGH] Joomla! "Conservative" cache bug: you could not enter the Download ID when prompted
# [HIGH] Joomla! "Conservative" cache bug: you could not apply the proposed Secret Word when prompted
# [HIGH] Joomla! "Conservative" cache bug: component Options (e.g. Download ID, Secret Word, front-end file scanner feature) would be forgotten on the next page load
