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

require_once( DISCUSS_ROOT . '/views.php' );

class EasyDiscussViewTags extends EasyDiscussView
{
	public function display( $tmpl = null )
	{
		$doc		= JFactory::getDocument();
		DiscussHelper::setPageTitle( JText::_( 'COM_EASYDISCUSS_TAGS_TITLE' ) );

		$user		= JFactory::getUser();
		$id			= JRequest::getInt( 'id' );

		if( $id )
		{
			return $this->tag( $tmpl );
		}

		$model 		= DiscussHelper::getModel( 'Tags' );
		$tagCloud	= $model->getTagCloud( '', '' , '' );


		$this->setPathway( JText::_('COM_EASYDISCUSS_TOOLBAR_TAGS') );

		$tpl	= new DiscussThemes();
		$tpl->set( 'tagCloud', $tagCloud );
		$tpl->set( 'user', $user );

		echo $tpl->fetch( 'tags.list.php' );
	}

	public function tag( $tmpl = null )
	{
		//initialise variables
		$mainframe	= JFactory::getApplication();
		$doc	= JFactory::getDocument();
		$user		= JFactory::getUser();
		$config		= DiscussHelper::getConfig();
		$tag		= JRequest::getInt( 'id' , 0 );

		if( empty($tag) )
		{
			return JError::raiseError(404, JText::_('COM_EASYDISCUSS_INVALID_TAG'));
		}

		DiscussHelper::setMeta();

		$table			= DiscussHelper::getTable( 'Tags' );
		$table->load( $tag );

		$doc		= JFactory::getDocument();
		DiscussHelper::setPageTitle( JText::sprintf( 'COM_EASYDISCUSS_VIEWING_TAG_TITLE' , $this->escape( $table->title ) ) );

		$this->setPathway( JText::_( $table->title ) );
		$concatCode		= DiscussHelper::getJConfig()->getValue( 'sef' ) ? '?' : '&';
		$doc->addHeadLink( JRoute::_( 'index.php?option=com_easydiscuss&view=tags&id=' . $tag ) . $concatCode . 'format=feed&type=rss' , 'alternate' , 'rel' , array('type' => 'application/rss+xml', 'title' => 'RSS 2.0') );
		$doc->addHeadLink( JRoute::_( 'index.php?option=com_easydiscuss&view=tags&id=' . $tag ) . $concatCode . 'format=feed&type=atom' , 'alternate' , 'rel' , array('type' => 'application/atom+xml', 'title' => 'Atom 1.0') );

		$filteractive	= JRequest::getString('filter', 'allposts');
		$sort			= JRequest::getString('sort', 'latest');

		if($filteractive == 'unanswered' && ($sort == 'active' || $sort == 'popular'))
		{
			//reset the active to latest.
			$sort = 'latest';
		}

		$postModel 	= DiscussHelper::getModel( 'Posts' );

		$posts		= $postModel->getTaggedPost($tag, $sort, $filteractive);
		$pagination	= $postModel->getPagination($sort, $filteractive);


		$authorIds  = array();
		$topicIds 	= array();

		if(count( $posts ) > 0 )
		{
			foreach( $posts as $item )
			{
				$authorIds[]  = $item->user_id;
				$topicIds[]   = $item->id;
			}
		}

		$lastReplyUser      = $postModel->setLastReplyBatch( $topicIds );
		$authorIds			= array_merge( $lastReplyUser, $authorIds );

		// Reduce SQL queries by pre-loading all author object.
		$authorIds  = array_unique($authorIds);
		$profile	= DiscussHelper::getTable( 'Profile' );
		$profile->init( $authorIds );

		$postLoader   = DiscussHelper::getTable('Posts');
		$postLoader->loadBatch( $topicIds );

		$postTagsModel		= DiscussHelper::getModel( 'PostsTags' );
		$postTagsModel->setPostTagsBatch( $topicIds );


		$posts		= DiscussHelper::formatPost($posts, false , true);
		$currentTag	= $table->title;
		
		$posts = Discusshelper::getPostStatusAndTypes( $posts );

		$tpl			= new DiscussThemes();
		$tpl->set( 'rssLink'	, JRoute::_( 'index.php?option=com_easydiscuss&view=tags&id=' . $tag . '&format=feed' ) );
		$tpl->set( 'posts'	, $posts );
		$tpl->set( 'paginationType'	, DISCUSS_TAGS_TYPE );
		$tpl->set( 'pagination'	, $pagination );
		$tpl->set( 'sort'		, $sort );
		$tpl->set( 'filter'		, $filteractive );
		$tpl->set( 'showEmailSubscribe'	, true );
		$tpl->set( 'currentTag'	, $currentTag );
		$tpl->set( 'parent_id'	, $tag );
		$tpl->set( 'config'	, $config );

		echo $tpl->fetch( 'tag.php' );
	}

