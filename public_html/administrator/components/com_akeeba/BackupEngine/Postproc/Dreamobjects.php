<?php
/**
 * Akeeba Engine
 * The PHP-only site backup engine
 *
 * @copyright Copyright (c)2006-2019 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license   GNU GPL version 3 or, at your option, any later version
 * @package   akeebaengine
 */

namespace Akeeba\Engine\Postproc;

// Protection against direct access
defined('AKEEBAENGINE') or die();

use Akeeba\Engine\Factory;

/**
 * DreamObjects is a sub-case of the Amazon S3 engine with a custom endpoint
 *
 * @package Akeeba\Engine\Postproc
 */
class Dreamobjects extends Amazons3
{
	/**
	 * Used in log messages.
	 *
	 * @var  string
	 */
	protected $engineLogName = 'DreamObjects';

	/**
	 * The prefix to use for volatile key storage
	 *
	 * @var  string
	 */
	protected $volatileKeyPrefix = 'volatile.postproc.dreamobjects.';

	public function __construct()
	{
		parent::__construct();

		// You can't download directly to the browser
		$this->can_download_to_browser = false;
	}

	/**
	 * Get the configuration information for this post-processing engine
	 *
	 * @return  array
	 */
	protected function getEngineConfiguration()
	{
		// The default cluster setting for Akeeba Backup is 'west' before October 1st, 2018 (for backwards compatibility), 'east' afterwards.
		$akeebaConfig = Factory::getConfiguration();
		$cluster      = $akeebaConfig->get('engine.postproc.dreamobjects.cluster', (time() > 1538352000) ? 'east' : 'west');

		// The US West cluster is going away on October 1st, 2018.
		if (($cluster == 'west'))
		{
			if (time() > 1538352000)
			{
				// Past October 1st, 2018: We cannot continue
				$this->setWarning('You are using the ‘west’ DreamHost cluster. This cluster has stopped working on October 1st, 2018 and DREAMHOST (NOT AKEEBA!) HAS REMOVED ALL YOUR DATA. DO NOT SEEK SUPPORT WITH AKEEBA; THERE IS NOTHING WE CAN HELP YOU WITH. This also means that your backup archive will fail to upload. If you are not sure why this happened please read https://help.dreamhost.com/hc/en-us/articles/360002135871-Cluster-migration-procedure for more information. Kindly note that all backups taken between July and now issued a warning that you needed to take action. Moreover, DreamHost had emailed all of its clients about this change.');
			}
			else
			{
				// Before October 1st, 2018: Issue a Big Fat warning asking the user to update.
				$this->setWarning('!!! ACTION REQUIRED !!! You are using the ‘west’ DreamHost cluster. This cluster will stop working on October 1st, 2018 AND ALL YOUR ARCHIVES WILL BE DELETED BY DREAMHOST, NOT AKEEBA, ON THAT DATE. Please read https://help.dreamhost.com/hc/en-us/articles/360002135871-Cluster-migration-procedure for instructions to migrate your data to DreamHost\'s US East cluster. Afterwards, please go to your backup profile\'s Configuration page, Post-processing Engine row, Configure button and set Cluster to US East.');
			}
		}

		$endpoint = "objects-us-{$cluster}-1.dream.io";
		Factory::getLog()->info("DreamObjects: using the $cluster cluster, endpoint $endpoint");

		$ret      = array(
			'accessKey'        => $akeebaConfig->get('engine.postproc.dreamobjects.accesskey', ''),
			'secretKey'        => $akeebaConfig->get('engine.postproc.dreamobjects.secretkey', ''),
			'useSSL'           => $akeebaConfig->get('engine.postproc.dreamobjects.usessl', 0),
			'bucket'           => $akeebaConfig->get('engine.postproc.dreamobjects.bucket', null),
			'lowercase'        => $akeebaConfig->get('engine.postproc.dreamobjects.lowercase', 1),
			'customEndpoint'   => $endpoint,
			'signatureMethod'  => 'v2',
			'region'           => '',
			'disableMultipart' => 1,
			'directory'        => $akeebaConfig->get('engine.postproc.dreamobjects.directory', null),
			'rrs'              => 0,
		);

		if ($ret['lowercase'] && !empty($ret['bucket']))
		{
			$ret['bucket'] = strtolower($ret['bucket']);
		}

		return $ret;
	}
}
