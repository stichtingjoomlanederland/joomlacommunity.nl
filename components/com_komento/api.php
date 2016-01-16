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

require_once( JPATH_ROOT . DIRECTORY_SEPARATOR . 'components' . DIRECTORY_SEPARATOR . 'com_komento' . DIRECTORY_SEPARATOR . 'bootstrap.php' );

class KomentoAPI
{
	public static function loadReadmore( $id, $component, $options = array() )
	{
		$commentsModel = Komento::getModel( 'comments' );
		$commentCount = $commentsModel->getCount( $component, $id );

		$application = Komento::loadApplication( $component );

		if( $application->load( $id ) === false )
		{
			$application = Komento::getErrorApplication( $component, $id );
		}

		$theme	= Komento::getTheme();
		$theme->set( 'commentCount'		, $commentCount );
		$theme->set( 'componentHelper'	, $application );
		$html	= $theme->fetch('comment/bar.php');

		return $html;
	}

	public static function loadComments( $id, $component, $options = array() )
	{
		$commentsModel = Komento::getModel( 'comments' );
		$comments = $commentsModel->getComments( $component, $id, $options );

		$commentsModel = Komento::getModel( 'comments' );
		$commentCount = $commentsModel->getCount( $component, $id );

		if( array_key_exists('raw', $options) )
		{
			return $comments;
		}

		// @task: load necessary css and javascript files.
		Komento::getHelper( 'Document' )->loadHeaders();

		$application = Komento::loadApplication( $component );
		if( $application->load( $id ) === false )
		{
			$application = Komento::getErrorApplication( $component, $id );
		}

		$theme	= Komento::getTheme();
		$theme->set( 'component', $component );
		$theme->set( 'cid', $id );
		$theme->set( 'comments', $comments );
		$theme->set( 'options', $options );
		$theme->set( 'componentHelper', $application );
		$theme->set( 'application', $application );
		$theme->set( 'commentCount', $commentCount );
		$contentLink = $application->getContentPermalink();

		$theme->set( 'contentLink', $contentLink );

		$html	= $theme->fetch('comment/box.php');

		/* [KOMENTO_POWERED_BY_LINK] */

		// free version powered by link append (for reference only)
		// $html	.= '<div style="text-align: center; padding: 20px 0;"><a href="http://stackideas.com">' . JText::_( 'COM_KOMENTO_POWERED_BY_KOMENTO' ) . '</a></div>';

		return $html;
	}

	public static function loadForm( $id, $component, $options = array() )
	{
		$theme	= Komento::getTheme();
		$html	= $theme->fetch( 'comment/form.php' );

		echo $html;
	}
}
