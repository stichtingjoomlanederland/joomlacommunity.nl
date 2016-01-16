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

// Include main constants and helpers
require_once JPATH_ROOT . '/components/com_easydiscuss/constants.php';
require_once JPATH_ROOT . '/components/com_easydiscuss/helpers/helper.php';
require_once JPATH_ROOT . '/components/com_content/helpers/route.php';

jimport( 'joomla.plugin.plugin' );

class plgContentEasyDiscuss extends JPlugin
{
	var $extension	= null;
	var $view		= null;
	var $loaded		= null;

	public function plgContentEasyDiscuss( &$subject , $params )
	{
		$this->extension	= JRequest::getString( 'option' );
		$this->view			= JRequest::getString( 'view' );

		// Load language file for use throughout the plugin
		JFactory::getLanguage()->load( 'com_easydiscuss' , JPATH_ROOT );

		parent::__construct( $subject, $params );
	}

	/**
	 * Needed to update the content of the discussion whenever the article is being edited and saved.
	 */
	function onAfterContentSave( &$article, $isNew )
	{
		// If the current page is easydiscuss, we want to skip this altogether.
		// We also need to skip this when the plugins are being triggered in the discussion replies otherwise it will
		// be in an infinite loop generating all contents.
		if( $this->extension == 'com_easydiscuss' || $this->loaded || ( isset( $article->easydiscuss ) && $article->easydiscuss == true ) || ( isset($article->state) && !$article->state && $this->extension == 'com_content') )
		{
			return;
		}

		$this->mapExisting( $article );

		return true;
	}

	/**
	 * onContentAfterSave trigger for Joomla 1.6 onwards.
	 *
	 **/
	public function onContentAfterSave($context, &$article, $isNew)
	{
		return $this->onAfterContentSave( $article , $isNew );
	}

	/**
	 * onContentAfterDisplay trigger for Joomla 1.6 onwards.
	 *
	 **/
	function onContentAfterDisplay( $context , &$article, &$params, $page = 0 )
	{
		return $this->onAfterDisplayContent( $article , $params , $page );
	}

	/**
	 * Triggers for EasyBlog.
	 */
	public function onDisplayComments( &$blog , &$params )
	{
		$blog->catid = $blog->category_id;

		return $this->onAfterDisplayContent( $blog , $params , 0 , __FUNCTION__ );
	}

	/**
	 * Triggered after the content is displayed.
	 *
	 */
	function onAfterDisplayContent( &$article, &$articleParams, $limitstart , $trigger = '' )
	{
		// If the current page is easydiscuss, we want to skip this altogether.
		// We also need to skip this when the plugins are being triggered in the discussion replies otherwise it will
		// be in an infinite loop generating all contents.
		if( $this->extension == 'com_easydiscuss' || $this->loaded || ( isset( $article->easydiscuss ) && $article->easydiscuss == true ) || ( $this->extension == 'com_content' && !$article->state ) )
		{
			return;
		}

		if( $this->extension == 'com_easyblog' && empty($trigger) )
		{
			return;
		}

		$app	= JFactory::getApplication();
		$params = $this->getParams();

		$allowed	= $params->get( 'allowed_components' , 'com_content,com_easyblog');
		$allowed	= explode( ',' , $allowed );

		if( !in_array( $this->extension , $allowed ) || !$article->id )
		{
			return '';
		}

		// @rule: Test for exclusions on the categories
		$excludedCategories	= $params->get( 'exclude_category' );

		if( !is_array( $excludedCategories ) )
		{
			$excludedCategories	= explode(',' , $excludedCategories);
		}

		if( in_array( $article->catid , $excludedCategories ) )
		{
			return '';
		}

		// @rule: Test for exclusions on the article id.
		$excludedArticles	= trim( $params->get( 'exclude_articles' ) );

		if( !empty( $excludedArticles ) )
		{
			$excludedArticles	= explode( ',' , $excludedArticles );

			if( in_array( $article->id , $excludedArticles ) )
			{
				return '';
			}
		}

		// @rule: Test for inclusions on the categories
		$allowedCategories	= $params->get( 'include_category' );

		if( is_array( $allowedCategories ) )
		{
			$allowedCategories	= implode(',' , $allowedCategories);
		}

		$allowedCategories 	= trim( $allowedCategories );

		if( $allowedCategories != 'all' && !empty( $allowedCategories ) && $this->extension == 'com_content' )
		{
			$allowedCategories 	= explode( ',' , $allowedCategories );

			if( !in_array( $article->catid , $allowedCategories ) )
			{
				return '';
			}
		}

		// Get the mapping
		$ref		= DiscussHelper::getTable( 'PostsReference' );
		$exists 	= $ref->loadByExtension( $article->id , $this->extension );

		if( !$exists )
		{
			// Map the article into EasyDiscuss
			$this->mapExisting( $article );

			$ref		= DiscussHelper::getTable( 'PostsReference' );
			$ref->loadByExtension( $article->id , $this->extension );
		}

		// Load the discussion item
		$post		= DiscussHelper::getTable( 'Post' );
		$post->load( $ref->post_id );

		if( !$post->published )
		{
			return;
		}

		// Load css file
		$this->attachHeaders();

		if( $this->isFrontpage() )
		{
			$this->addFrontpageTools( $article , $post );
		}
		else
		{
			$this->loaded	= true;

			// Show normal discussions data
			$html = $this->addResponses( $article , $post );

			if( $this->extension == 'com_easyblog' )
			{
				return $html;
			}
		}

		return '';
	}

