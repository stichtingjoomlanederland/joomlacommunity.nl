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

// No direct access
defined('_JEXEC') or die('Restricted access');

// Always load abstract class by uncommenting the following line
// require_once( JPATH_ROOT . DIRECTORY_SEPARATOR . 'components' . DIRECTORY_SEPARATOR . 'com_komento' . DIRECTORY_SEPARATOR . 'komento_plugins' . DIRECTORY_SEPARATOR .'abstract.php' );

abstract class KomentoExtension
{
	const APIVERSION = '1.3';

	/**
	 * The state of the component plugin
	 * @var boolean
	 */
	public $state = true;

	/**
	 * The extension name
	 * @var string
	 */
	public $component = null;

	/**
	 * The main article object
	 * @var mixed
	 */
	public $_item = null;

	/**
	 * Article properties mapping
	 *
	 * @var    array
	 */
	public $_map = array(
		// not needed with custom getContentId()
		'id'			=> 'id',

		// not needed with custom getContentTitle()
		'title'			=> 'title',

		// not needed with custom getContentHits()
		'hits'			=> 'hits',

		// not needed with custom getAuthorId()
		'created_by'	=> 'created_by',

		// not needed with custom getCategoryId()
		'catid'			=> 'catid',

		// not needed with custom getContentPermalink()
		'permalink'		=> 'permalink'
	);

	/******************************************************
	 *
	 * START
	 * [BASIC FUNCTIONS]
	 * These functions are mandatory
	 *
	 ******************************************************/

	/**
	 * Method to load a plugin object by content id number
	 *
	 * @access	public
	 *
	 * @return	object	Instance of this class
	 */
	abstract public function load( $cid );

	/**
	 * Method to get content's ID based on category filter
	 *
	 * @access	public
	 *
	 * @param	string/array $categories Category Ids
	 * @return	array	The IDs of the article
	 */
	abstract public function getContentIds( $categories = '' );

	/**
	 * Method to get a list of categories
	 *
	 * @access	public
	 *
	 * @return	array	array of category object with id, title, level, parent_id as key
	 */
	abstract public function getCategories();

	/**
	 * Method to check if the current view is listing view
	 *
	 * @access	public
	 *
	 * @return	boolean	True if it is listing view
	 */
	abstract public function isListingView();

	/**
	 * Method to check if the current view is entry view
	 *
	 * @access	public
	 *
	 * @return	boolean	True if it is entry view
	 */
	abstract public function isEntryView();

	/**
	 * Method to append the comment to the article
	 *
	 * @access	public
	 *
	 * @param	object	$article	The article object
	 * @param	string	$html		The comment in HTML
	 * @param	string	$view		The current view
	 * @param	array	$options	Parameter key
	 *
	 * @return	void
	 */
	abstract public function onExecute( &$article, $html, $view, $options = array() );

	/******************************************************
	 *
	 * END
	 * [BASIC FUNCTIONS]
	 *
	 ******************************************************/


	/******************************************************
	 *
	 * START
	 * [EXTENDED FUNCTIONS]
	 *
	 ******************************************************/

	/**
	 * Initialize the plugin
	 */
	public function __construct( $component )
	{
		$this->component	= $component;
	}

	public function addFile( $file )
	{
		if( $this->state )
		{
			if( JFile::exists( $file ) )
			{
				require_once( $file );
			}
			else
			{
				$this->state = false;
			}
		}
	}

	/**
	 * Method to get the name of the current API version number
	 *
	 * @access	public
	 *
	 * @return	string	The version number
	 */
	public function getAPIVersion()
	{
		return self::APIVERSION;
	}

	/**
	 * Method to get the translated name of current component
	 *
	 * @access	public
	 *
	 * @return	string	This component's name
	 */
	public function getComponentName()
	{
		return JText::_( 'COM_KOMENTO_' . strtoupper( $this->component ) );
	}

	/**
	 * Method to get the icon address of current component
	 *
	 * @access	public
	 *
	 * @return	string	This component's icon link
	 */
	public function getComponentIcon()
	{
		$base = 'administrator/components/com_komento/assets/images/components/';
		$file = $this->component . '.png';

		if( !JFile::exists( JPATH_ROOT . '/' . $base . $file ) )
		{
			return JURI::root() . $base . 'error.png';
		}

		return JURI::root() . $base . $file;
	}

	/**
	 * Method to get the component template override path
	 *
	 * @access	public
	 *
	 * @return	string	This component's template override path
	 */
	public function getComponentThemePath()
	{
		return JPATH_ROOT . DIRECTORY_SEPARATOR . 'components' . DIRECTORY_SEPARATOR . $this->component . DIRECTORY_SEPARATOR . 'komento';
	}

	/**
	 * Method to get the component template override path uri
	 *
	 * @access	public
	 *
	 * @return	string	This component's template override path uri
	 */
	public function getComponentThemeURI()
	{
		return JURI::root() . 'components/' . $this->component . '/komento';
	}

