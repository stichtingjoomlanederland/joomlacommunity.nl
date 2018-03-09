<?php
/**
 * Akeeba Engine
 * The modular PHP5 site backup engine
 *
 * This is Akeeba Engine's RackSpace CloudFiles API implementation
 *
 * @copyright Copyright (c)2006-2018 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license   GNU GPL version 3 or, at your option, any later version
 * @package   akeebaengine
 */

namespace Akeeba\Engine\Postproc\Connector;

// Protection against direct access
defined('AKEEBAENGINE') or die();

use Akeeba\Engine\Postproc\Connector\Cloudfiles\Exception\Missing\Apikey as MissingApikey;
use Akeeba\Engine\Postproc\Connector\Cloudfiles\Exception\Missing\Username as MissingUsername;
use Akeeba\Engine\Postproc\Connector\Cloudfiles\Request;

/**
 * Self-contained implementation of the RackSpace CloudFiles in PHP
 */
class Cloudfiles
{
	/** @var string The CloudFiles username */
	private $username = '';

	/** @var string The CloudFiles API key */
	private $apiKey = '';

	/** @var string The token returned by CloudFiles */
	private $token = '';

	/** @var int The expiration timestamp of the token we got from CloudFiles */
	private $tokenExpiration = 0;

	/** @var string RackSpace Tenant ID */
	private $tenantId = '';

	/** @var string The user contract (MossoCloudFS_aaaaaaaa-bbbb-cccc-dddd-eeeeeeeeeeee) returned by CloudFiles */
	private $userContract = '';

	/** @var string The authentication endpoint. This is a universal endpoint for all accounts now. */
	private $authEndpoint = 'https://identity.api.rackspacecloud.com/v2.0';

	/** @var array List of storage endpoints per region */
	private $storageEndpoints = array(
		'ORD' => 'https://storage101.ord1.clouddrive.com',
		'DFW' => 'https://storage101.dfw1.clouddrive.com',
		'HKG' => 'https://storage101.hkg1.clouddrive.com',
		'LON' => 'https://storage101.lon3.clouddrive.com',
		'IAD' => 'https://storage101.iad3.clouddrive.com',
		'SYD' => 'https://storage101.syd2.clouddrive.com',
	);

	/** @var string The region of the account. It is kindly reported by the Swift API, no need to set it. */
	private $region = 'LON';

	/** @var string The storage endpoint to use. If unspecified we use the default endpoint of each region. */
	private $storageEndpoint = '';

	/** @var string The storage API version to use */
	private $apiVersion = 'v1';

	/**
	 * Public constructor
	 *
	 * @param string $username The CloudFiles username
	 * @param string $apiKey   The CloudFiles API key
	 * @param array  $options  Configuration options (authEndpoint, storageEndpoint, apiVersion, userContract, tenantId, tokenExpiration, token)
	 *
	 * @return Cloudfiles
	 *
	 * @throws MissingUsername  You have not given me a username
	 * @throws MissingApikey    You have not given me an API key
	 */
	public function __construct($username, $apiKey, $options = array())
	{
		// Data validation
		if (empty($username))
		{
			throw new MissingUsername('You have not specified your CloudFiles username');
		}

		if (empty($username))
		{
			throw new MissingApikey('You have not specified your CloudFiles API key');
		}

		// Very simplistic options parsing
		if (is_array($options) && count($options))
		{
			foreach ($options as $key => $value)
			{
				if (isset($this->$key))
				{
					$this->$key = $value;
				}
			}
		}

		// Initialisation
		$this->username = $username;
		$this->apiKey = $apiKey;
	}

	/**
	 * Return the current options, useful to instantiate a new object without having to re-authenticate to CloudFiles
	 *
	 * @return array
	 */
	public function getCurrentOptions()
	{
		return array(
			'token'           => $this->token,
			'tokenExpiration' => $this->tokenExpiration,
			'tenantId'        => $this->tenantId,
			'userContract'    => $this->userContract,
			'authEndpoint'    => $this->authEndpoint,
			'region'          => $this->region,
			'storageEndpoint' => $this->storageEndpoint,
			'apiVersion'      => $this->apiVersion,
		);
	}

