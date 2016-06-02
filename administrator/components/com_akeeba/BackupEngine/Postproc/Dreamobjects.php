<?php
/**
 * Akeeba Engine
 * The modular PHP5 site backup engine
 *
 * @copyright Copyright (c)2006-2016 Nicholas K. Dionysopoulos
 * @license   GNU GPL version 3 or, at your option, any later version
 * @package   akeebaengine
 *
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
		$akeebaConfig = Factory::getConfiguration();

		$ret = array(
			'accessKey'        => $akeebaConfig->get('engine.postproc.dreamobjects.accesskey', ''),
			'secretKey'        => $akeebaConfig->get('engine.postproc.dreamobjects.secretkey', ''),
			'useSSL'           => $akeebaConfig->get('engine.postproc.dreamobjects.usessl', 0),
			'bucket'           => $akeebaConfig->get('engine.postproc.dreamobjects.bucket', null),
			'lowercase'        => $akeebaConfig->get('engine.postproc.dreamobjects.lowercase', 1),
			'customEndpoint'   => 'objects-us-west-1.dream.io',
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