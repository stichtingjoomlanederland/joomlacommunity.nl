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

class EasyDiscussViewProfile extends EasyDiscussView
{
	public function tab()
	{
		// always reset the limitstart.
		JRequest::setVar( 'limitstart', 0 );

		$type		= JRequest::getVar( 'type' );
		$profileId	= JRequest::getVar( 'id' );

		$ajax		= DiscussHelper::getHelper( 'ajax' );

		$model		= DiscussHelper::getModel('Posts');
		$tagsModel	= DiscussHelper::getModel( 'Tags' );
		$config 	= DiscussHelper::getConfig();

		$template	= new DiscussThemes();
		$html		= '';
		$pagination	= null;

		switch( $type )
		{
			case 'tags':
				$tags	= $tagsModel->getTagCloud( '' , '' , '' , $profileId );

				$template->set( 'tags'	, $tags );
				$html	= $template->fetch( 'profile.tags.php' );
				break;

			case 'questions':

				$posts		= $model->getPostsBy( 'user' , $profileId );
				$posts 		= DiscussHelper::formatPost($posts);
				$pagination	= $model->getPagination();

				$template->set( 'posts'	, $posts );
				$html	= $template->fetch( 'profile.questions.php' );
				break;

			case 'unresolved':

				$posts		= $model->getUnresolvedFromUser( $profileId );
				$posts 		= DiscussHelper::formatPost($posts);
				$pagination	= $model->getPagination();

				$posts = Discusshelper::getPostStatusAndTypes( $posts );

				$template->set( 'posts'	, $posts );
				$html	= $template->fetch( 'profile.unresolved.php' );
				break;

			case 'favourites':

				if( !$config->get( 'main_favorite' ) )
				{
					return false;
				}

				$posts 		= $model->getData( true , 'latest' , null , 'favourites' , '' , null , 'all', $profileId );
				$posts 		= DiscussHelper::formatPost( $posts );

				$posts = Discusshelper::getPostStatusAndTypes( $posts );

				$template->set( 'posts', $posts );
				$html 		= $template->fetch( 'profile.favourites.php' );
				break;

			case 'replies':

				$posts		= $model->getRepliesFromUser( $profileId );
				$posts 		= DiscussHelper::formatPost($posts);
				$pagination	= $model->getPagination();

				$posts = Discusshelper::getPostStatusAndTypes( $posts );

				$template->set( 'posts'	, $posts );
				$html	= $template->fetch( 'profile.replies.php' );
				break;

			case 'tabEasyBlog':

				$helperFile = JPATH_ADMINISTRATOR . '/components/com_easyblog/includes/easyblog.php';

				if( !JFile::exists( $helperFile ) )
				{
					$html 	= JText::_( 'COM_EASYDISCUSS_EASYBLOG_DOES_NOT_EXIST' );
				}
				else
				{
					require_once( $helperFile );

					$blogModel = EB::model('Blog');
					$blogs = $blogModel->getBlogsBy('blogger', $profileId);
					$blogs = EB::formatter('list', $blogs);
					$ebConfig = EB::config();

					$user 		= JFactory::getUser( $profileId );
					$template->set( 'user'		, $user );
					$template->set( 'ebConfig' , $ebConfig );
					$template->set( 'blogs' , $blogs );

					// Load EasyBlog's language file
					JFactory::getLanguage()->load( 'com_easyblog' , JPATH_ROOT );

					$html	= $template->fetch( 'profile.blogs.php' );
				}

				break;

			case 'tabKomento':
				$helperFile = JPATH_ROOT . '/components/com_komento/helpers/helper.php';

				if( !JFile::exists( $helperFile ) )
				{
					$html = JText::_( 'COM_EASYDISCUSS_KOMENTO_DOES_NOT_EXIST' );
				}
				else
				{
					require_once( $helperFile );

					$commentsModel	= Komento::getModel( 'comments' );
					$commentHelper	= Komento::getHelper( 'comment' );

					$options = array(
						'sort'		=> 'latest',
						'userid'	=> $profileId,
						'threaded'	=> 0
					);

					$comments = $commentsModel->getComments( 'all', 'all', $options );

					foreach( $comments as &$comment )
					{
						$comment = $commentHelper->process( $comment );
					}

					$feedUrl = Komento::getHelper( 'router' )->getFeedUrl( 'all', 'all', $profileId );

					JFactory::getLanguage()->load( 'com_komento', JPATH_ROOT );

					$template->set( 'feedUrl', $feedUrl );
					$template->set( 'comments', $comments );

					$html	= $template->fetch( 'profile.comments.php' );
				}

				break;

			case 'subscriptions':

				$subModel	= DiscussHelper::getModel( 'subscribe' );
				$rows		= $subModel->getSubscriptions();
				$subs		= array();

				if( $rows )
				{
					foreach($rows as $row)
					{
						$obj			= new stdClass();
						$obj->id		= $row->id;
						$obj->type		= $row->type;
						$obj->unsublink	= Discusshelper::getUnsubscribeLink($row, false);

						switch($row->type)
						{
							case 'site':
								$obj->title	= '';
								$obj->link	= '';
								break;
							case 'post':
								$post		= DiscussHelper::getTable( 'Post' );
								$post->load( $row->cid );
								$obj->title	= $post->title;
								$obj->link	= DiscussRouter::_( 'index.php?option=com_easydiscuss&view=post&id=' . $post->id );
								break;
							case 'category':
								$category	= DiscussHelper::getTable( 'Category' );
								$category->load( $row->cid );
								$obj->title	= $category->title;
								$obj->link	= DiscussRouter::getCategoryRoute( $category->id );
								break;
							case 'user':
								$profile	= DiscussHelper::getTable( 'Profile' );
								$profile->load( $row->cid );
								$obj->title	= $profile->getName();
								$obj->link	= $profile->getLink();
								break;
							default:
								unset($obj);
								break;
						}

						if (!empty($obj))
						{
							$obj->title	= DiscussStringHelper::escape($obj->title);
							$subs[$row->type][]	= $obj;
							unset($obj);
						}
					}
				}

				$template->set( 'subscriptions'	, $subs );
				$html	= $template->fetch( 'profile.subscriptions.php' );

				break;

			default:
				break;

		}

		if( $pagination )
		{
			$filterArr				= array();
			$filterArr['viewtype']	= $type;
			$filterArr['id']		= $profileId;

			$pagination = $pagination->getPagesLinks('profile', $filterArr, true);
		}

		$ajax->success( $html, $pagination );
		$ajax->send();

	}