	/**
	 * Authenticate the user and obtain a new token. If there is a token and it's not expired yet we will reuse it.
	 *
	 * @param bool $force Force authentication?
	 */
	public function authenticate($force = false)
	{
		// Should I proceed?
		if (!$force)
		{
			if (!empty($this->token) && !empty($this->tokenExpiration))
			{
				if ($this->tokenExpiration > (time() + 3600))
				{
					// I have a token and its expiration time is more than one hour into the future. No need to re-auth.
					return;
				}
			}
		}

		$request = new Request('POST', $this->authEndpoint . '/tokens');

		$dataRaw = (object)array(
			'auth' => array(
				"RAX-KSKEY:apiKeyCredentials" => array(
					'username' => $this->username,
					'apiKey'   => $this->apiKey,
				)
			)
		);

		$dataForPost = json_encode($dataRaw);
		$request->data = $dataForPost;
		$request->setHeader('Accept', 'application/json');
		$request->setHeader('Content-Type', 'application/json');
		$request->setHeader('Content-Length', strlen($request->data));

		$response = $request->getResponse();

		$this->token = $response->body->access->token->id;
		$this->tenantId = $response->body->access->token->tenant->id;

		$date = new \DateTime($response->body->access->token->expires);
		$this->tokenExpiration = $date->getTimestamp();

		$raxAuthRegionKey = 'RAX-AUTH:defaultRegion';
		$defaultRegion = $response->body->access->user->$raxAuthRegionKey;

        $this->region = strtoupper($defaultRegion);

		if (empty($this->storageEndpoint))
		{
			$this->storageEndpoint = $this->storageEndpoints[$this->region];
		}

		foreach ($response->body->access->serviceCatalog as $service)
		{
			if ($service->name != 'cloudFiles')
			{
				continue;
			}

			foreach ($service->endpoints as $endpoint)
			{
				if ($endpoint->region != $defaultRegion)
				{
					continue;
				}

				$this->userContract = $endpoint->tenantId;
				break;
			}
		}
	}

	/**
	 * Lists the containers in the CloudFiles account
	 *
	 * @param bool   $assoc         Should I return an associative array, where the key is the container name? (default: no)
	 * @param string $lastContainer Start listing AFTER this last container (pagination)
	 * @param int    $limit         How many containers to list
	 *
	 * @return array Array or objects. Internal objects have keys count, bytes, name
	 */
	public function listContainers($assoc = false, $lastContainer = null, $limit = 10000)
	{
		// Re-authenticate if necessary
		$this->authenticate();

		// Get the URL to list containers
		$url = $this->storageEndpoint . '/' . $this->apiVersion . '/' . $this->userContract;

		// Get the request object
		$request = new Request('GET', $url);
		$request->setHeader('X-Auth-Token', $this->token);
		$request->setHeader('Accept', 'application/json');
		$request->setParameter('format', 'json');

		if (!empty($lastContainer))
		{
			$request->setParameter('marker', $lastContainer);
		}

		if (!is_numeric($limit))
		{
			$limit = 10000;
		}

		if ($limit <= 0)
		{
			$limit = 10000;
		}

		$request->setParameter('limit', $limit);

		$response = $request->getResponse();

		if (!$assoc)
		{
			return $response->body;
		}

		$ret = array();

		if (!empty($response->body))
		{
			foreach ($response->body as $container)
			{
				$ret[$container->name] = $container;
			}
		}

		return $ret;
	}

	/**
	 * Lists the contents of a directory inside the container
	 *
	 * @param string $container The name of the container to list
	 * @param string $path      The path to the directory you want to list, '' for the root.
	 * @param bool   $assoc     Should I return an associative array with filenames as keys?
	 * @param null   $lastEntry The entry AFTER which to start listing
	 * @param int    $limit     How many files to show (1000 by default)
	 * @param string $prefix    The common prefix of files to list
	 *
	 * @return array Array of objects. Object keys: hash, last_modified, bytes, name, content_type
	 */
	public function listContents($container, $path = '', $assoc = false, $lastEntry = null, $limit = 1000, $prefix = '')
	{
		// Re-authenticate if necessary
		$this->authenticate();

		// Get the URL to list containers
		$url = $this->storageEndpoint . '/' . $this->apiVersion . '/' . $this->userContract . '/' . $container;
		$url = rtrim($url, '\\/');
		$path = ltrim($path, '\\/');
		$url .= '/' . $path;

		// Get the request object
		$request = new Request('GET', $url);
		$request->setHeader('X-Auth-Token', $this->token);
		$request->setHeader('Accept', 'application/json');
		$request->setParameter('format', 'json');

		if (!empty($lastEntry))
		{
			$request->setParameter('marker', $lastEntry);
		}

		if (!empty($prefix))
		{
			$request->setParameter('prefix', $prefix);
		}

		if (!is_numeric($limit))
		{
			$limit = 1000;
		}

		if ($limit <= 0)
		{
			$limit = 1000;
		}

		$request->setParameter('limit', $limit);
		$request->setParameter('delimiter', '/');

		$response = $request->getResponse();

		if (!$assoc)
		{
			return $response->body;
		}

		$ret = array();

		if (!empty($response->body))
		{
			foreach ($response->body as $file)
			{
				$ret[$file->name] = $file;
			}
		}

		return $ret;
	}

