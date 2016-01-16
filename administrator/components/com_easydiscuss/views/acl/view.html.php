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

class EasyDiscussViewAcl extends EasyDiscussAdminView
{
	public function display($tpl = null)
	{
		$mainframe	= JFactory::getApplication();
		$model		= $this->getModel( 'Acl' );
		$document	= JFactory::getDocument();

		$cid	= JRequest::getVar('cid', null, 'REQUEST');
		$type	= JRequest::getVar('type', '', 'REQUEST');
		$add	= JRequest::getVar('add', '', 'REQUEST');

		if(( is_null($cid) || empty($type)) && empty($add))
		{
			$mainframe->redirect( 'index.php?option=com_easydiscuss&view=acls' , JText::_('Invalid Id or acl type. Please try again.') , 'error' );
		}

		$rulesets = $model->getRuleSet($type, $cid, $add);

		if ( $type == 'assigned' )
		{
			$document->setTitle( JText::_("COM_EASYDISCUSS_ACL_ASSIGN_USER") );
			JToolBarHelper::title( JText::_( 'COM_EASYDISCUSS_ACL_ASSIGN_USER' ), 'acl' );
		}
		else
		{
			$document->setTitle( JText::_("COM_EASYDISCUSS_ACL_JOOMLA_USER_GROUP") );
			JToolBarHelper::title( JText::_( 'COM_EASYDISCUSS_ACL_JOOMLA_USER_GROUP' ), 'acl' );
		}

		$joomlaVersion	= DiscussHelper::getJoomlaVersion();

		$this->assignRef( 'joomlaversion'	, $joomlaVersion );
		$this->assignRef( 'rulesets'		, $rulesets );
		$this->assignRef( 'type'			, $type );
		$this->assignRef( 'add'				, $add );

		parent::display($tpl);
	}

	public function getRuleDescription( $action )
	{
		$db		= DiscussHelper::getDBO();

		$query	= 'SELECT `description` FROM ' . $db->nameQuote( '#__discuss_acl' ) . ' '
				. 'WHERE `action`=' . $db->Quote( $action );

		$db->setQuery( $query );
		$description	= $db->loadResult();

		return $description;
	}

	public function registerToolbar()
	{
		JToolBarHelper::back( 'COM_EASYDISCUSS_BACK' , 'index.php?option=com_easydiscuss&view=acls' );
		JToolBarHelper::divider();
		JToolBarHelper::apply();
		JToolBarHelper::save();
		JToolBarHelper::divider();
		JToolBarHelper::cancel();
	}
}
