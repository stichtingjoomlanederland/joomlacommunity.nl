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

class EasyDiscussViewBadges extends EasyDiscussAdminView
{
	public function display($tpl = null)
	{
		// @rule: Test for user access if on 1.6 and above
		if( DiscussHelper::getJoomlaVersion() >= '1.6' )
		{
			if(!JFactory::getUser()->authorise('discuss.manage.badges' , 'com_easydiscuss') )
			{
				JFactory::getApplication()->redirect( 'index.php' , JText::_( 'JERROR_ALERTNOAUTHOR' ) , 'error' );
				JFactory::getApplication()->close();
			}
		}
		// Initialise variables
		$document	= JFactory::getDocument();
		$user		= JFactory::getUser();
		$mainframe	= JFactory::getApplication();

		if( $this->getLayout() == 'install' )
		{
			return $this->installLayout();
		}

		if( $this->getLayout() == 'managerules' )
		{
			return $this->manageRules();
		}

		$filter_state	= $mainframe->getUserStateFromRequest( 'com_easydiscuss.badges.filter_state', 	'filter_state', 	'*', 'word' );
		$search			= $mainframe->getUserStateFromRequest( 'com_easydiscuss.badges.search', 			'search', 			'', 'string' );

		$search			= trim(JString::strtolower( $search ) );
		$order			= $mainframe->getUserStateFromRequest( 'com_easydiscuss.badges.filter_order', 		'filter_order', 	'a.id', 'cmd' );
		$orderDirection	= $mainframe->getUserStateFromRequest( 'com_easydiscuss.badges.filter_order_Dir',	'filter_order_Dir',	'', 'word' );

		$exclusion 		= JRequest::getVar( 'exclude' ,'' );

		$model 			= $this->getModel( 'Badges' );
		$badges 		= $model->getBadges( $exclusion );

		$pagination		= $this->get( 'Pagination' );

		// Determines if the current request is shown in a modal window.
		$browse 		= JRequest::getInt( 'browse' , 0 );
		$browseFunction	= JRequest::getVar( 'browseFunction' , '' );

		$this->assign( 'browseFunction'	, $browseFunction );
		$this->assign( 'browse'		, $browse );
		$this->assign( 'badges'		, $badges );
		$this->assign( 'pagination'	, $pagination );

		$this->assign( 'state'			, $this->getFilterState($filter_state));
		$this->assign( 'search'			, $search );
		$this->assign( 'order'			, $order );
		$this->assign( 'orderDirection'	, $orderDirection );

		parent::display($tpl);
	}

	public function getTotalUsers( $badgeId )
	{
		$db		= DiscussHelper::getDBO();
		$query	= 'SELECT COUNT(1) FROM ' . $db->nameQuote( '#__discuss_badges_users' ) . ' '
				. 'WHERE ' . $db->nameQuote( 'badge_id' ) . '=' . $db->Quote( $badgeId );
		$db->setQuery( $query );

		return $db->loadResult();
	}

	public function registerToolbar()
	{
		JToolBarHelper::title( JText::_( 'COM_EASYDISCUSS_BADGES' ), 'badges' );

		JToolBarHelper::custom( 'home', 'arrow-left', '', JText::_( 'COM_EASYDISCUSS_TOOLBAR_HOME' ), false);
		JToolBarHelper::divider();
		JToolBarHelper::custom( 'rules' , 'rules' , '' , JText::_( 'COM_EASYDISCUSS_MANAGE_RULES_BUTTON' ) , false );
		JToolBarHelper::divider();
		JToolbarHelper::publishList();
		JToolbarHelper::unpublishList();
		JToolBarHelper::divider();
		JToolbarHelper::addNew();
		JToolbarHelper::deleteList();
	}
}
