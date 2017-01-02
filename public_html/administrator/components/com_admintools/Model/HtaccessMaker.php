<?php
/**
 * @package   AdminTools
 * @copyright 2010-2016 Akeeba Ltd / Nicholas K. Dionysopoulos
 * @license   GNU General Public License version 3, or later
 */

namespace Akeeba\AdminTools\Admin\Model;

defined('_JEXEC') or die;

use Akeeba\AdminTools\Admin\Helper\ServerTechnology;
use JLoader;

class HtaccessMaker extends ServerConfigMaker
{
	/**
	 * The default configuration of this feature.
	 *
	 * Note that you define an array. It becomes an object in the constructor. We have to do that since PHP doesn't
	 * allow the intitialisation of anonymous objects (like e.g. Javascript) but lets us typecast an array to an object
	 * – just not in the property declaration!
	 *
	 * @var  object
	 */
	public $defaultConfig = [
		// == System configuration ==
		// Host name for HTTPS requests (without https://)
		'httpshost'           => '',
		// Host name for HTTP requests (without http://)
		'httphost'            => '',
		// Follow symlinks (may cause a blank page or 500 Internal Server Error)
		'symlinks'            => 0,
		// Base directory of your site (/ for domain's root)
		'rewritebase'         => '',

		// == Optimization and utility ==
		// Force index.php parsing before index.html
		'fileorder'           => 1,
		// Set default expiration time to 1 hour
		'exptime'             => 0,
		// Automatically compress static resources
		'autocompress'        => 0,
		// Force GZip compression for mangled Accept-Encoding headers
		'forcegzip'           => 1,
		// Redirect index.php to root
		'autoroot'            => 1,
		// Redirect www and non-www addresses
		'wwwredir'            => 0,
		// Redirect old to new domain
		'olddomain'           => '',
		// Force HTTPS for these URLs
		'httpsurls'           => array(),
		// HSTS Header (for HTTPS-only sites)
		'hstsheader'          => 0,
		// Disable HTTP methods TRACE and TRACK (protect against XST)
		'notracetrack'        => 0,
		// Cross-Origin Resource Sharing (CORS)
		'cors'                => 0,
		// Set UTF-8 charset as default
		'utf8charset'         => 1,
		// Send ETag
		'etagtype'            => 'default',

		// == Basic security ==
		// Disable directory listings
		'nodirlists'          => 1,
		// Protect against common file injection attacks
		'fileinj'             => 1,
		// Disable PHP Easter Eggs
		'phpeaster'           => 1,
		// Block access from specific user agents
		'nohoggers'           => 0,
		// Block access to configuration.php-dist and htaccess.txt
		'leftovers'           => 1,
		// Protect against clickjacking
		'clickjacking'        => 1,
		// Reduce MIME type security risks
		'reducemimetyperisks' => 1,
		// Reflected XSS prevention
		'reflectedxss'        => 1,
		// Remove Apache and PHP version signature
		'noserversignature'   => 1,
		// Prevent content transformation
		'notransform'         => 1,
		// User agents to block (one per line)
		'hoggeragents'        => array(
			'WebBandit',
			'webbandit',
			'Acunetix',
			'binlar',
			'BlackWidow',
			'Bolt 0',
			'Bot mailto:craftbot@yahoo.com',
			'BOT for JCE',
			'casper',
			'checkprivacy',
			'ChinaClaw',
			'clshttp',
			'cmsworldmap',
			'comodo',
			'Custo',
			'Default Browser 0',
			'diavol',
			'DIIbot',
			'DISCo',
			'dotbot',
			'Download Demon',
			'eCatch',
			'EirGrabber',
			'EmailCollector',
			'EmailSiphon',
			'EmailWolf',
			'Express WebPictures',
			'extract',
			'ExtractorPro',
			'EyeNetIE',
			'feedfinder',
			'FHscan',
			'FlashGet',
			'flicky',
			'GetRight',
			'GetWeb!',
			'Go-Ahead-Got-It',
			'Go!Zilla',
			'grab',
			'GrabNet',
			'Grafula',
			'harvest',
			'HMView',
			'ia_archiver',
			'Image Stripper',
			'Image Sucker',
			'InterGET',
			'Internet Ninja',
			'InternetSeer.com',
			'jakarta',
			'Java',
			'JetCar',
			'JOC Web Spider',
			'kmccrew',
			'larbin',
			'LeechFTP',
			'libwww',
			'Mass Downloader',
			'Maxthon$',
			'microsoft.url',
			'MIDown tool',
			'miner',
			'Mister PiX',
			'NEWT',
			'MSFrontPage',
			'Navroad',
			'NearSite',
			'Net Vampire',
			'NetAnts',
			'NetSpider',
			'NetZIP',
			'nutch',
			'Octopus',
			'Offline Explorer',
			'Offline Navigator',
			'PageGrabber',
			'Papa Foto',
			'pavuk',
			'pcBrowser',
			'PeoplePal',
			'planetwork',
			'psbot',
			'purebot',
			'pycurl',
			'RealDownload',
			'ReGet',
			'Rippers 0',
			'SeaMonkey$',
			'sitecheck.internetseer.com',
			'SiteSnagger',
			'skygrid',
			'SmartDownload',
			'sucker',
			'SuperBot',
			'SuperHTTP',
			'Surfbot',
			'tAkeOut',
			'Teleport Pro',
			'Toata dragostea mea pentru diavola',
			'turnit',
			'vikspider',
			'VoidEYE',
			'Web Image Collector',
			'Web Sucker',
			'WebAuto',
			'WebCopier',
			'WebFetch',
			'WebGo IS',
			'WebLeacher',
			'WebReaper',
			'WebSauger',
			'Website eXtractor',
			'Website Quester',
			'WebStripper',
			'WebWhacker',
			'WebZIP',
			'Wget',
			'Widow',
			'WWW-Mechanize',
			'WWWOFFLE',
			'Xaldon WebSpider',
			'Yandex',
			'Zeus',
			'zmeu',
			'CazoodleBot',
			'discobot',
			'ecxi',
			'GT::WWW',
			'heritrix',
			'HTTP::Lite',
			'HTTrack',
			'ia_archiver',
			'id-search',
			'id-search.org',
			'IDBot',
			'Indy Library',
			'IRLbot',
			'ISC Systems iRc Search 2.1',
			'LinksManager.com_bot',
			'linkwalker',
			'lwp-trivial',
			'MFC_Tear_Sample',
			'Microsoft URL Control',
			'Missigua Locator',
			'panscient.com',
			'PECL::HTTP',
			'PHPCrawl',
			'PleaseCrawl',
			'SBIder',
			'Snoopy',
			'Steeler',
			'URI::Fetch',
			'urllib',
			'Web Sucker',
			'webalta',
			'WebCollage',
			'Wells Search II',
			'WEP Search',
			'zermelo',
			'ZyBorg',
			'Indy Library',
			'libwww-perl',
			'Go!Zilla',
			'TurnitinBot',
		),

		// == Server protection ==
		// -- Toggle protection
		// Back-end protection
		'backendprot'         => 1,
		// Back-end protection
		'frontendprot'        => 1,
		// -- Fine-tuning
		// Back-end directories where file type exceptions are allowed
		'bepexdirs'           => array('components', 'modules', 'templates', 'images', 'plugins'),
		// Back-end file types allowed in selected directories
		'bepextypes'          => array(
			'jpe', 'jpg', 'jpeg', 'jp2', 'jpe2', 'png', 'gif', 'bmp', 'css', 'js',
			'swf', 'html', 'mpg', 'mp3', 'mpeg', 'mp4', 'avi', 'wav', 'ogg', 'ogv',
			'xls', 'xlsx', 'doc', 'docx', 'ppt', 'pptx', 'zip', 'rar', 'pdf', 'xps',
			'txt', '7z', 'svg', 'odt', 'ods', 'odp', 'flv', 'mov', 'htm', 'ttf',
			'woff', 'woff2', 'eot',
			'JPG', 'JPEG', 'PNG', 'GIF', 'CSS', 'JS', 'TTF', 'WOFF', 'WOFF2', 'EOT'
		),
		// Front-end directories where file type exceptions are allowed
		'fepexdirs'           => array('components', 'modules', 'templates', 'images', 'plugins', 'media', 'libraries',
			'media/jui/fonts'),
		// Front-end file types allowed in selected directories
		'fepextypes'          => array(
			'jpe', 'jpg', 'jpeg', 'jp2', 'jpe2', 'png', 'gif', 'bmp', 'css', 'js',
			'swf', 'html', 'mpg', 'mp3', 'mpeg', 'mp4', 'avi', 'wav', 'ogg', 'ogv',
			'xls', 'xlsx', 'doc', 'docx', 'ppt', 'pptx', 'zip', 'rar', 'pdf', 'xps',
			'txt', '7z', 'svg', 'odt', 'ods', 'odp', 'flv', 'mov', 'ico', 'htm',
			'ttf', 'woff', 'woff2', 'eot',
			'JPG', 'JPEG', 'PNG', 'GIF', 'CSS', 'JS', 'TTF', 'WOFF', 'WOFF2', 'EOT'
		),
		// -- Exceptions
		// Allow direct access to these files
		'exceptionfiles'      => array(
			"administrator/components/com_akeeba/restore.php",
			"administrator/components/com_admintools/restore.php",
			"administrator/components/com_joomlaupdate/restore.php"
		),
		// Allow direct access, except .php files, to these directories
		'exceptiondirs'       => array(
			'.well-known'
		),
		// Allow direct access, including .php files, to these directories
		'fullaccessdirs'      => array(
			"templates/your_template_name_here"
		),

		// == Custom .htaccess rules ==
		// At the top of the file
		'custhead'            => '',
		// At the bottom of the file
		'custfoot'            => ''
	];

