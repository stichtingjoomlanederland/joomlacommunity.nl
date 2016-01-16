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

class EasyDiscussViewLabels extends EasyDiscussAdminView
{

	public function display($tpl = null)
	{
		// @rule: Test for user access if on 1.6 and above
		if( DiscussHelper::getJoomlaVersion() >= '1.6' )
		{
			if(!JFactory::getUser()->authorise('discuss.manage.labels' , 'com_easydiscuss') )
			{
				JFactory::getApplication()->redirect( 'index.php' , JText::_( 'JERROR_ALERTNOAUTHOR' ) , 'error' );
				JFactory::getApplication()->close();
			}
		}
		$task	= JRequest::getCmd('task');
		if( $task == 'labels.edit')
		{
			$this->diplayLabel();
		}
		else
		{
			// Initialise variables
			$mainframe		= JFactory::getApplication();

			$filter_state	= $mainframe->getUserStateFromRequest( 'com_easydiscuss.labels.filter_state', 	'filter_state', 	'*', 'word' );
			$search			= $mainframe->getUserStateFromRequest( 'com_easydiscuss.labels.search', 		'search', 			'', 'string' );

			$search			= trim(JString::strtolower( $search ) );
			$order			= $mainframe->getUserStateFromRequest( 'com_easydiscuss.labels.filter_order', 		'filter_order', 	'a.ordering', 'cmd' );
			$orderDirection	= $mainframe->getUserStateFromRequest( 'com_easydiscuss.labels.filter_order_Dir',	'filter_order_Dir',	'asc', 'word' );

			// Get data from the model
			$labels			= $this->get( 'Data' );
			$pagination		= $this->get( 'Pagination' );
			$this->state		= $this->get('State');

			$this->assignRef( 'labels' 		, $labels );
			$this->assignRef( 'pagination'	, $pagination );

			$this->assign( 'state'			, JHTML::_('grid.state', $filter_state ) );
			$this->assign( 'search'			, $search );
			$this->assign( 'order'			, $order );
			$this->assign( 'orderDirection'	, $orderDirection );

			parent::display($tpl);
		}
	}

	public function diplayLabel()
	{
		// Initialise variables
		$mainframe	= JFactory::getApplication();

		$labelId	= JRequest::getVar( 'label_id' , '' );

		$label		= DiscussHelper::getTable( 'Label' );

		$label->load( $labelId );

		$label->title	= JString::trim($label->title);

		$this->label	= $label;

		// Set default values for new entries.
		if( empty( $label->created ) )
		{
			$date			= DiscussHelper::getDate();
			$date->setOffSet( $mainframe->getCfg('offset') );

			$label->created	= $date->toFormat();
			$label->published	= true;
		}

		$this->assignRef( 'label', $label );

		parent::display('edit');
	}

	public function registerToolbar()
	{
		$task	= JRequest::getCmd('task');
		if( $task == 'labels.edit')
		{
			if( $this->label->id != 0 )
			{
				JToolBarHelper::title( JText::_( 'COM_EASYDISCUSS_EDITING_LABEL' ), 'labels' );
			}
			else
			{
				JToolBarHelper::title( JText::_( 'COM_EASYDISCUSS_ADD_NEW_LABEL' ), 'labels' );
			}

			JToolBarHelper::back( JText::_( 'COM_EASYDISCUSS_BACK' ) , 'index.php?option=com_easydiscuss&view=labels' );
			JToolBarHelper::divider();
			JToolBarHelper::custom( 'save','save.png','save_f2.png', JText::_( 'COM_EASYDISCUSS_SAVE_BUTTON' ) , false);
			JToolBarHelper::custom( 'savePublishNew','save.png','save_f2.png', JText::_( 'COM_EASYDISCUSS_SAVE_AND_NEW' ) , false);
			JToolBarHelper::divider();
			JToolBarHelper::cancel();
		}
		else
		{
			JToolBarHelper::title( JText::_( 'COM_EASYDISCUSS_LABELS' ), 'labels' );

			JToolBarHelper::custom( 'home', 'arrow-left', '', JText::_( 'COM_EASYDISCUSS_TOOLBAR_HOME' ), false);
			JToolBarHelper::divider();
			JToolbarHelper::publishList();
			JToolbarHelper::unpublishList();
			JToolBarHelper::divider();
			JToolbarHelper::addNew('labels.edit');
			JToolbarHelper::deleteList();
		}
	}
}
