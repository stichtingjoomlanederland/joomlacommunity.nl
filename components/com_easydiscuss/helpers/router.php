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

jimport( 'joomla.filter.filteroutput');

$jVerArr	= explode('.', JVERSION);
$jVersion	= $jVerArr[0] . '.' . $jVerArr[1];

if( $jVersion <= '3.1' )
{
	jimport( 'joomla.application.router' );
}
else
{
	jimport( 'joomla.libraries.cms.router' );
}


class DiscussJoomlaRouter extends JRouter
{
	public function encode( $segments )
	{
		return parent::_encodeSegments( $segments );
	}
}

require_once JPATH_ROOT . '/components/com_easydiscuss/constants.php';
require_once JPATH_ROOT . '/components/com_easydiscuss/helpers/helper.php';

class DiscussRouter
{
	public static function getMessageRoute( $id = 0 , $xhtml = true , $ssl = null )
	{
		$url 	= self::_( 'index.php?option=com_easydiscuss&view=conversation&layout=read&id=' . $id , $xhtml , $ssl );

		return $url;
	}

	public static function getPrintRoute( $id = 0 , $xhtml = true , $ssl = null )
	{
		$url 	= self::_( 'index.php?option=com_easydiscuss&view=post&id=' . $id . '&tmpl=component&print=1' , $xhtml , $ssl );

		return $url;
	}

	public static function getPostRoute( $id = 0 , $xhtml = true , $ssl = null )
	{
		$url 	= self::_( 'index.php?option=com_easydiscuss&view=post&id=' . $id , $xhtml , $ssl );

		return $url;
	}

	public static function getTagRoute( $id = 0 , $xhtml = true , $ssl = null )
	{
		$url 	= self::_( 'index.php?option=com_easydiscuss&view=tags&id=' . $id , $xhtml , $ssl );

		return $url;
	}

	public static function getBadgeRoute( $id = 0 , $xhtml = true , $ssl = null )
	{
		$url 	= self::_( 'index.php?option=com_easydiscuss&view=badges&layout=listings&id=' . $id , $xhtml , $ssl );

		return $url;
	}

	public static function getCategoryRoute( $id = 0, $xhtml = true , $ssl = null )
	{
		$url 	= self::_( 'index.php?option=com_easydiscuss&view=categories&layout=listings&category_id=' . $id , $xhtml , $ssl );

		return $url;
	}

	public static function getEditRoute( $postId = null , $xhtml = true , $ssl = null )
	{
		$tmp 	= 'index.php?option=com_easydiscuss&view=ask';

		if( !is_null( $postId ) )
		{
			$tmp 	.= '&id=' . $postId;
		}

		$url 	= self::_( $tmp , $xhtml , $ssl );

		return $url;
	}

	public static function getUserRoute( $userId = null ,$xhtml = true , $ssl = null )
	{
		$tmp 	= 'index.php?option=com_easydiscuss&view=profile';

		if( $userId )
		{
			$tmp 	.= '&id=' . $userId;
		}

		return self::_( $tmp , $xhtml , $ssl );
	}

	public static function getAskRoute( $categoryId = null , $xhtml = true , $ssl = null )
	{
		$tmp 	= 'index.php?option=com_easydiscuss&view=ask';

		if( !is_null( $categoryId ) )
		{
			$tmp 	.= '&category=' . $categoryId;
		}

		$url 	= self::_( $tmp , $xhtml , $ssl );

		return $url;
	}

