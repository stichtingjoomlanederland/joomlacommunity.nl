<?php
/**
 * @package		Komento
 * @copyright	Copyright (C) 2012 Stack Ideas Private Limited. All rights reserved.
 * @license		GNU/GPL, see LICENSE.php
 * Komento is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * See COPYRIGHT.php for copyright notices and details.
 */

// No direct access
defined('_JEXEC') or die('Restricted access');

require_once( KOMENTO_CONTROLLERS . DIRECTORY_SEPARATOR . 'parent.php' );

require_once( KOMENTO_HELPERS . DIRECTORY_SEPARATOR . 'helper.php' );

class KomentoController extends KomentoParentController
{
	/**
	 * Constructor
	 *
	 * @since 0.1
	 */
	function __construct($config = array())
	{
		// Load necessary css and javascript files.
		KomentoDocumentHelper::loadHeaders();

		// By default, we use the tables specified at the back end.
		JTable::addIncludePath( JPATH_ROOT . DIRECTORY_SEPARATOR . 'administrator' . DIRECTORY_SEPARATOR . 'components' . DIRECTORY_SEPARATOR . 'com_komento' . DIRECTORY_SEPARATOR . 'tables');

		//load the content plugins so that the content trigger will work.
		JPluginHelper::importPlugin('content');

		parent::__construct($config);
	}

	function display($cachable = false, $urlparams = false)
	{
		$document = JFactory::getDocument();

		// $viewLayout	= JRequest::getCmd('layout', 'default' );
		// $format		= JRequest::getCmd('format', 'html');
		// $tmpl		= JRequest::getCmd('tmpl', 'html');

		$viewType	= JRequest::getCmd('format', $document->getType());
		$viewLayout	= JRequest::getCmd('layout', 'default');

		$viewName = JRequest::getCmd('view', $this->getName());

		// catch all possible valid view names here
		switch( $viewName )
		{
			case 'rss':
				$viewType = 'feed';
				break;
			case 'profile':
				break;
			default:
				// always redirect back to root for all other option=com_komento links
				$mainframe	= JFactory::getApplication();
				$mainframe->redirect( JURI::root() );
				break;
		}

		$view = $this->getView( $viewName, $viewType, '' );
		$view->display();
	}

	/**
	 * Overrides parent method
	 **/
	public static function getInstance( $controllerName, $config = array() )
	{
		static $instances;

		if( !$instances )
		{
			$instances	= array();
		}

		// Set the controller name
		$className	= 'KomentoController' . ucfirst( $controllerName );

		if( !isset( $instances[ $className ] ) )
		{
			if( !class_exists( $className ) )
			{
				jimport( 'joomla.filesystem.file' );
				$controllerFile	= KOMENTO_CONTROLLERS . DIRECTORY_SEPARATOR . JString::strtolower( $controllerName ) . '.php';

				if( JFile::exists( $controllerFile ) )
				{
					require_once( $controllerFile );

					if( !class_exists( $className ) )
					{
						// Controller does not exists, throw some error.
						JError::raiseError( '500' , JText::sprintf('Controller %1$s not found' , $className ) );
					}
				}
				else
				{
					// File does not exists, throw some error.
					JError::raiseError( '500' , JText::sprintf('Controller %1$s.php not found' , $controllerName ) );
				}
			}

			$instances[ $className ]	= new $className($config);
		}
		return $instances[ $className ];
	}

	// process all custom task here

	public function cron()
	{
		$total = JRequest::getInt( 'total', 5 );
		$mailq	= Komento::getMailQueue();
		$mailq->send( $total );
		echo JText::_( 'COM_KOMENTO_EMAIL_BATCH_PROCESS_FINISHED' );
		exit;
	}

	public function clearCaptcha()
	{
		$days = JRequest::getInt( 'days', 7 );
		$result = Komento::clearCaptcha( $days );
		echo JText::_( 'COM_KOMENTO_CLEAR_CAPTCHA_PROCESS_FINISHED' );
		exit;
	}

	public function comfirmSubscription()
	{
		$id = JRequest::getInt( 'id', '' );
		$subscribe = Komento::getModel( 'subscription' );
		if( $subscribe->confirmSubscription( $id ) )
		{
			echo JText::_( 'COM_KOMENTO_FORM_NOTIFICATION_SUBSCRIBED' );
		}
		else
		{
			echo JText::_( 'COM_KOMENTO_FORM_NOTIFICATION_SUBSCRIBE_ERROR' );
		}
		exit;
	}

	public function unsubscribe()
	{
		$id = JRequest::getInt( 'id', '' );
		$table = Komento::getTable( 'subscription' );

		if( empty( $id ) || !$table->load( $id ) || !$table->delete() )
		{
			echo JText::_( 'COM_KOMENTO_FORM_NOTIFICATION_UNSUBSCRIBED_ERROR' );
			exit;
		}

		echo JText::_( 'COM_KOMENTO_FORM_NOTIFICATION_UNSUBSCRIBED_SUCCESSFULLY' );
		exit;
	}

	public function approveComment()
	{
		$token = JRequest::getVar( 'token', '' );

		if( empty( $token ) )
		{
			echo JText::_( 'COM_KOMENTO_INVALID_TOKEN' );
			exit;
		}

		$hashkeys = Komento::getTable( 'hashkeys' );

		if( !$hashkeys->loadByKey( $token ) )
		{
			echo JText::_( 'COM_KOMENTO_INVALID_TOKEN' );
			exit;
		}

		if( empty( $hashkeys->uid ) )
		{
			echo JText::_( 'COM_KOMENTO_APPROVE_COMMENT_INVALID_COMMENT_ID' );
			exit;
		}

		$comment = Komento::getComment( $hashkeys->uid );

		// If this hashkey has been used, then either redirect to the comment or throw error
		if( $hashkeys->state )
		{
			if( $comment->published != 1 )
			{
				echo JText::_( 'COM_KOMENTO_APPROVE_COMMENT_ADMIN_REMOVED_COMMENT' );
				exit;
			}

		}
		else
		{
			$state = $comment->publish();

			if( !$state )
			{
				$error = $comment->getError();

				if( empty( $error ) )
				{
					$error = JText::_( 'COM_KOMENTO_APPROVE_COMMENT_ERROR_OCCURED' );
				}

				echo $error;
				exit;
			}

			$hashkeys->state = 1;
			$hashkeys->store();
		}

		$mainframe = JFactory::getApplication();
		$mainframe->redirect( $comment->getPermalink() );

		return;
	}

	// clears captcha dated 7 days old.
	public function clear()
	{
		$db = Komento::getDBO();

		$query = 'SELECT * FROM ' . $db->nameQuote( '#__komento_captcha' ) . ' WHERE ' . $db->nameQuote( 'created' ) . ' <= DATE_SUB(NOW(), INTERVAL 7 DAY)';

		$db->setQuery( $query );
		$db->query();
	}
}
