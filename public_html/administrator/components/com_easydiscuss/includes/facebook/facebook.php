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

class EasyDiscussFacebook extends EasyDiscuss
{
	/**
	 * Adds the opengraph tags on the page for the post
	 *
	 * @since	4.0.7
	 * @access	public
	 * @param	string
	 * @return	
	 */
	public function addOpenGraph(EasyDiscussPost $post)
	{
		// Use facebook developer tools to check the result
		// https://developers.facebook.com/tools/debug/og
		$doc = JFactory::getDocument();
		$config = ED::config();

		// Search for an image in the content
		$image = self::searchImage($post->content);

		if ($image) {
			$doc->addCustomTag('<meta property="og:image" content="' . $image . '" />');
		}

		if ($config->get('integration_facebook_like')) {
			
			$appId = $config->get('integration_facebook_like_appid');

			if ($appId) {
				$doc->addCustomTag('<meta property="fb:app_id" content="' . $appId . '" />');
			}

			$adminId = $config->get('integration_facebook_like_admin');

			if ($adminId) {
				$doc->addCustomTag('<meta property="fb:admins" content="' . $adminId . '" />');
			}
		}

		// Add post title info here.
		$doc->addCustomTag('<meta property="og:title" content="' . $post->title . '" />');

		// Add post content.
		$maxContent = 350;

		// Remove bbcode tags from the content.
		$description = $post->content;

		$description = preg_replace('/\s+/', ' ', (strip_tags(ED::parser()->bbcode($description))));

        // strip this kind of tag -> &nbsp; &amp;
		$description = strip_tags(html_entity_decode($description));

		// We need to escape quotes this now
		$description = ED::string()->escape($description);
		
		if (JString::strlen($description) > $maxContent) {
			$description = JString::substr($description, 0, $maxContent) . '...';
		}

		// Add content to description
		$doc->addCustomTag('<meta property="og:description" content="' . $description . '" />');

		// Add the type of the og tag.
		$doc->addCustomTag('<meta property="og:type" content="article" />');

		// Add the URL for this page.;
		$url = EDR::getRoutedURL('view=post&id=' . $post->id, false, true);

		$doc->addCustomTag('<meta property="og:url" content="' . $url . '" />');

		$doc->setTitle($post->title);
		$doc->setDescription($description);

		return true;
	}

	/**
	 * Searches for an image tag in a given content.
	 *
	 * @since	3.0
	 * @access	public
	 */
	public static function searchImage($content)
	{
		// Search for first image in a content
		$image = '';
		$pattern = '#<img[^>]*>#i';

		preg_match($pattern, $content, $matches);

		// If there's a match, get hold of the image as we need to run some processing.
		if ($matches && isset($matches[0])) {
			$image = $matches[0];

			// Try to just get the image url.
			$pattern = '/src=[\"\']?([^\"\']?.*(png|jpg|jpeg|gif))[\"\']?/i';

			preg_match($pattern, $image, $matches);

			if ($matches && isset($matches[1])) {
				$image = $matches[ 1 ];
				$image = ED::image()->rel2abs($image, DISCUSS_JURIROOT);
			}
		}

		if (!$image) {
			return false;
		}

		return $image;
	}
}
