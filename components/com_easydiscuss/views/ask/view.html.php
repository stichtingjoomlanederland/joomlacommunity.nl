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

require_once DISCUSS_ROOT . '/views.php';
require_once DISCUSS_CLASSES . '/composer.php';

class EasyDiscussViewAsk extends EasyDiscussView
{
	/**
	 *	Method is called when the new form is called.
	 *
	 * @since	1.0
	 * @access	public
	 */
	public function display($tpl = null)
	{
		$app	= JFactory::getApplication();
		$doc	= JFactory::getDocument();
		$my		= JFactory::getUser();
		$acl	= DiscussHelper::getHelper( 'ACL' );
		$config	= DiscussHelper::getConfig();

		// Load post item
		$id		= JRequest::getInt( 'id' , 0 );
		$post	= DiscussHelper::getTable( 'Post' );
		$post->load( $id );

		$post->content_raw = $post->content;

		$editing	= (bool) $post->id;

		if( ! $editing )
		{
			// try to get from session if there are any.
			$this->getSessionData( $post );
			$post->content_raw = $post->content;
		}

		$categoryId	= JRequest::getInt( 'category' , $post->category_id );

		// Load category item.
		$category 	= DiscussHelper::getTable( 'Category' );
		$category->load( $categoryId );

		// Check if user is allowed to post a discussion, we also need to check against the category acl
		if( empty($my->id) && !$acl->allowed('add_question', 0 ) )
		{
			DiscussHelper::setMessageQueue( JText::_('COM_EASYDISCUSS_PLEASE_KINDLY_LOGIN_TO_CREATE_A_POST') );
			$app->redirect( DiscussRouter::_( 'index.php?option=com_easydiscuss&view=index' , false ) );

			$app->close();
			return;
		}

		if( $my->id != 0 && !$acl->allowed('add_question', '0') && !$category->canPost() )
		{
			$app->redirect( DiscussRouter::_('index.php?option=com_easydiscuss&view=index' , false ) , JText::_('COM_EASYDISCUSS_SYSTEM_INSUFFICIENT_PERMISSIONS') );
			$app->close();
			return;
		}

		// Set the breadcrumbs.
		$this->setPathway( JText::_( 'COM_EASYDISCUSS_BREADCRUMBS_ASK') );

		// Set the page title.
		$title	= JText::_( 'COM_EASYDISCUSS_TITLE_ASK' );

		if( $id && $post->id )
		{
			$title 	= JText::sprintf( 'COM_EASYDISCUSS_TITLE_EDIT_QUESTION' , $post->getTitle() );
		}

		// Set the page title
		DiscussHelper::setPageTitle( $title );


		if( $editing )
		{
			$isModerator = DiscussHelper::getHelper( 'Moderator' )->isModerator( $post->category_id );
			if( !DiscussHelper::isMine( $post->user_id ) && !DiscussHelper::isSiteAdmin() && !$acl->allowed( 'edit_question' ) && !$isModerator)
			{
				$app->redirect( DiscussRouter::_('index.php?option=com_easydiscuss&view=post&id='.$postid , false ) , JText::_('COM_EASYDISCUSS_SYSTEM_INSUFFICIENT_PERMISSIONS') );
				$app->close();
			}

			$tagsModel	= DiscussHelper::getModel( 'PostsTags' );
			$post->tags	= $tagsModel->getPostTags( $post->id );
		}
		else
		{
			if( $categoryId )
			{
				// set the default category
				$post->category_id = $categoryId;
			}
		}

		$attachments = $post->getAttachments();

		if( isset( $post->sessiondata ) )
		{
			$attachments    = '';
		}


		$model		= DiscussHelper::getModel( 'Posts' );
		$postCount	= count( $model->getPostsBy( 'user' , $my->id ) );


		$onlyPublished = ( empty( $post->id ) ) ? true : false;

		// @rule: If there is a category id passed through the query, respect it first.
		$showPrivateCat		= ( empty($post->id) && $my->id == 0 ) ? false : true;

		// [model:category]
		$categoryModel		= $this->getModel( 'Category' );

		$defaultCategory	= $categoryModel->getDefaultCategory();

		if( $categoryId == 0 && $defaultCategory !== false )
		{
			$categoryId 		= $defaultCategory->id;
		}

		$nestedCategories	= '';
		$categories			= '';

		if( $config->get( 'layout_category_selection' ) == 'multitier' )
		{
			$categoriesModel	= $this->getModel( 'Categories');
			$categories			= $categoriesModel->getCategories( array('acl_type' => DISCUSS_CATEGORY_ACL_ACTION_SELECT) );
		}
		else
		{
			$nestedCategories	= DiscussHelper::populateCategories('', '', 'select', 'category_id', $categoryId , true, $onlyPublished, $showPrivateCat , true );
		}

		// if nested categories return null result
		if ($my->id != 0 && empty($nestedCategories)) {
			$app->redirect( DiscussRouter::_('index.php?option=com_easydiscuss&view=index' , false ) , JText::_('COM_EASYDISCUSS_SYSTEM_INSUFFICIENT_PERMISSIONS') );
			$app->close();
			return;
		}

		// if( $config->get( 'layout_editor' ) == 'bbcode' )
		// {
		// 	// Legacy fix when switching from WYSIWYG editor to bbcode.
		// 	$post->content	= EasyDiscussParser::html2bbcode( $post->content );
		// }
		// else
		// {
		// 	$post->content = DiscussHelper::parseContent( $post->content, true );
		// }

		$editor = '';
		if( $config->get('layout_editor' ) != 'bbcode' )
		{
			$editor	= JFactory::getEditor( $config->get('layout_editor' ) );
		}

		// Get list of moderators from the site.
		$moderatorList = array();
		if( $config->get('main_assign_user') )
		{
			$moderatorList	= DiscussHelper::getHelper( 'Moderator' )->getSelectOptions( $post->category_id );
		}

		// Get post types list
		$postTypesModel = DiscussHelper::getModel( 'Post_types' );
		$postTypes = $postTypesModel->getTypes();

		$composer = new DiscussComposer("creating", $post);

		// Set the discussion object.
		$access		= $post->getAccess( $category );

		$theme 		= new DiscussThemes();

		// Test if reference is passed in query string.
		$reference 		= JRequest::getWord( 'reference' );
		$referenceId	= JRequest::getInt( 'reference_id' , 0 );
		$redirect		= JRequest::getVar( 'redirect' , '' );

		$theme->set( 'redirect'			, $redirect );
		$theme->set( 'reference'		, $reference );
		$theme->set( 'referenceId'		, $referenceId );


		$theme->set( 'isEditMode'		, $editing );
		$theme->set( 'post'				, $post );
		$theme->set( 'composer'			, $composer );
		$theme->set( 'nestedCategories'	, $nestedCategories );
		$theme->set( 'attachments'		, $attachments );
		$theme->set( 'editor'			, $editor );
		$theme->set( 'moderatorList'	, $moderatorList );
		$theme->set( 'categories'		, $categories );
		$theme->set( 'access'			, $access );
		$theme->set( 'postTypes'		, $postTypes );

		// Deprecated since 3.0. Will be removed in 4.0
		$theme->set( 'config'			, $config );

		echo $theme->fetch( 'form.new.php' );
	}

