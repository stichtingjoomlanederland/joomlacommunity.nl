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

require_once( DISCUSS_ROOT . '/views.php' );

class EasyDiscussViewIndex extends EasyDiscussView
{
	function ajaxRemoveSubscription( $type = null, $subscribeId = null )
	{
		$ajax		= new disjax();
		$my			= JFactory::getUser();

		$options	= new stdClass;
		$options->title 	= JText::_( 'COM_EASYDISCUSS_UNSUBSCRIBE_TO_' . strtoupper($type) );

		$buttons			= array();

		$button				= new stdClass();
		$button->title		= JText::_( 'COM_EASYDISCUSS_BUTTON_OK' );
		$button->action		= 'disjax.closedlg();';
		$buttons[]			= $button;

		$options->buttons	= $buttons;

		if( $my->id == 0)
		{
			$options->content	= JText::_('COM_EASYDISCUSS_NOT_ALLOWED_HERE');

			$ajax->dialog( $options );
			$ajax->send();
			return;
		}

		$subcription	= DiscussHelper::getTable( 'Subscribe' );
		$subcription->load( $subscribeId );

		if( empty($subcription->type) )
		{
			$options->content	= JText::_('COM_EASYDISCUSS_UNSUBSCRIPTION_FAILED_NO_RECORD_FOUND');

			$ajax->dialog( $options );
			$ajax->send();
			return;
		}

		//check if the id belong to the user or not.
		if( ! DiscussHelper::isMySubscription( $my->id, $type, $subscribeId) )
		{
			$options->content	= JText::_('COM_EASYDISCUSS_NOT_ALLOWED_HERE');

			$ajax->dialog( $options );
			$ajax->send();
			return;
		}

		switch($type)
		{
			case 'post';
				$options->content	= JText::_('COM_EASYDISCUSS_UNSUBSCRIPTION_POST_SUCCESS');
				break;
			case 'site':
				$options->content	= JText::_('COM_EASYDISCUSS_UNSUBSCRIPTION_SITE_SUCCESS');
				break;
			case 'category':
				$options->content	= JText::_('COM_EASYDISCUSS_UNSUBSCRIPTION_CATEGORY_SUCCESS');
				break;
			case 'user':
				$options->content	= JText::_('COM_EASYDISCUSS_UNSUBSCRIPTION_USER_SUCCESS');
				break;
			default:
				break;
		}

		//perform the unsubcribe
		$subcription->delete();

		$ajax->dialog( $options );
		$ajax->send();
		return;
	}

	/**
	 * Display subscription form for users who wants to subscribe to the discussion.
	 *
	 * @since	3.0
	 * @access	public
	 * @param	string	The type of subscription.
	 * @param	int		The unique id of the subscription type. 0 for site.
	 * @return	string
	 */
	function ajaxSubscribe( $type = 'site', $cid = 0 )
	{
		$ajax			= new Disjax();
		$app			= JFactory::getApplication();
		$my				= JFactory::getUser();
		$theme			= new DiscussThemes();
		$options		= new stdClass();
		$model			= DiscussHelper::getModel( 'Subscribe' );
		$interval		= false;
		$subscription	= false;

		$allowed 		= array( 'site' , 'post' , 'tag' , 'category' );

		if( !in_array( $type , $allowed ) )
		{
			return;
		}
		
		$theme->set( 'cid', $cid );
		$theme->set( 'type', $type );

		if( $type == 'post' )
		{
			$options->title 	= JText::_( 'COM_EASYDISCUSS_SUBSCRIBE_TO_POST' );
			$options->content	= $theme->fetch( 'ajax.subscribe.post.php', array( 'dialog'=> true ) );
		}
		else
		{
			$options->title 	= JText::_( 'COM_EASYDISCUSS_SUBSCRIBE_TO_' . strtoupper($type) );

			$interval       = '';
			$subscription	= $model->isSiteSubscribed( $type , $my->email , $cid );
			if( $subscription )
			{
				$interval		= $subscription->interval;
			}


			$theme->set( 'subscription'	, $subscription );
			$theme->set( 'interval' 	, $interval );

			$options->content	= $theme->fetch( 'ajax.subscribe.' . $type . '.php', array( 'dialog'=> true ) );
		}

		$buttons			= array();

		$button				= new stdClass();
		$button->title		= JText::_( 'COM_EASYDISCUSS_BUTTON_CANCEL' );
		$button->action		= 'disjax.closedlg();';
		$buttons[]			= $button;

		$button				= new stdClass();
		$button->title		= $subscription ? JText::_( 'COM_EASYDISCUSS_BUTTON_UPDATE' ) : JText::_( 'COM_EASYDISCUSS_BUTTON_SUBSCRIBE' );
		$button->form		= '#subscribeForm';
		$button->className	= 'btn-primary';
		$buttons[]			= $button;

		$options->buttons	= $buttons;

		$ajax->dialog( $options );
		$ajax->send();
	}

