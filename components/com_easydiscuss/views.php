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

defined('_JEXEC') or die();

jimport( 'joomla.application.component.view');

if( DiscussHelper::getJoomlaVersion() >= '3.0' )
{
	class EasyDiscussParentView extends JViewLegacy
	{

	}
}
else
{
	class EasyDiscussParentView extends JView
	{

	}
}


class EasyDiscussView extends EasyDiscussParentView
{
	function setPathway( $name , $link = '' )
	{
		JFactory::getLanguage()->load( 'com_easydiscuss' , JPATH_ROOT );
		$mainframe	= JFactory::getApplication();
		$pathway	= $mainframe->getPathway();
		return $pathway->addItem( $name , $link );
	}

	public function json_encode( $data )
	{
		include_once DISCUSS_CLASSES . '/json.php';
		$json	= new Services_JSON();

		return $json->encode( $data );
	}

	public function json_decode( $data )
	{
		include_once DISCUSS_CLASSES . '/json.php';
		$json	= new Services_JSON();

		return $json->decode( $data );
	}

	public function getModel( $name = null )
	{
		static $model = array();

		if( !isset( $model[ $name ] ) )
		{
			$file	= JString::strtolower( $name );

			$path	= DISCUSS_MODELS . '/' . $file . '.php';

			jimport('joomla.filesystem.path');
			if ( JFolder::exists( $path ))
			{
				JError::raiseWarning( 0, 'Model file not found.' );
			}

			$modelClass		= 'EasyDiscussModel' . ucfirst( $name );

			if( !class_exists( $modelClass ) )
				require_once( $path );


			$model[ $name ] = new $modelClass();
		}

		return $model[ $name ];
	}

	function getView( $name , $tmpl = 'html')
	{
		static $view = array();

		if( !isset( $view[ $name ] ) )
		{
			$file	= JString::strtolower( $name );

			$path	= DISCUSS_ROOT . '/views/' . $file . '/view.'. $tmpl . '.php';

			jimport('joomla.filesystem.path');
			if ( JFolder::exists( $path ))
			{
				JError::raiseWarning( 0, 'View file not found.' );
			}

			$viewClass		= 'EasyDiscussView' . ucfirst( $name );

			if( !class_exists( $viewClass ) )
				require_once( $path );


			$view[ $name ] = new $viewClass();
		}

		return $view[ $name ];
	}

	public function logView()
	{
		$my		= JFactory::getUser();

		if( $my->id > 0 )
		{
			$db 		= DiscussHelper::getDBO();
			$query 		= 'SELECT `id` FROM ' . $db->nameQuote( '#__discuss_views' );
			$query 		.= ' WHERE ' . $db->nameQuote( 'user_id' ) . '=' . $db->Quote( $my->id );

			$db->setQuery( $query );
			$id		= $db->loadResult();

			$hash 		= md5( JRequest::getURI() );
			if( !$id )
			{
				// Create a new log view
				$view 	= DiscussHelper::getTable( 'Views' );
				$view->updateView( $my->id , $hash );
			}
			else
			{
				$query 	= 'UPDATE ' . $db->nameQuote( '#__discuss_views' );
				$query 	.= ' SET ' . $db->nameQuote( 'hash' ) . '=' . $db->Quote( $hash );
				$query	.= ', ' . $db->nameQuote( 'created' ) . '=' . $db->Quote( DiscussHelper::getDate()->toMySQL() );
				$query	.= ', ' . $db->nameQuote( 'ip' ) . '=' . $db->Quote( $_SERVER[ 'REMOTE_ADDR' ] );
				$query  .= ' WHERE ' . $db->nameQuote( 'id' ) . '=' . $db->Quote( $id );

				$db->setQuery( $query );
				$db->query();
			}

		}
	}
}
