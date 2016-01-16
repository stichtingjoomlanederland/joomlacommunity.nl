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

require_once( DISCUSS_ROOT . '/views.php' );
require_once( DISCUSS_HELPERS . '/url.php' );

class EasyDiscussViewProfile extends EasyDiscussView
{
	/**
	 * Displays the user's profile.
	 *
	 * @since	2.0
	 * @access	public
	 */
	function display( $tmpl = null )
	{
		$doc 		= JFactory::getDocument();
		$app 		= JFactory::getApplication();
		$id 		= JRequest::getInt( 'id' , null );
		$my 		= JFactory::getUser( $id );
		$config 	= DiscussHelper::getConfig();

		// Custom parameters.
		$sort = JRequest::getString('sort', 'latest');
		$filteractive = JRequest::getWord('filter', 'allposts');
		$viewType = JRequest::getWord('viewtype', 'questions');

		$profile		= DiscussHelper::getTable( 'Profile' );
		$profile->load( $my->id );

		// If profile is invalid, throw an error.
		if( !$profile->id )
		{
			// Show login form.
			$theme 	= new DiscussThemes();

			$theme->set( 'redirect' , DiscussRouter::_( 'index.php?option=com_easydiscuss&view=profile' , false ) );
			echo $theme->fetch( 'login.form.php' );

			return;
		}

		$params 	= DiscussHelper::getRegistry( $profile->params );
		$fields 	= array( 'facebook' , 'linkedin' , 'twitter', 'website' );

		foreach( $fields as $site )
		{
			if( $params->get( $site  , '' ) != '' )
			{
				if( $site == 'facebook' || $site == 'linkedin' || $site == 'twitter' )
				{
					$name	= $params->get( $site );
					$url 	= 'www.' . $site . '.com/' . $name;
					$params->set( $site , DiscussUrlHelper::clean( $url ) );
				}
				if( $site == 'website' )
				{
					$url	= $params->get( $site );
					$params->set( $site , DiscussUrlHelper::clean( $url ) );
				}
			}
		}

		// Set the title for the page.
		DiscussHelper::setPageTitle( JText::sprintf( 'COM_EASYDISCUSS_PROFILE_PAGE_TITLE' , $profile->getName() ) );

		// Set the pathway
		$this->setPathway( JText::_( $profile->getName() ) );

		$postsModel		= DiscussHelper::getModel( 'Posts' );
		$tagsModel 		= DiscussHelper::getModel( 'Tags' );

		$posts			= array();
		$replies		= array();
		$tagCloud		= array();
		$badges 		= array();
		$unresolved		= array();

		$pagination 	= null;
		$filterArr  	= array();
		$filterArr['viewtype'] 		= $viewType;
		$filterArr['id'] 			= $profile->id;

		switch( $viewType)
		{
			case 'replies':
				$replies	= $postsModel->getRepliesFromUser( $profile->id );
				$pagination	= $postsModel->getPagination();
				$pagination	= $pagination->getPagesLinks('profile', $filterArr, true);
				$replies	= DiscussHelper::formatPost( $replies );
				break;
			case 'unresolved':
				$unresolved	= $postsModel->getUnresolvedFromUser( $profile->id );
				$pagination	= $postsModel->getPagination();
				$pagination	= $pagination->getPagesLinks('profile', $filterArr, true);
				$unresolved	= DiscussHelper::formatPost( $unresolved );
				break;
			case 'questions':
			default:
				$posts		= $postsModel->getPostsBy( 'user' , $profile->id );
				$pagination	= $postsModel->getPagination();
				$pagination	= $pagination->getPagesLinks('profile', $filterArr, true);
				$posts		= DiscussHelper::formatPost( $posts );
				break;
		}

		// Get user badges
		$badges			= $profile->getBadges();

		// @rule: Clear up any notifications that are visible for the user.
		$notifications	= DiscussHelper::getModel( 'Notification' );
		$notifications->markRead( $profile->id , false , array( DISCUSS_NOTIFICATIONS_PROFILE , DISCUSS_NOTIFICATIONS_BADGE ) );

		$tpl		= new DiscussThemes();

		// EasyBlog integrations
		$easyblogExists		= $this->easyblogExists();
		$blogCount		 	= 0;

		if ($easyblogExists && $config->get('integrations_easyblog_profile')) {
			$bloggerModel = EB::model('Blogger');
			$blogCount = $bloggerModel->getTotalBlogCreated($profile->id);
		}

		$komentoExists		= $this->komentoExists();
		$commentCount		= 0;

		if ($komentoExists && $config->get('integrations_komento_profile')) {
			$commentsModel	= Komento::getModel('comments');
			$commentCount 	= $commentsModel->getTotalComment( $profile->id );
		}

		$posts = Discusshelper::getPostStatusAndTypes( $posts );

		$favPosts = $postsModel->getData( 'true', 'latest', 'null', 'favourites' );
		$favPosts = DiscussHelper::formatPost( $favPosts );

		$tpl->set( 'sort'			, $sort );
		$tpl->set( 'filter'			, $filteractive );
		$tpl->set( 'tagCloud'		, $tagCloud );
		$tpl->set( 'paginationType'	, DISCUSS_USERQUESTIONS_TYPE );
		$tpl->set( 'parent_id'		, $profile->id );
		$tpl->set( 'pagination'		, $pagination );
		$tpl->set( 'posts'			, $posts );
		$tpl->set( 'badges'			, $badges );
		$tpl->set( 'favPosts'		, $favPosts );
		$tpl->set( 'profile'		, $profile );
		$tpl->set( 'replies'		, $replies );
		$tpl->set( 'unresolved'		, $unresolved );
		$tpl->set( 'params'			, $params );
		$tpl->set( 'viewType'		, $viewType );
		$tpl->set( 'easyblogExists'	, $easyblogExists );
		$tpl->set( 'komentoExists'	, $komentoExists );
		$tpl->set( 'blogCount'		, $blogCount );
		$tpl->set( 'commentCount'	, $commentCount );

		$filterArr  = array();
		$filterArr['filter']	= $filteractive;
		$filterArr['id']		= $profile->id;
		$filterArr['sort']		= $sort;
		$filterArr['viewtype']	= $viewType;

		$tpl->set( 'filterArr'		, $filterArr );
		$tpl->set( 'page'			, 'profile' );

		echo $tpl->fetch( 'profile.php' );
	}

