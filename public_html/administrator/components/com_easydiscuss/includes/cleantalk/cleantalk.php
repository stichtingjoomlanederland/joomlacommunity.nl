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

require_once(dirname(__FILE__) . '/cleantalk.class.php');

class EasyDiscussCleantalk extends EasyDiscuss
{
	public $config_url = 'http://moderate.cleantalk.org';
	public $params = null;
	public $data = null;
	public $user = null;
	public $ip = null;
	public $result = null;

	public function __construct()
	{
		parent::__construct();
	}

	/**
	 * Builds cleantalkrequest object.
	 *
	 * @since   4.1.6
	 * @access  public
	 */
	public function build($method, $args = array())
	{
		$this->user = JFactory::getUser();
		$this->data = array();

		if (isset($args['user'])) {
			$this->user = $args['user'];
		}

		if (isset($args['data'])) {
			$this->data = $args['data'];
		}

		$cleantalk = new CleantalkRequest();
		$cleantalk->auth_key = $this->config->get('antispam_cleantalk_key', '');
		$cleantalk->method_name = $method;
		$cleantalk->sender_email = $this->user->email;
		$cleantalk->sender_nickname = $this->user->name;

		// Used in checking spam comment.
		if (isset($args['message'])) {
			if (!is_string($args['message'])) {
				$messageObj = (object) $args['message'];

				$args['message'] = $messageObj->comment;
			}

			$cleantalk->message = $args['message'];
		}

		// Must not be changed.
		$cleantalk->agent = 'php-api';

		$cleantalk->stop_words = 'stop_word';
		$cleantalk->sender_ip = $this->getUserIP();
		$cleantalk->js_on = 1;
		$cleantalk->stoplist_check = 1;
		$cleantalk->submit_time = $this->submitTime();

		$languageCode = JLanguageHelper::getLanguages('lang_code');
		$languageTag = JFactory::getLanguage()->getTag();
		$cleantalk->response_lang = $languageCode[$languageTag]->sef;

		if ($this->data) {
			$cleantalk->post_info = json_encode($this->data);
		}

		$senderInfo = json_encode(array(
			'page_url' => htmlspecialchars(@$_SERVER['SERVER_NAME'].@$_SERVER['REQUEST_URI']),
			'REFFERRER' => htmlspecialchars(@$_SERVER['HTTP_REFERER']),
			'USER_AGENT' => htmlspecialchars(@$_SERVER['HTTP_USER_AGENT']),
			'fields_number' => sizeof($_POST)
			));

		$cleantalk->sender_info = ($senderInfo === false) ? '': $senderInfo;
		$cleantalk->all_headers = json_encode(apache_request_headers());

		return $cleantalk;
	}

	/**
	 * Checks for spam in conversation
	 *
	 * @since	4.1.6
	 * @access	public
	 */
	public function contentValidate(&$discussion, $isQuestion = false)
	{
		// Skip this if that is site admin
		if (ED::isSiteAdmin() || !$this->config->get('antispam_cleantalk_key') || !$this->config->get('antispam_cleantalk', true)) {
			return false;
		}

		$content = $discussion->content;

		// validate for the discussion title as well
		if ($isQuestion) {
			$content = $discussion->title . $discussion->content;	
		}

		// Send request to Cleantalk
		$response = $this->sendRequest($this->build('check_message', array('message' => $content)), 'isAllowMessage');

		// Response returned needs to be 0 to be flagged as spam
		if (!$response->allow) {
			return true;
		}

		// return false if all good.
		return false;	
	}

	/**
	 * Responsible to get user's IP.
	 *
	 * @since   4.1.6
	 * @access  public
	 */
	public function getUserIP()
	{
		if (!$this->ip) {
			if (getenv('HTTP_CLIENT_IP')) {
				$this->ip = getenv('HTTP_CLIENT_IP');
				return $this->ip;
			}

			if(getenv('HTTP_X_FORWARDED_FOR')) {
				$this->ip = getenv('HTTP_X_FORWARDED_FOR');
				return $this->ip;
			}

			if(getenv('HTTP_X_FORWARDED')) {
				$this->ip = getenv('HTTP_X_FORWARDED');
				return $this->ip;
			}

			if(getenv('HTTP_FORWARDED_FOR')) {
				$this->ip = getenv('HTTP_FORWARDED_FOR');
				return $this->ip;
			}
			
			if(getenv('HTTP_FORWARDED')) {
			   $this->ip = getenv('HTTP_FORWARDED');
			   return $this->ip;
			}

			if(getenv('REMOTE_ADDR')) {
				$this->ip = getenv('REMOTE_ADDR');
				return $this->ip;
			}
		}

		return $this->ip;
	}	

	/**
	 * Responsible to process the submit time.
	 *
	 * @since   4.1.6
	 * @access  public
	 */
	public function submitTime()
	{
		$submitTime = 12;

		// TODO: Later, we'll need to implement this session feature to correctly capture spammers.

		// if (isset($_SESSION['CT_SUBMIT_FORM_TIME'])) {
		// 	$submitTime = time() - (int) $_SESSION['CT_SUBMIT_FORM_TIME'];
		// }

		return $submitTime;
	}	

	/**
	 * Send request to cleantalk engine.
	 *
	 * @since   2.1
	 * @access  public
	 */
	private function sendRequest($ctRequest, $method = 'isAllowUser')
	{
		$cleantalk = new Cleantalk();
		$cleantalk->server_url = $this->config_url;

		$result = $cleantalk->$method($ctRequest);

		return $result;
	}	
}	