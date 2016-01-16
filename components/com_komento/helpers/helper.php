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

if(!defined('KOMENTO_CLI'))
{
	require_once( JPATH_ROOT . DIRECTORY_SEPARATOR . 'components' . DIRECTORY_SEPARATOR . 'com_komento' . DIRECTORY_SEPARATOR . 'bootstrap.php' );
}

class Komento
{
	public static $package = 'paid';

	public static $component;
	public static $application;

	private static $messages = array();

	public static function getPackage()
	{
		return self::$package;
	}

	public static function import( $type, $filename )
	{ // Test
		$file = "";

		if ($type == 'helper')
		{

			$file = KOMENTO_HELPERS . DIRECTORY_SEPARATOR . $filename . '.php';
		}
		if ($type == 'class')
		{
			$file = KOMENTO_CLASSES . DIRECTORY_SEPARATOR . $filename . '.php';
		}

		if(!JFile::exists( $file ) )
		{
			return false;
		}

		require_once($file);

		return true;
	}

	/**
	 * Retrieve specific helper objects.
	 *
	 * @param	string	$helper	The helper class. Class name should be the same name as the file. e.g KomentoXXXHelper
	 * @return	object	Helper object.
	 **/
	public static function getHelper( $name )
	{
		static $helpers	= array();

		if( empty( $helpers[ $name ] ) )
		{
			$file	= KOMENTO_HELPERS . DIRECTORY_SEPARATOR . JString::strtolower( $name ) . '.php';

			if( JFile::exists( $file ) )
			{
				require_once( $file );
				$classname	= 'Komento' . ucfirst( $name ) . 'Helper';

				$helpers[ $name ] = class_exists($classname) ? new $classname() : false;
			}
			else
			{
				$helpers[ $name ]	= false;
			}
		}

		return $helpers[ $name ];
	}

	/**
	 * Retrieve JTable objects.
	 *
	 * @param	string	$tableName	The table name.
	 * @param	string	$prefix		JTable prefix.
	 * @return	object	JTable object.
	 **/
	public static function getTable( $tableName, $prefix = 'KomentoTable' )
	{
		require_once( KOMENTO_TABLES . '/parent.php' );
		$table    = KomentoParentTable::getInstance( $tableName, $prefix );

		return $table;
	}

	/**
	 * Retrieve Model objects.
	 *
	 * @param	string	$name	The model name.
	 * @return	object	model object.
	 **/
	public static function getModel( $name, $backend = false )
	{
		static $models = array();

		$signature	= json_encode(array($name, (bool) $backend));

		if( empty( $models[ $signature ] ) )
		{
			$file	= $backend ? KOMENTO_ADMIN_ROOT . DIRECTORY_SEPARATOR . 'models' : KOMENTO_MODELS;
			$file	.= DIRECTORY_SEPARATOR . JString::strtolower( $name ) . '.php';

			if( JFile::exists( $file ) )
			{
				require_once( $file );
				$classname	= 'KomentoModel' . ucfirst( $name );

				if( $backend )
				{
					$classname = 'KomentoModelAdmin' . ucfirst( $name );
				}

				$models[ $signature ] = class_exists($classname) ? new $classname() : false;
			}
			else
			{
				$models[ $signature ] = false;
			}
		}

		return $models[ $signature ];
	}

	/**
	 * Retrieve Class objects.
	 *
	 * @param	string	$filename	File name of the class.
	 * @param	string	$classname	Class name.
	 * @return	object	class object.
	 **/
	public static function getClass( $filename, $classname )
	{
		static $classes	= array();

		$sig	= md5(serialize(array($filename,$classname)));

		if ( empty($classes[$sig]) )
		{
			$file	= KOMENTO_CLASSES . DIRECTORY_SEPARATOR . JString::strtolower( $filename ) . '.php';

			if( JFile::exists($file) )
			{
				require_once( $file );

				$classes[ $sig ] = class_exists($classname) ? new $classname() : false;
			}
			else{
				$classes[ $sig ] = false;
			}
		}

		return $classes[ $sig ];
	}

	/**
	 * Retrieve Komento's configuration.
	 *
	 * @return	object	JParameter object.
	 **/
	public static function getConfig( $component = '', $default = true )
	{
		static $configs	= array();

		$component	= $component ? $component : ( self::$component ? self::$component : 'com_content' );
		$component	= preg_replace('/[^A-Z0-9_\.-]/i', '', $component);

		if( empty( $configs[$component] ) )
		{
			if( $default )
			{
				//load default ini data first
				$file		= KOMENTO_ADMIN_ROOT . DIRECTORY_SEPARATOR . 'configuration.json';
				$config		= Komento::getRegistry( JFile::read( $file ), 'json' );

				$config->default	= clone $config->toObject();
			}
			else
			{
				$config = Komento::getRegistry();
			}

			// get config stored in db
			$dbConfig	= Komento::getTable( 'configs' );
			$dbConfig->load( $component );
			$stored = Komento::getRegistry( $dbConfig->params, 'json' );

			$config->extend( $stored );

			$config->_current = $component;

			$configs[$component] = $config;
		}

		return $configs[$component];
	}

