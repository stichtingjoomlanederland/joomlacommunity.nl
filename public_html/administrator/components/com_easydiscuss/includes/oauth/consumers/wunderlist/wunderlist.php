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

class EasyDiscussWunderlist extends EasyDiscuss
{
	public $key = null;
	public $secret = null;
	public $callback = null;

	public $userAccessToken = null;

	public function __construct($key = '', $secret = '', $callback = '')
	{
		$this->config = ED::config();

		if (!$key) {
			$key = $this->config->get('main_autopost_wunderlist_id');
		}

		if (!$secret) {
			$secret = $this->config->get('main_autopost_wunderlist_secret');
		}

		if (!$callback) {
			$callback = rtrim(JURI::root(), '/') . '/administrator/index.php?option=com_easydiscuss&controller=autoposting&task=grant&type=wunderlist';
		}

		$this->key = $key;
		$this->secret = $secret;
		$this->callback	= $callback;
	}

	/**
	 * Retrieves the callback url
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return	
	 */
	public function getCallbackUrl()
	{
		return $this->callback;
	}

	/**
	 * Generates a request token
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return	
	 */
	public function getRequestToken()
	{
		$obj = new stdClass();
		$obj->token = 'wunderlist';
		$obj->secret = 'wunderlist';

		return $obj;
	}

	/**
	 * Get the verifier code that is sent back by Facebook
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return	
	 */
	public function getVerifier()
	{
		$verifier = JRequest::getVar('code', '', 'default');

		return $verifier;
	}

	/**
	 * Retrieves the authorization end point url
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return	
	 */
	public function getAuthorizationURL($requestToken)
	{
		$url = 'https://www.wunderlist.com/oauth/authorize?client_id=' . $this->key . '&redirect_uri=' . urlencode($this->callback);

		return $url;
	}


	/**
	 * Retrieves the access token given the request token, secret and verifier code.
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return	
	 */
	public function getAccessTokens($token, $secret, $code)
	{
		$params = array('code' => urlencode($code), 'client_id' => urlencode($this->key), 'client_secret' => urlencode($this->secret));
		$str = 'code=' . $params['code'] . '&client_id=' . $params['client_id'] . '&client_secret=' . $params['client_secret'];

		$ch = curl_init('https://www.wunderlist.com/oauth/access_token');
		curl_setopt($ch, CURLOPT_POST, count($params));
		curl_setopt($ch, CURLOPT_POSTFIELDS, $str);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

		$output = curl_exec($ch);
		curl_close($ch);

		$result = json_decode($output);

		$obj = new stdClass();
		$obj->token = $result->access_token;
		$obj->secret = $code;
		$obj->params = '';
		$obj->expires = '';

		return $obj;
	}

	/**
	 * Retrieves the list to post to 
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return	
	 */
	public function getAutopostLists()
	{
		// Get a list of stored lists
		$storedLists = $this->config->get('main_autopost_wunderlist_list_id', array());

		if ($storedLists) {
			$storedLists = explode(',', $storedLists);
		}

		return $storedLists;
	}

	/**
	 * Shares the item on Wunderlist
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return	
	 */
	public function share(EasyDiscussPost $post)
	{
        $permalink = rtrim(JURI::root(), '/') . $post->getPermalink();
        
        $lists = $this->getAutopostLists();

        foreach ($lists as $listId) {

	        $params = array('list_id' => (int) $listId, 'title' => $permalink);
	        $paramsStr = json_encode($params);

	        // Set a due date?
	        $ch = curl_init('https://a.wunderlist.com/api/v1/tasks');

	        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");                                                                     
	        curl_setopt($ch, CURLOPT_POSTFIELDS, $paramsStr);
	        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
	            'X-Access-Token: ' . $this->access_token,
	            'X-Client-ID: ' . $this->key,
	            'Content-Type: application/json',
	            'Content-Length: ' . strlen($paramsStr)
	            ));
	        $output = curl_exec($ch);
	        curl_close($ch);

	        $result = json_decode($output);
        }

        return true;
	}

	/**
	 * Set the access tokens
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return	
	 */
	public function setAccess($access)
	{
		$access = json_decode($access);
		$options = array('oauth_token' => $access->token, 'oauth_token_secret' => $access->secret);

		$this->access_token = $access->token;
	}

	/**
	 * Allows caller to revoke the access which was given by Facebook
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return	
	 */
	public function revokeApp()
	{
		return true;
	}

	/**
	 * Retrieves a list of groups the user owns.
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return	
	 */
	public function getLists()
	{
		$url = 'https://a.wunderlist.com/api/v1/lists';

		$ch = curl_init($url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'X-Access-Token: ' . $this->access_token,
            'X-Client-ID: ' . $this->key,
            'Content-Type: application/json'
        ));

		$output = curl_exec($ch);
		curl_close($ch);

		$result = json_decode($output);

		return $result;
	}

	/**
	 * Retrieves a list of pages a user owns.
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return	
	 */
	public function getPages()
	{
		// Get a list of pages associated to this user
		$result = parent::api('/me/accounts', array('access_token' => $this->userAccessToken, 'limit' => 999999));

		$pages = array();

		if (!$result) {
			return $pages;
		}

		foreach ($result['data'] as $page) {
			$pages[] = (object) $page;
		}

		return $pages;
	}
}
