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

class DiscussEasySocialHelper
{
	static $file 	= null;
	private $exists	= false;
	private $config = null;

	public function __construct()
	{
		$lang		= JFactory::getLanguage();
		$lang->load( 'com_easydiscuss' , JPATH_ROOT );

		self::$file 		= JPATH_ADMINISTRATOR . '/components/com_easysocial/includes/foundry.php';

		$this->exists		= $this->exists();
		$this->config		= DiscussHelper::getConfig();
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
		jimport( 'joomla.filesystem.file' );

		$file 	= JPATH_ADMINISTRATOR . '/components/com_easysocial/includes/foundry.php';

		if( !JFile::exists( $file ) )
		{
			return false;
		}

		include_once( $file );

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
		$this->init();

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
	public static function init()
	{
		static $loaded 	= false;

		if( !$loaded )
		{
			require_once( self::$file );

			$document 	= JFactory::getDocument();

			if( $document->getType() == 'html' )
			{
				// We also need to render the styling from EasySocial.
				$doc 		= Foundry::document();
				$doc->init();

				$page 		= Foundry::page();
				$page->processScripts();
			}

			Foundry::language()->load( 'com_easysocial' , JPATH_ROOT );

			$loaded 	= true;
		}

		return $loaded;
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
		$config = EasyBlogHelper::getConfig();

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
	 * Displays comments
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function getCommentHTML( $blog )
	{
		if( !$this->exists() )
		{
			return;
		}

		Foundry::language()->load( 'com_easysocial' , JPATH_ROOT );

		$url 		= EasyBlogRouter::_( 'index.php?option=com_easyblog&view=entry&id=' . $blog->id );
		$comments 	= Foundry::comments( $blog->id , 'blog' , SOCIAL_APPS_GROUP_USER , $url );

		$theme 	= new CodeThemes();
		$theme->set( 'blog' , $blog );
		$theme->set( 'comments' , $comments );
		$output 	= $theme->fetch( 'easysocial.comments.php' );

		return $output;
	}

	/**
	 * Returns the comment counter
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function getCommentCount( $blog )
	{
		if( !$this->exists() )
		{
			return;
		}

		Foundry::language()->load( 'com_easysocial' , JPATH_ROOT );

		$url 		= EasyBlogRouter::_( 'index.php?option=com_easyblog&view=entry&id=' . $blog->id );
		$comments 	= Foundry::comments( $blog->id , 'blog' , SOCIAL_APPS_GROUP_USER , $url );

		return $comments->getCount();
	}

	/**
	 * Assign badge
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function assignBadge( $rule , $creatorId , $message )
	{
		if( !$this->exists() )
		{
			return false;
		}

		$creator 	= Foundry::user( $creatorId );

		$badge 	= Foundry::badges();
		$state 	= $badge->log( 'com_easydiscuss' , $rule , $creator->id , $message );

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
	public function assignPoints( $rule , $creatorId = null, $post = null)
	{
		if( !$this->exists() || !$this->config->get( 'integration_easysocial_points' ) )
		{
			return false;
		}

		// Since all the "rule" in EasyDiscuss is prepended with discuss. , we need to remove it
		$rule 		= str_ireplace( 'easydiscuss.' , '' , $rule );
		$creator 	= Foundry::user( $creatorId );

		$points		= Foundry::points();

		if ($rule == 'new.comment') {
			$params	= $points->getParams('new.comment', 'com_easydiscuss');

			$length	= JString::strlen($post->comment);
			$state 	= false;

			if (!$params) {

				// For earlier versions of EasySocial
				// This could be older version of EasySocial
				$points->assign($rule, 'com_easydiscuss', $creator->id);
			} else {
				$min = isset($params->get('min')->value) ? $params->get('min')->value : $params->get('min')->default;

				// Get the content length
				if ($length > $min || $min == 0) {
					$state 	= $points->assign($rule , 'com_easydiscuss', $creator->id );
				}
			}

			return $state;
		}

		if ($rule == 'new.reply') {

			$params 	= false;

			if (method_exists($points, 'getParams')) {
				$params	= $points->getParams('new.reply', 'com_easydiscuss');
			}

			$length	= JString::strlen($post->content);
			$state 	= false;

			if (!$params) {
				// For earlier versions of EasySocial
				// This could be older version of EasySocial
				$points->assign($rule, 'com_easydiscuss', $creator->id);
			} else {
			// Get the content length
			if ($length > $params->get('min')->value || $params->get('min')->value == 0) {
				$state 	= $points->assign($rule, 'com_easydiscuss', $creator->id);
			}
		}

			return $state;
		}

		$state 		= $points->assign( $rule , 'com_easydiscuss' , $creator->id );

		return $state;
	}

	/**
	 * Creates a new stream for new discussion
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function createDiscussionStream( $post )
	{
		if( !$this->exists() || !$this->config->get( 'integration_easysocial_activity_new_question' ) )
		{
			return;
		}

		$stream 	= Foundry::stream();
		$template 	= $stream->getTemplate();

		$category 	= DiscussHelper::getTable( 'Category' );
		$category->load( $post->category_id );

		$post->cat 	= $category;

		// Get the stream template
		$template->setActor( $post->user_id , SOCIAL_TYPE_USER );
		$template->setContext( $post->id , 'discuss' , $post );
		$template->setContent( $post->content );

		$template->setVerb( 'create' );
		$template->setPublicStream( 'core.view' );

		$state 	= $stream->add( $template );

		return $state;
	}

	/**
	 * Creates a new stream for new replies
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function replyDiscussionStream( $post )
	{

		if( !$this->exists() || !$this->config->get( 'integration_easysocial_activity_reply_question' ) )
		{
			return;
		}

		$stream 	= Foundry::stream();
		$template 	= $stream->getTemplate();
		$question 	= DiscussHelper::getTable( 'Post' );
		$question->load( $post->parent_id );

		$rawPost 	= DiscussHelper::getTable( 'Post' );
		$rawPost->load( $post->id );

		$category 	= DiscussHelper::getTable( 'Category' );
		$category->load( $question->category_id );

		$obj 			= new stdClass();
		$obj->post 		= $rawPost;
		$obj->question	= $question;
		$obj->cat 		= $category;

		// Get the stream template
		$template->setActor( $post->user_id , SOCIAL_TYPE_USER );
		$template->setContext( $post->id , 'discuss' , $obj );
		$template->setContent( $rawPost->content );

		$template->setVerb( 'reply' );

		$template->setPublicStream( 'core.view' );
		$state 	= $stream->add( $template );

		return $state;
	}

	/**
	 * Creates a new stream for new replies
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function commentDiscussionStream( $comment , $post , $question )
	{
		if( !$this->exists() || !$this->config->get( 'integration_easysocial_activity_comment' ) )
		{
			return;
		}

		$stream 	= Foundry::stream();
		$template 	= $stream->getTemplate();

		$obj 			= new stdClass();
		$obj->comment 	= $comment;
		$obj->post 		= $post;
		$obj->question	= $question;

		// Get the stream template
		$template->setActor( $comment->user_id , SOCIAL_TYPE_USER );
		$template->setContext( $comment->id , 'discuss' , $obj );
		$template->setContent( $comment->comment );

		$template->setVerb( 'comment' );

		$template->setPublicStream( 'core.view' );
		$state 	= $stream->add( $template );

		return $state;
	}

	/**
	 * Creates a new stream for new likes
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function likesStream( $post , $question )
	{
		if( !$this->exists() || !$this->config->get( 'integration_easysocial_activity_likes' ) )
		{
			return;
		}

		$stream 	= Foundry::stream();
		$template 	= $stream->getTemplate();
		$actor 		= Foundry::user();

		$obj 			= new stdClass();
		$obj->post		= $post;
		$obj->question	= $question;

		// Get the stream template
		$template->setActor( $actor->id , SOCIAL_TYPE_USER );
		$template->setContext( $post->id , 'discuss' , $obj );
		$template->setContent( $post->content );

		$template->setVerb( 'likes' );

		$template->setPublicStream( 'core.view' );
		$state 	= $stream->add( $template );

		return $state;
	}

	/**
	 * Creates a new stream for new likes
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function rankStream( $rank )
	{
		if( !$this->exists() || !$this->config->get( 'integration_easysocial_activity_ranks' ) )
		{
			return;
		}

		$stream 	= Foundry::stream();
		$template 	= $stream->getTemplate();

		$obj 			= new stdClass();
		$obj->id 		= $rank->rank_id;
		$obj->user_id 	= $rank->user_id;
		$obj->title		= $rank->title;

		// Get the stream template
		$template->setActor( $rank->user_id , SOCIAL_TYPE_USER );
		$template->setContext( $rank->rank_id , 'discuss' , $obj );
		$template->setContent( $rank->title );

		$template->setVerb( 'ranks' );

		$template->setPublicStream( 'core.view' );
		$state 	= $stream->add( $template );

		return $state;
	}

	/**
	 * Creates a new stream for new likes
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function favouriteStream( $post )
	{
		if( !$this->exists() || !$this->config->get( 'integration_easysocial_activity_favourite' ) )
		{
			return;
		}

		$stream 	= Foundry::stream();
		$template 	= $stream->getTemplate();

		// Get the stream template
		$template->setActor( Foundry::user()->id , SOCIAL_TYPE_USER );
		$template->setContext( $post->id , 'discuss' , $post );
		$template->setContent( $post->title );

		$template->setVerb( 'favourite' );

		$template->setPublicStream( 'core.view' );
		$state 	= $stream->add( $template );

		return $state;
	}

	/**
	 * Creates a new stream for accepted items
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function acceptedStream( $post , $question )
	{
		if( !$this->exists() || !$this->config->get( 'integration_easysocial_activity_accepted' ) )
		{
			return;
		}

		$stream 	= Foundry::stream();
		$template 	= $stream->getTemplate();

		$obj 			= new stdClass();
		$obj->post		= $post;
		$obj->question	= $question;

		// Get the stream template
		$template->setActor( $post->user_id , SOCIAL_TYPE_USER );
		$template->setContext( $post->id , 'discuss' , $obj );
		$template->setContent( $post->title );

		$template->setVerb( 'accepted' );

		$template->setPublicStream( 'core.view' );
		$state 	= $stream->add( $template );

		return $state;
	}

	/**
	 * Creates a new stream for new likes
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function voteStream( $post )
	{
		if( !$this->exists() || !$this->config->get( 'integration_easysocial_activity_vote' ) )
		{
			return;
		}

		$stream 	= Foundry::stream();
		$template 	= $stream->getTemplate();

		// The actor should always be the person that is voting.
		$my 		= Foundry::user();

		// Get the stream template
		$template->setActor( $my->id , SOCIAL_TYPE_USER );
		$template->setContext( $post->id , 'discuss' , $post );
		$template->setContent( $post->title );

		$template->setVerb( 'vote' );

		$template->setPublicStream( 'core.view' );
		$state 	= $stream->add( $template );

		return $state;
	}

	private function getRecipients( $action , $post )
	{
		$recipients 	= array();

		if( $action == 'new.discussion' )
		{
			$rows 	= DiscussHelper::getHelper( 'Mailer' )->getSubscribers( 'site', 0, 0 , array() , array( $post->user_id ) );

			if( !$rows )
			{
				return false;
			}

			foreach( $rows as $row )
			{
				// We don't want to add the owner of the post to the recipients
				if( $row->userid != $post->user_id )
				{
					$recipients[]	= $row->userid;
				}
			}

			return $recipients;
		}

		if( $action == 'new.reply' )
		{
			// Get all users that are subscribed to this post
			$model	= DiscussHelper::getModel( 'Posts' );
			$rows	= $model->getParticipants( $post->parent_id );

			if( !$rows )
			{
				return false;
			}

			// Add the thread starter into the list of participants.
			$question 	= DiscussHelper::getTable( 'Post' );
			$question->load( $post->parent_id );

			$rows[]		= $question->user_id;

			foreach( $rows as $id )
			{
				if( $id != $post->user_id )
				{
					$recipients[]	= $id;
				}
			}

			return $recipients;
		}
	}

	/**
	 * Retrieves the popbox code for avatars
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function getPopbox( $userId )
	{
		if( !$this->exists() || !$this->config->get( 'integration_easysocial_popbox' ) || !$userId )
		{
			return;
		}

		// Initialize our script
		$this->init();

		$popbox 	= ' data-user-id="' . $userId . '" data-popbox="module://easysocial/profile/popbox" ';

		return $popbox;
	}

	/**
	 * Notify site subscribers whenever a new blog post is created
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function notify( $action , $post , $question = null , $comment = null , $actor = null )
	{
		if( !$this->exists() )
		{
			return;
		}

		// We don't want to notify via e-mail
		$emailOptions 	= false;
		$recipients 	= array();
		$rule 			= '';

		$recipients 	= $this->getRecipients( $action , $post );

		if( $action == 'new.discussion' )
		{
			if( !$this->config->get( 'integration_easysocial_notify_create' ) )
			{
				return;
			}

			if( !$recipients )
			{
				return;
			}

			$permalink 		= DiscussRouter::_( 'index.php?option=com_easydiscuss&view=post&id=' . $post->id );
			$image 			= '';

			$options 	= array( 'actor_id' => $post->user_id , 'uid' => $post->id , 'title' => JText::sprintf( 'COM_EASYDISCUSS_EASYSOCIAL_NOTIFICATION_NEW_POST' , $post->title ) , 'type' => 'discuss' , 'url' => $permalink );

			$rule 		= 'discuss.create';
		}

		if( $action == 'new.reply' )
		{
			if( !$this->config->get( 'integration_easysocial_notify_reply' ) )
			{
				return;
			}

			if( !$recipients )
			{
				return;
			}

			$permalink	 	= DiscussRouter::_( 'index.php?option=com_easydiscuss&view=post&id=' . $question->id );

			$options 	= array( 'actor_id' => $post->user_id , 'uid' => $post->id , 'title' => JText::sprintf( 'COM_EASYDISCUSS_EASYSOCIAL_NOTIFICATION_REPLY' , $question->title ) , 'type' => 'discuss' , 'url' => $permalink );

			$rule 		= 'discuss.reply';
		}

		if( $action == 'new.comment' )
		{
			if( !$this->config->get( 'integration_easysocial_notify_comment' ) )
			{
				return;
			}

			// The recipient should only be the post owner
			$recipients 	= array( $post->user_id );

			$permalink	 	= DiscussRouter::_( 'index.php?option=com_easydiscuss&view=post&id=' . $question->id ) . '#' . JText::_('COM_EASYDISCUSS_REPLY_PERMALINK') . '-' . $post->id;

			$content 	= JString::substr( $comment->comment , 0 , 25 ) . '...';
			$options 	= array( 'actor_id' => $comment->user_id , 'uid' => $comment->id , 'title' => JText::sprintf( 'COM_EASYDISCUSS_EASYSOCIAL_NOTIFICATION_COMMENT' , $content ) , 'type' => 'discuss' , 'url' => $permalink );

			$rule 		= 'discuss.comment';
		}

		if( $action == 'accepted.answer' )
		{
			if( !$this->config->get( 'integration_easysocial_notify_accepted' ) )
			{
				return;
			}

			// The recipient should only be the post owner
			$recipients 	= array( $post->user_id );

			$permalink	 	= DiscussRouter::_( 'index.php?option=com_easydiscuss&view=post&id=' . $question->id ) . '#answer';

			$options 	= array( 'actor_id' => $actor , 'uid' => $post->id , 'title' => JText::sprintf( 'COM_EASYDISCUSS_EASYSOCIAL_NOTIFICATION_ACCEPTED_ANSWER' , $question->title ) , 'type' => 'discuss' , 'url' => $permalink );

			$rule 		= 'discuss.accepted';
		}

		if( $action == 'accepted.answer.owner' )
		{
			if( !$this->config->get( 'integration_easysocial_notify_accepted' ) )
			{
				return;
			}

			// The recipient should only be the post owner
			$recipients 	= array( $question->user_id );

			$permalink	 	= DiscussRouter::_( 'index.php?option=com_easydiscuss&view=post&id=' . $question->id ) . '#answer';

			$options 	= array( 'actor_id' => $actor , 'uid' => $post->id , 'title' => JText::sprintf( 'COM_EASYDISCUSS_EASYSOCIAL_NOTIFICATION_ACCEPTED_ANSWER_OWNER' , $question->title ) , 'type' => 'discuss' , 'url' => $permalink );

			$rule 		= 'discuss.accepted.owner';
		}

		if( $action == 'new.likes' )
		{
			if( !$this->config->get( 'integration_easysocial_notify_likes' ) )
			{
				return;
			}

			// The recipient should only be the post owner
			$recipients 	= array($post->user_id);

			$permalink	 	= DiscussRouter::_( 'index.php?option=com_easydiscuss&view=post&id=' . $question->id ) . '#' . JText::_('COM_EASYDISCUSS_REPLY_PERMALINK') . '-' . $post->id;

			$options 	= array( 'actor_id' => JFactory::getUser()->id , 'uid' => $post->id , 'title' => JText::_( 'COM_EASYDISCUSS_EASYSOCIAL_NOTIFICATION_LIKES' ) , 'type' => 'discuss' , 'url' => $permalink );

			$rule 		= 'discuss.likes';
		}

		if( empty( $rule ) )
		{
			return false;
		}

		// Send notifications to the receivers when they unlock the badge
		Foundry::notify( $rule , $recipients , $emailOptions , $options );
	}

	/**
	 * Creates a new stream for new comments in EasyBlog
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function addIndexerNewBlog( $blog )
	{
		if (!class_exists('Foundry')) return;

		$config 	= EasyBlogHelper::getConfig();

		$indexer 	= Foundry::get( 'Indexer', 'com_easyblog' );
		$template 	= $indexer->getTemplate();

		// getting the blog content
		$content 	= $blog->intro . $blog->content;


		$image 		= '';

		// @rule: Try to get the blog image.
		if( $blog->getImage() )
		{
			$image 	= $blog->getImage()->getSource( 'small' );
		}

		if( empty( $image ) )
		{
			// @rule: Match images from blog post
			$pattern	= '/<\s*img [^\>]*src\s*=\s*[\""\']?([^\""\'\s>]*)/i';
			preg_match( $pattern , $content , $matches );

			$image		= '';

			if( $matches )
			{
				$image		= isset( $matches[1] ) ? $matches[1] : '';

				if( JString::stristr( $matches[1], 'https://' ) === false && JString::stristr( $matches[1], 'http://' ) === false && !empty( $image ) )
				{
					$image	= rtrim(JURI::root(), '/') . '/' . ltrim( $image, '/');
				}
			}
		}

		if(! $image )
		{
			$image = rtrim( JURI::root() , '/' ) . '/components/com_easyblog/assets/images/default_facebook.png';
		}

		$content    = strip_tags( $content );

		if( JString::strlen( $content ) > $config->get( 'integrations_easysocial_indexer_newpost_length', 250 ) )
		{
			$content = JString::substr( $content, 0, $config->get( 'integrations_easysocial_indexer_newpost_length', 250 ) );
		}
		$template->setContent( $blog->title, $content );

		$url = EasyBlogRouter::_('index.php?option=com_easyblog&view=entry&id='.$blog->id);

		$template->setSource($blog->id, 'blog', $blog->created_by, $url);

		$template->setThumbnail( $image );

		$template->setLastUpdate( $blog->modified );

		$state = $indexer->index( $template );
		return $state;
	}

	public function deleteDiscussStream($post)
	{
		if (!$this->exists() || !$this->config->get('integration_easysocial_activity_new_question')) {
			return;
		}

		$stream = Foundry::stream();
		$state = $stream->delete($post->id, 'discuss');

		return $state;
	}


}
