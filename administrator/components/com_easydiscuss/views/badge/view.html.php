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

class EasyDiscussViewBadge extends EasyDiscussAdminView
{
	public function display($tpl = null)
	{
		$id		= JRequest::getInt( 'id' , 0 );

		$badge	= DiscussHelper::getTable( 'Badges' );
		$badge->load( $id );

		if( !$badge->created )
		{
			$date = DiscussHelper::getHelper( 'Date' )->dateWithOffset( DiscussHelper::getDate()->toMySQL() );
			$badge->created	= $date->toMySQL();
		}

		// There could be some errors here.
		if( JRequest::getMethod() == 'POST' )
		{
			$badge->bind( JRequest::get( 'post' ) );

			// Description might contain html codes
			$description 			= JRequest::getVar( 'description' , '' , 'post' , 'string' , JREQUEST_ALLOWRAW );
			$badge->description 	= $description;
		}

		$jConfig 			= DiscussHelper::getJConfig();
		$editor				= JFactory::getEditor( $jConfig->get( 'editor' ) );

		$model	= $this->getModel( 'Badges' );
		$rules	= $model->getRules();
		$badges	= $this->getBadges();

		$this->assign( 'editor'	, $editor );
		$this->assign( 'badges'	, $badges );
		$this->assign( 'rules'	, $rules );
		$this->assign( 'badge'	, $badge );

		parent::display($tpl);
	}

	public function getBadges()
	{
		$exclude = array('.svn', 'CVS', '.DS_Store', '__MACOSX', 'index.html');
		$badges	= JFolder::files(DISCUSS_BADGES_PATH, '.', false, false, $exclude);

		return $badges;
	}

	public function registerToolbar()
	{
		JToolBarHelper::title( JText::_( 'COM_EASYDISCUSS_BADGES' ), 'badges' );

		JToolBarHelper::back( JText::_( 'COM_EASYDISCUSS_BACK' ) , 'index.php?option=com_easydiscuss&view=badges' );
		JToolBarHelper::divider();
		JToolBarHelper::custom( 'save','save.png','save_f2.png', JText::_( 'COM_EASYDISCUSS_SAVE_BUTTON' ) , false);
		JToolBarHelper::custom( 'savePublishNew','save.png','save_f2.png', JText::_( 'COM_EASYDISCUSS_SAVE_NEW_BUTTON' ) , false);
		JToolBarHelper::cancel();
	}
}
