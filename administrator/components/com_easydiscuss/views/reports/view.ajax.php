<?php
/**
* @package		EasyDiscuss
* @copyright	Copyright (C) 2010 Stack Ideas Private Limited. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasyDiscuss is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Restricted access');

require_once DISCUSS_ADMIN_ROOT . '/views.php';
require_once DISCUSS_HELPERS . '/string.php';

class EasyDiscussViewReports extends EasyDiscussAdminView
{
	public function ajaxSubmitEmail( $data )
	{
		$my		= JFactory::getUser();
		$djax	= new Disjax();
		$post	= DiscussStringHelper::ajaxPostToArray($data);

		if($my->id == 0)
		{
			$djax->alert(JText::_('COM_EASYDISCUSS_YOU_DO_NOT_HAVE_PERMISION_TO_SUBMIT_REPORT'), JText::_('ERROR'), '450', 'auto');
			$djax->send();
			return;
		}

		// Load language files from front end.
		JFactory::getLanguage()->load( 'com_easydiscuss' , JPATH_ROOT );

		if(empty($post['post_id']))
		{
			$djax->alert(JText::_('COM_EASYDISCUSS_INVALID_POST_ID'), JText::_('ERROR'), '450', 'auto');
			$djax->send();
			return;
		}

		$postId			= (int) $post['post_id'];
		$emailContent	= $post['content'];

		// Prepare email data
		$postTbl	= JTable::getInstance( 'posts', 'Discuss' );
		$postTbl->load($postId);

		$moderator	= DiscussHelper::getTable( 'Profile' );
		$moderator->load( $my->id );

		$creator	= JFactory::getUser($postTbl->user_id);
		$date 		= DiscussHelper::getDate( $postTbl->created );

		$emailData						= array();
		$emailData['postAuthor']		= $moderator->getName();
		$emailData['postAuthorAvatar']	= $moderator->getAvatar();
		$emailData['postDate']			= $date->toFormat();
		$emailData['postLink']			= JURI::root() . 'index.php?option=com_easydiscuss&view=post&id=' . $postTbl->id;
		$emailData['postTitle']			= $postTbl->title;
		$emailData['messages']			= $emailContent;

		if(! empty($postTbl->parent_id))
		{
			$parentTbl = JTable::getInstance( 'posts', 'Discuss' );
			$parentTbl->load( $postTbl->parent_id );

			$emailData['postTitle']		= $parentTbl->title;
			$emailData['postLink']		= JURI::root() . 'index.php?option=com_easydiscuss&view=post&id=' . $parentTbl->id;
		}

		$noti	= DiscussHelper::getNotification();
		$noti->addQueue( $creator->email , JText::sprintf('COM_EASYDISCUSS_REQUIRED_YOUR_ATTENTION', $emailData['postTitle']), '', 'email.report.attention.php', $emailData);

		$djax->assign('report-entry-msg-' . $postId, JText::_( 'COM_EASYDISCUSS_EMAIL_SENT_TO_AUTHOR' ) );
		$djax->script('admin.reports.revertEmailForm("' . $postId . '");');

		$djax->send();
		return;
	}
}