	/**
	 * Retrieves Joomla's version
	 */
	function getJoomlaVersion()
	{
		$jVerArr   = explode('.', JVERSION);
		$jVersion  = $jVerArr[0] . '.' . $jVerArr[1];

		return $jVersion;
	}

	/**
	 * Retrieves parameter plugins.
	 *
	 * @access 	public
	 * @param 	null
	 * @return 	JParameter		JParameter object from Joomla.
	 */
	public function getParams()
	{
		static $params 	= null;

		if( !$params )
		{
			$plugin 		= JPluginHelper::getPlugin( 'content', 'easydiscuss' );
			$params 		= DiscussHelper::getRegistry( $plugin->params );
		}

		return $params;
	}

	/**
	 * Attaches the plugin's css file.
	 *
	 * @access 	public
	 * @param 	null
	 * @return 	boolean 	True on success, false otherwise.
	 */
	private function attachHeaders()
	{
		static $loaded 	= false;

		if( !$loaded )
		{
			DiscussHelper::loadHeaders();
			DiscussHelper::loadThemeCss();

			$doc 	= JFactory::getDocument();
			$path 	= rtrim( JURI::root() , '/' ) . '/plugins/content/easydiscuss/css/styles.css';

			$doc->addStyleSheet( $path );

			$loaded 	= true;
		}
		return $loaded;
	}

	private function isFrontpage()
	{
		switch( $this->extension )
		{
			case 'com_content':
				return ( $this->view == 'frontpage' ) || $this->view == 'featured';
			break;
			case 'com_k2':
				return $this->view == 'latest' || $this->view == 'itemlist';
			break;
			case 'com_easyblog':
				return ( $this->view == 'latest' );
			break;
		}
		return false;
	}

	/**
	 * Adds some nifty contents into the frontpage listing of com_content.
	 *
	 * @access 	public
	 * @param 	stdclass $article 		The standard Joomla article object.
	 * @param 	DiscussTablePost $post 	EasyDiscuss DiscussTablePost object.
	 * @return 	stdclass 				The Joomla article object.
	 **/
	public function addFrontpageTools( &$article , &$post )
	{
		$params 	= $this->getParams();

		// Just return if it's not needed.
		if( !$params->get( 'frontpage_tools' , true ) )
		{
			return $article;
		}

		$total		= $post->getReplyCount();
		$url		= $this->getArticleURL( $article );
		$hits 		= $this->getArticleHits( $article );
		$config 	= DiscussHelper::getConfig();
		$my 		= JFactory::getUser();

		ob_start();
		include( $this->getTemplatePath( 'frontpage.php' ) );
		$contents 	= ob_get_contents();
		ob_end_clean();

		// EasyBlog specifically uses 'text'
		if( $this->extension == 'com_easyblog' )
		{
			$article->text		.= $contents;
			return $article;
		}

		if( $this->getJoomlaVersion() >= '1.6' )
		{
			$article->introtext		.= $contents;
		}
		else
		{
			$article->text 			.= $contents;
		}
		return $article;
	}

