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

class EasyDiscussThemesHelperCard extends EasyDiscussHelperAbstract
{
	/**
	 * Generates the active category card layout
	 *
	 * @since	5.0.0
	 * @access	public
	 */
	public function activeCategory($category)
	{
		$description = $category->getDescription();

		// Determine whether need to show the dot
		$renderSubscription = true;
		$my = JFactory::getUser();

		if (!$this->config->get('main_ed_categorysubscription') || (!$my->id && !$this->config->get('main_allowguestsubscribe'))) {
			$renderSubscription = false;
		}

		$theme = ED::themes();
		$theme->set('description', $description);
		$theme->set('category', $category);
		$theme->set('renderSubscription', $renderSubscription);

		$output = $theme->output('site/helpers/card/active.category');

		return $output;
	}

	/**
	 * Generates the active category card layout
	 *
	 * @since	5.0.0
	 * @access	public
	 */
	public function activeTag(EasyDiscussTag $tag)
	{
		$theme = ED::themes();
		$theme->set('tag', $tag);

		$output = $theme->output('site/helpers/card/active.tag');

		return $output;
	}

	/**
	 * Generates a card layout for a badge
	 *
	 * @since	5.0.0
	 * @access	public
	 */
	public function badge(DiscussBadges $badge)
	{
		$my = JFactory::getUser();
		$achieved = $badge->achieved($my->id);

		$theme = ED::themes();
		$theme->set('badge', $badge);
		$theme->set('achieved', $achieved);
		
		$output = $theme->output('site/helpers/card/badge');

		return $output;
	}

	/**
	 * Generates a card layout for a category in a list
	 *
	 * @since	5.0.0
	 * @access	public
	 */
	public function category($category)
	{
		$description = $category->getDescription();

		$theme = ED::themes();
		$theme->set('description', $description);
		$theme->set('category', $category);
		
		$output = $theme->output('site/helpers/card/category');

		return $output;
	}

	/**
	 * Renders an empty card 
	 *
	 * @since	5.0.0
	 * @access	public
	 */
	public function emptyCard($icon, $text, $fixedHeight = true)
	{
		$theme = ED::themes();
		$theme->set('fixedHeight', $fixedHeight);
		$theme->set('text', $text);
		$theme->set('icon', $icon);

		$output = $theme->output('site/helpers/card/empty');

		return $output;
	}

	/**
	 * Renders the card post layout
	 *
	 * @since	5.0.0
	 * @access	public
	 */
	public function post($post, $options = array())
	{
		$isSearch = ED::normalize($options, 'isSearch', false);
		
		$wrapperClass = 'o-card--ed-post-item';

		$theme = ED::themes();
		$theme->set('wrapperClass', $wrapperClass);
		$theme->set('post', $post);
		$theme->set('isSearch', $isSearch);
		$output = $theme->output('site/helpers/card/post');

		return $output;
	}

	/**
	 * Renders a user card
	 *
	 * @since	5.0.0
	 * @access	public
	 */
	public function user($user)
	{
		$badges = $user->getBadges();

		$theme = ED::themes();
		$theme->set('user', $user);
		$theme->set('badges', $badges);

		$output = $theme->output('site/helpers/card/user');

		return $output;
	}

	/**
	 * Renders the card post layout
	 *
	 * @since	5.0.0
	 * @access	public
	 */
	public function forumPost($post)
	{
		$wrapperClass = 'o-card--ed-forum-post';

		$theme = ED::themes();
		$theme->set('wrapperClass', $wrapperClass);
		$theme->set('post', $post);
		$theme->set('isSearch', false);
		
		$output = $theme->output('site/helpers/card/post');

		return $output;
	}

	/**
	 * Renders the card tag layout
	 *
	 * @since	5.0.0
	 * @access	public
	 */
	public function tag($tag)
	{
		$theme = ED::themes();
		$theme->set('tag', $tag);
		$output = $theme->output('site/helpers/card/tag');

		return $output;
	}
}