	function tags( $tmpl = null )
	{
		//initialise variables
		$mainframe	= JFactory::getApplication();
		$document	= JFactory::getDocument();
		$user		= JFactory::getUser();
		$config		= DiscussHelper::getConfig();
		$tags		= JRequest::getVar( 'ids' );

		if( is_null($tags) || $tags == '' )
		{
			$mainframe->enqueueMessage(JText::_('COM_EASYDISCUSS_INVALID_TAG'), 'error');
			$mainframe->redirect(DiscussRouter::_('index.php?option=com_easydiscuss&view=index'));
		}

		$tags = explode('+', $tags);

		if( count($tags) < 2 )
		{
			$tags = explode(' ', $tags[0]);
		}

		$dirtyTags	= $tags;
		unset($tags);
		$tags	= array();

		foreach ($dirtyTags as $dirtyTag) {
			$dirtyTag = (int) $dirtyTag;
			if( !empty($dirtyTag) ) {
				$tags[] = $dirtyTag;
			}
		}

		if( empty($tags) )
		{
			$mainframe->enqueueMessage(JText::_('COM_EASYDISCUSS_INVALID_TAG'), 'error');
			$mainframe->redirect(DiscussRouter::_('index.php?option=com_easydiscuss&view=index'));
		}

		$this->setPathway( JText::_( 'COM_EASYDISCUSS_TAGS' ) , DiscussRouter::_( 'index.php?option=com_easydiscuss&view=tags' ) );
		DiscussHelper::setMeta();

		$tagNames	= array();
		foreach ($tags as $tag)
		{
			$table		= DiscussHelper::getTable( 'Tags' );
			$table->load( $tag );
			$tagNames[]	= JText::_( $table->title );
		}

		$this->setPathway( implode(' + ', $tagNames) );

		$tagIDs		= implode('+', $tags);

		$concatCode		= DiscussHelper::getJConfig()->getValue( 'sef' ) ? '?' : '&';
		$document->addHeadLink( JRoute::_( 'index.php?option=com_easydiscuss&view=tags&ids=' . $tagIDs ) . $concatCode . 'format=feed&type=rss' , 'alternate' , 'rel' , array('type' => 'application/rss+xml', 'title' => 'RSS 2.0') );
		$document->addHeadLink( JRoute::_( 'index.php?option=com_easydiscuss&view=tags&ids=' . $tagIDs ) . $concatCode . 'format=feed&type=atom' , 'alternate' , 'rel' , array('type' => 'application/atom+xml', 'title' => 'Atom 1.0') );

		$filteractive	= JRequest::getString('filter', 'allposts');
		$sort			= JRequest::getString('sort', 'latest');

		if($filteractive == 'unanswered' && ($sort == 'active' || $sort == 'popular'))
		{
			//reset the active to latest.
			$sort = 'latest';
		}

		$postModel	= $this->getModel('Posts');
		$posts		= $postModel->getTaggedPost($tags, $sort, $filteractive);
		$pagination	= $postModel->getPagination($sort, $filteractive);
		$posts		= DiscussHelper::formatPost($posts);
		$tagModel	= $this->getModel('Tags');
		$currentTag	= $tagModel->getTagNames($tags);

		$tpl			= new DiscussThemes();
		$tpl->set( 'rssLink'	, JRoute::_( 'index.php?option=com_easydiscuss&view=tags&id=' . $tag . '&format=feed' ) );
		$tpl->set( 'posts'		, $posts );
		$tpl->set( 'paginationType'	, DISCUSS_TAGS_TYPE );
		$tpl->set( 'pagination'	, $pagination );
		$tpl->set( 'sort'		, $sort );
		$tpl->set( 'filter'		, $filteractive );
		$tpl->set( 'showEmailSubscribe'	, true );
		$tpl->set( 'currentTag'	, $currentTag );
		$tpl->set( 'parent_id'	, 0 );
		$tpl->set( 'config'		, $config );

		echo $tpl->fetch( 'tag.php' );
	}
}