	public static function getKonfig()
	{
		static $konfig = null;

		if( !( $konfig ) )
		{
			//load default ini data first
			$file		= KOMENTO_ADMIN_ROOT . DIRECTORY_SEPARATOR . 'konfiguration.json';
			$konfig		= Komento::getRegistry( JFile::read( $file ), 'json' );

			$konfig->default	= clone $konfig->toObject();

			//get config stored in db
			$dbConfig	= self::getTable( 'configs' );
			$dbConfig->load( 'com_komento' );

			$stored = Komento::getRegistry( $dbConfig->params, 'json' );

			$konfig->extend( $stored );
		}

		return $konfig;
	}

	public static function getACL()
	{
		$my			= JFactory::getUser();
		$userId		= $my->id;

		Komento::import( 'helper', 'acl' );

		$acl		= KomentoACLHelper::getRules( $userId, self::$component );
		$acl		= JArrayHelper::toObject( $acl );

		return $acl;
	}

	/**
	 * Retrieve Theme objects.
	 *
	 * @param	string	$sel_theme	Theme name.
	 * @return	object	Theme class
	 **/
	public static function getTheme( $new = false )
	{
		static $themeObj = array();

		if ( !class_exists('KomentoThemes') )
		{
			require_once(KOMENTO_CLASSES . DIRECTORY_SEPARATOR . 'themes.php');
		}

		$selected = KOMENTO_THEME_BASE;
		$override = '';

		if(!defined('KOMENTO_CLI'))
		{
			$config		= Komento::getConfig();
			$selected	= $config->get( 'layout_theme', KOMENTO_THEME_BASE );
			$override	= JRequest::getCmd( 'theme', '' );
		}

		$theme		= $override ? $override : $selected;

		if( $new )
		{
			return new KomentoThemes( $theme );
		}
		else
		{
			if( empty( $themeObj[$theme] ) )
			{
				$themeObj[$theme] = new KomentoThemes( $theme );
			}
		}

		return $themeObj[$theme];
	}

	/**
	 * Method to get user's profile
	 *
	 * @param	$id		The user id. leave empty for current user.
	 * @return	object
	 */
	public static function getProfile( $id = null )
	{
		if (!class_exists('KomentoProfile'))
		{
			require_once( KOMENTO_CLASSES . DIRECTORY_SEPARATOR . 'profile.php' );
		}

		return KomentoProfile::getUser($id);
	}

	public static function getComment( $id = 0, $process = 0, $admin = 0 )
	{
		static $commentsObj = array();

		if( empty( $commentsObj[$id] ) )
		{
			$comment = new KomentoComment( $id );

			if( $comment->getError() )
			{
				return false;
			}

			$commentsObj[$id] = $comment;
		}

		if( $process )
		{
			self::import( 'helper', 'comment' );
			$commentsObj[$id] = KomentoCommentHelper::process( $commentsObj[$id], $admin );
		}

		return $commentsObj[$id];
	}

	public static function getCaptcha()
	{
		return Komento::getHelper( 'Captcha' )->getInstance();
	}

	public static function getDBO()
	{
		return Komento::getHelper( 'database' );
	}

	public static function getRegistry( $contents = '', $type = 'ini' )
	{
		Komento::import( 'class', 'registry' );

		$registry = new KomentoRegistry( $contents, $type );

		return $registry;
	}

	public static function getXML( $contents, $isFile = false )
	{
		Komento::import( 'class', 'xml' );

		$xml = new KomentoXml( $contents, $isFile );

		return $xml;
	}

	public static function getDate( $time = '', $offset = null )
	{
		static $current;

		Komento::import( 'class', 'date' );

		if( $time == '' )
		{
			if( !$current )
			{
				$current = new KomentoDate( $time, $offset );
			}

			return $current;
		}

		$date = new KomentoDate( $time, $offset );

		return $date;
	}

	public static function getAjax()
	{
		return Komento::getHelper( 'ajax' );
	}

	public static function getJSON()
	{
		static $json = null;

		if( is_null( $json ) )
		{
			Komento::getClass( 'json', 'KomentoJson' );

			$json = new KomentoJson();
		}

		return $json;
	}