	public function easyblog()
	{
		$helperFile = JPATH_ROOT . '/components/com_easyblog/helpers/helper.php';

		if( !Jfile::exists($helperFile) )
		{
			return JText::_( 'COM_EASYDISCUSS_EASYBLOG_DOES_NOT_EXIST' );
		}

		require_once $blogHelper;


	}

	public function komento()
	{
		$komentoAPI = JPATH_ROOT . '/components/com_komento/configuration.php';

		if( !JFile::exists($komentoAPI) )
		{
			return '';
		}

		require_once $komentoAPI;
		require_once JPATH_ROOT . '/components/com_komento/views/profile/view.html.php';

		Komento::getHelper( 'document' )->loadHeaders();

		$view = new KomentoViewProfile;
		$view->display();
	}

	public function filter( $viewtype = 'user-post', $profileId = null)
	{
		$ajax		= new Disjax();
		$mainframe	= JFactory::getApplication();
		$config		= DiscussHelper::getConfig();
		$acl		= DiscussHelper::getHelper( 'ACL' );

		$sort		= 'latest';
		$data		= null;
		$pagination	= null;
		$model		= $this->getModel('Posts');
		$tagsModel	= $this->getModel( 'Tags' );


		switch( $viewtype )
		{
			case 'user-achievements':

				$profile	= DiscussHelper::getTable( 'Profile' );
				$profile->load( $profileId );
				$data		= $profile->getBadges();

				break;

			case 'user-tags':
				$data	= $tagsModel->getTagCloud( '' , '' , '' , $profileId );
				break;

			case 'user-replies':
				$data		= $model->getRepliesFromUser( $profileId );
				$pagination	= $model->getPagination();
				DiscussHelper::formatPost( $data );
				break;

			case 'user-unresolved':
				$data	= $model->getUnresolvedFromUser( $profileId );
				$pagination	= $model->getPagination();
				DiscussHelper::formatPost( $data );

				break;

			case 'user-post':
			default:

				if( is_null($profileId) )
				{
					break;
				}

				$model		= $this->getModel('Posts');
				$data		= $model->getPostsBy( 'user' , $profileId );
				$data		= DiscussHelper::formatPost($data);
				$pagination	= $model->getPagination();
				break;
		}

		// replace the content
		$content	= '';
		$tpl		= new DiscussThemes();

		$tpl->set( 'profileId' , $profileId );

		if( $viewtype == 'user-post' || $viewtype == 'user-replies' || $viewtype == 'user-unresolved')
		{
			$nextLimit		= DiscussHelper::getListLimit();
			if( $nextLimit >= $pagination->total )
			{
				// $ajax->remove( 'dc_pagination' );
				$ajax->assign( $viewtype . ' #dc_pagination', '' );
			}

			$tpl->set( 'posts'		, $data );
			$content	= $tpl->fetch( 'main.item.php' );

			$ajax->assign( $viewtype . ' #dc_list' , $content );

			//reset the next start limi
			$ajax->value( 'pagination-start' , $nextLimit );

			if( $nextLimit < $pagination->total )
			{
				$filterArr  = array();
				$filterArr['viewtype'] 		= $viewtype;
				$filterArr['id'] 			= $profileId;
				$ajax->assign( $viewtype . ' #dc_pagination', $pagination->getPagesLinks('profile', $filterArr, true) );
			}
		}
		else if( $viewtype == 'user-tags' )
		{
			$tpl->set( 'tagCloud'		, $data );
			$content	= $tpl->fetch( 'tags.item.php' );

			$ajax->assign( 'discuss-tag-list' , $content );
		}
		else if( $viewtype == 'user-achievements' )
		{
			$tpl->set( 'badges'		, $data );
			$content	= $tpl->fetch( 'users.achievements.list.php' );
			$ajax->assign( 'user-achievements' , $content );
		}

		$ajax->script( 'discuss.spinner.hide( "profile-loading" );' );


		//$ajax->assign( 'sort-wrapper' , $sort );
		//$ajax->script( 'EasyDiscuss.$("#pagination-filter").val("'.$viewtype.'");');
		$ajax->script( 'EasyDiscuss.$("#' . $viewtype . '").show();');
		$ajax->script( 'EasyDiscuss.$("#' . $viewtype. ' #dc_pagination").show();');

		$ajax->send();
	}