	/**
	 * Method to prepare a proper link
	 *
	 * @access	public
	 *
	 * @param	string	$link		The unprocessed link
	 * @param	array	$params		Parameter key
	 *
	 */
	public function prepareLink( $link )
	{
		$link = JRoute::_( $link );

		// remove relatiave path if exist
		$relpath = JURI::root( true );
		if( $relpath != '' && strpos( $link, $relpath ) === 0 )
		{
			$link = substr( $link, strlen( $relpath ) );
		}

		// backend or frontend, remove administrator from link
		if( strpos( $link, '/administrator/' ) === 0 )
		{
			$link = substr( $link, 14 );
		}

		$link = rtrim( JURI::root(), '/' ) . '/' . ltrim( $link, '/' );

		return $link;
	}

	/**
	 * Method to get allowed trigger to run Komento
	 *
	 * @access	public
	 *
	 * @return	boolean/string	true if no trigger check is needed, string if specified a specific trigger
	 */
	public function getEventTrigger()
	{
		return true;
	}

	/**
	 * Method to get allowed context to run Komento
	 *
	 * @access	public
	 *
	 * @return	boolean/string	true if no context check is needed, string if specified a specific context
	 */
	public function getContext()
	{
		return true;
	}

	/**
	 * Method to get content's ID
	 *
	 * @access	public
	 *
	 * @return	integer	The ID of the article
	 */
	public function getContentId()
	{
		return $this->_item->{$this->_map['id']};
	}

	/**
	 * Method to get content's title
	 *
	 * @access	public
	 *
	 * @return	string	The title of the article
	 */
	public function getContentTitle()
	{
		return $this->_item->{$this->_map['title']};
	}

	/**
	 * Method to get content's hits count
	 *
	 * @access	public
	 *
	 * @return	string	The hits count of the article
	 */
	public function getContentHits()
	{
		return $this->_item->{$this->_map['hits']};
	}

	/**
	 * Method to get content's permalink
	 *
	 * @access	public
	 *
	 * @return	string	The permalik tho the article
	 */
	public function getContentPermalink()
	{
		return $this->_item->{$this->_map['permalink']};
	}

	/**
	 * Method to get article's category ID.
	 * If category is not applicable, return true
	 *
	 * @access	public
	 *
	 * @return	Integer	Category ID
	 */
	public function getCategoryId()
	{
		return $this->_item->{$this->_map['catid']};
	}

	/**
	 * Method to get author's ID
	 *
	 * @access	public
	 *
	 * @return	integer	The ID of the article's creator
	 */
	public function getAuthorId()
	{
		return $this->_item->{$this->_map['created_by']};
	}

	/**
	 * Method to get author's display name
	 *
	 * @access	public
	 *
	 * @return	string	The name of the article's creator
	 */
	public function getAuthorName()
	{
		return JFactory::getUser( $this->getAuthorId() )->name;
	}

	/**
	 * Method to get author's avatar
	 *
	 * @access	public
	 *
	 * @return	string	The avatar of the article's creator
	 */
	public function getAuthorAvatar()
	{
		return '';
	}

	/**
	 * Method to get custom anchor link to work with comment section jump
	 *
	 * @access	public
	 *
	 * @return	string	The anchor id of the comment section.
	 */
	public function getCommentAnchorId()
	{
		return '';
	}

	/**
	 * Prepare the data if necessary before the checking
	 *
	 * @access	public
	 *
	 * @param	string	$eventTrigger	The event trigger
	 * @param	string	$context		Context
	 * @param	object	$article		The article
	 * @param	array	$params			Parameter key
	 * @param	array	$page			Parameter key
	 * @param	array	$options		Parameter key
	 *
	 * @return	boolean	True if success
	 */
	public function onBeforeLoad( $eventTrigger, $context, &$article, &$params, &$page, &$options )
	{
		return true;
	}

	/**
	 * Prepare article if Komento is disabled
	 *
	 * @access	public
	 *
	 * @param	string	$eventTrigger	The event trigger
	 * @param	string	$context		Context
	 * @param	object	$article		The article
	 * @param	array	$params			Parameter key
	 * @param	array	$page			Parameter key
	 * @param	array	$options		Parameter key
	 *
	 * @return	boolean	False by default
	 */
	public function onParameterDisabled( $eventTrigger, $context, &$article, &$params, &$page, &$options )
	{
		return false;
	}

	/**
	 * After the loading the content article with id
	 *
	 * @access	public
	 *
	 * @param	string	$eventTrigger	The event trigger
	 * @param	string	$context		Context
	 * @param	object	$article		The article
	 * @param	array	$params			Parameter key
	 * @param	array	$page			Parameter key
	 * @param	array	$options		Parameter key
	 *
	 * @return	boolean	True if success
	 */
	public function onAfterLoad( $eventTrigger, $context, &$article, &$params, &$page, &$options )
	{
		return true;
	}

	/**
	 * Roll back passed by reference
	 *
	 * @access	public
	 *
	 * @param	string	$eventTrigger	The event trigger
	 * @param	string	$context		Context
	 * @param	object	$article		The article
	 * @param	array	$params			Parameter key
	 * @param	array	$page			Parameter key
	 * @param	array	$options		Parameter key
	 *
	 * @return	boolean	True if success
	 */
	public function onRollBack( $eventTrigger, $context, &$article, &$params, &$page, &$options )
	{
		return true;
	}

