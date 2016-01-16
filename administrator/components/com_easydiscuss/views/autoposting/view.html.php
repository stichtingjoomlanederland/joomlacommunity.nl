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

class EasyDiscussViewAutoposting extends EasyDiscussAdminView
{
	public function display($tpl = null)
	{
		// @rule: Test for user access if on 1.6 and above
		if( DiscussHelper::getJoomlaVersion() >= '1.6' )
		{
			if(!JFactory::getUser()->authorise('discuss.manage.autoposting' , 'com_easydiscuss') )
			{
				JFactory::getApplication()->redirect( 'index.php' , JText::_( 'JERROR_ALERTNOAUTHOR' ) , 'error' );
				JFactory::getApplication()->close();
			}
		}

		$config = DiscussHelper::getConfig();

		$this->assignRef( 'config' , $config );

		$layout = $this->getLayout();

		if( method_exists( $this , $layout ) )
		{
			$this->$layout( $tpl );
			return;
		}

		$facebookSetup	= $this->setuped( 'facebook' );
		$twitterSetup	= $this->setuped( 'twitter' );

		$this->assignRef( 'twitterSetup'	, $twitterSetup );
		$this->assignRef( 'facebookSetup'	, $facebookSetup );
		parent::display($tpl);
	}

	public function form( $tpl = null )
	{
		$type	= JRequest::getVar( 'type' );
		$config	= DiscussHelper::getConfig();

		$callback	= DiscussRouter::getRoutedURL( 'index.php?option=com_easydiscuss&view=autoposting' , false, true );
		$oauth	= DiscussHelper::getHelper( 'OAuth' )->getConsumer( 'facebook' , $config->get( 'main_autopost_facebook_id') , $config->get( 'main_autopost_facebook_secret') , $callback );

		$oauth	= DiscussHelper::getTable( 'OAuth' );
		if( $type == 'twitter' )
		{
			$oauth->loadByType( 'twitter' );
		}
		else
		{
			$oauth->loadByType( 'facebook' );
		}
		$associated	= (bool) $oauth->id;

		$this->assignRef( 'associated'	, $associated );
		$this->assignRef( 'config'		, $config );
		$this->assignRef( 'type'		, $type );

		parent::display($tpl);
	}

	public function setuped( $type )
	{
		$db		= DiscussHelper::getDBO();

		$query	= 'SELECT COUNT(1) FROM ' . $db->nameQuote( '#__discuss_oauth' ) . ' '
				. 'WHERE ' . $db->nameQuote( 'type' ) . '=' . $db->Quote( $type ) . ' '
				. 'AND ' . $db->nameQuote( 'access_token' ) . ' IS NOT NULL';
		$db->setQuery( $query );

		$exists	= $db->loadResult();

		return $exists > 0;
	}

	public function facebook( $tpl = null )
	{
		$step	= JRequest::getVar( 'step' );
		$config	= DiscussHelper::getConfig();

		$callback	= DiscussRouter::getRoutedURL( 'index.php?option=com_easydiscuss&view=autoposting' , false, true );
		$oauth	= DiscussHelper::getHelper( 'OAuth' )->getConsumer( 'facebook' , $config->get( 'main_autopost_facebook_id') , $config->get( 'main_autopost_facebook_secret') , $callback );

		$oauth	= DiscussHelper::getTable( 'OAuth' );
		$oauth->loadByType( 'facebook' );

		$associated	= (bool) $oauth->id;

		if( $step == '3' )
		{
			//this mean we completed the final steps. reset step to empty.
			$mainframe  =& JFactory::getApplication();
			$mainframe->redirect( 'index.php?option=com_easydiscuss&view=autoposting&layout=form&type=facebook');
			$mainframe->close();
		}

		$this->assignRef( 'associated' , $associated );
		$this->assignRef( 'config'	, $config );
		$this->assignRef( 'step'	, $step );

		parent::display($tpl);
	}


	public function twitter( $tpl = null )
	{
		$step	= JRequest::getVar( 'step' );
		$config	= DiscussHelper::getConfig();

		$callback	= DiscussRouter::getRoutedURL( 'index.php?option=com_easydiscuss&view=autoposting' , false, true );
		$oauth		= DiscussHelper::getHelper( 'OAuth' )->getConsumer( 'twitter' , $config->get( 'main_autopost_twitter_id') , $config->get( 'main_autopost_twitter_secret') , $callback );

		$oauth	= DiscussHelper::getTable( 'OAuth' );
		$oauth->loadByType( 'twitter' );

		$associated	= (bool) $oauth->id;

		if( $step == '3' )
		{
			//this mean we completed the final steps. reset step to empty.
			$mainframe  =& JFactory::getApplication();
			$mainframe->redirect( 'index.php?option=com_easydiscuss&view=autoposting&layout=form&type=twitter');
			$mainframe->close();
		}

		$this->assignRef( 'associated' , $associated );
		$this->assignRef( 'config'	, $config );
		$this->assignRef( 'step'	, $step );

		parent::display($tpl);
	}

	public function registerToolbar()
	{
		JToolBarHelper::title( JText::_( 'COM_EASYDISCUSS_AUTOPOST' ), 'autoposting' );

		if( $this->getLayout() == 'default' )
		{
			JToolBarHelper::custom( 'home', 'arrow-left', '', JText::_( 'COM_EASYDISCUSS_TOOLBAR_HOME' ), false);
		}
		else if( $this->getLayout() == 'form' )
		{
			JToolBarHelper::back( JText::_( 'COM_EASYDISCUSS_BACK' ) , 'index.php?option=com_easydiscuss&view=autoposting');
			JToolBarHelper::divider();
			JToolBarHelper::custom( 'save','save.png','save_f2.png', JText::_( 'COM_EASYDISCUSS_SAVE_BUTTON' ) , false);
			JToolBarHelper::cancel();
		}
		else
		{
			JToolBarHelper::back( JText::_( 'COM_EASYDISCUSS_BACK' ) , 'index.php?option=com_easydiscuss&view=autoposting');
		}
	}
}