	/**
	 * Returns the formatted date which is required during the output.
	 * The resultant date includes the offset.
	 *
	 * @access 	public
	 * @param 	string $format 		The date format.
	 * @param 	string $dateString 	The date result.
	 * @return 	string 				The formatted date result.
	 */
	public function formatDate( $format , $dateString )
	{
		return DiscussDateHelper::toFormat($dateString, $format);
	}

	/**
	 * Attaches the response and form in the article.
	 *
	 * @access 	public
	 * @param 	stdclass			$article 		The standard object from the article.
	 * @param 	DiscussTablePost	$post 			The post table.
	 * @return 	string 				The formatted date result.
	 */
	public function addResponses( &$article , &$post )
	{
		$params 	= $this->getParams();
		$count		= $post->getReplyCount();

		require_once DISCUSS_HELPERS . '/vote.php';

		if( !class_exists( 'EasyDiscussModelPosts' ) )
		{
			require_once DISCUSS_MODELS . '/posts.php';
		}

		$model 		= DiscussHelper::getModel( 'Posts' );

		$repliesLimit	= $params->get( 'items_count' , 5 );
		$totalReplies	= $model->getTotalReplies( $post->id );

		$hasMoreReplies	= false;

		$limitstart		= null;
		$limit			= null;

		if( $repliesLimit )
		{
			$limit		= $repliesLimit;

			$hasMoreReplies = ( $totalReplies - $repliesLimit ) > 0;
		}

		$replies 	= $model->getReplies( $post->id , 'latest' , $limitstart , $limit );

		$config 	= DiscussHelper::getConfig();
		$my 		= JFactory::getUser();
		$acl 		= DiscussHelper::getHelper( 'ACL' );

		$readMoreURI	= '';

		$readMoreURI	= JURI::getInstance()->toString();
		$delimiteter	= JString::strpos($readMoreURI, '&') ? '&' : '?';
		$readMoreURI	= $hasMoreReplies ? $readMoreURI . $delimiteter . 'viewallreplies=1' : $readMoreURI;
		
		// Get the likes authors.
		$post->likesAuthor	= DiscussHelper::getHelper( 'Likes' )->getLikesHTML( $post->id , $my->id , 'post' );

		// Get composer
		require_once DISCUSS_CLASSES . '/composer.php';
		$composer = new DiscussComposer( "replying" , $post );

		$sort			= JRequest::getString('sort', DiscussHelper::getDefaultRepliesSorting() );

		$isMainLocked 	= false;
		$canDeleteReply = false;

		// Load the category.
		$category	= DiscussHelper::getTable( 'Category' );
		$category->load( (int) $post->category_id );

		$canReply   	= ((($my->id != 0) || ($my->id == 0 && $config->get('main_allowguestpost' ) ) ) && $acl->allowed('add_reply', '0') ) ? true : false;
		$replies 		= DiscussHelper::formatReplies( $replies , $category );

		$system 		= new stdClass();
		$system->config	= DiscussHelper::getConfig();
		$system->my 	= $my;
		$system->acl 	= $acl;

		ob_start();
		include( dirname(__FILE__) . '/tmpl/default.php' );
		$contents	= ob_get_contents();
		ob_end_clean();

		$article->text	.= $contents;


		return $contents;
	}

