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

jimport('joomla.utilities.date');

class EasyDiscussFavourite extends EasyDiscuss
{
	/**
	 * Unfavourite a post
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function unfavourite(EasyDiscussPost $post)
	{
		// If the user previously did not favourite the post, they shouldn't be able to ununfavourite the post
		$favourite = $post->isFavBy($this->my->id);

		if (!$favourite) {
			return false;
		}

		// If the post is not published, we shouldn't allow them to favourite
		if (!$post->isPublished()) {
			return false;
		}

		// Remove the favourite
		$this->removeFav($post->id, 'post', $this->my->id);
		$post->updateThread(array('num_fav' => '-1'));

		if ($post->user_id != $this->my->id) {
			if ($post->isQuestion()) {
				// Remove unfavourite
				ED::history()->removeLog('easydiscuss.favourite.discussion', $this->my->id, $post->id);

				ED::badges()->assign('easydiscuss.unfavourite.discussion', $this->my->id);
				ED::points()->assign('easydiscuss.unfavourite.discussion', $this->my->id);
			}
		}

		// Get the favourite's text.
		$text = $this->html($post->id, $this->my->id, 'post');

		if (!$text) {
			$text = JText::_('COM_EASYDISCUSS_BE_THE_FIRST_TO_FAVOURITE');
		}

		return $text;
	}

	/**
	 * Favourite a post
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function favourite(EasyDiscussPost $post)
	{
		// If the user is already favourite the item, they shouldn't be able to like this again.
		$favourite = $post->isFavBy($this->my->id);

		if ($favourite) {
			return false;
		}

		// If the post is not published, we shouldn't allow them to favourite
		if (!$post->isPublished()) {
			return false;
		}

		// Add the favourite
		$this->addFav($post->id, 'post', $this->my->id);
		$post->updateThread(array('num_fav' => '+1'));

		// Add activity in jomsocial and easysocial
		if ($post->user_id != $this->my->id) {

			// Add integrations when a post is liked for questions
			if ($post->isQuestion()) {
				// Add logging for user.
				ED::history()->log('easydiscuss.favourite.discussion', $this->my->id, JText::sprintf('COM_EASYDISCUSS_BADGES_HISTORY_FAVOURITE_DISCUSSION', $post->title), $post->id);

				ED::badges()->assign( 'easydiscuss.favourite.discussion' , $this->my->id);
				ED::points()->assign( 'easydiscuss.favourite.discussion' , $this->my->id);

				// Assign badge for EasySocial
				ED::easysocial()->assignBadge('favourite.question', $this->my->id, JText::sprintf('COM_EASYDISCUSS_BADGES_HISTORY_FAVOURITE_DISCUSSION', $post->title));
			}
		}

		// Add stream for EasySocial
		ED::easysocial()->favouriteStream($post);

		// Get the like's text.
		$text = $this->html($post->id, $this->my->id, 'post');

		if (!$text) {
			$text = JText::_('COM_EASYDISCUSS_BE_THE_FIRST_TO_FAVOURITE');
		}

		return $text;
	}

	/**
	 * Generates the like button
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function button(EasyDiscussPost $post)
	{
		$button = new stdClass();

		// If the post cannot be favourite, we should not displaying anything
		if (!$post->canFav()) {
			return;
		}

		// Load fav lib
		$button->authors = $this->html($post->id, $this->my->id, 'post');

		// Get the total like from the post
		$model = ED::model('Favourites');
		$button->total = $model->getFavouritesCount($post->id);

		// If there are no authors, we need to set a default value
		if (!$button->authors) {
			$button->authors = JText::_('COM_EASYDISCUSS_BE_THE_FIRST_TO_FAVOURITE');
		}

		// By default, we treat all users as never fav the post before
		$fav = false;

		// Determine if the user liked this post before
		if ($this->my->id) {
			$fav = $post->isFavBy($this->my->id);
		}

		if ($this->my->id && !$button->authors) {
			$button->message = 'COM_EASYDISCUSS_MARK_AS_FAVOURITE';
			$button->label = 'COM_EASYDISCUSS_FAVOURITE';

			if ($fav) {
				$button->message = 'COM_EASYDISCUSS_REMOVE_AS_FAVOURITE';
				$button->label = 'COM_EASYDISCUSS_UNFAVOURITE';
			}
		}

		$theme = ED::themes();
		$theme->set('button', $button);
		$theme->set('fav', $fav);
		$theme->set('post', $post);

		$output = $theme->output('site/favourites/button');

		return $output;
	}

	/**
	 * Favourite a post
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public static function addFav($contentId, $type, $userId = null)
	{
		if (is_null($userId)) {
			$userId	= JFactory::getUser()->id;
		}

		$date = ED::date();
		$favourite = ED::table('Favourites');

		$params	= array();
		$params['type']	= $type;
		$params['post_id'] = $contentId;
		$params['created_by'] = $userId;
		$params['created'] = $date->toSql();

		$favourite->bind($params);

		// Check if the user already favourite or not. if yes, then return the id.
		$id	= $favourite->favExists();

		if ($id !== false) {
			return $id;
		}

		$favourite->store();

		// We need to update the fav count in post table
		if ($type == 'post') {
			$model = ED::model('favourites');
			$model->updatePostFav($contentId, true);
		}

		return $favourite->id;
	}

	/**
	 * Remove favourite from a post
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public static function removeFav($contentId, $type = DISCUSS_ENTITY_TYPE_POST, $userId = null)
	{
		if (is_null($userId)) {
			$userId	= JFactory::getUser()->id;
		}

		$favourite = ED::table('Favourites');
		$favourite->loadByPost($contentId, $userId);

		// We need to update the fav count in post table
		if ($favourite->type == 'post') {
			$model = ED::model('favourites');
			$model->updatePostFav($contentId, false);
		}

		return $favourite->delete();
	}

	/**
	 * Retrieve favourite from a post
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public static function getFavourite($contentId, $userId = null, $type = DISCUSS_ENTITY_TYPE_POST, $preloadedObj = null)
	{
		static $loaded = array();

		if (is_null($userId)) {
			$userId = ED::user()->id;
		}

		if (is_null($preloadedObj)) {
			$model = ED::model('Favourites');
			$lists = $model->getPostFavourites($contentId, $type);
		} else {
			$lists = $preloadedObj;
		}

		if (count($lists) <= 0) {
			return '';
		}

		$user = array();
		foreach ($lists as $key => $list) {
			$user[] = ED::user($list->user_id);
		}

		return $user;
	}

	public static function html($contentId, $userId = null, $type = DISCUSS_ENTITY_TYPE_POST, $preloadedObj = null)
	{
		static $loaded = array();

		if (is_null($userId)) {
			$userId = JFactory::getUser()->id;
		}

		if (is_null($preloadedObj)) {
			$model = ED::model('Favourites');
			$list = $model->getPostFavourites($contentId, $type);
		} else {
			$list = $preloadedObj;
		}

		if (count($list) <= 0) {
			return '';
		}

		$names = array();

		for ($i = 0; $i < count($list); $i++) {

			if ($list[$i]->user_id == $userId) {
				array_unshift($names, JText::_('COM_EASYDISCUSS_YOU'));
			} else {
				$user = ED::user($list[$i]->user_id);
				$names[] = '<a href="' . $user->getLink() . '">' . $list[$i]->displayname . '</a>';
			}
		}

		// Maximum names to be display
		$max = 3;
		$total = count($names);
		$break = 0;

		if ($total == 1) {
			$break = $total;

		} else {

			if ($max >= $total) {
				$break = $total - 1;

			} else if($max < $total) {
				$break = $max;
			}
		}

		$main = array_slice($names, 0, $break);
		$remain	= array_slice($names, $break);

		$stringFront = implode(", ", $main);
		$returnString = '';

		if (count($remain) > 1) {
			$returnString = JText::sprintf('COM_EASYDISCUSS_AND_OTHERS_FAVOURITE_THIS', $stringFront, count($remain));

		} else if(count($remain) == 1) {
			$returnString = JText::sprintf('COM_EASYDISCUSS_AND_FAVOURITE_THIS', $stringFront, $remain[0]);

		} else {

			if ($list[0]->user_id == $userId) {
				$returnString = JText::sprintf('COM_EASYDISCUSS_FAVOURITE_THIS', $stringFront);
			} else {
				$returnString = JText::sprintf('COM_EASYDISCUSS_FAVOURITES_THIS', $stringFront);
			}
		}

		return $returnString;
	}
}