	function ajaxUnSubscribe( $type = 'site', $sid = 0, $cid = 0 )
	{
		$ajax			= new Disjax();
		$options		= new stdClass();

		$options->title		= JText::_( 'COM_EASYDISCUSS_UNSUBSCRIBE_TO_' . strtoupper($type) );

		$theme 				= new DiscussThemes();
		$theme->set( 'cid' , $cid );
		$content 			= $theme->fetch( 'ajax.unsubscribe.' . strtolower( $type ) . '.php' , array( 'dialog' => true ) );
		$options->content	= $content;

		$buttons			= array();

		$button				= new stdClass();
		$button->title		= JText::_( 'COM_EASYDISCUSS_BUTTON_CANCEL' );
		$button->action		= 'disjax.closedlg();';
		$buttons[]			= $button;

		// $button				= new stdClass();
		// $button->title		= JText::_( 'COM_EASYDISCUSS_BUTTON_UPDATE' );
		// $button->action		= "disjax.loadingDialog();disjax.load('index', 'ajaxSubscribe', '$type', '$cid')";
		// $buttons[]			= $button;

		$button				= new stdClass();
		$button->title		= JText::_( 'COM_EASYDISCUSS_BUTTON_UNSUBSCRIBE' );
		$button->action		= "disjax.loadingDialog();disjax.load('index', 'ajaxRemoveSubscription', '$type', '$sid')";
		$button->className	= 'btn-primary';
		$buttons[]			= $button;

		$options->buttons	= $buttons;

		$ajax->dialog( $options );
		$ajax->send();
	}

	/**
	 * Ajax call which is responsible to output more entries
	 * on the front listings.
	 **/
	function ajaxReadmore( $limitstart = null, $sorting = null, $type = 'questions' , $parentId = 0, $filter = '', $category = '', $query = '' )
	{
		$func	= 'ajaxReadmore' . ucfirst( $type );
		if( $type == 'questions')
			$this->$func( $limitstart , $sorting , $type , $parentId, $filter, $category, $query );
		else
			$this->$func( $limitstart , $sorting , $type , $parentId, $filter, $category );
	}

	function ajaxReadmoreSearch( $limitstart = null, $sorting = null, $type = null, $parentId = null, $filter = null, $category = null )
	{
		$ajax		= new Disjax();
		$model		= $this->getModel( 'Posts' );
		$limitstart	= (int) $limitstart;
		$mainframe	= JFactory::getApplication();

		$query		= $parentId;
		$posts		= $model->getPostsBy( 'search' , '0' , 'latest' , $limitstart, '', $query);
		$pagination	= $model->getPagination( '0' , $sorting );
		$posts		= DiscussHelper::formatPost($posts);
		$template	= new DiscussThemes();
		$template->set( 'posts'	, $posts );

		$html		= $template->fetch( 'main.item.php' );

		$nextLimit	= $limitstart + DiscussHelper::getListLimit();
		if( $nextLimit >= $pagination->total )
		{
			$ajax->remove( 'dc_pagination a' );
		}

		$ajax->value( 'pagination-start' , $nextLimit );
		$ajax->script( 'EasyDiscuss.$("#dc_list").children( ":last" ).addClass( "separator" );');
		$ajax->append( 'dc_list' , $html );
		$ajax->send();
	}