	/**
	 * Returns the URL to a specific article in Joomla.
	 *
	 * @access 	public
	 * @param 	stdclass 	$article 	The standard Joomla article object.
	 * @return 	string 					The formatted url to the article.
	 */
	private function getArticleURL( &$article )
	{
		$uri		= JURI::getInstance();


		switch( $this->extension )
		{
			 case 'com_content':

				JTable::addIncludePath( JPATH_ROOT . '/libraries/joomla/database/table' );

				$category	= JTable::getInstance( 'Category' , 'JTable' );
				$category->load( $article->catid );

				if( $this->getJoomlaVersion() < '1.6' )
				{
					$section	= JTable::getInstance( 'Section' , 'JTable' );
					$section->load( $article->sectionid );

					 $url	= ContentHelperRoute::getArticleRoute( $article->id . ':' . $article->alias , $article->catid . ':' . $category->alias , $article->sectionid . ':' . $section->alias );
				}
				else
				{
					$url		= ContentHelperRoute::getArticleRoute( $article->id . ':' . $article->alias , $article->catid . ':' . $category->alias );
				}

				$url = $url . '#discuss-' . $article->id;

				return $uri->toString( array('scheme', 'host', 'port')) . '/' . ltrim( $url , '/' );

			 break;
			 case 'com_easyblog':
				require_once( JPATH_ROOT . '/components/com_easyblog/constants.php' );
				require_once( EBLOG_HELPERS . '/helper.php' );

				return EasyBlogRouter::getRoutedURL( 'index.php?option=com_easyblog&view=entry&id=' . $article->id, false, true );
			 break;
			 case 'com_k2':
				require_once( JPATH_ROOT . '/components/com_k2/helpers/route.php' );

				JTable::addIncludePath( JPATH_ROOT . '/libraries/joomla/database/table' );

				$category	= JTable::getInstance( 'Category' , 'JTable' );
				$category->load( $article->catid );

				$url		= K2HelperRoute::getItemRoute( $article->id . ':' . $article->alias , $article->catid . ':' . $category->alias );

				$url = $url . '#discuss-' . $article->id;

				return $uri->toString( array('scheme', 'host', 'port')) . '/' . ltrim( $url , '/' );
			 break;
		}
	}

	/**
	 * Gets the total hit count for the specific article.
	 *
	 * @access 	private
	 * @param 	stdclass	$article 	The article object.
	 * @return 	int 					The total hits for the specific article.
	 */
	private function getArticleHits( &$article )
	{
		$db 	= DiscussHelper::getDBO();
		$query	= 'SELECT ' . $db->nameQuote( 'hits' ) . ' FROM ' . $db->nameQuote( '#__content' ) . ' '
				. 'WHERE ' . $db->nameQuote( 'id' ) . '=' . $db->Quote( $article->id );
		$db->setQuery( $query );
		$hits 	= (int) $db->loadResult();

		return $hits;
	}

	public function mapExisting( &$article )
	{
		// @rule: If article is not published, do not try to process anything
		if( $this->extension == 'com_easydiscuss' || ( !$article->state && $this->extension == 'com_content') )
		{
			return false;
		}

		$ref	= DiscussHelper::getTable( 'PostsReference' );
		$exists	= $ref->loadByExtension( $article->id , $this->extension );
		$isNew	= !$exists;

		// @rule: Only append discussions that are already added into the reference table.
		$post	= $this->createDiscussion( $article , $isNew );

		if( !$exists )
		{
			// @rule: Store the references
			$ref->set( 'post_id' , $post->get( 'id' ) );
			$ref->set( 'reference_id', $article->id );
			$ref->set( 'extension', $this->extension );
			$ref->store();
		}
	}

	public function getTemplatePath( $file )
	{
		return dirname( __FILE__ ) . '/tmpl/' . $file;
	}