	public function cropPhoto()
	{
		$my 		= JFactory::getUser();
		$ajax 		= DiscussHelper::getHelper( 'Ajax' );

		if( !$my->id )
		{
			$ajax->reject( JText::_( 'You are not allowed here' ) );
			return $ajax->send();
		}

		$config 	= DiscussHelper::getConfig();
		$profile 	= DiscussHelper::getTable( 'Profile' );
		$profile->load( $my->id );

		$path 		= rtrim( $config->get( 'main_avatarpath') , DIRECTORY_SEPARATOR );
		$path 		= JPATH_ROOT . '/' . $path;

		$photoPath 		= $path . '/' . $profile->avatar;
		$originalPath 	= $path . '/' . 'original_' . $profile->avatar;
		// @rule: Delete existing image first.
		if( JFile::exists( $photoPath ) )
		{
			JFile::delete( $photoPath );
		}

		$x1 = JRequest::getInt( 'x1' );
		$y1 = JRequest::getInt( 'y1' );
		$width = JRequest::getInt( 'width' );
		$height = JRequest::getInt( 'height' );

		if (is_null($x1) &&
		    is_null($y1) &&
		    is_null($width) &&
		    is_null($height))
		{
			$ajax->reject( JText::_('Unable to crop because cropping parameters are incomplete!'));
			return $ajax->send();
		}

		require_once DISCUSS_CLASSES . '/simpleimage.php';
		$image 		= new SimpleImage();
		$image->load( $originalPath );

		$image->crop( $width , $height , $x1 , $y1 );

		$image->resize( 160, 160 );

		$image->save( $photoPath );


		$path	= trim( $config->get( 'main_avatarpath') , '/' ) . '/' . $profile->avatar;
		$uri	= rtrim( JURI::root() , '/' );
		$uri	.= '/' . $path;

		$ajax->resolve($uri, 'Avatar cropped successfully!');

		return $ajax->send();
	}

	public function ajaxCheckAlias($alias)
	{
		$disjax		= new disjax();
		$my			= JFactory::getUser();

		// do not let unregistered user
		if ( $my->id <= 0 )
		{
			return false;
		}

		// satinize input
		$filter	= JFilterInput::getInstance();
		$alias	= $filter->clean( $alias, 'ALNUM' );

		// check for existance
		$db		= DiscussHelper::getDBO();
		$query	= 'SELECT `alias` FROM `#__discuss_users` WHERE `alias` = ' . $db->quote($alias) . ' '
				. 'AND ' . $db->nameQuote( 'id' ) . '!=' . $db->Quote( $my->id );
		$db->setQuery( $query );
		$result	= $db->loadResult();

		// prepare output
		if ( $result )
		{
			$html	= JText::_('COM_EASYDISCUSS_ALIAS_NOT_AVAILABLE');
			$class	= 'failed';
		}
		else
		{
			$html	= JText::_('COM_EASYDISCUSS_ALIAS_AVAILABLE');
			$class 	= 'success';
		}

		$options = new stdClass();

		// fill in the value
		$disjax->assign( 'profile-alias' , $alias );
		$disjax->script( 'EasyDiscuss.$( "#alias-status" ).html("'.$html.'").removeClass("failed").removeClass("success").addClass( "'.$class.'" );' );
		$disjax->value( 'profile-alias' , $alias );

		$disjax->send();
	}
}
