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

jimport('joomla.application.component.controller');

require_once DISCUSS_HELPERS . '/date.php';
require_once DISCUSS_HELPERS . '/input.php';

class EasyDiscussControllerVbulletin extends EasyDiscussController
{
	function save()
	{
		$mainframe	= JFactory::getApplication();
		$db = DiscussHelper::getDBO();

		// $driver    = JRequest::getVar( 'migrator_vBulletin_driver' );
		// $host      = JRequest::getVar( 'migrator_vBulletin_host' );
		// $user      = JRequest::getVar( 'migrator_vBulletin_user' );
		// $password  = JRequest::getVar( 'migrator_vBulletin_password' );
		// $database  = JRequest::getVar( 'migrator_vBulletin_name' );
		$prefix = JRequest::getVar( 'migrator_vBulletin_prefix', '' );

		// // Data validation
		// $data = array(
		// 		'driver' => $driver,
		// 		'host' => $host,
		// 		'user' => $user,
		// 		'password' => $password,
		// 		'database' => $database,
		// 		'prefix' => $prefix
		// 		);

		// $invalid = array();
		// foreach( $data as $key => $item )
		// {
		// 	if( empty( $item ) )
		// 	{
		// 		$invalid[] = $key;
		// 	}
		// }

		// if( !empty( $invalid ) )
		// {
		// 	$msg = implode( ', ', $invalid );

		// 	DiscussHelper::setMessageQueue( JText::sprintf( 'COM_EASYDISCUSS_VBULLETN_DB_MISSING_DATA' , $msg ) , DISCUSS_QUEUE_ERROR );
		// 	$mainframe->redirect( 'index.php?option=com_easydiscuss&view=migrators&layout=default_vbulletin' );
		// 	$mainframe->close();
		// }

		// //Test connection
		// jimport('joomla.database.database');
		// jimport( 'joomla.database.table' );

		// // Prepare the data to be connect
		// $options	= array( 'driver' => $driver, 'host' => $host, 'user' => $user, 'password' => $password, 'database' => $database, 'prefix' => $prefix );

		// // Store it as static so that can be used else where
		// $connect = DiscussHelper::getHelper( 'DB' )->setVBConnection( $options );

		// if ( JError::isError($connect) ) {
		// 	header('HTTP/1.1 500 Internal Server Error');
		// 	jexit('Database Error: ' . $connect->toString() );
		// }

		// if ($connect->getErrorNum() > 0) {
		// 	// JError::raiseError(500 , 'JDatabase::getInstance: Could not connect to database <br />' . 'joomla.library:'.$connect->getErrorNum().' - '.$connect->getErrorMsg() );
		// 	DiscussHelper::setMessageQueue( JText::_( 'COM_EASYDISCUSS_VBULLETN_COULD_NOT_CONNECT_DB' ) , DISCUSS_QUEUE_ERROR );
		// 	$mainframe->redirect( 'index.php?option=com_easydiscuss&view=migrators&layout=default_vbulletin' );
		// 	$mainframe->close();
		// }

		if( empty( $prefix ) )
		{
			DiscussHelper::setMessageQueue( JText::sprintf( 'COM_EASYDISCUSS_VBULLETN_DB_PREFIX_NOT_FOUND' , $prefix ) , DISCUSS_QUEUE_ERROR );
			$mainframe->redirect( 'index.php?option=com_easydiscuss&view=migrators&layout=default_vbulletin' );
			$mainframe->close();
		}

		// // Check if the vBulletin table exist
		// $query = 'SELECT * FROM ' . $db->nameQuote( $prefix . 'thread' );
		// $db->setQuery( $query );
		// $results = $db->loadobject();

		// if( empty( $results ) )
		// {
		// 	DiscussHelper::setMessageQueue( JText::_( 'COM_EASYDISCUSS_VBULLETN_DB_TABLE_NOT_FOUND' ) , DISCUSS_QUEUE_ERROR );
		// 	$mainframe->redirect( 'index.php?option=com_easydiscuss&view=migrators&layout=default_vbulletin' );
		// 	$mainframe->close();
		// }

		// Check if the vBulletin table exist
		$tables = $db->getTableList();
		$exist = in_array( $prefix . 'thread', $tables );

		if( empty( $exist ) )
		{
			DiscussHelper::setMessageQueue( JText::_( 'COM_EASYDISCUSS_VBULLETN_DB_TABLE_NOT_FOUND' ) , DISCUSS_QUEUE_ERROR );
			$mainframe->redirect( 'index.php?option=com_easydiscuss&view=migrators&layout=default_vbulletin' );
			$mainframe->close();
		}

		// Save into the configuration file so that it can be use globally
		$model = DiscussHelper::getModel( 'Settings', true );

		// $data['migrator_vBulletin_driver'] = $driver;
		// $data['migrator_vBulletin_host'] = $host;
		// $data['migrator_vBulletin_user'] = $user;
		// $data['migrator_vBulletin_password'] = $password;
		// $data['migrator_vBulletin_name'] = $database;

		$data['migrator_vBulletin_prefix'] = $prefix;
		$model->save( $data );

		$mainframe->redirect( 'index.php?option=com_easydiscuss&view=migrators&layout=default_vBulletin_1' );
	}
}