	/**
	 * A model to get data from a component's content item
	 */
	public static function loadApplication( $component = '' )
	{
		static $instances = null;

		if( is_numeric($instances) )
		{
			$instances = array();
		}

		$component = preg_replace('/[^A-Z0-9_\.-]/i', '', $component);

		$properInstance = true;
		$komentoPlugin = true;

		// Create a copy of the name so that the original $component won't get affected
		$componentName = $component;

		// If component is empty, then try to load it from getCurrentComponent
		if( empty( $componentName ) )
		{
			$componentName = Komento::getCurrentComponent();

			// If component is still empty, then assign it as error
			if( empty( $componentName ) )
			{
				$componentName = 'error';
			}
		}

		// Check for pro components
		// $package = Komento::getPackage();
		// if( $package !== 'paid' && in_array( $component, Komento::getPaidComponents() ) )
		// {
		// 	$componentName = 'error';
		// }

		// It is possible that even getCurrentComponent doesn't have a data
		if( empty($instances[$componentName]) )
		{
			// Require the base abstract class first
			require_once( KOMENTO_ROOT . DIRECTORY_SEPARATOR . 'komento_plugins' . DIRECTORY_SEPARATOR . 'abstract.php' );

			$classObject = '';

			// Get the component's object first
			$file = JPATH_ROOT . DIRECTORY_SEPARATOR . 'components' . DIRECTORY_SEPARATOR . $componentName . DIRECTORY_SEPARATOR . 'komento_plugin.php';

			// If it doesn't exist in component path, then look for Komento's native plugin
			if( !JFile::exists($file) )
			{
				// Load from Komento's plugin folder
				$file = KOMENTO_ROOT . DIRECTORY_SEPARATOR . 'komento_plugins' . DIRECTORY_SEPARATOR . $componentName . '.php';

				if ( !JFile::exists($file) )
				{
					$komentoPlugin = false;
				}
			}

			// If Komento plugin is found, then initialise it
			if( $komentoPlugin )
			{
				require_once( $file );

				// Load the class
				$className = 'Komento' . ucfirst( strtolower( preg_replace( '/[^A-Z0-9]/i', '', $componentName ) ) );

				if( class_exists( $className ) )
				{
					$classObject = new $className( $component );

					// If there are any errors in initialising the class
					if( !($classObject instanceof KomentoExtension) || !$classObject->state )
					{
						$properInstance = false;
					}
					else
					{
						$instances[$componentName] = $classObject;
					}
				}
				else
				{
					$properInstance = false;
				}
			}
		}

		// If there are any errors
		if( !$komentoPlugin || !$properInstance || empty( $componentName ) )
		{
			require_once( KOMENTO_ROOT . DIRECTORY_SEPARATOR . 'komento_plugins' . DIRECTORY_SEPARATOR . 'error.php' );
			$classObject = new KomentoError( $component );

			if( empty( $componentName ) )
			{
				$componentName = 'error';
			}

			$instances[$componentName] = $classObject;
		}

		return $instances[$componentName];
	}

	public static function getErrorApplication( $component, $cid )
	{
		static $componentInstances	= array();
		static $cidInstances		= array();

		if( empty( $componentInstances[$component] ) )
		{
			require_once( KOMENTO_ROOT . DIRECTORY_SEPARATOR . 'komento_plugins' . DIRECTORY_SEPARATOR . 'error.php' );
			$componentInstances[$component] = new KomentoError( $component );
		}

		if( empty( $cidInstances[$component][$cid] ) )
		{
			$cidInstances[$component][$cid] = $componentInstances[$component]->load( $cid );
		}

		return $cidInstances[$component][$cid];
	}

	/**
	 * Prerequisites check, right after an event is triggered.
	 * (Forced hack on Komento side to work with multiple components properly because sometimes component doesn't care if their plugin file conflicts with other things or not)
	 *
	 * @param	$plugin			string
	 * @param	$eventTrigger	string
	 * @param	$extension		string
	 * @param	$context		string
	 * @return 	boolean
	 */
	public static function onAfterEventTriggered( $plugin, $eventTrigger, $extension, $context, $article, $params )
	{
		if( $extension === 'com_k2' )
		{
			return true;
		}

		// modules check, generally, don't run komento within modules
		if( !empty( $context ) && stristr( $context , 'mod_' ) !== false )
		{
			return false;
		}

		if( $params instanceof JRegistry )
		{
			// exception to ohanah
			if( $extension != 'com_ohanah' && Komento::_( 'JParameter_exists', $params, 'moduleclass_sfx' ) )
			{
				return false;
			}
		}

		return true;
	}

	private static function verifyContext( $context, $source )
	{
		if( is_null( $context ) )
		{
			return true;
		}

		if( empty( $source ) )
		{
			return false;
		}
		elseif( is_array( $source ) )
		{
			return in_array( $context, $source );
		}
		elseif( is_string( $source ) )
		{
			return $context === $source;
		}
		elseif( is_bool( $source ) )
		{
			return $source;
		}
		else
		{
			return true;
		}
	}

	private static function verifyEventTrigger( $trigger, $source )
	{
		if( is_null( $trigger ) )
		{
			return true;
		}

		if( empty( $source ) )
		{
			return false;
		}
		elseif( is_array( $source ) )
		{
			return in_array( $trigger, $source );
		}
		elseif( is_string( $source ) )
		{
			return $trigger === $source;
		}
		elseif( is_bool( $source ) )
		{
			return $source;
		}
		else
		{
			return true;
		}
	}

