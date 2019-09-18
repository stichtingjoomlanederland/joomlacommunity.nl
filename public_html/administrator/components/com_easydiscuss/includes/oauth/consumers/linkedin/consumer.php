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

// the PECL OAuth extension is not present, load our third-party OAuth library
require_once(JPATH_ADMINISTRATOR . '/components/com_easydiscuss/includes/oauth/consumers/oauth.php');

class LinkedInException extends Exception {}

class EDLinkedInConsumer {

	// Linkedin API V2 end-points
	const _URL_API = 'https://api.linkedin.com';
	const _URL_API_V2 = 'https://api.linkedin.com/v2';
	const _URL_AUTH_V2 = 'https://www.linkedin.com/oauth/v2/authorization?response_type=code';
	const _URL_ACCESS_V2 = 'https://www.linkedin.com/oauth/v2/accessToken';
	const _URL_REVOKE = 'https://api.linkedin.com/uas/oauth/invalidateToken';

	const _USER_CONSTANT = 'LINKEDIN_USER_ID_';

	const _SHARE_COMMENT_LENGTH = 700;
	const _SHARE_CONTENT_TITLE_LENGTH = 200;
	const _SHARE_CONTENT_DESC_LENGTH = 400;

	// oauth properties
	protected $callback = null;
	protected $token = null;
	protected $access_token = null;

	// application properties
	protected $application_key = null;
	protected $application_secret = null;

	public function __construct($config)
	{
		// bad data passed
		if (!is_array($config)) {
			throw new LinkedInException('LinkedIn->__construct(): bad data passed, $config must be of type array.');
		}

		$this->setApplicationKey($config['appKey']);
		$this->setApplicationSecret($config['appSecret']);
		$this->setCallbackUrl($config['callbackUrl']);
	}

	/**
	 * Set application key
	 *
	 * @since	4.10
	 * @access	public
	 */
	public function setApplicationKey($key)
	{
		$this->application_key = $key;
	}

	/**
	 * Set application secret
	 *
	 * @since	4.1.0
	 * @access	public
	 */
	public function setApplicationSecret($secret)
	{
		$this->application_secret = $secret;
	}

	/**
	* Set callback url after authentication
	*
	* @since 4.1.0
	* @access  public
	*/
	public function setCallbackUrl($url)
	{
		$this->callback = $url;
	}

	/**
	 * API to retrieve user email address for Oauth V2
	 *
	 * @since	4.1.7
	 * @access	public
	 */
	public function emailAddress($options = '?q=members&projection=(elements*(handle~))')
	{
		// check passed data
		if(!is_string($options)) {
			// bad data passed
			throw new LinkedInException('LinkedIn->emailAddress(): bad data passed, $options must be of type string.');
		}

		$query = self::_URL_API_V2 . '/emailAddress' . trim($options);
		$response = $this->fetch('GET', $query);

		return $this->checkResponse(200, $response);
	}

	/**
	 * Method to share the post (version 2.0)
	 *
	 * @since	4.1.7
	 * @access	public
	 */
	public function sharePost($action, $content, $private = true, $twitter = false, $company = false)
	{
		if (!empty($action) && !empty($content)) {

			// Process the media
			$mediaData = $this->processMedia($content);

			// Personal Share
			$author = 'urn:li:person:' . $content['userId'];

			// Organization Share (Company)
			if ($company) {
				$author = 'urn:li:organization:' . $company;
			}

			$data = array(
				'author' => $author,
				'lifecycleState' => 'PUBLISHED',
				'specificContent' =>
						array(
							'com.linkedin.ugc.ShareContent' =>
								array(
									'shareCommentary' => array('text' => $content['text']),
									'shareMediaCategory' => $mediaData->mediaCategory,
									'media' => array($mediaData->media)
								)
						),
				'visibility' => array('com.linkedin.ugc.MemberNetworkVisibility' => $content['visibility'])
			);

			$share_url = self::_URL_API_V2 . '/ugcPosts';
			$data = json_encode($data);

			// send request
			$response = $this->fetch('POST', $share_url, $data);

			return $this->checkResponse(201, $response);
		}
	}

