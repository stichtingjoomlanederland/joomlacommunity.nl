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

class EasyDiscussGithub extends EasyDiscuss
{
	/**
	 * Creates a new gist anonymously
	 *
	 * @since   4.0
	 * @access  public
	 * @param   string
	 * @return  
	 */
	public function createGist($contents, $language)
	{
		$token = $this->getAccessToken();

		if (!$token) {
			return false;
		}

		// Load front end's language
		ED::loadLanguages();

		$files = array('snippet.' . $language => array('content' => $contents));
		$params = array('files' => $files, 'description' => JText::sprintf('COM_ED_GIST_SNIPPET_SHARED_FROM', JURI::root()), 'public' => true);
		$paramsStr = json_encode($params);

		// Set a due date?
		$ch = curl_init('https://api.github.com/gists');

		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
		curl_setopt($ch, CURLOPT_POSTFIELDS, $paramsStr);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_HTTPHEADER, array(
			'Authorization: token ' . $token,
			'Accept: application/json',
			'Content-Type: application/json',
			'User-Agent: StackIdeas Forums',
			'Content-Length: ' . strlen($paramsStr)
			));
		$output = curl_exec($ch);

		curl_close($ch);

		if (!$output) {
			return false;
		}

		$result = json_decode($output);

		if (!$result || !isset($result->html_url)) {
			return false;
		}

		return $result->html_url;
	}

	private function getAccessToken()
	{
		static $token = null;

		if (is_null($token)) {

			$token = false;

			$oauth = ED::table('Oauth');
			$oauth->load(array('type' => 'github'));

			if ($oauth->id && $oauth->access_token) {
				$data = json_decode($oauth->access_token);
				$token = $data->token;
			}
		}

		return $token;
	}
}
