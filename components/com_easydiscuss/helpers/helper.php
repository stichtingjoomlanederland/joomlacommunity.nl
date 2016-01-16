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

jimport('joomla.filesystem.file');
jimport('joomla.filesystem.folder');
jimport('joomla.html.parameter');
jimport('joomla.access.access');
jimport('joomla.application.component.model');

require_once JPATH_ROOT . '/components/com_easydiscuss/constants.php';
require_once DISCUSS_HELPERS . '/router.php';
require_once DISCUSS_HELPERS . '/filter.php';
require_once DISCUSS_HELPERS . '/parser.php';
require_once DISCUSS_HELPERS . '/date.php';
require_once DISCUSS_HELPERS . '/events.php';
require_once DISCUSS_HELPERS . '/ranks.php';
require_once DISCUSS_CLASSES . '/themes.php';
require_once DISCUSS_CLASSES . '/postaccess.php';
require_once DISCUSS_HELPERS . '/xml.php';

class DiscussHelper
{
	public static function _()
	{
		return self::getHelper( func_get_args() );
	}

	public static function compileJS()
	{
		$compile 	= JRequest::getVar( 'compile' );
		$minify 	= JRequest::getVar( 'minify' );

		if( $compile )
		{
			require_once( DISCUSS_CLASSES . '/compiler.php' );

			$minify 	= $minify ? true : false;
			$compiler 	= new DiscussCompiler();
			$result = $compiler->compile( $minify );

			var_dump($result);
			exit;
		}

	}

	public static function getToken( $contents = '' )
	{
		$version 	= DiscussHelper::getJoomlaVersion();

		if( $version >= '1.6' )
		{
			$token = JFactory::getSession()->getFormToken();
		} else {
			$token = JUtility::getToken();
		}

		return $token;
	}

	public static function getHash( $seed = '' )
	{
		if( DiscussHelper::getJoomlaVersion() >= '2.5' )
		{
			return JApplication::getHash( $seed );
		}

		return JUtility::getHash( $seed );
	}

	public static function getDate( $current = '', $tzoffset = null )
	{
		require_once( DISCUSS_CLASSES . '/date.php' );

		$date		= new DiscussDate( $current, $tzoffset );

		return $date;
	}

	public static function getBBCodeParser() {
		require_once( DISCUSS_CLASSES . '/decoda.php');
		$decoda = new DiscussDecoda( '', array('strictMode'=>false) );
		return $decoda;
	}

	public static function getHelper()
	{
		static $helpers	= array();

		$args = func_get_args();

		if (func_num_args() == 0 || empty($args) || empty($args[0]))
		{
			return false;
		}

		$sig = md5(serialize($args));

		if( !array_key_exists($sig, $helpers) )
		{
			$helper	= preg_replace('/[^A-Z0-9_\.-]/i', '', $args[0]);
			$file = DISCUSS_HELPERS . '/' . JString::strtolower($helper) . '.php';

			if( JFile::exists($file) )
			{
				require_once($file);
				$class	= 'Discuss' . ucfirst( $helper ) . 'Helper';

				switch (func_num_args()) {
					case '2':
						$helpers[$sig]	= new $class($args[1]);
						break;
					case '3':
						$helpers[$sig]	= new $class($args[1], $args[2]);
						break;
					case '4':
						$helpers[$sig]	= new $class($args[1], $args[2], $args[3]);
						break;
					case '5':
						$helpers[$sig]	= new $class($args[1], $args[2], $args[3], $args[4]);
						break;
					case '6':
						$helpers[$sig]	= new $class($args[1], $args[2], $args[3], $args[4], $args[5]);
						break;
					case '7':
						$helpers[$sig]	= new $class($args[1], $args[2], $args[3], $args[4], $args[5], $args[6]);
						break;
					case '1':
					default:
						$helpers[$sig]	= new $class();
						break;
				}
			}
			else
			{
				$helpers[$sig]	= false;
			}
		}

		return $helpers[$sig];
	}

	public static function getDBO()
	{
		$db = DiscussHelper::getHelper( 'DB' );

		return $db;
	}

	/**
	 * Retrieve specific helper objects.
	 *
	 * @param	string	$helper	The helper class . Class name should be the same name as the file. e.g EasyDiscussXXXHelper
	 * @return	object	Helper object.
	 **/
	public static function getHelperLegacy( $helper )
	{
		static $obj	= array();

		if( !isset( $obj[ $helper ] ) )
		{
			$file	= DISCUSS_HELPERS . '/' . JString::strtolower( $helper ) . '.php';

			if( JFile::exists( $file ) )
			{
				require_once( $file );
				$class	= 'Discuss' . ucfirst( $helper ) . 'Helper';

				$obj[ $helper ]	= new $class();
			}
			else
			{
				$obj[ $helper ]	= false;
			}
		}

		return $obj[ $helper ];
	}

	public static function getRegistry( $data = '' )
	{
		if( self::getJoomlaVersion() >= '1.6' )
		{
			$registry = new JRegistry($data);
		}
		else
		{
			require_once DISCUSS_CLASSES . '/registry.php';
			$registry = new DiscussRegistry($data);
		}

		return $registry;
	}

	public static function getXML($data, $isFile = true)
	{
		if( self::getJoomlaVersion() >= '1.6' )
		{
			$xml = JFactory::getXML($data, true);
		}
		else
		{
			// Disable libxml errors and allow to fetch error information as needed
			libxml_use_internal_errors(true);

			if ($isFile)
			{
				// Try to load the XML file
				//$xml = simplexml_load_file($data, 'JXMLElement');
				$xml = simplexml_load_file($data);
			}
			else
			{
				// Try to load the XML string
				//$xml = simplexml_load_string($data, 'JXMLElement');
				$xml = simplexml_load_string($data);
			}

			if (empty($xml))
			{
				// There was an error
				JError::raiseWarning(100, JText::_('JLIB_UTIL_ERROR_XML_LOAD'));

				if ($isFile)
				{
					JError::raiseWarning(100, $data);
				}

				foreach (libxml_get_errors() as $error)
				{
					JError::raiseWarning(100, 'XML: ' . $error->message);
				}
			}
		}

		return $xml;
	}

	public static function getUnansweredCount( $categoryId = '0', $excludeFeatured = false )
	{
		$db		= DiscussHelper::getDBO();

		$excludeCats	= DiscussHelper::getPrivateCategories();
		$catModel		= DiscussHelper::getModel('Categories');

		if( !is_array( $categoryId ) && !empty( $categoryId ))
		{
			$categoryId 	= array( $categoryId );
		}

		$childs 		= array();
		if( $categoryId )
		{
			foreach( $categoryId as $id )
			{
				$data 		= $catModel->getChildIds( $id );

				if( $data )
				{
					foreach( $data as $childCategory )
					{
						$childs[]	= $childCategory;
					}
				}
				$childs[]		= $id;
			}
		}

		if( !$categoryId )
		{
			$categoryIds 	= false;
		}
		else
		{
			$categoryIds	= array_diff($childs, $excludeCats);
		}

		$query	= 'SELECT COUNT(a.`id`) FROM `#__discuss_posts` AS a';
		$query	.= '  LEFT JOIN `#__discuss_posts` AS b';
		$query	.= '    ON a.`id`=b.`parent_id`';
		$query	.= '    AND b.`published`=' . $db->Quote('1');
		$query	.= ' WHERE a.`parent_id` = ' . $db->Quote('0');
		$query	.= ' AND a.`published`=' . $db->Quote('1');
		$query  .= ' AND  a.`answered` = 0';
		$query	.= ' AND a.`isresolve`=' . $db->Quote('0');
		$query	.= ' AND b.`id` IS NULL';


		if( $categoryIds )
		{
			if( count( $categoryIds ) == 1 )
			{
				$categoryIds 	= array_shift( $categoryIds );
				$query .= ' AND a.`category_id` = ' . $db->Quote( $categoryIds );
			}
			else
			{
				$query .= ' AND a.`category_id` IN (' . implode( ',', $categoryIds ) .')';
			}
		}

		if( $excludeFeatured )
		{
			$query 	.= ' AND a.`featured`=' . $db->Quote( '0' );
		}

		if (!EDC::isSiteAdmin() && !EDC::isModerator()) {
			$query	.= ' AND a.`private`=' . $db->Quote(0);
		}


		$db->setQuery( $query );

		return $db->loadResult();
	}

	public static function getFeaturedCount( $categoryId )
	{
		$db = DiscussHelper::getDBO();

		$query  = 'SELECT COUNT(1) as `CNT` FROM `#__discuss_posts` AS a';

		$query  .= ' WHERE a.`featured` = ' . $db->Quote('1');
		$query  .= ' AND a.`parent_id` = ' . $db->Quote('0');
		$query  .= ' AND a.`published` = ' . $db->Quote('1');
		$query	.= ' AND a.`category_id`= ' . $db->Quote( $categoryId );

		$db->setQuery($query);

		$result = $db->loadResult();

		return $result;
	}

	/*
	 * type - string - info | warning | error
	 */
	public static function setMessageQueue($message, $type = 'info')
	{
		$session 	= JFactory::getSession();

		$msgObj = new stdClass();
		$msgObj->message	= $message;
		$msgObj->type		= strtolower($type);

		//save messsage into session
		$session->set('discuss.message.queue', $msgObj, 'DISCUSS.MESSAGE');

	}

	public static function getMessageQueue()
	{
		$session	= JFactory::getSession();
		$msgObj		= $session->get('discuss.message.queue', null, 'DISCUSS.MESSAGE');

		//clear messsage into session
		$session->set('discuss.message.queue', null, 'DISCUSS.MESSAGE');

		return $msgObj;
	}

	public static function getAlias( $title, $type='post', $id='0' )
	{

		$items = explode( ' ', $title );
		foreach( $items as $index => $item )
		{
			if( strpos( $item, '*' ) !== false  )
			{
				$items[$index] = 'censored';
			}
		}

		$title = implode( $items, ' ' );

		$alias	= DiscussHelper::permalinkSlug($title);

		// Make sure no such alias exists.
		$i	= 1;
		while( DiscussRouter::_isAliasExists( $alias, $type, $id ) )
		{
			$alias	= DiscussHelper::permalinkSlug( $title ) . '-' . $i;
			$i++;
		}

		return $alias;
	}

	public static function permalinkSlug( $string )
	{
		$config		= DiscussHelper::getConfig();
		if ($config->get( 'main_sef_unicode' ))
		{
			// Unicode support.
			$alias  = DiscussHelper::permalinkUnicodeSlug($string);

		}
		else
		{
			// Replace accents to get accurate string
			//$alias	= DiscussRouter::replaceAccents( $string );
			// hÃ¤llÃ¶ wÃ¶rldÃŸ became hallo-world instead haelloe-woerld thus above line is commented
			// for consistency with joomla

			$alias	= JFilterOutput::stringURLSafe( $string );

			// check if anything return or not. If not, then we give a date as the alias.
			if(trim(str_replace('-', '', $alias)) == '')
			{
				$alias = DiscussHelper::getDate()->format("Y-m-d-H-i-s");
			}
		}
		return $alias;
	}

	public static function permalinkUnicodeSlug( $string )
	{
		$slug	= '';
		if(DiscussHelper::getJoomlaVersion() >= '1.6')
		{
			$slug	= JFilterOutput::stringURLUnicodeSlug($string);
		}
		else
		{
			//replace double byte whitespaces by single byte (Far-East languages)
			$slug = preg_replace('/\xE3\x80\x80/', ' ', $string);

			// remove any '-' from the string as they will be used as concatenator.
			// Would be great to let the spaces in but only Firefox is friendly with this
			$slug = str_replace('-', ' ', $slug);

			// replace forbidden characters by whitespaces
			$slug = preg_replace( '#[:\#\*"@+=;!><&\.%()\]\/\'\\\\|\[]#', "\x20", $slug );

			//delete all '?'
			$slug = str_replace('?', '', $slug);

			//trim white spaces at beginning and end of alias, make lowercase
			$slug = trim(JString::strtolower($slug));

			// remove any duplicate whitespace and replace whitespaces by hyphens
			$slug =preg_replace('#\x20+#','-', $slug);
		}

		return $slug;
	}

	public static function getNotification()
	{
		static $notify = false;

		if( !$notify )
		{
			require_once DISCUSS_CLASSES . '/notification.php';
			$notify	= new DNotification();
		}
		return $notify;

	}

	public static function getMailQueue()
	{
		static $mailq = false;

		if( !$mailq )
		{
			require_once DISCUSS_CLASSES . '/mailqueue.php';

			$mailq	= new DMailQueue();
		}
		return $mailq;

	}

	/**
	 * Get's the adsense helper.
	 */
	public static function getAdsense()
	{
		$obj 	= DiscussHelper::getHelper( 'Adsense' )->getHTML();

		return $obj;
	}

	public static function getSiteSubscriptionClass()
	{
		static $sitesubscriptionclass = false;

		if( !$sitesubscriptionclass )
		{
			require_once DISCUSS_CLASSES . '/subscription.php';

			$sitesubscriptionclass	= new DiscussSubscription();
		}
		return $sitesubscriptionclass;
	}

	public static function getParser()
	{
		$data		= new stdClass();

		// Get the xml file
		$site		= DISCUSS_UPDATES_SERVER;
		$xml		= 'stackideas.xml';
		$contents	= '';

		$handle		= @fsockopen( $site , 80, $errno, $errstr, 30);

		if( !$handle )
			return false;

		$out = "GET /$xml HTTP/1.1\r\n";
		$out .= "Host: $site\r\n";
		$out .= "Connection: Close\r\n\r\n";

		fwrite($handle, $out);

		$body		= false;

		while( !feof( $handle ) )
		{
			$return	= fgets( $handle , 1024 );

			if( $body )
			{
				$contents	.= $return;
			}

			if( $return == "\r\n" )
			{
				$body	= true;
			}
		}
		fclose($handle);

		$parser		= new DiscussXMLHelper( $contents );

		return $parser;
	}

	public static function getLoginHTML( $returnURL )
	{
		$tpl	= new DiscussThemes();
		$tpl->set( 'return'	, base64_encode( $returnURL ) );

		return $tpl->fetch( 'ajax.login.php' );
	}

	public static function getLocalParser()
	{
		$data		= new stdClass();

		$contents	= JFile::read( DISCUSS_ADMIN_ROOT . '/easydiscuss.xml' );

		$parser		= new DiscussXMLHelper( $contents );

		return $parser;
	}

	public static function getLocalVersion()
	{
		$parser	= DiscussHelper::getLocalParser();

		if( !$parser )
		{
			return false;
		}

		$version 	= (string) $parser->getVersion();

		return $version;
	}

	public static function getVersion()
	{
		$parser	= DiscussHelper::getParser();

		if( !$parser )
		{
			return false;
		}

		if( DiscussHelper::getJoomlaVersion() >= '3.0' )
		{
			$version	= (string) $parser->discuss->version;

			if( !$version )
			{
				return false;
			}

			return $version;
		}

		$element	= $parser->document->getElementByPath( 'discuss/version' );

		return $element->data();
	}

	public static function getRecentNews()
	{
		$parser	= DiscussHelper::getParser();

		if( !$parser )
		{
			return false;
		}

		$news 	= array();

		if( DiscussHelper::getJoomlaVersion() >= '3.0')
		{
			$items 	= (Array) $parser->discuss->news;
			$items 	= $items[ 'item' ];

			foreach( $items as $item )
			{
				$obj 			= new stdClass();
				$obj->title		= (string) $item->title;
				$obj->desc 		= (string) $item->description;
				$obj->date 		= (string) $item->pubdate;

				$news[]			= $obj;
			}
		}
		else
		{
			$items	= $parser->document->getElementByPath('discuss/news');

			foreach($items->children() as $item)
			{
				$element	= $item->getElementByPath( 'title' );
				$obj		= new stdClass();
				$obj->title	= $element->data();
				$element	= $item->getElementByPath( 'description' );
				$obj->desc	= $element->data();
				$element	= $item->getElementByPath( 'pubdate' );
				$obj->date	= $element->data();
				$news[]		= $obj;
			}
		}


		return $news;
	}

	public static function getDefaultConfigValue( $key, $defaultVal = null )
	{
		static $defaultConfig	= null;

		if( is_null( $defaultConfig ) )
		{
			// Load default ini data first
			$ini		= DISCUSS_ADMIN_ROOT . '/configuration.ini';
			$raw		= JFile::read($ini);

			$defaultConfig		= DiscussHelper::getRegistry($raw);
		}

		return $defaultConfig->get( $key, $defaultVal);
	}

