<?php
/**
* @package		EasyDiscuss
* @copyright	Copyright (C) 2010 - 2017 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasyBlog is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');

class EasyDiscussVote extends EasyDiscuss
{
	/**
	 * Generate the voting user interface HTML
	 *
	 * @access	public
	 * @param	object	$post		The post object.
	 **/
	public static function getHTML( &$post, $params = array() )
	{
		$isLock		= false;

		if (isset($params['parent_id']))
		{
			$isLock	= self::isLock($params['parent_id']);
		}

		if (isset($params['isMainLocked']))
		{
			$isLock	= $params['isMainLocked'];
		}

		$my		= isset($params['my']) ? $params['my'] : JFactory::getUser();
		$config	= isset($params['config']) ? $params['config'] : DiscussHelper::getConfig();
		$tmpl	= isset($params['tmpl']) ? $params['tmpl'] : 'vote.php';

		$canVote	= (!$config->get( 'main_allowselfvote') && ($my->id == $post->user_id)) ? false : true;

		$template	= new DiscussThemes();
		$template->set( 'post'		, $post );
		$template->set( 'isLock'	, $isLock );
		$template->set( 'canVote'	, $canVote );

		$html		= $template->fetch( $tmpl );

		return $html;
	}

	private static function isLock( $post_id )
	{
		$db		= DiscussHelper::getDBO();
		$query	= 'SELECT `islock` FROM `#__discuss_posts` WHERE `id` = ' . $db->quote( $post_id );
		$db->setQuery($query);

		return $db->loadResult();
	}

	public function undoVotes($post, $sessionId)
	{
		$model = ED::model('Votes');
		$status = true;

		// Determine whether he has already perform undo process.
		$undo = $model->voteModifying($post->id, $this->my->id, $sessionId);

		if (!$undo) {
			return false;
		}

		$state = $model->undoVote($post, $this->my->id, $sessionId);

		return $state;
	}

	public function getTotalVotes($postId)
	{
		$model = ED::model('Votes');
		$votes = $model->getTotalVotes($postId);

		return $votes;
	}
}