	/**
	 * The current configuration of this feature
	 *
	 * @var  object
	 */
	protected $configKey = 'htconfig';

	/**
	 * The methods which are allowed to call the saveConfiguration method. Each line is in the format:
	 * Full\Class\Name::methodName
	 *
	 * @var  array
	 */
	protected $allowedCallersForSave = [
		'Akeeba\AdminTools\Admin\Controller\ServerConfigMaker::apply',
		'Akeeba\AdminTools\Admin\Controller\ServerConfigMaker::save',
		'Akeeba\AdminTools\Admin\Controller\HtaccessMaker::apply',
		'Akeeba\AdminTools\Admin\Controller\HtaccessMaker::save',
		'Akeeba\AdminTools\Admin\Model\QuickStart::applyHtmaker',
	];

	/**
	 * The methods which are allowed to call the writeConfigFile method. Each line is in the format:
	 * Full\Class\Name::methodName
	 *
	 * @var  array
	 */
	protected $allowedCallersForWrite = [
		'Akeeba\AdminTools\Admin\Controller\ServerConfigMaker::apply',
		'Akeeba\AdminTools\Admin\Controller\HtaccessMaker::apply',
		'Akeeba\AdminTools\Admin\Model\QuickStart::applyHtmaker'
	];

	/**
	 * The methods which are allowed to call the makeConfigFile method. Each line is in the format:
	 * Full\Class\Name::methodName
	 *
	 * @var  array
	 */
	protected $allowedCallersForMake = [
		'Akeeba\AdminTools\Admin\Controller\ServerConfigMaker::apply',
		'Akeeba\AdminTools\Admin\Controller\HtaccessMaker::apply',
		'Akeeba\AdminTools\Admin\Model\ServerConfigMaker::writeConfigFile',
		'Akeeba\AdminTools\Admin\Model\HtaccessMaker::writeHtaccess',
		'Akeeba\AdminTools\Admin\View\HtaccessMaker\Html::onBeforePreview'
	];

