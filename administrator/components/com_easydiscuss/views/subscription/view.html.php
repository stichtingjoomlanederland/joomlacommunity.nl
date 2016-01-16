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

class EasyDiscussViewSubscription extends EasyDiscussAdminView
{
	public function display($tpl = null)
	{
		// @rule: Test for user access if on 1.6 and above
		if( DiscussHelper::getJoomlaVersion() >= '1.6' )
		{
			if(!JFactory::getUser()->authorise('discuss.manage.subscriptions' , 'com_easydiscuss') )
			{
				JFactory::getApplication()->redirect( 'index.php' , JText::_( 'JERROR_ALERTNOAUTHOR' ) , 'error' );
				JFactory::getApplication()->close();
			}
		}
		// Initialise variables
		$mainframe		= JFactory::getApplication();

		$filter			= $mainframe->getUserStateFromRequest( 'com_easydiscuss.subscription.filter', 	'filter', 	'site', 'word' );
		$search			= $mainframe->getUserStateFromRequest( 'com_easydiscuss.subscription.search', 	'search', 		'', 'string' );

		$search			= trim(JString::strtolower( $search ) );
		$order			= $mainframe->getUserStateFromRequest( 'com_easydiscuss.subscription.filter_order', 		'filter_order', 'fullname', 'cmd' );
		$orderDirection	= $mainframe->getUserStateFromRequest( 'com_easydiscuss.subscription.filter_order_Dir','filter_order_Dir',		'', 'word' );


		$model			= $this->getModel('Subscribe');
		$subscriptions	= $model->getSubscription();

		$pagination		= $model->getPagination();
		$this->assignRef( 'subscriptions' 	, $subscriptions );
		$this->assignRef( 'pagination'		, $pagination );

		$this->assign( 'filter'			, $filter );
		$this->assign( 'filterList'		, $this->_getFilter($filter) );
		$this->assign( 'search'			, $search );
		$this->assign( 'order'			, $order );
		$this->assign( 'orderDirection'	, $orderDirection );

		parent::display($tpl);
	}

	private function _getFilter( $filter )
	{
		$filterType = array();
		$attribs	= 'size="1"  onchange="submitform();"';

		$filterType[] = JHTML::_( 'select.option', 'site', JText::_( 'COM_EASYDISCUSS_SITE_OPTION' ) );
		$filterType[] = JHTML::_( 'select.option', 'category', JText::_( 'COM_EASYDISCUSS_CATEGORY_OPTION' ) );
		$filterType[] = JHTML::_( 'select.option', 'post', JText::_( 'COM_EASYDISCUSS_POST_OPTION' ) );

		return JHTML::_('select.genericlist',   $filterType, 'filter', $attribs, 'value', 'text', $filter );
	}

	public function registerToolbar()
	{
		JToolBarHelper::title( JText::_( 'COM_EASYDISCUSS_SUBSCRIPTION' ), 'subscriptions' );

		JToolBarHelper::custom( 'home', 'arrow-left', '', JText::_( 'COM_EASYDISCUSS_TOOLBAR_HOME' ), false);
		JToolBarHelper::divider();
		JToolbarHelper::deleteList();
	}
}
