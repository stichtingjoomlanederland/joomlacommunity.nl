<?php
/**
 * @package   AdminTools
 * @copyright 2010-2016 Akeeba Ltd / Nicholas K. Dionysopoulos
 * @license   GNU General Public License version 3, or later
 */

namespace Akeeba\AdminTools\Admin\Model;

defined('_JEXEC') or die;

use Akeeba\AdminTools\Admin\Helper\Storage;
use FOF30\Container\Container;
use FOF30\Model\Model;
use FOF30\Utils\Ip;
use JFactory;
use JText;
use JUri;

class QuickStart extends Model
{
	/**
	 * The parameters storage model
	 *
	 * @var   Storage
	 */
	private $storageModel;

	/**
	 * Administrator password protection model
	 *
	 * @var   \Akeeba\AdminTools\Admin\Model\AdminPassword
	 */
	private $adminPasswordModel;

	/**
	 * WAF Config model
	 *
	 * @var   \Akeeba\AdminTools\Admin\Model\ConfigureWAF
	 */
	private $wafModel;

	/**
	 * WAF configuration
	 *
	 * @var   array
	 */
	private $config;

	public function  __construct(Container $container, $config = array())
	{
		parent::__construct($container, $config);

		$this->storageModel = Storage::getInstance();
		$this->adminPasswordModel = $this->container->factory->model('AdminPassword')->tmpInstance();
		$this->wafModel = $this->container->factory->model('ConfigureWAF')->tmpInstance();
		$this->config = $this->wafModel->getConfig();
	}

	/**
	 * Applies the wizard preferences to the component's configuration
	 *
	 * @return  void
	 */
	public function applyPreferences()
	{
		// Reset all stored settings
		$this->storageModel->resetContents();

		// Apply administrator secret URL parameter
		$this->config['adminpw'] = $this->getState('adminpw', '');

		// Password protect administrator
		$this->applyAdministratorPassword();

		// Apply email on admin login
		$this->config['emailonadminlogin'] = $this->getState('emailonadminlogin', '');
		$this->config['emailonfailedadminlogin'] = $this->getState('emailonadminlogin', '');

		// Apply IP whitelist
		$this->applyIpWhitelist();

		// Disable editing backend users' properties
		$this->config['nonewadmins'] = $this->getState('nonewadmins', 0);

		// Forbid front-end Super Administrator login
		$this->config['nofesalogin'] = $this->getState('nofesalogin', 0);

		// Enable WAF
		$this->applyWafPreferences($this->getState('enablewaf', 0));

		// Apply IP workarounds
		$this->config['ipworkarounds'] = $this->getState('ipworkarounds', 0);

		// Apply IP autoban preferences
		$this->applyAutoban($this->getState('autoban', 0));

		// Apply automatic permanent blacklist
		$this->applyBlacklist($this->getState('autoblacklist', 0));

		// Apply email address to report WAF exceptions and blocks
		$this->config['emailbreaches'] = $this->getState('emailbreaches', '');
		$this->config['emailafteripautoban'] = $this->getState('emailbreaches', '');

		// Project Honeypot HTTP:BL
		$this->applyProjectHoneypot($this->getState('bbhttpblkey', ''));

		// Save the WAF configuration
		$this->wafModel->saveConfig($this->config);

		// Apply .htaccess Maker
		if ($this->getState('htmaker', 0))
		{
			$written = $this->applyHtmaker();

			if (!$written)
			{
				JFactory::getApplication()->enqueueMessage(JText::_('COM_ADMINTOOLS_QUICKSTART_MSG_HTMAKERNOTAPPLIED'), 'error');
			}
		}

		// Save a flag indicating we no longer need to run the Quick Start
		$this->storageModel->load();
		$this->storageModel->setValue('quickstart', 1, 1);
	}

	/**
	 * Password protect / unprotect administrator
	 *
	 * @return  void
	 */
	private function applyAdministratorPassword()
	{
		$this->adminPasswordModel->username = $this->getState('admin_username', '');
		$this->adminPasswordModel->password = $this->getState('admin_password', '');

		if (empty($this->adminPasswordModel->username) || empty($this->adminPasswordModel->password))
		{
			$this->adminPasswordModel->unprotect();
		}
		else
		{
			$this->adminPasswordModel->protect();
		}
	}