	/**
	 * Creates a new discussion in EasyDiscuss so that we can link the article and the content.
	 *
	 * @access 	public
	 * @param 	stdclass 	$article 	The standard Joomla article object.
	 * @return 	DiscussTablePost 		EasyDiscuss post table.
	 */
	public function createDiscussion( &$article , $isNew = true )
	{
		$post	= DiscussHelper::getTable( 'Post' );
		$params = $this->getParams();

		if( !$isNew )
		{
			// Get the mapping
			$ref		= DiscussHelper::getTable( 'PostsReference' );
			$ref->loadByExtension( $article->id , $this->extension );

			// Load the discussion item
			$post->load( $ref->post_id );
		}

		// @rule: Set the category
		$post->set( 'category_id' , $params->get( 'category_storage' , 1 ) );

		// @rule: Set the discussion title
		$post->set( 'title' , $article->title );

		// @rule: Set the creation date
		$post->set( 'created', $article->created );

		// @rule: Set the publishing state
		$post->set( 'published' , DISCUSS_ID_PUBLISHED );

		// @rule: Set the modified date
		$post->set( 'modified', $article->modified );

		// @rule: Set the user id
		$post->set( 'user_id' , $article->created_by );

		// @rule: Set the user type
		$post->set( 'user_type', 'member' );

		// @rule: Set the hits
		$post->set( 'hits'		, $article->hits );

		// @rule: We only take the introtext part.
		$text	= $article->introtext;

		// Replace the text with proper bbcodes replacements.
		require_once DISCUSS_HELPERS . '/parser.php';

		$config 	= DiscussHelper::getConfig();
		$contentType = 'html';

		if( $config->get( 'layout_editor') == 'bbcode' )
		{
			$text	= EasyDiscussParser::html2bbcode( $text );
			$contentType = 'bbcode';
		}

		// @rule: Add a read more text that links to the article.
		if( $params->get( 'readmore_in_post' , true ) )
		{
			$url	= $this->getArticleURL( $article );

			ob_start();
			include( $this->getTemplatePath( 'readmore.' . $contentType . '.php' ) );
			$readmore = ob_get_contents();
			ob_end_clean();

			$text	.= $readmore;
		}

		$post->set( 'content', $text );

		$post->set( 'content_type', $contentType );

		$post->store();

		return $post;
	}

	/**
	 * Get registration link based on the provider.
	 *
	 * @access 	public
	 * @param 	null
	 * @return 	string 	The URL to the responsible component.
	 */
	public function getRegistrationLink()
	{
		$params 	= $this->getParams();
		$url 		= '';

		switch( $params->get( 'login_provider' , 'joomla' ) )
		{
			case 'cb':
				$url 	= JRoute::_( 'index.php?option=com_comprofiler&task=registers');
			break;

			case 'jomsocial':
				include_once JPATH_ROOT . '/components/com_community/libraries/core.php';
				$url 	= CRoute::_( 'index.php?option=com_community&view=register' );
			break;

			default:
				if( DiscussHelper::getJoomlaVersion() >= '1.6' )
				{
					$url 	= JRoute::_( 'index.php?option=com_users&view=registration' );
				}
				else
				{
					$url 	= JRoute::_( 'index.php?option=com_user&view=register' );
				}
			break;
		}

		return $url;
	}

	/**
	 * Get login link based on the provider.
	 *
	 * @access 	public
	 * @param 	null
	 * @return 	string 	The URL to the responsible component.
	 */
	public function getLoginLink()
	{
		$params 	= $this->getParams();
		$url 		= '';

		$id 		= JRequest::getInt( 'id' );

		$article 	= JTable::getInstance( 'Content' , 'JTable' );
		$article->load( $id );

		$return 	= base64_encode( $this->getArticleURL( $article ) );

		switch( $params->get( 'login_provider' , 'joomla' ) )
		{
			case 'jomsocial':
				include_once JPATH_ROOT . '/components/com_community/libraries/core.php';
				$url 	= CRoute::_( 'index.php?option=com_community' );
			break;
			case 'cb':
			default:
				if( DiscussHelper::getJoomlaVersion() >= '1.6' )
				{
					$url 	= JRoute::_( 'index.php?option=com_users&view=login&return=' . $return );
				}
				else
				{
					$url 	= JRoute::_( 'index.php?option=com_user&view=login&return=' . $return );
				}
			break;
		}

		return $url;
	}
}
