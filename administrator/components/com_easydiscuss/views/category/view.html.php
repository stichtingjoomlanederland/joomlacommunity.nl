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

class EasyDiscussViewCategory extends EasyDiscussAdminView
{
	public $cat	= null;

	public function display($tpl = null)
	{
		// Initialise variables
		$config = DiscussHelper::getConfig();

		$catId		= JRequest::getVar( 'catid' , '' );

		$cat		= JTable::getInstance( 'Category' , 'Discuss' );
		$cat->load( $catId );

		$this->cat	= $cat;

		$this->addPathway( 'Home' , 'index.php?option=com_easydiscuss' );
		$this->addPathway( JText::_( 'Categories' ) , 'index.php?option=com_easydiscuss&view=categories' );

		if( $catId )
		{
			$this->addPathway( 'Edit Category' );
		}
		else
		{
			$this->addPathway( 'New Category' );
		}

		// Set default values for new entries.
		if( empty( $cat->created ) )
		{
			$date	= DiscussDateHelper::getDate();
			$now	= DiscussDateHelper::toFormat($date);

			$cat->created	= $now;
			$cat->published	= true;
		}


		$catRuleItems		= JTable::getInstance( 'CategoryAclItem' , 'Discuss' );
		$categoryRules		= $catRuleItems->getAllRuleItems();

		$assignedGroupACL	= $cat->getAssignedACL( 'group' );
		$assignedUserACL	= $cat->getAssignedACL( 'user' );

		$assignedGroupMod	= $cat->getAssignedModerator( 'group' );
		$assignedUserMod	= $cat->getAssignedModerator( 'user' );

		$joomlaGroups		= DiscussHelper::getJoomlaUserGroups();

		$parentList = DiscussHelper::populateCategories('', '', 'select', 'parent_id', $cat->parent_id);

		$jConfig 			= DiscussHelper::getJConfig();
		$editor				= JFactory::getEditor( $jConfig->get( 'editor' ) );

		$this->assignRef( 'editor'				, $editor );
		$this->assignRef( 'cat'					, $cat );
		$this->assignRef( 'config'				, $config );
		$this->assignRef( 'acl'					, $acl );
		$this->assignRef( 'parentList'			, $parentList );
		$this->assignRef( 'categoryRules'		, $categoryRules );
		$this->assignRef( 'assignedGroupACL'	, $assignedGroupACL );
		$this->assignRef( 'assignedUserACL'		, $assignedUserACL );
		$this->assignRef( 'assignedGroupMod'	, $assignedGroupMod );
		$this->assignRef( 'assignedUserMod'		, $assignedUserMod );
		$this->assignRef( 'joomlaGroups'		, $joomlaGroups );


		parent::display($tpl);
	}

	public function registerToolbar()
	{
		if( $this->cat->id != 0 )
		{
			JToolBarHelper::title( JText::sprintf( 'COM_EASYDISCUSS_CATEGORIES_EDIT_CATEGORY_TITLE' , $this->cat->title ), 'category' );
		}
		else
		{
			JToolBarHelper::title( JText::_( 'COM_EASYDISCUSS_CATEGORIES_EDIT_ADD_CATEGORY_TITLE' ), 'category' );
		}

		JToolBarHelper::back( JText::_( 'COM_EASYDISCUSS_BACK' ) , 'index.php?option=com_easydiscuss&view=categories');
		JToolBarHelper::divider();
		JToolbarHelper::apply();
		JToolbarHelper::save();

		if( DiscussHelper::getJoomlaVersion() > '1.6' )
		{
			JToolBarHelper::save2new( 'savePublishNew' );
		}
		else
		{
			JToolBarHelper::save( 'savePublishNew' , JText::_( 'COM_EASYDISCUSS_SAVE_AND_NEW' ) );
		}
		JToolBarHelper::divider();
		JToolBarHelper::cancel();
	}
}
