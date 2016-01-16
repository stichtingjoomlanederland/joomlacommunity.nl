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

class EasyDiscussControllerTags extends EasyDiscussController
{
	function __construct()
	{
		parent::__construct();

		$this->registerTask( 'add' , 'edit' );
		$this->registerTask('unpublish', 'unpublish');
		$this->registerTask( 'savePublishNew' , 'save' );
	}

	function save()
	{
		$mainframe	= JFactory::getApplication();

		$message	= '';
		$type		= 'message';

		$task 		= $this->getTask();

		if( JRequest::getMethod() == 'POST' )
		{
			$post				= JRequest::get( 'post' );

			if(empty($post['title']))
			{
				DiscussHelper::setMessageQueue( JText::_( 'COM_EASYDISCUSS_INVALID_TAG' ) , DISCUSS_QUEUE_ERROR );
				$url  = 'index.php?option=com_easydiscuss&view=tags';
				$mainframe->redirect(JRoute::_($url, false));
				return;
			}

			$user				= JFactory::getUser();
			$post['user_id']	= $user->id;
			$tagId				= JRequest::getVar( 'tagid' , '' );
			$tag				= JTable::getInstance( 'tags', 'Discuss' );

			if( !empty( $tagId ) )
			{
				$tag->load( $tagId );
			}
			else
			{
				$tagModel 	= $this->getModel( 'Tags' );
				$result 	= $tagModel->searchTag($tag->title);

				if(!empty($result))
				{
					DiscussHelper::setMessageQueue( JText::_( 'COM_EASYDISCUSS_TAG_EXISTS' ) , DISCUSS_QUEUE_ERROR );
					$mainframe->redirect( 'index.php?option=com_easydiscuss&view=tags' );
				}
			}

			$tag->bind( $post );

			$tag->title = JString::trim($tag->title);
			$tag->alias = JString::trim($tag->alias);

			if (!$tag->store())
			{
				JError::raiseError(500, $tag->getError() );
			}
			else
			{
				$message	= JText::_( 'COM_EASYDISCUSS_TAG_SAVED' );
			}

			$mergeTo	= isset($post['mergeTo']) ? (int) $post['mergeTo'] : 0;

			$mergeToTag	= DiscussHelper::getTable( 'Tags' );
			$mergeToTag->load($mergeTo);

			if( $mergeToTag->id > 0 && $tag->id > 0 )
			{
				// Move to merge tag id
				$db		= DiscussHelper::getDBO();

				// Find posts tagged in both id
				$query	= 'SELECT a.id FROM #__discuss_posts_tags AS a'
						. ' LEFT JOIN #__discuss_posts_tags AS b ON b.post_id = a.post_id'
						. ' WHERE a.tag_id = ' . $db->quote( $tag->id )
						. ' AND b.tag_id = ' . $db->quote( $mergeToTag->id )
						. ' GROUP BY a.post_id';
				$db->setQuery( $query );
				$excludeIds		= $db->loadResultArray();

				// Do not update post having both tags, let $table->delete() handle them
				$query	= 'UPDATE `#__discuss_posts_tags`'
						. ' SET `tag_id` = ' . $db->quote($mergeToTag->id)
						. ' WHERE `tag_id` = ' . $db->quote($tag->id);

				if( count($excludeIds) > 0 )
				{
					JArrayHelper::toInteger($excludeIds);

					$query .= ' AND `id` NOT IN (' . implode(',', $excludeIds) . ')';
				}

				$db->setQuery( $query );
				$db->query();

				$tag->delete();
			}
		}
		else
		{
			$message	= JText::_('COM_EASYDISCUSS_INVALID_FORM_METHOD');
			$type		= 'error';
		}

		DiscussHelper::setMessageQueue( $message , $type );

		if( $task == 'savePublishNew' )
		{
			$mainframe->redirect( 'index.php?option=com_easydiscuss&view=tag' );
			$mainframe->close();
		}

		$mainframe->redirect( 'index.php?option=com_easydiscuss&view=tags' );
	}

	function cancel()
	{
		$this->setRedirect( 'index.php?option=com_easydiscuss&view=tags' );

		return;
	}

	function edit()
	{
		JRequest::setVar( 'view', 'tag' );
		JRequest::setVar( 'tagid' , JRequest::getVar( 'tagid' , '' , 'REQUEST' ) );

		parent::display();
	}

	function remove()
	{
		$tags	= JRequest::getVar( 'cid' , '' , 'POST' );

		$message	= '';
		$type		= 'success';

		if( empty( $tags ) )
		{
			$message	= JText::_('COM_EASYDISCUSS_INVALID_TAG_ID');
			$type		= 'error';
		}
		else
		{
			$table		= JTable::getInstance( 'Tags' , 'Discuss' );
			foreach( $tags as $tag )
			{
				$table->load( $tag );

				if( !$table->delete() )
				{
					$message	= JText::_( 'COM_EASYDISCUSS_REMOVE_TAG_ERROR' );
					$type		= 'error';
					DiscussHelper::setMessageQueue( $message , $type );
					$this->setRedirect( 'index.php?option=com_easydiscuss&view=tags' );
					return;
				}
			}

			$message	= JText::_('COM_EASYDISCUSS_TAG_DELETED');
		}

		DiscussHelper::setMessageQueue( $message , $type );

		$this->setRedirect( 'index.php?option=com_easydiscuss&view=tags' );
	}

	function publish()
	{
		$tags	= JRequest::getVar( 'cid' , array(0) , 'POST' );

		$message	= '';
		$type		= 'success';

		if( count( $tags ) <= 0 )
		{
			$message	= JText::_('COM_EASYDISCUSS_INVALID_TAG_ID');
			$type		= 'error';
		}
		else
		{
			$model		= $this->getModel( 'Tags' );

			if( $model->publish( $tags , 1 ) )
			{
				$message	= JText::_('COM_EASYDISCUSS_TAG_PUBLISHED');
			}
			else
			{
				$message	= JText::_('COM_EASYDISCUSS_TAG_PUBLISH_ERROR');
				$type		= 'error';
			}

		}
		DiscussHelper::setMessageQueue( $message , $type );

		$this->setRedirect( 'index.php?option=com_easydiscuss&view=tags' );
	}

	function unpublish()
	{
		$tags	= JRequest::getVar( 'cid' , array(0) , 'POST' );

		$message	= '';
		$type		= 'success';

		if( count( $tags ) <= 0 )
		{
			$message	= JText::_('COM_EASYDISCUSS_INVALID_TAG_ID');
			$type		= 'error';
		}
		else
		{
			$model		= $this->getModel( 'Tags' );

			if( $model->publish( $tags , 0 ) )
			{
				$message	= JText::_('COM_EASYDISCUSS_TAG_UNPUBLISHED');
			}
			else
			{
				$message	= JText::_('COM_EASYDISCUSS_TAG_UNPUBLISH_ERROR');
				$type		= 'error';
			}

		}
		DiscussHelper::setMessageQueue( $message , $type );

		$this->setRedirect( 'index.php?option=com_easydiscuss&view=tags' );
	}
}
