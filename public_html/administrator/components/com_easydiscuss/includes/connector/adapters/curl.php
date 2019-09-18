<?php
/**
* @package		EasyDiscuss
* @copyright	Copyright (C) 2010 - 2019 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasyDiscuss is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');

class EasyDiscussConnectorCurl
{
	public $defaultOptions	= array(
										CURLOPT_CONNECTTIMEOUT	=> 15,
										CURLOPT_RETURNTRANSFER	=> true,
										CURLOPT_TIMEOUT			=> 60,
										CURLOPT_USERAGENT		=> 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10.8; rv:23.0) Gecko/20100101 Firefox/23.0',
										CURLOPT_HEADER			=> true,
										CURLOPT_CAINFO			=> ''
									);

	public $options = array();
	public $handles = array();
	public $headers = array();
	public $urls = array();

	/**
	 * If there's a redirection, we know where's the final destination.
	 * @var	string
	 */
	public $finalUrls = array();

	public $handle = null;
	public $result = array();

	public $redirects = array(300, 301, 302, 303, 304, 305, 306, 307);
	public $args = array();
	public $current	= '';

	// for Joomla proxy settings.
	public $proxyEnable = null;
	public $proxyHost = null;
	public $proxyPort = null;
	public $proxyUser = null;
	public $proxyPass = null;
	public $proxyEnabled = null;
	public $proxy = null;
	public $proxyauth = null;

	public function __construct()
	{
		$this->handle = curl_multi_init();

		$jConfig = ED::jConfig();

		$this->proxyEnable = $jConfig->get('proxy_enable');
		$this->proxyHost = $jConfig->get('proxy_host');
		$this->proxyPort = $jConfig->get('proxy_port');
		$this->proxyUser = $jConfig->get('proxy_user');
		$this->proxyPass = $jConfig->get('proxy_pass');
		$this->proxyEnabled = false;

		if ($this->proxyEnable && $this->proxyHost && $this->proxyPort && $this->proxyUser && $this->proxyPass) {

			$this->proxy = $this->proxyHost . ':' . $this->proxyPort;
			$this->proxyauth = $this->proxyUser . ':' . $this->proxyPass;
			$this->proxyEnabled = true;
		}		
	}

	public function addUrl($url)
	{
		$this->urls[$url] = $url;
		$this->current = $url;
		$this->options[$url] = $this->defaultOptions;

		// We need to set the local ssl verifier
		$this->options[$url][CURLOPT_CAINFO] = dirname(__FILE__) . '/cacert.pem';
		$this->options[$url][CURLOPT_SSL_VERIFYPEER] = false;

		return true;
	}

	public function addQuery($key, $value)
	{
		$this->args[$this->current][$key] = $value;
	}

	public function addLength( $length )
	{
		$this->options[$this->current][CURLOPT_RANGE] = $length;
		$this->options[$this->current][CURLOPT_HEADER] = false;
	}

	public function useHeadersOnly()
	{
		$this->options[$this->current][CURLOPT_HEADER]	= true;
		$this->options[$this->current][CURLOPT_NOBODY]	= true;

		return true;
	}

	public function execute()
	{
		$running = null;

		foreach ($this->urls as $url) {
			$this->handles[$url] = curl_init($url);

			// If this is a post request, then we should add the necessary post data
			if (isset($this->options[$url][CURLOPT_POST]) && $this->options[$url][CURLOPT_POST] === true) {
				$this->options[$url][CURLOPT_POSTFIELDS] = http_build_query($this->args[$url]);
			}

			// check for whether user got enable proxy server setting in joomla global configuration or not
			if ($this->proxyEnabled) {
				$this->options[$url][CURLOPT_PROXY] = $this->proxy;
				$this->options[$url][CURLOPT_PROXYUSERPWD] = $this->proxyauth;
				$this->options[$url][CURLOPT_FOLLOWLOCATION] = true;
			}

			// Set options for specific urls.
			curl_setopt_array($this->handles[$url], $this->options[$url]);

			// Add the handle into the multi handle
			curl_multi_add_handle($this->handle, $this->handles[$url]);
		}

		do {
			curl_multi_exec($this->handle, $running);
		} while($running > 0);

		foreach ($this->handles as $key => $handle) {
			
			$code = curl_getinfo($handle, CURLINFO_HTTP_CODE);

			if (in_array($code, $this->redirects)) {
				
				// Debugging only.
				// $error = curl_error( $handle);
				$headers = curl_multi_getcontent($handle);

				$this->executeRedirects($handle, $key, $code, $headers);
			
			} else {
			
				$contents = curl_multi_getcontent($handle);

				// if the user configured proxy server in joomla, it will injected this response "HTTP/1.0/1.1 200 Connection established" string from the header
				if ($this->proxyEnabled && (stripos($contents, "HTTP/1.1 200 Connection established\r\n\r\n") !== false || stripos($contents, "HTTP/1.0 200 Connection established\r\n\r\n") !== false)) {
					$contents = str_ireplace("HTTP/1.1 200 Connection established\r\n\r\n", '', $contents);
					$contents = str_ireplace("HTTP/1.0 200 Connection established\r\n\r\n", '', $contents);
				}

				// We only want to split it once.
				$data = explode("\r\n\r\n", $contents, 2);

				$obj = new stdClass();
				$obj->headers = isset($data[0]) ? $data[0] : '';
				$obj->contents = isset($data[1]) ? $data[1] : '';

				$this->finalUrls[$key] = $key;
				$this->result[$key] = $obj;
			}

			curl_multi_remove_handle($this->handle, $handle);
		}

		curl_multi_close($this->handle);
		return true;
	}

	public function executeRedirects($handle, $key, $code = '', $headers = '')
	{
		static $loops = 0;
		static $maxLoops = 5;

		if ($loops++ >= $maxLoops) {
			$loops = 0;
			return false;
		}

		if ($loops == 1) {
			$last_url = parse_url(curl_getinfo($handle, CURLINFO_EFFECTIVE_URL));
		}

		// This block will be executed second time onwards.
		if ($loops > 1) {
			
			$contents = curl_exec($handle);

			// We only want to split it once.
			$data = explode("\r\n\r\n", $contents, 2);

			$headers = isset($data[0]) ? $data[0] : '';
			$contents = isset($data[1]) ? $data[1] : '';

			$code = curl_getinfo($handle, CURLINFO_HTTP_CODE);
			$last_url = parse_url(curl_getinfo($handle, CURLINFO_EFFECTIVE_URL));

			curl_close($handle);
		}

		// This block will be executed the first time.
		if ($code == 301 || $code == 302 || $code == 303) {
			$matches = array();

			if (strpos($headers, "HTTP/2") === 0) {
				// Check if the site using http/2 we need to use small letter
				preg_match('/location:(.*?)\n/', $headers, $matches);
			} else {
				// Most sites
				preg_match('/Location:(.*?)\n/', $headers, $matches);
			}

			// Cnn.com is pretty peticular.
			if (!$matches) {
				preg_match('/Location: (.*)/', $headers, $matches);
			}

			// Get the last item from the matched url
			$url = @parse_url(trim(array_pop($matches)));

			if (!$url) {
				$loops = 0;
				return $newdata;
			}

			if (isset($url['scheme']) && !$url['scheme']) {
				$url['scheme'] = $last_url['scheme'];
			}

			if (isset($url['host']) && !$url['host']) {
				$url['host'] = $last_url['host'];
			}

			if (isset($url['path']) && !$url['path']) {
				$url['path'] = $last_url['path'];
			}

			// If the new url does not have scheme, use the previous one.
			$scheme = isset( $url[ 'scheme' ] ) ? $url[ 'scheme' ] : $last_url[ 'scheme' ];
			$host = isset( $url[ 'host' ] ) ? $url[ 'host' ] : $last_url[ 'host' ];
			$path = isset( $url[ 'path' ] ) ? $url[ 'path' ] : $last_url[ 'path' ];
			$query = isset( $url[ 'query' ] ) ? '?' . $url[ 'query' ] : '';

			$newUrl = $scheme . '://' . $host . $path . $query;
			$newUrl	= urldecode( $newUrl );

			// dump( $newUrl );
			$newHandle = curl_init($newUrl);

			// Refresh with a new curl resource to avoid multi init issues.
			// curl_setopt( $handle , CURLOPT_URL , $newUrl );
			curl_setopt($newHandle, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($newHandle, CURLOPT_HEADER, true);
			curl_setopt($newHandle, CURLOPT_REFERER, 'http://www.google.com');
			curl_setopt($newHandle, CURLOPT_AUTOREFERER, true);

			$this->finalUrls[$key] = $newUrl;

			self::executeRedirects($newHandle, $key, $code, $headers);

		} else {

			$obj = new stdClass();
			$obj->headers = $headers;
			$obj->contents = $contents;

			$this->result[$key]	= $obj;
			$loops = 0;
		}
	}

	/**
	 * Returns the final URL
	 *
	 * @since	1.0
	 * @access	public
	 */
	public function getFinalUrl($key)
	{
		return $this->finalUrls[$key];
	}

	/**
	 * Returns the result that has already been executed.
	 *
	 * @since	1.0
	 * @access	public
	 */
	public function getResult($url = null, $withHeaders = false)
	{
		if (empty($url)) {
			$url = $this->current;
		}

		if (!isset($this->result[$url])) {
			return false;
		}

		if ($withHeaders) {
			return $this->result[$url]->headers . "\r\n\r\n" . $this->result[$url]->contents;
		}

		return $this->result[$url]->contents;
	}

	public function getResults()
	{
		return $this->result;
	}

	public function addHeader($value)
	{
		$this->options[$this->current][CURLOPT_HTTPHEADER] = $value;
	}

	public function addOption($key, $value)
	{
		$this->options[$this->current][$key] = $value;
	}

	public function addFile($resource, $size)
	{
		$this->addOption(CURLOPT_INFILE, $resource);
		$this->addOption(CURLOPT_INFILESIZE, $size);

		return true;
	}

	public function setMethod($method = 'GET')
	{
		switch($method)
		{
			case 'GET':
				$this->addOption(CURLOPT_HTTPGET, true);
			break;
			case 'POST':
				$this->addOption(CURLOPT_POST, true);
			break;
			case 'PUT':
				$this->addOption(CURLOPT_PUT, true);
			break;
		}

		return true;
	}
}
