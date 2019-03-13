<?php
/**
* @package		EasyDiscuss
* @copyright	Copyright (C) 2010 - 2018 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasyDiscuss is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');

class EasyDiscussTwitter extends EasyDiscuss
{
	/**
	 * Renders the Twitter card on the head of the page
	 *
	 * @since	4.1.3
	 * @access	public
	 */
	public function addCard(EasyDiscussPost $post)
	{
		if (!$this->config->get('integration_twitter_card')) {
			return;
		}

		$data = $post->getEmbedData();
		$type = 'summary';

		$this->doc->addCustomTag('<meta name="twitter:title" content="' . $post->getTitle() . '" />');
		$this->doc->addCustomTag('<meta name="twitter:url" content="' . $data->url . '" />');
		$this->doc->addCustomTag('<meta property="twitter:description" content="' . $data->description . '" />');

		$images = $data->images;

		if ($images) {
			$type = 'summary_large_image';

			foreach ($images as $image) {
				$this->doc->addCustomTag('<meta name="twitter:image" content="' . $image . '"/>');		
			}
		}

		$this->doc->addCustomTag('<meta name="twitter:card" content="' . $type . '" />');
	}
}
