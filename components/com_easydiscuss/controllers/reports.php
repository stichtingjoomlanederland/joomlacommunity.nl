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

jimport('joomla.application.component.controller');

class EasyDiscussControllerReports extends EasyDiscussController
{
	public function submit()
	{
		$config 	= DiscussHelper::getConfig();
		$my 		= JFactory::getUser();
		$id 		= JRequest::getInt( 'id' );
		$app 		= JFactory::getApplication();

		$post 		= DiscussHelper::getTable( 'Post' );
		$state 		= $post->load( $id );

		$acl = DiscussHelper::getHelper( 'ACL' );

		if( !$post->id || !$state )
		{
			DiscussHelper::setMessageQueue( JText::_('COM_EASYDISCUSS_INVALID_POST_ID' ) , DISCUSS_QUEUE_ERROR );
			$app->redirect( DiscussRouter::_( 'index.php?option=com_easydiscuss' , false ) );
			$app->close();
		}

		// Get the URL to the discussion.
		$url 		= DiscussRouter::getPostRoute( $post->id , false );

		if( $post->isReply() )
		{
			$url 	= DiscussRouter::getPostRoute( $post->parent_id , false );
		}

		if( !$acl->allowed( 'send_report' ) )
		{
			DiscussHelper::setMessageQueue( JText::_('COM_EASYDISCUSS_YOU_DO_NOT_HAVE_PERMISION_TO_SUBMIT_REPORT' ) , DISCUSS_QUEUE_ERROR );
			$app->redirect( $url );
			$app->close();
		}

		if( !$config->get( 'main_report' ) )
		{
			DiscussHelper::setMessageQueue( JText::_('COM_EASYDISCUSS_REPORT_HAS_BEEN_DISABLED_BY_ADMINISTRATOR' ) , DISCUSS_QUEUE_ERROR );
			$app->redirect( $url );
			$app->close();
		}

		$message 	= JRequest::getString( 'reporttext' , '' );

		if( empty( $message ) )
		{
			DiscussHelper::setMessageQueue( JText::_('COM_EASYDISCUSS_REPORT_EMPTY_TEXT' ) , DISCUSS_QUEUE_ERROR );
			$app->redirect( $url );
			$app->close();
		}

		$date   	= DiscussHelper::getDate();
		$report 	= DiscussHelper::getTable( 'Report' );
		$report->created_by	= $my->id;
		$report->post_id 	= $post->id;
		$report->reason 	= $message;
		$report->created    = $date->toMySQL();

		if( !$report->store() )
		{
			DiscussHelper::setMessageQueue( $report->getError() , DISCUSS_QUEUE_ERROR );
			$app->redirect( $url );
			$app->close();
		}

		// Mark post as reported.
		$report->markPostReport();

		$threshold 		= $config->get('main_reportthreshold', 15);
		$totalReports	= $report->getReportCount();
		$redirectMessage 		= JText::_('COM_EASYDISCUSS_REPORT_SUBMITTED');

		// Check if the number of reports for this post exceeded the threshold.
		if( $totalReports > $reportThreshold )
		{
			$owner 		= $post->getOwner();
			$date 		= DiscussHelper::getDate( $post->created );

			$emailData						= array();
			$emailData['postContent']		= $post->content;
			$emailData['postAuthor']		= $owner->name;
			$emailData['postAuthorAvatar']	= $owner->avatar;
			$emailData['postDate']			= $date->toFormat();
			$emailData['postLink']			= DiscussRouter::getRoutedURL('index.php?option=com_easydiscuss&view=post&id=' . $post->id, false, true);
			$emailData[ 'emailSubject' ]	= JText::sprintf('COM_EASYDISCUSS_REPORT_REQUIRED_YOUR_ATTENTION', JString::substr($postTbl->content, 0, 15) ) . '...';
			$emailData[ 'emailTemplate' ]	= 'email.post.attention.php';

			if( $post->isReply() )
			{
				$emailData['postLink']		= DiscussRouter::getRoutedURL('index.php?option=com_easydiscuss&view=post&id=' . $post->parent_id , false, true);
			}

			DiscussHelper::getHelper( 'Mailer' )->notifyAdministrators( $emailData, array(), $config->get( 'notify_admin' ), $config->get( 'notify_moderator' ) );


			$redirectMessage	= JText::_('COM_EASYDISCUSS_REPORT_SUBMITTED_BUT_POST_MARKED_AS_REPORT');
		}

		DiscussHelper::setMessageQueue( JText::_( 'COM_EASYDISCUSS_REPORT_SUBMITTED' ) , DISCUSS_QUEUE_SUCCESS );
		$app->redirect( $url );
	}

}