	/**
	 * This is the heart of Komento that does magic
	 *
	 * @param	$component	string
	 * @param	$article	object
	 * @param	$options	array
	 * @return null
	 */
	public static function commentify( $component, &$article, $options = array() )
	{
		$eventTrigger	= null;
		$context		= null;
		$params			= array();
		$page			= 0;

		if( array_key_exists('trigger', $options) )
		{
			$eventTrigger = $options[ 'trigger' ];
		}
		if( array_key_exists('context', $options) )
		{
			$context = $options[ 'context' ];
		}
		if( array_key_exists('params', $options) )
		{
			$params = $options[ 'params' ];
		}
		if( array_key_exists('page', $options) )
		{
			$page = $options[ 'page' ];
		}

		// TODO: Allow string/int: see line 662
		// Sometimes people pass in $article as an array, we convert it to object
		if( is_array( $article ) )
		{
			$article = (object) $article;
		}

		// Check if there is a valid component
		if( empty( $component ) )
		{
			return false;
		}

		// @task: prepare data and checking on plugin level
		$application = Komento::loadApplication( $component );

		// We verify context and trigger first before going into onBeforeLoad because onBeforeLoad already expects the article to be what Komento want to integrate

		// @task: verify if context is correct
		if( !Komento::verifyContext( $context, $application->getContext() ) )
		{
			return false;
		}

		// @task: verify if event trigger is correct
		if( !Komento::verifyEventTrigger( $eventTrigger, $application->getEventTrigger() ) )
		{
			return false;
		}

		// @trigger: onBeforeLoad
		// we do this checking before load because in some cases,
		// article is not an object and the article id might be missing.
		if( !$application->onBeforeLoad( $eventTrigger, $context, $article, $params, $page, $options ) )
		{
			return false;
		}

		// @task: set the component
		self::setCurrentComponent($component);

		// @task: get all the configuration
		$config	= self::getConfig( $component );
		$konfig = Komento::getKonfig();

		// @task: check if enabled
		if( !$config->get('enable_komento') )
		{
			return false;
		}

		// @task: disable Komento in tmpl=component mode such as print mode
		if( $config->get('disable_komento_on_tmpl_component') && JRequest::getString('tmpl', '') === 'component' )
		{
			return false;
		}

		// We accept $article as an int
		// For $article as a string, onBeforeLoad should already prepare the $article object properly
		if( is_string( $article ) || is_int( $article ) )
		{
			$cid = $article;
		}
		else
		{
			// @task: set cid based on application mapping keys because some component might have custom keys (not necessarily always $article-id)
			$cid = $article->{$application->_map['id']};
		}

		// Don't proceed if $cid is empty
		if( empty( $cid ) )
		{
			return false;
		}

		// @task: process in-content parameters
		self::processParameter( $article, $options );

		// terminate if it's disabled
		if( $options['disable'] )
		{
			if( !$application->onParameterDisabled( $eventTrigger, $context, $article, $params, $page, $options ) )
			{
				return false;
			}
		}

		// @task: loading article infomation with defined get methods
		if( !$application->load( $cid ) )
		{
			return false;
		}

		// If enabled flag exists, bypass category check
		if( array_key_exists('enable', $options ) && !$options['enable'] )
		{
			// @task: category access check
			$categories		= $config->get( 'allowed_categories' );

			// no categories mode
			switch( $config->get( 'allowed_categories_mode' ) )
			{
				// selected categories
				case 1:
					if( empty( $categories ) )
					{
						return false;
					}
					else
					{
						// @task: For some reason $article->catid might not be set. If it it's not set, just return false.
						$catid	= $application->getCategoryId();

						if( !$catid )
						{
							if( !$application->onRollBack( $eventTrigger, $context, $article, $params, $page, $options ) )
							{
								// raise error
							}
							return false;
						}

						if( !is_array( $categories ) )
						{
							$categories	= explode( ',' , $categories );
						}

						if( !in_array( $catid , $categories ) )
						{
							if( !$application->onRollBack( $eventTrigger, $context, $article, $params, $page, $options ) )
							{
								// raise error
							}

							return false;
						}
					}
					break;

				// except selected categories
				case 2:
					if( !empty( $categories ) )
					{
						// @task: For some reason $article->catid might not be set. If it it's not set, just return false.
						$catid	= $application->getCategoryId();

						if( !$catid )
						{
							if( !$application->onRollBack( $eventTrigger, $context, $article, $params, $page, $options ) )
							{
								// raise error
							}
							return false;
						}

						if( !is_array( $categories ) )
						{
							$categories	= explode( ',' , $categories );
						}

						if( in_array( $catid , $categories ) )
						{
							if( !$application->onRollBack( $eventTrigger, $context, $article, $params, $page, $options ) )
							{
								// raise error
							}
							return false;
						}
					}
					break;

				// no categories
				case 3:
					return false;
					break;

				// all categories
				case 0:
				default:
					break;
			}
		}

		// @trigger: onAfterLoad
		// Now the article with id has been loaded.
		if( !$application->onAfterLoad( $eventTrigger, $context, $article, $params, $page, $options ) )
		{
			return false;
		}

		// @task: send mail on page load
		if( $config->get( 'notification_sendmailonpageload' ) )
		{
			self::getMailQueue()->sendOnPageLoad();
		}

		// @task: clear captcha database
		if( $konfig->get( 'database_clearcaptchaonpageload' ) )
		{
			self::clearCaptcha();
		}

		// @task: load necessary css and javascript files.
		self::getHelper( 'Document' )->loadHeaders();


		/**********************************************/
		// Run Komento!

		$commentsModel	= Komento::getModel( 'comments' );

		$comments		= '';
		$return			= false;

		$commentCount	= $commentsModel->getCount( $component, $cid );

		// Get total ratings
		$ratings 			= $commentsModel->getOverallRatings($component, $cid);
		$totalRating 		= 0;
		$totalRatingCount	= 0;

		if ($ratings) {
			$totalRating		= $ratings->value;
			$totalRatingCount 	= $ratings->total;
		}

		if( $application->isListingView() )
		{
			$html = '';

			if( !array_key_exists('skipBar', $options) )
			{
				$commentOptions = array();

				$commentOptions['threaded'] = 0;
				$commentOptions['limit'] = $config->get( 'preview_count', '3' );
				$commentOptions['sort'] = $config->get( 'preview_sort', 'latest' );
				$commentOptions['parentid'] = $config->get( 'preview_parent_only', false ) ? 0 : 'all';
				$commentOptions['sticked'] = $config->get( 'preview_sticked_only', false ) ? true : 'all';

				if( $commentOptions['sort'] == 'popular' )
				{
					$comments = $commentsModel->getPopularComments( $component, $cid, $commentOptions );
				}
				else
				{
					$comments = $commentsModel->getComments( $component, $cid, $commentOptions );
				}

				$theme	= Komento::getTheme();
				$theme->set( 'commentCount'		, $commentCount );
				$theme->set( 'componentHelper'	, $application );
				$theme->set( 'component', $component );
				$theme->set( 'cid', $cid );
				$theme->set( 'comments', $comments );
				$theme->set( 'article', $article );
				$html	= $theme->fetch('comment/bar.php');
			}

			$return	= $application->onExecute( $article, $html, 'listing', $options );
		}

		if( $application->isEntryView() )
		{
			// check for escaped_fragment (google ajax crawler)
			$fragment = JRequest::getVar( '_escaped_fragment_', '' );

			if( $fragment != '' )
			{
				$tmp = explode( '=', $fragment );

				$fragment = array( $tmp[0] => $tmp[1] );

				if( isset( $fragment['kmt-start'] ) )
				{
					$options['limitstart'] = $fragment['kmt-start'];
				}
			}
			else
			{
				// Sort comments oldest first by default.
				if (!isset($options['sort']))
				{
					$options['sort'] = JRequest::getVar('kmt-sort', $config->get( 'default_sort' ) );
				}

				if( $config->get( 'load_previous' ) )
				{
					$options['limitstart'] = $commentCount - $config->get( 'max_comments_per_page' );
					if( $options['limitstart'] < 0 )
					{
						$options['limitstart'] = 0;
					}
				}
			}

			$options['threaded'] = $config->get( 'enable_threaded' );

			$profile		= Komento::getProfile();

			$my	= JFactory::getUser();

			if( !$profile->allow( 'read_others_comment' ) )
			{
				$options['userid'] = $my->id;
			}

			if( $profile->allow( 'read_comment' ) )
			{
				$comments	= $commentsModel->getComments( $component, $cid, $options );
			}

			$contentLink = $application->getContentPermalink();


			$theme	= Komento::getTheme();

			$theme->set('totalRating', $totalRating);
			$theme->set('totalRatingCount', $totalRatingCount);
			$theme->set( 'component', $component );
			$theme->set( 'cid', $cid );
			$theme->set( 'comments', $comments );
			$theme->set( 'options', $options );
			$theme->set( 'componentHelper', $application );
			$theme->set( 'application', $application );
			$theme->set( 'commentCount', $commentCount );
			$theme->set( 'contentLink', $contentLink );
			$html	= $theme->fetch('comment/box.php');

			/* [KOMENTO_POWERED_BY_LINK] */

			// free version powered by link append (for reference only)
			// $html	.= '<div style="text-align: center; padding: 20px 0;"><a href="http://stackideas.com">' . JText::_( 'COM_KOMENTO_POWERED_BY_KOMENTO' ) . '</a></div>';

			$return	= $application->onExecute( $article, $html, 'entry', $options );

			// @task: Append hidden token into the page.
			$return .= '<span id="komento-token" style="display:none;"><input type="hidden" name="' . Komento::_( 'getToken' ) . '" value="1" /></span>';
		}

		return $return;
	}

