<?php
/**
 * Copyright (c) 2009, RealDolmen
 * All rights reserved.
 *
 * Redistribution and use in source and binary forms, with or without
 * modification, are permitted provided that the following conditions are met:
 *     * Redistributions of source code must retain the above copyright
 *       notice, this list of conditions and the following disclaimer.
 *     * Redistributions in binary form must reproduce the above copyright
 *       notice, this list of conditions and the following disclaimer in the
 *       documentation and/or other materials provided with the distribution.
 *     * Neither the name of RealDolmen nor the
 *       names of its contributors may be used to endorse or promote products
 *       derived from this software without specific prior written permission.
 *
 * THIS SOFTWARE IS PROVIDED BY RealDolmen ''AS IS'' AND ANY
 * EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED
 * WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE
 * DISCLAIMED. IN NO EVENT SHALL RealDolmen BE LIABLE FOR ANY
 * DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES
 * (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES;
 * LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND
 * ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT
 * (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS
 * SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 *
 * @category   Microsoft
 * @package    Microsoft
 * @copyright  Copyright (c) 2009, RealDolmen (http://www.realdolmen.com)
 * @license    http://phpazure.codeplex.com/license
 */

namespace Akeeba\Engine\Postproc\Connector\Azure\Credentials;

// Protection against direct access
defined('AKEEBAENGINE') or die();

use Akeeba\Engine\Postproc\Connector\Azure\AzureStorage;
use Akeeba\Engine\Postproc\Connector\Azure\Credentials;
use Akeeba\Engine\Postproc\Connector\Azure\Exception\Api;
use Akeeba\Engine\Postproc\Connector\Azure\Http\Transport;

/**
 * @category   Microsoft
 * @package    Microsoft_WindowsAzure
 * @copyright  Copyright (c) 2009, RealDolmen (http://www.realdolmen.com)
 * @license    http://phpazure.codeplex.com/license
 */
class Sharedsignature extends Credentials
{
	/**
	 * Permission set
	 *
	 * @var array
	 */
	protected $_permissionSet = array();

	/**
	 * Creates a new Sharedsignature instance
	 *
	 * @param string  $accountName     Account name for Windows Azure
	 * @param string  $accountKey      Account key for Windows Azure
	 * @param boolean $usePathStyleUri Use path-style URI's
	 * @param array   $permissionSet   Permission set
	 */
	public function __construct($accountName = Credentials::DEVSTORE_ACCOUNT, $accountKey = Credentials::DEVSTORE_KEY, $usePathStyleUri = false, $permissionSet = array())
	{
		parent::__construct($accountName, $accountKey, $usePathStyleUri);
		$this->_permissionSet = $permissionSet;
	}

	/**
	 * Get permission set
	 *
	 * @return array
	 */
	public function getPermissionSet()
	{
		return $this->_permissionSet;
	}

	/**
	 * Set permisison set
	 *
	 * Warning: fine-grained permissions should be added prior to coarse-grained permissions.
	 * For example: first add blob permissions, end with container-wide permissions.
	 *
	 * Warning: the signed access signature URL must match the account name of the
	 * Sharedsignature instance
	 *
	 * @param array $value Permission set
	 *
	 * @throws Api
	 */
	public function setPermissionSet($value = array())
	{
		foreach ($value as $url)
		{
			if (strpos($url, $this->_accountName) === false)
			{
				throw new Api('The permission set can only contain URLs for the account name specified in the SharedSignature instance.');
			}
		}
		$this->_permissionSet = $value;
	}

	/**
	 * Create signature
	 *
	 * @param string $path        Path for the request
	 * @param string $resource    Signed resource - container (c) - blob (b)
	 * @param string $permissions Signed permissions - read (r), write (w), delete (d) and list (l)
	 * @param string $start       The time at which the Shared Access Signature becomes valid.
	 * @param string $expiry      The time at which the Shared Access Signature becomes invalid.
	 * @param string $identifier  Signed identifier
	 *
	 * @return string
	 */
	public function createSignature($path = '/', $resource = 'b', $permissions = 'r', $start = '', $expiry = '', $identifier = '')
	{
		// Determine path
		if ($this->_usePathStyleUri)
		{
			$path = substr($path, strpos($path, '/'));
		}

		// Add trailing slash to $path
		if (substr($path, 0, 1) !== '/')
		{
			$path = '/' . $path;
		}

		// Build canonicalized resource string
		$canonicalizedResource = '/' . $this->_accountName;
		if ($this->_usePathStyleUri)
		{
			$canonicalizedResource .= '/' . $this->_accountName;
		}
		$canonicalizedResource .= $path;

		// Create string to sign
		$stringToSign = array();
		$stringToSign[] = $permissions;
		$stringToSign[] = $start;
		$stringToSign[] = $expiry;
		$stringToSign[] = $canonicalizedResource;
		$stringToSign[] = $identifier;

		$stringToSign = implode("\n", $stringToSign);
		$signature = base64_encode(hash_hmac('sha256', $stringToSign, $this->_accountKey, true));

		return $signature;
	}

