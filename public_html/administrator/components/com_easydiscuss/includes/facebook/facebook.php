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

class EasyDiscussFacebook extends EasyDiscuss
{
	/**
	 * Adds the opengraph tags on the page for the post
	 *
	 * @since	4.0.7
	 * @access	public
	 */
	public function addOpenGraph(EasyDiscussPost $post)
	{
		// Post title
		$this->doc->addCustomTag('<meta property="og:title" content="' . $post->getTitle() . '" />');

		// Facebook likes data
		if ($this->config->get('integration_facebook_like')) {
			$appId = $this->config->get('integration_facebook_like_appid');
			if ($appId) {
				$this->doc->addCustomTag('<meta property="fb:app_id" content="' . $appId . '" />');
			}

			$adminId = $this->config->get('integration_facebook_like_admin');
			if ($adminId) {
				$this->doc->addCustomTag('<meta property="fb:admins" content="' . $adminId . '" />');
			}
		}

		$data = $post->getEmbedData();

		$this->doc->addCustomTag('<meta property="og:description" content="' . $data->description . '" />');
		$this->doc->addCustomTag('<meta property="og:type" content="article" />');
		$this->doc->addCustomTag('<meta property="og:url" content="' . $data->url . '" />');

		$postTitle = $post->getTitle();
		$pageTitle = htmlspecialchars_decode($postTitle, ENT_QUOTES);

		$this->doc->setTitle($pageTitle);
		$this->doc->setDescription($data->description);

		// If we still can't find any images, load up the placeholder
		$images = $data->images;

		if (!$images) {
			$images[] = ED::getPlaceholderImage();
		}

		if ($images) {
			foreach ($images as $image) {
				$this->doc->addCustomTag('<meta property="og:image" content="' . $image . '" />');
			}
		}

		return true;
	}
}