	public static function processParameter( &$article, &$options )
	{
		// Retrieve user parameters e.g.
		// {KomentoDisable}, {KomentoLock}

		if( is_string($article) )
		{
			$text		= &$article;
		}
		elseif( is_object($article) )
		{
			// adjust to standard format
			if( !isset( $article->introtext ) )
			{
				$article->introtext = '';
			}

			if( !isset( $article->text ) )
			{
				$article->text = '';
			}

			$introtext	= &$article->introtext;
			$text		= &$article->text;
		}
		else
		{
			return;
		}

		$options['disable'] = ( JString::strpos($introtext, '{KomentoDisable}') !== false || JString::strpos($text, '{KomentoDisable}') !== false );
		$options['enable'] = ( JString::strpos($introtext, '{KomentoEnable}') !== false || JString::strpos($text, '{KomentoEnable}') !== false );
		$options['lock'] = ( JString::strpos($introtext, '{KomentoLock}') !== false || JString::strpos($text, '{KomentoLock}') !== false );

		// Remove in-content parameters
		if (!empty($introtext))
		{
			$introtext	= JString::str_ireplace( '{KomentoDisable}', '', $introtext );
			$introtext	= JString::str_ireplace( '{KomentoEnable}', '', $introtext );
			$introtext	= JString::str_ireplace( '{KomentoLock}', '', $introtext );
		}

		if (!empty($text))
		{
			$text		= JString::str_ireplace( '{KomentoDisable}', '', $text );
			$text		= JString::str_ireplace( '{KomentoEnable}', '', $text );
			$text		= JString::str_ireplace( '{KomentoLock}', '', $text );
		}
	}