	public static function _($url, $xhtml = true, $ssl = null)
	{
		static $eUri 	= array();
		static $loaded 	= array();

		// to test if the Itemid is there or not.
		$jURL			= $url . $xhtml;
		$index 			= $url . $xhtml;

		if( isset( $loaded[ $index ] ) )
		{
			return $loaded[ $index ];
		}

		// convert the string to variable so that we can access it.
		parse_str( parse_url($url, PHP_URL_QUERY) );

		if( !empty( $Itemid ) )
		{
			$loaded[ $index ]	= JRoute::_( $url , $xhtml , $ssl );

			return $loaded[ $index ];
		}

		$tmpId				= '';
		$config				= DiscussHelper::getConfig();
		$routingBehavior	= $config->get( 'main_routing', 'currentactive');
		$mainframe			= JFactory::getApplication();

		$dropSegment       	= false;

		if( empty($view) )
		{
			$view	= 'index';
		}


		if( $routingBehavior == 'currentactive' || $routingBehavior == 'menuitem')
		{
			$routingMenuItem = $config->get('main_routing_itemid','');

			if( ($routingBehavior == 'menuitem') && ($routingMenuItem != '') )
			{
				$tmpId = $routingMenuItem;
			}

			// @rule: If there is already an item id, try to use the explicitly set one.
			if( empty( $tmpId ) )
			{
				if ( !$mainframe->isAdmin() )
				{
					// Retrieve the active menu item.
					$menu = JFactory::getApplication()->getMenu();
					$item = $menu->getActive();

					if( isset( $item->id ) )
					{
						$tmpId = $item->id;
					}
				}
			}
		}
		else
		{
			// let easydiscuss to determine the best menu itemid.
			switch($view)
			{
				case 'list':
				case 'categories':

					if( isset( $category_id ) )
					{
						$tmpId	= self::getItemIdByCategories( $category_id );
					}
					else
					{
						$tmpId	= self::getItemId( 'categories', '', true );
					}

					if( ! empty( $tmpId ) )
					{
						$dropSegment    = true;
					}

					break;

				case 'users':
					
					$tmpId	= self::getItemId( 'users', '', true );
					

					if( ! empty( $tmpId ) )
					{
						$dropSegment    = true;
					}

					break;

				case 'tags':

					if( isset( $id ) )
					{
						$tmpId	= self::getItemIdByTags( $id );
					}
					else
					{
						$tmpId	= self::getItemId( 'tags', '', true );
					}

					if( ! empty( $tmpId ) )
					{
						$dropSegment    = true;
					}

					break;


				case 'post';
					$postId = $id;

					if( !empty($postId ) )
					{
						$tbl	= DiscussHelper::getTable( 'Post' );
						$tbl->load($postId);
						$tmpId	= self::getItemIdByDiscussion( $tbl->id );

						// we try to get the menu item base on category id
						if( empty( $tmpId ) )
						{
							$tmpId  = self::getItemIdByCategories( $tbl->category_id );
						}
					}

					break;
			}
		}

		if( empty( $tmpId ) )
		{
			// make compatible with version 1.1.x where new question will be view=post&layout=submit
			if( $view == 'post')
			{
				$tmpId		= ( !empty($layout) ) ? DiscussRouter::getItemId($view, $layout) :  DiscussRouter::getItemId($view);
			}
			else
			{
				$tmpId		= DiscussRouter::getItemId($view);
			}
		}

		if( !empty( $tmpId ) )
		{
			if( self::isSefEnabled() && $dropSegment )
			{
				$url    = 'index.php?Itemid=' . $tmpId;
				$loaded[ $index ]	= JRoute::_( $url , $xhtml , $ssl );

				return $loaded[ $index ];
			}
		}

		//check if there is any anchor in the link or not.
		$pos = JString::strpos($url, '#');
		if ($pos === false)
		{
			$url .= '&Itemid='.$tmpId;
		}
		else
		{
			$url = JString::str_ireplace('#', '&Itemid='.$tmpId.'#', $url);
		}


		$loaded[ $index ]	= JRoute::_( $url , $xhtml , $ssl );

		return $loaded[ $index ];
	}

	public static function isSefEnabled()
	{
		$jConfig	= DiscussHelper::getJConfig();
		$isSef		= false;

		//check if sh404sef enabled or not.
		if( defined('sh404SEF_AUTOLOADER_LOADED') && JFile::exists(JPATH_ADMINISTRATOR . '/components/com_sh404sef/sh404sef.class.php'))
		{
			require_once JPATH_ADMINISTRATOR . '/components/com_sh404sef/sh404sef.class.php';
			if( class_exists( 'shRouter' ) )
			{
				$sefConfig = shRouter::shGetConfig();

				if ($sefConfig->Enabled)
					$isSef  = true;
			}
		}

		// if sh404sef not enabled, we check on joomla
		if(! $isSef)
		{
			$isSef = $jConfig->getValue( 'sef' );
		}

		return $isSef;
	}