	/**
	 * Create signed query string
	 *
	 * @param string $path        Path for the request
	 * @param string $queryString Query string for the request
	 * @param string $resource    Signed resource - container (c) - blob (b)
	 * @param string $permissions Signed permissions - read (r), write (w), delete (d) and list (l)
	 * @param string $start       The time at which the Shared Access Signature becomes valid.
	 * @param string $expiry      The time at which the Shared Access Signature becomes invalid.
	 * @param string $identifier  Signed identifier
	 *
	 * @return string
	 */
	public function createSignedQueryString($path = '/', $queryString = '', $resource = 'b', $permissions = 'r', $start = '', $expiry = '', $identifier = '')
	{
		// Parts
		$parts = array();
		if ($start !== '')
		{
			$parts[] = 'st=' . urlencode($start);
		}
		$parts[] = 'se=' . urlencode($expiry);
		$parts[] = 'sr=' . $resource;
		$parts[] = 'sp=' . $permissions;
		if ($identifier !== '')
		{
			$parts[] = 'si=' . urlencode($identifier);
		}
		$parts[] = 'sig=' . urlencode($this->createSignature($path, $resource, $permissions, $start, $expiry, $identifier));

		// Assemble parts and query string
		if ($queryString != '')
		{
			$queryString .= '&';
		}
		$queryString .= implode('&', $parts);

		return $queryString;
	}

	/**
	 * Permission matches request?
	 *
	 * @param string $permissionUrl      Permission URL
	 * @param string $requestUrl         Request URL
	 * @param string $resourceType       Resource type
	 * @param string $requiredPermission Required permission
	 *
	 * @return string Signed request URL
	 */
	public function permissionMatchesRequest($permissionUrl = '', $requestUrl = '', $resourceType = AzureStorage::RESOURCE_UNKNOWN, $requiredPermission = Credentials::PERMISSION_READ)
	{
		// Build requirements
		$requiredResourceType = $resourceType;
		if ($requiredResourceType == AzureStorage::RESOURCE_BLOB)
		{
			$requiredResourceType .= AzureStorage::RESOURCE_CONTAINER;
		}

		// Parse permission url
		$parsedPermissionUrl = parse_url($permissionUrl);

		// Parse permission properties
		$permissionParts = explode('&', $parsedPermissionUrl['query']);

		// Parse request url
		$parsedRequestUrl = parse_url($requestUrl);

		// Check if permission matches request
		$matches = true;
		foreach ($permissionParts as $part)
		{
			list($property, $value) = explode('=', $part, 2);
			if ($property == 'sr')
			{
				$matches = $matches && (strpbrk($value, $requiredResourceType) !== false);
			}
			if ($property == 'sp')
			{
				$matches = $matches && (strpbrk($value, $requiredPermission) !== false);
			}
		}

		// Ok, but... does the resource match?
		$matches = $matches && (strpos($parsedRequestUrl['path'], $parsedPermissionUrl['path']) !== false);

		// Return
		return $matches;
	}

	/**
	 * Sign request URL with credentials
	 *
	 * @param string $requestUrl         Request URL
	 * @param string $resourceType       Resource type
	 * @param string $requiredPermission Required permission
	 *
	 * @return string Signed request URL
	 */
	public function signRequestUrl($requestUrl = '', $resourceType = AzureStorage::RESOURCE_UNKNOWN, $requiredPermission = Credentials::PERMISSION_READ)
	{
		// Look for a matching permission
		foreach ($this->getPermissionSet() as $permittedUrl)
		{
			if ($this->permissionMatchesRequest($permittedUrl, $requestUrl, $resourceType, $requiredPermission))
			{
				// This matches, append signature data
				$parsedPermittedUrl = parse_url($permittedUrl);

				if (strpos($requestUrl, '?') === false)
				{
					$requestUrl .= '?';
				}
				else
				{
					$requestUrl .= '&';
				}

				$requestUrl .= $parsedPermittedUrl['query'];

				// Return url
				return $requestUrl;
			}
		}

		// Return url, will be unsigned...
		return $requestUrl;
	}

	/**
	 * Sign request with credentials
	 *
	 * @param string  $httpVerb           HTTP verb the request will use
	 * @param string  $path               Path for the request
	 * @param string  $queryString        Query string for the request
	 * @param array   $headers            x-ms headers to add
	 * @param boolean $forTableStorage    Is the request for table storage?
	 * @param string  $resourceType       Resource type
	 * @param string  $requiredPermission Required permission
	 *
	 * @return array Array of headers
	 */
	public function signRequestHeaders($httpVerb = Transport::VERB_GET, $path = '/', $queryString = '', $headers = null, $forTableStorage = false, $resourceType = AzureStorage::RESOURCE_UNKNOWN, $requiredPermission = Credentials::PERMISSION_READ)
	{
		return $headers;
	}
}