	function ajaxReadmoreQuestions( $limitstart = null, $sorting = null, $type = null, $parentId = null, $filter = null, $category = '', $query = '' )
	{
		$ajax		= new Disjax();
		$model		= $this->getModel( 'Posts' );
		$limitstart	= (int) $limitstart;
		$mainframe	= JFactory::getApplication();

		if( !empty($query))
			JRequest::setVar('query', $query);

		$pagination	= $model->getPagination( '0' , $sorting, $filter, $category );
		$posts		= $model->getData( true , $sorting , $limitstart, $filter, $category );
		$posts		= DiscussHelper::formatPost($posts);
		$template	= new DiscussThemes();
		$template->set( 'posts'	, $posts );

		$html		= $template->fetch( 'main.item.php' );

		$nextLimit	= $limitstart + DiscussHelper::getListLimit();
		if( $nextLimit >= $pagination->total )
		{
			$ajax->remove( 'dc_pagination a' );
		}

		$ajax->value( 'pagination-start' , $nextLimit );
		$ajax->script( 'EasyDiscuss.$("#dc_list").children( ":last" ).addClass( "separator" );');
		$ajax->append( 'dc_list' , $html );
		$ajax->send();
	}

	function ajaxReadmoreTags( $limitstart = null, $sorting = null, $type = null, $uniqueId = null, $filter = null, $category = null )
	{
		$ajax		= new Disjax();
		$model		= $this->getModel( 'Posts' );
		$limitstart	= (int) $limitstart;
		$mainframe	= JFactory::getApplication();

		$posts		= $model->getTaggedPost( $uniqueId , $sorting, $filter, $limitstart );
		$pagination	= $model->getPagination( '0' , $sorting, $filter );
		$posts		= DiscussHelper::formatPost($posts);
		$template	= new DiscussThemes();
		$template->set( 'posts'	, $posts );

		$html		= $template->fetch( 'main.item.php' );

		$nextLimit	= $limitstart + DiscussHelper::getListLimit();
		if( $nextLimit >= $pagination->total )
		{
			$ajax->remove( 'dc_pagination a' );
		}

		$ajax->value( 'pagination-start' , $nextLimit );
		$ajax->script( 'EasyDiscuss.$("#dc_list").children( ":last" ).addClass( "separator" );');
		$ajax->append( 'dc_list' , $html );
		$ajax->send();
	}

	function ajaxReadmoreUserQuestions( $limitstart = null, $sorting = null, $type = null, $uniqueId = null, $filter = null, $category = null )
	{
		$ajax		= new Disjax();
		$model		= $this->getModel( 'Posts' );
		$limitstart	= (int) $limitstart;
		$mainframe	= JFactory::getApplication();

		$posts		= $model->getPostsBy( 'user' , $uniqueId , 'latest' , $limitstart );
		$pagination	= $model->getPagination( '0' , $sorting );

		$posts		= DiscussHelper::formatPost($posts);
		$template	= new DiscussThemes();
		$template->set( 'posts'	, $posts );

		$html		= $template->fetch( 'main.item.php' );

		$nextLimit	= $limitstart + DiscussHelper::getListLimit();
		if( $nextLimit >= $pagination->total )
		{
			$ajax->remove( 'dc_pagination a' );
		}

		$ajax->value( 'pagination-start' , $nextLimit );
		$ajax->script( 'EasyDiscuss.$("#dc_list").children( ":last" ).addClass( "separator" );');
		$ajax->append( 'dc_list' , $html );
		$ajax->send();
	}