	public static function getCategoryAliases( $categoryId )
	{
		static $loaded = array();

		if(! isset( $loaded[$categoryId] ) )
		{
			$table	= DiscussHelper::getTable( 'Category' );
			$table->load( $categoryId );

			$items		= array();
			self::recurseCategories( $categoryId , $items );

			$items		= array_reverse( $items );

			$loaded[$categoryId]    = $items;
		}

		return $loaded[$categoryId];
	}

	public static function recurseCategories( $currentId , &$items )
	{
		static $loaded = array();


		if(! isset( $loaded[$currentId] ) )
		{

			$db		= DiscussHelper::getDBO();

			$query	= 'SELECT ' . $db->nameQuote( 'alias' ) . ',' . $db->nameQuote( 'parent_id' ) . ' '
					. 'FROM ' . $db->nameQuote( '#__discuss_category' ) . ' WHERE ' . $db->nameQuote( 'id' ) . '=' . $db->Quote( $currentId );
			$db->setQuery( $query );
			$result	= $db->loadObject();

			$loaded[$currentId] = $result;

		}

		$result = $loaded[$currentId];

		if( !$result )
		{
			return;
		}

		$items[]	= $result->alias;

		if( $result->parent_id != 0 )
		{
			self::recurseCategories( $result->parent_id , $items );
		}
	}

	public static function getAlias( $tableName , $key )
	{
		static $loaded = array();

		$sig    = $tableName . '-' . $key;

		if(! isset( $loaded[$sig] ) )
		{

			$table	= DiscussHelper::getTable( $tableName );
			$table->load( $key );

			$loaded[$sig]   = $table->alias;
		}

		return $loaded[$sig];
	}

