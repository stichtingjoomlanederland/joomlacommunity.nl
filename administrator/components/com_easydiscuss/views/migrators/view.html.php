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

jimport( 'joomla.filesystem.file' );

class EasyDiscussViewMigrators extends EasyDiscussAdminView
{
	public function display( $tpl = null )
	{
		// @rule: Test for user access if on 1.6 and above
		if( DiscussHelper::getJoomlaVersion() >= '1.6' )
		{
			if(!JFactory::getUser()->authorise('discuss.manage.migrators' , 'com_easydiscuss') )
			{
				JFactory::getApplication()->redirect( 'index.php' , JText::_( 'JERROR_ALERTNOAUTHOR' ) , 'error' );
				JFactory::getApplication()->close();
			}
		}

		parent::display( $tpl );
	}

	public function communitypolls()
	{
		$this->set( 'installed' , $this->communityPollsExists() );

		parent::display( 'communitypolls' );
	}

	public function discussions()
	{
		parent::display('discussions');
	}

	public function discussionsExists()
	{
		$file = JPATH_ADMINISTRATOR . '/components/com_discussions/discussions.php';

		return JFile::exists($file);
	}

	public function communityPollsExists()
	{
		return JFile::exists( JPATH_ROOT . '/administrator/components/com_communitypolls/communitypolls.xml' );
	}

	public function kunenaExists()
	{

		return JFile::exists( JPATH_ROOT . '/components/com_kunena/kunena.php' );
	}

	public function jomsocialExists()
	{
		jimport( 'joomla.filesystem.file' );
		return JFile::exists( JPATH_ROOT . '/components/com_community/community.php' );
	}

	public function ninjaboardExists()
	{
		jimport( 'joomla.filesystem.file' );
		return JFile::exists( JPATH_ROOT . '/components/com_ninjaboard/ninjaboard.php' );
	}

	public function registerToolbar()
	{
		JToolBarHelper::title( JText::_( 'COM_EASYDISCUSS_MIGRATORS' ), 'migrators' );
		JToolBarHelper::back( 'COM_EASYDISCUSS_BACK' , 'index.php?option=com_easydiscuss');
	}
}
