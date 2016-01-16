<?php
/**
 * @package		Komento
 * @copyright	Copyright (C) 2011 Stack Ideas Private Limited. All rights reserved.
 * @license		GNU/GPL, see LICENSE.php
 *
 * Komento is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * See COPYRIGHT.php for copyright notices and details.
 */

defined('_JEXEC') or die('Restricted access');

jimport( 'joomla.filesystem.file' );

class KomentoEasySocialHelper
{
	private $file = null;

	private $config = null;
	private $konfig = null;

	public function __construct()
	{
		$lang		= JFactory::getLanguage();
		$lang->load( 'com_komento' , JPATH_ROOT );

		$this->file =  JPATH_ADMINISTRATOR . '/components/com_easysocial/includes/foundry.php';

		$this->konfig		= Komento::getKonfig();
		$this->config		= Komento::getConfig();
	}

	/**
	 * Determines if EasySocial is installed on the site.
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function exists()
	{
		static $loaded = false;

		if( $loaded )
		{
			return true;
		}

		jimport( 'joomla.filesystem.file' );

		if( !JFile::exists( $this->file ) )
		{
			return false;
		}

		require_once( $this->file );

		$loaded = true;

		return true;
	}

	/**
	 * Retrieves EasySocial's toolbar
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function getToolbar()
	{
		$toolbar 	= Foundry::get( 'Toolbar' );
		$output 	= $toolbar->render();

		return $output;
	}

	/**
	 * Initializes EasySocial
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function init()
	{
		static $loaded 	= false;

		if( $this->exists() && !$loaded )
		{
			require_once( $this->file );

			$document 	= JFactory::getDocument();

			if( $document->getType() == 'html' )
			{
				// We also need to render the styling from EasySocial.
				$doc 		= Foundry::document();
				$doc->init();

				$page 		= Foundry::page();
				$page->processScripts();

			}

			Foundry::language()->loadSite();

			$loaded 	= true;
		}

		return $loaded;
	}

	/**
	 * Get the Komento Comments app table object
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function getApp()
	{
		static $app = null;

		if( !isset( $app ) )
		{
			$table = Foundry::table( 'app' );
			$state = $table->load( array( 'element' => 'comments', 'type' => 'apps', 'group' => 'user' ) );

			if( !$state )
			{
				$app = false;
			}
			else
			{
				$app = $table;
			}
		}

		return $app;
	}

	/**
	 * Displays the user's points
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function getPoints( $id )
	{
		if( !$this->exists() )
		{
			return false;
		}

		$konfig		= Komento::getKonfig();
		$config		= Komento::getConfig();

		if( !$config->get( 'integrations_easysocial_points' ) )
		{
			return;
		}

		$theme 	= new CodeThemes();

		$user 	= Foundry::user( $id );

		$theme->set( 'user' , $user );
		$output = $theme->fetch( 'easysocial.points.php' );

		return $output;
	}

	/**
	 * Assign badge
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function assignBadge( $rule , $message , $creatorId = null )
	{
		if( !$this->exists() )
		{
			return false;
		}

		$creator 	= Foundry::user( $creatorId );

		$badge 	= Foundry::badges();
		$state 	= $badge->log( 'com_komento' , $rule , $creator->id , $message );

		return $state;
	}

	/**
	 * Assign points
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function assignPoints( $rule , $creatorId = null, $comment = null)
	{

		if( !$this->exists() )
		{
			return false;
		}

		$creator 	= Foundry::user( $creatorId );
		$points		= Foundry::points();

		if ($rule == 'komento.add.comment' || $rule == 'komento.reply.comment') {

			if (method_exists($points, 'getParams')) {
				$params	= $points->getParams($rule, 'com_komento');
				$length	= JString::strlen($comment->comment);
				$state 	= false;

				if ($params) {
					// Get the content length
					if ($length > $params->get('min')->value || $params->get('min')->value == 0) {

						$state 	= $points->assign($rule, 'com_komento', $creator->id);
					}
				} else {
					$state 	= $points->assign($rule, 'com_komento', $creator->id);
				}

			} else {
				$state 	= $points->assign($rule, 'com_komento', $creator->id);
			}



			return $state;
		}


		$state 		= $points->assign( $rule , 'com_komento' , $creator->id );

		return $state;
	}

	/**
	 * Creates a new stream for new comment post
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function createStream( $action, &$comment )
	{
		if( !$this->exists() )
		{
			return false;
		}

		$me = Foundry::user();

		$stream 	= Foundry::stream();
		$template 	= $stream->getTemplate();

		$template->setContext( $comment->id , 'komento' );
		$template->setContent( $comment->comment );

		$template->setVerb( $action );

		$template->setPublicStream( 'core.view' );

		$template->setType( 'full' );

		switch( $action )
		{
			case 'like':
				// Check if stream already exist
				$table = Foundry::table( 'streamitem' );
				$state = $table->load( array( 'actor_id' => $me->id, 'actor_type' => SOCIAL_TYPE_USER, 'context_type' => 'komento', 'context_id' => $comment->id, 'verb' => 'like' ) );

				if( $state )
				{
					return false;
				}

				$template->setActor( $me->id , SOCIAL_TYPE_USER );
				$template->setType( 'mini' );
				break;

			case 'comment':
				$template->setActor( $comment->created_by , SOCIAL_TYPE_USER );
				break;

			case 'reply':
				$template->setActor( $comment->created_by , SOCIAL_TYPE_USER );
				break;
		}


		$state 	= $stream->add( $template );

		if( $state )
		{
			$table = Foundry::table( 'streamitem' );
			$table->load( array( 'actor_id' => $me->id, 'actor_type' => SOCIAL_TYPE_USER, 'context_type' => 'komento', 'context_id' => $comment->id, 'verb' => 'comment' ) );

			if( empty( $comment->params->social ) )
			{
				$comment->params->social = new stdClass();
			}

			$comment->params->social->stream = $table->uid;

			$comment->store();
		}

		return $state;
	}

	public function injectComment( &$comment )
	{
		if( !$this->exists() )
		{
			return false;
		}

		$parent = Komento::getTable( 'comments' );
    	$state = $parent->load( $comment->parent_id );

		if( empty( $parent->params->social->stream ) )
		{
			return false;
		}

		$streamId = $parent->params->social->stream;

		$commentTable = Foundry::table( 'comments' );
		$commentTable->element = 'komento.user';
		$commentTable->created_by = $comment->created_by;
		$commentTable->comment = $comment->comment;
		$commentTable->uid = $streamId;

		$commentTable->params = (object) array( 'url' => FRoute::stream( array( 'layout' => 'item', 'id' => $streamId ) ), 'komento' => (object) array( 'source' => $comment->id ) );

		$state = $commentTable->store();

		if( $state )
		{
			if( empty( $comment->params->social ) )
			{
				$comment->params->social = new stdClass();
			}

			$comment->params->social->target = $commentTable->id;

			$comment->store();
		}
	}

	public function removeComment( $comment )
	{
		if( !$this->exists() )
		{
			return false;
		}

		if( !empty( $comment->params->social->stream ) )
		{
			$stream = Foundry::stream();
			$stream->delete( $comment->id, 'komento', $comment->created_by );
		}

		if( !empty( $comment->params->social->target ) )
		{
			$commentTable = Foundry::table( 'comments' );
			$state = $commentTable->load( $comment->params->social->target );

			if( $state )
			{
				$commentTable->delete();
			}
		}

		if( !empty( $comment->params->social->source ) )
		{
			$commentTable = Foundry::table( 'comments' );
			$state = $commentTable->load( $comment->params->social->source );

			if( $state )
			{
				$commentTable->delete();
			}
		}
	}

	public function injectLike( $comment )
	{
		if( !$this->exists() )
		{
			return false;
		}

		if( !empty( $comment->params->social->stream ) )
		{
			$likeTable = Foundry::table( 'likes' );

			$liked = $likeTable->load( array( 'type' => 'komento.user', 'uid' => $comment->params->social->stream, 'created_by' => Foundry::user()->id ) );

			if( !$liked )
			{
				$likeTable->type = 'komento.user';
				$likeTable->uid = $comment->params->social->stream;
				$likeTable->created_by = Foundry::user()->id;
				$likeTable->created = Foundry::date()->toSQL();

				$state = $likeTable->store();
			}
		}
	}

	public function removeLike( $comment )
	{
		if( !$this->exists() )
		{
			return false;
		}

		if( !empty( $comment->params->social->stream ) )
		{
			$likeTable = Foundry::table( 'likes' );

			$liked = $likeTable->load( array( 'type' => 'komento.user', 'uid' => $comment->params->social->stream, 'created_by' => Foundry::user()->id ) );

			if( $liked )
			{
				$likeTable->delete();
			}
		}
	}

	public function notify( $action, $comment )
	{
		if( !$this->exists() )
		{
			return false;
		}

		$targets = array();

		switch( $action )
		{
			case 'comment':
				$targets = array( 'author', 'usergroup', 'participant' );
			break;

			case 'reply':
				$targets = array( 'parent', 'author', 'usergroup', 'participant' );
			break;

			case 'like':
				$targets = array( 'owner', 'usergroup' );
			break;

			default:
				return false;
			break;
		}

		$actor = Foundry::user();

		$application = Komento::loadApplication( $comment->component )->load( $comment->cid );

		$pagelink = $application->getContentPermalink();
		$permalink = $pagelink . '#kmt-' . $comment->id;
		$title = $application->getContentTitle();

		$systemOptions = array(
			'uid'			=> $comment->id,
			'actor_id'		=> $actor->id,
			'type'			=> 'komento',
			'url'			=> $permalink,
			'image'			=> $actor->getAvatar( SOCIAL_AVATAR_LARGE ),
			'contentTitle'	=> $title,
			'owner'			=> $comment->created_by
		);

		$socialApp = $this->getApp();

		if( $socialApp )
		{
			$systemOptions['app_id'] = $socialApp->id;
		}

		$notified = array();

		// We always do not want action user to get notified
		$notified[] = $actor->id;

		foreach( $targets as $target )
		{
			$users = array_diff( $this->getNotificationTarget( $target, $action, $comment ), $notified );

			if( !empty( $users ) )
			{
				$systemOptions['target'] = $target;

				Foundry::notify( 'komento.' . $action, $users, false, $systemOptions );
			}

			$notified = array_merge( $notified, $users );
		}
	}

	public function getNotificationTarget( $target, $action, $comment )
	{
		$ids = array();

		switch( $target )
		{
			case 'usergroup':
				$gids = $this->config->get( 'notification_es_to_usergroup_' . $action );

				if( !empty( $gids ) )
				{
					if( !is_array( $gids ) )
					{
						$gids = explode( ',', $gids );
					}

					foreach( $gids as $gid )
					{
						$ids += Komento::getUsersByGroup( $gid );
					}
				}
			break;

			case 'author':
				if( $this->config->get( 'notification_es_to_author' ) )
				{
					$application = Komento::loadApplication( $comment->component )->load( $comment->cid );

					$author = $application->getAuthorId();

					if( !empty( $author ) )
					{
						$ids = array( $author );
					}
				}
			break;

			case 'parent':
				if( !empty( $comment->parent_id ) )
				{
					$parent = Komento::getTable( 'comments' );
				    $state = $parent->load( $comment->parent_id );

				     if( $state && !empty( $parent->created_by ) )
				     {
				      $ids = array( $parent->created_by );
				     }
				}
			break;

			case 'owner':
				if( !empty( $comment->created_by ) )
				{
					$ids = array( $comment->created_by );
				}
			break;

			case 'participant':
				if( $this->config->get( 'notification_es_to_participant' ) )
				{
					$options = array(
						'component' => $comment->component,
						'cid' => $comment->cid,
						'noguest' => true,
						'state' => 1
					);

					$model = Komento::getModel( 'comments' );
					$ids = $model->getUsers( $options );
				}
			break;
		}

		return $ids;
	}
}
