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

	/**
	 * Perform undo vote
	 *
	 * @since	3.0
	 * @access	public
	 **/
	public function undoVotes($post, $sessionId)
	{
		$model = ED::model('Votes');

		// Determine whether he has already perform undo process.
		$undo = $model->voteModifying($post->id, $this->my->id, $sessionId);

		if (!$undo) {
			return false;
		}

		// when the system reach here mean this user trying to unvote his previous vote
		$state = $model->undoVote($post, $this->my->id, $sessionId);

		return $state;
	}

	/**
	 * Retrieve the total of votes
	 *
	 * @since	3.0
	 * @access	public
	 **/
	public function getTotalVotes($postId)
	{
		$model = ED::model('Votes');
		$votes = $model->getTotalVotes($postId);

		return $votes;
	}

	/**
	 * Determine voting behaviour
	 *
	 * @since	4.1.3
	 * @access	public
	 **/
	public function isVotingContribution()
	{
		$votingBehaviour = $this->config->get('main_voting_behavior_type', 'default');
		$result = false;

		if ($votingBehaviour == 'contribution') {
			$result = true;
		}

		return $result;
	}

	/**
	 * Determine voting behaviour
	 *
	 * @since	4.1.3
	 * @access	public
	 **/
	public function convertUpVoteRules($command)
	{
		$rules = array(
						'easydiscuss.unvote.reply' => 'easydiscuss.vote.reply',
					    'easydiscuss.unvote.answer' => 'easydiscuss.vote.answer', 
					    'easydiscuss.unvote.question' => 'easydiscuss.vote.question'
					);

		// if doesn't match then return false
		if (!isset($rules[$command])) {
			return false;
		}

		$result = $rules[$command];

		return $result;
	}
}