	/**
	 * Retrieves the core configuration object for EasyDiscuss.
	 *
	 * @since	1.0
	 * @access	public
	 * @param	null
	 * @return	JRegistry
	 */
	public static function getConfig()
	{
		static $config	= null;

		if( is_null( $config ) )
		{
			// Load default ini data first
			$ini		= DISCUSS_ADMIN_ROOT . '/configuration.ini';
			$raw		= JFile::read($ini);

			$config		= DiscussHelper::getRegistry($raw);

			$db 		= DiscussHelper::getDBO();

			$query 		= 'SELECT ' . $db->nameQuote( 'params' ) . ' FROM ' . $db->nameQuote( '#__discuss_configs' );
			$query 		.= ' WHERE ' . $db->nameQuote( 'name' ) . '=' . $db->Quote( 'config' );

			$db->setQuery( $query );
			$rawParams 	= $db->loadResult();

			$config->loadString( $rawParams , 'INI');
		}

		return $config;
	}

	public static function getJConfig()
	{
		$config 	= DiscussHelper::getHelper( 'JConfig' );
		return $config;
	}

	public static function getPostAccess( DiscussPost $post , DiscussCategory $category )
	{
		static $access	= null;

		if( is_null( $access[ $post->id ] ) )
		{
			// Load default ini data first
			$access[ $post->id ] = new DiscussPostAccess( $post , $category);
		}

		return $access[ $post->id ];
	}

	/*
	 * Method used to determine whether the user a guest or logged in user.
	 * return : boolean
	 */
	public static function isLoggedIn()
	{
		$my	= JFactory::getUser();
		$loggedIn	= (empty($my) || $my->id == 0) ? false : true;
		return $loggedIn;
	}

	public static function isSiteAdmin($userId = null)
	{
		static  $loaded = array();

		$sig    = is_null($userId) ? 'me' : $userId ;

		if(! isset( $loaded[$sig] ) )
		{
			$my	= JFactory::getUser( $userId );

			$admin = false;
			if(DiscussHelper::getJoomlaVersion() >= '1.6')
			{
				$admin	= $my->authorise('core.admin');
			}
			else
			{
				$admin	= $my->usertype == 'Super Administrator' || $my->usertype == 'Administrator' ? true : false;
			}

			$loaded[ $sig ] = $admin;
		}

		return $loaded[ $sig ];
	}

	public static function isMine($uid)
	{
		$my	= JFactory::getUser();

		if($my->id == 0)
			return false;

		if( empty($uid) )
			return false;

		$mine	= $my->id == $uid ? 1 : 0;
		return $mine;
	}

	public static function getUserId( $username )
	{
		static $userids = array();

		if( !isset( $userids[ $username ] ) || empty($userids[$username]) )
		{
			$db		= DiscussHelper::getDBO();

			// first get from user alias
			$query	= 'SELECT `id` FROm `#__discuss_users` WHERE `alias` = ' . $db->quote( $username );
			$db->setQuery( $query );
			$userid	= $db->loadResult();

			// then get from user nickname
			if (!$userid)
			{
				$query	= 'SELECT `id` FROm `#__discuss_users` WHERE `nickname` = ' . $db->quote( $username );
				$db->setQuery( $query );
				$userid	= $db->loadResult();
			}

			// then get from username
			if (!$userid)
			{
				$query	= 'SELECT `id` FROM `#__users` WHERE `username`=' . $db->quote( $username );
				$db->setQuery( $query );

				$userid	= $db->loadResult();
			}

			$userids[$username] = $userid;
		}

		return $userids[$username];
	}

	public static function getAjaxURL()
	{
		$uri		= JFactory::getURI();
		$language	= $uri->getVar('lang', 'none');
		$app		= JFactory::getApplication();
		$config		= DiscussHelper::getJConfig();
		$router		= $app->getRouter();
		$url		= rtrim( JURI::base() , '/' );

		$url 		= $url . '/index.php?option=com_easydiscuss&lang=' . $language;

		if( $router->getMode() == JROUTER_MODE_SEF && JPluginHelper::isEnabled("system","languagefilter") )
		{
			$rewrite	= $config->get('sef_rewrite');

			$base		= str_ireplace( JURI::root( true ) , '' , $uri->getPath() );
			$path		=  $rewrite ? $base : JString::substr( $base , 10 );
			$path		= JString::trim( $path , '/' );
			$parts		= explode( '/' , $path );

			$language = addslashes($language);

			if($parts) {
				// First segment will always be the language filter.
				$language	= reset( $parts );
			} else {
				$language	= 'none';
			}

			if ($rewrite) {
				$url		= rtrim( JURI::root() , '/' ) . '/' . $language . '/?option=com_easydiscuss';
				$language	= 'none';
			} else {
				$url		= rtrim( JURI::root() , '/' ) . '/index.php/' . $language . '/?option=com_easydiscuss';
			}
		}

		return $url;
	}

	public static function getBaseUrl()
	{
		static $url;

		if (isset($url)) return $url;

		if( DiscussHelper::getJoomlaVersion() >= '1.6' )
		{
			$uri		= JFactory::getURI();
			$language	= $uri->getVar( 'lang' , 'none' );
			$app		= JFactory::getApplication();
			$config		= DiscussHelper::getJConfig();
			$router		= $app->getRouter();
			$url		= rtrim( JURI::base() , '/' );

			$url 		= $url . '/index.php?option=com_easydiscuss&lang=' . $language;

			if( $router->getMode() == JROUTER_MODE_SEF && JPluginHelper::isEnabled("system","languagefilter") )
			{
				$rewrite	= $config->get('sef_rewrite');

				$base		= str_ireplace( JURI::root( true ) , '' , $uri->getPath() );
				$path		=  $rewrite ? $base : JString::substr( $base , 10 );
				$path		= JString::trim( $path , '/' );
				$parts		= explode( '/' , $path );

				if( $parts )
				{
					// First segment will always be the language filter.
					$language	= reset( $parts );
				}
				else
				{
					$language	= 'none';
				}

				if( $rewrite )
				{
					$url		= rtrim( JURI::root() , '/' ) . '/' . $language . '/?option=com_easydiscuss';
					$language	= 'none';
				}
				else
				{
					$url		= rtrim( JURI::root() , '/' ) . '/index.php/' . $language . '/?option=com_easydiscuss';
				}
			}
		}
		else
		{

			$url		= rtrim( JURI::root() , '/' ) . '/index.php?option=com_easydiscuss';
		}

		$menu = JFactory::getApplication()->getmenu();

		if( !empty($menu) )
		{
			$item = $menu->getActive();
			if( isset( $item->id) )
			{
				$url    .= '&Itemid=' . $item->id;
			}
		}

		// Some SEF components tries to do a 301 redirect from non-www prefix to www prefix.
		// Need to sort them out here.
		$currentURL		= isset( $_SERVER[ 'HTTP_HOST' ] ) ? $_SERVER[ 'HTTP_HOST' ] : '';

		if( !empty( $currentURL ) )
		{
			// When the url contains www and the current accessed url does not contain www, fix it.
			if( stristr($currentURL , 'www' ) === false && stristr( $url , 'www') !== false )
			{
				$url	= str_ireplace( 'www.' , '' , $url );
			}

			// When the url does not contain www and the current accessed url contains www.
			if( stristr( $currentURL , 'www' ) !== false && stristr( $url , 'www') === false )
			{
				$url	= str_ireplace( '://' , '://www.' , $url );
			}
		}

		return $url;
	}

	public static function loadHeaders()
	{
		static $headersLoaded = false;

		if( !$headersLoaded )
		{
			$url 		= self::getAjaxURL();
			$config		= DiscussHelper::getConfig();
			$document	= JFactory::getDocument();
			$ajaxData	=
"/*<![CDATA[*/
	var discuss_site 	= '" . $url . "';
	var spinnerPath		= '" . DISCUSS_SPINNER . "';
	var lang_direction	= '" . $document->direction . "';
	var discuss_featured_style	= '" . $config->get('layout_featuredpost_style', 0) . "';
/*]]>*/";

			$document->addScriptDeclaration( $ajaxData );

			// Only legacy and oranje should be using this.
			if( $config->get( 'layout_site_theme' ) == 'legacy' || $config->get( 'layout_site_theme') == 'oranje' )
			{
				$document->addStyleSheet( DISCUSS_MEDIA_URI . '/styles/legacy-common.css' );
			}

			// Load MCE editor css if editor is not bbcode
			if( $config->get( 'layout_editor' ) != 'bbcode' )
			{
				$document->addStyleSheet( DISCUSS_MEDIA_URI . '/styles/editor-mce.css' );
			}


			// Load EasyBlogConfiguration class
			require_once( DISCUSS_CLASSES . '/configuration.php' );

			// Get configuration instance
			$configuration = EasyDiscussConfiguration::getInstance();

			// Attach configuration to headers
			$configuration->attach();

			$headersLoaded = true;
		}

		return $headersLoaded;
	}

	public static function loadStylesheet($location, $name)
	{
		$config	= DiscussHelper::getConfig();
		$doc	= JFactory::getDocument();

		$less = DiscussHelper::getHelper('less');

		$less->compileMode = $config->get('layout_compile_mode');

		$less->allowTemplateOverride = $config->get('layout_compile_allow_template_override');

		switch ($location)
		{
			case "admin":
				$result = $less->compileAdminStylesheet($name);
				break;

			case "site":
				$result = $less->compileSiteStylesheet($name);
				break;

			case "module":
				$result = $less->compileModuleStylesheet($name);
				break;
		}

		if (!isset($result)) {
			DiscussHelper::setMessageQueue('Could not load stylesheet for ' . $name . '.', 'error');
		};

		if (JFile::exists($result->out)) {

			if ($result->failed) {
				DiscussHelper::setMessageQueue( 'Could not compile stylesheet for ' . $name . '. Using last compiled stylesheet.', 'error' );
			}

			$doc->addStyleSheet($result->out_uri);

		} elseif (JFile::exists($result->failsafe)) {

			if ($result->failed) {
				DiscussHelper::setMessageQueue( 'Could not compile stylesheet for ' . $name . '. Using failsafe stylesheet.', 'error' );
			} else {
				DiscussHelper::setMessageQueue( 'Could not locate compiled stylesheet for ' . $name . '. Using failsafe stylesheet.', 'error' );
			}

			$doc->addStyleSheet($result->failsafe_uri);

		} else {

			DiscussHelper::setMessageQueue( 'Unable to load stylesheet for ' . $name . '.', 'error' );
		}

		return $result;
	}

	/**
	 * Load the theme's css file.
	 *
	 * @since	3.0
	 * @access	public
	 * @param	string	The theme's name
	 * @author	Mark Lee <mark@stackideas.com>
	 */
	public static function loadThemeCss()
	{
		$app	= JFactory::getApplication();

		$assets	= DiscussHelper::getHelper('assets');
		$config	= DiscussHelper::getConfig();

		// Determine site location
		$location = ($app->isAdmin()) ? 'admin' : 'site';

		// Get theme name
		$theme = strtolower($config->get('layout_' . $location . '_theme'));

		return self::loadStylesheet($location, $theme);
	}

	public static function loadString( $view )
	{
		$doc 	= JFactory::getDocument();

		switch( $view )
		{
			case 'post':
				$string = '
					var langEmptyTitle			= "' . JText::_( 'COM_EASYDISCUSS_POST_TITLE_CANNOT_EMPTY' , true ) .'";
					var langEmptyContent		= "' . JText::_( 'COM_EASYDISCUSS_POST_CONTENT_IS_EMPTY' , true ) . '";
					var langConfirmDeleteReply	= "' . JText::_( 'COM_EASYDISCUSS_CONFIRM_DELETE_REPLY' , true ).'";
					var langConfirmDeleteReplyTitle	= "'. JText::_('COM_EASYDISCUSS_CONFIRM_DELETE_REPLY_TITLE' , true ).'";

					var langConfirmDeleteComment		= "'.JText::_('COM_EASYDISCUSS_CONFIRM_DELETE_COMMENT' , true ).'";
					var langConfirmDeleteCommentTitle	= "'.JText::_('COM_EASYDISCUSS_CONFIRM_DELETE_COMMENT_TITLE', true ).'";

					var langPostTitle	= "'.JText::_('COM_EASYDISCUSS_POST_TITLE_EXAMPLE', true ).'";
					var langEmptyTag	= "'.JText::_('COM_EASYDISCUSS_POST_EMPTY_TAG_NOT_ALLOWED' , true ).'";
					var langTagSepartor	= "'.JText::_('COM_EASYDISCUSS_POST_TAGS_SEPERATE', true ).'";
					var langTagAlreadyAdded	= "'.JText::_('COM_EASYDISCUSS_TAG_ALREADY_ADDED', true ).'";

					var langEmptyCategory	= "'.JText::_('COM_EASYDISCUSS_POST_CATEGORY_IS_EMPTY', true ).'";
				';
		}

		$doc->addScriptDeclaration($string);
	}


	public static function getDurationString( $dateTimeDiffObj )
	{
		$lang		= JFactory::getLanguage();
		$lang->load( 'com_easydiscuss' , JPATH_ROOT );

		$data		= $dateTimeDiffObj;
		$returnStr	= '';

		if($data->daydiff <= 0)
		{
			$timeDate	= explode(':', $data->timediff);

			if(intval($timeDate[0], 10) >= 1)
			{
				$returnStr	= DiscussHelper::getHelper( 'String' )->getNoun( 'COM_EASYDISCUSS_HOURS_AGO' , intval($timeDate[0], 10) , true );
			}
			else if(intval($timeDate[1], 10) >= 2)
			{
				$returnStr	= DiscussHelper::getHelper( 'String' )->getNoun( 'COM_EASYDISCUSS_MINUTES_AGO' , intval($timeDate[1], 10) , true );
			}
			else
			{
				$returnStr  = JText::_('COM_EASYDISCUSS_LESS_THAN_A_MINUTE_AGO');
			}
		}
		else if(($data->daydiff >= 1) && ($data->daydiff < 7) )
		{
			$returnStr	= DiscussHelper::getHelper( 'String' )->getNoun( 'COM_EASYDISCUSS_DAYS_AGO' , $data->daydiff , true );
		}
		else if($data->daydiff >= 7 && $data->daydiff <= 30)
		{
			$returnStr = (intval($data->daydiff/7, 10) == 1 ? JText::_('COM_EASYDISCUSS_ONE_WEEK_AGO') : JText::sprintf('COM_EASYDISCUSS_WEEKS_AGO', intval($data->daydiff/7, 10)));
		}
		else
		{
			$returnStr  = JText::_('COM_EASYDISCUSS_MORE_THAN_A_MONTH_AGO');
		}

		return $returnStr;
	}

	public static function storeSession($data, $key, $ns = 'com_easydiscuss')
	{
		$mySess	= JFactory::getSession();
		$mySess->set($key, $data, $ns);
	}

	public static function getSession($key, $ns = 'com_easydiscuss')
	{
		$data = null;

		$mySess = JFactory::getSession();
		if($mySess->has($key, $ns))
		{
			$data = $mySess->get($key, '', $ns);
			$mySess->clear($key, $ns);
			return $data;
		}
		else
		{
			return $data;
		}
	}

	public static function isNew( $noofdays )
	{
		$config	= DiscussHelper::getConfig();
		$isNew	= ($noofdays <= $config->get('layout_daystostaynew', 7)) ? true : false;

		return $isNew;
	}

	public static function getExternalLink($link)
	{
		$uri	= JURI::getInstance();
		$domain	= $uri->toString( array('scheme', 'host', 'port'));

		return $domain . '/' . ltrim(DiscussRouter::_( $link, false ), '/');
	}