	/**
	 * Method to process the media properties for sharing
	 *
	 * @since	4.1.7
	 * @access	public
	 */
	private function processMedia($content)
	{
		$obj = new stdClass();
		$obj->media = '';
		$obj->mediaCategory = 'NONE';

		// Get the media if exists
		if (isset($content['submitted-url'])) {
			$obj->mediaCategory = 'ARTICLE';
			$obj->media = array(
				'status' => 'READY',
				'title' => array('text' => $content['submitted-url-title']),
				'description' => array('text' => $content['submitted-url-desc']),
				'originalUrl' => $content['submitted-url']
			);
		}

		if (isset($content['submitted-image'])) {
			$obj->mediaCategory = 'IMAGE';

			// 1. Register the image to be uploaded.
			$url = self::_URL_API_V2 . '/assets?action=registerUpload';
			$data = array(
				'registerUploadRequest' => array(
					'recipes' => array('urn:li:digitalmediaRecipe:feedshare-image'),
					'owner' => 'urn:li:person:' . $content["userId"],
					'serviceRelationships' => array(
						array(
							'relationshipType' => 'OWNER',
							'identifier' => 'urn:li:userGeneratedContent'
						)
					)
				)
			);

			$data = json_encode($data);
			$response = $this->fetch('POST', $url, $data);

			// Get the upload url and asset.
			if ($response) {
				$response = json_decode($response['linkedin']);

				$uploadUrl = $response->value->uploadMechanism->{"com.linkedin.digitalmedia.uploading.MediaUploadHttpRequest"}->uploadUrl;
				$asset = $response->value->asset;

				// 2. Upload the image to LinkedIn.
				$photo = $content['submitted-image'];
				$imageFile = $photo->getPath('large');
				$mimeType = $photo->getMime('large');

				$imageBinary = class_exists('CURLFile', false) ? new CURLFile($imageFile, $mimeType, basename($imageFile)) : "@" . $imageFile;

				$header = array();
				$header[] = 'Authorization: Bearer ' . $this->access_token;
				$header[] = 'Content-Type:multipart/form-data';

				$ch = curl_init();
				curl_setopt($ch, CURLOPT_URL, $uploadUrl);
				curl_setopt($ch, CURLOPT_POST, true);
				curl_setopt($ch, CURLOPT_TIMEOUT, 100000);
				curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
				curl_setopt($ch, CURLOPT_POSTFIELDS, array('file' => $imageBinary));
				curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
				curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

				$result = curl_exec($ch);
				$error = curl_error($ch);
				$status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
				curl_close($ch);

				// 3. Create the image share.
				$obj->media = array(
					'status' => 'READY',
					'description' => array('text' => $content['text']),
					'media' => $asset,
					'title' => array('text' => $content['text'])
				);
			}
		}

		return $obj;
	}

