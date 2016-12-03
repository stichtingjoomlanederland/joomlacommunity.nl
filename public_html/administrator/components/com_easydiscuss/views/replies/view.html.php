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

require_once DISCUSS_ADMIN_ROOT . '/views.php';

class EasyDiscussViewReplies extends EasyDiscussAdminView
{
	public function display($tpl = null)
	{
		$this->checkAccess('discuss.manage.replies');

		// Initialise variables
		$mainframe	= JFactory::getApplication();

		$filter_state	= $mainframe->getUserStateFromRequest( 'com_easydiscuss.replies.filter_state',		'filter_state', 	'*',	'word' );
		$search			= $mainframe->getUserStateFromRequest( 'com_easydiscuss.replies.search',			'search', 			'',		'string' );

		$search			= trim(JString::strtolower( $search ) );
		$order			= $mainframe->getUserStateFromRequest( 'com_easydiscuss.replies.filter_order',		'filter_order',		'a.id',		'cmd' );
		$orderDirection	= $mainframe->getUserStateFromRequest( 'com_easydiscuss.replies.filter_order_Dir',	'filter_order_Dir',	'',			'word' );

		$postModel		= ED::model('Replies');

		$posts			= $postModel->getPosts();
		$pagination		= $postModel->getPagination();


		$this->assignRef( 'posts' 		, $posts );
		$this->assignRef( 'pagination'	, $pagination );

		$this->assign( 'state'			, $this->getFilterState($filter_state) );
		$this->assign( 'search'			, $search );
		$this->assign( 'order'			, $order );
		$this->assign( 'orderDirection'	, $orderDirection );

		parent::display($tpl);
	}
}