	public static function uploadAvatar( $profile, $isFromBackend = false )
	{
		jimport('joomla.utilities.error');
		jimport('joomla.filesystem.file');
		jimport('joomla.filesystem.folder');

		$my			= JFactory::getUser();
		$mainframe	= JFactory::getApplication();
		$config		= DiscussHelper::getConfig();

		$avatar_config_path	= $config->get('main_avatarpath');
		$avatar_config_path	= rtrim($avatar_config_path, '/');
		$avatar_config_path	= JString::str_ireplace('/', DIRECTORY_SEPARATOR, $avatar_config_path);

		$upload_path		= JPATH_ROOT . '/' . $avatar_config_path;
		$rel_upload_path	= $avatar_config_path;

		$err				= null;
		$file				= JRequest::getVar( 'Filedata', '', 'files', 'array' );

		// Check whether the upload folder exist or not. if not create it.
		if(! JFolder::exists($upload_path))
		{
			if(! JFolder::create( $upload_path ))
			{
				// Redirect
				if(! $isFromBackend)
				{
					DiscussHelper::setMessageQueue( JText::_( 'COM_EASYDISCUSS_FAILED_TO_CREATE_UPLOAD_FOLDER' ) , 'error');
					$mainframe->redirect( DiscussRouter::_('index.php?option=com_easydiscuss&view=profile', false) );
				}
				else
				{
					// From backend
					$mainframe->redirect( DiscussRouter::_('index.php?option=com_easydiscuss&view=users', false), JText::_( 'COM_EASYDISCUSS_FAILED_TO_CREATE_UPLOAD_FOLDER' ), 'error' );
				}
				return;
			}
		}

		// Makesafe on the file
		$date			= DiscussHelper::getDate();
		$file_ext		= DiscussImageHelper::getFileExtention($file['name']);
		$file['name']	= $my->id . '_' . JFile::makeSafe(md5($file['name'].$date->toMySQL())) . '.' . strtolower($file_ext);


		if (isset($file['name']))
		{
			$target_file_path		= $upload_path;
			$relative_target_file	= $rel_upload_path . '/' . $file['name'];
			$target_file			= JPath::clean($target_file_path . '/' . JFile::makeSafe($file['name']));
			$original				= JPath::clean($target_file_path . '/' . 'original_' . JFile::makeSafe($file['name']));

			$isNew					= false;

			require_once DISCUSS_HELPERS . '/image.php';
			require_once DISCUSS_CLASSES . '/simpleimage.php';

			if (! DiscussImageHelper::canUpload( $file, $err ))
			{
				if(! $isFromBackend)
				{
					DiscussHelper::setMessageQueue( JText::_( $err ) , 'error');
					$mainframe->redirect(DiscussRouter::_('index.php?option=com_easydiscuss&view=profile&layout=edit', false));
				}
				else
				{
					// From backend
					$mainframe->redirect(DiscussRouter::_('index.php?option=com_easydiscuss&view=users', false), JText::_( $err ), 'error');
				}
				return;
			}

			if (0 != (int)$file['error'])
			{
				if(! $isFromBackend)
				{
					DiscussHelper::setMessageQueue( $file['error'] , 'error');
					$mainframe->redirect(DiscussRouter::_('index.php?option=com_easydiscuss&view=profile&layout=edit', false));
				}
				else
				{
					//from backend
					$mainframe->redirect(DiscussRouter::_('index.php?option=com_easydiscuss&view=users', false), $file['error'], 'error');
				}
				return;
			}

			//rename the file 1st.
			$oldAvatar	= $profile->avatar;
			$tempAvatar	= '';
			if( $oldAvatar != 'default.png')
			{
				$session	= JFactory::getSession();
				$sessionId	= $session->getToken();

				$fileExt	= JFile::getExt(JPath::clean($target_file_path . '/' . $oldAvatar));
				$tempAvatar	= JPath::clean($target_file_path . '/' . $sessionId . '.' . $fileExt);

				// Test if old original file exists.
				if( JFile::exists( $target_file_path . '/original_' . $oldAvatar) )
				{
					JFile::delete( $target_file_path . '/original_' . $oldAvatar );
				}

				JFile::move($target_file_path . '/' . $oldAvatar, $tempAvatar);
			}
			else
			{
				$isNew	= true;
			}

			if (JFile::exists($target_file))
			{
				if( $oldAvatar != 'default.png')
				{
					//rename back to the previous one.
					JFile::move($tempAvatar, $target_file_path . '/' . $oldAvatar);
				}

				if(! $isFromBackend)
				{
					DiscussHelper::setMessageQueue( JText::sprintf('COM_EASYDISCUSS_FILE_ALREADY_EXISTS', $relative_target_file) , 'error');
					$mainframe->redirect(DiscussRouter::_('index.php?option=com_easydiscuss&view=profile', false));
				}
				else
				{
					//from backend
					$mainframe->redirect(DiscussRouter::_('index.php?option=com_easydiscuss&view=users', false), JText::sprintf('COM_EASYDISCUSS_FILE_ALREADY_EXISTS', $relative_target_file), 'error');
				}
				return;
			}

			if (JFolder::exists($target_file))
			{

				if( $oldAvatar != 'default.png')
				{
					//rename back to the previous one.
					JFile::move($tempAvatar, $target_file_path . '/' . $oldAvatar);
				}

				if(! $isFromBackend)
				{
					DiscussHelper::setMessageQueue( JText::sprintf('COM_EASYDISCUSS_FILE_ALREADY_EXISTS', $relative_target_file) , 'error');
					$mainframe->redirect(DiscussRouter::_('index.php?option=com_easydiscuss&view=profile', false));
				}
				else
				{
					//from backend
					$mainframe->redirect(DiscussRouter::_('index.php?option=com_easydiscuss&view=users', false), JText::sprintf('COM_EASYDISCUSS_FILE_ALREADY_EXISTS', $relative_target_file), 'error');
				}
				return;
			}

			$configImageWidth	= $config->get('layout_avatarwidth', 160);
			$configImageHeight	= $config->get('layout_avatarheight', 160);

			$originalImageWidth		= $config->get( 'layout_originalavatarwidth' , 400 );
			$originalImageHeight	= $config->get( 'layout_originalavatarheight' , 400 );

			// Copy the original image files over
			$image = new SimpleImage();
			$image->load($file['tmp_name']);


			//$image->resizeToFill( $originalImageWidth , $originalImageHeight );

			// By Kevin Lankhorst
			$image->resizeOriginal($originalImageWidth, $originalImageHeight, $configImageWidth, $configImageHeight);


			$image->save($original, $image->image_type);
			unset( $image );

			$image = new SimpleImage();
			$image->load($file['tmp_name']);
			$image->resizeToFill( $configImageWidth, $configImageHeight);
			$image->save($target_file, $image->image_type);

			//now we update the user avatar. If needed, we remove the old avatar.
			if( $oldAvatar != 'default.png')
			{
				if(JFile::exists( $tempAvatar ))
				{
					JFile::delete( $tempAvatar );
				}
			}

			return JFile::makeSafe( $file['name'] );
		}
		else
		{
			return 'default.png';
		}

	}

	public static function uploadCategoryAvatar( $category, $isFromBackend = false )
	{
		return DiscussHelper::uploadMediaAvatar( 'category', $category, $isFromBackend);
	}

	public static function uploadMediaAvatar( $mediaType, $mediaTable, $isFromBackend = false )
	{
		jimport('joomla.utilities.error');
		jimport('joomla.filesystem.file');
		jimport('joomla.filesystem.folder');

		$my			= JFactory::getUser();
		$mainframe	= JFactory::getApplication();
		$config		= DiscussHelper::getConfig();

		//$acl		= DiscussACLHelper::getRuleSet();


		// required params
		$layout_type	= ($mediaType == 'category') ? 'categories' : 'teamblogs';
		$view_type		= ($mediaType == 'category') ? 'categories' : 'teamblogs';
		$default_avatar_type	= ($mediaType == 'category') ? 'default_category.png' : 'default_team.png';



		if(! $isFromBackend && $mediaType == 'category')
		{
			$url  = 'index.php?option=com_easydiscuss&view=categories';
			DiscussHelper::setMessageQueue( JText::_('COM_EASYDISCUSS_NO_PERMISSION_TO_UPLOAD_AVATAR') , 'warning');
			$mainframe->redirect(DiscussRouter::_($url, false));
		}

		$avatar_config_path	= ($mediaType == 'category') ? $config->get('main_categoryavatarpath') : $config->get('main_teamavatarpath');
		$avatar_config_path	= rtrim($avatar_config_path, '/');
		$avatar_config_path	= str_replace('/', DIRECTORY_SEPARATOR, $avatar_config_path);

		$upload_path		= JPATH_ROOT . '/' . $avatar_config_path;
		$rel_upload_path	= $avatar_config_path;

		$err				= null;
		$file				= JRequest::getVar( 'Filedata', '', 'files', 'array' );

		//check whether the upload folder exist or not. if not create it.
		if(! JFolder::exists($upload_path))
		{
			if(! JFolder::create( $upload_path ))
			{
				// Redirect
				if(! $isFromBackend)
				{
					DiscussHelper::setMessageQueue( JText::_('COM_EASYDISCUSS_IMAGE_UPLOADER_FAILED_TO_CREATE_UPLOAD_FOLDER') , 'error');
					$this->setRedirect( DiscussRouter::_('index.php?option=com_easydiscuss&view=categories', false) );
				}
				else
				{
					//from backend
					$this->setRedirect( DiscussRouter::_('index.php?option=com_easydiscuss&view=categories', false), JText::_('COM_EASYDISCUSS_IMAGE_UPLOADER_FAILED_TO_CREATE_UPLOAD_FOLDER'), 'error' );
				}
				return;
			}
			else
			{
				// folder created. now copy index.html into this folder.
				if(! JFile::exists( $upload_path . '/index.html' ) )
				{
					$targetFile	= DISCUSS_ROOT . '/index.html';
					$destFile	= $upload_path . '/index.html';

					if( JFile::exists( $targetFile ) )
						JFile::copy( $targetFile, $destFile );
				}
			}
		}

		//makesafe on the file
		$file['name']	= $mediaTable->id . '_' . JFile::makeSafe($file['name']);

		if (isset($file['name']))
		{
			$target_file_path		= $upload_path;
			$relative_target_file	= $rel_upload_path . '/' . $file['name'];
			$target_file			= JPath::clean($target_file_path . '/' . JFile::makeSafe($file['name']));
			$isNew					= false;

			require_once DISCUSS_HELPERS . '/image.php';
			require_once DISCUSS_CLASSES . '/simpleimage.php';

			if (! DiscussImageHelper::canUpload( $file, $err ))
			{
				if(! $isFromBackend)
				{
					DiscussHelper::setMessageQueue( JText::_( $err ) , 'error');
					$mainframe->redirect(DiscussRouter::_('index.php?option=com_easydiscuss&view=categories', false));
				}
				else
				{
					// From backend
					$mainframe->redirect(DiscussRouter::_('index.php?option=com_easydiscuss&view=categories'), JText::_( $err ), 'error');
				}
				return;
			}

			if (0 != (int)$file['error'])
			{
				if(! $isFromBackend)
				{
					DiscussHelper::setMessageQueue( $file['error'] , 'error');
					$mainframe->redirect(DiscussRouter::_('index.php?option=com_easydiscuss&view=categories', false));
				}
				else
				{
					// From backend
					$mainframe->redirect(DiscussRouter::_('index.php?option=com_easydiscuss&view=categories', false), $file['error'], 'error');
				}
				return;
			}

			// Rename the file 1st.
			$oldAvatar	= (empty($mediaTable->avatar)) ? $default_avatar_type : $mediaTable->avatar;
			$tempAvatar	= '';
			if( $oldAvatar != $default_avatar_type)
			{
				$session   = JFactory::getSession();
				$sessionId = $session->getToken();

				$fileExt 	= JFile::getExt(JPath::clean($target_file_path . '/' . $oldAvatar));
				$tempAvatar	= JPath::clean($target_file_path . '/' . $sessionId . '.' . $fileExt);

				JFile::move($target_file_path . '/' . $oldAvatar, $tempAvatar);
			}
			else
			{
				$isNew  = true;
			}

			if (JFile::exists($target_file))
			{
				if( $oldAvatar != $default_avatar_type)
				{
					//rename back to the previous one.
					JFile::move($tempAvatar, $target_file_path . '/' . $oldAvatar);
				}

				if(! $isFromBackend)
				{
					DiscussHelper::setMessageQueue( JText::sprintf('ERROR.FILE_ALREADY_EXISTS', $relative_target_file) , 'error');
					$mainframe->redirect(DiscussRouter::_('index.php?option=com_easydiscuss&view=categories', false));
				}
				else
				{
					//from backend
					$mainframe->redirect(DiscussRouter::_('index.php?option=com_easydiscuss&view=categories', false), JText::sprintf('ERROR.FILE_ALREADY_EXISTS', $relative_target_file), 'error');
				}
				return;
			}

			if (JFolder::exists($target_file))
			{

				if( $oldAvatar != $default_avatar_type)
				{
					//rename back to the previous one.
					JFile::move($tempAvatar, $target_file_path . '/' . $oldAvatar);
				}

				if(! $isFromBackend)
				{
					//JError::raiseNotice(100, JText::sprintf('ERROR.FOLDER_ALREADY_EXISTS',$relative_target_file));
					DiscussHelper::setMessageQueue( JText::sprintf('ERROR.FOLDER_ALREADY_EXISTS', $relative_target_file) , 'error');
					$mainframe->redirect(DiscussRouter::_('index.php?option=com_easydiscuss&view=categories', false));
				}
				else
				{
					//from backend
					$mainframe->redirect(DiscussRouter::_('index.php?option=com_easydiscuss&view=categories', false), JText::sprintf('ERROR.FILE_ALREADY_EXISTS', $relative_target_file), 'error');
				}
				return;
			}

			$configImageWidth  = DISCUSS_AVATAR_LARGE_WIDTH;
			$configImageHeight = DISCUSS_AVATAR_LARGE_HEIGHT;

			$image = new SimpleImage();
			$image->load($file['tmp_name']);
			$image->resize($configImageWidth, $configImageHeight);
			$image->save($target_file, $image->image_type);

			//now we update the user avatar. If needed, we remove the old avatar.
			if( $oldAvatar != $default_avatar_type)
			{
				if(JFile::exists( $tempAvatar ))
				{
					JFile::delete( $tempAvatar );
				}
			}

			return JFile::makeSafe( $file['name'] );
		}
		else
		{
			return $default_avatar_type;
		}

	}

	public static function wordFilter( $text )
	{
		$config = DiscussHelper::getConfig();

		if( empty( $text ) )
			return $text;

		if( trim($text) == '')
			return $text;

		if($config->get('main_filterbadword', 1) && $config->get('main_filtertext', '') != '')
		{
			require_once DISCUSS_HELPERS . '/filter.php';
			// filter out bad words.
			$bwFilter		= new BadWFilter();
			$textToBeFilter	= explode(',', $config->get('main_filtertext'));

			// lets do some AI here. for each string, if there is a space,
			// remove the space and make it as a new filter text.
			if( count($textToBeFilter) > 0 )
			{
				$newFilterSet   = array();
				foreach( $textToBeFilter as $item)
				{
					if( JString::stristr($item, ' ') !== false )
					{
						$newKeyWord 	= JString::str_ireplace(' ', '', $item);
						$newFilterSet[] = $newKeyWord;
					}
				} // foreach

				if( count($newFilterSet) > 0 )
				{
					$tmpNewFitler	= array_merge($textToBeFilter, $newFilterSet);
					$textToBeFilter	= array_unique($tmpNewFitler);
				}

			}//end if

			$bwFilter->strings	= $textToBeFilter;

			//to be filtered text
			$bwFilter->text		= $text;
			$new_text			= $bwFilter->filter();

			$text				= $new_text;
		}

		return $text;
	}

	/**
	 * Responsible to format message replies.
	 *
	 * @since	3.0
	 * @access	public
	 * @param	Array 	An array of result.
	 */
	public static function formatConversationReplies( &$replies )
	{
		if( !$replies )
		{
			return false;
		}

		foreach( $replies as &$reply )
		{
			$reply->creator 	= DiscussHelper::getTable( 'Profile' );
			$reply->creator->load( $reply->created_by );

			$reply->message	= DiscussHelper::parseContent( $reply->message );
			$reply->lapsed 	= DiscussHelper::getHelper( 'Date' )->getLapsedTime( $reply->created );
		}
	}

