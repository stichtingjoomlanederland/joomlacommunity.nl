<?php
/**
 * Joomlatools Framework - https://www.joomlatools.com/developer/framework/
 *
 * @copyright	Copyright (C) 2011 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link		http://github.com/joomlatools/joomlatools-framework-files for the canonical source repository
 */

/**
 * Url Entity
 *
 * @author  Ercan Ozkaya <https://github.com/ercanozkaya>
 * @package Koowa\Component\Files
 */
class ComFilesModelEntityUrl extends KModelEntityAbstract
{
	/**
	 * Adapters to use for remote access
	 * @var array
	 */
	protected $_adapters = array();

	public function __construct(KObjectConfig $config)
	{
		parent::__construct($config);

		if (isset($config->adapters)) {
			$this->_adapters = $config->adapters;
		}
	}

	protected function _initialize(KObjectConfig $config)
	{
		if (empty($config->adapters)) {
			$config->adapters = array('curl', 'fsockopen', 'fopen');
		}
		elseif (is_string($config->adapters)) {
			$config->adapters = array($config->adapters);
		}

		parent::_initialize($config);
	}

    public function getProperty($name)
    {
        if($name == 'contents' && !isset($this->_data['contents']))
        {
            $url = $this->file;
            $response = $this->_fetch($url);

            $this->_data['contents'] = $response;
        }

        return parent::getProperty($name);
    }

	protected function _fetch($url)
	{
		$response = false;
		foreach ($this->_adapters as $i => $adapter)
		{
			try {
				$function = '_fetch'.ucfirst($adapter);
				$response = $this->$function($url);
				break;
			}
			catch (ComFilesDatabaseExceptionRemoteAdapterNotAvailable $e) {
				continue;
			}
			catch (ComFilesDatabaseExceptionRemoteAdapterError $e)
			{
				if ($i+1 < count($this->_adapters)) {
					continue;
				}
				else {
					throw $e;
				}
			}
		}

		return $response;
	}

	protected function _fetchCurl($url)
	{
		if (!function_exists('curl_init')) {
			throw new ComFilesDatabaseExceptionRemoteAdapterNotAvailable('Adapter does not exist');
		}

		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
		curl_setopt($ch, CURLOPT_MAXREDIRS,		 10);
		curl_setopt($ch, CURLOPT_TIMEOUT, 		 120);

		$response = curl_exec($ch);

		if (curl_errno($ch)) {
			throw new ComFilesDatabaseExceptionRemoteAdapterError('Curl Error: '.curl_error($ch));
		}

		$info = curl_getinfo($ch);
		if (isset($info['http_code']) && $info['http_code'] != 200) {
			$response = false;
		}

		curl_close($ch);

		return $response;
	}

	protected function _fetchFsockopen($url)
	{
		if (!in_array('tcp', stream_get_transports())) {
			throw new ComFilesDatabaseExceptionRemoteAdapterNotAvailable('Adapter does not exist');
		}

		$uri = $this->getObject('lib:http.url', array('url' => $url));

		$scheme = $uri->toString(KHttpUrl::SCHEME);
		$host   = $uri->toString(KHttpUrl::HOST);
		$port   = $uri->toString(KHttpUrl::PORT);
		$path   = $uri->toString(KHttpUrl::PATH | KHttpUrl::QUERY | KHttpUrl::FRAGMENT);

		if ($scheme == 'https://') {
			if (!in_array('ssl', stream_get_transports())) {
				throw new ComFilesDatabaseExceptionRemoteAdapterNotAvailable('fsockopen does not support SSL');
			}
			$host = 'ssl://'.$host;
			$port = 443;
		}
		elseif (!$port) {
			$port = 80;
		}

		if (!$path) {
			$path = '/';
		}

		$errno = null;
		$errstr = null;
		$fp = @fsockopen($host, $port, $errno, $errstr, 120);
		if (!$fp) {
			throw new ComFilesDatabaseExceptionRemoteAdapterError('PHP Socket Error: '.$errstr);
		}
		$out = "GET $path HTTP/1.1\r\n";
		$out .= "Host: $host\r\n";
		$out .= "Connection: Close\r\n\r\n";
		fwrite($fp, $out);

		$contents = '';
		while (!feof($fp)) {
			$contents .= fgets($fp, 1024);
		}
		fclose($fp);

		$response = false;
		list($headers, $response) = explode("\r\n\r\n", $contents, 2);

		if (!preg_match('#http/[0-9\.]+ 200 OK#i', $headers) || empty($response)) {
			$response = false;
		}

		return $response;
	}

	protected function _fetchFopen($url)
	{
		if (!ini_get('allow_url_fopen')) {
			throw new ComFilesDatabaseExceptionRemoteAdapterNotAvailable('Adapter does not exist');
		}

		$response = @file_get_contents($url);

		return $response;
	}
}