	function ajaxReadmoreReplies( $limitstart = null, $sorting = null, $type = 'questions' , $parentId = null, $filter = null, $category = null )
	{
		$ajax		= new Disjax();
		$model		= $this->getModel( 'Posts' );
		$limitstart	= (int) $limitstart;
		$mainframe	= JFactory::getApplication();
		$config		= DiscussHelper::getConfig();
		$posts		= $model->getReplies( $parentId , $sorting , $limitstart );
		$pagination	= $model->getPagination( $parentId , $sorting );
		$my			= JFactory::getUser();
		$posts		= DiscussHelper::formatPost($posts);
		$parent		= DiscussHelper::getTable( 'Post' );
		$parent->load( $parentId );


		//check all the 'can' or 'canot' here.
		$acl 		= DiscussHelper::getHelper( 'ACL' );

		$isMainLocked	= ( $parent->islock) ? true : false;
		$canDelete	= false;
		$canTag		= false;
		$canReply	= $acl->allowed('add_reply', '0');

		if($config->get('main_allowdelete', 2) == 2)
		{
			$canDelete	= ($isSiteAdmin) ? true : false;
		}
		else if($config->get('main_allowdelete', 2) == 1)
		{
			$canDelete	= $acl->allowed('delete_reply', '0');
		}

		$category 		= DiscussHelper::getTable( 'Category' );
		$category->load( $category );

		$posts		= DiscussHelper::formatReplies( $posts , $category );
		$template	= new DiscussThemes();
		$template->set( 'replies'	, $posts );
		$template->set( 'config'	, $config );
		$template->set( 'canReply', $canReply );
		$template->set( 'canDelete'	, $canDelete );
		$template->set( 'isMainLocked'	, $isMainLocked );
		//$template->set( 'isMine'		, false );
		//$template->set( 'isAdmin'		, false );

		$template->set( 'isMine'		, DiscussHelper::isMine($parent->user_id) );
		$template->set( 'isAdmin'	, DiscussHelper::isSiteAdmin() );

		$html		= $template->fetch( 'reply.item.php' );

		$nextLimit	= $limitstart + DiscussHelper::getListLimit();

		if( $nextLimit >= $pagination->total )
		{
			$ajax->remove( 'dc_pagination a' );
		}

		$ajax->value( 'pagination-start' , $nextLimit );
		$ajax->script( 'EasyDiscuss.$("#dc_response").children().children( ":last").addClass( "separator" );');
		$ajax->append( 'dc_response tbody' , $html );

		$ajax->send();
	}

	/**
	 * Filters discussion based on a given filter
	 *
	 * @since	3.2
	 * @access	public
	 * @param	string
	 * @return	
	 */
	public function filter()
	{
		$filterType = JRequest::getVar( 'filter' );
		$sort 		= JRequest::getVar( 'sort', 'latest' );
		$categoryId = JRequest::getVar( 'id', '0' );

		if( !$categoryId )
		{
			$categoryId 	= array();
		}
		else
		{
			$categoryId		= explode( ',' , $categoryId );
		}


		$view	= JRequest::getVar( 'view', 'index' );

		$ajax	= DiscussHelper::getHelper( 'ajax' );

		JRequest::setVar('filter', $filterType);


		$postModel	= DiscussHelper::getModel( 'Posts' );

		$registry	= DiscussHelper::getRegistry();

		// Get the pagination limit
		$limit			= $registry->get( 'limit' );
		$limit			= ( $limit == '-2' ) ? DiscussHelper::getListLimit() : $limit;
		$limit			= ( $limit == '-1' ) ? DiscussHelper::getJConfig()->get('list_limit') : $limit;

		// Get normal discussion posts.
		$options 	= array(
						'sort'		=> $sort,
						'category'	=> $categoryId,
						'filter'	=> $filterType,
						'limit'		=> $limit,
						'featured'	=> false
					);

		$posts		= $postModel->getDiscussions( $options );

		//$posts		= $postModel->getData( false , $sort , null , $filterType , $categoryId, null, '');


		$posts		= DiscussHelper::formatPost($posts);

		$pagination = '';
		$pagination 	= $postModel->getPagination( 0 , $sort , $filterType , $categoryId, false );


		$filtering = array( 'category_id' => $categoryId,
							'filter' => $filterType,
							'sort' => $sort);

		$pagination		= $pagination->getPagesLinks( $view , $filtering, true);

		$html 			= '';
		$empty 			= '';

		if( count( $posts ) > 0 )
		{
			$template	= new DiscussThemes();

			$badgesTable	= DiscussHelper::getTable( 'Profile' );
			$onlineUsers = Discusshelper::getModel( 'Users' )->getOnlineUsers();

			foreach( $posts as $post )
			{
				$badgesTable->load( $post->user->id );
				$post->badges = $badgesTable->getBadges();

				// Translate post status from integer to string
				switch( $post->post_status )
				{
					case '0':
						$post->post_status_class = '';
						$post->post_status = '';
						break;
					case '1':
						$post->post_status_class = '-on-hold';
						$post->post_status = JText::_( 'COM_EASYDISCUSS_POST_STATUS_ON_HOLD' );
						break;
					case '2':
						$post->post_status_class = '-accept';
						$post->post_status = JText::_( 'COM_EASYDISCUSS_POST_STATUS_ACCEPTED' );
						break;
					case '3':
						$post->post_status_class = '-working-on';
						$post->post_status = JText::_( 'COM_EASYDISCUSS_POST_STATUS_WORKING_ON' );
						break;
					case '4':
						$post->post_status_class = '-reject';
						$post->post_status = JText::_( 'COM_EASYDISCUSS_POST_STATUS_REJECT' );
						break;
					default:
						$post->post_status_class = '';
						$post->post_status = '';
						break;
				}

				$alias = $post->post_type;
				$modelPostTypes = DiscussHelper::getModel( 'Post_types' );

				// Get each post's post status title
				$title = $modelPostTypes->getTitle( $alias );
				$post->post_type = $title;

				// Get each post's post status suffix
				$suffix = $modelPostTypes->getSuffix( $alias );
				$post->suffix = $suffix;

				$template->set( 'post'	, $post );
				$html		.= $template->fetch( 'frontpage.post.php' );
			}
		}
		else
		{
			$template	= new DiscussThemes();
			$html 	.= $template->fetch( 'frontpage.empty.php' );
		}

		// This post is already favourite
		$ajax->resolve( $html, $pagination );
		$ajax->send();
	}