	/**
	 * Apply administrator IP whitelist
	 *
	 * @return  void
	 */
	private function applyIpWhitelist()
	{
		$this->config['ipwl'] = $this->getState('ipwl', 0);

		if ($this->config['ipwl'])
		{
			/** @var WhitelistedAddresses $ipwlModel */
			$ipwlModel = $this->container->factory->model('WhitelistedAddresses')->tmpInstance();
			$tableName = $ipwlModel->getTableName();

			$db = $this->container->db;
			$db->truncateTable($tableName);

			$ipwlModel->reset();
			$detectedIp = $this->getState('detectedip', '');

			if (!empty($detectedIp) && ($detectedIp != Ip::getIp()))
			{
				$ipwlModel->save(array(
					'ip'          => $this->getState('detectedip', ''),
					'description' => JText::_('COM_ADMINTOOLS_QUICKSTART_MSG_IPADDEDBYWIZARD')
				));
			}
			else
			{
				$ipwlModel->save(array(
					'ip'          => Ip::getIp(),
					'description' => JText::_('COM_ADMINTOOLS_QUICKSTART_MSG_IPADDEDBYWIZARD')
				));
			}
		}
	}

	/**
	 * Apply main WAF preference (global disable/enable)
	 *
	 * @param   bool  $enabled  Should I enable WAF?
	 *
	 * @return  void
	 */
	private function applyWafPreferences($enabled = true)
	{
		$state = $enabled ? 1 : 0;

		// UploadShield is disabled on Joomla! 3.4.1 and later (it's included in Joomla! itself)
		$uploadShieldState = version_compare(JVERSION, '3.4.1', 'ge') ? 0 : $state;

		$newValues = array(
			'ipbl'                    => $state,
			'sqlishield'              => $state,
			'antispam'                => 0,
			'custgenerator'           => $state,
			'generator'               => 'MYOB',
			'tpone'                   => $state,
			'tmpl'                    => $state,
			'template'                => $state,
			'logbreaches'             => 1,
			'muashield'               => $state,
			'csrfshield'              => 0,
			'rfishield'               => $state,
			'dfishield'               => $state,
			'uploadshield'            => $uploadShieldState,
			'sessionshield'           => $state,
			'tmplwhitelist'           => 'component,system,raw,koowa',
			'allowsitetemplate'       => 0,
			'trackfailedlogins'       => $state,
			'use403view'              => 0,
			'showpwonloginfailure'    => 1,
			'iplookup'                => 'ip-lookup.net/index.php?ip={ip}',
			'iplookupscheme'          => 'http',
			'saveusersignupip'        => $state,
			'whitelist_domains'       => '.googlebot.com,.search.msn.com',
			'reasons_nolog'           => 'geoblocking',
			'reasons_noemail'         => 'geoblocking',
			'resetjoomlatfa'          => 0,
			'email_throttle'          => 1,
		);

		$this->config = array_merge($this->config, $newValues);
	}

	/**
	 * Apply automatic IP ban
	 *
	 * @param   bool  $enabled  Should I enable it?
	 *
	 * @return  void
	 */
	private function applyAutoban($enabled = true)
	{
		$state = $enabled ? 1 : 0;

		$newValues = array(
			'tsrenable'               => $state,
			'tsrstrikes'              => 3,
			'tsrnumfreq'              => 1,
			'tsrfrequency'            => 'minute',
			'tsrbannum'               => 15,
			'tsrbanfrequency'         => 'minute',
		);

		$this->config = array_merge($this->config, $newValues);
	}

	/**
	 * Apply automatic IP ban
	 *
	 * @param   bool  $enabled  Should I enable it?
	 *
	 * @return  void
	 */
	private function applyBlacklist($enabled = true)
	{
		$state = $enabled ? 1 : 0;

		$newValues = array(
			'permaban'                => $state,
			'permabannum'             => 3,
		);

		$this->config = array_merge($this->config, $newValues);
	}

	/**
	 * Apply Project Honeypot HTTP:BL settings
	 *
	 * @param   string  $key  The HTTP:BL key
	 *
	 * @return  void
	 */
	private function applyProjectHoneypot($key = '')
	{
		$state = empty($key) ? 0 : 1;

		$newValues = array(
			'bbhttpblkey'             => $key,
			'httpblenable'            => $state,
			'httpblthreshold'         => 25,
			'httpblmaxage'            => 30,
			'httpblblocksuspicious'   => 0,
		);

		$this->config = array_merge($this->config, $newValues);
	}