	/**
	 * Method to share the post made in ED
	 *
	 * @since	4.1.0
	 * @access	public
	 */
	public function sharePostLegacy($action, $content, $private = TRUE, $twitter = FALSE , $companies = array())
	{
		// check the status itself
		if (!empty($action) && !empty($content)) {

			// prepare the share data per the rules above
			$share_flag = FALSE;
			$content_xml = NULL;

			switch($action) {
			case 'new':
				// share can be an article
				if (array_key_exists('title', $content) && array_key_exists('submitted-url', $content)) {
					// we have shared content, format it as needed per rules above
					$content_title = trim(htmlspecialchars(strip_tags(stripslashes($content['title']))));

					if (JString::strlen($content_title) > self::_SHARE_CONTENT_TITLE_LENGTH) {
						throw new LinkedInException('LinkedIn->share(): title length is too long - max length is ' . self::_SHARE_CONTENT_TITLE_LENGTH . ' characters.');
					}

					$content_xml .= '<content>
										<title>' . $content_title . '</title>
										<submitted-url>' . trim(htmlspecialchars($content['submitted-url'])) . '</submitted-url>';

					if (array_key_exists('submitted-image-url', $content)) {
						$content_xml .= '<submitted-image-url>' . trim(htmlspecialchars($content['submitted-image-url'])) . '</submitted-image-url>';
					}

					if (array_key_exists('description', $content)) {
						// $content_desc = trim(htmlspecialchars(strip_tags(stripslashes($content['description']))));
						$content_desc = $content['description'];

						if (JString::strlen($content_desc) > self::_SHARE_CONTENT_DESC_LENGTH) {
							throw new LinkedInException('LinkedIn->share(): description length is too long - max length is ' . self::_SHARE_CONTENT_DESC_LENGTH . ' characters.');
						}

						$content_xml .= '<description>' . $content_desc . '</description>';
					}

					$content_xml .= '</content>';
					$share_flag = TRUE;
				}

				// share can be just a comment
				if (array_key_exists('comment', $content)) {
					// comment located
					$comment = $content['comment'];

					if (JString::strlen($comment) > self::_SHARE_COMMENT_LENGTH) {
						throw new LinkedInException('LinkedIn->share(): comment length is too long - max length is ' . self::_SHARE_COMMENT_LENGTH . ' characters.');
					}

					$content_xml .= '<comment>' . $comment . '</comment>';
					$share_flag = TRUE;
				}

				break;
			case 'reshare':
				if (array_key_exists('id', $content)) {
					// put together the re-share attribution XML
					$content_xml .= '<attribution>
										<share>
											<id>' . trim($content['id']) . '</id>
										</share>
									</attribution>';

					// optional additional comment
					if (array_key_exists('comment', $content)) {
						// comment located
						$comment = htmlspecialchars(trim(strip_tags(stripslashes($content['comment']))));

						if (JString::strlen($comment) > self::_SHARE_COMMENT_LENGTH) {
							throw new LinkedInException('LinkedIn->share(): comment length is too long - max length is ' . self::_SHARE_COMMENT_LENGTH . ' characters.');
						}

						$content_xml .= '<comment>' . $comment . '</comment>';
					}

					$share_flag = TRUE;
				}
				break;
			default:
				// bad action passed
				throw new LinkedInException('LinkedIn->share(): share action is an invalid value, must be one of: share, reshare.');
				break;
			}

			// proceed with the sharing
			if ($share_flag) {
				// put all of the xml together
				$visibility = ($private) && !$companies ? 'connections-only' : 'anyone';
				$data = '<?xml version="1.0" encoding="UTF-8"?>
						   <share>
							 ' . $content_xml . '
							 <visibility>
							   <code>' . $visibility . '</code>
							 </visibility>
						   </share>';

				if (!empty($companies)) {
					foreach ($companies as $company) {
						$share_url = self::_URL_API . '/v1/companies/' . $company . '/shares';
					}
				} else {
					// create the proper url
					$share_url = self::_URL_API . '/v1/people/~/shares';
				}

				if ($twitter) {
					// update twitter as well
					$share_url .= '?twitter-post=true';
				}

				// send request
				$response = $this->fetch('POST', $share_url, $data);
			} else {
				// data contraints/rules not met, raise an exception
				throw new LinkedInException('LinkedIn->share(): sharing data constraints not met; check that you have supplied valid content and combinations of content to share.');
			}
		} else {
			// data missing, raise an exception
			throw new LinkedInException('LinkedIn->share(): sharing action or shared content is missing.');
		}

		return $this->checkResponse(201, $response);
	}

	/**
	 * Set access token required for API call
	 *
	 * @since	4.1.0
	 * @access	public
	 */
	public function setAccessToken($token)
	{
		// Ensure the data is array
		if (!is_null($token) && is_array($token)) {
			throw new LinkedInException('LinkedIn->setToken(): bad data passed, $access_token should not be in array format.');
		}

		$this->access_token = $token;
	}