	/**
	 * Uploads a file. The $input array can have one of the following formats:
	 *
	 * 1. A string with the contents of the file to be put to CloudFiles
	 *
	 * 2. An array('fp' => $fp) containing a file pointer, open in read binary mode, to the file to upload
	 *
	 * 3. An array('file' => $pathToFile) containing the path to the file to upload
	 *
	 * 4. An array('data' => $rawData) which is the same as passing a string (case 1)
	 *
	 * When using an array you can also pass the following optional parameters in the array:
	 * size        The size of the uploaded content in bytes
	 *
	 * @param string|array $input       See the method description
	 * @param string       $container   The name of the CloudFiles container to use
	 * @param string       $path        The path inside the container of the uploaded file
	 * @param string       $contentType The content type of the uploaded file
	 */
	public function putObject($input, $container, $path, $contentType = null)
	{
		// Re-authenticate if necessary
		$this->authenticate();

		// Get the URL to list containers
		$url = $this->storageEndpoint . '/' . $this->apiVersion . '/' . $this->userContract . '/' . $container;
		$url = rtrim($url, '\\/');
		$path = ltrim($path, '\\/');
		$url .= '/' . $path;

		// Get the request object
		$request = new Request('PUT', $url);
		$request->setHeader('X-Auth-Token', $this->token);
		//$request->setHeader('Accept', 'application/json');

		// Decide what to do based on the $input format
		if (is_string($input))
		{
			$input = array(
				'data' => $input,
				'size' => strlen($input),
			);
		}

		// Data
		if (isset($input['fp']))
		{
			$request->fp = $input['fp'];
		}
		elseif (isset($input['file']))
		{
			$request->fp = @fopen($input['file'], 'rb');
		}
		elseif (isset($input['data']))
		{
			$request->data = $input['data'];
		}

		// Content-Length (required)
		if (isset($input['size']) && $input['size'] >= 0)
		{
			$request->size = $input['size'];
		}
		else
		{
			if (isset($input['file']))
			{
				clearstatcache(false, $input['file']);
				$request->size = @filesize($input['file']);
			}
			elseif (isset($input['data']))
			{
				$request->size = strlen($input['data']);
			}
		}

		if (empty($contentType))
		{
			$contentType = 'application/octet-stream';
		}

		$request->setParameter('Content-Type', $contentType);
		$request->setParameter('Content-Length', $request->size);

		$request->getResponse();

		if (isset($input['file']))
		{
			@fclose($request->fp);
		}
	}

	/**
	 * Downloads a file from CloudFiles back to your server
	 *
	 * @param string   $container The name of the CloudFiles container
	 * @param string   $path      The path to the file to download
	 * @param resource $fp        A file pointer, opened in write binary mode, to write out the downloaded file
	 * @param array    $headers   An array of headers to send during the download, e.g. ['Range' => '1-100']
	 *
	 * @return void
	 */
	public function downloadObject($container, $path, &$fp, $headers = array())
	{
		// Re-authenticate if necessary
		$this->authenticate();

		// Get the URL to list containers
		$url = $this->storageEndpoint . '/' . $this->apiVersion . '/' . $this->userContract . '/' . $container;
		$url = rtrim($url, '\\/');
		$path = ltrim($path, '\\/');
		$url .= '/' . $path;

		// Get the request object
		$request = new Request('GET', $url);
		$request->setHeader('X-Auth-Token', $this->token);

		if (!empty($headers))
		{
			foreach($headers as $k => $v)
			{
				$request->setHeader($k, $v);
			}
		}

		$request->fp = $fp;

		$request->getResponse();
	}

	/**
	 * Delete a file from CloudFiles
	 *
	 * @param string $container The name of the CloudFiles container
	 * @param string $path      The path to the file to download
	 *
	 * @return void
	 */
	public function deleteObject($container, $path)
	{
		// Re-authenticate if necessary
		$this->authenticate();

		// Get the URL to list containers
		$url = $this->storageEndpoint . '/' . $this->apiVersion . '/' . $this->userContract . '/' . $container;
		$url = rtrim($url, '\\/');
		$path = ltrim($path, '\\/');
		$url .= '/' . $path;

		// Get the request object
		$request = new Request('DELETE', $url);
		$request->setHeader('X-Auth-Token', $this->token);
		//$request->setHeader('Accept', 'application/json');

		$request->getResponse();
	}
}