	public static function replaceAccents( $string )
	{
		$a = array('Ã„', 'Ã¤', 'Ã–', 'Ã¶', 'Ãœ', 'Ã¼', 'ÃŸ' , 'Ã€', 'Ã', 'Ã‚', 'Ãƒ', 'Ã„', 'Ã…', 'Ã†', 'Ã‡', 'Ãˆ', 'Ã‰', 'ÃŠ', 'Ã‹', 'ÃŒ', 'Ã', 'Ã', 'Ã', 'Ã', 'Ã‘', 'Ã’', 'Ã“', 'Ã”', 'Ã•', 'Ã–', 'Ã˜', 'Ã™', 'Ãš', 'Ã›', 'Ãœ', 'Ã', 'ÃŸ', 'Ã ', 'Ã¡', 'Ã¢', 'Ã£', 'Ã¤', 'Ã¥', 'Ã¦', 'Ã§', 'Ã¨', 'Ã©', 'Ãª', 'Ã«', 'Ã¬', 'Ã­', 'Ã®', 'Ã¯', 'Ã±', 'Ã²', 'Ã³', 'Ã´', 'Ãµ', 'Ã¶', 'Ã¸', 'Ã¹', 'Ãº', 'Ã»', 'Ã¼', 'Ã½', 'Ã¿', 'Ä€', 'Ä', 'Ä‚', 'Äƒ', 'Ä„', 'Ä…', 'Ä†', 'Ä‡', 'Äˆ', 'Ä‰', 'ÄŠ', 'Ä‹', 'ÄŒ', 'Ä', 'Ä', 'Ä', 'Ä', 'Ä‘', 'Ä’', 'Ä“', 'Ä”', 'Ä•', 'Ä–', 'Ä—', 'Ä˜', 'Ä™', 'Äš', 'Ä›', 'Äœ', 'Ä', 'Ä', 'ÄŸ', 'Ä ', 'Ä¡', 'Ä¢', 'Ä£', 'Ä¤', 'Ä¥', 'Ä¦', 'Ä§', 'Ä¨', 'Ä©', 'Äª', 'Ä«', 'Ä¬', 'Ä­', 'Ä®', 'Ä¯', 'Ä°', 'Ä±', 'Ä²', 'Ä³', 'Ä´', 'Äµ', 'Ä¶', 'Ä·', 'Ä¹', 'Äº', 'Ä»', 'Ä¼', 'Ä½', 'Ä¾', 'Ä¿', 'Å€', 'Å', 'Å‚', 'Åƒ', 'Å„', 'Å…', 'Å†', 'Å‡', 'Åˆ', 'Å‰', 'ÅŒ', 'Å', 'Å', 'Å', 'Å', 'Å‘', 'Å’', 'Å“', 'Å”', 'Å•', 'Å–', 'Å—', 'Å˜', 'Å™', 'Åš', 'Å›', 'Åœ', 'Å', 'Å', 'ÅŸ', 'Å ', 'Å¡', 'Å¢', 'Å£', 'Å¤', 'Å¥', 'Å¦', 'Å§', 'Å¨', 'Å©', 'Åª', 'Å«', 'Å¬', 'Å­', 'Å®', 'Å¯', 'Å°', 'Å±', 'Å²', 'Å³', 'Å´', 'Åµ', 'Å¶', 'Å·', 'Å¸', 'Å¹', 'Åº', 'Å»', 'Å¼', 'Å½', 'Å¾', 'Å¿', 'Æ’', 'Æ ', 'Æ¡', 'Æ¯', 'Æ°', 'Ç', 'Ç', 'Ç', 'Ç', 'Ç‘', 'Ç’', 'Ç“', 'Ç”', 'Ç•', 'Ç–', 'Ç—', 'Ç˜', 'Ç™', 'Çš', 'Ç›', 'Çœ', 'Çº', 'Ç»', 'Ç¼', 'Ç½', 'Ç¾', 'Ç¿');
        $b = array('AE', 'ae', 'O', 'o', 'U', 'u', 'ss', 'A', 'A', 'A', 'A', 'A', 'A', 'AE', 'C', 'E', 'E', 'E', 'E', 'I', 'I', 'I', 'I', 'D', 'N', 'O', 'O', 'O', 'O', 'O', 'O', 'U', 'U', 'U', 'U', 'Y', 's', 'a', 'a', 'a', 'a', 'a', 'a', 'ae', 'c', 'e', 'e', 'e', 'e', 'i', 'i', 'i', 'i', 'n', 'o', 'o', 'o', 'o', 'o', 'o', 'u', 'u', 'u', 'u', 'y', 'y', 'A', 'a', 'A', 'a', 'A', 'a', 'C', 'c', 'C', 'c', 'C', 'c', 'C', 'c', 'D', 'd', 'D', 'd', 'E', 'e', 'E', 'e', 'E', 'e', 'E', 'e', 'E', 'e', 'G', 'g', 'G', 'g', 'G', 'g', 'G', 'g', 'H', 'h', 'H', 'h', 'I', 'i', 'I', 'i', 'I', 'i', 'I', 'i', 'I', 'i', 'IJ', 'ij', 'J', 'j', 'K', 'k', 'L', 'l', 'L', 'l', 'L', 'l', 'L', 'l', 'l', 'l', 'N', 'n', 'N', 'n', 'N', 'n', 'n', 'O', 'o', 'O', 'o', 'O', 'o', 'O', 'o', 'R', 'r', 'R', 'r', 'R', 'r', 'S', 's', 'S', 's', 'S', 's', 'S', 's', 'T', 't', 'T', 't', 'T', 't', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'W', 'w', 'Y', 'y', 'Y', 'Z', 'z', 'Z', 'z', 'Z', 'z', 's', 'f', 'O', 'o', 'U', 'u', 'A', 'a', 'I', 'i', 'O', 'o', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'A', 'a', 'AE', 'ae', 'O', 'o');
        
		return str_replace($a, $b, $string);
	}