	/**
	 * Revoke application access
	 *
	 * @since	4.1.0
	 * @access	public
	 */
	public function revoke()
	{
		// construct and send the request
		$response = $this->fetch('GET', self::_URL_REVOKE);

		// Check for successful request (a 200 response from LinkedIn server)
		return $this->checkResponse(200, $response);
	}

	/**
	 * General user profile retrieval function
	 *
	 * @since	4.1.7
	 * @access	public
	 */
	public function me($options = '?projection=(id,firstName,lastName,profilePicture(displayImage~:playableStreams))')
	{
		if (!is_string($options)) {
			throw new LinkedInException('LinkedIn->me(): bad data passed, $options must be of type string.');
		}

		$query = self::_URL_API_V2 . '/me' . trim($options);
		$response = $this->fetch('GET', $query);

		return $this->checkResponse(200, $response);
	}

	/**
	 * Retrieve token access for oauth2 authentication
	 *
	 * @since	4.1.0
	 * @access	public
	 */
	public function retrieveTokenAccess($code)
	{
		// check passed data
		if (!$code) {
			throw new LinkedInException('LinkedIn->retrieveTokenAccess(): bad data passed, string type is required for $code.');
		}

		$data = array();
		$data['grant_type'] = 'authorization_code';
		$data['code'] = $code;
		$data['redirect_uri'] = $this->callback;
		$data['client_id'] = $this->application_key;
		$data['client_secret'] = $this->application_secret;

		return $this->fetch('POST', self::_URL_ACCESS_V2, $data);
	}

	/**
	 * General fetch method for all API call
	 *
	 * @since	4.1.0
	 * @access	public
	 */
	protected function fetch($method, $url, $data = NULL, $parameters = array())
	{
		// check for cURL
		if (!extension_loaded('curl')) {
			throw new LinkedInException('LinkedIn->fetch(): PHP cURL extension does not appear to be loaded/present.');
		}

		try {
			// start cURL, checking for a successful initiation
			if (!$ch = curl_init()) {
				throw new LinkedInException('LinkedIn->fetch(): cURL did not initialize properly.');
			}

			// Construct headers
			$header = array();

			// Append oauth2 access token
			$header[] = 'Authorization: Bearer ' . $this->access_token;

			// json_encode all params values that are not strings
			if ($data) {

				if (!is_array($data) && !is_object($data)) {
					$header[] = 'Content-Type: text/xml; charset=UTF-8';
					curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
				} else {
					foreach ($data as $key => $value) {
						if (!is_string($value)) {
							$data[$key] = json_encode($value);
						}
					}

					$data = http_build_query($data, null, '&');
				}
			}

			// Construct curl options
			curl_setopt($ch, CURLOPT_URL, $url);
			curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
			curl_setopt($ch, CURLOPT_TIMEOUT, 100000);
			curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
			curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

			// Gather the response
			$return_data = array();
			$return_data['linkedin'] = curl_exec($ch);
			$return_data['info'] = curl_getinfo($ch);

			// Close the curl
			curl_close($ch);

			// check for throttling
			if ($this->isThrottled($return_data['linkedin'])) {
				throw new LinkedInException('LinkedIn->fetch(): throttling limit for this user/application has been reached for LinkedIn resource - ' . $url);
			}

			// no exceptions thrown, return the data
			return $return_data;
		} catch(EDOAuthException $e) {
			// oauth exception raised
			throw new LinkedInException('OAuth exception caught: ' . $e->getMessage());
		}
	}

	/**
	 * Determine if request is throttled by Linkedin
	 *
	 * @since	4.1.0
	 * @access	public
	 */
	public function isThrottled($response)
	{
		$return_data = FALSE;

		// check the variable
		if (!empty($response) && is_string($response)) {
			// we have an array and have a properly formatted LinkedIn response
			// store the response in a temp variable
			$temp_response = $this->xmlToArray($response);
			if ($temp_response !== FALSE) {
				// check to see if we have an error
				if (array_key_exists('error', $temp_response) &&
					$temp_response['error']['children']['status']['content'] == 403 && preg_match('/throttle/i', $temp_response['error']['children']['message']['content'])) {

					// we have an error, it is 403 and we have hit a throttle limit
					$return_data = TRUE;
				}
			}
		}

		return $return_data;
	}