	/**
	 * Responsible to format message items.
	 *
	 * @since	3.0
	 * @access	public
	 * @param	Array 	An array of result.
	 */
	public static function formatConversations( &$conversations )
	{
		if( !$conversations )
		{
			return false;
		}

		$my 	= JFactory::getUser();
		$model 	= DiscussHelper::getModel( 'Conversation' );

		foreach( $conversations as &$conversation )
		{
			// Get the participant.
			$participants		= $model->getParticipants( $conversation->id , $my->id );

			$creator 			= DiscussHelper::getTable( 'Profile' );
			$creator->load( $participants[0] );

			$conversation->creator	= $creator;

			$intro 			= $conversation->getLastMessage( $my->id );

			// @TODO: Configurable length.
			$length 		= JString::strlen( $intro );
			$intro 			= JString::substr( strip_tags( $intro ) , 0 , 10 );

			// Append ellipses if necessary.
			if( $length > 10 )
			{
				$intro 	.= JText::_( 'COM_EASYDISCUSS_ELLIPSES' );
			}

			$conversation->intro 	= $intro;
			$conversation->lapsed 	= DiscussHelper::getHelper( 'Date' )->getLapsedTime( $conversation->lastreplied );
		}
	}

	public static function formatPost($posts, $isSearch = false , $isFrontpage = false )
	{
		$config = DiscussHelper::getConfig();

		if( !$posts )
		{
			return $posts;
		}

		$model	= DiscussHelper::getModel( 'Posts' );
		$result = array();

		for($i = 0; $i < count($posts); $i++)
		{
			$row 	= $posts[ $i ];
			$obj 	= DiscussHelper::getTable( 'Post' );
			$obj->bind( $row );

			// Set post owner
			$owner	= DiscussHelper::getTable( 'Profile' );
			$owner->load($row->user_id);

			if ( $row->user_id == 0 || $row->user_type == DISCUSS_POSTER_GUEST )
			{
				$owner->id		= 0;
				$owner->name	= 'Guest';
			}
			else
			{
				$owner->id		= $row->user_id;
				$owner->name	= $owner->getName();
			}

			$obj->user			= $owner;
			$obj->title			= self::wordFilter( $row->title );
			$obj->content		= self::wordFilter( $row->content );
			$obj->isFeatured	= $row->featured;
			$obj->category 		= JText::_( $row->category );

			// get total replies
			$totalReplies		= ( isset( $row->num_replies ) ) ? $row->num_replies : '0';
			$obj->totalreplies	= $totalReplies;

			if ( $totalReplies > 0 )
			{
				// get last reply
				$lastReply	= $model->getLastReply( $row->id );

				if ( !empty( $lastReply ) )
				{
					$replier	= DiscussHelper::getTable( 'Profile' );
					$replier->load( $lastReply->user_id );

					$replier->poster_name	= ($lastReply->user_id) ? $replier->getName() : $lastReply->poster_name;
					$replier->poster_email	= ($lastReply->user_id) ? $replier->user->email : $lastReply->poster_email;

					$obj->reply = $replier;
				}
			}

			//check whether the post is still withing the 'new' duration.
			$obj->isnew		= DiscussHelper::isNew( $row->noofdays );

			//get post duration so far.
			$durationObj	= new stdClass();
			$durationObj->daydiff	= $row->daydiff;
			$durationObj->timediff	= $row->timediff;

			$obj->duration  	= DiscussHelper::getDurationString($durationObj);

			// Some result set may already been optimized using the `totalFavourites` column.
			if( !isset( $row->totalFavourites ) )
			{
				$favouritesModel	= DiscussHelper::getModel( 'Favourites' );

				// Get total favourites based on post id
				$obj->totalFavourites = $favouritesModel->getFavouritesCount( $row->id );
			}
			else
			{
				$obj->totalFavourites 	= $row->totalFavourites;
			}

			if( !$isSearch )
			{
				$postsTagsModel	= DiscussHelper::getModel( 'PostsTags' );

				$tags			= $postsTagsModel->getPostTags( $row->id );
				$obj->tags		= $tags;

				// Some result set may already been optimized using the `polls_cnt` column
				if( isset( $row->polls_cnt ) )
				{
					$obj->polls 		= $row->polls_cnt;
				}
				else
				{
					$obj->polls			= $model->hasPolls( $row->id );
				}

				if( isset( $row->attachments_cnt ) )
				{
					$obj->attachments 	= $row->attachments_cnt;
				}
				else
				{
					$obj->attachments	= $model->hasAttachments( $row->id , DISCUSS_QUESTION_TYPE );
				}

			}
			else
			{
				$obj->tags			= '';
				$obj->polls			= '';
				$obj->attachments	= '';
			}

			if( $config->get('main_password_protection') && !empty( $row->password ) && !DiscussHelper::hasPassword( $row ) )
			{
				$tpl	= new DiscussThemes();
				$tpl->set( 'post' , $obj );
				$obj->content = $tpl->fetch( 'entry.password.php' );

				$obj->introtext = $obj->content;
			}

			// @since 3.0
			// Format introtext here.

			if( !empty( $row->password ) && !DiscussHelper::hasPassword( $row ) )
			{
				if(! $obj->isProtected() )
				{
					$obj->introtext	= $row->content; //display password input form.
					$obj->introtext = strip_tags( $obj->introtext );
				}
			}
			else
			{
				$obj->content_raw 	= $row->content;

				// Remove codes block
				$obj->content 		= EasyDiscussParser::removeCodes( $row->content );

				// bbcode translation if there are any.
				$obj->content 		= self::formatContent($obj);

				$obj->introtext 	= strip_tags($obj->content);

				// Truncate it now.
				$obj->introtext		= JString::substr( $obj->introtext , 0 , $config->get( 'layout_introtextlength' ) ) . JText::_( 'COM_EASYDISCUSS_ELLIPSES' );
			}

			// Apply badword filters here
			$obj->introtext 		= DiscussHelper::wordFilter($obj->introtext);

			// Set the post type suffix and title
			$obj->post_type_suffix 	= $row->post_type_suffix;
			$obj->post_type_title 	= $row->post_type_title;

			// used in search.
			if( isset( $row->itemtype ) )
			{
				$obj->itemtype = $row->itemtype;
			}

			// Assigned user
			if( !$isFrontpage )
			{
				$assignment			= DiscussHelper::getTable( 'PostAssignment' );
				$assignment->load( $row->id );
				$obj->assignment	= $assignment;
			}

			$result[]	= $obj;
		}

		return $result;
	}

	public static function formatComments( $comments )
	{
		$config 	= DiscussHelper::getConfig();

		if( !$comments )
		{
			return false;
		}

		$result 	= array();

		foreach( $comments as $row )
		{
			$duration			= new StdClass();
			$duration->daydiff	= $row->daydiff;
			$duration->timediff	= $row->timediff;

			$comment 	= DiscussHelper::getTable( 'Comment' );
			$comment->bind($row);

			$comment->duration  = DiscussHelper::getDurationString( $duration );

			$creator = DiscussHelper::getTable( 'Profile' );
			$creator->load( $comment->user_id );

			$comment->creator	= $creator;

			if ( $config->get( 'main_content_trigger_comments' ) )
			{
				// process content plugins
				$comment->content	= $comment->comment;

				DiscussEventsHelper::importPlugin( 'content' );
				DiscussEventsHelper::onContentPrepare('comment', $comment);

				$comment->event = new stdClass();

				$results	= DiscussEventsHelper::onContentBeforeDisplay('comment', $comment);
				$comment->event->beforeDisplayContent	= trim(implode("\n", $results));

				$results	= DiscussEventsHelper::onContentAfterDisplay('comment', $comment);
				$comment->event->afterDisplayContent	= trim(implode("\n", $results));

				$comment->comment	= $comment->content;
				unset($comment->content);

				$comment->comment = DiscussHelper::wordFilter( $comment->comment );
			}

			$result[]	= $comment;
		}

		return $result;
	}

	public static function formatReplies( $result , $category = null )
	{
		$config		= DiscussHelper::getConfig();

		if( !$result )
		{
			return $result;
		}
		$my			= JFactory::getUser();
		$replies	= array();

		for($i = 0; $i < count($result); $i++)
		{
			$row				=& $result[$i];
			$reply		= DiscussHelper::getTable( 'Post' );
			$reply->bind( $row );

			$response	= new stdClass();

			if( $row->user_id == 0 || $row->user_type == DISCUSS_POSTER_GUEST )
			{
				$response->id	 = '0';
				$response->name	 = 'Guest'; // TODO: user the poster_name
			}
			else
			{
				$replier		= JFactory::getUser( $row->user_id );
				$response->id	= $replier->id;
				$response->name	= $replier->name;
			}

			//load porfile info and auto save into table if user is not already exist in discuss's user table.
			$creator = DiscussHelper::getTable( 'Profile' );
			$creator->load( $response->id);

			$reply->user			= $creator;
			$reply->content_raw		= $row->content;
			$reply->isVoted			= $row->isVoted;
			$reply->total_vote_cnt	= $row->total_vote_cnt;

			$reply->title			= DiscussHelper::wordFilter( $reply->title);
			$reply->content			= DiscussHelper::wordFilter( $reply->content);


			// Legacy fix when switching from WYSIWYG editor to bbcode.
			$reply->content	= EasyDiscussParser::html2bbcode( $reply->content );

			// Parse bbcodes.
			$reply->content	= self::parseContent( $reply->content, true );

			// Parse @username links.
			$reply->content			= DiscussHelper::getHelper( 'String' )->nameToLink( $reply->content );

			// set for vote status
			$reply->voted			= $reply->hasVoted();

			// get total vote for this reply
			$reply->totalVote		= $reply->sum_totalvote;

			// get the 5 latest voters
			$voters					= DiscussHelper::getVoters($row->id);
			$reply->voters			= $voters->voters;
			$reply->shownVoterCount = $voters->shownVoterCount;

			$reply->minimize		= DiscussHelper::getHelper( 'Post' )->toMinimizePost($row->sum_totalvote);
			$reply->likesAuthor		= DiscussHelper::getHelper( 'Likes' )->getLikesHTML( $row->id, null, null, $reply->getLikeAuthorsObject( $row->id ) );
			$reply->isLike			= DiscussHelper::getHelper( 'Post' )->isLiked( $row->id );

			// get reply comments
			$commentLimit			= $config->get( 'main_comment_pagination' ) ? $config->get( 'main_comment_pagination_count' ) : null;
			$comments				= $reply->getComments( $commentLimit );

			$reply->comments 		= false;

			if( $config->get( 'main_comment' ) )
			{
				$reply->comments 		= DiscussHelper::formatComments( $comments );
			}

			// get reply comments count
			$reply->commentsCount	= $reply->getTotalComments();

			// @rule: Check for url references
			$reply->references  = $reply->getReferences();

			$reply->content 	= DiscussHelper::formatContent( $reply );

			if ( $config->get( 'main_content_trigger_replies' ) )
			{

				// Move aside the original content_raw
				$content_raw_temp = $reply->content_raw;

				// Add the br tags in the content, we do it here so that the content triggers's javascript will not get added with br tags
				// $reply->content_raw = DiscussHelper::bbcodeHtmlSwitcher( $reply, 'reply', false );

				// process content plugins
				DiscussEventsHelper::importPlugin( 'content' );
				DiscussEventsHelper::onContentPrepare('reply', $reply);

				$reply->event = new stdClass();

				$results	= DiscussEventsHelper::onContentBeforeDisplay('reply', $reply);
				$reply->event->beforeDisplayContent	= trim(implode("\n", $results));

				$results	= DiscussEventsHelper::onContentAfterDisplay('reply', $reply);
				$reply->event->afterDisplayContent	= trim(implode("\n", $results));

				// Assign the processed content back
				// $reply->content = $reply->content_raw;

				// Move back the original content_raw
				$reply->content_raw = $content_raw_temp;

			}

			$reply->access 	= $reply->getAccess( $category );
			$replies[] = $reply;
		}

		return $replies;
	}

	public static function formatUsers( $result )
	{
		if( !$result )
		{
			return $result;
		}

		$total	= count( $result );

		$authorIds  = array();
		for( $i =0 ; $i < $total; $i++ )
		{
			$item			= $result[ $i ];
			$authorIds[] 	= $item->id;
		}

		// Reduce SQL queries by pre-loading all author object.
		$authorIds  = array_unique($authorIds);
		$profile	= DiscussHelper::getTable( 'Profile' );
		$profile->init( $authorIds );

		$users	= array();
		for( $i =0 ; $i < $total; $i++ )
		{
			$row	=& $result[ $i ];

			$user	= DiscussHelper::getTable( 'Profile' );
			$user->load( $row->id );

			$users[] = $user;
		}

		return $users;
	}

	public static function getVoters($id, $limit='5')
	{
		$config	= DiscussHelper::getConfig();

		$table	= DiscussHelper::getTable( 'Post' );
		$voters	= $table->getVoters($id, $limit);

		$data					= new stdClass();
		$data->voters			= '';
		$data->shownVoterCount	= '';

		if(!empty($voters))
		{
			$data->shownVoterCount = count($voters);

			foreach($voters as $voter)
			{
				$displayname = $config->get('layout_nameformat');

				switch($displayname)
				{
					case "name" :
						$votername = $voter->name;
						break;
					case "username" :
						$votername = $voter->username;
						break;
					case "nickname" :
					default :
						$votername = (empty($voter->nickname)) ? $voter->name : $voter->nickname;
						break;
				}

				if(!empty($data->voters))
				{
					$data->voters .= ', ';
				}

				$data->voters .= '<a href="' . DiscussRouter::_( 'index.php?option=com_easydiscuss&view=profile&id=' . $voter->user_id ) . '">' . $votername . '</a>';
			}
		}

		return $data;
	}

	public static function getJoomlaVersion()
	{
		$jVerArr	= explode('.', JVERSION);
		$jVersion	= $jVerArr[0] . '.' . $jVerArr[1];

		return $jVersion;
	}

	public static function isJoomla30()
	{
		return DiscussHelper::getJoomlaVersion() >= '3.0';
	}

	public static function isJoomla25()
	{
		return DiscussHelper::getJoomlaVersion() >= '1.6' && DiscussHelper::getJoomlaVersion() <= '2.5';
	}

	public static function isJoomla15()
	{
		return DiscussHelper::getJoomlaVersion() == '1.5';
	}

	public static function getDefaultSAIds()
	{
		$saUserId	= '62';

		if(DiscussHelper::getJoomlaVersion() >= '1.6')
		{
			$saUsers	= DiscussHelper::getSAUsersIds();
			$saUserId	= $saUsers[0];
		}

		return $saUserId;
	}

	/**
	 * Used in J1.5!. To retrieve list of superadmin users's id.
	 * array
	 */
	public static function getSAUsersIds15()
	{
		$db = DiscussHelper::getDBO();

		$query = 'SELECT `id` FROM `#__users`';
		$query .= ' WHERE (LOWER( usertype ) = ' . $db->Quote('super administrator');
		$query .= ' OR `gid` = ' . $db->Quote('25') . ')';
		$query .= ' ORDER BY `id` ASC';

		$db->setQuery($query);
		$result = $db->loadResultArray();

		$result = (empty($result)) ? array( '62' ) : $result;

		return $result;
	}

	/**
	 * Used in J1.6!. To retrieve list of superadmin users's id.
	 * array
	 */
	public static function getSAUsersIds()
	{
		if( self::getJoomlaVersion() < '1.6' ) {
			return self::getSAUsersIds15();
		}

		$db = DiscussHelper::getDBO();

		$query	= 'SELECT a.`id`, a.`title`';
		$query	.= ' FROM `#__usergroups` AS a';
		$query	.= ' LEFT JOIN `#__usergroups` AS b ON a.lft > b.lft AND a.rgt < b.rgt';
		$query	.= ' GROUP BY a.id';
		$query	.= ' ORDER BY a.lft ASC';

		$db->setQuery($query);
		$result = $db->loadObjectList();

		$saGroup    = array();
		foreach($result as $group)
		{
			if(JAccess::checkGroup($group->id, 'core.admin'))
			{
				$saGroup[]  = $group;
			}
		}

		//now we got all the SA groups. Time to get the users
		$saUsers = array();
		if(count($saGroup) > 0)
		{
			foreach($saGroup as $sag)
			{
				$userArr	= JAccess::getUsersByGroup($sag->id);
				if(count($userArr) > 0)
				{
					foreach($userArr as $user)
					{
						$saUsers[] = $user;
					}
				}
			}
		}

		return $saUsers;
	}