	public static function getPostAlias( $id , $external = false )
	{

		static $loaded = array();

		if(! isset( $loaded[$id] ) )
		{
			$config	= DiscussHelper::getConfig();
			$db		= DiscussHelper::getDBO();

			$data	= DiscussHelper::getTable( 'Posts' );
			$data->load( $id );

			// Empty alias needs to be regenerated.
			if( empty($data->alias) )
			{
				$data->alias	= JFilterOutput::stringURLSafe( $data->title );
				$i			= 1;

				while( DiscussRouter::_isAliasExists( $data->alias, 'post' , $id ) )
				{
					$data->alias	= JFilterOutput::stringURLSafe( $data->title ) . '-' . $i;
					$i++;
				}

				$query	= 'UPDATE #__discuss_posts SET alias=' . $db->Quote( $data->alias ) . ' '
						. 'WHERE ' . $db->nameQuote( 'id' ) . '=' . $db->Quote( $id );
				$db->setQuery( $query );
				$db->Query();
			}

			$loaded[$id]    = $data->alias;
		}



		if( $external )
		{
			$uri		= JURI::getInstance();
			return $uri->toString( array('scheme', 'host', 'port')) . '/' . $loaded[$id];
		}

		return $loaded[$id];
	}

	public static function getTagAlias( $id )
	{
		static $loaded = array();

		if(! isset( $loaded[$id] ) )
		{
			$table	= DiscussHelper::getTable( 'Tags' );
			$table->load( $id );

			$loaded[$id]   = $table->alias;
		}

		return $loaded[$id];
	}



	public static function getUserAlias( $id )
	{
		static $loaded = array();

		if(! isset( $loaded[$id] ) )
		{
			$config		= DiscussHelper::getConfig();
			$profile	= DiscussHelper::getTable( 'Profile' );
			$profile->load($id);

			if ($config->get('main_sef_user') == 'realname') {
				$urlname 	= $profile->id . ':' . $profile->user->name;
			}

			if ($config->get('main_sef_user') == 'username') {
				$urlname 	= $profile->id . ':' . $profile->user->username;
			}

			if ($config->get('main_sef_user') == 'default') {
				$urlname 	= empty($profile->alias) ? $profile->user->name : $profile->alias; 				
			}

			$urlname	= DiscussHelper::permalinkUnicodeSlug($urlname);

			if ($config->get( 'main_sef_unicode' )) {
				//unicode support.
				$alias	= DiscussHelper::permalinkUnicodeSlug( $urlname );
			} else {
				$alias	= JFilterOutput::stringURLSafe( $urlname );
			}

			$loaded[$id] = $alias;
		}

		return $loaded[$id];
	}

	public static function getRoutedURL( $url , $xhtml = false , $external = false )
	{
		if( !$external )
		{
			return DiscussRouter::_( $url , $xhtml );
		}

		$mainframe	= JFactory::getApplication();
		$uri		= JURI::getInstance( JURI::base() );

		//To fix 1.6 Jroute issue as it will include the administrator into the url path.
		$url 	= str_replace('/administrator/', '/', DiscussRouter::_( $url  , $xhtml ));

		if( $mainframe->isAdmin() && DiscussRouter::isSefEnabled() )
		{
			if( DiscussHelper::getJoomlaVersion() >= '1.6')
			{
				JFactory::$application = JApplication::getInstance('site');
			}

			if( DiscussHelper::getJoomlaVersion() >= '3.0' )
			{
				jimport( 'joomla.libraries.cms.router' );
			}
			else
			{
				jimport( 'joomla.application.router' );
				require_once (JPATH_ROOT . '/includes/router.php');
				require_once (JPATH_ROOT . '/includes/application.php');
			}

			$router	= new JRouterSite( array('mode'=>JROUTER_MODE_SEF) );
			$urls	= $router->build($url)->toString(array('path', 'query', 'fragment'));
			$urls	= DISCUSS_JURIROOT . '/' . ltrim( str_replace('/administrator/', '/', $urls) , '/' );

			$container	= explode('/', $urls);
			$container	= array_unique($container);
			$urls = implode('/', $container);

			if( DiscussHelper::getJoomlaVersion() >= '1.6')
			{
				JFactory::$application = JApplication::getInstance('administrator');
			}

			return $urls;
		}
		else
		{

			$url	= rtrim($uri->toString( array('scheme', 'host', 'port' )), '/' ) . '/' . ltrim( $url , '/' );
			$url	= str_replace('/administrator/', '/', $url);

			if( DiscussRouter::isSefEnabled() )
			{
				$container  = explode('/', $url);
				$container	= array_unique($container);
				$url = implode('/', $container);
			}

			return $url;
		}
	}