	public function easyblogExists()
	{
		$helperFile 	= JPATH_ROOT . '/components/com_easyblog/helpers/helper.php';
		$exists 		= JFile::exists( $helperFile );

		if( $exists )
		{
			require_once( $helperFile );

			return true;
		}
		return false;
	}

	public function komentoExists()
	{
		$helperFile 	= JPATH_ROOT . '/components/com_komento/helpers/helper.php';
		$exists 		= JFile::exists( $helperFile );

		if( $exists )
		{
			require_once( $helperFile );

			return true;
		}
		return false;
	}

	/**
	 * Displays the user editing form
	 *
	 * @since	1.0
	 * @access	public
	 * @return
	 */
	public function edit( $tmpl = null )
	{
		require_once DISCUSS_HELPERS . '/integrate.php';

		$document	= JFactory::getDocument();
		$mainframe	= JFactory::getApplication();
		$user		= JFactory::getUser();
		$config		= DiscussHelper::getConfig();

		if(empty($user->id))
		{
			$mainframe->enqueueMessage(JText::_('COM_EASYDISCUSS_YOU_MUST_LOGIN_FIRST'), 'error');
			$mainframe->redirect(DiscussRouter::_('index.php?option=com_easydiscuss&view=index'));
			return false;
		}

		$this->setPathway( JText::_('COM_EASYDISCUSS_PROFILE') , DiscussRouter::_( 'index.php?option=com_easydiscuss&view=profile&id=' . $user->id ) );
		$this->setPathway( JText::_('COM_EASYDISCUSS_EDIT_PROFILE') );

		//load porfile info and auto save into table if user is not already exist in discuss's user table.
		$profile = DiscussHelper::getTable( 'Profile' );
		$profile->load($user->id);

		$userparams	= DiscussHelper::getRegistry($profile->get('params'));
		$siteDetails = DiscussHelper::getRegistry($profile->get('site'));
		$maxSize	= ini_get( 'upload_max_filesize' );

		$configMaxSize  = $config->get( 'main_upload_maxsize', 0 );
		if( $configMaxSize > 0 )
		{
			// Backend settings is MB
			$configMaxSize = $configMaxSize * 1024 * 1204;

			// We convert to bytes because the function is accepting bytes
			$configMaxSize  = DiscussHelper::getHelper( 'String' )->bytesToSize($configMaxSize);
		}

		$avatar_config_path = $config->get('main_avatarpath');
		$avatar_config_path = rtrim($avatar_config_path, '/');
		$avatar_config_path = JString::str_ireplace('/', DIRECTORY_SEPARATOR, $avatar_config_path);

		$croppable 				= false;
		$allowJFBCAvatarEdit    = false;

		if( $config->get( 'layout_avatarIntegration') == 'default' )
		{
			$original 	= JPATH_ROOT . '/' . rtrim( $config->get( 'main_avatarpath' ) , '/' ) . '/' . 'original_' . $profile->avatar;

			if( JFile::exists( $original ) )
			{
				$size 		= getimagesize( $original );

				$width 		= $size[0];
				$height 	= $size[1];

				$configAvatarWidth = $config->get('layout_avatarwidth', 160);
				$configAvatarHeight = $config->get('layout_avatarheight', 160);

				if( $width >= $configAvatarWidth && $height >= $configAvatarHeight ) {
					$croppable = true;
				}
			}
		} else if( $config->get( 'layout_avatarIntegration') == 'jfbconnect' )
		{
			$integrate	= new DiscussIntegrate;
			$hasAvatar = $integrate->jfbconnect( $profile );

			if(! $hasAvatar) {
				$croppable 				= true;
				$allowJFBCAvatarEdit 	= true;
			}
		}

		$tpl	= new DiscussThemes();
		$tpl->set( 'croppable'			, $croppable );
		$tpl->set( 'allowJFBCAvatarEdit', $allowJFBCAvatarEdit );
		$tpl->set( 'size'				, $maxSize );
		$tpl->set( 'user'				, $user );
		$tpl->set( 'profile'			, $profile );
		$tpl->set( 'config'				, $config );
		$tpl->set( 'configMaxSize'		, $configMaxSize );
		$tpl->set( 'avatarIntegration'	, $config->get( 'layout_avatarIntegration', 'default' ) );
		$tpl->set( 'userparams'			, $userparams );
		$tpl->set( 'siteDetails'		, $siteDetails );

		echo $tpl->fetch( 'form.user.edit.php' );
	}
}
