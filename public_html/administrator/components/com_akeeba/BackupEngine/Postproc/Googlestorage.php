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
 * Google Storage is a sub-case of the Amazon S3 engine with a custom endpoint
 *
 * @package Akeeba\Engine\Postproc
 */
class Googlestorage extends Amazons3
{
	/**
	 * Used in log messages.
	 *
	 * @var  string
	 */
	protected $engineLogName = 'Google Storage';

	/**
	 * The prefix to use for volatile key storage
	 *
	 * @var  string
	 */
	protected $volatileKeyPrefix = 'volatile.postproc.googlestorage.';

	public function __construct()
	{
		parent::__construct();

		Factory::getLog()->warning("The old Google Storage integration you are currently using, the one that makes use of the legacy S3 API, is deprecated and will be removed in a future version. Please switch to the new Upload to Google Storage (JSON API) integration.");

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
		$akeebaConfig = Factory::getConfiguration();

		$ret = array(
			'accessKey'        => $akeebaConfig->get('engine.postproc.googlestorage.accesskey', ''),
			'secretKey'        => $akeebaConfig->get('engine.postproc.googlestorage.secretkey', ''),
			'useSSL'           => $akeebaConfig->get('engine.postproc.googlestorage.usessl', 0),
			'bucket'           => $akeebaConfig->get('engine.postproc.googlestorage.bucket', null),
			'lowercase'        => $akeebaConfig->get('engine.postproc.googlestorage.lowercase', 1),
			'customEndpoint'   => 'commondatastorage.googleapis.com',
			'signatureMethod'  => 'v2',
			'region'           => '',
			'disableMultipart' => 1,
			'directory'        => $akeebaConfig->get('engine.postproc.googlestorage.directory', null),
			'rrs'              => 0,
		);

		if ($ret['lowercase'] && !empty($ret['bucket']))
		{
			$ret['bucket'] = strtolower($ret['bucket']);
		}

		return $ret;
	}
}
