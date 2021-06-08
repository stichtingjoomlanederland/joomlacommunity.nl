<?php
/**
 * @package   admintools
 * @copyright Copyright (c)2010-2021 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license   GNU General Public License version 3, or later
 */

namespace Akeeba\AdminTools\Admin\Model;

defined('_JEXEC') || die;

use Akeeba\AdminTools\Admin\Helper\Storage;
use FOF40\Container\Container;
use FOF40\Model\Model;
use FOF40\IP\IPHelper as Ip;
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Uri\Uri;

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
	 * @var   AdminPassword
	 */
	private $adminPasswordModel;

	/**
	 * WAF Config model
	 *
	 * @var   ConfigureWAF
	 */
	private $wafModel;

	/**
	 * WAF configuration
	 *
	 * @var   array
	 */
	private $config;

	public function __construct(Container $container, $config = [])
	{
		parent::__construct($container, $config);

		$this->storageModel       = Storage::getInstance();
		$this->adminPasswordModel = $this->container->factory->model('AdminPassword')->tmpInstance();
		$this->wafModel           = $this->container->factory->model('ConfigureWAF')->tmpInstance();
		$this->config             = $this->wafModel->getConfig();
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
		$this->config['emailonadminlogin']       = $this->getState('emailonadminlogin', '');
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
		$this->config['emailbreaches']       = $this->getState('emailbreaches', '');
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
				Factory::getApplication()->enqueueMessage(Text::_('COM_ADMINTOOLS_QUICKSTART_MSG_HTMAKERNOTAPPLIED'), 'error');
			}
		}

		// Save a flag indicating we no longer need to run the Quick Start
		$this->storageModel->load();
		$this->storageModel->setValue('quickstart', 1, 1);
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

	/**
	 * Password protect / unprotect administrator
	 *
	 * @return  void
	 */
	private function applyAdministratorPassword()
	{
		$this->adminPasswordModel->username        = $this->getState('admin_username', '');
		$this->adminPasswordModel->password        = $this->getState('admin_password', '');
		$this->adminPasswordModel->resetErrorPages = true;

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
				$ipwlModel->save([
					'ip'          => $this->getState('detectedip', ''),
					'description' => Text::_('COM_ADMINTOOLS_QUICKSTART_MSG_IPADDEDBYWIZARD'),
				]);
			}
			else
			{
				$ipwlModel->save([
					'ip'          => Ip::getIp(),
					'description' => Text::_('COM_ADMINTOOLS_QUICKSTART_MSG_IPADDEDBYWIZARD'),
				]);
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

		$newValues = [
			'ipbl'              => $state,
			'sqlishield'        => $state,
			'antispam'          => 0,
			'custgenerator'     => $state,
			'generator'         => 'MYOB',
			'tpone'             => $state,
			'tmpl'              => $state,
			'template'          => $state,
			'logbreaches'       => 1,
			'muashield'         => $state,
			'rfishield'         => $state,
			'dfishield'         => $state,
			'uploadshield'      => $uploadShieldState,
			'sessionshield'     => $state,
			'tmplwhitelist'     => 'component,system,raw,koowa,cartupdate',
			'allowsitetemplate' => 0,
			'trackfailedlogins' => $state,
			'use403view'        => 0,
			'iplookup'          => 'ip-lookup.net/index.php?ip={ip}',
			'iplookupscheme'    => 'http',
			'saveusersignupip'  => $state,
			'whitelist_domains' => '.googlebot.com,.search.msn.com',
			'reasons_nolog'     => '',
			'reasons_noemail'   => '',
			'resetjoomlatfa'    => 0,
			'email_throttle'    => 1,
			'selfprotect'       => 1,
			'criticalfiles'     => 1,
			'superuserslist'    => 0,
		];

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

		$newValues = [
			'tsrenable'       => $state,
			'tsrstrikes'      => 3,
			'tsrnumfreq'      => 1,
			'tsrfrequency'    => 'minute',
			'tsrbannum'       => 15,
			'tsrbanfrequency' => 'minute',
		];

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

		$newValues = [
			'permaban'    => $state,
			'permabannum' => 3,
		];

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

		$newValues = [
			'bbhttpblkey'           => $key,
			'httpblenable'          => $state,
			'httpblthreshold'       => 25,
			'httpblmaxage'          => 30,
			'httpblblocksuspicious' => 0,
		];

		$this->config = array_merge($this->config, $newValues);
	}

	private function applyHtmaker()
	{
		/** @var HtaccessMaker $htMakerModel */
		$htMakerModel = $this->container->factory->model('HtaccessMaker')->tmpInstance();

		// Get the base bath to the site's root
		$basePath = Uri::base(true);

		if (substr($basePath, -14) == '/administrator')
		{
			$basePath = substr($basePath, 14);
		}

		$basePath = trim($basePath, '/');

		$basePath = empty($basePath) ? '/' : '';

		// Get the site's hostname
		$hostname = Uri::getInstance()->getHost();

		// Should I redirect non-www to www or vice versa?
		$wwwRedir = substr($hostname, 0, 4) == 'www.' ? 1 : 2;

		// Is it an HTTPS site?
		$isHttps = Uri::getInstance()->getScheme() == 'https';

		// Create an object with fine-tuned rules for this site
		$newConfig = (object) [
			// == System configuration ==
			// Host name for HTTPS requests (without https://)
			'httpshost'           => $hostname,
			// Host name for HTTP requests (without http://)
			'httphost'            => $hostname,
			// Follow symlinks (may cause a blank page or 500 Internal Server Error)
			'symlinks'            => -1,
			// Base directory of your site (/ for domain's root)
			'rewritebase'         => $basePath,

			// == Optimization and utility ==
			// Set default expiration time to 1 hour
			'exptime'             => 1,
			// Automatically compress static resources
			'autocompress'        => 1,
			// Redirect index.php to root
			'autoroot'            => 0,
			// Redirect www and non-www addresses
			'wwwredir'            => $wwwRedir,
			// HSTS Header (for HTTPS-only sites)
			'hstsheader'          => $isHttps ? 1 : 0,
			// Set UTF-8 charset as default
			'utf8charset'         => 0,

			// == Basic security ==
			// Disable directory listings
			'nodirlists'          => 0,
			// Block access from specific user agents
			'nohoggers'           => 1,
			// Protect against clickjacking
			'clickjacking'        => 0,
			// Reduce MIME type security risks
			'reducemimetyperisks' => 0,
			// Reflected XSS prevention
			'reflectedxss'        => 0,
			// Prevent content transformation
			'notransform'         => 0,
			// -- Fine-tuning
			// Allow direct access, including .php files, to these directories
			'fullaccessdirs'      => [
			],
		];

		// Pass everything back to the model, it will merge the new config with the default one
		$htMakerModel->saveConfiguration($newConfig);

		return $htMakerModel->writeConfigFile();
	}
}