	private function applyHtmaker()
	{
		/** @var HtaccessMaker $htMakerModel */
		$htMakerModel = $this->container->factory->model('HtaccessMaker')->tmpInstance();

		// Get the base bath to the site's root
		$basePath = JUri::base(true);

		if (substr($basePath, -14) == '/administrator')
		{
			$basePath = substr($basePath, 14);
		}

		$basePath = trim($basePath, '/');

		$basePath = empty($basePath) ? '/' : '';

		// Get the site's hostname
		$hostname = JUri::getInstance()->getHost();

		// Should I redirect non-www to www or vice versa?
		$wwwRedir = substr($hostname, 0, 4) == 'www.' ? 1 : 2;

		// Is it an HTTPS site?
		$isHttps = JUri::getInstance()->getScheme() == 'https';

		// Get the new .htaccess Maker configuration values
		$newConfig = array(
			// == System configuration ==
			// Host name for HTTPS requests (without https://)
			'httpshost'      => $hostname,
			// Host name for HTTP requests (without http://)
			'httphost'       => $hostname,
			// Follow symlinks (may cause a blank page or 500 Internal Server Error)
			'symlinks'       => -1,
			// Base directory of your site (/ for domain's root)
			'rewritebase'    => $basePath,

			// == Optimization and utility ==
			// Force index.php parsing before index.html
			'fileorder'      => 1,
			// Set default expiration time to 1 hour
			'exptime'        => 1,
			// Automatically compress static resources
			'autocompress'   => 1,
			// Force GZip compression for mangled Accept-Encoding headers
			'forcegzip'      => 1,
			// Redirect index.php to root
			'autoroot'       => 0,
			// Redirect www and non-www addresses
			'wwwredir'       => $wwwRedir,
			// HSTS Header (for HTTPS-only sites)
			'hstsheader'     => $isHttps ? 1 : 0,
			// Disable HTTP methods TRACE and TRACK (protect against XST)
			'notracetrack'   => 0,
			// Cross-Origin Resource Sharing (CORS)
			'cors'     => 0,
			// Set UTF-8 charset as default
			'utf8charset'     => 0,
			// Send ETag
			'etagtype' => 'default',

			// == Basic security ==
			// Disable directory listings
			'nodirlists'     => 0,
			// Protect against common file injection attacks
			'fileinj'        => 1,
			// Disable PHP Easter Eggs
			'phpeaster'      => 1,
			// Block access from specific user agents
			'nohoggers'      => 1,
			// Block access to configuration.php-dist and htaccess.txt
			'leftovers'      => 1,
			// Protect against clickjacking
			'clickjacking'   => 0,
			// Reduce MIME type security risks
			'reducemimetyperisks' => 0,
			// Reflected XSS prevention
			'reflectedxss' => 0,
			// Remove Apache and PHP version signature
			'noserversignature' => 1,
			// Prevent content transformation
			'notransform' => 0,
			// User agents to block (one per line)
			'hoggeragents'   => array(
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
			'backendprot'    => 1,
			// Back-end protection
			'frontendprot'   => 1,
			// -- Fine-tuning
			// Back-end directories where file type exceptions are allowed
			'bepexdirs'      => array('components', 'modules', 'templates', 'images', 'plugins'),
			// Back-end file types allowed in selected directories
			'bepextypes'     => array(
				'jpe', 'jpg', 'jpeg', 'jp2', 'jpe2', 'png', 'gif', 'bmp', 'css', 'js',
				'swf', 'html', 'mpg', 'mp3', 'mpeg', 'mp4', 'avi', 'wav', 'ogg', 'ogv',
				'xls', 'xlsx', 'doc', 'docx', 'ppt', 'pptx', 'zip', 'rar', 'pdf', 'xps',
				'txt', '7z', 'svg', 'odt', 'ods', 'odp', 'flv', 'mov', 'htm', 'ttf',
				'woff', 'woff2', 'eot',
				'JPG', 'JPEG', 'PNG', 'GIF', 'CSS', 'JS', 'TTF', 'WOFF', 'WOFF2', 'EOT'
			),
			// Front-end directories where file type exceptions are allowed
			'fepexdirs'      => array('components', 'modules', 'templates', 'images', 'plugins', 'media', 'libraries', 'media/jui/fonts'),
			// Front-end file types allowed in selected directories
			'fepextypes'     => array(
				'jpe', 'jpg', 'jpeg', 'jp2', 'jpe2', 'png', 'gif', 'bmp', 'css', 'js',
				'swf', 'html', 'mpg', 'mp3', 'mpeg', 'mp4', 'avi', 'wav', 'ogg', 'ogv',
				'xls', 'xlsx', 'doc', 'docx', 'ppt', 'pptx', 'zip', 'rar', 'pdf', 'xps',
				'txt', '7z', 'svg', 'odt', 'ods', 'odp', 'flv', 'mov', 'ico', 'htm',
				'ttf', 'woff', 'woff2', 'eot',
				'JPG', 'JPEG', 'PNG', 'GIF', 'CSS', 'JS', 'TTF', 'WOFF', 'WOFF2', 'EOT'
			),
			// Allow direct access, including .php files, to these directories
			'fullaccessdirs' => array(
			),
			// Allow direct access, except .php files, to these directories
			'exceptiondirs'       => array(
				'.well-known'
			),
		);

		$htMakerModel->saveConfiguration($newConfig, true);

		return $htMakerModel->writeConfigFile();
	}

	/**
	 * Is it the Quick Setup Wizard's first run?
	 *
	 * @return  bool
	 */
	public function isFirstRun()
	{
		return $this->storageModel->getValue('quickstart', 0) == 0;
	}
}