	/**
	 * Method to retrieve lists of company that the user have
	 *
	 * @since	4.1.7
	 * @access	public
	 */
	public function getCompanyLists($options)
	{
		// check passed data
		if (!is_string($options)) {
			throw new LinkedInException('LinkedIn->getCompanyLists(): bad data passed, $options must be of type string.');
		}

		$query = self::_URL_API_V2 . '/organizationalEntityAcls?q=roleAssignee&' . trim($options);
		$response = $this->fetch('GET', $query);

		return $this->checkResponse(200, $response);
	}

	/**
	 * Method to retrieve company information
	 *
	 * @since	4.1.7
	 * @access	public
	 */
	public function company($options)
	{
		// check passed data
		if (!is_string($options)) {
			throw new LinkedInException('LinkedIn->company(): bad data passed, $options must be of type string.');
		}

		$query = self::_URL_API_V2 . '/organizations/' . $options;
		$response = $this->fetch('GET', $query);

		return $this->checkResponse(200, $response);
	}

	/**
	 * Determine the response type from each API call
	 *
	 * @since	4.1.0
	 * @access	public
	 */
	public function checkResponse($http_code_required, $response)
	{
		// check passed data
		if (!is_array($http_code_required)) {
			if (!is_int($http_code_required)) {
				throw new LinkedInException('LinkedIn->checkResponse(): $http_code_required must be an integer or an array of integer values');
			} else {
				$http_code_required = array($http_code_required);
			}
		}

		if (!is_array($response)) {
			throw new LinkedInException('LinkedIn->checkResponse(): $response must be an array');
		}

		// check for a match
		if (in_array($response['info']['http_code'], $http_code_required)) {
			// response found
			$response['success'] = TRUE;
		} else {
			// response not found
			$response['success'] = FALSE;
			$response['error']   = 'HTTP response from LinkedIn end-point was not code ' . implode(', ', $http_code_required);
		}

		return $response;
	}

	/**
	 * Converts passed XML data to an array
	 *
	 * @since	4.1.0
	 * @access	public
	 */
	public function xmlToArray($xml)
	{
		// check passed data
		if (!is_string($xml)) {
			// bad data possed
			throw new LinkedInException('LinkedIn->xmlToArray(): bad data passed, $xml must be a non-zero length string.');
		}

		$parser = xml_parser_create();
		xml_parser_set_option($parser, XML_OPTION_CASE_FOLDING, 0);
		xml_parser_set_option($parser, XML_OPTION_SKIP_WHITE, 1);

		if (xml_parse_into_struct($parser, $xml, $tags)) {
			$elements = array();
			$stack = array();

			foreach ($tags as $tag) {
				$index = count($elements);

				if ($tag['type'] == 'complete' || $tag['type'] == 'open') {
					$elements[$tag['tag']] = array();
					$elements[$tag['tag']]['attributes'] = (array_key_exists('attributes', $tag)) ? $tag['attributes'] : NULL;
					$elements[$tag['tag']]['content'] = (array_key_exists('value', $tag)) ? $tag['value'] : NULL;

					if ($tag['type'] == 'open') {
						$elements[$tag['tag']]['children'] = array();
						$stack[count($stack)] = &$elements;
						$elements = &$elements[$tag['tag']]['children'];
					}
				}

				if ($tag['type'] == 'close') {
					$elements = &$stack[count($stack) - 1];
					unset($stack[count($stack) - 1]);
				}
			}

			$return_data = $elements;
		} else {
			// not valid xml data
			$return_data = FALSE;
		}

		xml_parser_free($parser);
		return $return_data;
	}
}