	/**
	 * The base name of the configuration file being saved by this feature, e.g. ".htaccess". The file is always saved
	 * in the site's root. Any old files under that name are renamed with a .admintools suffix.
	 *
	 * @var string
	 */
	protected $configFileName = '.htaccess';

	/**
	 * Compile and return the contents of the .htaccess configuration file
	 *
	 * @return  string
	 */
	public function makeConfigFile()
	{
		// Make sure we are called by an expected caller
		ServerTechnology::checkCaller($this->allowedCallersForMake);

		// Guess Apache features
		$apacheVersion = $this->apacheVersion();
		$serverCaps    = (object)array(
			'customCodes' => version_compare($apacheVersion, '2.2', 'ge'), // Custom redirections, e.g. R=301
			'deflate'     => version_compare($apacheVersion, '2.0', 'ge') // mod_deflate support
		);
		$redirCode     = $serverCaps->customCodes ? '[R=301,L]' : '[R,L]';

		JLoader::import('joomla.utilities.date');
		$date     = new \JDate();
		$d        = $date->format('Y-m-d H:i:s', true);
		$version  = ADMINTOOLS_VERSION;
		$htaccess = <<<END
### ===========================================================================
### Security Enhanced & Highly Optimized .htaccess File for Joomla!
### automatically generated by Admin Tools $version on $d GMT
### Auto-detected Apache version: $apacheVersion (best guess)
### ===========================================================================
###
### The contents of this file are based on the same author's work "Master
### .htaccess".
###
### Admin Tools is Free Software, distributed under the terms of the GNU
### General Public License version 3 or, at your option, any later version
### published by the Free Software Foundation.
###
### !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!! IMPORTANT !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
### !!                                                                       !!
### !!  If you get an Internal Server Error 500 or a blank page when trying  !!
### !!  to access your site, remove this file and try tweaking its settings  !!
### !!  in the back-end of the Admin Tools component.                        !!
### !!                                                                       !!
### !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
###


END;

		$config = $this->loadConfiguration();

		// Is HSTS enabled?
		$hasHSTS = $config->hstsheader == 1;

		$htaccess .= "##### RewriteEngine enabled - BEGIN\n";
		$htaccess .= "RewriteEngine On\n";
		$htaccess .= "##### RewriteEngine enabled - END\n\n";

		$rewritebase = $config->rewritebase;

		if (!empty($rewritebase))
		{
			$htaccess .= "##### RewriteBase set - BEGIN\n";
			$rewritebase = trim($rewritebase, '/');
			$htaccess .= "RewriteBase /$rewritebase\n";
			$htaccess .= "##### RewriteBase set - END\n\n";
		}

		if ($hasHSTS)
		{
			$httpsHost = $config->httpshost;
			$htaccess .= <<< END
##### HTTP to HTTPS redirection
## Since you have enabled HSTS the first redirection rule will instruct the browser to visit the HTTPS version of your
## site. This prevents unsafe redirections through HTTP.
RewriteCond %{HTTPS} !=on [OR]
RewriteCond %{HTTP:X-Forwarded-Proto} =http
RewriteRule .* https://$httpsHost%{REQUEST_URI} [L,R=301]


END;
		}

		if (!empty($config->custhead))
		{
			$htaccess .= "##### Custom Rules (Top of File) -- BEGIN\n";
			$htaccess .= $config->custhead . "\n";
			$htaccess .= "##### Custom Rules (Top of File) -- END\n\n";
		}

		if ($config->fileorder == 1)
		{
			$htaccess .= "##### File execution order -- BEGIN\n";
			$htaccess .= "DirectoryIndex index.php index.html\n";
			$htaccess .= "##### File execution order -- END\n\n";
		}

		if ($config->nodirlists == 1)
		{
			$htaccess .= "##### No directory listings -- BEGIN\n";
			$htaccess .= "IndexIgnore *\n";

			switch ($config->symlinks)
			{
				case 0:
					$htaccess .= "Options -Indexes\n";
					break;

				case 1:
					$htaccess .= "Options -Indexes +FollowSymLinks\n";
					break;

				case 2:
					$htaccess .= "Options -Indexes +SymLinksIfOwnerMatch\n";
					break;
			}

			$htaccess .= "##### No directory listings -- END\n\n";
		}
		elseif ($config->symlinks != 0)
		{
			$htaccess .= "##### Follow symlinks -- BEGIN\n";

			switch ($config->symlinks)
			{
				case 1:
					$htaccess .= "Options +FollowSymLinks\n";
					break;

				case 2:
					$htaccess .= "Options +SymLinksIfOwnerMatch\n";
					break;
			}

			$htaccess .= "##### Follow symlinks -- END\n\n";
		}

		if ($config->exptime == 1)
		{
			$htaccess .= <<<END
##### Optimal default expiration time - BEGIN
<IfModule mod_expires.c>
	# Enable expiration control
	ExpiresActive On

	# CSS and JS expiration: 1 week after request
	ExpiresByType text/css "now plus 1 week"
	ExpiresByType application/javascript "now plus 1 week"
	ExpiresByType application/x-javascript "now plus 1 week"

	# Image files expiration: 1 month after request
	ExpiresByType image/bmp "now plus 1 month"
	ExpiresByType image/gif "now plus 1 month"
	ExpiresByType image/jpeg "now plus 1 month"
	ExpiresByType image/jp2 "now plus 1 month"
	ExpiresByType image/pipeg "now plus 1 month"
	ExpiresByType image/png "now plus 1 month"
	ExpiresByType image/svg+xml "now plus 1 month"
	ExpiresByType image/tiff "now plus 1 month"
	ExpiresByType image/vnd.microsoft.icon "now plus 1 month"
	ExpiresByType image/x-icon "now plus 1 month"
	ExpiresByType image/ico "now plus 1 month"
	ExpiresByType image/icon "now plus 1 month"
	ExpiresByType text/ico "now plus 1 month"
	ExpiresByType application/ico "now plus 1 month"
	ExpiresByType image/vnd.wap.wbmp "now plus 1 month"
	ExpiresByType application/vnd.wap.wbxml "now plus 1 month"
	ExpiresByType application/smil "now plus 1 month"
	
	# Font files expiration: 1 week after request
	ExpiresByType application/vnd.ms-fontobject "now plus 1 week"
	ExpiresByType application/x-font-ttf "now plus 1 week"
	ExpiresByType application/x-font-opentype "now plus 1 week"
	ExpiresByType application/x-font-woff "now plus 1 week"
	ExpiresByType image/svg+xml "now plus 1 week"

	# Audio files expiration: 1 month after request
	ExpiresByType audio/ogg "now plus 1 month"
	ExpiresByType application/ogg "now plus 1 month"
	ExpiresByType audio/basic "now plus 1 month"
	ExpiresByType audio/mid "now plus 1 month"
	ExpiresByType audio/midi "now plus 1 month"
	ExpiresByType audio/mpeg "now plus 1 month"
	ExpiresByType audio/mp3 "now plus 1 month"
	ExpiresByType audio/x-aiff "now plus 1 month"
	ExpiresByType audio/x-mpegurl "now plus 1 month"
	ExpiresByType audio/x-pn-realaudio "now plus 1 month"
	ExpiresByType audio/x-wav "now plus 1 month"

	# Movie files expiration: 1 month after request
	ExpiresByType application/x-shockwave-flash "now plus 1 month"
	ExpiresByType x-world/x-vrml "now plus 1 month"
	ExpiresByType video/x-msvideo "now plus 1 month"
	ExpiresByType video/mpeg "now plus 1 month"
	ExpiresByType video/mp4 "now plus 1 month"
	ExpiresByType video/quicktime "now plus 1 month"
	ExpiresByType video/x-la-asf "now plus 1 month"
	ExpiresByType video/x-ms-asf "now plus 1 month"
</IfModule>
##### Optimal default expiration time - END


END;
		}

		if (!empty($config->hoggeragents) && ($config->nohoggers == 1))
		{
			$htaccess .= "##### Common hacking tools and bandwidth hoggers block -- BEGIN\n";

			foreach ($config->hoggeragents as $agent)
			{
				$htaccess .= "SetEnvIf user-agent \"$agent\" stayout=1\n";
			}

			$htaccess .= <<< HTACCESS
<IfModule !mod_authz_core.c>
deny from env=stayout
</IfModule>
<IfModule mod_authz_core.c>
  <RequireAll>
	Require all granted
	Require not env stayout
  </RequireAll>
</IfModule>
##### Common hacking tools and bandwidth hoggers block -- END


HTACCESS;
		}

		if (($config->autocompress == 1) && ($serverCaps->deflate))
		{
			$htaccess .= <<<ENDHTCODE
##### Automatic compression of resources -- BEGIN
<ifmodule mod_deflate.c>
AddOutputFilterByType DEFLATE text/plain text/xml text/css application/xml application/xhtml+xml application/rss+xml application/javascript application/x-javascript image/svg+xml
</ifmodule>
<ifModule mod_gzip.c>
mod_gzip_on Yes
mod_gzip_dechunk Yes
mod_gzip_keep_workfiles No
mod_gzip_can_negotiate Yes
mod_gzip_add_header_count Yes
mod_gzip_send_vary Yes
mod_gzip_min_http 1000
mod_gzip_minimum_file_size 300
mod_gzip_maximum_file_size 512000
mod_gzip_maximum_inmem_size 60000
mod_gzip_handle_methods GET
mod_gzip_item_include file \.(html?|txt|css|js|php|pl|xml|rb|py|svg|scgz)$
mod_gzip_item_include mime ^text/plain$
mod_gzip_item_include mime ^text/xml$
mod_gzip_item_include mime ^text/css$
mod_gzip_item_include mime ^application/xml$
mod_gzip_item_include mime ^application/xhtml+xml$
mod_gzip_item_include mime ^application/rss+xml$
mod_gzip_item_include mime ^application/javascript$
mod_gzip_item_include mime ^application/x-javascript$
mod_gzip_item_include mime ^image/svg+xml$
mod_gzip_item_exclude rspheader ^Content-Encoding:.*gzip.*
mod_gzip_item_include handler ^cgi-script$
mod_gzip_item_include handler ^server-status$
mod_gzip_item_include handler ^server-info$
mod_gzip_item_include handler ^application/x-httpd-php
mod_gzip_item_exclude mime ^image/.*
</ifmodule>
##### Automatic compression of resources -- END

ENDHTCODE;

			if ($config->forcegzip == 1)
			{
				$htaccess .= <<< HTACCESS
## Force GZip compression for mangled Accept-Encoding headers
<IfModule mod_setenvif.c>
	<IfModule mod_headers.c>
		SetEnvIfNoCase ^(Accept-EncodXng|X-cept-Encoding|X{15}|~{15}|-{15})$ ^((gzip|deflate)\s*,?\s*)+|[X~-]{4,13}$ HAVE_Accept-Encoding
		RequestHeader append Accept-Encoding "gzip,deflate" env=HAVE_Accept-Encoding
	</IfModule>
</IfModule>

HTACCESS;
			}
		}

		if ($config->etagtype != 'default')
		{
			$htaccess .= "## Send ETag (selected method: {$config->etagtype})\n";

			switch ($config->etagtype)
			{
				case 'full':
					$htaccess .= <<< HTACCESS
FileETag All

HTACCESS;
					break;

				case 'sizetime':
					$htaccess .= <<< HTACCESS
FileETag MTime Size

HTACCESS;
					break;

				case 'size':
					$htaccess .= <<< HTACCESS
FileETag Size

HTACCESS;
					break;

				case 'none':
					$htaccess .= <<< HTACCESS
<IfModule mod_headers.c>
	Header unset ETag
</IfModule>

FileETag None

HTACCESS;
					break;
			}
		}

		if ($config->autoroot)
		{
			$htaccess .= <<<END
##### Redirect index.php to / -- BEGIN
RewriteCond %{THE_REQUEST} !^POST
RewriteCond %{THE_REQUEST} ^[A-Z]{3,9}\ /index\.php\ HTTP/
RewriteCond %{SERVER_PORT}>s ^(443>(s)|[0-9]+>s)$
RewriteRule ^index\.php$ http%2://{$config->httphost}/ $redirCode
##### Redirect index.php to / -- END

END;
		}

		// No HSTS. We will perform a simple non-www to www or vice versa redirection
		switch ($config->wwwredir)
		{
			// non-www to www
			case 1:
				// If I have a rewriteBase condition, I have to append it here
				$subfolder = trim($config->rewritebase, '/') ? trim($config->rewritebase, '/') . '/' : '';

				if ($hasHSTS)
				{
					$htaccess .= <<<END
##### Redirect non-www to www -- BEGIN
RewriteCond %{HTTP_HOST} !^www\. [NC]
RewriteRule ^(.*)$ https://www.%{HTTP_HOST}/$subfolder$1 $redirCode
##### Redirect non-www to www -- END


END;
				}
				else
				{
					{
						$htaccess .= <<<END
##### Redirect non-www to www -- BEGIN
RewriteCond %{HTTP_HOST} !^www\. [NC]
RewriteCond %{HTTPS}>s ^(on>(s)|.*>s)$
RewriteRule ^(.*)$ http%2://www.%{HTTP_HOST}/$subfolder$1 $redirCode
##### Redirect non-www to www -- END


END;
					}
				}

				break;

			// www to non-www
			case 2:
				if ($hasHSTS)
				{
					$htaccess .= <<<END
##### Redirect www to non-www -- BEGIN
RewriteCond %{HTTP_HOST} ^www\.(.+)$ [NC]
RewriteRule ^(.*)$ https://%1/$1 $redirCode
##### Redirect www to non-www -- END


END;
				}
				else
				{
					$htaccess .= <<<END
##### Redirect www to non-www -- BEGIN
# HTTP
RewriteCond %{HTTPS} !=on [OR]
RewriteCond %{HTTP:X-Forwarded-Proto} =http
RewriteCond %{HTTP_HOST} ^www\.(.+)$ [NC]
RewriteRule ^(.*)$ http://%1/$1 $redirCode
# HTTPS
RewriteCond %{HTTPS} =on [OR]
RewriteCond %{HTTP:X-Forwarded-Proto} !=http
RewriteCond %{HTTP_HOST} ^www\.(.+)$ [NC]
RewriteRule ^(.*)$ https://%1/$1 $redirCode
##### Redirect www to non-www -- END


END;
				}
				break;
		}

		if (!empty($config->olddomain))
		{
			$htaccess .= "##### Redirect old to new domain -- BEGIN\n";

			$domains        = trim($config->olddomain);
			$domains        = explode(',', $domains);
			$newHTTPDomain  = $config->httphost;
			$newHTTPSDomain = $config->httpshost;

			foreach ($domains as $olddomain)
			{
				$olddomain = trim($olddomain);

				if (empty($olddomain))
				{
					continue;
				}

				$httpRedirect  = $olddomain != $newHTTPDomain;
				$httpsRedirect = $olddomain != $newHTTPSDomain;
				$olddomain     = $this->escape_string_for_regex($olddomain);

				if ($httpRedirect && !$hasHSTS)
				{
					$htaccess .= <<<END
## Plain HTTP
RewriteCond %{HTTPS} !=on [OR]
RewriteCond %{HTTP:X-Forwarded-Proto} =http
RewriteCond %{HTTP_HOST} ^$olddomain [NC]
RewriteRule (.*) http://$newHTTPDomain/$1 $redirCode

END;
				}

				if ($httpsRedirect && !$hasHSTS)
				{
					$htaccess .= <<<END
## HTTPS
RewriteCond %{HTTPS} =on [OR]
RewriteCond %{HTTP:X-Forwarded-Proto} !=http
RewriteCond %{HTTP_HOST} ^$olddomain [NC]
RewriteRule (.*) https://$newHTTPSDomain/$1 $redirCode

END;
				}

				if ($httpsRedirect && $hasHSTS)
				{
					$htaccess .= <<<END
## Forced HTTPS - You have enabled the HSTS feature
RewriteCond %{HTTP_HOST} ^$olddomain [NC]
RewriteRule (.*) https://$newHTTPSDomain/$1 $redirCode

END;

				}
			}
			$htaccess .= "##### Redirect old to new domain -- END\n\n";
		}

		if (!empty($config->httpsurls))
		{
			$htaccess .= "##### Force HTTPS for certain pages -- BEGIN\n";

			foreach ($config->httpsurls as $url)
			{
				$urlesc = '^' . $this->escape_string_for_regex($url) . '$';
				$htaccess .= <<<END
RewriteCond %{HTTPS} ^off$ [NC,OR]
RewriteCond %{HTTP:X-Forwarded-Proto} =http
RewriteRule $urlesc https://{$config->httpshost}/$url $redirCode

END;
			}
			$htaccess .= "##### Force HTTPS for certain pages -- END\n\n";
		}

		$htaccess .= <<<END
##### Rewrite rules to block out some common exploits -- BEGIN
RewriteCond %{QUERY_STRING} proc/self/environ [OR]
RewriteCond %{QUERY_STRING} mosConfig_[a-zA-Z_]{1,21}(=|\%3D) [OR]
RewriteCond %{QUERY_STRING} base64_(en|de)code\(.*\) [OR]
RewriteCond %{QUERY_STRING} (<|%3C).*script.*(>|%3E) [NC,OR]
RewriteCond %{QUERY_STRING} GLOBALS(=|\[|\%[0-9A-Z]{0,2}) [OR]
RewriteCond %{QUERY_STRING} _REQUEST(=|\[|\%[0-9A-Z]{0,2})
RewriteRule .* index.php [F]
##### Rewrite rules to block out some common exploits -- END

END;

		if ($config->fileinj == 1)
		{
			$htaccess .= <<<END
##### File injection protection -- BEGIN
RewriteCond %{REQUEST_METHOD} GET
RewriteCond %{QUERY_STRING} [a-zA-Z0-9_]=http[s]?:// [OR]
RewriteCond %{QUERY_STRING} [a-zA-Z0-9_]=(\.\.//?)+ [OR]
RewriteCond %{QUERY_STRING} [a-zA-Z0-9_]=/([a-z0-9_.]//?)+ [NC]
RewriteRule .* - [F]
##### File injection protection -- END


END;
		}

		$htaccess .= "##### Advanced server protection rules exceptions -- BEGIN\n";

		if (!empty($config->exceptionfiles))
		{
			foreach ($config->exceptionfiles as $file)
			{
				$file = '^' . $this->escape_string_for_regex($file) . '$';
				$htaccess .= <<<END
RewriteRule $file - [L]

END;
			}
		}

		if (!empty($config->exceptiondirs))
		{
			foreach ($config->exceptiondirs as $dir)
			{
				$dir = trim($dir, '/');
				$dir = $this->escape_string_for_regex($dir);
				$htaccess .= <<<END
RewriteCond %{REQUEST_FILENAME} !(\.php)$
RewriteCond %{REQUEST_FILENAME} -f
RewriteRule ^$dir/ - [L]

END;
			}
		}

		if (!empty($config->fullaccessdirs))
		{
			foreach ($config->fullaccessdirs as $dir)
			{
				$dir = trim($dir, '/');
				$dir = $this->escape_string_for_regex($dir);
				$htaccess .= <<<END
RewriteRule ^$dir/ - [L]

END;
			}
		}

		$htaccess .= "##### Advanced server protection rules exceptions -- END\n\n";

		$htaccess .= "##### Advanced server protection -- BEGIN\n\n";

		if ($config->phpeaster == 1)
		{
			$htaccess .= <<<END
RewriteCond %{QUERY_STRING} \=PHP[a-f0-9]{8}-[a-f0-9]{4}-[a-f0-9]{4}-[a-f0-9]{4}-[a-f0-9]{12} [NC]
RewriteRule .* - [F]

END;
		}

		if ($config->backendprot == 1)
		{
			$bedirs  = implode('|', $config->bepexdirs);
			$betypes = implode('|', $config->bepextypes);
			$htaccess .= <<<END
## Back-end protection
RewriteRule ^administrator/?$ - [L]
RewriteRule ^administrator/index\.(php|html?)$ - [L]
RewriteRule ^administrator/index[23]\.php$ - [L]
RewriteRule ^administrator/($bedirs)/.*\.($betypes)$ - [L]
RewriteRule ^administrator/ - [F]

END;
		}

		if ($config->frontendprot == 1)
		{
			$fedirs  = implode('|', $config->fepexdirs);
			$fetypes = implode('|', $config->fepextypes);
			$htaccess .= <<<END
## Allow limited access for certain Joomla! system directories with client-accessible content
RewriteRule ^($fedirs)/.*\.($fetypes)$ - [L]
RewriteRule ^($fedirs)/ - [F]
## Disallow front-end access for certain Joomla! system directories (unless access to their files is allowed above)
RewriteRule ^includes/js/ - [L]
RewriteRule ^(cache|includes|language|logs|log|tmp)/ - [F]
RewriteRule ^(configuration\.php|CONTRIBUTING\.md|htaccess\.txt|joomla\.xml|LICENSE\.txt|phpunit\.xml|README\.txt|web\.config\.txt) - [F]

## Disallow access to rogue PHP files throughout the site, unless they are explicitly allowed
RewriteCond %{REQUEST_FILENAME} (\.php)$
RewriteCond %{REQUEST_FILENAME} !(/index[23]?\.php)$
RewriteCond %{REQUEST_FILENAME} -f
RewriteRule (.*\.php)$ - [F]

END;
		}

		if ($config->leftovers == 1)
		{
			$htaccess .= <<<END
## Disallow access to htaccess.txt, php.ini and configuration.php-dist
RewriteRule ^(htaccess\.txt|configuration\.php-dist|php\.ini)$ - [F]

END;
		}

		if ($config->clickjacking == 1)
		{
			$action = version_compare($apacheVersion, '2.0', 'ge') ? 'always append' : 'append';
			$htaccess .= <<< ENDCONF
## Protect against clickjacking
<IfModule mod_headers.c>

	Header $action X-Frame-Options SAMEORIGIN

	# The `X-Frame-Options` response header should be send only for
	# HTML documents and not for the other resources.

	<FilesMatch "\.(appcache|atom|bbaw|bmp|crx|css|cur|eot|f4[abpv]|flv|geojson|gif|htc|ico|jpe?g|js|json(ld)?|m4[av]|manifest|map|mp4|oex|og[agv]|opus|otf|pdf|png|rdf|rss|safariextz|svgz?|swf|topojson|tt[cf]|txt|vcard|vcf|vtt|webapp|web[mp]|woff2?|xloc|xml|xpi)$">
		Header unset X-Frame-Options
	</FilesMatch>

</IfModule>

ENDCONF;
		}

		if ($config->reducemimetyperisks == 1)
		{
			$htaccess .= <<< HTACCESS
## Reduce MIME type security risks
<IfModule mod_headers.c>
	Header set X-Content-Type-Options "nosniff"
</IfModule>

HTACCESS;
		}

		if ($config->reflectedxss == 1)
		{
			$htaccess .= <<< HTACCESS
## Reflected XSS prevention
<IfModule mod_headers.c>
Header set X-XSS-Protection "1; mode=block"
</IfModule>

# mod_headers cannot match based on the content-type, however,
# the X-XSS-Protection response header should be send only for
# HTML documents and not for the other resources.

<IfModule mod_headers.c>
	<FilesMatch "\.(appcache|atom|bbaw|bmp|crx|css|cur|eot|f4[abpv]|flv|geojson|gif|htc|ico|jpe?g|js|json(ld)?|m4[av]|manifest|map|mp4|oex|og[agv]|opus|otf|pdf|png|rdf|rss|safariextz|svgz?|swf|topojson|tt[cf]|txt|vcard|vcf|vtt|webapp|web[mp]|webmanifest|woff2?|xloc|xml|xpi)$">
		Header unset X-XSS-Protection
	</FilesMatch>
</IfModule>

HTACCESS;
		}

		if ($config->noserversignature == 1)
		{
			$htaccess .= <<< HTACCESS
## Remove Apache and PHP version signature
<IfModule mod_headers.c>
	Header unset X-Powered-By
</IfModule>

ServerSignature Off

HTACCESS;
		}

		if ($config->notransform == 1)
		{
			$htaccess .= <<< HTACCESS
## Prevent content transformation
<IfModule mod_headers.c>
	Header merge Cache-Control "no-transform"
</IfModule>

HTACCESS;
		}

		$htaccess .= "##### Advanced server protection -- END\n\n";

		if ($hasHSTS)
		{
			$action = version_compare($apacheVersion, '2.0', 'ge') ? 'always set' : 'set';
			$htaccess .= <<<END
## HSTS Header - See http://en.wikipedia.org/wiki/HTTP_Strict_Transport_Security
<IfModule mod_headers.c>
Header $action Strict-Transport-Security "max-age=31536000" env=HTTPS
</IfModule>

END;
		}

		if ($config->notracetrack == 1)
		{
			$tmpRedirCode = $serverCaps->customCodes ? '[R=405,L]' : '[F,L]';
			$htaccess .= <<<END
## Disable HTTP methods TRACE and TRACK (protect against XST)
TraceEnable off

END;
		}

		if ($config->cors == 1)
		{
			$action = version_compare($apacheVersion, '2.0', 'ge') ? 'always set' : 'set';
			$htaccess .= <<<END
## Cross-Origin Resource Sharing (CORS) -- See http://enable-cors.org/
<IfModule mod_headers.c>
	Header $action Access-Control-Allow-Origin "*"
	Header $action Timing-Allow-Origin "*"
</IfModule>

END;
		}

		if ($config->utf8charset == 1)
		{
			$htaccess .= <<<END
## Set the UTF-8 character set as the default
#  Serve all resources labeled as `text/html` or `text/plain`
#  with the media type `charset` parameter set to `UTF-8`.

AddDefaultCharset utf-8

# Serve the following file types with the media type `charset`
# parameter set to `UTF-8`.
#
# https://httpd.apache.org/docs/current/mod/mod_mime.html#addcharset

<IfModule mod_mime.c>
	AddCharset utf-8 .atom \
					 .bbaw \
					 .css \
					 .geojson \
					 .js \
					 .json \
					 .jsonld \
					 .rdf \
					 .rss \
					 .topojson \
					 .vtt \
					 .webapp \
					 .xloc \
					 .xml
</IfModule>

END;
		}

		$htaccess .= <<<END
##### Joomla! core SEF Section -- BEGIN
RewriteRule .* - [E=HTTP_AUTHORIZATION:%{HTTP:Authorization}]
RewriteCond %{REQUEST_URI} !^/index\.php
RewriteCond %{REQUEST_FILENAME} !-f
RewriteCond %{REQUEST_FILENAME} !-d
RewriteRule .* index.php [L]
##### Joomla! core SEF Section -- END


END;

		$htaccess .= "\n\n" . $config->custfoot . "\n";

		return $htaccess;
	}

	/**
	 * Guesses and returns the Apache version family.
	 *
	 * @return  string  1.1, 1.3, 2.0, 2.2, 2.5 or 0.0 (if no match)
	 */
	private function apacheVersion()
	{
		// Get the server string
		$serverString = $_SERVER['SERVER_SOFTWARE'];

		// Not defined? Assume Apache 2.0.
		if (empty($serverString))
		{
			return '2.0';
		}

		// LiteSpeed? Fake it.
		if (strtoupper(substr($serverString, 0, 9)) == 'LITESPEED')
		{
			return '2.0';
		}

		// Not Apache? Return 0.0
		if (strtoupper(substr($serverString, 0, 6)) !== 'APACHE')
		{
			return '0.0';
		}

		// No slash after Apache? Assume 2.5
		if (strlen($serverString) < 7)
		{
			return '2.5';
		}

		if (substr($serverString, 6, 1) != '/')
		{
			return '2.5';
		}

		// Strip part past the version string
		$serverString = substr($serverString, 7);

		$v = substr($serverString, 0, 3);
		switch ($v)
		{
			case '1.3':
			case '2.0':
			case '2.2':
			case '2.5':
				return $v;

				break;

			default:
				if (version_compare($v, '1.3', 'lt'))
				{
					return '1.1';
				}
				else
				{
					return '2.2';
				}

				break;
		}
	}
}