	/**
	 * parentId - if this option spcified, it will list the parent and all its childs categories.
	 * userId - if this option specified, it only return categories created by this userId
	 * outType - the output type. Currently supported links and drop down selection
	 * eleName - the element name of this populated categeries provided the outType os dropdown selection.
	 * default - the default value. If given, it used at dropdown selection (auto select)
	 * isWrite - determine whether the categories list used in write new page or not.
	 * isPublishedOnly - if this option is true, only published categories will fetched.
	 */

	public static function populateCategories($parentId, $userId, $outType, $eleName, $default = false, $isWrite = false, $isPublishedOnly = false, $showPrivateCat = true , $disableContainers = false , $customClass = 'inputbox full-width' )
	{
		$catModel 	= DiscussHelper::getModel( 'Categories' );

		$parentCat	= null;

		if(! empty($userId))
		{
			$parentCat  = $catModel->getParentCategories($userId, 'poster', $isPublishedOnly, $showPrivateCat);
		}
		else if(! empty($parentId))
		{
			$parentCat  = $catModel->getParentCategories($parentId, 'category', $isPublishedOnly, $showPrivateCat);
		}
		else
		{
			$parentCat  = $catModel->getParentCategories('', 'all', $isPublishedOnly, $showPrivateCat);
		}

		// If the result == null
		if (empty($parentCat)) {
			return;
		}

		$ignorePrivate  = false;

		switch($outType)
		{
			case 'link' :
				$ignorePrivate  = false;
				break;
			case 'select':
			default:
				$ignorePrivate  = true;
				break;
		}

		$selectACLOnly = false;
		if( $isWrite )
		{
			$ignorePrivate	= false;
			$selectACLOnly	= true;
		}

		if(! empty($parentCat))
		{
			for($i = 0; $i < count($parentCat); $i++)
			{
				$parent =& $parentCat[$i];

				//reset
				$parent->childs = null;

				DiscussHelper::buildNestedCategories($parent->id, $parent, $ignorePrivate, $isPublishedOnly, $showPrivateCat, $selectACLOnly);
			}//for $i
		}//end if !empty $parentCat

		$formEle    = '';
		foreach($parentCat as $category)
		{
			$selected   = ($category->id == $default) ? ' selected="selected"' : '';

			if( $default === false )
			{
				$selected   = $category->default ? ' selected="selected"' : '';
			}

			$style 		= '';
			$disabled	= '';

			// @rule: Test if the category should just act as a container
			if( $disableContainers )
			{
				$disabled	= $category->container	? ' disabled="disabled"' : '';
				$style		= $disabled ? ' style="font-weight:700;"' : '';
			}

			$formEle   .= '<option value="' . $category->id . '" ' . $selected . $disabled . $style . '>' . JText::_( $category->title ) . '</option>';

			DiscussHelper::accessNestedCategories($category, $formEle, '0', $default, $outType , '' , $disableContainers );
		}

		$selected = empty($default) ? ' selected="selected"' : '';

		$html	= '';
		$html	.= '<select name="' . $eleName . '" id="' . $eleName .'" class="' . $customClass . '">';
		if(! $isWrite)
			$html	.=	'<option value="0">' . JText::_('COM_EASYDISCUSS_SELECT_PARENT_CATEGORY') . '</option>';
		else
			$html	.= '<option value="0" ' . $selected . '>' . JText::_('COM_EASYDISCUSS_SELECT_CATEGORY') . '</option>';
		$html	.=	$formEle;
		$html	.= '</select>';

		return $html;
	}

	public static function buildNestedCategories($parentId, $parent, $ignorePrivate = false, $isPublishedOnly = false, $showPrivate = true, $selectACLOnly = false )
	{
		$catsModel	= self::getModel( 'Categories' );

		// [model:category]
		$catModel	= self::getModel( 'Category' );

		$childs		= $catsModel->getChildCategories($parentId, $isPublishedOnly, $showPrivate);

		$aclType	= ( $selectACLOnly ) ? DISCUSS_CATEGORY_ACL_ACTION_SELECT : DISCUSS_CATEGORY_ACL_ACTION_VIEW;
		$accessibleCatsIds	= DiscussHelper::getAccessibleCategories( $parentId, $aclType );

		if(! empty($childs))
		{
			for($j = 0; $j < count($childs); $j++)
			{
				$child  = $childs[$j];
				$child->count	= $catModel->getTotalPostCount($child->id);
				$child->childs	= null;

				if(! $ignorePrivate)
				{
					if( count( $accessibleCatsIds ) > 0)
					{
						$access = false;
						foreach( $accessibleCatsIds as $canAccess)
						{
							if( $canAccess->id == $child->id)
							{
								$access = true;
							}
						}

						if( !$access )
							continue;

					}
					else
					{
						continue;
					}
				}

				if(! DiscussHelper::buildNestedCategories($child->id, $child, $ignorePrivate, $isPublishedOnly, $showPrivate, $selectACLOnly))
				{
					$parent->childs[]   = $child;
				}
			}// for $j

			if( ! empty( $parent->childs ) )
			{
				$parent->childs	= array_reverse( $parent->childs );
			}
		}
		else
		{
			return false;
		}
	}

	public static function accessNestedCategories($arr, &$html, $deep='0', $default='0', $type='select', $linkDelimiter = '' , $disableContainers = false )
	{
		$config = DiscussHelper::getConfig();
		if(isset($arr->childs) && is_array($arr->childs))
		{
			$sup	= '<sup>|_</sup>';
			$space	= '';
			$ld		= (empty($linkDelimiter)) ? '>' : $linkDelimiter;

			if($type == 'select' || $type == 'list')
			{
				$deep++;
				for($d=0; $d < $deep; $d++)
				{
					$space .= '&nbsp;&nbsp;&nbsp;';
				}
			}

			if($type == 'list' && !empty($arr->childs))
			{
				$html .= '<ul>';
			}

			for($j	= 0; $j < count($arr->childs); $j++)
			{
				$child  = $arr->childs[$j];

				switch($type)
				{
					case 'select':
						$selected    = ($child->id == $default) ? ' selected="selected"' : '';

						if( !$default )
						{
							$selected   = $child->default ? ' selected="selected"' : '';
						}

						$disabled 		= '';
						$style 			= '';

						// @rule: Test if the category should just act as a container
						if( $disableContainers )
						{
							$disabled	= $child->container	? ' disabled="disabled"' : '';
							$style		= $disabled ? ' style="font-weight:700;"' : '';
						}

						$html   	.= '<option value="'.$child->id.'" ' . $selected . $disabled . $style . '>' . $space . $sup . $child->title . '</option>';
						break;
					case 'list':
						$expand 	= !empty($child->childs)? '<span onclick="EasyDiscuss.$(this).parents(\'li:first\').toggleClass(\'expand\');">[+] </span>' : '';
						$html 		.= '<li><div>' . $space . $sup . $expand . '<a href="' . DiscussRouter::getCategoryRoute( $child->id ) . '">' . $child->title . '</a> <b>(' . $child->count . ')</b></div>';
						break;
					case 'listlink':
						$str = '<li><a href="' . DiscussRouter::getCategoryRoute( $child->id ) . '">';
						$str   		.= (empty($html)) ? $child->title : $ld . '&nbsp;' . $child->title;
						$str        .= '</a></li>';
						$html   	.= $str;
						break;
					default:
						$str    	 = '<a href="' . DiscussRouter::getCategoryRoute( $child->id ) . '">';
						//str   		.= (empty($html)) ? $child->title : $ld . '&nbsp;' . $child->title;
						$str   		.= (empty($html)) ? $child->title : $ld . '&nbsp;' . $child->title;
						$str        .= '</a></li>';
						$html   	.= $str;
				}

				if( !$config->get('layout_category_one_level', 0) )
				{
					DiscussHelper::accessNestedCategories($child, $html, $deep, $default, $type, $linkDelimiter , $disableContainers );
				}


				if($type == 'list')
				{
					$html .= '</li>';
				}
			}

			if($type == 'list' && !empty($arr->childs))
			{
				$html .= '</ul>';
			}
		}
		else
		{
			return false;
		}
	}

	public static function accessNestedCategoriesId($arr, &$newArr)
	{
		if(isset($arr->childs) && is_array($arr->childs))
		{
			//$modelSubscribe	= DiscussHelper::getModel( 'Subscribe' );
			//$subscribers	= $modelSubscribe->getSiteSubscribers('instant');

			for($j = 0; $j < count($arr->childs); $j++)
			{
				$child = $arr->childs[$j];

				$newArr[] = $child->id;
				DiscussHelper::accessNestedCategoriesId($child, $newArr);
			}
		}
		else
		{
			return false;
		}
	}

	/**
	 * function to retrieve the linkage backward from a child id.
	 * return the full linkage from child up to parent
	 */

	public static function populateCategoryLinkage($childId)
	{
		$arr		= array();
		$category	= DiscussHelper::getTable( 'Category' );
		$category->load($childId);

		$obj		= new stdClass();
		$obj->id	= $category->id;
		$obj->title	= $category->title;
		$obj->alias	= $category->alias;

		$arr[]  = $obj;

		if((!empty($category->parent_id)))
		{
			DiscussHelper::accessCategoryLinkage($category->parent_id, $arr);
		}

		$arr    = array_reverse($arr);
		return $arr;

	}

	public static function accessCategoryLinkage($childId, &$arr)
	{
		$category	= DiscussHelper::getTable( 'Category' );

		$category->load($childId);

		$obj		= new stdClass();
		$obj->id	= $category->id;
		$obj->title	= $category->title;
		$obj->alias	= $category->alias;

		$arr[]  = $obj;

		if((!empty($category->parent_id)))
		{
			DiscussHelper::accessCategoryLinkage($category->parent_id, $arr);
		}
		else
		{
			return false;
		}
	}

	public static function showSocialButtons( $post, $position = 'vertical' )
	{
		require_once DISCUSS_CLASSES . '/google.php';
		require_once DISCUSS_CLASSES . '/googleshare.php';
		require_once DISCUSS_CLASSES . '/twitter.php';
		require_once DISCUSS_CLASSES . '/facebook.php';
		require_once DISCUSS_CLASSES . '/digg.php';
		require_once DISCUSS_CLASSES . '/linkedin.php';

		$config		= DiscussHelper::getConfig();
		$document	= JFactory::getDocument();

		$googlebuzz		= '';
		$twitterbutton	= '';


		$twitterbutton	= DiscussTwitter::getButtonHTML( $post, $position );
		$googleone		= DiscussGoogleOne::getButtonHTML( $post, $position );
		$googleShare	= DiscussGoogleShare::getButtonHTML( $post, $position );

		$facebookLikes	= DiscussFacebook::getLikeHTML( $post, $position );
		$digg			= DiscussDigg::getButtonHTML( $post , $position );
		$linkedIn		= DiscussLinkedIn::getButtonHTML( $post , $position );

		$float = ($position == 'vertical') ? 'class="discuss-post-share float-r"' : 'class="discuss-post-share"';

		$socialButtons = '';

		$socialButtonsHere = $digg . $linkedIn . $googlebuzz . $googleone . $googleShare . $twitterbutton . $facebookLikes;

		if( !empty($socialButtonsHere) )
		{
			$socialButtons  = '<div id="dc_share" '.$float.'>' . $digg . $linkedIn . $googlebuzz . $googleone . $googleShare . $twitterbutton . $facebookLikes . '</div>';
		}
		echo $socialButtons;
	}

	/**
	 * $post - post jtable object
	 * $parent - post's parent id.
	 * $isNew - indicate this is a new post or not.
	 */

	public static function sendNotification( $post, $parent = 0, $isNew, $postOwner, $prevPostStatus)
	{
		JFactory::getLanguage()->load( 'com_easydiscuss' , JPATH_ROOT );

		$config = DiscussHelper::getConfig();
		$notify	= DiscussHelper::getNotification();

		$emailPostTitle = $post->title;
		$modelSubscribe		= self::getModel( 'Subscribe' );

		//get all admin emails
		$adminEmails = array();
		$ownerEmails = array();
		$newPostOwnerEmails = array();
		$postSubscriberEmails = array();
		$participantEmails = array();

		$catSubscriberEmails = array();

		if( empty( $parent ) )
		{
			// only new post we notify admin.
			if($config->get( 'notify_admin' ))
			{
				$admins = $notify->getAdminEmails();

				if(! empty($admins))
				{
					foreach($admins as $admin)
					{
						$adminEmails[]   = $admin->email;
					}
				}
			}

			// notify post owner too when moderate is on
			if( !empty( $postOwner ) )
			{
				$postUser    			= JFactory::getUser( $postOwner );
				$newPostOwnerEmails[]  	= $postUser->email;
			}
			else
			{
				$newPostOwnerEmails[]	= $post->poster_email;
			}

		}
		else
		{
			// if this is a new reply, notify post owner.
			$parentTable		= DiscussHelper::getTable( 'Post' );
			$parentTable->load( $parent );

			$emailPostTitle = $parentTable->title;

			$oriPostAuthor  = $parentTable->user_id;

			if( !$parentTable->user_id )
			{
				$ownerEmails[]	= $parentTable->poster_email;
			}
			else
			{
				$oriPostUser    = JFactory::getUser( $oriPostAuthor );
				$ownerEmails[]  = $oriPostUser->email;
			}
		}

		$emailSubject	= ( empty( $parent ) ) ? JText::sprintf('COM_EASYDISCUSS_NEW_POST_ADDED', $post->id , $emailPostTitle ) : JText::sprintf( 'COM_EASYDISCUSS_NEW_REPLY_ADDED', $parent, $emailPostTitle );
		$emailTemplate	= ( empty( $parent ) ) ? 'email.subscription.site.new.php' : 'email.post.reply.new.php';

		//get all site's subscribers email that want to receive notification immediately
		$subscriberEmails	= array();
		$subscribers		= array();


		// @rule: Specify the default name and avatar
		$authorName 			= $post->poster_name;
		$authorAvatar 			= DISCUSS_JURIROOT . '/media/com_easydiscuss/images/default.png';



		// @rule: Only process author items that belongs to a valid user.
		if( !empty( $postOwner ) )
		{
			$profile			= DiscussHelper::getTable( 'Profile' );
			$profile->load( $postOwner );

			$authorName 		= $profile->getName();
			$authorAvatar 		= $profile->getAvatar();
		}

		if( $config->get('main_sitesubscription') && ($isNew || $prevPostStatus == DISCUSS_ID_PENDING) )
		{

			//$modelSubscribe		= self::getModel( 'Subscribe' );
			$subscribers        = $modelSubscribe->getSiteSubscribers('instant','',$post->category_id);
			$postSubscribers	= $modelSubscribe->getPostSubscribers( $post->parent_id );

			// This was added because the user allow site wide notification (as in all subscribers should get notified) but category subscribers did not get it.
			$catSubscribers		= $modelSubscribe->getCategorySubscribers( $post->id );

			if(! empty($subscribers))
			{
				foreach($subscribers as $subscriber)
				{
					$subscriberEmails[]   = $subscriber->email;
				}
			}
			if(! empty($postSubscribers))
			{
				foreach($postSubscribers as $postSubscriber)
				{
					$postSubscriberEmails[]   = $postSubscriber->email;
				}
			}
			if(! empty($catSubscribers))
			{
				foreach($catSubscribers as $catSubscriber)
				{
					$catSubscriberEmails[]   = $catSubscriber->email;
				}
			}
		}


		// Notify Participants if this is a reply
		if( !empty( $parent ) && $config->get( 'notify_participants' ) && ($isNew || $prevPostStatus == DISCUSS_ID_PENDING) )
		{
			$participantEmails = DiscussHelper::getHelper( 'Mailer' )->_getParticipants( $post->parent_id );

			$participantEmails  = array_unique( $participantEmails );

			// merge into owneremails. dirty hacks.
			if( count( $participantEmails ) > 0 )
			{
				$newPostOwnerEmails = array_merge( $newPostOwnerEmails, $participantEmails );
			}
		}


		if( !empty( $adminEmails ) || !empty( $subscriberEmails ) || !empty( $newPostOwnerEmails ) || !empty( $postSubscriberEmails ) || $config->get( 'notify_all' ) )
		{
			$emails = array_unique(array_merge($adminEmails, $subscriberEmails, $newPostOwnerEmails, $postSubscriberEmails, $catSubscriberEmails));

			// prepare email content and information.
			$emailData						= array();
			$emailData['postTitle']			= $emailPostTitle;
			$emailData['postAuthor']		= $authorName;
			$emailData['postAuthorAvatar']	= $authorAvatar;
			$emailData['replyAuthor']		= $authorName;
			$emailData['replyAuthorAvatar']	= $authorAvatar;
			$emailData['comment']			= $post->content;
			$emailData['postContent' ]		= $post->trimEmail( $post->content );
			$emailData['replyContent']		= $post->trimEmail( $post->content );

			$attachments	= $post->getAttachments();
			$emailData['attachments']	= $attachments;

			// get the correct post id in url, the parent post id should take precedence
			$postId	= empty( $parent ) ? $post->id : $parentTable->id;

			$emailData['postLink']		= DiscussRouter::getRoutedURL('index.php?option=com_easydiscuss&view=post&id=' . $postId, false, true);

			if( $config->get( 'notify_all' ) && $post->published == DISCUSS_ID_PUBLISHED )
			{
				$emailData['emailTemplate']	= 'email.subscription.site.new.php';
				$emailData['emailSubject']	= JText::sprintf('COM_EASYDISCUSS_NEW_QUESTION_ASKED', $post->id , $post->title);
				DiscussHelper::getHelper( 'Mailer' )->notifyAllMembers( $emailData, $newPostOwnerEmails );
			}
			else
			{
				//insert into mailqueue
				foreach ($emails as $email)
				{

					if ( in_array($email, $subscriberEmails) || in_array($email, $postSubscriberEmails) || in_array($email, $newPostOwnerEmails) )
					{
						$doContinue = false;

						// these are subscribers
						if (!empty($subscribers))
						{
							foreach ($subscribers as $key => $value)
							{
								if ($value->email == $email)
								{
									$emailData['unsubscribeLink']	= DiscussHelper::getUnsubscribeLink( $subscribers[$key], true, true);
									$notify->addQueue($email, $emailSubject, '', $emailTemplate, $emailData);
									$doContinue = true;
									break;
								}
							}
						}

						if( $doContinue )
							continue;

						if (!empty($postSubscribers))
						{

							foreach ($postSubscribers as $key => $value)
							{
								if ($value->email == $email)
								{

									$emailData['unsubscribeLink']	= DiscussHelper::getUnsubscribeLink( $postSubscribers[$key], true, true);
									$notify->addQueue($email, $emailSubject, '', $emailTemplate, $emailData);
									$doContinue = true;
									break;
								}
							}
						}

						if( $doContinue )
							continue;


						if (!empty($newPostOwnerEmails))
						{

							$emailSubject = JText::sprintf( 'COM_EASYDISCUSS_NEW_POST_ADDED', $emailPostTitle, $post->id );

							foreach ($newPostOwnerEmails as $ownerEmail)
							{

								//$emailData['unsubscribeLink']	= DiscussHelper::getUnsubscribeLink( $ownerEmail, true, true);
								$notify->addQueue($email, $emailSubject, '', $emailTemplate, $emailData);
								$doContinue = true;
								break;
							}
						}

					}
					else
					{

						// non-subscribers will not get the unsubscribe link
						$notify->addQueue($email, $emailSubject, '', $emailTemplate, $emailData);
					}
				}
			}
		}
	}