	public function sort( $sort = null, $filter = null, $categoryId = null )
	{
		$ajax			= new Disjax();
		$mainframe		= JFactory::getApplication();
		$user			= JFactory::getUser();
		$config			= DiscussHelper::getConfig();
		$acl			= DiscussHelper::getHelper( 'ACL' );
		$activeCategory	= DiscussHelper::getTable( 'Category' );
		$activeCategory->load( $categoryId );

		//todo: check against the config
		$showAllPosts	= 'all';

		$postModel	= $this->getModel('Posts');
		$posts		= $postModel->getData( true , $sort , null , $filter , $categoryId, null, $showAllPosts );
		$pagination	= $postModel->getPagination( '0' , $sort, $filter, $categoryId, $showAllPosts );
		$posts		= DiscussHelper::formatPost($posts);

		$nextLimit	= DiscussHelper::getListLimit();
		if( $nextLimit >= $pagination->total )
		{
			$ajax->remove( 'dc_pagination .pagination-wrap' );
		}


		$tpl		= new DiscussThemes();
		$tpl->set( 'posts'		, $posts );
		$content	= $tpl->fetch( 'main.item.php' );

		//reset the next start limi
		$ajax->value( 'pagination-start' , $nextLimit );

		if( $nextLimit < $pagination->total )
		{

			$filterArr					= array();
			$filterArr['filter']		= $filter;
			$filterArr['category_id']	= $categoryId;
			$filterArr['sort']			= $sort;
			$ajax->assign( 'dc_pagination', $pagination->getPagesLinks('index', $filterArr, true) );

		}

		$ajax->script( 'discuss.spinner.hide( "index-loading" );' );
		$ajax->script( 'EasyDiscuss.$("#pagination-sorting").val("'.$sort.'");');

		$ajax->assign( 'dc_list' , $content );
		$ajax->script( 'EasyDiscuss.$("#dc_list").show();');
		$ajax->script( 'EasyDiscuss.$("#dc_pagination").show();');

		$ajax->send();
	}

	public function getTemplate( $name = null, $vars = array() )
	{
		$theme	= new DiscussThemes();

		if( !empty( $vars ) )
		{
			foreach( $vars as $key => $value )
			{
				$theme->set( $key , $value );
			}
		}

		$ajax	= new Disjax();
		$option	= new stdClass();
		$option->content	= $theme->fetch( $name , array( 'dialog' => true ));

		$ajax->dialog( $option );
		$ajax->send();
	}

	/**
	 * Responds to the getcategory ajax call by return a list of category items.
	 *
	 * @access	public
	 * @param	null
	 */
	public function getCategory()
	{
		$ajax	= DiscussHelper::getHelper( 'Ajax' );
		$id		= JRequest::getInt( 'id' );

		$model	= $this->getModel( 'categories' );
		$items	= $model->getChildCategories( $id , true, true );

		if( !$items )
		{
			return $ajax->success( array() );
		}

		$categories		= array();

		for($i = 0; $i < count($items); $i++)
		{
			$item		= $items[$i];

			$category	= DiscussHelper::getTable( 'Category' );
			$category->load( $item->id );

			$item->hasChild = $category->getChildCount();
		}

		$ajax->success( $items );
	}
}
