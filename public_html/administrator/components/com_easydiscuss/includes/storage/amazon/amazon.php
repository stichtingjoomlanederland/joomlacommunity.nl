<?php
/**
* @package		EasyDiscuss
* @copyright	Copyright (C) 2010 - 2015 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasyDiscuss is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');

// Include amazon's autoloader
require_once(__DIR__ . '/autoloader.php');

// define("STDOUT", fopen(__DIR__ . '/log.txt', 'w'));

use Aws\S3\S3Client;

class EasyDiscussStorageAmazon implements EasyDiscussStorageInterface
{
	public $config = null;
	public $bucket = null;
	public $region = null;
	public $endpoint = 's3.amazonaws.com';
	public $secure = null;

	private $client = null;

	public function __construct()
	{
		$this->config = ED::config();

		// Get the access and secret keys
		$access = trim($this->config->get('amazon_access_key'));
		$secret = trim($this->config->get('amazon_access_secret'));

		// Determines if we should be using ssl
		$this->secure = $this->config->get('amazon_ssl');

		// Determines the bucket that we should use
		$this->bucket = rtrim($this->config->get('amazon_bucket'), '/');

		// Get the region
		$this->region = $this->config->get('amazon_region');

		// We need to construct the endpoint uri for non "US" regions
		if ($this->region != "us") {
			$this->endpoint = 's3-' . $this->region . '.amazonaws.com';
		}

		// Amazon renamed their standard us region to us-east-1
		if ($this->region == 'us') {
			$this->region = 'us-east-1';
		}

		$options = new stdClass();
		$options->credentials = array('key' => $access, 'secret' => $secret);
		$options->signature = 'v4';
		$options->region = $this->region;
		$options->version = 'latest';


		$options = (array) $options;

		$this->client = S3Client::factory($options);
	}

	/**
	 * Initializes a bucket
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return	
	 */
	public function init()
	{
		// We assume that either the system or the user has already created the bucket.
		if (!empty($this->bucket)) {
			return $this->bucket;
		}

		// Initialize to check if the container exists
		$jConfig = ED::jconfig();
		$bucket = str_ireplace('http://' , '' , JURI::root());
		$bucket = JFilterOutput::stringURLSafe($bucket);

		// Create a new container
		$exists = $this->containerExists($bucket);

		if (!$exists) {
			$result = $this->createContainer($bucket);
		}

		return $bucket;
	}

	/**
	 * Checks if the provided bucket exists on S3
	 *
	 * @since	1.4.6
	 * @access	public
	 * @param	string
	 * @return	
	 */
	public function containerExists($bucket)
	{
		// Create a new container
		$exists = $this->client->doesBucketExist($bucket);

		if (!$exists) {
			return false;
		}

		return true;
	}

	/**
	 * Creates a new container on Amazon S3
	 *
	 * @since	1.4.6
	 * @access	public
	 * @param	string
	 * @return	
	 */
	public function createContainer($container)
	{
		$options = new stdClass();
		$options->Bucket = $container;

		// Since this amazon API is version 2, we should not be passing in the location constraint if this region is us standard.
		if ($this->region != 'us-east-1') {
			$options->LocationConstraint = $this->region;
		}
		
		$options = (array) $options;

		$result = $this->client->createBucket($options);

		if (isset($result['RequestId'])) {
			return true;
		}

		return false;
	}

	/**
	 * Returns a list of buckets
	 *
	 * @since	1.4.6
	 * @access	public
	 * @param	string
	 * @return	
	 */
	public function getContainers()
	{
		$result = $this->client->listBuckets();

		if (!isset($result['Buckets'])) {
			return array();
		}

		$buckets = $result['Buckets'];

		return $buckets;
	}

	/**
	 * Returns the absolute path to the object
	 *
	 * @since	1.0
	 * @access	public
	 * @param	int		The storage id
	 * @return	string	The absolute URI to the object
	 */
	public function getPermalink($relativePath)
	{
		// Ensure that the preceeding / is removed
		$relativePath = ltrim($relativePath, '/');

		$paths = explode('/', $this->bucket);

		$subfolder = false;
		$base = $paths[0];

		if (count($paths) > 1) {
			unset($paths[0]);
			$subfolder = implode('/', $paths);
		}

		$url = $this->secure ? 'https://' : 'http://';

		if ($url == 'https://' || stristr($base, '_') !== false) {
			$url .= $this->endpoint . '/' . $base . '/';
		} else {
			$url .= $this->endpoint . '/' . $base . '/';
		}

		if ($subfolder) {
			$url .= rtrim($subfolder, '/') . '/';
		}

		$url .= $relativePath;

		return $url;
	}

	/**
	 * Uploads a file to Amazon S3
	 *
	 * @since	4.0
	 * @access	public
	 */
	public function upload($fileName, $source, $destination, $deleteOriginalFile = false)
	{
		jimport('joomla.filesystem.file');

		if (!JFile::exists($source)) {
			return false;
		}

		// We always want to remove JPATH_ROOT from the destination
		$destination = str_ireplace(JPATH_ROOT, '', $destination);

		$options = new stdClass();
		$options->Bucket = $this->bucket;
		$options->Key = $destination;
		$options->SourceFile = $source;
		$options->ACL = "public-read";
		$options->ContentType = "application/octet-stream";
		$options->ContentDisposition = "attachment; filename=" . $fileName;

		$options = (array) $options;

		$result = $this->client->putObject($options);

		// Here we assume that if there is an "ObjectURL" returned, it is success.
		if (isset($result["ObjectURL"])) {

			// Delete the original source if needed
			if ($deleteOriginalFile) {
				JFile::delete($source);
			}
			
			return true;
		}

		return false;
	}

	/**
	 * Pulls a file from the remote repositor
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string	The relative path to the file
	 * @return
	 */
	public function download($targetFile, $relativePath)
	{
		$obj = $this->client->getObject(array('Bucket' => $this->bucket, 'Key' => $relativePath));

		if (!isset($obj["Body"])) {
			return false;
		}

		// Try to write
		JFile::write($targetFile, $obj["Body"]);

		return true;
	}

	/**
	 * Deletes a file
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return	
	 */
	public function delete($path)
	{
		// Ensure that leading / is removed
		$relativePath = str_ireplace(JPATH_ROOT, '', $path);

		// Finally delete the last item
		$result = $this->client->deleteObject(array('Bucket' => $this->bucket, 'Key' => $relativePath));

		return true;
	}
}
