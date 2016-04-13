<?php
/**
* @package		EasyDiscuss
* @copyright	Copyright (C) 2010 - 2015 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasyBlog is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');

class EasyDiscussPostAccess extends EasyDiscuss
{
	public function __construct($opts = array())
	{
		parent::__construct();

		$post = $opts[0];
		$category = $opts[1];

		// Creator of the post
		$this->creator = ED::user($post->user_id);

		$this->post = $post;
		$this->parent = null;

		// we now this item is a reply. let get the parent;
		if (!empty($this->post->parent_id)) {
			$parentPost = ED::table('Post');
			$parentPost->load($this->post->parent_id);

			$this->parent = $parentPost;
		}

		$this->category = $category;
		$this->isModerator = ED::moderator()->isModerator($this->post->category_id);
	}

	public function isMine()
	{
		return DiscussHelper::isMine( $this->post->user_id );
	}

	public function isModerate()
	{
		return $this->post->isPending();
	}

	public function isModerator()
	{
		return $this->isModerator;
	}

	public function isLocked()
	{
		return $this->post->islock;
	}

	/* post deletion */
	public function canDelete()
	{
		if( $this->config->get( 'main_allowdelete' ) == '0')
		{
			return false;
		}

		// Super admin always allowed to delete.
		if( $this->isSiteAdmin || $this->isModerator )
		{
			return true;
		}

		if (!empty($this->post->parent_id)) {
			return $this->canDeleteReply();
		}

		if( $this->acl->allowed( 'delete_question' ) || ( $this->acl->allowed( 'delete_own_question' ) && self::isMine() ) )
		{
			return true;
		}

		return false;
	}

	/**
	 * Determines if the user can move the post to a different category.
	 *
	 * @since	3.0
	 * @access	public
	 */
	public function canMove()
	{
		// Replies can not be moved.
		if( $this->post->parent_id )
		{
			return false;
		}

		// Admin and moderators are always allowed.
		if( $this->isSiteAdmin || $this->isModerator )
		{
			return true;
		}

		return false;
	}

	/**
	 * Determines if the user can branch out a reply.
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function canBranch()
	{

		// Questions can not be branched out because it's already a branch by itself.
		if( !$this->post->parent_id )
		{
			return false;
		}

		// Admin and moderators are always allowed.
		if( $this->isSiteAdmin || $this->isModerator )
		{
			return true;
		}

		if( $this->acl->allowed( 'edit_branch' ) )
		{
			return true;
		}

		return false;
	}

	public function canDeleteReply()
	{
		if ($this->config->get( 'main_allowdelete' ) == '0') {
			return false;
		}

		// Super admin always allowed to delete.
		if ($this->isSiteAdmin || $this->isModerator ) {
			return true;
		}

		if ($this->acl->allowed('delete_reply')) {
			return true;
		}

		if (self::isMine() && $this->acl->allowed('delete_own_replies')) {
			return true;
		}

		return false;
	}

	public function canEdit()
	{
		if ($this->isSiteAdmin || $this->isModerator) {
			return true;
		}

		if (self::isModerate()) {
			return false;
		}

		$isQuestion = empty($this->post->parent_id);
		$isReply = !$isQuestion;

		if ($isQuestion && $this->acl->allowed('edit_question')) {
			return true;
		}

		//TODO: Add edit_own_question rule in the future
		if ($isQuestion && self::isMine() && $this->acl->allowed('edit_own_question')) {
		return true;
		}

		if ($isReply && $this->acl->allowed('edit_reply')) {
			return true;
		}

		if ($isReply && self::isMine() && $this->acl->allowed('edit_own_reply')) {
			return true;
		}

		return false;
	}

	public function canResolve()
	{
		if( !$this->config->get( 'main_qna' ))
		{
			return false;
		}

		if( $this->isSiteAdmin || $this->isModerator || $this->isMine() )
		{
			return true;
		}

		return false;
	}

	public function canFeature()
	{
		if( $this->acl->allowed( 'feature_post' ) || $this->isSiteAdmin || $this->isModerator )
		{
			return true;
		}

		return false;
	}

	public function canLock()
	{
		if( $this->acl->allowed( 'lock_discussion' ) || $this->isSiteAdmin || $this->isModerator )
		{
			return true;
		}

		return false;
	}

	public function canUnlock()
	{
		if( $this->acl->allowed( 'lock_discussion' ) || $this->isSiteAdmin || $this->isModerator )
		{
			return true;
		}

		return false;
	}

	public function canVote()
	{
		// Is a guest
		if( empty($this->my->id) )
		{
			if ( $this->config->get( 'main_allowguest_vote_question' ) && !self::isModerate() && $this->post->isQuestion() )
			{
				return true;
			}

			if ( $this->config->get( 'main_allowguest_vote_reply' ) && !self::isModerate() && $this->post->isReply() )
			{
				return true;
			}

			return false;
		}

		if ( $this->config->get( 'main_allowquestionvote' ) && !self::isModerate() && $this->post->isQuestion() )
		{
			return true;
		}

		if ( $this->config->get( 'main_allowvote' ) && !self::isModerate() && $this->post->isReply() )
		{
			return true;
		}

		return false;
	}

	public function canLike()
	{
		if( $this->my->id > 0 && !self::isModerate() && !$this->post->islock && $this->config->get( 'main_likes_discussions' ) )
		{
			return true;
		}

		return false;
	}

	public function canReport()
	{
		if( $this->my->id > 0 && $this->config->get( 'main_report' ) && !self::isMine() )
		{
			return true;
		}

		return false;
	}

	public function canSubscribe()
	{
		if( !self::isMine() && !self::isModerate() && $this->config->get( 'main_postsubscription' ) )
		{
			return true;
		}
		return false;
	}

	public function canReply()
	{
		if( $this->isSiteAdmin || $this->isModerator )
		{
			return true;
		}

		// now we need to check against the associated category acl
		if ($this->acl->allowed('add_reply')) {

			if (!$this->category->canReply()) {
				return false;
			}

			return true;
		}

		return false;
	}

	public function canAssign()
	{
		if ($this->config->get('main_assign_user') && ($this->isSiteAdmin || $this->isModerator)) {
			return true;
		}

		return false;
	}

	public function canLabel()
	{
		if( $this->config->get('main_discussion_labels') && ($this->isSiteAdmin || $this->isModerator) )
		{
			return true;
		}

		return false;
	}

	public function canMarkAnswered()
	{
		if( ! $this->config->get('main_qna') )
			return false;

		if( $this->isSiteAdmin || $this->isModerator )
		{
			return true;
		}

		if(! is_null( $this->parent ) )
		{
			//check if the parent post is belong to current user or not.
			if( DiscussHelper::isMine( $this->parent->user_id ) )
				return true;
		}

		if( $this->acl->allowed('mark_answered') )
		{
			return true;
		}

		return false;
	}

	public function canUnmarkAnswered()
	{
		if( ! $this->config->get('main_qna') )
			return false;

		if( ! $this->post->answered )
			return false;

		if( $this->isSiteAdmin || $this->isModerator )
		{
			return true;
		}

		if(! is_null( $this->parent ) )
		{
			//check if the parent post is belong to current user or not.
			if( DiscussHelper::isMine( $this->parent->user_id ) )
				return true;
		}

		if( $this->acl->allowed('mark_answered') )
		{
			return true;
		}

		return false;
	}

	public function canComment()
	{
		if (!is_null($this->parent)) {

			// this is a reply
			if (!$this->config->get('main_comment')) {
				return false;
			}

		} else {
			
			if (!$this->config->get('main_commentpost')) {
				return false;
			}
		}

		//Fixed: If moderator is a registered group, and registered group is not allow to add comment
		if ($this->acl->allowed('add_comment') && ($this->category->canReply() || $this->isSiteAdmin) || $this->isModerator) {
			return true;
		}

		return false;
	}

	public function canLockPolls()
	{
		if ( $this->config->get( 'main_polls_lock' ) && !self::isModerate() && ( $this->isSiteAdmin || $this->isModerator || self::isMine() ) )
		{
			return true;
		}

		return false;
	}

	public function canOnHold()
	{
		if( $this->isSiteAdmin || $this->isModerator )
		{
			return true;
		}

		if( $this->acl->allowed( 'mark_on_hold' ) )
		{
			return true;
		}

		return false;
	}

	public function canAccepted()
	{
		if( $this->isSiteAdmin || $this->isModerator )
		{
			return true;
		}

		if( $this->acl->allowed( 'mark_accepted' ) )
		{
			return true;
		}

		return false;
	}

	public function canWorkingOn()
	{
		if( $this->isSiteAdmin || $this->isModerator )
		{
			return true;
		}

		if( $this->acl->allowed( 'mark_working_on' ) )
		{
			return true;
		}

		return false;
	}

	public function canReject()
	{
		if( $this->isSiteAdmin || $this->isModerator )
		{
			return true;
		}

		if( $this->acl->allowed( 'mark_reject' ) )
		{
			return true;
		}

		return false;
	}

	public function canNoStatus()
	{
		if( $this->isSiteAdmin || $this->isModerator )
		{
			return true;
		}

		if( $this->acl->allowed( 'mark_no_status' ) )
		{
			return true;
		}

		return false;
	}

	public function canBan()
	{
		if (!$this->my->id) {
			return false;
		}

		//Check if the current user is it match with post created by id/setting
		if (($this->config->get('main_ban') && $this->acl->allowed('ban_user') && !self::isMine()) || $this->isSiteAdmin || $this->isModerator) {
			return true;
		}

		return false;
	}

}