	private function getSessionData( &$post )
	{
		// Get form values from session.
		$data		= DiscussHelper::getSession('NEW_POST_TOKEN');

		if( !empty( $data ) )
		{
			// Try to bind the data from the object.
			$post->bind( $data , true );

			//$post->polls			= array();
			$post->tags				= array();
			$post->attachments		= array();

			if( isset( $data[ 'tags' ] ) )
			{
				foreach( $data[ 'tags' ] as $tag )
				{
					$obj 			= new stdClass();
					$obj->title		= $tag;

					$post->tags[]	= $obj;
				}
			}

			if( isset($data['polls']) && isset($data['pollitems']) && is_array($data['pollitems']) )
			{
				$polls = array();
				foreach( $data['pollitems'] as $key => $value )
				{
					$poll		 = DiscussHelper::getTable( 'Poll' );
					$poll->id 	 = $key;
					$poll->value = $value;

					$polls[]	= $poll;
				}
				$post->setPolls( $polls );
			}

			$poll 	= DiscussHelper::getTable( 'PollQuestion' );
			$poll->title    	= isset( $data['poll_question'] ) ? $data['poll_question'] : '';
			$poll->multiple    	= isset( $data['multiplePolls'] ) ? $data['multiplePolls'] : false;


			$post->setPollQuestions( $poll );


			// Process custom fields.
			$customfields = array();
			$fieldIds 	  = isset( $data['customFields'] ) ? $data['customFields'] : '';
			if( !empty($fieldIds) )
			{
				foreach( $fieldIds as $fieldId )
				{
					$fields	= isset( $data['customFieldValue_'.$fieldId ] ) ? $data['customFieldValue_'.$fieldId ] : '';
					//$post->bindCustomFields( $fields, $fieldId );
					//$customfields[] = array( $fieldId => $fields[0] );
					$customfields[] = array( $fieldId => $fields );
				}

				$post->setCustomFields( $customfields );
			}

// 			$attachmentsTmp = $data['attachments'];
//
// 			if( $attachmentsTmp )
// 			{
// 				$attachments	= array();
//
// 				for( $i = 0; $i < count($attachmentsTmp['name']); $i++ )
// 				{
//
// 					$row = new stdClass();
//
// 					$row->title = $attachmentsTmp['name'][$i];
// 					$row->mime  = $attachmentsTmp['type'][$i];
// 					$row->type  = 'question';
//
// 					$table	= JTable::getInstance( 'Attachments' , 'Discuss' );
// 					$table->bind( $row );
//
// 					$type = explode("/", $row->mime);
// 					$table->attachmentType = $type[0];
//
// 					$attachments[]	= $table;
// 				}
//
// 				$post->setAttachments($attachments);
// 			}




			$post->bindParams( $data );

			$post->sessiondata = true;
		}
	}
}