	public static function mergeOptions( $defaults, $options )
	{
		$options	= array_merge($defaults, $options);
		foreach ($options as $key => $value)
		{
			if( !array_key_exists($key, $defaults) )
				unset($options[$key]);
		}

		return $options;
	}

	public static function setMessageQueue($message, $type = 'info')
	{
		$session 	= JFactory::getSession();

		$msgObj = new stdClass();
		$msgObj->message    = $message;
		$msgObj->type       = strtolower($type);

		//save messsage into session
		$session->set('komento.message.queue', $msgObj, 'KOMENTO.MESSAGE');
	}

	public static function getMessageQueue()
	{
		$session 	= JFactory::getSession();
		$msgObj 	= $session->get('komento.message.queue', null, 'KOMENTO.MESSAGE');

		//clear messsage into session
		$session->set('komento.message.queue', null, 'KOMENTO.MESSAGE');

		return $msgObj;
	}

	public static function getMailQueue()
	{
		static $mailq = false;

		if( !$mailq )
		{
			require_once( KOMENTO_CLASSES . DIRECTORY_SEPARATOR . 'mailqueue.php' );

			$mailq	= new KomentoMailQueue();
		}
		return $mailq;
	}

	/**
	 * Method to get Joomla's version
	 *
	 * @return	string
	 */
	public static function joomlaVersion()
	{
		static $version = null;

		if (!$version)
		{
			require_once( KOMENTO_HELPERS . DIRECTORY_SEPARATOR . 'version.php' );
			$version = KomentoVersionHelper::getJoomlaVersion();
		}

		return $version;
	}

	public static function isJoomla30()
	{
		return Komento::joomlaVersion() >= '3.0';
	}

	public static function isJoomla25()
	{
		return Komento::joomlaVersion() >= '1.6' && Komento::joomlaVersion() <= '2.5';
	}

	public static function isJoomla15()
	{
		return Komento::joomlaVersion() == '1.5';
	}

	/**
	 * Method to get installed Komento version
	 *
	 * @return	string
	 */
	public static function komentoVersion()
	{
		static $version = null;

		if (!$version)
		{
			require_once( KOMENTO_HELPERS . DIRECTORY_SEPARATOR . 'version.php' );
			$version = KomentoVersionHelper::getLocalVersion();
		}

		return $version;
	}

	public static function getCurrentComponent()
	{
		return self::$component;
	}

	public static function setCurrentComponent( $component = 'com_component' )
	{
		$component	= preg_replace('/[^A-Z0-9_\.-]/i', '', $component);

		self::$component = $component;

		return self::$component;
	}

	/**
	 * Used in J1.6!. To retrieve list of superadmin users's id.
	 * array
	 */
	public static function getSAUsersIds()
	{
		$saGroup	= array();

		if( Komento::joomlaVersion() >= '1.6' )
		{
			$sql = Komento::getSql();

			$sql->select( '#__usergroups', 'a' )
				->column( 'a.id' )
				->column( 'a.title' )
				->leftjoin( '#__usergroups', 'b' )
				->on( 'a.lft', 'b.lft', '>' )
				->on( 'a.rgt', 'b.rgt', '<' )
				->group( 'a.id' )
				->order( 'a.lft' );

			$result = $sql->loadObjectList();

			foreach( $result as $group )
			{
				if( JAccess::checkGroup( $group->id, 'core.admin' ) )
				{
					$saGroup[]	= $group;
				}
			}
		}
		else
		{
			$tmp = new stdClass();
			$tmp->id = 25;
			$tmp->title = 'Super Administrator';
			$saGroup[] = $tmp;
		}

		//now we got all the SA groups. Time to get the users
		$saUsers	= array();
		if(count($saGroup) > 0)
		{
			foreach($saGroup as $sag)
			{
				$userArr = array();

				if( Komento::joomlaVersion() >= '1.6' )
				{
					$userArr	= JAccess::getUsersByGroup($sag->id);
				}
				else
				{
					$sql = Komento::getSql();

					$sql->select( '#__users' )
						->column( 'id' )
						->where( 'gid', $sag->id );

					$userArr = $sql->loadResultArray();
				}

				if( count( $userArr ) > 0 )
				{
					foreach( $userArr as $user )
					{
						$saUsers[]	= $user;
					}
				}
			}
		}

		return $saUsers;
	}

	// Method to liase legacy functions
	public static function _()
	{
		$class = 'KomentoLegacy16';
		if( Komento::joomlaVersion() == '1.5' )
		{
			$class = 'KomentoLegacy15';
		}

		$legacy = Komento::getClass( 'legacy', $class );
		$args = func_get_args();
		$function = array_shift( $args );

		if( strstr( $function, '::' ) ) // strstr( $function, '->' )
		{
			$function = str_replace( '::', '_', $function );
		}

		if( is_callable( array( $class, $function ) ) )
		{
			return call_user_func_array( array( $class, $function ), $args );
		}
		else
		{
			return false;
		}
	}