	public static function _isAliasExists( $alias, $type='post', $id='0')
	{
		// Check reserved alias. alias migh conflict with view names.
		$aliases = array( 'ask', 'attachments', 'badges', 'categories', 'favourites', 'featured', 'index',
			'likes', 'notifications', 'polls', 'post', 'profile', 'search', 'subscriptions', 'tags',
			'users', 'votes' );


		if( $type == 'post' && in_array($alias, $aliases) )
		{
			return true;
		}

		$db		= DiscussHelper::getDBO();

		switch($type)
		{
			case 'badge':
				$query	= 'SELECT `id` FROM ' . $db->nameQuote( '#__discuss_badges' ) . ' '
					. 'WHERE ' . $db->namequote( 'alias' ) . '=' . $db->Quote( $alias );
				break;
			case 'tag':
				$query	= 'SELECT `id` FROM ' . $db->nameQuote( '#__discuss_tags' ) . ' '
					. 'WHERE ' . $db->nameQuote( 'alias' ) . '=' . $db->Quote( $alias );
				break;
			case 'post':
			default:
				$query	= 'SELECT `id` FROM ' . $db->nameQuote( '#__discuss_posts' ) . ' '
						. 'WHERE ' . $db->nameQuote( 'alias' ) . '=' . $db->Quote( $alias ) . ' '
						. 'AND ' . $db->nameQuote( 'id' ) . '!=' . $db->Quote( $id );
				break;
		}

		$db->setQuery( $query );

		$result = $db->loadAssocList();
		$count	= count($result);

		if( $count == '1' && !empty($id))
		{
			return ($id == $result['0']['id'])? false : true;
		}
		else
		{
			return ($count > 0) ? true : false;
		}
	}


	public static function getItemIdByUsers()
	{
		static $discussionItems	= null;

		if( !isset( $discussionItems[ $postId ] ) )
		{
			$db	= DiscussHelper::getDBO();

			$query	= 'SELECT ' . $db->nameQuote('id') . ' FROM ' . $db->nameQuote( '#__menu' ) . ' '
					. 'WHERE ' . $db->nameQuote( 'link' ) . '=' . $db->Quote( 'index.php?option=com_easydiscuss&view=users') . ' '
					. 'AND ' . $db->nameQuote( 'published' ) . '=' . $db->Quote( '1' ) . ' '
					. self::getLanguageQuery()
					. ' LIMIT 1';

			$db->setQuery( $query );
			$itemid = $db->loadResult();

			$discussionItems[ $postId ] = $itemid;
		}

		return $discussionItems[ $postId ];

	}

	public static function getItemIdByDiscussion( $postId )
	{
		static $discussionItems	= null;

		if( !isset( $discussionItems[ $postId ] ) )
		{
			$db	= DiscussHelper::getDBO();

			$query	= 'SELECT ' . $db->nameQuote('id') . ' FROM ' . $db->nameQuote( '#__menu' ) . ' '
					. 'WHERE ' . $db->nameQuote( 'link' ) . '=' . $db->Quote( 'index.php?option=com_easydiscuss&view=post&id='.$postId) . ' '
					. 'AND ' . $db->nameQuote( 'published' ) . '=' . $db->Quote( '1' ) . ' '
					. self::getLanguageQuery()
					. ' LIMIT 1';

			$db->setQuery( $query );
			$itemid = $db->loadResult();

			$discussionItems[ $postId ] = $itemid;
		}

		return $discussionItems[ $postId ];

	}

