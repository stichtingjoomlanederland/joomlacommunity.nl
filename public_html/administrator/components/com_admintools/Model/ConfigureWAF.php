<?php
/**
 * @package   admintools
 * @copyright Copyright (c)2010-2020 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license   GNU General Public License version 3, or later
 */

namespace Akeeba\AdminTools\Admin\Model;

defined('_JEXEC') or die;

use Akeeba\AdminTools\Admin\Helper\Storage;
use FOF30\Model\Model;

class ConfigureWAF extends Model
{
	/**
	 * Default configuration variables
	 *
	 * @var array
	 */
	private $defaultConfig = [
		'ipworkarounds'                   => 2,
		'ipwl'                            => 0,
		'ipbl'                            => 0,
		'adminpw'                         => '',
		'nonewadmins'                     => 1,
		'nonewfrontendadmins'             => 1,
		'sqlishield'                      => 1,
		'antispam'                        => 0,
		'custgenerator'                   => 0,
		'generator'                       => '',
		'tpone'                           => 1,
		'tmpl'                            => 1,
		'template'                        => 1,
		'logbreaches'                     => 1,
		'emailonadminlogin'               => '',
		'emailonfailedadminlogin'         => '',
		'emailbreaches'                   => '',
		'muashield'                       => 1,
		'csrfshield'                      => 0,
		'rfishield'                       => 1,
		'phpshield'                       => 1,
		'dfishield'                       => 1,
		'sessionshield'                   => 1,
		'badbehaviour'                    => 0,
		'bbstrict'                        => 0,
		'bbhttpblkey'                     => '',
		'bbwhitelistip'                   => '',
		'tsrenable'                       => 0,
		'tsrstrikes'                      => 3,
		'tsrnumfreq'                      => 1,
		'tsrfrequency'                    => 'hour',
		'tsrbannum'                       => 1,
		'tsrbanfrequency'                 => 'day',
		'spammermessage'                  => 'You are a spammer, hacker or an otherwise bad person.',
		'uploadshield'                    => 1,
		'nofesalogin'                     => 0,
		'tmplwhitelist'                   => 'component,system,raw,koowa,cartupdate',
		'neverblockips'                   => '',
		'emailafteripautoban'             => '',
		'custom403msg'                    => '',
		'httpblenable'                    => 0,
		'httpblthreshold'                 => 25,
		'httpblmaxage'                    => 30,
		'httpblblocksuspicious'           => 0,
		'allowsitetemplate'               => 0,
		'trackfailedlogins'               => 1,
		'use403view'                      => 0,
		'iplookup'                        => 'ip-lookup.net/index.php?ip={ip}',
		'iplookupscheme'                  => 'http',
		'saveusersignupip'                => 0,
		'twofactorauth'                   => 0,
		'twofactorauth_secret'            => '',
		'twofactorauth_panic'             => '',
		'whitelist_domains'               => '.googlebot.com,.search.msn.com',
		'reasons_nolog'                   => '',
		'reasons_noemail'                 => '',
		'resetjoomlatfa'                  => 0,
		'email_throttle'                  => 1,
		'permaban'                        => 0,
		'permabannum'                     => 0,
		'deactivateusers_num'             => 0,
		'deactivateusers_numfreq'         => 1,
		'deactivateusers_frequency'       => 'day',
		'awayschedule_from'               => '',
		'awayschedule_to'                 => '',
		'adminlogindir'                   => '',
		// PLEASE NOTE: Previously this field was used only to BLOCK email domains,
		// but now is used to hold the list of blocked OR allowed domains.
		'blockedemaildomains'             => '',
		'configmonitor_global'            => 1,
		'configmonitor_components'        => 1,
		'configmonitor_action'            => 'email',
		'selfprotect'                     => 1,
		'criticalfiles'                   => 0,
		'criticalfiles_global'            => '',
		'superuserslist'                  => 0,
		'consolewarn'                     => 1,
		'404shield_enable'                => 1,
		'404shield'                       => "wp-admin.php\nwp-login.php\nwp-content/*\nwp-admin/*",
		'emailphpexceptions'              => '',
		'logfile'                         => 0,
		'filteremailregistration'         => 'block',
		'leakedpwd'                       => 0,
		'leakedpwd_groups'                => [],
		'disableobsoleteadmins'           => 0,
		'disableobsoleteadmins_freq'      => 60,
		'disableobsoleteadmins_groups'    => [],
		'disableobsoleteadmins_maxdays'   => 90,
		'disableobsoleteadmins_action'    => 'reset',
		'disableobsoleteadmins_protected' => [],
		'logusernames'                    => 0,
		'troubleshooteremail'             => 1,
	];

