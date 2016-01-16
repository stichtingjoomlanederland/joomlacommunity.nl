<?php
/**
 * @package		EasyDiscuss
 * @copyright	Copyright (C) 2010 Stack Ideas Private Limited. All rights reserved.
 * @license		GNU/GPL, see LICENSE.php
 *
 * EasyDiscuss is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * See COPYRIGHT.php for copyright notices and details.
 */

defined('_JEXEC') or die('Restricted access');

class DiscussAupHelper
{
	var $exists	= null;
	var $rules	= array(
					DISCUSS_POINTS_NEW_DISCUSSION		=> 'new_discussion',
					DISCUSS_POINTS_DELETE_DISCUSSION	=> 'delete_discussion',
					DISCUSS_POINTS_VIEW_DISCUSSION		=> 'view_discussion',
					DISCUSS_POINTS_NEW_AVATAR			=> 'new_avatar',
					DISCUSS_POINTS_UPDATE_AVATAR		=> 'update_avatar',
					DISCUSS_POINTS_NEW_REPLY			=> 'new_reply',
					DISCUSS_POINTS_DELETE_REPLY			=> 'delete_reply',
					DISCUSS_POINTS_NEW_COMMENT			=> 'new_comment',
					DISCUSS_POINTS_DELETE_COMMENT		=> 'delete_comment',
					DISCUSS_POINTS_ACCEPT_REPLY			=> 'accept_reply',
					DISCUSS_POINTS_ANSWER_VOTE_UP		=> 'answer_vote_up',
					DISCUSS_POINTS_ANSWER_VOTE_DOWN		=> 'answer_vote_down',
					DISCUSS_POINTS_QUESTION_VOTE_UP		=> 'question_vote_up',
					DISCUSS_POINTS_QUESTION_VOTE_DOWN	=> 'question_vote_down'

				);

	public function __construct()
	{
		$this->exists	= $this->exists();
	}

	public function exists()
	{
		jimport('joomla.filesystem.file');

		$config	= DiscussHelper::getConfig();
		$path	= JPATH_ROOT . '/components/com_alphauserpoints/helper.php';

		if( !$config->get('integration_aup') )
		{
			return false;
		}

		if( JFile::exists( $path ) )
		{
			require_once( $path );
			return true;
		}
		return false;
	}

	public function assign( $rule , $userId , $title )
	{
		if( !$this->exists || !isset( $this->rules[ $rule ] ) )
		{
			return false;
		}

		JFactory::getLanguage()->load( 'com_easydiscuss' , JPATH_ROOT );

		// TODO: Fixed strict standard issue.
		$aup = new AlphaUserPointsHelper;
		$id	= $aup->getAnyUserReferreID( $userId );
		//$id	= AlphaUserPointsHelper::getAnyUserReferreID( $userId );

		$rule	= $this->rules[ $rule ];
		$aup->newpoints( 'plgaup_easydiscuss_' . strtolower( $rule ) , $id , '' , JText::sprintf( 'COM_EASYDISCUSS_AUP_' . strtoupper( $rule ) , $title ) );
	}

	public function getUserPoints( $userId )
	{
		static $points;


		if (!isset($points))
		{
			$points = array();
		}

		if (empty($points[$userId]))
		{
			$db		= DiscussHelper::getDBO();
			$query	= 'SELECT `points` FROM `#__alpha_userpoints` WHERE `userid` = ' . $db->quote($userId);
			$db->setQuery($query);
			$points[$userId]	= $db->loadResult();
		}

		return $points[$userId];
	}
}
