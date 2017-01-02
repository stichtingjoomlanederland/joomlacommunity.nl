<?php
/**
 * @package   AdminTools
 * @copyright 2010-2016 Akeeba Ltd / Nicholas K. Dionysopoulos
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
	private $defaultConfig = array(
		'ipworkarounds'			  => 0,
		'ipwl'                    => 0,
		'ipbl'                    => 0,
		'adminpw'                 => '',
		'nonewadmins'             => 1,
		'sqlishield'              => 1,
		'antispam'                => 0,
		'custgenerator'           => 0,
		'generator'               => '',
		'tpone'                   => 1,
		'tmpl'                    => 1,
		'template'                => 1,
		'logbreaches'             => 1,
		'emailonadminlogin'       => '',
		'emailonfailedadminlogin' => '',
		'emailbreaches'           => '',
		'muashield'               => 1,
		'csrfshield'              => 0,
		'rfishield'               => 1,
		'dfishield'               => 1,
		'sessionshield'           => 1,
		'badbehaviour'            => 0,
		'bbstrict'                => 0,
		'bbhttpblkey'             => '',
		'bbwhitelistip'           => '',
		'tsrenable'               => 0,
		'tsrstrikes'              => 3,
		'tsrnumfreq'              => 1,
		'tsrfrequency'            => 'hour',
		'tsrbannum'               => 1,
		'tsrbanfrequency'         => 'day',
		'spammermessage'          => 'You are a spammer, hacker or an otherwise bad person.',
		'uploadshield'            => 1,
		'nofesalogin'             => 0,
		'tmplwhitelist'           => 'component,system,raw,koowa',
		'neverblockips'           => '',
		'emailafteripautoban'     => '',
		'custom403msg'            => '',
		'httpblenable'            => 0,
		'httpblthreshold'         => 25,
		'httpblmaxage'            => 30,
		'httpblblocksuspicious'   => 0,
		'allowsitetemplate'       => 0,
		'trackfailedlogins'       => 1,
		'use403view'              => 0,
		'showpwonloginfailure'    => 1,
		'iplookup'                => 'ip-lookup.net/index.php?ip={ip}',
		'iplookupscheme'          => 'http',
		'saveusersignupip'        => 0,
		'twofactorauth'           => 0,
		'twofactorauth_secret'    => '',
		'twofactorauth_panic'     => '',
		'whitelist_domains'       => '.googlebot.com,.search.msn.com',
		'reasons_nolog'           => 'geoblocking',
		'reasons_noemail'         => 'geoblocking',
		'resetjoomlatfa'          => 0,
		'email_throttle'          => 1,
		'permaban'                => 0,
		'permabannum'             => 0,
		'deactivateusers_num'     => 0,
		'deactivateusers_numfreq' => 1,
		'deactivateusers_frequency' => 'day',
		'awayschedule_from'       => '',
		'awayschedule_to'         => '',
		'adminlogindir'           => '',
		'blockedemaildomains'     => ''
	);

	/**
	 * Load the WAF configuration
	 *
	 * @return  array
	 */
	public function getConfig()
	{
		$params = Storage::getInstance();

		$config = array();

		foreach ($this->defaultConfig as $k => $v)
		{
			$config[ $k ] = $params->getValue($k, $v);
		}

		$this->migrateIplookup($config);

		return $config;
	}

	/**
	 * Merge and save $newParams into the WAF configuration
	 *
	 * @param   array  $newParams  New parameters to save
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
	}

	/**
	 * Used to transparently set the IP lookup service to a sane default when none is specified
	 *
	 * @param   array  $data  The configuration data we'll modify
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
}