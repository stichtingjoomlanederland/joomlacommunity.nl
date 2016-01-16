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

class EasyDiscussViewAcls extends EasyDiscussAdminView
{
	public function display($tpl = null)
	{
		// @rule: Test for user access if on 1.6 and above
		if( DiscussHelper::getJoomlaVersion() >= '1.6' )
		{
			if(!JFactory::getUser()->authorise('discuss.manage.acls' , 'com_easydiscuss') )
			{
				JFactory::getApplication()->redirect( 'index.php' , JText::_( 'JERROR_ALERTNOAUTHOR' ) , 'error' );
				JFactory::getApplication()->close();
			}
		}

		// Initialise variables
		$document	= JFactory::getDocument();
		$mainframe	= JFactory::getApplication();
		$model		= $this->getModel( 'Acl' );
		$config		= DiscussHelper::getConfig();

		$type = $mainframe->getUserStateFromRequest( 'com_easydiscuss.acls.filter_type', 'filter_type', 'group', 'word' );

		// Filtering
		$filter = new stdClass();
		$filter->type 	= $this->getFilterType($type);
		$filter->search = $mainframe->getUserStateFromRequest( 'com_easydiscuss.acls.search', 'search', '', 'string' );

		// Sorting
		$sort = new stdClass();
		$sort->order			= $mainframe->getUserStateFromRequest( 'com_easydiscuss.acls.filter_order', 'filter_order', 'a.`id`', 'cmd' );
		$sort->orderDirection	= $mainframe->getUserStateFromRequest( 'com_easydiscuss.acls.filter_order_Dir', 'filter_order_Dir', '', 'word' );

		$rulesets	= $model->getRuleSets($type);
		$pagination	= $model->getPagination($type);

		if( $type == 'assigned' )
		{
			$document->setTitle( JText::_("COM_EASYDISCUSS_ACL_ASSIGN_USER") );
			JToolBarHelper::title( JText::_( 'COM_EASYDISCUSS_ACL_ASSIGN_USER' ), 'acl' );
		}
		else
		{
			$document->setTitle( JText::_("COM_EASYDISCUSS_ACL_JOOMLA_USER_GROUP") );
			JToolBarHelper::title( JText::_( 'COM_EASYDISCUSS_ACL_JOOMLA_USER_GROUP' ), 'acl' );
		}

		$this->assignRef( 'config', $config );
		$this->assignRef( 'rulesets', $rulesets );
		$this->assignRef( 'filter', $filter );
		$this->assignRef( 'sort', $sort );
		$this->assignRef( 'type', $type );
		$this->assignRef( 'pagination', $pagination );

		parent::display($tpl);
	}

	public function getFilterType( $filter_type='*', $group='COM_EASYDISCUSS_JOOMLA_GROUP', $assigned='COM_EASYDISCUSS_ASSIGNED' )
	{
		$filter[] = JHTML::_('select.option', '', '- '. JText::_( 'COM_EASYDISCUSS_ACL_SELECT_TYPE' ) .' -' );
		$filter[] = JHTML::_('select.option', 'group', JText::_( $group ) );
		$filter[] = JHTML::_('select.option', 'assigned', JText::_( $assigned ) );

		return JHTML::_('select.genericlist', $filter, 'filter_type', ' size="1" onchange="submitform( );"', 'value', 'text', $filter_type );
	}

	public function registerToolbar()
	{
		JToolBarHelper::custom( 'home', 'arrow-left', '', JText::_( 'COM_EASYDISCUSS_TOOLBAR_HOME' ), false);

		$mainframe	= JFactory::getApplication();
		$type		= $mainframe->getUserStateFromRequest( 'com_easydiscuss.acls.filter_type', 'filter_type', 'group', 'word' );

		if( $type=='assigned' )
		{
			JToolBarHelper::divider();
			JToolbarHelper::addNew();
			JToolbarHelper::deleteList();
		}
	}
}