	public static function getUserRepliesHTML( $postId, $excludeLastReplyUser	= false)
	{
		$model		= DiscussHelper::getModel( 'Posts' );
		$replies	= $model->getUserReplies($postId, $excludeLastReplyUser);

		$html = '';
		if( !empty( $replies ) )
		{
			$tpl	= new DiscussThemes();
			$tpl->set( 'replies'	, $replies );
			$html	=  $tpl->fetch( 'main.item.replies.php' );
		}

		return $html;
	}

	public static function getUserAcceptedReplyHTML( $postId )
	{
		$model	= JDiscussHelper::getModel( 'Posts' );
		$reply	= $model->getAcceptedReply( $postId );

		$html	= '';
		if( ! empty( $reply ) )
		{
			$tpl	= new DiscussThemes();
			$tpl->set( 'reply'	, $reply );
			$html	=  $tpl->fetch( 'main.item.answered.php' );
		}

		return $html;
	}

	public static function isSiteSubscribed( $userId )
	{
		if( !class_exists( 'EasyDiscussModelSubscribe') )
		{
			jimport( 'joomla.application.component.model' );
			JLoader::import( 'subscribe' , DISCUSS_MODELS );
		}
		$model	= DiscussHelper::getModel( 'Subscribe' );

		$user	= JFactory::getUser( $userId );

		$subscription = array();
		$subscription['type']	= 'site';
		$subscription['email']	= $user->email;
		$subscription['cid']	= 0;

		$result = $model->isSiteSubscribed( $subscription );

		return ( !isset($result['id']) ) ? '0' : $result['id'];
	}

	public static function isPostSubscribed( $userId, $postId )
	{
		$model	= DiscussHelper::getModel( 'Subscribe' );

		$user	= JFactory::getUser( $userId );

		$subscription = array();
		$subscription['type']	= 'post';
		$subscription['userid']	= $user->id;
		$subscription['email']	= $user->email;
		$subscription['cid']	= $postId;

		$result = $model->isPostSubscribedEmail( $subscription );

		return ( !isset($result['id']) ) ? '0' : $result['id'];
	}

	public static function isMySubscription( $userid, $type, $subId)
	{
		$model 		= DiscussHelper::getModel( 'Subscribe' );
		return $model->isMySubscription($userid, $type, $subId);
	}

	public static function hasPassword( $post )
	{
		$session	= JFactory::getSession();
		$password	= $session->get( 'DISCUSSPASSWORD_' . $post->id , '' , 'com_easydiscuss' );

		if( $password == $post->password )
		{
			return true;
		}
		return false;
	}

	public static function getUserComponent()
	{
		return ( DiscussHelper::getJoomlaVersion() >= '1.6' ) ? 'com_users' : 'com_user';
	}

	public static function getUserComponentLoginTask()
	{
		return ( DiscussHelper::getJoomlaVersion() >= '1.6' ) ? 'user.login' : 'login';
	}

	public static function getAccessibleCategories( $parentId = 0, $type = DISCUSS_CATEGORY_ACL_ACTION_VIEW, $customUserId = '' )
	{
		static $accessibleCategories = array();

		if( !empty($customUserId) )
		{
			$my = JFactory::getUser( $customUserId );
		}
		else
		{
			$my	= JFactory::getUser();
		}

		// $sig 	= serialize( array($type, $my->id, $parentId) );

		$sig    = (int) $my->id . '-' . (int) $parentId . '-' . (int) $type;


		//if( !array_key_exists($sig, $accessibleCategories) )
		if(! isset( $accessibleCategories[$sig] ) )
		{

			$db	= DiscussHelper::getDBO();

			$gids		= '';
			$catQuery	= 	'select distinct a.`id`, a.`private`';
			$catQuery	.=  ' from `#__discuss_category` as a';


			if( $my->id == 0 )
			{
				$catQuery	.=  ' where (a.`private` = ' . $db->Quote('0') . ' OR ';
			}
			else
			{
				$catQuery	.=  ' where (a.`private` = ' . $db->Quote('0') . ' OR a.`private` = ' . $db->Quote('1') . ' OR ';
			}


			$gid	= array();
			$gids	= '';

			if( DiscussHelper::getJoomlaVersion() >= '1.6' )
			{
				$gid    = array();
				if( $my->id == 0 )
				{
					$gid 	= JAccess::getGroupsByUser(0, false);
				}
				else
				{
					$gid 	= JAccess::getGroupsByUser($my->id, false);
				}
			}
			else
			{
				$gid	= DiscussHelper::getUserGids();
			}


			if( count( $gid ) > 0 )
			{
				foreach( $gid as $id)
				{
					$gids   .= ( empty($gids) ) ? $db->Quote( $id ) : ',' . $db->Quote( $id );
				}

				$catQuery   .=	'  a.`id` IN (';
				$catQuery .= '		select b.`category_id` from `#__discuss_category_acl_map` as b';
				$catQuery .= '			where b.`category_id` = a.`id` and b.`acl_id` = '. $db->Quote( $type );
				$catQuery .= '			and b.`type` = ' . $db->Quote('group');
				$catQuery .= '			and b.`content_id` IN (' . $gids . ')';

				//logged in user
				if( $my->id != 0 )
				{
					$catQuery .= '			union ';
					$catQuery .= '			select b.`category_id` from `#__discuss_category_acl_map` as b';
					$catQuery .= '				where b.`category_id` = a.`id` and b.`acl_id` = ' . $db->Quote( $type );
					$catQuery .= '				and b.`type` = ' . $db->Quote('user');
					$catQuery .= '				and b.`content_id` = ' . $db->Quote( $my->id );
				}
				$catQuery   .= ')';

			}

			$catQuery   .= ')';
			$catQuery   .= ' AND a.parent_id = ' . $db->Quote($parentId);

			$db->setQuery($catQuery);
			$result = $db->loadObjectList();

			$accessibleCategories[ $sig ] = $result;

		}

		return $accessibleCategories[ $sig ];
	}

	public static function getPrivateCategories( $acltype = DISCUSS_CATEGORY_ACL_ACTION_VIEW )
	{
		$db 			= DiscussHelper::getDBO();
		$my 			= JFactory::getUser();
		static $result	= array();

		$excludeCats	= array();

		$sig    = (int) $my->id . '-' . (int) $acltype;

		if(! isset( $result[ $sig ] ) )
		{
			if($my->id == 0)
			{
				$catQuery	= 	'select distinct a.`id`, a.`private`';
				$catQuery	.=  ' from `#__discuss_category` as a';
				$catQuery	.=	' 	left join `#__discuss_category_acl_map` as b on a.`id` = b.`category_id`';
				$catQuery	.=	' 		and b.`acl_id` = ' . $db->Quote( $acltype );
				$catQuery	.=	' 		and b.`type` = ' . $db->Quote( 'group' );
				$catQuery	.=  ' where a.`private` != ' . $db->Quote('0');

				$gid	= array();
				$gids	= '';


				if( DiscussHelper::getJoomlaVersion() >= '1.6' )
				{
					// $gid	= JAccess::getGroupsByUser(0, false);

					$gid	= DiscussHelper::getUserGroupId($my);
				}
				else
				{
					$gid	= DiscussHelper::getUserGids();
				}

				if( count( $gid ) > 0 )
				{
					foreach( $gid as $id)
					{
						$gids   .= ( empty($gids) ) ? $db->Quote( $id ) : ',' . $db->Quote( $id );
					}
					$catQuery	.= ' and a.`id` NOT IN (';
					$catQuery	.= '     SELECT c.category_id FROM `#__discuss_category_acl_map` as c ';
					$catQuery	.= '        WHERE c.acl_id = ' .$db->Quote( $acltype );
					$catQuery	.= '        AND c.type = ' . $db->Quote('group');
					$catQuery	.= '        AND c.content_id IN (' . $gids . ') )';
				}

				$db->setQuery($catQuery);
				$result = $db->loadObjectList();
			}
			else
			{
				$result = self::getAclCategories ( $acltype, $my->id );
			}

			for($i=0; $i < count($result); $i++)
			{
				$item =& $result[$i];
				$item->childs = null;

				DiscussHelper::buildNestedCategories($item->id, $item, true);

				$catIds		= array();
				$catIds[]	= $item->id;
				DiscussHelper::accessNestedCategoriesId($item, $catIds);

				$excludeCats	= array_merge($excludeCats, $catIds);
			}

			$result[ $sig ] = $excludeCats;
		}

		return $result[ $sig ];
	}

	public static function getAclCategories ( $type = DISCUSS_CATEGORY_ACL_ACTION_VIEW, $userId = '', $parentId = false )
	{
		static $categories = array();

		//$sig = serialize( array($type, $userId, $parentId) );
		$sig = (int) $type . '-' . (int) $userId . '-' . (int) $parentId;

		//if( !array_key_exists($sig, $categories) )
		if( ! isset( $categories[$sig] ) )
		{
			$db		= DiscussHelper::getDBO();
			$gid	= '';

			if( DiscussHelper::getJoomlaVersion() >= '1.6' )
			{
				if( $userId == '' )
				{
					$gid	= JAccess::getGroupsByUser(0, false);
				}
				else
				{
					$gid = JAccess::getGroupsByUser($userId, false);
				}
			}
			else
			{
				$gid = JAccess::getGroupsByUser($userId, false);
			}

			$gids   = '';
			if( count( $gid ) > 0 )
			{
				foreach( $gid as $id)
				{
					$gids   .= ( empty($gids) ) ? $db->Quote( $id ) : ',' . $db->Quote( $id );
				}
			}

			$query = 'select c.`id` from `#__discuss_category` as c';
			$query .= ' where not exists (';
			$query .= '		select b.`category_id` from `#__discuss_category_acl_map` as b';
			$query .= '			where b.`category_id` = c.`id` and b.`acl_id` = '. $db->Quote( $type );
			$query .= '			and b.`type` = ' . $db->Quote('group');
			$query .= '			and b.`content_id` IN (' . $gids . ')';

			//logged in user
			if(! empty($userId) )
			{
				$query .= '			union ';
				$query .= '			select b.`category_id` from `#__discuss_category_acl_map` as b';
				$query .= '				where b.`category_id` = c.`id` and b.`acl_id` = ' . $db->Quote( $type );
				$query .= '				and b.`type` = ' . $db->Quote('user');
				$query .= '				and b.`content_id` = ' . $db->Quote( $userId );
			}

			$query .= '      )';
			$query .= ' and c.`private` = ' . $db->Quote( DISCUSS_PRIVACY_ACL );
			if( $parentId !== false )
				$query .= ' and c.`parent_id` = ' . $db->Quote($parentId);

			$db->setQuery($query);

			$categories[$sig] = $db->loadObjectList();
		}

		return $categories[$sig];
	}

	public static function getTable( $tableName , $prefix = 'Discuss' , $config = array() )
	{
		// Sanitize and prepare the table class name.
		$type       = preg_replace('/[^A-Z0-9_\.-]/i', '', $tableName);
		$tableClass = $prefix . ucfirst($type);

		// Only try to load the class if it doesn't already exist.
		if (!class_exists($tableClass))
		{
			// Search for the class file in the JTable include paths.
			$path 	= DISCUSS_TABLES . '/' . strtolower( $type ) . '.php';

			// Import the class file.
			include_once $path;
		}

		return JTable::getInstance( $type , $prefix , $config );
	}

	/**
	 * Retrieve model from easydiscuss.
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string	The model's name.
	 * @return	mixed
	 */
	public static function getModel( $name , $backend = false )
	{
		static $model = array();

		$key = $backend ? 'backend' : 'frontend';

		if( !isset( $model[ $name . $key ] ) )
		{
			$file	= JString::strtolower( $name );

			if( $backend )
			{
				$path 	= JPATH_ROOT . '/administrator/components/com_easydiscuss/models/' . $file . '.php';
			}
			else
			{
				$path	= DISCUSS_MODELS  . '/' . $file . '.php';
			}

			jimport('joomla.filesystem.path');
			if( !JFile::exists( $path ))
			{
				JError::raiseWarning( 0, 'Model file not found.' );
			}

			$modelClass	= 'EasyDiscussModel' . ucfirst( $name );

			if( !class_exists( $modelClass ) )
			{
				require_once( $path );
			}

			$model[ $name . $key ] = new $modelClass();
		}

		return $model[ $name . $key ];
	}

	public static function getPagination($total, $limitstart, $limit, $prefix = '')
	{
		static $instances;

		if (!isset($instances))
		{
			$instances = array();
		}

		$signature = serialize(array($total, $limitstart, $limit, $prefix));

		if (empty($instances[$signature]))
		{
			require_once DISCUSS_CLASSES . '/pagination.php';
			$pagination	= new DiscussPagination($total, $limitstart, $limit, $prefix);

			$instances[$signature] = &$pagination;
		}

		return $instances[$signature];
	}

	/**
	 * Retrieve @JUser object based on the given email address.
	 *
	 * @access	public
	 * @param	string $email	The user's email address.
	 * @return	JUser			@JUser object.
	 **/
	public static function getUserByEmail( $email )
	{
		$email	= strtolower( $email );

		$db		= DiscussHelper::getDBO();

		$query	= 'SELECT ' . $db->nameQuote( 'id' ) . ' FROM '
				. $db->nameQuote( '#__users' ) . ' '
				. 'WHERE LOWER(' . $db->nameQuote( 'email' ) . ') = ' . $db->Quote( $email );
		$db->setQuery( $query );
		$id		= $db->loadResult();

		if( !$id )
		{
			return false;
		}

		return JFactory::getUser( $id );
	}

