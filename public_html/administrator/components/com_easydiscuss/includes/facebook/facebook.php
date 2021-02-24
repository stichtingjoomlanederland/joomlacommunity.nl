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
		if (!$this->config->get('integration_facebook_opengraph')) {
			return false;
		}

		$data = $post->getEmbedData();

		// Add the title tag
		$this->doc->setMetaData('og:title', $post->getTitle(), 'property');
		$this->doc->setMetaData('og:description', $data->description, 'property');
		$this->doc->setMetaData('og:type', 'article', 'property');
		$this->doc->setMetaData('og:url', $data->url, 'property');

		// Facebook likes data
		if ($this->config->get('integration_facebook_like')) {
			
			$appId = $this->config->get('integration_facebook_like_appid');
			
			if ($appId) {
				$this->doc->setMetaData('fb:app_id', $appId, 'property');
			}

			$adminId = $this->config->get('integration_facebook_like_admin');

			if ($adminId) {
				$this->doc->setMetaData('fb:admins', $adminId, 'property');
			}
		}

		// If we still can't find any images, load up the placeholder
		$images = $data->images;

		if (!$images) {
			$images[] = ED::getPlaceholderImage();
		}

		if ($images) {
			foreach ($images as $image) {
				$this->doc->setMetaData('og:image', $image, 'property');
			}
		}

		return true;
	}
}