	public static function getItemIdByTags( $tagId )
	{
		static $tagItems	= null;

		if( !isset( $tagItems[ $tagId ] ) )
		{

			$db	= DiscussHelper::getDBO();

			$query	= 'SELECT ' . $db->nameQuote('id') . ' FROM ' . $db->nameQuote( '#__menu' ) . ' '
					. 'WHERE ' . $db->nameQuote( 'link' ) . '=' . $db->Quote( 'index.php?option=com_easydiscuss&view=tags&layout=tag&id='.$tagId) . ' '
					. 'OR ' . $db->nameQuote( 'link' ) . ' LIKE ' . $db->Quote( 'index.php?option=com_easydiscuss&view=tags&layout=tag&id='.$tagId . '%') . ' '
					. 'AND ' . $db->nameQuote( 'published' ) . '=' . $db->Quote( '1' ) . ' '
					. self::getLanguageQuery()
					. ' LIMIT 1';

			$db->setQuery( $query );
			$itemid = $db->loadResult();

			$tagItems[ $tagId ] = $itemid;
			return $itemid;
		}
		else
		{
			return $tagItems[ $tagId ];
		}
	}

	public static function getItemIdByCategories( $categoryId )
	{
		static $categoryItems	= null;

		if( !isset( $categoryItems[ $categoryId ] ) )
		{

			$db	= DiscussHelper::getDBO();

			$query	= 'SELECT ' . $db->nameQuote('id') . ' FROM ' . $db->nameQuote( '#__menu' ) . ' '
					. 'WHERE ' . $db->nameQuote( 'link' ) . '=' . $db->Quote( 'index.php?option=com_easydiscuss&view=categories&layout=listings&category_id='.$categoryId) . ' '
					. 'OR ' . $db->nameQuote( 'link' ) . ' LIKE ' . $db->Quote( 'index.php?option=com_easydiscuss&view=categories&layout=listings&category_id='.$categoryId . '&limit%') . ' '
					. 'AND ' . $db->nameQuote( 'published' ) . '=' . $db->Quote( '1' ) . ' '
					. self::getLanguageQuery()
					. ' LIMIT 1';

			$db->setQuery( $query );
			$itemid = $db->loadResult();

			$categoryItems[ $categoryId ] = $itemid;
			return $itemid;
		}
		else
		{
			return $categoryItems[ $categoryId ];
		}
	}