	public static function getUserGids( $userId = '' )
	{
		$userId = empty($userId) ? null : $userId;
		$user = JFactory::getUser($userId);

		$groups = JAccess::getGroupsByUser($user->id);
		$ids = array();

		foreach ($groups as $group) {
			$ids[] = $group;
		}

		return $ids;
	}

	public static function getUserRankScore( $userId, $percentage = true)
	{
		return DiscussHelper::getHelper( 'Ranks' )->getScore( $userId , $percentage );
	}

	public static function getUserRanks( $userId )
	{
		return DiscussHelper::getHelper( 'Ranks' )->getRank( $userId );
	}

	public static function getJoomlaUserGroups( $cid = '' )
	{
		$db = DiscussHelper::getDBO();

		if(self::getJoomlaVersion() >= '1.6')
		{
			$query = 'SELECT a.id, a.title AS `name`, COUNT(DISTINCT b.id) AS level';
			$query .= ' , GROUP_CONCAT(b.id SEPARATOR \',\') AS parents';
			$query .= ' FROM #__usergroups AS a';
			$query .= ' LEFT JOIN `#__usergroups` AS b ON a.lft > b.lft AND a.rgt < b.rgt';
		}
		else
		{
			$query	= 'SELECT `id`, `name`, 0 as `level` FROM ' . $db->nameQuote('#__core_acl_aro_groups') . ' a ';
		}

		// Condition
		$where  = array();

		// We need to filter out the ROOT and USER dummy records.
		if(self::getJoomlaVersion() < '1.6')
		{
			$where[] = '(a.`id` > 17 AND a.`id` < 26)';
		}

		if( !empty( $cid ) )
		{
			$where[] = ' a.`id` = ' . $db->quote($cid);
		}
		$where = ( count( $where ) ? ' WHERE ' .implode( ' AND ', $where ) : '' );

		$query  .= $where;

		// Grouping and ordering
		if( self::getJoomlaVersion() >= '1.6' )
		{
			$query	.= ' GROUP BY a.id';
			$query	.= ' ORDER BY a.lft ASC';
		}
		else
		{
			$query 	.= ' ORDER BY a.id';
		}

		$db->setQuery( $query );
		$result = $db->loadObjectList();

		if( DiscussHelper::getJoomlaVersion() < '1.6' )
		{
			$guest = new stdClass();
			$guest->id		= '0';
			$guest->name	= 'Public';
			$guest->level	= '0';
			array_unshift( $result, $guest );
		}

		return $result;
	}

	public static function getSubscriptionHTML( $userid, $cid = 0, $type = DISCUSS_ENTITY_TYPE_POST, $class = '', $simpleText = true )
	{
		$config 		= DiscussHelper::getConfig();

		// If guest subscription is disabled, do not show subscription link at all.
		if( !$userid && !$config->get( 'main_allowguestsubscribe' ) )
		{
			return '';
		}

		$model 			= DiscussHelper::getModel( 'Subscribe', false );
		$type			= ($type == 'index') ? 'site' : $type;

		$isSubscribed	= $model->isSubscribed( $userid, $cid, $type );
		$sid			= $isSubscribed ? $isSubscribed : 0;

		$tpl	= new DiscussThemes();
		$tpl->set( 'isSubscribed', $isSubscribed );
		$tpl->set( 'type', $type );
		$tpl->set( 'cid', $cid );
		$tpl->set( 'sid', $sid );
		$tpl->set( 'simple', $simpleText );
		$tpl->set( 'class', $class );

		return $tpl->fetch( 'subscription.php' );
	}

	public static function getUnsubscribeLink($subdata, $external = false, $html = false)
	{
		$unsubdata	= base64_encode("type=".$subdata->type."\r\nsid=".$subdata->id."\r\nuid=".$subdata->userid."\r\ntoken=".md5($subdata->id.$subdata->created));

		$link		= DiscussRouter::getRoutedURL('index.php?option=com_easydiscuss&controller=subscription&task=unsubscribe&data='.$unsubdata, false, $external);

		return $link;
	}

	/*
	 * Return class name according to user's group.
	 * e.g. 'reply-usergroup-1 reply-usergroup-2'
	 *
	 */
	public static function userToClassname($jUserObj, $classPrefix = 'reply', $delimiter = '-')
	{
		if (is_numeric($jUserObj))
		{
			$jUserObj	= JFactory::getUser($jUserObj);
		}

		if( !$jUserObj instanceof JUser )
		{
			return '';
		}

		static $classNames;

		if (!isset($classNames))
		{
			$classNames = array();
		}

		$signature = serialize(array($jUserObj->id, $classPrefix, $delimiter));

		if (!isset($classNames[$signature]))
		{
			$classes	= array();

			$classes[]	= $classPrefix . $delimiter . 'user' . $delimiter . $jUserObj->id;

			if (property_exists($jUserObj, 'gid'))
			{
				$classes[]	= $classPrefix . $delimiter . 'usergroup' . $delimiter . $jUserObj->get( 'gid' );
			}
			else
			{
				$groups		= $jUserObj->getAuthorisedGroups();

				foreach($groups as $id)
				{
					$classes[] = $classPrefix . $delimiter . 'usergroup' . $delimiter . $id;
				}
			}

			$classNames[$signature] = implode(' ', $classes);
		}

		return $classNames[$signature];
	}

	/**
	 * Retrieve similar question based on the keywords
	 *
	 * @access	public
	 * @param	string	$keywords
	 */
	public static function getSimilarQuestion( $text = '' )
	{
		if( empty( $text ) )
			return '';

		$config = Discusshelper::getConfig();

		if(! $config->get( 'main_similartopic', 0 ) )
		{
			return '';
		}

		// $text   = 'how to configure facebook integration?';
		$itemLimit  = $config->get('main_similartopic_limit', '5');
		$db = DiscussHelper::getDBO();

		// remove punctuation from the string.
		$text = preg_replace("/(?![.=$'â?])\p{P}/u", "", $text);
		//$text = preg_replace("/(?![.=$'â?)\p{P}/u", "", $text);

		$queryExclude   = '';
		if( ! $config->get( 'main_similartopic_privatepost', 0 ) )
		{
			$excludeCats    = DiscussHelper::getPrivateCategories();
			if(! empty($excludeCats))
			{
				$queryExclude .= ' AND a.`category_id` NOT IN (' . implode(',', $excludeCats) . ')';
			}
		}


		// lets check if db has more than 2 records or not.
		$query = 'SELECT COUNT(1) FROM `#__discuss_posts` as a';
		$query .= ' WHERE a.`published` = ' . $db->Quote('1');
		$query .= ' AND a.`parent_id` = ' . $db->Quote('0');
		$query .= $queryExclude;

		$db->setQuery( $query );
		$rCount = $db->loadResult();

		if( $rCount <= 2 )
		{
			// full index search will fail if record has only two. So we do a normal like search.
			$phrase = 'or';
			$words	= explode(' ', $text);

			$wheres = array();
			foreach ($words as $word) {

				$word		= $db->Quote('%'.$db->getEscaped($word, true).'%', false);
				$wheres2	= array();
				$wheres2[]	= 'a.title LIKE '.$word;
				$wheres2[]	= 'a.content LIKE '.$word;

				$wheres[]	= implode(' OR ', $wheres2);
			}

			$whereString = '(' . implode( ($phrase == 'all' ? ') AND (' : ') OR ('), $wheres) . ')';

			$query = 'select a.`id`,  a.`title`, 0 AS score';
			$query .= ' FROM `#__discuss_posts` as a';
			$query .= ' WHERE a.`published` = ' . $db->Quote('1');
			$query .= ' AND a.`parent_id` = ' . $db->Quote('0');
			$query .= ' AND ' . $whereString;
			$query .= $queryExclude;
			$query .= ' LIMIT ' . $itemLimit;

			$db->setQuery( $query );
			$result = $db->loadObjectList();
			return $result;
		}

		// we know table has more than 3 records.
		// lets do a full index search.

		// lets get the tags match the keywords
		$tagkeywords = explode(' ', $text);
		for($i = 0; $i < count( $tagkeywords ); $i++ )
		{
			if( JString::strlen($tagkeywords[$i]) > 3 )
			{
				$tagkeywords[$i] = $tagkeywords[$i] . '*';
			}
			else
			{
				$tagkeywords[$i] = $tagkeywords[$i];
			}
		}
		$tagkeywords   = implode(' ', $tagkeywords);

		$query	= 'select `id` FROM `#__discuss_tags`';
		$query	.= ' WHERE MATCH(`title`) AGAINST (' . $db->Quote($tagkeywords) . ' IN BOOLEAN MODE)';

		$db->setQuery( $query );

		$tagResults = $db->loadResultArray();

		// now try to get the main topic
		$query = 'select a.`id`,  a.`title`';
		$query .= ' FROM `#__discuss_posts` as a';
		$query .= ' WHERE MATCH(a.`title`,a.`content`) AGAINST (' . $db->Quote( $text ) . ' WITH QUERY EXPANSION)';
		$query .= ' AND a.`published` = ' . $db->Quote('1');
		$query .= ' AND a.`parent_id` = ' . $db->Quote('0');
		$query .= $queryExclude;
		$query .= ' LIMIT ' . $itemLimit;

		$tagQuery   = '';
		if( count( $tagResults ) > 0 )
		{
			$tagQuery = 'select a.`id`,  a.`title`';
			$tagQuery .= ' FROM `#__discuss_posts` as a';
			$tagQuery .= ' 	INNER JOIN `#__discuss_posts_tags` as b ON a.id = b.post_id';
			$tagQuery .= ' WHERE b.`tag_id` IN (' . implode( ',', $tagResults) . ')';
			$tagQuery .= ' AND a.`published` = ' . $db->Quote('1');
			$tagQuery .= ' AND a.`parent_id` = ' . $db->Quote('0');
			$tagQuery .= $queryExclude;
			$tagQuery .= ' LIMIT ' . $itemLimit;

			$query  = 'SELECT * FROM (' . $query . ' UNION ' . $tagQuery . ') AS x LIMIT ' . $itemLimit;
		}



		$db->setQuery( $query );
		$result = $db->loadObjectList();
		return $result;

	}

	/**
	 * Retrieves the html block for board statistics.
	 *
	 * @since	3.0
	 * @access	public
	 */
	public static function getBoardStatistics()
	{
		$config = DiscussHelper::getConfig();
		$allowed = true;
		$disallowedGroups = $config->get('main_exclude_frontend_statistics');

		if (!$config->get('main_frontend_statistics')) {
			return;
		}

		if (!empty($disallowedGroups)) {

			//Remove whitespace
			$disallowedGroups = trim($disallowedGroups);
			$disallowedGroups = explode(',', $disallowedGroups);

			$my = JFactory::getUser();
			$groups = $my->groups;

			$result = array_intersect($groups, $disallowedGroups);

			$allowed = !$result ? true : false;
		}

		if (!$allowed) {
			return;
		}

		$theme = new DiscussThemes();

		$postModel = DiscussHelper::getModel('Posts');
		$totalPosts	= $postModel->getTotal();

		$resolvedPosts = $postModel->getTotalResolved();
		$unresolvedPosts = $postModel->getUnresolvedCount();

		$userModel = DiscussHelper::getModel('Users');
		$totalUsers	= $userModel->getTotalUsers();

		$latestMember = DiscussHelper::getTable('Profile');
		$latestMember->load($userModel->getLatestUser());

		// Total guests
		$totalGuests = $userModel->getTotalGuests();

		// Online users
		$onlineUsers = $userModel->getOnlineUsers();

		$theme->set('latestMember', $latestMember);
		$theme->set('unresolvedPosts', $unresolvedPosts);
		$theme->set('resolvedPosts', $resolvedPosts);
		$theme->set('totalUsers', $totalUsers);
		$theme->set('totalPosts', $totalPosts);
		$theme->set('onlineUsers', $onlineUsers);
		$theme->set('totalGuests', $totalGuests);

		return $theme->fetch('frontpage.statistics.php');
	}

	/**
	 * Retrieve the html block for who's viewing this page.
	 *
	 * @access	public
	 * @param	string	$url
	 */
	public static function getWhosOnline( $uri = '' )
	{
		$config		= DiscussHelper::getConfig();
		$enabled	= $config->get( 'main_viewingpage' );

		if( !$enabled )
		{
			return '';
		}

		if( !empty($uri) )
		{
			$hash 	= md5( $uri );
		}
		else
		{
			$hash	= md5( JRequest::getURI() );
		}

		require_once DISCUSS_CLASSES . '/themes.php';

		$model 	= self::getModel( 'Users' );
		$users	= $model->getPageViewers( $hash );

		if( !$users )
		{
			return '';
		}

		$theme	= new DiscussThemes();
		$theme->set( 'users' , $users );
		return $theme->fetch( 'users.online.php' );
	}

	public static function getListLimit()
	{
		$app		= JFactory::getApplication();
		$default 	= DiscussHelper::getJConfig()->getValue( 'list_limit' );

		if( $app->isAdmin() )
		{
			return $default;
		}

		$app	= JFactory::getApplication();
		$menus	= $app->getMenu();
		$menu	= $menus->getActive();
		$limit	= -2;

		if( is_object( $menu ) )
		{
			$params	= DiscussHelper::getRegistry( $menu->params );
			$limit	= $params->get( 'limit' , '-2' );
		}

		if( $limit == '-2' )
		{
			// Use default configurations.
			$config	= DiscussHelper::getConfig();
			$limit	= $config->get( 'layout_list_limit', '-2' );
		}

		// Revert to joomla's pagination if configured to inherit from Joomla
		if( $limit == '0' || $limit == '-1' || $limit == '-2' )
		{
			$limit		= $default;
		}

		return $limit;
	}

	public static function getRegistrationLink()
	{
		$config	= DiscussHelper::getConfig();

		$default	= JRoute::_( 'index.php?option=com_user&view=register' );
		if( DiscussHelper::getJoomlaVersion() >= '1.6' )
		{
			$default	= JRoute::_( 'index.php?option=com_users&view=registration' );
		}

		switch( $config->get( 'main_login_provider' ) )
		{
			case 'joomla':
				$link	= $default;
				break;

			case 'cb':
				$link	= JRoute::_( 'index.php?option=com_comprofiler&task=registers' );
				break;

			case 'easysocial':
				$easysocial 	= DiscussHelper::getHelper( 'EasySocial' );

				if( $easysocial->exists() )
				{
					$link 	= FRoute::registration();
				}
				else
				{
					$link 	= $default;
				}

				break;

			case 'jomsocial':
 				$link	= JRoute::_( 'index.php?option=com_community&view=register' );
				$file 	= JPATH_ROOT . '/components/com_community/libraries/core.php';

				if( JFile::exists( $file ) )
				{
					require_once( $file );
					$link 	= CRoute::_( 'index.php?option=com_community&view=register' );
				}
			break;
		}

		return $link;
	}

	public static function getResetPasswordLink()
	{
		$config 	= DiscussHelper::getConfig();

		$default	= JRoute::_( 'index.php?option=com_user&view=reset' );

		if( DiscussHelper::getJoomlaVersion() >= '1.6' )
		{
			$default	= JRoute::_( 'index.php?option=com_users&view=reset' );
		}


		switch( $config->get( 'main_login_provider' ) )
		{
			case 'joomla':
			case 'cb':
			case 'jomsocial':
				$link	= $default;
				break;

			case 'easysocial':

				$easysocial 	= DiscussHelper::getHelper( 'EasySocial' );

				if( $easysocial->exists() )
				{
					$link 	= FRoute::profile( array( 'layout' => 'forgetPassword' ) );
				}
				else
				{
					$link 	= $default;
				}


			break;
		}

		return $link;
	}

	public static function getDefaultRepliesSorting()
	{
		$config 		= DiscussHelper::getConfig();
		$defaultFilter  = $config->get( 'layout_replies_sorting' );

		switch( $defaultFilter )
		{
			case 'voted':
				if( ! $config->get( 'main_allowvote') )
				{
					$defaultFilter  = 'replylatest';
				}
			break;
			case 'likes':
				if( ! $config->get( 'main_likes_replies') )
				{
					$defaultFilter  = 'replylatest';
				}
			break;
			case 'latest':
			break;
			case 'oldest':
			default:
				$defaultFilter  = 'replylatest';
			break;
		}

		return $defaultFilter;
	}

