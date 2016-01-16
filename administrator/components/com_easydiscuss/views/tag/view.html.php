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

class EasyDiscussViewTag extends EasyDiscussAdminView
{
	public $tag	= null;

	public function display($tpl = null)
	{
		// Initialise variables
		$mainframe	= JFactory::getApplication();

		$tagId		= JRequest::getVar( 'tagid' , '' );

		$tag		= JTable::getInstance( 'Tags' , 'Discuss' );

		$tag->load( $tagId );

		$tag->title	= JString::trim($tag->title);
		$tag->alias	= JString::trim($tag->alias);

		$this->tag	= $tag;

		// Generate All tags for merging selections
		$tagsModel	= $this->getModel( 'Tags' );
		$tags		= $tagsModel->getData(false);
		$tagList	= array();
		array_push($tagList, JHTML::_('select.option', 0, 'Select tag', 'value', 'text', false));

		if( !empty($tags) )
		{
			foreach ($tags as $item)
			{
				if( $item->id != $tagId )
				{
					$tagList[] = JHtml::_('select.option', $item->id, $item->title );
				}
			}
		}

		// Set default values for new entries.
		if( empty( $tag->created ) )
		{
			$date			= DiscussHelper::getDate();
			$date->setOffSet( $mainframe->getCfg('offset') );

			$tag->created	= $date->toFormat();
			$tag->published	= true;
		}

		$this->assignRef( 'tag'		, $tag );
		$this->assignRef( 'tagList'	, $tagList );

		parent::display($tpl);
	}

	public function registerToolbar()
	{
		if( $this->tag->id != 0 )
		{
			JToolBarHelper::title( JText::_( 'COM_EASYDISCUSS_EDITING_TAG' ), 'tags' );
		}
		else
		{
			JToolBarHelper::title( JText::_( 'COM_EASYDISCUSS_ADD_NEW_TAG' ), 'tags' );
		}

		JToolBarHelper::back( JText::_( 'COM_EASYDISCUSS_BACK' ) , 'index.php?option=com_easydiscuss&view=tags' );
		JToolBarHelper::divider();
		JToolBarHelper::custom( 'save','save.png','save_f2.png', JText::_( 'COM_EASYDISCUSS_SAVE_BUTTON' ) , false);
		JToolBarHelper::custom( 'savePublishNew','save.png','save_f2.png', JText::_( 'COM_EASYDISCUSS_SAVE_AND_NEW' ) , false);
		JToolBarHelper::divider();
		JToolBarHelper::cancel();
	}
}
