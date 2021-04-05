<?php
/**
* @package		EasyDiscuss
* @copyright	Copyright (C) Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasyDiscuss is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');

class EasyDiscussTelegram extends EasyDiscuss
{
	private $token = null;

	public function __construct()
	{
		parent::__construct();

		// Get the bot's token
		$this->token = $this->config->get('integrations_telegram_token');
	}

	/**
	 * Discovers for new chats
	 *
	 * @since   4.0
	 * @access  public
	 */
	public function discover()
	{
		$result = $this->ping('getUpdates');

		if (!$result) {
			return false;
		}

		// We keep items that has already been assigned before here
		$unique = array();
		$messages = array();

		// Format the result
		foreach ($result as &$item) {

			// Check for private chat.
			if (!isset($item->message)) {
				// Check for channel chat.
				if (!isset($item->my_chat_member)) {
					continue;
				}
			}

			$chat = isset($item->message) ? $item->message->chat : $item->my_chat_member->chat;

			// Don't keep appending the same chats into the array.
			if (in_array($chat->id, $unique)) {
				continue;
			}

			// Standardize all items to have a "title" attribute
			if (!isset($chat->title)) {
				$chat->title = $chat->first_name . ' ' . $chat->last_name;
			}

			$unique[] = $chat->id;
			$messages[] = $chat;
		}

		return $messages;
	}

	/**
	 * Executes a http request to telegram's api
	 *
	 * @since   4.0
	 * @access  public
	 */
	public function ping($method, $data = array())
	{
		$url = 'https://api.telegram.org/bot' . $this->token . '/' . $method;

		if ($data) {
			$url .= '?';

			foreach ($data as $key => $value) {
				$url .= $key . '=' . $value;

				if (next($data) !== false) {
					$url .= '&';
				}
			}
		}

		// Initiate the curl request
		$ch = curl_init();
		
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

		$response = curl_exec($ch);
		curl_close($ch);

		// Try to decode the result
		$response = json_decode($response);

		if (!$response || !isset($response->ok) || !$response->ok) {
			return false;
		}

		return $response->result;
	}

	/**
	 * Sends a message to a chat id
	 *
	 * @since   4.0
	 * @access  public 
	 */
	public function sendMessage($message)
	{
		$data = array();
		$data['chat_id'] = $this->config->get('integrations_telegram_chat_id');

		// @TODO: Replace with the proper message here

		$data['text'] = urlencode($message);
		$data['parse_mode'] = 'markdown';

		$this->ping('sendMessage', $data);
	}

	/**
	 * Allows caller to send data via telegram's bot
	 *
	 * @since   4.0
	 * @access  public
	 */
	public function share(EasyDiscussPost $post)
	{
		if (!$this->config->get('integrations_telegram')) {
			return false;
		}

		if (!$post->isQuestion() && $this->config->get('integrations_telegram_only_post_notify')) {
			return false;
		}

		$postTitle = $post->title;
		$permalink = $post->getPermalink(true);
		$message = $this->config->get('integrations_telegram_message');

		if ($post->isReply()) {
			$message = $this->config->get('integrations_telegram_reply_message');

			// retrieve the post title
			$question = ED::post($post->parent_id);
			$postTitle = $question->title;
		}

		$message = str_ireplace('{permalink}', $permalink, $message);
		$message = str_ireplace('{snippet}', $post->getIntro(), $message);
		$message = str_ireplace('{title}', $postTitle, $message);
		
		// We need to escape any underscore in the message as this will break the markdown
		$message = str_replace("_", "\_", $message);
		
		return $this->sendMessage($message);
	}
}