	public static function setPageTitle( $text = '' )
	{
		// now check if site name is needed or not.
		$app	= JFactory::getApplication();
		$doc	= JFactory::getDocument();

		$menu	= $app->getMenu();
		$item	= $menu->getActive();

		if( empty( $text ) )
		{
			// use menu item title
			if( is_object( $item ) )
			{
				$params			= $item->params;

				if(! $params instanceof JRegistry )
				{
					$params			= DiscussHelper::getRegistry( $item->params );
				}

				$text = 	$params->get('page_title', '');

				if( empty( $text ) )
				{
					if( isset( $item->title ) )
					{
						$text = 	$item->title;
					}
					else
					{
						$text = 	$item->name;
					}
				}
			}
		}



		// Check for empty title and add site name if param is set
		if (empty($text)) {
			$title = $app->getCfg('sitename');
		}
		elseif ($app->getCfg('sitename_pagetitles', 0) == 1) {
			$text = JText::sprintf('JPAGETITLE', $app->getCfg('sitename'), $text);
		}
		elseif ($app->getCfg('sitename_pagetitles', 0) == 2) {
			$text = JText::sprintf('JPAGETITLE', $text, $app->getCfg('sitename'));
		}

		$doc->setTitle($text);
	}


	public static function setMeta()
	{
		$config	= DiscussHelper::getConfig();
		$db		= DiscussHelper::getDBO();


		$menu	= JFactory::getApplication()->getMenu();
		$item	= $menu->getActive();

		$result	= new stdClass();
		$result->description	= $config->get( 'main_description' );
		$result->keywords		= '';

		$description 	= '';

		if( is_object( $item ) )
		{
			$params			= $item->params;

			if(! $params instanceof JRegistry )
			{
				$params			= DiscussHelper::getRegistry( $item->params );
			}

			$description	= $params->get( 'menu-meta_description' , '' );
			$keywords		= $params->get( 'menu-meta_keywords' , '' );

			if( ! empty ( $description ) )
			{
				$result->description	= $description;
			}

			if( ! empty ( $keywords ) )
			{
				$result->keywords	= $keywords;
			}
		}

		$document = JFactory::getDocument();
		if ( empty( $result->keywords ) && empty( $result->description ) )
		{
			// Get joomla default description.
			$jConfig	= DiscussHelper::getJConfig();
			$joomlaDesc	= $jConfig->getValue('MetaDesc');

			$metaDesc	= $description . ' - ' . $joomlaDesc;
			$document->setMetadata('description', $metaDesc);
		}
		else
		{
			if ( !empty( $result->keywords ) )
			{
				$document->setMetadata('keywords', $result->keywords);
			}

			if ( !empty( $result->description ) )
			{
				$document->setMetadata('description', $result->description);
			}
		}
	} //end function setMeta

	public static function getFrontpageCategories()
	{
		$catModel		= self::getModel( 'Categories' );
		$newPostCount	= 0;

		if( !$categories = $catModel->getCategories() )
		{
			return array();
		}

		foreach ($categories as $category)
		{
			$postModel = self::getModel( 'Posts' );
			$category->newCount = $postModel->getNewCount( '' , $category->id , null , false );
			$newPostCount += $category->newCount;
		}

		// Temporary store in user state.
		$app = JFactory::getApplication();
		$app->setUserState( 'com_easydiscuss.helper.totalnewpost', $newPostCount );

		return $categories;
	}

	public static function log( $var = '', $force = 0 )
	{
		$debugroot = DISCUSS_HELPERS . '/debug';

		$firephp = false;
		$chromephp = false;

		if( JFile::exists( $debugroot . '/fb.php' ) && JFile::exists( $debugroot . '/FirePHP.class.php' ) )
		{
			include_once( $debugroot . '/fb.php' );
			fb( $var );
		}

		if( JFile::exists( $debugroot . '/chromephp.php' ) )
		{
			include_once( $debugroot . '/chromephp.php' );
			ChromePhp::log( $var );
		}
	}

	public static function isModerator( $categoryId = null , $userId = null )
	{
		return DiscussHelper::getHelper( 'Moderator' )->isModerator( $categoryId , $userId );
	}

	public static function getUserGroupId( JUser $user )
	{
		$config = DiscussHelper::getConfig();

		$groups = JAccess::getGroupsByUser($user->id);

		return $groups;
	}

	/**
	 * Method determines if the content needs to be parsed through any parser or not.
	 *
	 * @since	3.0
	 * @access	public
	 * @param	string	The content's string.
	 */
	public static function parseContent( $content, $forceBBCode=false )
	{
		$config		= self::getConfig();

		$content	= DiscussHelper::getHelper( 'String' )->escape( $content );

		// Pass it to bbcode parser.
		$content	= EasyDiscussParser::bbcode( $content );
		$content	= nl2br($content);

		//Remove BR in pre tag
		$content = preg_replace_callback('/<pre.*?\>(.*?)<\/pre>/ims', array( 'EasyDiscussParser' , 'removeBr' ) , $content );
		$content = preg_replace_callback('/<ol.*?\>(.*?)<\/ol>/ims', array( 'EasyDiscussParser' , 'removeBr' ) , $content );
		$content = preg_replace_callback('/<ul.*?\>(.*?)<\/ul>/ims', array( 'EasyDiscussParser' , 'removeBr' ) , $content );

		$content = str_ireplace("</pre><br />", '</pre>', $content);
		$content = str_ireplace("</ol><br />", '</ol>', $content);
		$content = str_ireplace("</ol>\r\n<br />", '</ol>', $content);
		$content = str_ireplace("</ul><br />", '</ul>', $content);
		$content = str_ireplace("</ul>\r\n<br />", '</ul>', $content);
		$content = str_ireplace("</pre>\r\n<br />", '</pre>', $content);
		$content = str_ireplace("</blockquote><br />", '</blockquote>', $content);
		$content = str_ireplace("</blockquote>\r\n<br />", '</blockquote>', $content);

		return $content;
	}

	/**
	 * Triggers plugins.
	 *
	 * @since	3.0
	 * @access	public
	 */
	public static function triggerPlugins( $type , $eventName , &$data ,$hasReturnValue = false )
	{
		DiscussEventsHelper::importPlugin( $type );

		$args 			= array( 'post' , &$data );

		$returnValue 	= call_user_func_array( 'DiscussEventsHelper::' . $eventName , $args );

		if( $hasReturnValue )
		{
			return trim( implode( "\n" , $returnValue ) );
		}

		return;
	}

	/**
	 * Renders a module position in the template
	 */
	public static function renderModule( $position , $attributes = array() , $content = null )
	{
		jimport( 'joomla.application.module.helper' );

		$doc		= JFactory::getDocument();
		$renderer	= $doc->loadRenderer( 'module' );
		$buffer		= '';
		$modules	= JModuleHelper::getModules( $position );

		foreach( $modules as $module )
		{
			$theme	= new DiscussThemes();
			$theme->set( 'position'	, $position );
			$theme->set( 'output' 	, $renderer->render( $module , $attributes , $content ) );
			$buffer .= $theme->fetch( 'modules.item.php' );
		}

		return $buffer;
	}

	public static function getEditorType( $type = '' )
	{
		// Cater for #__discuss_posts column content_type
		$config = self::getConfig();

		if( !empty($type) )
		{
			if( $type == 'question' )
			{
				if( $config->get( 'layout_editor' ) == 'bbcode' )
				{
					return 'bbcode';
				}
				else
				{
					return 'html';
				}
			}
			if( $type == 'reply' )
			{
				if( $config->get( 'layout_reply_editor' ) == 'bbcode' )
				{
					return 'bbcode';
				}
				else
				{
					return 'html';
				}
			}

		}
		return;
	}

	/**
	 * Formats the content of a post
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public static function formatContent($post)
	{
		$config = DiscussHelper::getConfig();

		// Determine the current editor
		$editor = !$post->parent_id == 'questions' ? $config->get('layout_editor') : $config->get('layout_reply_editor');

		// If the post is bbcode source and the current editor is bbcode
		if (($post->content_type == 'bbcode' || is_null($post->content_type)) && $editor == 'bbcode') {

			$content = $post->content_raw;

			// strip this kind of tag -> &nbsp; &amp; 
			// $content = strip_tags(html_entity_decode($content));

			// Allow syntax highlighter even on html codes.
			$content = EasyDiscussParser::replaceCodes($content);

			$content = EasyDiscussParser::bbcode($content , true);

			// Since this is a bbcode content and source, we want to replace \n with <br /> tags.
			$content = nl2br($content);

			// $content = EasyDiscussParser::removeBrTag( $content );
		}

		// If the admin decides to switch from bbcode to wysiwyg editor, we need to format it back
		if( $post->content_type == 'bbcode' && $editor != 'bbcode' )
		{
			$content 	= $post->content_raw;

			//strip this kind of tag -> &nbsp; &amp;
			$content = strip_tags(html_entity_decode($content));

			// Since the original content is bbcode, we don't really need to do any replacements.
			// Just feed it in through bbcode formatter.
			$content	= EasyDiscussParser::bbcode( $content );
		}

		// If the admin decides to switch from wysiwyg to bbcode, we need to fix the content here.
		if( $post->content_type != 'bbcode' && !is_null($post->content_type) && $editor == 'bbcode' )
		{
			$content	= $post->content_raw;

			// Switch html back to bbcode
			$content 	= EasyDiscussParser::html2bbcode( $content );

			// Update the quote messages
			$content 	= EasyDiscussParser::quoteBbcode( $content );
		}

		// If the content is from wysiwyg and editor is also wysiwyg, we only do specific formatting.
		if( $post->content_type != 'bbcode' && $editor != 'bbcode' )
		{
			$content 	= $post->content_raw;

			// Allow syntax highlighter even on html codes.
			$content 	= EasyDiscussParser::replaceCodes( $content );
		}

		// Apply word censorship on the content
		$content	= DiscussHelper::wordFilter( $content );

		return $content;
	}

	// For displaying on frontend
	public static function bbcodeHtmlSwitcher( $post = '', $type = '', $isEditing = false )
	{
		$config = DiscussHelper::getConfig();

		if( $type == 'question' )
		{
			$editor = $config->get( 'layout_editor' );
		}
		else if( $type == 'reply' )
		{
			$editor = $config->get( 'layout_reply_editor' );
		}
		else if( $type == 'signature' || $type == 'description' )
		{
			$temp = $post;
			$post = new stdClass();
			$post->content_raw = $temp;
			$post->content_type = 'bbcode';
			$editor = 'bbcode';
		}

		if( $editor != 'bbcode' )
		{
			$editor = 'html';
		}

		if( $post->content_type == 'bbcode' )
		{
			if( $editor == 'bbcode' )
			{
				//If content_type is bbcode and editor is bbcode
				if( $isEditing )
				{
					$content = $post->content_raw;
				}
				else
				{
					$content = $post->content_raw;
					//$content = DiscussHelper::getHelper( 'String' )->escape( $content );
					$content = EasyDiscussParser::bbcode( $content );
					$content = EasyDiscussParser::removeBrTag( $content );
				}
			}
			else
			{
				//If content_type is bbcode and editor is html
				// Need content raw to work
				//$content = DiscussHelper::getHelper( 'String' )->escape( $post->content_raw );
				$content = $post->content_raw;
				$content = EasyDiscussParser::bbcode( $content );
				$content = EasyDiscussParser::removeBrTag( $content );
			}
		}
		else // content_type is html
		{
			if( $editor == 'bbcode' )
			{
				//If content_type is html and editor is bbcode
				if( $isEditing )
				{
					$content = EasyDiscussParser::quoteBbcode( $post->content_raw );
					$content = EasyDiscussParser::smiley2bbcode( $content ); // we need to parse smiley 1st before we parse htmltobbcode.
					$content = EasyDiscussParser::html2bbcode( $content );

				}
				else
				{
					$content = $post->content_raw;
					//Quote all bbcode here
					$content = EasyDiscussParser::quoteBbcode( $content );
				}
			}
			else
			{
				//If content_type is html and editor is html

				$content = $post->content_raw;
			}
		}

		// Apply censorship
		$content = DiscussHelper::wordFilter( $content );

		return $content;
	}

	public static function getLoginLink( $returnURL = '' )
	{
		$config 	= DiscussHelper::getConfig();

		if( !empty( $returnURL ) )
		{
			$returnURL	= '&return=' . $returnURL;
		}

		if( DiscussHelper::getJoomlaVersion() >= '1.6' )
		{
			$link 	= DiscussRouter::_('index.php?option=com_users&view=login' . $returnURL );
		}
		else
		{
			$link	= DiscussRouter::_( 'index.php?option=com_user&view=login' . $returnURL );
		}

		return $link;
	}

	public static function getPostStatusAndTypes( $posts = null)
	{
		if( empty($posts) )
		{
			return;
		}

		$badgesTable	= DiscussHelper::getTable( 'Profile' );

		foreach( $posts as $post )
		{
			$badgesTable->load( $post->user->id );
			$post->badges = $badgesTable->getBadges();

			// Translate post status from integer to string
			switch( $post->post_status )
			{
				case '0':
					$post->post_status_class = '';
					$post->post_status = '';
					break;
				case '1':
					$post->post_status_class = '-on-hold';
					$post->post_status = JText::_( 'COM_EASYDISCUSS_POST_STATUS_ON_HOLD' );
					break;
				case '2':
					$post->post_status_class = '-accept';
					$post->post_status = JText::_( 'COM_EASYDISCUSS_POST_STATUS_ACCEPTED' );
					break;
				case '3':
					$post->post_status_class = '-working-on';
					$post->post_status = JText::_( 'COM_EASYDISCUSS_POST_STATUS_WORKING_ON' );
					break;
				case '4':
					$post->post_status_class = '-reject';
					$post->post_status = JText::_( 'COM_EASYDISCUSS_POST_STATUS_REJECT' );
					break;
				default:
					$post->post_status_class = '';
					$post->post_status = '';
					break;
			}

			$alias = $post->post_type;
			$modelPostTypes = DiscussHelper::getModel( 'Post_types' );

			// Get each post's post status title
			$title = $modelPostTypes->getTitle( $alias );
			$post->post_type = $title;

			// Get each post's post status suffix
			$suffix = $modelPostTypes->getSuffix( $alias );
			$post->suffix = $suffix;
		}

		return $posts;
	}

	public static function isModerateThreshold( $userId = null )
	{
		$config 	= DiscussHelper::getConfig();
		$limit = $config->get( 'moderation_threshold' );
		$db = DiscussHelper::getDBO();

		$query  = 'SELECT COUNT(1) as `CNT` FROM `#__discuss_posts` AS a';

		$query  .= ' WHERE a.`user_id` = ' . $db->Quote($userId);
		$query  .= ' AND a.`published` = ' . $db->Quote('1');

		$db->setQuery($query);

		$result = $db->loadResult();

		if( $limit !=0)
		{
			if( $result <= $limit)
			{
				return false;
			}
			else
			{
				return true;
			}
		}
	}

	public static function validateCaptcha($data)
	{
		require_once(DISCUSS_CLASSES . '/recaptcha.php');
		require_once(DISCUSS_CLASSES . '/captcha.php');

		$config 	= DiscussHelper::getConfig();

		if ($config->get('antispam_easydiscuss_captcha')) {

			// If captcha is not enforced, we should allow this to bypass
			if (!DiscussHelper::getHelper('Captcha')->showCaptcha()) {
				return true;
			}

			$discussCaptcha	= new stdClass();
			$discussCaptcha->captchaResponse	= JRequest::getVar('captcha-response');;
			$discussCaptcha->captchaId			= JRequest::getInt('captcha-id');

			$state = DiscussHelper::getHelper( 'Captcha' )->verify( $discussCaptcha );

			return $state;
		}

		if ( !DiscussRecaptcha::isRequired()) {
			return true;
		}

		$obj = DiscussRecaptcha::recaptcha_check_answer( $config->get( 'antispam_recaptcha_private' ) , $_SERVER['REMOTE_ADDR'] , $data['recaptcha_challenge_field'] , $data['recaptcha_response_field'] );

		if ($obj->is_valid) {
			return true;
		}

		return false;
	}
}

class Discuss extends DiscussHelper
{

}

class EDC extends DiscussHelper {}
