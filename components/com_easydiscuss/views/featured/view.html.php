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

jimport( 'joomla.application.component.view');

require_once( DISCUSS_ROOT . '/views.php' );

require_once DISCUSS_HELPERS . '/filter.php';
require_once DISCUSS_HELPERS . '/integrate.php';

class EasyDiscussViewFeatured extends EasyDiscussView
{
	function display($tpl = null)
	{
		//initialise variables
		$document	= JFactory::getDocument();
		$user		= JFactory::getUser();
		$config		= DiscussHelper::getConfig();
		$mainframe	= JFactory::getApplication();

		$acl = DiscussHelper::getHelper( 'ACL' );

		$filteractive	= JRequest::getString('filter', 'allposts');
		$query			= JRequest::getString('query', '');
		$sort			= JRequest::getString('sort', 'latest');

		$category		= JRequest::getInt( 'category_id' , 0 );

		$postModel		= $this->getModel('Posts');

		$featuredposts		= '';
		$featuredpostsHTML	= '';

		$showFeaturedPost	= true;

		$posts			= $postModel->getData( true , $sort , null , $filteractive , $category, null, $showFeaturedPost);
		$pagination		= $postModel->getPagination( '0' , $sort, $filteractive , $category, $showFeaturedPost );
		$posts			= DiscussHelper::formatPost($posts);

		$concatCode		= DiscussHelper::getJConfig()->getValue( 'sef' ) ? '?' : '&';
		$document->addHeadLink( JRoute::_( 'index.php?option=com_easydiscuss&view=featured') . $concatCode . 'format=feed&type=rss' , 'alternate' , 'rel' , array('type' => 'application/rss+xml', 'title' => 'RSS 2.0') );
		$document->addHeadLink( JRoute::_( 'index.php?option=com_easydiscuss&view=featured') . $concatCode . 'format=feed&type=atom' , 'alternate' , 'rel' , array('type' => 'application/atom+xml', 'title' => 'Atom 1.0') );

		$rssLink	= DiscussHelper::getHelper( 'Feeds' )->getFeedURL( 'index.php?option=com_easydiscuss&view=featured' );

		$tpl			= new DiscussThemes();

		$tpl->set( 'rssLink'	, $rssLink );
		$filterBar		= '';

		$tpl->set( 'acl'			, $acl );
		$tpl->set( 'posts'			, $posts );
		$tpl->set( 'paginationType'	, DISCUSS_QUESTION_TYPE );
		$tpl->set( 'parent_id'		, 0 );
		$tpl->set( 'pagination'		, $pagination );
		$tpl->set( 'sort'			, $sort );
		$tpl->set( 'filter'			, $filteractive );
		$tpl->set( 'filterbar'		, $filterBar );
		$tpl->set( 'query'			, $query );
		$tpl->set( 'config'			, $config );

		echo $tpl->fetch( 'featured.php' );
	}
}