	// Method to route standard links (bugged)
	public static function route( $link )
	{
		if( JPATH_BASE == JPATH_ADMINISTRATOR )
		{
			JFactory::$application = JApplication::getInstance('site');
		}

		$link = JRoute::_( $link );

		if( JPATH_BASE == JPATH_ADMINISTRATOR )
		{
			$link = str_ireplace( '/administrator/', '/', $link );
			JFactory::$application = JApplication::getInstance('administrator');
		}

		return $link;
	}

	public static function getUniqueFileName($originalFilename, $path)
	{
		$ext			= JFile::getExt($originalFilename);
		$ext			= $ext ? '.'.$ext : '';
		$uniqueFilename	= JFile::stripExt($originalFilename);

		$i = 1;

		while( JFile::exists($path.DIRECTORY_SEPARATOR.$uniqueFilename.$ext) )
		{
			// $uniqueFilename	= JFile::stripExt($originalFilename) . '-' . $i;
			$uniqueFilename	= JFile::stripExt($originalFilename) . '_' . $i . '_' . Komento::getDate()->toFormat( "%Y%m%d-%H%M%S" );
			$i++;
		}

		//remove the space into '-'
		$uniqueFilename = str_ireplace(' ', '-', $uniqueFilename);

		return $uniqueFilename.$ext;
	}

	public static function getUsergroupById( $id )
	{
		$sql = Komento::getSql();

		if( Komento::isJoomla15() )
		{
			$sql->select( '#__core_acl_aro_groups' )
				->column( 'id' )
				->column( 'name', 'title' )
				->column( '0', 'depth', '', true )
				->where( 'id', $id );
		}
		else
		{
			$sql->select( '#__usergroups' )
				->column( 'title' )
				->where( 'id', $id );
		}

		return $sql->loadResult();
	}

	public static function getUsergroups()
	{
		$sql = Komento::getSql();

		if( Komento::isJoomla15() )
		{
			$sql->select( '#__core_acl_aro_groups' )
				->column( 'id' )
				->column( 'name', 'title' )
				->column( '0', 'depth', '', true )
				->where( '(' )
				->where( 'id', '17', '>' )
				->where( 'id', '26', '<' )
				->where( ')' )
				->where( 'id', '29', '=', 'or' )
				->order( 'lft' );
		}
		else
		{
			$query  = "SELECT `x`.*, COUNT(`y`.`id`) - 1 AS `depth` FROM `#__usergroups` AS `x` INNER JOIN `#__usergroups` AS `y` ON `x`.`lft` BETWEEN `y`.`lft` AND `y`.`rgt` GROUP BY `x`.`id` ORDER BY `x`.`lft`";

			$sql->raw( $query );
		}

		return $sql->loadObjectList();
	}

	public static function getUsersByGroup( $gid )
	{
		$userArr = array();

		if( Komento::joomlaVersion() >= '1.6' )
		{
			$userArr	= JAccess::getUsersByGroup($gid);
		}
		else
		{
			$sql = Komento::getSql();

			$sql->select( '#__users' )
				->column( 'id' )
				->where( 'gid', $gid );

			$userArr = $sql->loadResultArray();
		}

		return $userArr;
	}

	public static function addJomSocialPoint( $action , $userId = 0 )
	{
		$my	= JFactory::getUser();

		if( !empty( $userId ) )
		{
			$my	= JFactory::getUser( $userId );
		}

		if( $my->id != 0 )
		{
			$jsUserPoint	= JPATH_ROOT . DIRECTORY_SEPARATOR . 'components' . DIRECTORY_SEPARATOR . 'com_community' . DIRECTORY_SEPARATOR . 'libraries' . DIRECTORY_SEPARATOR . 'userpoints.php';

			if( JFile::exists( $jsUserPoint ) )
			{
				require_once( $jsUserPoint );
				CUserPoints::assignPoint( $action , $my->id );
			}
		}
		return true;
	}

	public static function addAUP( $plugin_function = '', $referrerid = '', $keyreference = '', $datareference = '' )
	{
		$my	= JFactory::getUser();

		if( !empty( $referrerid ) )
		{
			$my	= JFactory::getUser( $referrerid );
		}

		if( $my->id != 0 )
		{
			$aup	= JPATH_ROOT . DIRECTORY_SEPARATOR . 'components' . DIRECTORY_SEPARATOR . 'com_alphauserpoints' . DIRECTORY_SEPARATOR . 'helper.php';
			if ( JFile::exists( $aup ) )
			{
				require_once( $aup );
				AlphaUserPointsHelper::newpoints( $plugin_function, AlphaUserPointsHelper::getAnyUserReferreID( $referrerid ), $keyreference, $datareference );
			}
		}
	}

