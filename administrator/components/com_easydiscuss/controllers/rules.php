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

require_once DISCUSS_HELPERS . '/date.php';
require_once DISCUSS_HELPERS . '/input.php';
require_once DISCUSS_HELPERS . '/filter.php';

class EasyDiscussControllerRules extends EasyDiscussController
{
	public function remove()
	{
		// Request forgeries check
		JRequest::checkToken() or die( 'Invalid Token' );

		$app	= JFactory::getApplication();
		$ids	= JRequest::getVar( 'cid' );

		// @task: Sanitize the id's to integer.
		foreach( $ids as $id )
		{
			$id		= (int) $id;

			$rule	= DiscussHelper::getTable( 'BadgesRules' );
			$rule->load( $id );
			$rule->delete();
		}

		DiscussHelper::setMessageQueue( JText::_( 'COM_EASYDISCUSS_RULE_IS_NOW_DELETED' ) , DISCUSS_QUEUE_SUCCESS );
		$app->redirect( 'index.php?option=com_easydiscuss&view=rules' );
	}

	public function newrule()
	{
		$this->setRedirect( 'index.php?option=com_easydiscuss&view=rules&layout=install&from=rules' );
		return;
	}

	public function install()
	{
		// Request forgeries check
		JRequest::checkToken() or die( 'Invalid Token' );

		$file	= JRequest::getVar( 'rule' , '' , 'FILES' );
		$app	= JFactory::getApplication();
		$files	= array();

		// @task: If there's no tmp_name in the $file, we assume that the data sent is corrupted.
		if( !isset( $file[ 'tmp_name' ] ) )
		{
			DiscussHelper::setMessageQueue( JText::_( 'COM_EASYDISCUSS_INVALID_RULE_FILE' ) , DISCUSS_QUEUE_ERROR );
			$app->redirect( 'index.php?option=com_easydiscuss&view=rules&layout=install' );
			$app->close();
		}

		// There are various MIME type for compressed file. So let's check the file extension instead.
		if( $file['name'] && JFile::getExt($file['name']) == 'xml' )
		{
			$files	= array( $file['tmp_name'] );
		}
		else
		{
			$jConfig	= DiscussHelper::getJConfig();
			$path		= rtrim( $jConfig->get( 'tmp_path' ) , '/' ) . '/' . $file['name'];

			// @rule: Copy zip file to temporary location
			if( !JFile::copy( $file[ 'tmp_name' ] , $path ) )
			{
				DiscussHelper::setMessageQueue( JText::_( 'COM_EASYDISCUSS_INVALID_RULE_FILE' ) , DISCUSS_QUEUE_ERROR );
				$app->redirect( 'index.php?option=com_easydiscuss&view=rules&layout=install' );
				$app->close();
			}

			jimport( 'joomla.filesystem.archive' );
			$tmp		= md5( DiscussHelper::getDate()->toMysQL() );
			$dest		= rtrim( $jConfig->get( 'tmp_path' ) , '/' ) . '/' . $tmp;

			if( !JArchive::extract( $path , $dest ) )
			{
				DiscussHelper::setMessageQueue( JText::_( 'COM_EASYDISCUSS_INVALID_RULE_FILE' ) , DISCUSS_QUEUE_ERROR );
				$app->redirect( 'index.php?option=com_easydiscuss&view=rules&layout=install' );
				$app->close();
			}

			$files		= JFolder::files( $dest , '.' , true , true );

			if( empty( $files ) )
			{
				// Try to do a level deeper in case the zip is on the outer.
				$folder	= JFolder::folders( $dest );

				if( !empty( $folder ) )
				{
					$files	= JFolder::files( $dest . '/' . $folder[0] , true );
					$dest	= $dest . '/' . $folder[0];
				}
			}

			if( empty( $files ) )
			{
				DiscussHelper::setMessageQueue( JText::_( 'COM_EASYDISCUSS_INVALID_RULE_FILE' ) , DISCUSS_QUEUE_ERROR );
				$app->redirect( 'index.php?option=com_easydiscuss&view=rules&layout=install');
				$app->close();
			}
		}

		if( empty($files) )
		{
			DiscussHelper::setMessageQueue( JText::_( 'COM_EASYDISCUSS_RULE_INSTALL_FAILED' ) , DISCUSS_QUEUE_ERROR );
			$app->redirect( 'index.php?option=com_easydiscuss&view=rules&layout=install' );
			$app->close();
		}

		foreach( $files as $file )
		{
			$this->installXML( $file );
		}

		DiscussHelper::setMessageQueue( JText::_( 'COM_EASYDISCUSS_RULE_INSTALL_SUCCESS' ) , DISCUSS_QUEUE_SUCCESS );

		$app->redirect( 'index.php?option=com_easydiscuss&view=rules&layout=install' );
		$app->close();
	}

	private function installXML( $path )
	{
		// @task: Try to read the temporary file.
		$contents	= JFile::read( $path );
		$parser 	= DiscussHelper::getHelper( 'XML' , $contents );


		$app 		= JFactory::getApplication();

		// @task: Test for appropriate manifest type
		if( $parser->getName() != 'easydiscuss' )
		{
			DiscussHelper::setMessageQueue( JText::_( 'COM_EASYDISCUSS_INVALID_RULE_FILE' ) , DISCUSS_QUEUE_ERROR );
			$app->redirect( 'index.php?option=com_easydiscuss&view=rules&layout=install' );
			$app->close();
		}

		// @task: Bind appropriate values from the xml file into the database table.
		$rule		= DiscussHelper::getTable( 'Rules' );

		$rule->command 		= (string) $parser->command;
		$rule->title 		= (string) $parser->title;
		$rule->description	= (string) $parser->description;

		$rule->set( 'published' , 1 );
		$rule->set( 'created'	, DiscussHelper::getDate()->toMySQL() );


		if( $rule->exists( $rule->command ) )
		{
			return;
		}

		return $rule->store();
	}
}