	/**
	 * Load the WAF configuration
	 *
	 * @return  array
	 */
	public function getConfig()
	{
		$params = Storage::getInstance();

		$config = [];

		foreach ($this->defaultConfig as $k => $v)
		{
			$config[$k] = $params->getValue($k, $v);
		}

		$this->migrateIplookup($config);
		$this->fillLeakedPwdGroups($config);

		return $config;
	}

	/**
	 * Merge and save $newParams into the WAF configuration
	 *
	 * @param   array $newParams New parameters to save
	 *
	 * @return  void
	 */
	public function saveConfig(array $newParams)
	{
		$this->migrateIplookup($newParams);

		$params = Storage::getInstance();

		foreach ($newParams as $key => $value)
		{
			// Do not save unnecessary parameters
			if (!array_key_exists($key, $this->defaultConfig))
			{
				continue;
			}

			if (($key == 'awayschedule_from') || ($key == 'awayschedule_to'))
			{
				// Sanity check for Away Schedule time format
				if (!preg_match('#^([0-1]?[0-9]|[2][0-3]):([0-5][0-9])$#', $value))
				{
					$value = '';
				}
			}

			$params->setValue($key, $value);
		}

		$params->setValue('quickstart', 1);

		// Dealing with special fields
		if (is_array($newParams['reasons_nolog']))
		{
			$params->setValue('reasons_nolog', implode(',', $newParams['reasons_nolog']));
		}

		if (is_array($newParams['reasons_noemail']))
		{
			$params->setValue('reasons_noemail', implode(',', $newParams['reasons_noemail']));
		}

		$params->save();

		/**
		 * Special case: when superuserslist is set to 0 we need to remove the saved Super User IDs from the
		 * #__admintools_storage table
		 */
		if ($params->getValue('superuserslist', 0) == 0)
		{
			$db    = $this->container->db;
			$query = $db->getQuery(true)
				->delete($db->quoteName('#__admintools_storage'))
				->where($db->quoteName('key') . ' = ' . $db->quote('superuserslist'));
			$db->setQuery($query);
			$db->execute();
		}
	}

	/**
	 * Migrates IP Workarounds set on No to Auto
	 */
	public function migrateIpWorkarounds()
	{
		$config = $this->getConfig();

		// Already enabled or on auto, nothing to do here
		if ($config['ipworkarounds'] == 1 || $config['ipworkarounds'] == 2)
		{
			return;
		}

		$config['ipworkarounds'] = 2;

		$this->saveConfig($config);
	}

	/**
	 * Used to transparently set the IP lookup service to a sane default when none is specified
	 *
	 * @param   array $data The configuration data we'll modify
	 *
	 * @return  void
	 */
	private function migrateIplookup(&$data)
	{
		$iplookup       = $data['iplookup'];
		$iplookupscheme = $data['iplookupscheme'];

		if (empty($iplookup))
		{
			$iplookup       = 'ip-lookup.net/index.php?ip={ip}';
			$iplookupscheme = 'http';
		}

		$test = strtolower($iplookup);

		if (substr($test, 0, 7) == 'http://')
		{
			$iplookup       = substr($iplookup, 7);
			$iplookupscheme = 'http';
		}
		elseif (substr($test, 0, 8) == 'https://')
		{
			$iplookup       = substr($iplookup, 8);
			$iplookupscheme = 'https';
		}

		$data['iplookup']       = $iplookup;
		$data['iplookupscheme'] = $iplookupscheme;
	}

	/**
	 * If empty, fills the groups where we should check for leaked passwords
	 *
	 * @param    array $data The configuration data we'll modify
	 */
	private function fillLeakedPwdGroups(&$data)
	{
		// Already filled, nothing to do
		if ($data['leakedpwd_groups'])
		{
			return;
		}

		// Let's see if we already calculated them previously
		$params            = $this->container->params;
		$super_user_groups = $params->get('default_super_user_groups', []);

		if ($super_user_groups)
		{
			$data['leakedpwd_groups'] = $super_user_groups;

			return;
		}

		// Ok, I don't have any. Let's get them
		$db        = $this->container->db;
		$query     = $db->getQuery(true)
			->select($db->qn('rules'))
			->from($db->qn('#__assets'))
			->where($db->qn('parent_id') . ' = ' . $db->q(0));
		$rulesJSON = $db->setQuery($query, 0, 1)->loadResult();
		$rules     = json_decode($rulesJSON, true);

		$rawGroups = $rules['core.admin'];
		$groups    = [];

		foreach ($rawGroups as $g => $enabled)
		{
			if ($enabled)
			{
				$groups[] = $db->q($g);
			}
		}

		$data['leakedpwd_groups'] = $groups;

		$params->set('default_super_user_groups', $groups);
		$params->save();
	}
}