	/**
	 * When article of the component is deleted
	 *
	 * @access	public
	 *
	 * @param	int/object	$article	The article id or object
	 *
	 * @return	boolean	True if success
	 */
	public function onArticleDeleted( $article )
	{
		$cid = $article;

		if( is_object( $article ) )
		{
			$cid = $article->{$this->_map['id']};
		}

		require_once( JPATH_ROOT . DIRECTORY_SEPARATOR . 'components' . DIRECTORY_SEPARATOR . 'com_komento' . DIRECTORY_SEPARATOR . 'helpers' . DIRECTORY_SEPARATOR . 'helper.php' );

		$model = Komento::getModel( 'comments' );

		$result = $model->deleteArticleComments( $this->component, $cid );

		return $result;
	}

	/**
	 * Failsafe function
	 *
	 * @access	public
	 *
	 * @param	int		$cid	Article id
	 *
	 * @return	object	The failsafe article object that contains error messages to each required property
	 */
	public function onLoadArticleError( $cid )
	{
		static $componentInstances	= array();
		static $cidInstances		= array();

		if( empty( $componentInstances[$this->component] ) )
		{
			require_once( JPATH_ROOT . DIRECTORY_SEPARATOR . 'components' . DIRECTORY_SEPARATOR . 'com_komento' . DIRECTORY_SEPARATOR . 'komento_plugins' . DIRECTORY_SEPARATOR . 'error.php' );
			$componentInstances[$this->component] = new KomentoError( $this->component );
		}

		if( empty( $cidInstances[$this->component][$cid] ) )
		{
			$cidInstances[$this->component][$cid] = $componentInstances[$this->component]->load( $cid );
		}

		return $cidInstances[$this->component][$cid];
	}

	/******************************************************
	 *
	 * END
	 * [EXTENDED FUNCTIONS]
	 *
	 ******************************************************/

	/******************************************************
	 *
	 * START
	 * [TRIGGER FUNCTIONS]
	 *
	 ******************************************************/

	/**
	 * Called before Komento's bar
	 *
	 * @access	public
	 *
	 * @param	int		$commentCount	Total comment count
	 */
	public function onBeforeKomentoBar( $commentCount ) {}


	/**
	 * Called before Komento's box
	 *
	 * @access	public
	 *
	 * @param	object	$system		Komento's system object
	 * @param	array	$comments	Array of Komento comments
	 */
	public function onBeforeKomentoBox( $system, $comments ) {}

	/**
	 * Called before Komento save comment
	 *
	 * @access	public
	 *
	 * @param	object	$comment	Comment
	 *
	 * @return	bool	Result of the trigger
	 */
	public function onBeforeSaveComment( $comment )
	{
		return true;
	}

	/**
	 * Called after Komento save comment
	 *
	 * @access	public
	 *
	 * @param	object	$comment	Comment
	 */
	public function onAfterSaveComment( $comment ) {}

	/**
	 * Called before Komento process comment
	 *
	 * @access	public
	 *
	 * @param	object	$comment	Comment
	 */
	public function onBeforeProcessComment( $comment ) {}

	/**
	 * Called after Komento process comment
	 *
	 * @access	public
	 *
	 * @param	object	$comment	Comment
	 */
	public function onAfterProcessComment( $comment ) {}

	/**
	 * Called before Komento stores a notification to the database
	 *
	 * @access	public
	 *
	 * @param	object	$recipient	Recipient
	 *
	 * @return	bool	Result of the trigger
	 */
	public function onBeforeSendNotification( $recipient )
	{
		return true;
	}

	/**
	 * Called before Komento delete comment
	 *
	 * @access	public
	 *
	 * @param	object	$comment	Comment
	 *
	 * @return	bool	Result of the trigger
	 */
	public function onBeforeDeleteComment( $comment )
	{
		return true;
	}

	/**
	 * Called after Komento delete comment
	 *
	 * @access	public
	 *
	 * @param	object	$comment	Comment
	 */
	public function onAfterDeleteComment( $comment ) {}

	/**
	 * Called before Komento publish comment
	 *
	 * @access	public
	 *
	 * @param	object	$comment	Comment
	 *
	 * @return	bool	Result of the trigger
	 */
	public function onBeforePublishComment( $comment )
	{
		return true;
	}

	/**
	 * Called after Komento publish comment
	 *
	 * @access	public
	 *
	 * @param	object	$comment	Comment
	 */
	public function onAfterPublishComment( $comment ) {}

	/**
	 * Called before Komento unpublish comment
	 *
	 * @access	public
	 *
	 * @param	object	$comment	Comment
	 *
	 * @return	bool	Result of the trigger
	 */
	public function onBeforeUnpublishComment( $comment )
	{
		return true;
	}

	/**
	 * Called after Komento unpublish comment
	 *
	 * @access	public
	 *
	 * @param	object	$comment	Comment
	 */
	public function onAfterUnpublishComment( $comment ) {}

	/******************************************************
	 *
	 * END
	 * [TRIGGER FUNCTIONS]
	 *
	 ******************************************************/
}