	public static function addDiscussPoint( $action , $userId = 0, $title = '' )
	{
		$my	= JFactory::getUser();

		if( !empty( $userId ) )
		{
			$my	= JFactory::getUser( $userId );
		}

		if( $my->id != 0 )
		{
			jimport( 'joomla.filesystem.file' );
			$file 		= JPATH_ROOT . DIRECTORY_SEPARATOR . 'components' . DIRECTORY_SEPARATOR . 'com_easydiscuss' . DIRECTORY_SEPARATOR . 'constants.php';

			if( !JFile::exists( $file ) )
			{
				return false;
			}

			include_once( $file );
			include_once( DISCUSS_HELPERS . DIRECTORY_SEPARATOR . 'helper.php' );

			DiscussHelper::getHelper( 'Points' )->assign( $action , $my->id );

			if( $title != '' && Komento::getConfig()->get( 'enable_discuss_log' ) )
			{
				DiscussHelper::getHelper( 'History' )->log( $action , $userId, $title, 0 );
			}

			return true;
		}
	}

	public static function clearCaptcha( $days = '7' )
	{
		$db = Komento::getDBO();

		$query = 'DELETE FROM ' . $db->nameQuote( '#__komento_captcha' ) . ' WHERE ' . $db->nameQuote( 'created' ) . ' <= DATE_SUB(NOW(), INTERVAL ' . $days . ' DAY)';

		$db->setQuery( $query );
		$db->query();

		return $query;
	}

	/*
	General trigger function to trigger custom Komento events
	List of triggers:
		void onBeforeKomentoBar( int &$commentCount )
		void onBeforeKomentoBox( object &$system, object &$comments )
		bool onBeforeSaveComment object( &$comment )
		void onAfterSaveComment( object &$comment )
		void onBeforeProcessComment( object &$comment )
		void onAfterProcessComment( object &$comment )
		bool onBeforeSendNotification( object &$recipient )
		bool onBeforeDeleteComment( object &$comment )
		void onAfterDeleteComment( object &$comment )
		bool onBeforePublishComment( object &$comment )
		void onAfterPublishComment( object &$comment )
		bool onBeforeUnpublishComment( object &$comment )
		void onAfterUnpublishComment( object &$comment )
	*/
	public static function trigger( $event, $params = array() )
	{
		$config = Komento::getConfig();

		$component = null;
		$cid = null;

		if( isset( $params['component'] ) )
		{
			$component = $params['component'];
			unset( $params['component'] );
		}

		if( isset( $params['cid'] ) )
		{
			$cid = $params['cid'];
			unset( $params['cid'] );
		}

		if( $config->get( 'trigger_method' ) === 'joomla' )
		{
			static $plugin = false;

			if( $plugin === false )
			{
				$plugin = true;
				JPluginHelper::importPlugin( 'komento' );
			}

			$application = JFactory::getApplication();

			$arguments = array();

			if( !empty( $component ) )
			{
				$arguments[] = $component;
			}

			if( !empty( $cid ) )
			{
				$arguments[] = $cid;
			}

			$arguments[] = &$params;

			$results = $application->triggerEvent( $event, $arguments );

			if( is_array( $results ) && in_array( false, $results ) )
			{
				return false;
			}

			return true;
		}

		if( $config->get( 'trigger_method' ) === 'component' )
		{
			if( !empty( $component ) )
			{
				$application = Komento::loadApplication( $component );

				if( !empty( $cid ) )
				{
					$application->load( $cid );
				}

				return call_user_func_array( array( $application, $event ), $params );
			}
		}

		return true;
	}

	public static function debugSql( $query )
	{
		return nl2br(str_replace('#__', 'jos_', $query));
	}

	public static function getIP()
	{
		return Komento::getHelper( 'ip' )->getIP();
	}

	public static function log( $var, $force = 0 )
	{
		if( $force == 1 || Komento::getKonfig()->get( 'komento_environment' ) == 'development' )
		{
			$debugroot = KOMENTO_HELPERS . DIRECTORY_SEPARATOR . 'debug' . DIRECTORY_SEPARATOR;

			if( JFile::exists( $debugroot . 'fb.php' ) && JFile::exists( $debugroot . 'FirePHP.class.php' ) )
			{
				include_once( $debugroot . 'fb.php' );
				fb( $var );
			}

			if( JFile::exists( $debugroot . 'chromephp.php' ) )
			{
				include_once( $debugroot . 'chromephp.php' );
				ChromePhp::log( $var );
			}
		}
	}

	public static function setMessage( $msg, $type = 'notice' )
	{
		Komento::$messages[] = array( 'message' => $msg, 'type' => $type );
	}

	public static function getMessages( $type = 'all' )
	{
		if( $type === 'all' )
		{
			return Komento::$messages;
		}
		else
		{
			$filtered = array();

			foreach( Komento::$messages as $message )
			{
				if( $message['type'] === $type )
				{
					$filtered[] = $message['message'];
				}
			}

			return $filtered;
		}
	}

	public static function setError( $msg )
	{
		Komento::setMessage( $msg, 'error' );
	}

	public static function getErrors()
	{
		return Komento::getMessages( 'error' );
	}

	public static function getSql()
	{
		Komento::import( 'class', 'sql' );

		$sql = new KomentoSql();

		return $sql;
	}

	public static function getPaidComponents()
	{
		return array( 'com_aceshop', 'com_flexicontent', 'com_hwdmediashare', 'com_jevents', 'com_k2', 'com_mtree', 'com_ohanah', 'com_redshop', 'com_sobipro', 'com_virtuemart', 'com_zoo' );
	}
}

class kmt extends Komento {}
