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

jimport( 'joomla.application.component.view');

require_once( dirname( dirname( dirname( __FILE__ ) ) ) . '/views.php' );

class EasyDiscussViewCustomFields extends EasyDiscussAdminView
{
	public function getAdvanceOption()
	{
		$ajax 		= DiscussHelper::getHelper( 'Ajax' );
		$active		= JRequest::getVar( 'activeType' );
		$id			= JRequest::getInt( 'customId' );


		// $active IS BASED ON WHAT THE USER SELECTED IN THE SELECT LIST
		if( empty($active) )
		{
			$ajax->reject();
			return $ajax->send();
		}

		$field 	= DiscussHelper::getTable( 'CustomFields' );

		if( !empty($id) )
		{
			$field->load( $id );
		}

		$result = $field->getAdvanceOption( $active );

		$ajax->resolve( $result['addButton'], $result['html'], $result['count'] );
		return $ajax->send();
	}

	public function addFieldOption()
	{
		$ajax 		= DiscussHelper::getHelper( 'Ajax' );
		$addField	= JRequest::getVar( 'activeType' );
		$count		= JRequest::getInt( 'fieldCount' );

		if( !empty($id) )
		{
			//Get previous value
			$field 	= DiscussHelper::getTable( 'CustomFields' );
			$field->load( $id );
		}

		if( empty($addField) )
		{
			$ajax->reject();
			return $ajax->send();
		}

		switch( $addField )
		{
			case 'radiobtn':
				$fieldName = 'radioBtnValue[]';
			break;
			case 'checkbox':
				$fieldName = 'checkBoxValue[]';
			break;
			case 'selectlist':
				$fieldName = 'selectValue[]';
			break;
			case 'multiplelist':
				$fieldName = 'multipleValue[]';
			break;
			default:
			break;
		}

		// We need to add 1 from the previous count for the next item
		$count++;
		//We do not want add button for text and textarea
		if( $addField != 'text' || $addField != 'area' )
		{
			//New field
			$html 	= '<li class="remove'.$addField.'_'.$count.'">'
					. '<div class="span10 remove'.$addField.'_'.$count.'">'
					. '<div class="input-append remove'.$addField.'_'.$count.'">'
					. '<input type="text" class="input-full '.$addField.'" id="'.$addField.'_'.$count.'" name="'. $fieldName .'" value="" />'
					. '<button type="button" id="'.$count.'" class="btn btn-danger btn-customfield-remove" name="Remove" data-removetype="'.$addField.'"><i class="icon-remove"></i></button>'
					. '</div>'
					. '</div>'
					. '</li>';
		}

		$ajax->resolve( $html, $count );
		return $ajax->send();
	}
}
