<?php
/**
 * @package		Komento
 * @copyright	Copyright (C) 2012 Stack Ideas Private Limited. All rights reserved.
 * @license		GNU/GPL, see LICENSE.php
 *
 * Komento is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * See COPYRIGHT.php for copyright notices and details.
 */

defined('_JEXEC') or die('Restricted access');

jimport('joomla.plugin.plugin');
jimport('joomla.filesystem.file');

/**
 * Komento System Plugin
 *
 */
class plgSystemKomento extends JPlugin
{
	private $extension		= null;

	/**
	 * com_sobipro
	 */
	public function ContentDisplayEntryView( &$text )
	{
		// Skip komento trying to display comments in adding new listing
		if( JRequest::getVar( 'task' ) == 'entry.add' )
		{
			return;
		}

		$article		= new stdClass;
		$article->id	= JRequest::getInt( 'sid' );
		$article->text	= &$text;
		$params			= new stdClass;

		$this->execute( __FUNCTION__, null, $article, $params, null );
	}

	public function AfterDisplayEntryView()
	{
		// Skip komento trying to display comments in adding new listing
		if( JRequest::getVar( 'task' ) == 'entry.add' )
		{
			return;
		}
		
		$article		= new stdClass;
		$article->id	= JRequest::getInt( 'sid' );
		$article->text	= '';
		$params			= new stdClass;

		$this->execute( __FUNCTION__, null, $article, $params, null );
	}

	private function execute( $eventTrigger, $context = '', &$article, &$params, $page = 0 )
	{
		static $bootstrap;

		// @task: load bootstrap
		if( !$bootstrap )
		{
			$file	= JPATH_ROOT . DIRECTORY_SEPARATOR . 'components' . DIRECTORY_SEPARATOR . 'com_komento' . DIRECTORY_SEPARATOR . 'bootstrap.php';

			jimport('joomla.filesystem.file');

			if( !JFile::exists( $file ) )
			{
				// missing bootstrap
				return false;
			}

			require_once( $file );
			$bootstrap = true;
		}

		if( !$this->extension )
		{
			$this->extension = JRequest::getCmd( 'option' );
		}

		// @task: trigger onAfterEventTriggered
		if( !$result = Komento::onAfterEventTriggered( __CLASS__, $eventTrigger, $this->extension, $context, $article, $params ) )
		{
			return false;
		}

		// @task: trigger onBeforeCommentify
		// if( !$result = Komento::loadApplication( $this->extension )->onBeforeCommentify( $eventTrigger, $context, $article, $params, $page ) )
		// {
		// 	return false;
		// }

		// Passing in the data
		$options			= array();
		$options['trigger']	= $eventTrigger;
		$options['context']	= $context;
		$options['params']	= $params;
		$options['page']	= $page;

		// Ready to Commentify!
		return Komento::commentify( $this->extension, $article, $options );
	}
}
