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
defined( '_JEXEC' ) or die( 'Restricted access' );

require_once( KOMENTO_ROOT . DIRECTORY_SEPARATOR . 'views' . DIRECTORY_SEPARATOR . 'view.php' );

jimport( 'joomla.html.parameter' );

require_once( KOMENTO_CLASSES . DIRECTORY_SEPARATOR . 'themes.php' );
require_once( KOMENTO_HELPERS . DIRECTORY_SEPARATOR . 'helper.php' );

class KomentoViewKomento extends KomentoView
{
	function __construct()
	{
		$component = JRequest::getCmd('component', '');
		$cid = JRequest::getInt( 'cid', 0 );
		$id = JRequest::getInt( 'id', 0 );

		if( $component == '' && $cid == 0 && $id != 0 )
		{
			$tmp = Komento::getTable( 'comments');
			$tmp->load( $id );
			$component = $tmp->component;
			$cid = $tmp->cid;
		}

		if( $component )
		{
			Komento::setCurrentComponent( $component );
		}
	}

	public function addComment()
	{
		$now			= Komento::getDate()->toMySQL();
		$my 			= JFactory::getUser();
		$profile		= Komento::getProfile();
		$commentObj		= Komento::getComment();
		$commentsModel	= Komento::getModel( 'comments' );
		$commentHelper	= Komento::getHelper( 'comment' );
		$dateHelper		= Komento::getHelper( 'date' );
		$config			= Komento::getConfig();
		$ajax			= Komento::getAjax();
		$session		= Komento::getHelper( 'session' );

		$contentLink	= JRequest::getVar( 'contentLink' );

		// Get comment data
		$commentObj->component	= JRequest::getCmd( 'component' );
		$commentObj->cid		= JRequest::getInt( 'cid' );
		$commentObj->parent_id	= JRequest::getInt( 'parent_id', 0 );
		$commentObj->depth		= JRequest::getInt( 'depth', 0 );
		$commentObj->comment	= JRequest::getVar( 'comment', '', 'post', '', JREQUEST_ALLOWRAW );
		$commentObj->name		= JRequest::getVar( 'name', '' );
		$commentObj->email		= JRequest::getVar( 'email', '' );
		$commentObj->url		= JRequest::getVar( 'website', '' );
		$commentObj->ratings 	= JRequest::getVar('ratings', '0');

		// Check and get location data
		$commentObj->latitude	= '';
		$commentObj->longitude	= '';
		$commentObj->address	= '';
		$address = JRequest::getVar( 'address', '' );
		if( $address != JText::_( 'COM_KOMENTO_COMMENT_WHERE_ARE_YOU' ) && $address != '' )
		{
			$commentObj->latitude	= JRequest::getVar( 'latitude', '' );
			$commentObj->longitude	= JRequest::getVar( 'longitude', '' );
			$commentObj->address	= JRequest::getVar( 'address', '' );
		}

		// Set static comment data
		$commentObj->modified_by	= 0;
		$commentObj->modified		= '0000-00-00 00:00:00';
		$commentObj->deleted_by		= 0;
		$commentObj->deleted		= '0000-00-00 00:00:00';
		$commentObj->flag			= 0;
		$commentObj->sent			= 0;

		// Construct other comment data
		$ipAddress = @$_SERVER['REMOTE_ADDR'];
		if (array_key_exists('HTTP_X_FORWARDED_FOR', $_SERVER)) {
		    $ipAddress = array_pop(explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']));
		}

		$commentObj->ip			= $ipAddress;
		$commentObj->created_by	= $profile->id;
		$commentObj->created	= $now;
		$commentObj->published	= 1;

		// Get other data
		$data				= new stdClass();
		$data->username		= JRequest::getVar( 'username', '' );
		$data->register		= JRequest::getVar( 'register', '' ) == 'true' ? 1 : 0; // javascript pass boolean as string "true"/"false"
		$data->subscribe	= JRequest::getVar( 'subscribe', '' ) == 'true' ? 1 : 0; // javascript pass boolean as string "true"/"false"
		$data->tnc			= JRequest::getVar( 'tnc', '' ) == 'true' ? 1 : 0; // javascript pass boolean as string "true"/"false"

		// Access check
		if( !$profile->allow( 'add_comment' ) )
		{
			$ajax->fail( JText::_( 'COM_KOMENTO_FORM_NOT_ALLOWED' ) );
			$ajax->send();
		}

		// Flood control
		if( $config->get( 'antispam_flood_control' ) )
		{
			$lastReplyTime = $session->getLastReplyTime();
			$timeDifference = $dateHelper->getDifference( $lastReplyTime );

			if( $timeDifference && $timeDifference <= $config->get( 'antispam_flood_interval' ) )
			{
				$ajax->fail( JText::_( 'COM_KOMENTO_FORM_NOTIFICATION_FLOOD') );
				$ajax->send();
			}
		}

		// Field check
		if( !$this->validate( $commentObj, $data ) )
		{
			$ajax->fail( JText::_( 'COM_KOMENTO_FORM_NOTIFICATION_FIELD_REQUIRED' ) );
			$ajax->send();
		}

		// Regex check
		$regexResult = $this->regexCheck( $commentObj, $data );
		if( $regexResult !== true )
		{
			$ajax->fail( $regexResult );
			$ajax->send();
		}

		// Default to profile's details
		if( empty( $commentObj->name ) )
		{
			$commentObj->name = $profile->getName();
		}

		if( $profile->id && empty( $commentObj->email ) )
		{
			$commentObj->email = $profile->email;
		}

		// Captcha check
		if( $config->get( 'antispam_captcha_enable' ) )
		{
			$captchaGroup		= $config->get( 'show_captcha' );

			if( !is_array( $captchaGroup ) )
			{
				$captchaGroup	= explode( ',', $captchaGroup );
			}

			$usergids			= $profile->getUsergroups();
			$requiresCaptcha	= false;

			foreach( $usergids as $gid )
			{
				if( in_array( $gid, $captchaGroup ) )
				{
					$requiresCaptcha = true;
					break;
				}
			}

			if ($requiresCaptcha) {
				
				$captchaData = array();
				$captchaData['recaptcha_response_field'] = JRequest::getVar('recaptchaResponse');
				$captchaData['captcha-response'] = JRequest::getVar('captchaResponse');
				$captchaData['captcha-id'] = JRequest::getVar('captchaId');
				
				if (Komento::getCaptcha() && !Komento::getCaptcha()->verify($captchaData)) {
					$error = Komento::getCaptcha()->getError();
					$reload = Komento::getCaptcha()->getReloadSyntax();

					if ($error == 'incorrect-captcha-sol') {
						$error = JText::_( 'COM_KOMENTO_CAPTCHA_INVALID_RESPONSE' );
					}

					$ajax->fail($error);
					
					if ($config->get('antispam_captcha_type') == 0) {
						$ajax->captcha($reload);
					}

					$ajax->send();
				}
			}
		}

		// Akismet detection
		if( $config->get( 'antispam_akismet' ) )
		{
			$akismetData = array(
				'author'	=> $commentObj->name,
				'email'		=> $commentObj->email,
				'website'	=> JURI::root(),
				'body'		=> $commentObj->comment,
				'permalink'	=> $contentLink
			);

			if( Komento::getHelper( 'Akismet' )->isSpam( $akismetData ) )
			{
				$ajax->fail( JText::_( 'COM_KOMENTO_FORM_NOTIFICATION_AKISMET_SPAM' ) );
				$ajax->send();
			}
		}

		// Length check
		if( trim( $commentObj->comment ) == '' )
		{
			$ajax->fail( JText::_( 'COM_KOMENTO_FORM_NOTIFICATION_COMMENT_REQUIRED' ) );
			$ajax->send();
		}
		if( $config->get( 'antispam_min_length_enable' ) && JString::strlen( $commentObj->comment ) < $config->get( 'antispam_min_length' ) )
		{
			$ajax->fail( JText::_( 'COM_KOMENTO_FORM_NOTIFICATION_COMMENT_TOO_SHORT' ) );
			$ajax->send();
		}
		if( $config->get( 'antispam_max_length_enable' ) && JString::strlen( $commentObj->comment ) > $config->get( 'antispam_max_length' ) )
		{
			$ajax->fail( JText::_( 'COM_KOMENTO_FORM_NOTIFICATION_COMMENT_TOO_LONG' ) );
			$ajax->send();
		}

		// Moderation
		if( $profile->toModerate() )
		{
			$commentObj->published = 2;
		}

		if( $commentObj->published == 1 )
		{
			$commentObj->modified = $now;
			$commentObj->publish_up = $now;
		}

		// Check if the poster's ip address matches any blacklisted ip addresses.
		$blacklistedIpAddresses = trim($config->get('blacklist_ip'));
		$userIpAddress = @$_SERVER['REMOTE_ADDR'];

		if (!empty($blacklistedIpAddresses)) {
			$blacklistedIpAddresses = explode(',', $blacklistedIpAddresses);

			if (in_array($userIpAddress, $blacklistedIpAddresses)) {
				$commentObj->published = 2;
			}
		}

		// Calculate and update comment boundaries
		$commentsModel->updateCommentLftRgt( $commentObj );

		$nodeId = 0;

		// Calculate which comment to append/prepend
		if( $commentObj->parent_id != 0 )
		{
			$previousNode = $commentObj->parent_id;
			do
			{
				$latestNode = $commentsModel->getLatestComment( $commentObj->component, $commentObj->cid, $previousNode );

				if( $latestNode == NULL )
				{
					$nodeId = $previousNode;
					break;
				}
				else
				{
					$previousNode = $latestNode->id;
				}
			}
			while( $latestNode != NULL );
		}

		if( !$commentObj->save() )
		{
			$errors = JText::_( 'COM_KOMENTO_ERROR' );

			if( $commentObj->getErrors() )
			{
				$errors = implode( '\n', $commentObj->getErrors() );
			}

			$ajax->fail( $errors );
			$ajax->send();
		}

		// Subscription
		$subscribeResult = '';

		// enforce subscription
		if( $config->get( 'subscription_auto' ) )
		{
			$data->subscribe = 1;
		}

		if( $data->subscribe && !empty( $commentObj->email ) )
		{
			$subscribeResult = self::subscribe( 'comment', $commentObj->component, $commentObj->cid, $commentObj->id, $profile->id, $commentObj->name, $commentObj->email );
			if( $subscribeResult )
			{
				if( $config->get( 'subscription_confirmation' ) )
				{
					$ajax->confirmSubscribe();
				}
				else
				{
					$ajax->subscribe();
				}
			}
			else
			{
				$ajax->subscribeError();
			}
		}

		// Set reply datetime in session for flood control
		if( $config->get( 'antispam_flood_control' ) )
		{
			$session->setReplyTime();
		}

		// attach uploaded files after getting commentid
		$attachments = JRequest::getVar( 'attachments' );
		if( is_array( $attachments ) && !empty( $attachments ) )
		{
			$filehelper = Komento::getHelper( 'file' );
			foreach( $attachments as $attachment )
			{
				$state = $filehelper->attach( $attachment, $commentObj->id );

				if( $state )
				{
					Komento::getHelper( 'activity' )->process( 'upload', $commentObj );
				}
			}
		}

		// Initialise additional values = 0
		$commentObj->likes = 0;
		$commentObj->liked = 0;
		$commentObj->sticked = 0;
		$commentObj->childs = 0;
		$commentObj->reported = 0;

		$theme = Komento::getTheme();
		$theme->set( 'row', $commentObj );
		$theme->set( 'contentLink', $contentLink );
		$html = $theme->fetch( 'comment/item.php' );

		// clear cache to prevent issues
		JFactory::getCache()->clean();

		$ajax->success( $nodeId, $html, $commentObj->published );

		$ajax->send();
	}

	function loadMoreComments()
	{
		$config			= Komento::getConfig();
		$profile		= Komento::getProfile();
		$ajax			= Komento::getAjax();
		$commentsModel	= Komento::getModel( 'comments' );

		$component		= JRequest::getCmd( 'component' );
		$cid			= JRequest::getCmd( 'cid' );
		$start			= JRequest::getInt( 'start' );
		$limit			= JRequest::getInt( 'limit' );
		$sort			= JRequest::getCmd( 'sort', 'oldest' );
		$contentLink	= JRequest::getVar( 'contentLink' );

		$options		= array(
			'limit'			=> $limit,
			'limitstart'	=> $start,
			'sort'			=> $sort,
			'threaded'		=> $config->get( 'enable_threaded' )
		);

		if( !$profile->allow( 'read_comment' ) )
		{
			$ajax->fail( JText::_( 'COM_KOMENTO_ACL_NO_PERMISSION' ) );
			$ajax->send();
		}

		$comments = $commentsModel->getComments( $component, $cid, $options );
		$loadedComments = count( $comments );

		if($loadedComments == 0)
		{
			$ajax->fail($options['limit'], $options['limitstart'], $options['sort']);
			$ajax->send();
		}

		$commentCount = $commentsModel->getCount( $component, $cid );
		$loaded = 0;
		$more = 0;

		// if( !$config->get( 'enable_threaded') && $config->get( 'load_previous' ) )
		if( $config->get( 'load_previous' ) )
		{
			$loaded = (int)$commentCount - (int)$start;
			if( $start > 0 )
			{
				$more = 1;
			}
		}
		else
		{
			$loaded = (int)$start + (int)$loadedComments;
			if( $loaded < $commentCount )
			{
				$more = 1;
			}
		}

		$theme = Komento::getTheme();
		$theme->set( 'component', $component );
		$theme->set( 'cid', $cid );
		$theme->set( 'comments', $comments );
		$theme->set( 'commentCount', $commentCount );
		$theme->set( 'contentLink', $contentLink );
		$html = $theme->fetch( 'comment/ajax.list.php' );

		$ajax->success( $html, $loaded, $more );
		$ajax->send();
	}

	function loadComments()
	{
		$config			= Komento::getConfig();
		$profile		= Komento::getProfile();
		$ajax			= Komento::getAjax();
		$commentsModel	= Komento::getModel( 'comments' );

		$contentLink	= JRequest::getVar( 'contentLink' );
		$type			= JRequest::getCmd( 'type' );
		$component		= JRequest::getCmd( 'component' );
		$cid			= JRequest::getCmd( 'cid' );
		$options		= array(
			'sort' => JRequest::getCmd( 'sort', 'default' ),
			'sticked' => JRequest::getCmd( 'sticked', 'all' ),
			'threaded' => JRequest::getInt( 'threaded', $config->get( 'enable_threaded' ) ),
			'limit' => JRequest::getInt( 'limit', $config->get( 'max_comments_per_page' ) )
		);

		$comments = '';
		$funcName = 'getComments';
		$allowed = 0;

		$file = 'comment/list.php';
		$commentsArray = 'comments';

		switch( $type )
		{
			case 'stickies':
				$allowed = $profile->allow( 'read_stickies' ) ? 1 : 0;
				$file = 'comment/stick/list.php';
				$commentsArray = 'sticks';
				break;
			case 'lovies':
				$allowed = $profile->allow( 'read_lovies' ) ? 1 : 0;
				$file = 'comment/love/list.php';
				$commentsArray = 'loves';
				$funcName = 'getPopularComments';
				$options['minimumlikes'] = $config->get( 'minimum_likes_lovies' );
				break;
			default:
				$allowed = $profile->allow( 'read_comment' ) ? 1 : 0;
				break;
		}

		if( !$allowed )
		{
			$ajax->fail();
			$ajax->send();
		}

		$comments	= $commentsModel->$funcName( $component, $cid, $options );

		$theme = Komento::getTheme();
		$theme->set( 'ajaxcall', 1 );
		$theme->set( 'component', $component );
		$theme->set( 'cid', $cid );
		$theme->set( $commentsArray, $comments );
		$theme->set( 'contentLink', $contentLink );
		$html = $theme->fetch( $file );

		$ajax->success( $html );
		$ajax->send();
	}

	function reloadComments()
	{
		$config			= Komento::getConfig();
		$profile		= Komento::getProfile();
		$ajax			= Komento::getAjax();
		$acl			= Komento::getHelper( 'acl' );
		$commentsModel	= Komento::getModel( 'comments' );

		$component		= JRequest::getCmd( 'component' );
		$cid			= JRequest::getCmd( 'cid' );
		// $options		= array( 'sort' => JRequest::getCmd( 'sort', 'default' ) );
		$options		= JRequest::getVar( 'options' );
		$contentLink	= JRequest::getVar( 'contentLink' );

		$application = Komento::loadApplication( $component )->load( $cid );

		if( $application === false )
		{
			$application = Komento::getErrorApplication( $component, $cid );
		}

		if( !$profile->allow( 'read_comment' ) )
		{
			$ajax->fail();
			$ajax->send();
		}

		// check if allowed in admin mode
		if( isset( $options['published'] ) && $options['published'] != '1' && !$acl->allow( 'publish', '', $component, $cid ) )
		{
			$ajax->fail();
			$ajax->send();
		}

		$commentCount	= $commentsModel->getCount( $component, $cid, $options );

		if( $config->get( 'load_previous' ) )
		{
			$options['limitstart'] = $commentCount - $config->get( 'max_comments_per_page' );
			if( $options['limitstart'] < 0 )
			{
				$options['limitstart'] = 0;
			}

			// re-query comments under load_previous case
			// $comments	= $commentsModel->getComments( $component, $cid, $options );
		}

		$options['threaded'] = $config->get( 'enable_threaded' );

		$comments	= $commentsModel->getComments( $component, $cid, $options );

		$theme = Komento::getTheme();
		$theme->set( 'ajaxcall', 1 );
		$theme->set( 'component', $component );
		$theme->set( 'cid', $cid );
		$theme->set( 'comments', $comments );
		$theme->set( 'options', $options );
		$theme->set( 'commentCount', $commentCount );
		$theme->set( 'application', $application );
		$theme->set( 'contentLink', $contentLink );
		$html = $theme->fetch( 'comment/list.php' );

		$ajax->success( $html, count($comments), $commentCount );
		$ajax->send();
	}

	function getComment()
	{
		$profile = Komento::getProfile();
		if( !$profile->allow( 'read_comment' ) )
		{
			$ajax->fail();
			$ajax->send();
		}

		$id = JRequest::getVar( 'id' );

		$comment = Komento::getComment( $id );
		$comment = KomentoCommentHelper::process( $comment );

		$parentTheme = Komento::getTheme();
		$parentTheme->set( 'row', $comment );

		// todo: configurable
		$html  = $parentTheme->fetch( 'comment/item/avatar.php' );
		$html .= $parentTheme->fetch( 'comment/item/author.php' );
		$html .= $parentTheme->fetch( 'comment/item/time.php' );
		$html .= $parentTheme->fetch( 'comment/item/text.php' );

		$ajax = Komento::getAjax();
		$ajax->success( $html );
		$ajax->send();
	}

	function getCommentRaw()
	{
		$id = JRequest::getVar( 'id' );

		$commentObj = Komento::getComment( $id );

		$ajax = Komento::getAjax();

		$ajax->success( $commentObj->comment );
		$ajax->send();
	}

	function editComment()
	{
		$now		= Komento::getDate()->toMySQL();
		$profile	= Komento::getProfile();
		$config		= Komento::getConfig();

		$id = JRequest::getVar( 'id' );
		$edittedComment = JRequest::getVar( 'edittedComment', '', 'post', '', JREQUEST_ALLOWRAW );

		// use table instead of commentclass to avoid sending mail
		$commentObj = Komento::getTable( 'comments' );
		$commentObj->load( $id );

		$ajax = Komento::getAjax();
		$acl = Komento::getHelper( 'Acl' );

		if( $acl->allow( 'edit', $commentObj ) )
		{
			// Length check
			if( trim( $edittedComment ) == '' )
			{
				$ajax->fail( JText::_( 'COM_KOMENTO_FORM_NOTIFICATION_COMMENT_REQUIRED' ) );
				$ajax->send();
			}
			if( $config->get( 'antispam_min_length_enable' ) && JString::strlen( $edittedComment ) < $config->get( 'antispam_min_length' ) )
			{
				$ajax->fail( '<p>' . JText::_( 'COM_KOMENTO_FORM_NOTIFICATION_COMMENT_TOO_SHORT' ) . '. ' . JText::sprintf( 'COM_KOMENTO_FORM_CHARACTER_MIN', $config->get( 'antispam_min_length' ) ) . '.</p>' );
				$ajax->send();
			}
			if( $config->get( 'antispam_max_length_enable' ) && JString::strlen( $edittedComment ) > $config->get( 'antispam_max_length' ) )
			{
				$ajax->fail( '<p>' . JText::_( 'COM_KOMENTO_FORM_NOTIFICATION_COMMENT_TOO_LONG' ) . '. ' . JText::sprintf( 'COM_KOMENTO_FORM_CHARACTER_MAX', $config->get( 'antispam_max_length' ) ) . '.</p>' );
				$ajax->send();
			}

			$commentObj->comment = $edittedComment;
			$commentObj->modified_by = $profile->id;
			$commentObj->modified = $now;

			$result = 1;
			if( !$commentObj->store() )
			{
				$result = 0;
			}

			$comment = KomentoCommentHelper::parseComment( $commentObj->comment );

			if( $result )
			{
				// success(parsed comment, modified date/time, by )
				$ajax->success( $comment, $commentObj->modified, $profile->name );
			}
			else
			{
				$errors = JText::_( 'COM_KOMENTO_ERROR' );

				if( $commentObj->getErrors() )
				{
					$errors = implode( '\n', $commentObj->getErrors() );
				}

				$ajax->fail( $errors );
			}
		}
		else
		{
			$ajax->fail( JText::_( 'COM_KOMENTO_ACL_NO_PERMISSION' ) );
		}

		$ajax->send();
	}

	function deleteComment()
	{
		$affectChild	= JRequest::getInt( 'affectChild', 0 );
		$id				= JRequest::getInt( 'id' );

		$commentObj		= Komento::getComment( $id );
		$commentModel	= Komento::getModel( 'comments' );
		$ajax			= Komento::getAjax();
		$acl			= Komento::getHelper( 'Acl' );

		$childs			= 0;

		if( $acl->allow( 'delete', $commentObj ) )
		{
			if( $affectChild )
			{
				$childs = $commentModel->getChilds( $id );

				if( count( $childs ) > 0 )
				{
					foreach( $childs as $child )
					{
						if( !Komento::getComment( $child )->delete() )
						{
							$ajax->fail();
							$ajax->send();
						}
					}
				}
			}

			if( !$commentObj->delete() )
			{
				$ajax->fail();
				$ajax->send();
			}

			$ajax->success();

		}
		else
		{
			$ajax->fail( JText::_( 'COM_KOMENTO_ACL_NO_PERMISSION' ) );
		}

		$ajax->send();
	}

	function action()
	{
		$action			= JRequest::getVar( 'action' );
		$type			= JRequest::getVar( 'type' );
		$id				= JRequest::getInt( 'id' );

		$ajax			= Komento::getAjax();
		$profile		= Komento::getProfile();

		if( ( $type == 'likes' && !$profile->allow( 'like_comment' ) ) || ( $type == 'report' && !$profile->allow( 'report_comment' ) ) )
		{
			$ajax->fail( JText::_( 'COM_KOMENTO_ACL_NO_PERMISSION' ) );
			$ajax->send();
		}

		$commentObj = Komento::getComment( $id );

		$result = $commentObj->action( $action, $type, $profile->id );

		if( $result !== false )
		{
			$ajax->success();
		}
		else
		{
			$ajax->fail();
		}

		$ajax->send();
	}

	function mark()
	{
		$type		= JRequest::getVar( 'type' );
		$id			= JRequest::getInt( 'id' );

		$ajax		= Komento::getAjax();
		$commentObj	= Komento::getComment( $id );

		if( !$commentObj->mark( $type ) )
		{
			$ajax->fail();
			$ajax->send();
		}

		$ajax->success();
		$ajax->send();
	}

	function publish( $type = 1 )
	{
		$id				= JRequest::getInt( 'id' );
		$affectChild	= JRequest::getInt( 'affectChild', 1 );

		$ajax			= Komento::getAjax();
		$commentObj		= Komento::getComment( $id );
		$commentModel	= Komento::getModel( 'comments' );
		$acl			= Komento::getHelper( 'Acl' );

		$application = Komento::loadApplication( $commentObj->component )->load( $commentObj->cid );

		if( $application === false )
		{
			$application = Komento::getErrorApplication( $commentObj->component, $commentObj->cid );
		}

		if( ( $type == 1 && $acl->allow( 'publish', $commentObj ) ) || ( $type == 0 && $acl->allow( 'unpublish', $commentObj ) ) )
		{
			if( $affectChild )
			{
				$childs = $commentModel->getChilds( $id );

				if( count( $childs ) > 0 )
				{
					foreach( $childs as $child )
					{
						$childObj = Komento::getComment( $child );

						if( !$childObj->publish( $type ) )
						{
							$errors = JText::_( 'COM_KOMENTO_ERROR' );

							if( $childObj->getErrors() )
							{
								$errors = implode( '\n', $childObj->getErrors() );
							}

							$ajax->fail( $errors );
							$ajax->send();
						}
					}
				}
			}

			if( !$commentObj->publish( $type ) )
			{
				$errors = JText::_( 'COM_KOMENTO_ERROR' );

				if( $commentObj->getErrors() )
				{
					$errors = implode( '\n', $commentObj->getErrors() );
				}

				$ajax->fail( $errors );
				$ajax->send();
			}

			$ajax->success();
		}
		else
		{
			$ajax->fail( JText::_( 'COM_KOMENTO_ACL_NO_PERMISSION' ) );
		}
		$ajax->send();
	}

	function unpublish()
	{
		$this->publish( 0 );
	}

	function reloadCaptcha()
	{
		$ajax 	= Komento::getAjax();
		$reload	= Komento::getCaptcha()->getReloadSyntax();
		$ajax->success( $reload );
		$ajax->send();
	}

	function stick()
	{
		$id		= JRequest::getInt( 'id' );
		$ajax 	= Komento::getAjax();
		$model	= Komento::getModel( 'comments' );
		$acl = Komento::getHelper( 'acl' );

		$commentObj = Komento::getComment( $id );

		if( !$acl->allow( 'stick', $commentObj ) )
		{
			$ajax->fail();
			$ajax->send();
		}

		if( $model->stick( $id ) )
		{
			$ajax->success();
			$ajax->send();
		}
		else
		{
			$ajax->fail();
			$ajax->send();
		}
	}

	function unstick()
	{
		$id		= JRequest::getInt( 'id' );
		$ajax = Komento::getAjax();
		$model	= Komento::getModel( 'comments' );
		$acl = Komento::getHelper( 'acl' );

		$commentObj = Komento::getComment( $id );

		if( !$acl->allow( 'stick', $commentObj ) )
		{
			$ajax->fail();
			$ajax->send();
		}

		if( $model->unstick($id) )
		{
			$ajax->success();
			$ajax->send();
		}
		else
		{
			$ajax->fail();
			$ajax->send();
		}
	}

	function unsubscribe()
	{
		$component	= JRequest::getCmd( 'component' );
		$cid		= JRequest::getCmd( 'cid' );
		$email		= JRequest::getVar( 'email', '' );
		$ajax		= Komento::getAjax();
		$userid		= JFactory::getUser()->id;

		$model		= Komento::getModel( 'subscription' );

		if( $model->unsubscribe( $component, $cid, $userid, $email ) )
		{
			$ajax->success();
			$ajax->send();
		}
		else
		{
			$ajax->fail();
			$ajax->send();
		}
	}

	function checkNewComment()
	{
		$component	= JRequest::getCmd( 'component' );
		$cid		= JRequest::getCmd( 'cid' );
		$oldTotal	= JRequest::getInt( 'total' );

		$ajax		= Komento::getAjax();
		$model		= Komento::getModel( 'comments' );
		$newTotal	= $model->getCount( $component, $cid );

		$new = 0;
		$newCount = 0;

		if( $newTotal > $oldTotal )
		{
			$new = 1;
			$newCount = $newTotal - $oldTotal;
		}

		$ajax->success( $new, $newCount );
		$ajax->send();
	}

	function checkAcl()
	{
		$rule		= JRequest::getCmd( 'rule' );
		$profile	= Komento::getProfile();
		$ajax		= Komento::getAjax();
		$ajax->success( $profile->allow( $rule ) );
		$ajax->send();
	}

	function checkPermission()
	{
		$id			= JRequest::getInt( 'id' );
		$action		= JRequest::getCmd( 'action' );
		$acl		= Komento::getHelper( 'acl' );
		$ajax		= Komento::getAjax();
		$ajax->success( $acl->allow( $action, $id ) );
		$ajax->send();
	}

	function shortenLink()
	{
		$link = JRequest::getVar( 'url' );

		if( $link != '' )
		{
			Komento::import( 'helper', 'social' );
			$link = KomentoSocialHelper::shortenUrl( $link );
		}

		$ajax = Komento::getAjax();
		$ajax->success( $link );
		$ajax->send();
	}

	function deleteAttachment()
	{
		$id	= JRequest::getInt( 'id' );
		$attachmentid = JRequest::getInt( 'attachmentid' );
		$ajax = Komento::getAjax();
		$acl = Komento::getHelper( 'acl' );

		if( !$acl->allow( 'delete_attachment', $id ) )
		{
			$ajax->fail( JText::_( 'COM_KOMENTO_ACL_NO_PERMISSION' ) );
			$ajax->send();
		}

		$filetable = Komento::getTable( 'uploads' );
		$filetable->load( $attachmentid );

		if( !$filetable->isCommentAttachment( $id ) )
		{
			$ajax->fail( JText::_( 'COM_KOMENTO_ATTACHMENT_INVALID_COMMENT_ATTACHMENT'));
			return $ajax->send();
		}

		if( !$filetable->delete() )
		{
			$ajax->fail( JText::_( 'COM_KOMENTO_ERROR' ) );
			$ajax->send();
		}

		$ajax->success();
		$ajax->send();
	}

	function getLikedUsers()
	{
		$id = JRequest::getInt( 'id' );
		$ajax = Komento::getAjax();

		$model = Komento::getModel( 'actions' );
		$users = $model->getLikedUsers( $id );

		$theme = Komento::getTheme();
		$theme->set( 'likedUsers', $users );
		$html = $theme->fetch( 'comment/item/likedusers.php' );

		$ajax->success( $html );
		$ajax->send();
	}

	function showVideoDialog()
	{
		$element 		= JRequest::getVar( 'element' );
		$caretPosition	= JRequest::getVar( 'caretPosition' );

		$theme 		= Komento::getTheme();
		$theme->set( 'element' , $element );
		$theme->set( 'caretPosition' , $caretPosition );

		$html 		= $theme->fetch( 'comment/form/insertvideo.php' );

		$ajax 		= Komento::getAjax();
		$ajax->success( $html );
		$ajax->send();
	}

	private function subscribe( $type = 'comment', $component, $cid, $commentId = 0, $userid = 0, $name, $email )
	{
		if( $commentId !== 0 && ( !$component || !$cid ) )
		{
			$comment = Komento::getComment( $commentId );
			$component = $comment->component;
			$cid = $comment->cid;
		}

		if( !$component || !$cid )
		{
			return false;
		}

		$subscriptionExist = Komento::getModel( 'subscription' )->checkSubscriptionExist( $component, $cid, $userid, $email, $type );

		if( !$subscriptionExist )
		{
			$data = array(
				'type'		=> $type,
				'component' => $component,
				'cid'		=> $cid,
				'userid'	=> $userid,
				'fullname'	=> $name,
				'email'		=> $email,
				'created'	=> Komento::getDate()->toMySQL(),
				'published'	=> 1
			);

			$config = Komento::getConfig();

			if( $config->get( 'subscription_confirmation' ) )
			{
				$data['published'] = 0;
			}

			$subscribeTable = Komento::getTable( 'subscription' );

			$subscribeTable->bind( $data );

			if( !$subscribeTable->store() )
			{
				return false;
			}

			if( $config->get( 'subscription_confirmation' ) )
			{
				Komento::getHelper( 'Notification' )->push( 'confirm', 'me', array( 'component' => $component, 'cid' => $cid, 'subscribeId' => $subscribeTable->id, 'commentId' => $commentId ) );
			}
		}

		return true;
	}

	private function register()
	{

	}

	private function validate( $comment, $data )
	{
		$config = Komento::getConfig();
		$profile = Komento::getProfile();
		$result = array();

		// validate name
		if( empty( $comment->name ) && ( ( $config->get( 'show_name' ) == 2 && $config->get( 'require_name' ) == 2 ) || ( $profile->guest && $config->get( 'show_name' ) > 0 && $config->get( 'require_name' ) == 1 ) ) )
		{
			return false;
		}

		// validate email + subscription checkbox
		if( empty( $comment->email ) && ( ( $config->get( 'show_email' ) == 2 && ( $config->get( 'require_email' ) == 2 || $data->subscribe ) ) || ( $profile->guest && $config->get( 'show_email' ) > 0 && ( $config->get( 'require_email' ) == 1 || $data->subscribe ) ) ) )
		{
			return false;
		}

		// validate website
		if( empty( $comment->url ) && ( ( $config->get( 'show_website' ) == 2 && $config->get( 'require_website' ) == 2 ) || ( $profile->guest && $config->get( 'show_website' ) > 0 && $config->get( 'require_website' ) == 1 ) ) )
		{
			return false;
		}

		$tncGroup		= $config->get( 'show_tnc', '');
		if( !is_array( $tncGroup ) )
		{
			$tncGroup = explode( ',', $tncGroup );
		}
		$usergid		= $profile->getUsergroups();
		$requiresTnc	= false;

		foreach( $usergid as $gid )
		{
			if( in_array( $gid, $tncGroup ) )
			{
				$requiresTnc = true;
				break;
			}
		}

		if( $requiresTnc && !$data->tnc )
		{
			return false;
		}

		return true;
	}

	private function regexCheck( $comment, $data )
	{
		$config = Komento::getConfig();
		$profile = Komento::getProfile();

		if( !empty( $comment->email ) && $config->get( 'enable_email_regex' ) )
		{
			$regex = $config->get( 'email_regex' );

			if( is_array( $regex ) )
			{
				$regex = urldecode( $regex[0] );
			}

			$regex = str_replace( '/', '\\/', $regex );

			$pattern = '/' . $regex . '/';
			$subject = $comment->email;

			if( !preg_match( $pattern, $subject ) )
			{
				return JText::_( 'COM_KOMENTO_FORM_NOTIFICATION_EMAIL_INVALID');
			}
		}

		if( !empty( $comment->url ) && $config->get( 'enable_website_regex' ) )
		{
			$regex = $config->get( 'website_regex' );

			if( is_array( $regex ) )
			{
				$regex = urldecode( $regex[0] );
			}

			$regex = str_replace( '/', '\\/', $regex );

			$pattern = '/' . $regex . '/';
			$subject = $comment->url;

			if( !preg_match( $pattern, $subject ) )
			{
				return JText::_( 'COM_KOMENTO_FORM_NOTIFICATION_WEBSITE_INVALID');
			}
		}

		return true;
	}

	public function getSubscribeContent()
	{
		$component = JRequest::getCmd( 'component' );
		$cid = JRequest::getCmd( 'cid' );

		$user = Komento::getProfile();

		$subscribed = null;

		if( !$user->guest )
		{
			$subscriptionModel = Komento::getModel( 'subscription' );
			$subscribed = $subscriptionModel->checkSubscriptionExist( $component, $cid, $user->id );
		}

		$html = '';

		$theme = Komento::getTheme();
		$theme->set( 'my', $user );
		$theme->set( 'subscribed', $subscribed );

		$html = $theme->fetch( 'dialogs/subscribe.email.php' );

		$ajax = Komento::getAjax();

		$ajax->success( $html );
		$ajax->send();
	}

	public function subscribeUser()
	{
		$id = JRequest::getInt( 'user', null );

		$component = JRequest::getCmd( 'component' );
		$cid = JRequest::getCmd( 'cid' );

		$user = Komento::getProfile( $id );

		$state = $this->subscribe( 'comment', $component, $cid, 0, $id, $user->getName(), $user->email );

		$ajax = Komento::getAjax();

		if( $state )
		{
			$ajax->success();
		}
		else
		{
			$ajax->fail();
		}

		$ajax->send();
	}

	public function subscribeGuest()
	{
		$name = JRequest::getString( 'name' );
		$email = JRequest::getString( 'email' );

		$component = JRequest::getCmd( 'component' );
		$cid = JRequest::getCmd( 'cid' );

		$state = $this->subscribe( 'comment', $component, $cid, 0, 0, $name, $email );

		$ajax = Komento::getAjax();

		if( $state )
		{
			$ajax->success();
		}
		else
		{
			$ajax->fail();
		}

		$ajax->send();
	}

	public function unsubscribeUser()
	{
		$id = JRequest::getInt( 'user', null );
		$component = JRequest::getCmd( 'component' );
		$cid = JRequest::getCmd( 'cid' );

		$model = Komento::getModel( 'subscription' );

		$state = $model->unsubscribe( $component, $cid, $id );

		$ajax = Komento::getAjax();

		if( $state )
		{
			$ajax->success();
		}
		else
		{
			$ajax->fail();
		}

		$ajax->send();
	}
}