	public static function getItemId( $view='', $layout='', $exact = false )
	{
		static $loaded 	= array();

		$tmpView 		= $view;
		$indexKey       = $tmpView . $layout . $exact;

		// Since the search and index uses the same item id.
		if( $view == 'search' )
		{
			$tmpView 	= 'index';
		}

		if( isset( $loaded[ $indexKey ] ) )
		{
			return $loaded[ $indexKey ];
		}

		$db	= DiscussHelper::getDBO();

		switch($view)
		{
			case 'categories':
				$view = 'categories';
				break;
			case 'profile':
				$view='profile';
				break;
			case 'post':
				$view='post';
				break;
			case 'ask':
				$view='ask';
				break;
			case 'tags':
				$view = 'tags';
				break;
			case 'notification':
				$view = 'notification';
				break;
			case 'subscriptions':
				$view = 'subscriptions';
				break;
			case 'list':
				$view = 'list';
				break;
			case 'users':
				$view = 'users';
				break;
			case 'search':
			case 'index':
			default:
				$view = 'index';
				break;
		}

		$query	= 'SELECT ' . $db->nameQuote('id') . ' FROM ' . $db->nameQuote( '#__menu' ) . ' '
				. 'WHERE ' . $db->nameQuote( 'link' ) . '=' . $db->Quote( 'index.php?option=com_easydiscuss&view='.$view ) . ' '
				. 'AND ' . $db->nameQuote( 'published' ) . '=' . $db->Quote( '1' ) . ' '
				. self::getLanguageQuery()
				. ' LIMIT 1';
		$db->setQuery( $query );
		$itemid = $db->loadResult();

		if( ! $exact )
		{

			if( !$itemid && $view == 'post')
			{
				$query	= 'SELECT ' . $db->nameQuote('id') . ' FROM ' . $db->nameQuote( '#__menu' );

				if( empty( $layout ) )
				{
					$query	.= ' WHERE ' . $db->nameQuote( 'link' ) . ' = ' . $db->Quote( 'index.php?option=com_easydiscuss&view=' . $view );
				}
				else
				{
					$query	.= ' WHERE ' . $db->nameQuote( 'link' ) . ' = ' . $db->Quote( 'index.php?option=com_easydiscuss&view=' . $view . '&layout=' . $layout  );
				}
				$query	.= ' AND ' . $db->nameQuote( 'published' ) . '=' . $db->Quote( '1' );
				$query  .= self::getLanguageQuery() . ' LIMIT 1';

				$db->setQuery( $query );
				$itemid = $db->loadResult();
			}

			// @rule: Try to fetch based on the current view.
			if( !$itemid && $view != 'post')
			{
				//post view wil be abit special bcos of its layout 'submit'

				$query	= 'SELECT ' . $db->nameQuote('id') . ' FROM ' . $db->nameQuote( '#__menu' ) . ' '
						. 'WHERE ' . $db->nameQuote( 'link' ) . ' LIKE ' . $db->Quote( 'index.php?option=com_easydiscuss&view=' . $view . '%' ) . ' '
						. 'AND ' . $db->nameQuote( 'published' ) . '=' . $db->Quote( '1' ) . ' '
						. self::getLanguageQuery()
						. ' LIMIT 1';
				$db->setQuery( $query );
				$itemid = $db->loadResult();
			}

			// if still failed, try to get easydiscuss index view.
			if( !$itemid )
			{
				$query	= 'SELECT ' . $db->nameQuote('id') . ' FROM ' . $db->nameQuote( '#__menu' ) . ' '
						. 'WHERE ' . $db->nameQuote( 'link' ) . ' LIKE ' . $db->Quote( '%index.php?option=com_easydiscuss&view=index%' ) . ' '
						. 'AND ' . $db->nameQuote( 'published' ) . '=' . $db->Quote( '1' ) . ' '
						. self::getLanguageQuery()
						. ' LIMIT 1';
				$db->setQuery( $query );
				$itemid = $db->loadResult();
			}


			// If all else fails, just try to find anything with %index.php?option=com_easydiscuss%
			if( !$itemid )
			{
				$query	= 'SELECT ' . $db->nameQuote('id') . ' FROM ' . $db->nameQuote( '#__menu' ) . ' '
						. 'WHERE ' . $db->nameQuote( 'link' ) . ' LIKE ' . $db->Quote( '%index.php?option=com_easydiscuss%' ) . ' '
						. 'AND ' . $db->nameQuote( 'published' ) . '=' . $db->Quote( '1' ) . ' '
						. self::getLanguageQuery()
						. ' LIMIT 1';
				$db->setQuery( $query );
				$itemid = $db->loadResult();
			}


			$itemid = ( empty( $itemid ) ) ? '1' : $itemid;
		}



		$loaded[ $indexKey ]	= $itemid;

		return $loaded[ $indexKey ];
	}

	public static function getLanguageQuery()
	{
		if( DiscussHelper::isJoomla15() )
		{
			return '';
		}

		$lang		= JFactory::getLanguage()->getTag();

		$langQuery	= '';

		if( !empty( $lang ) && $lang != '*' )
		{
			$db			= DiscussHelper::getDBO();
			$langQuery	= ' AND (' . $db->nameQuote( 'language' ) . '=' . $db->Quote( $lang ) . ' OR ' . $db->nameQuote( 'language' ) . ' = '.$db->Quote('*').' )';
		}

		return $langQuery;
	}

	public static function encodeSegments($segments)
	{
		$router 	= new DiscussJoomlaRouter();
		return $router->encode( $segments );
	}

}
