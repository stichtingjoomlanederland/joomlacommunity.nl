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

require_once( DISCUSS_ROOT . '/views.php' );

require_once DISCUSS_HELPERS . '/date.php';

class EasyDiscussViewFavourites extends EasyDiscussView
{
	function display($tpl = null)
	{
		$config 	= DiscussHelper::getConfig();
		$app 		= JFactory::getApplication();

		if( !$config->get( 'main_favorite' ) )
		{
			DiscussHelper::setMessageQueue( JText::_( 'COM_EASYDISCUSS_FEATURE_IS_DISABLED' ) , DISCUSS_QUEUE_ERROR );
			$app->redirect( DiscussRouter::_( 'index.php?option=com_easydiscuss' , false ) );
			$app->close();
		}

		DiscussHelper::setPageTitle( JText::_( 'COM_EASYDISCUSS_FAVOURITES_TITLE' ) );

		// @task: Add view
		$this->logView();

		DiscussHelper::setMeta();

		$postModel 	= DiscussHelper::getModel( 'Posts' );
		$posts		= $postModel->getData( true , 'latest' , null , 'favourites' );
		$posts		= DiscussHelper::formatPost($posts);

		$theme 	= new DiscussThemes();
		$theme->set( 'posts'	, $posts );

		echo $theme->fetch( 'favourites.php' );
	}
}
