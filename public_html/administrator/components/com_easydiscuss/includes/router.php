<?php
/**
* @package		EasyDiscuss
* @copyright	Copyright (C) 2010 - 2018 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasyDiscuss is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');

require_once(JPATH_ADMINISTRATOR . '/components/com_easydiscuss/includes/easydiscuss.php');

jimport('joomla.filter.filteroutput');

$jVerArr = explode('.', JVERSION);
$jVersion = $jVerArr[0] . '.' . $jVerArr[1];

if ($jVersion <= '3.1') {
	jimport('joomla.application.router');
} else {
	jimport('joomla.libraries.cms.router');
}

class DiscussJoomlaRouter extends JRouter
{
	public function encode($segments)
	{
		return parent::_encodeSegments($segments);
	}
}

class EDR
{
	public static function getMessageRoute( $id = 0 , $xhtml = true , $ssl = null )
	{
		$url = self::_('view=conversation&layout=read&id=' . $id , $xhtml , $ssl);

		return $url;
	}

	public static function getPrintRoute( $id = 0 , $xhtml = true , $ssl = null )
	{
		$url = self::_('view=post&id=' . $id . '&tmpl=component&print=1' , $xhtml , $ssl);

		return $url;
	}

	public static function getPostRoute( $id = 0 , $xhtml = true , $ssl = null )
	{
		$url = self::_('view=post&id=' . $id , $xhtml , $ssl);

		return $url;
	}

	public static function getTagRoute( $id = 0 , $xhtml = true , $ssl = null )
	{
		$url = self::_('view=tags&id=' . $id , $xhtml , $ssl);
		return $url;
	}

	public static function getBadgeRoute( $id = 0 , $xhtml = true , $ssl = null )
	{
		$url = self::_('view=badges&layout=listings&id=' . $id , $xhtml , $ssl);

		return $url;
	}

	public static function getCategoryRoute( $id = 0, $xhtml = true , $ssl = null )
	{
		$url = self::_('view=categories&layout=listings&category_id=' . $id , $xhtml , $ssl);

		return $url;
	}

	/**
	 * Returns the forums route.
	 *
	 * @since	4.0
	 * @access	public
	 */
	public static function getForumsRoute($id = 0, $layout = '', $xhtml = true, $ssl = null)
	{
		$url = self::_('index.php?option=com_easydiscuss&view=forums', $xhtml, $ssl);

		if ($id) {
			$url = self::_('index.php?option=com_easydiscuss&view=forums&category_id=' . $id, $xhtml, $ssl);
		}

		if ($id && $layout) {
			$url = self::_('index.php?option=com_easydiscuss&view=forums&category_id=' . $id . '&layout=listings', $xhtml, $ssl);
		}

		return $url;
	}

	/**
	 * Returns the reply permalink.
	 *
	 * @since	4.0
	 * @access	public
	 */
	public static function getReplyRoute($postId = 0, $replyId = '', $xhtml = true, $ssl = null)
	{
		// Build the parent url
		// $url = EDR::getPostRoute($postId);

		// Retrieve the limit start if available
		$limitstart = JFactory::getApplication()->input->get('limitstart', 0, 'int');

		if ($limitstart) {
			// if (strpos($url, '?') === false) {
			// 	$url = $url . '?limitstart=' . $limitstart;
			// } else {
			// 	$url = $url . '&limitstart=' . $limitstart;
			// }

			$url = self::_('view=post&id=' . $postId . '&limitstart=' . $limitstart);
		} else {
			$url = EDR::getPostRoute($postId);
		}

		$url = $url . '#' . JText::_('COM_EASYDISCUSS_REPLY_PERMALINK') . '-' . $replyId;

		return $url;
	}

	/**
	 * Returns the groups route.
	 *
	 * @since	4.0
	 * @access	public
	 */
	public static function getGroupsRoute($id = 0, $layout = '', $xhtml = true, $ssl = null)
	{
		$tmp = 'index.php?option=com_easydiscuss&view=groups';

		$url = self::_($tmp, $xhtml, $ssl);

		if ($id) {
			$url = self::_($tmp . '&group_id=' . $id, $xhtml, $ssl);
		}

		if ($id && $layout) {
			$url = self::_($tmp . '&group_id=' . $id . '&layout=listings', $xhtml, $ssl);
		}

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

	/**
	 * We need to determine the url with itemid
	 *
	 * @since	4.0
	 * @access	public
	 */
	public static function _($url = '', $xhtml = true, $ssl = null, $jRouted = true)
	{
		static $eUri = array();
		static $loaded = array();

		$mainframe = JFactory::getApplication();
		$config = ED::config();

		// Since 4.0, we no longer need to add index.php?option=com_easydiscuss in the url any longer.
		if (stristr($url, 'index.php') === false) {
			if ($url) {
				$url = 'index.php?option=com_easydiscuss' . '&' . $url;
			} else {
				$url = 'index.php?option=com_easydiscuss';
			}
		}

		// To test if the Itemid is there or not.
		$jURL = $url . $xhtml . (int) $jRouted;
		$key = $url . $xhtml . (int) $jRouted;

		if (isset($loaded[$key])) {
			return $loaded[$key];
		}

		// Convert the string to variable so that we can access it.
		parse_str(parse_url($url, PHP_URL_QUERY), $query);

		// Get the view portion from the query string
		$view = isset($query['view']) ? $query['view'] : 'index';
		$layout = isset($query['layout']) ? $query['layout'] : null;
		$Itemid = isset($query['Itemid']) ? $query['Itemid'] : '';
		$task = isset($query['task']) ? $query['task'] : '';
		$id = isset($query['id']) ? $query['id'] : null;
		$sort = isset($query['sort']) ? $query['sort'] : null;
		$filter = isset($query['filter']) ? $query['filter'] : null;
		$lang = isset($query['lang']) ? $query['lang'] : null;
		$search = isset($query['search']) ? $query['search'] : null;
		$category_id = isset($query['category_id']) ? $query['category_id'] : null;
		$postStatus = isset($query['status']) ? $query['status'] : null;
		$groupId = isset($query['group_id']) ? $query['group_id'] : null;

		if (!empty($Itemid)) {
			if (self::isEasyDiscussMenuItem($Itemid)) {
				$loaded[$key] = JRoute::_($url, $xhtml, $ssl);
				return $loaded[$key];
			}
		}

		if ($lang) {
			// we knwo the lang that we passed in is the short tag. we need to get the full tag. e.g. en-GB
			$lang = self::getSiteLanguageTag($lang);
		}


		$tmpId = '';
		$routingBehavior = $config->get('main_routing', 'currentactive');
		$dropSegment = false;

		if ($routingBehavior == 'currentactive' || $routingBehavior == 'menuitem') {
			$routingMenuItem = $config->get('main_routing_itemid','');

			if (($routingBehavior == 'menuitem') && ($routingMenuItem != '')) {
				$tmpId = $routingMenuItem;
			}

			// @rule: If there is already an item id, try to use the explicitly set one.
			if (empty($tmpId)) {
				if (!$mainframe->isAdmin()) {
					// Retrieve the active menu item.
					$menu = $mainframe->getMenu();
					$item = $menu->getActive();

					if (isset($item->id)) {
						$tmpId = $item->id;
					}
				}
			}

			if ($tmpId) {
				if (! self::isEasyDiscussMenuItem($tmpId)) {
					$tmpId = '';
				}
			}

			// if still empty, means user is configured to use 'current active' but the link do not have menu Item.
			if (empty($tmpId)) {
				$defaultMenu = self::getMenus('index', null, null, $lang);
				if (! $defaultMenu) {
					$defaultMenu = self::getMenus('forums', null, null, $lang);

					if (! $defaultMenu) {
						// just return any menu item related to ED.
						$defaultMenu = self::getMenus('any', null, null, $lang);
					}
				}
			}


		} else {

			$defaultMenu = self::getMenus('index', null, null, $lang);

			if (!$defaultMenu) {
				$defaultMenu = self::getMenus('forums', null, null, $lang);

				if (!$defaultMenu) {
					// just return any menu item related to ED.
					$defaultMenu = self::getMenus('any', null, null, $lang);
				}
			}

			// Let easydiscuss to determine the best menu itemid.
			if ($view == 'index') {
				$menu = self::getMenus($view, null, null, $lang);
				if ($menu) {
					$tmpId = $menu->id;
				}

				// if ($tmpId && (($menu->segments->category_id == $category_id) || (!$layout && !$category_id))) {
				if ($tmpId) {
					$dropSegment = true;
				}
			}

			if ($view == 'forums') {
				$menu = self::getMenus($view, $layout, $category_id, $lang);
				if ($menu) {
					$tmpId = $menu->id;
				}

				// if ($tmpId && (($menu->segments->category_id == $category_id) || (!$layout && !$category_id))) {
				if ($tmpId && ($menu->segments->category_id == $category_id && !$layout)) {
					$dropSegment = true;
				} else if ($tmpId && (!$layout && !$category_id && !$menu->segments->category_id)) {
					// echo $menu->link;
					$dropSegment = true;
				}
			}

			if ($view == 'categories') {
				$menu = self::getMenus($view, $layout, $category_id, $lang);

				$hasLayout = is_null($layout);
				$useGeneric = false;

				if (!$menu && $layout && $category_id) {
					$menu = self::getMenus($view, null, null, $lang);
					$useGeneric = true;
				}

				if ($menu) {
					$tmpId = $menu->id;
				}

				if (!$useGeneric && $tmpId) {
					$dropSegment = true;
				}
			}

			if ($view == 'users') {
				$menu = self::getMenus($view, $layout, $id, $lang);
				if ($menu) {
					$tmpId = $menu->id;
				}

				if ($tmpId) {
					$dropSegment = true;
				}
			}

			if ($view == 'tags') {
				$menu = self::getMenus($view, $layout, $id, $lang);

				if ($menu) {
					$tmpId = $menu->id;
				}

				if ($tmpId && ($layout && $menu->layout == $layout) && $id) {
					$dropSegment = true;
				}
			}

			if ($view == 'post') {

				$menu = self::getMenus($view, $layout, $id, $lang);
				if ($menu) {
					$tmpId = $menu->id;
				} else {
					$post = ED::post($id);
					$menu = self::getMenus('categories', 'listings', $post->category_id, $lang);

					if ($menu) {
						$tmpId = $menu->id;
					}
				}
			}

			if ($view == 'mypost') {
				$menu = self::getMenus($view, $layout, $category_id, $lang);

				if ($menu) {
					$tmpId = $menu->id;
				}

				if ($tmpId) {
					$dropSegment = true;
				}
			}

			if ($view == 'subscription') {
				$menu = self::getMenus($view, $layout, $category_id, $lang);

				if ($menu) {
					$tmpId = $menu->id;
				}

				if ($tmpId) {
					$dropSegment = true;
				}
			}

			if ($view == 'favourites') {
				$menu = self::getMenus($view, $layout, $category_id, $lang);

				if ($menu) {
					$tmpId = $menu->id;
				}

				if ($tmpId) {
					$dropSegment = true;
				}
			}

			if ($view == 'badges') {
				$menu = self::getMenus($view, null, null, $lang);

				if ($menu) {
					$tmpId = $menu->id;
				}

				if (!$id && $tmpId) {
					$dropSegment = true;
				}
			}

			if ($view == 'groups') {
				$menu = self::getMenus($view, null, null, $lang);

				if ($menu) {
					$tmpId = $menu->id;
				}

				if (!$groupId && $tmpId) {
					$dropSegment = true;
				}
			}
		}

		if (!$tmpId){
			$tmpId = $defaultMenu->id;
		}

		// Some query strings may have "sort" in them.
		if ($sort) {
			$dropSegment = false;
		}

		if ($postStatus) {
			$dropSegment = false;
		}

		// Some query strings may have "search" in them.
		if ($search) {
			$dropSegment = false;
		}

		// Some query strings may have "task" in them.
		if ($task) {
			$dropSegment = false;
		}

		if (self::isSefEnabled() && $dropSegment) {

			$url    = 'index.php?Itemid=' . $tmpId;
			$loaded[$key]	= JRoute::_( $url , $xhtml , $ssl );

			return $loaded[$key];
		}

		//check if there is any anchor in the link or not.
		$pos = JString::strpos($url, '#');

		if ($pos === false) {
			$url .= '&Itemid='.$tmpId;
		} else {
			$url = JString::str_ireplace('#', '&Itemid='.$tmpId.'#', $url);
		}

		$loaded[$key] = ($jRouted) ? JRoute::_($url, $xhtml, $ssl) : $url;

		return $loaded[$key];
	}

	public static function isSefEnabled()
	{
		$jConfig	= ED::jconfig();
		$isSef		= false;

		//check if sh404sef enabled or not.
		if ( defined('sh404SEF_AUTOLOADER_LOADED') && JFile::exists(JPATH_ADMINISTRATOR . '/components/com_sh404sef/sh404sef.class.php')) {
			require_once JPATH_ADMINISTRATOR . '/components/com_sh404sef/sh404sef.class.php';
			if ( class_exists('shRouter')) {
				$sefConfig = shRouter::shGetConfig();

				if ($sefConfig->Enabled) {
					$isSef  = true;
				}
			}
		}

		// if sh404sef not enabled, we check on joomla
		if (! $isSef) {
			$isSef = $jConfig->get('sef');
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

			$query	= 'SELECT ' . $db->nameQuote( 'id' ) . ',' . $db->nameQuote( 'alias' ) . ',' . $db->nameQuote( 'parent_id' ) . ' '
					. 'FROM ' . $db->nameQuote( '#__discuss_category' ) . ' WHERE ' . $db->nameQuote( 'id' ) . '=' . $db->Quote( $currentId );
			$db->setQuery( $query );
			$result	= $db->loadObject();

			$loaded[$currentId] = $result;

		}

		$result = $loaded[$currentId];

		if( !$result ) {
			return;
		}

		$items[]	= ED::permalinkSlug($result->alias, $result->id);

		if ($result->parent_id != 0) {
			self::recurseCategories( $result->parent_id , $items );
		}
	}

	public static function getAlias( $tableName ,$key)
	{
		static $loaded = array();

		$sig    = $tableName . '-' . $key;

		if (! isset( $loaded[$sig])) {

			$table = ED::table($tableName);
			$table->load($key);

			$loaded[$sig]   = ED::permalinkSlug($table->alias, $table->id);
		}

		return $loaded[$sig];
	}

	/**
	 * Generates a permalink given a string
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public static function normalizePermalink($string)
	{
		$config = ED::config();
		$permalink = '';

		if (EDR::isSefEnabled() && $config->get('main_sef_unicode')) {
			$permalink = JFilterOutput::stringURLUnicodeSlug($string);
			return $permalink;
		}

		// Replace accents to get accurate string
		$string = EDR::replaceAccents($string);

		// no unicode supported.
		$permalink = JFilterOutput::stringURLSafe($string);

		// check if anything return or not. If not, then we give a date as the alias.
		if (trim(str_replace('-','',$permalink)) == '') {
			$date = ED::date();
			$permalink = $date->format("%Y-%m-%d-%H-%M-%S");
		}

		return $permalink;
	}

	public static function replaceAccents( $string )
	{
		$a = array('Ã„', 'Ã¤', 'Ã–', 'Ã¶', 'Ãœ', 'Ã¼', 'ÃŸ' , 'Ã€', 'Ã', 'Ã‚', 'Ãƒ', 'Ã„', 'Ã…', 'Ã†', 'Ã‡', 'Ãˆ', 'Ã‰', 'ÃŠ', 'Ã‹', 'ÃŒ', 'Ã', 'Ã', 'Ã', 'Ã', 'Ã‘', 'Ã’', 'Ã“', 'Ã”', 'Ã•', 'Ã–', 'Ã˜', 'Ã™', 'Ãš', 'Ã›', 'Ãœ', 'Ã', 'ÃŸ', 'Ã ', 'Ã¡', 'Ã¢', 'Ã£', 'Ã¤', 'Ã¥', 'Ã¦', 'Ã§', 'Ã¨', 'Ã©', 'Ãª', 'Ã«', 'Ã¬', 'Ã­', 'Ã®', 'Ã¯', 'Ã±', 'Ã²', 'Ã³', 'Ã´', 'Ãµ', 'Ã¶', 'Ã¸', 'Ã¹', 'Ãº', 'Ã»', 'Ã¼', 'Ã½', 'Ã¿', 'Ä€', 'Ä', 'Ä‚', 'Äƒ', 'Ä„', 'Ä…', 'Ä†', 'Ä‡', 'Äˆ', 'Ä‰', 'ÄŠ', 'Ä‹', 'ÄŒ', 'Ä', 'Ä', 'Ä', 'Ä', 'Ä‘', 'Ä’', 'Ä“', 'Ä”', 'Ä•', 'Ä–', 'Ä—', 'Ä˜', 'Ä™', 'Äš', 'Ä›', 'Äœ', 'Ä', 'Ä', 'ÄŸ', 'Ä ', 'Ä¡', 'Ä¢', 'Ä£', 'Ä¤', 'Ä¥', 'Ä¦', 'Ä§', 'Ä¨', 'Ä©', 'Äª', 'Ä«', 'Ä¬', 'Ä­', 'Ä®', 'Ä¯', 'Ä°', 'Ä±', 'Ä²', 'Ä³', 'Ä´', 'Äµ', 'Ä¶', 'Ä·', 'Ä¹', 'Äº', 'Ä»', 'Ä¼', 'Ä½', 'Ä¾', 'Ä¿', 'Å€', 'Å', 'Å‚', 'Åƒ', 'Å„', 'Å…', 'Å†', 'Å‡', 'Åˆ', 'Å‰', 'ÅŒ', 'Å', 'Å', 'Å', 'Å', 'Å‘', 'Å’', 'Å“', 'Å”', 'Å•', 'Å–', 'Å—', 'Å˜', 'Å™', 'Åš', 'Å›', 'Åœ', 'Å', 'Å', 'ÅŸ', 'Å ', 'Å¡', 'Å¢', 'Å£', 'Å¤', 'Å¥', 'Å¦', 'Å§', 'Å¨', 'Å©', 'Åª', 'Å«', 'Å¬', 'Å­', 'Å®', 'Å¯', 'Å°', 'Å±', 'Å²', 'Å³', 'Å´', 'Åµ', 'Å¶', 'Å·', 'Å¸', 'Å¹', 'Åº', 'Å»', 'Å¼', 'Å½', 'Å¾', 'Å¿', 'Æ’', 'Æ ', 'Æ¡', 'Æ¯', 'Æ°', 'Ç', 'Ç', 'Ç', 'Ç', 'Ç‘', 'Ç’', 'Ç“', 'Ç”', 'Ç•', 'Ç–', 'Ç—', 'Ç˜', 'Ç™', 'Çš', 'Ç›', 'Çœ', 'Çº', 'Ç»', 'Ç¼', 'Ç½', 'Ç¾', 'Ç¿');
		$b = array('AE', 'ae', 'O', 'o', 'U', 'u', 'ss', 'A', 'A', 'A', 'A', 'A', 'A', 'AE', 'C', 'E', 'E', 'E', 'E', 'I', 'I', 'I', 'I', 'D', 'N', 'O', 'O', 'O', 'O', 'O', 'O', 'U', 'U', 'U', 'U', 'Y', 's', 'a', 'a', 'a', 'a', 'a', 'a', 'ae', 'c', 'e', 'e', 'e', 'e', 'i', 'i', 'i', 'i', 'n', 'o', 'o', 'o', 'o', 'o', 'o', 'u', 'u', 'u', 'u', 'y', 'y', 'A', 'a', 'A', 'a', 'A', 'a', 'C', 'c', 'C', 'c', 'C', 'c', 'C', 'c', 'D', 'd', 'D', 'd', 'E', 'e', 'E', 'e', 'E', 'e', 'E', 'e', 'E', 'e', 'G', 'g', 'G', 'g', 'G', 'g', 'G', 'g', 'H', 'h', 'H', 'h', 'I', 'i', 'I', 'i', 'I', 'i', 'I', 'i', 'I', 'i', 'IJ', 'ij', 'J', 'j', 'K', 'k', 'L', 'l', 'L', 'l', 'L', 'l', 'L', 'l', 'l', 'l', 'N', 'n', 'N', 'n', 'N', 'n', 'n', 'O', 'o', 'O', 'o', 'O', 'o', 'O', 'o', 'R', 'r', 'R', 'r', 'R', 'r', 'S', 's', 'S', 's', 'S', 's', 'S', 's', 'T', 't', 'T', 't', 'T', 't', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'W', 'w', 'Y', 'y', 'Y', 'Z', 'z', 'Z', 'z', 'Z', 'z', 's', 'f', 'O', 'o', 'U', 'u', 'A', 'a', 'I', 'i', 'O', 'o', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'A', 'a', 'AE', 'ae', 'O', 'o');

		return str_replace($a, $b, $string);
	}

	public static function decodeAlias($alias, $tablename)
	{
		$config = ED::config();

		$id = $alias;

		if ($config->get('main_sef_unicode')) {

			$permalinkSegment = $alias;
			$permalinkArr = explode('-', $permalinkSegment);

			$id = $permalinkArr[0];

		} else {

			$table = ED::table($tablename);
			$table->load( $alias, true );

			$id = $table->id;
		}

		return $id;
	}

	public static function getPostAlias($id , $external = false)
	{

		static $loaded = array();

		if (! isset($loaded[$id])) {
			$config	= ED::config();
			$db		= ED::db();

			$data	= ED::table('Posts');
			$data->load($id);

			// Empty alias needs to be regenerated.
			if (empty($data->alias)) {
				$data->alias	= JFilterOutput::stringURLSafe( $data->title );
				$i			= 1;

				while (self::_isAliasExists($data->alias, 'post' , $id)) {
					$data->alias = JFilterOutput::stringURLSafe( $data->title ) . '-' . $i;
					$i++;
				}

				$query	= 'UPDATE `#__discuss_posts` SET alias=' . $db->Quote( $data->alias ) . ' '
						. 'WHERE ' . $db->nameQuote( 'id' ) . '=' . $db->Quote( $id );
				$db->setQuery($query);
				$db->Query();
			}


			$loaded[$id]    = ED::permalinkSlug($data->alias, $id);
		}



		if( $external )
		{
			$uri		= JURI::getInstance();
			return $uri->toString( array('scheme', 'host', 'port')) . '/' . $loaded[$id];
		}

		return $loaded[$id];
	}

	public static function getTagAlias($id)
	{
		static $loaded = array();

		$idx = (int) $id;

		$segments = explode(':', $id);

		if (! isset($loaded[$idx])) {

			if (count($segments) > 1 && $segments[1]) {
				$loaded[$idx] = ED::permalinkSlug($segments[1], $segments[0]);
			} else {
				$table	= ED::table('Tags');
				$table->load($id);

				$loaded[$idx] = ED::permalinkSlug($table->alias, $id);
			}
		}

		return $loaded[$idx];
	}



	public static function getUserAlias( $id )
	{
		static $loaded = array();

		if(! isset( $loaded[$id] ) )
		{
			$config		= DiscussHelper::getConfig();
			$profile = ED::user($id);

			if ($config->get('main_sef_user') == 'realname') {
				$urlname 	= $profile->id . ':' . $profile->user->name;
			}

			if ($config->get('main_sef_user') == 'username') {
				$urlname 	= $profile->id . ':' . $profile->user->username;
			}

			if ($config->get('main_sef_user') == 'default') {
				$urlname 	= empty($profile->alias) ? $profile->user->name : $profile->alias;

				$urlname = ED::permalinkSlug($urlname, $id);
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

	public static function getRoutedURL($url, $xhtml = false, $external = false, $forceRouted = false)
	{
		if (!$external) {
			return EDR::_($url, $xhtml);
		}

		$mainframe = JFactory::getApplication();
		$uri = JURI::getInstance(JURI::base());

		if ($mainframe->isAdmin() && EDR::isSefEnabled()) {

			$base64Url = base64_encode($url);

			// lets send to frontend to process the sef link.
			$targetUrl = rtrim(JURI::root(), '/') . '/index.php?option=com_easydiscuss&controller=route&task=sef&url=' . $base64Url;

			$connector = ED::connector();
			$connector->addUrl($targetUrl);
			$connector->execute();
			$response = $connector->getResult($targetUrl);

			$data = json_decode($response);

			if (($data && isset($data->link) && $data->link) || $forceRouted) {
				$routedUrl = $data->link;
				$routedUrl = $uri->toString(array('scheme', 'host', 'port')) . '/' . ltrim($routedUrl, '/');

				return $routedUrl;
			}

			return $url;
		}

		$url = EDR::_($url, $xhtml);
		$url = str_replace('/administrator/', '/', $url);
		$url = ltrim($url, '/');

		// We need to use $uri->toString() because JURI::root() may contain a subfolder which will be duplicated
		// since $url already has the subfolder.
		return $uri->toString(array('scheme', 'host', 'port')) . '/' . $url;
	}

	public static function _isAliasExists($alias, $type='post', $id='0')
	{
		// Check reserved alias. alias migh conflict with view names.
		$aliases = array('ask', 'attachments', 'badges', 'categories', 'favourites', 'featured', 'index',
			'likes', 'notifications', 'polls', 'post', 'profile', 'search', 'subscriptions', 'tags',
			'users', 'votes');


		if ($type == 'post' && in_array($alias, $aliases)) {
			return true;
		}

		$db	= ED::db();

		switch ($type) {
			case 'badge':
				$query	= 'SELECT `id` FROM ' . $db->nameQuote('#__discuss_badges') . ' '
					. 'WHERE ' . $db->namequote('alias') . '=' . $db->Quote($alias);
				break;
			case 'posttypes':
				$query = 'SELECT `id` FROM ' . $db->nameQuote('#__discuss_post_types') . ' '
					. 'WHERE ' . $db->nameQuote('alias') . '=' . $db->Quote($alias);
				break;
			case 'tag':
			case 'category':
			case 'post':
			default:

				$query	= 'SELECT `id` FROM ' . $db->nameQuote('#__discuss_tags');
				$query .= ' WHERE ' . $db->nameQuote('alias') . '=' . $db->Quote($alias);
				if ($type == 'tag' && $id) {
					$query .= ' AND ' . $db->nameQuote('id') . '!=' . $db->Quote($id);
				}

				$query .= ' UNION ALL ';

				$query	.= ' SELECT `id` FROM ' . $db->nameQuote('#__discuss_category');
				$query	.= ' WHERE ' . $db->namequote('alias') . '=' . $db->Quote($alias);
				if ($type == 'category' && $id) {
					$query .= ' AND ' . $db->nameQuote('id') . '!=' . $db->Quote($id);
				}

				$query .= ' UNION ALL ';

				$query	.= 'SELECT `id` FROM ' . $db->nameQuote('#__discuss_posts');
				$query	.= 'WHERE ' . $db->nameQuote('alias') . '=' . $db->Quote($alias);
				if ($type == 'post' && $id) {
					$query	.= 'AND ' . $db->nameQuote('id') . '!=' . $db->Quote($id);
				}
				break;
		}

		// echo $query;exit;

		$db->setQuery($query);

		$result = $db->loadAssocList();
		$count	= count($result);

		if ($count == '1' && $id && ($type != 'badge' && $type != 'posttypes')) {
			return ($id == $result['0']['id']) ? false : true;
		} else {
			return ($count > 0) ? true : false;
		}
	}


	public static function getItemIdByUsers()
	{
		static $discussionItems	= null;

		if(!isset( $discussionItems[ $postId ]))
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

		$tmpView = $view;
		$indexKey = $tmpView . $layout . $exact;

		// Since the search and index uses the same item id.
		if ($view == 'search') {
			$tmpView = 'index';
		}

		if (isset($loaded[$indexKey])) {
			return $loaded[$indexKey];
		}

		$db = ED::db();

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



		$loaded[$indexKey] = $itemid;

		return $loaded[$indexKey];
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

	/**
	 * Method to retrieves current uri that are being accessed
	 *
	 * @since	4.0.19
	 * @access	public
	 */
	public static function getCurrentURI()
	{
		$url = JURI::getInstance()->toString();

		return $url;
	}

	public static function getLoginRedirect()
	{
		$config = ED::config();

		// Redirect to dashboard by default
		$redirect = EDR::getRoutedURL('view=index', false, true);

		// Redirect to same page?
		if ($config->get('main_login_redirect') == 'same.page') {
			$redirect = EDR::getCurrentURI();
		}

		$redirect = base64_encode($redirect);

		return $redirect;
	}

	public static function getLogoutRedirect()
	{
		$config = ED::config();

		// Redirect to dashboard by default
		$redirect = EDR::getRoutedURL('view=index', false, true);

		// Redirect to same page?
		if ($config->get('main_logout_redirect') == 'same.page') {
			$redirect = EDR::getCurrentURI();
		}

		// Redirect to forums page
		if ($config->get('main_logout_redirect') == 'forums') {
			$redirect = EDR::getRoutedURL('view=forums', false, true);
		}

		$redirect = base64_encode($redirect);

		return $redirect;
	}

	/**
	 * Get site language code
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public static function getSiteLanguageTag($langSEF)
	{
		static $cache = null;

		if (is_null($cache)) {
			$db = ED::db();

			$query = "select * from #__languages";
			$db->setQuery($query);

			$results = $db->loadObjectList();

			if ($results) {
				foreach($results as $item) {
					$cache[$item->sef] = $item->lang_code;
				}
			}
		}

		if (isset($cache[$langSEF])) {
			return $cache[$langSEF];
		}

		return $langSEF;
	}

	/**
	 * check if this itemId belong to EasyDiscuss or not.
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public static function isEasyDiscussMenuItem($itemId)
	{
		$menuItems = self::getMenus();

		foreach($menuItems as $mItem) {
			if ($mItem->id == $itemId) {
				return true;
			}
		}

		return false;
	}

	/**
	 * Retrieve all menu's from the site associated with EasyDiscuss
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public static function getMenus($view = null, $layout = null, $id = null, $lang = null)
	{
		static $_cache = null;
		static $_cacheFlat = null;
		static $selection = array();

		if (is_null($_cache)) {
			// lets get from db for 1 time.

			$model = ED::model('Menu');
			$menus = $model->getAssociatedMenus();


			// now we need to do the grouping.
			foreach ($menus as $row) {

				// Remove the index.php?option=com_easydiscuss from the link
				$tmp = str_ireplace('index.php?option=com_easydiscuss', '', $row->link);

				// Parse the URL
				parse_str($tmp, $segments);

				// var_dump($tmp, $segments);

				// Convert the segments to std class
				$segments = (object) $segments;

				// if there is no view, most likely this menu item is a external link type. lets skip this item.
				if(!isset($segments->view)) {
					continue;
				}

				$menu = new stdClass();
				$menu->segments = $segments;
				$menu->link = $row->link;
				$menu->view = $segments->view;
				$menu->layout = isset($segments->layout) ? $segments->layout : 0;
				$menu->category_id = isset($segments->category_id) ? $segments->category_id : 0;
				$menu->id = $row->id;

				// check for forum category container
				if ($menu->view == 'forums' && isset($menu->category_id) && $menu->category_id) {
					// this is forum container
					$_cache['forumcategory'][$menu->category_id]['menu'] = $menu;
					$_cache['forumcategory'][$menu->category_id]['tree'] = $model->getCategoryTreeIds($menu->category_id);

				} else {
					// this is the safe step to ensure later we will have atlest one menu item to retrive.
					$_cache[$menu->view][$menu->layout]['*'][] = $menu;
					$_cache[$menu->view][$menu->layout][$row->language][] = $menu;
				}

				$_cacheFlat[] = $menu;

			}
		}

		// we know we just want the all menu items for EasyDiscuss. lets just return form the cache.
		if (is_null($view) && is_null($layout) && is_null($id) && is_null($lang)) {
			return $_cacheFlat;
		}

		// Always ensure that layout is lowercased
		if (!is_null($layout)) {
			$layout = strtolower($layout);
		}

		// We want to cache the selection user made.
		$language = false;
		$languageTag = JFactory::getLanguage()->getTag();

		// If language filter is enabled, we need to get the language tag
		if (!JFactory::getApplication()->isAdmin()) {
			$language = JFactory::getApplication()->getLanguageFilter();
			$languageTag = JFactory::getLanguage()->getTag();
		}

		if ($lang) {
			$languageTag = $lang;
		}

		$key = $view . $layout . $id . $languageTag;

		// Get the current selection of menus from the cache
		if (!isset($selection[$key])) {

			// 'any' is a special handle to get any menu items belong to ED.
			if ($view == 'any') {
				$selection[$key] = $_cacheFlat[0];

				return $selection[$key];
			}

			// lets check if we need to retrieve from forumcategory or not.
			$tmp = false;
			if ($view == 'post') {
				$tmp = self::getItemViewLayoutId($_cache, $view, $layout, $id, $languageTag);

				if (! $tmp) {
					// now we need to check if this post's category fall into any of the forumcategory or not.
					$tmp = self::getItemForumCategory($_cache, $view, $id, $languageTag);
				}
			}

			if ($view == 'forums') {
				$tmp = self::getItemForumCategory($_cache, $view, $id, $languageTag);

				if (! $tmp) {
					$tmp = self::getItemViewLayoutId($_cache, $view, $layout, $id, $languageTag);

					if (! $tmp) {
						$tmp = self::getItemViewLayoutId($_cache, $view, null, 0, $languageTag);
					}
				}
			}

			if ($tmp) {
				$selection[$key] = $tmp;

				if (is_array($selection[$key])) {
					$selection[$key] = $selection[$key][0];
				}

				return $selection[$key];
			}

			// Search for $view only. Does not care about layout nor the id
			if (isset($_cache[$view]) && isset($_cache[$view]) && is_null($layout)) {

				if (isset($_cache[$view][0][$languageTag])) {
					$selection[$key] = $_cache[$view][0][$languageTag];
				} else if (isset($_cache[$view][0]['*'])) {
					$selection[$key] = $_cache[$view][0]['*'];
				} else {
					$selection[$key] = false;
				}
			}

			// Searches for $view and $layout only.
			if (isset($_cache[$view]) && isset($_cache[$view]) && !is_null($layout) && isset($_cache[$view][$layout]) && (is_null($id) || empty($id)) ) {
				$selection[$key] = isset($_cache[$view][$layout][$languageTag]) ? $_cache[$view][$layout][$languageTag] : $_cache[$view][$layout]['*'];
			}

			// Searches for $view $layout and $id
			if (isset($_cache[$view]) && !is_null($layout) && isset($_cache[$view][$layout]) && !is_null($id) && !empty($id)) {
				$selection[$key] = self::getItemViewLayoutId($_cache, $view, $layout, $id, $languageTag);
			}

			// If we still can't find any menu, skip this altogether.
			if (!isset($selection[$key])) {
				$selection[$key] = false;
			}

			// Flatten the array so that it would be easier for the caller.
			if (is_array($selection[$key])) {
				$selection[$key] = $selection[$key][0];
			}
		}



		return $selection[$key];

		// echo '<pre>';
		// print_r($_cache);
		// echo '</pre>';
		// exit;
	}

	private static function getItemViewLayoutId($_cache, $view, $layout = 0, $id = 0, $languageTag = '*')
	{
		$return = false;

		if (is_null($layout)) {
			$layout = 0;
		}

		if (is_null($id)) {
			$id = 0;
		}

		// no view found. just return false to stop further processing.
		if (! isset($_cache[$view])) {
			return false;
		}

		if (! isset($_cache[$view][$layout])) {
			$layout = 0;
		}

		$tmp = isset($_cache[$view][$layout][$languageTag]) ? $_cache[$view][$layout][$languageTag] : $_cache[$view][$layout]['*'];

		foreach ($tmp as $tmpMenu) {

			// Backward compatibility support. Try to get the ID from the new alias style, ID:ALIAS
			$parts = explode(':', $id);
			$legacyId = null;

			if (count($parts) > 1) {
				$legacyId = $parts[0];
			}

			$checkId = 'id';
			if ($view == 'forums' || $view == 'categories') {
				$checkId = 'category_id';
			}

			if (isset($tmpMenu->segments->{$checkId}) && ($tmpMenu->segments->{$checkId} == $id || $tmpMenu->segments->{$checkId} == $legacyId)) {
				$return = array($tmpMenu);
				break;
			}
		}

		return $return;
	}

	private static function getItemForumCategory($_cache, $view, $id, $languageTag)
	{
		if (!isset($_cache['forumcategory'])) {
			return false;
		}

		if (is_null($id)) {
			return false;
		}

		$objId = null;

		if ($view == 'post') {
			$post = ED::post($id);
			$objId = $post->category_id;
		} else {
			$objId = $id;
		}

		foreach ($_cache['forumcategory'] as $catId => $items) {
			$tree = $items['tree'];

			if ($tree) {
				foreach($tree as $tItem) {

					if ($tItem->id == $objId) {
						// var_dump($objId, $items['menu']->id);
						return array($items['menu']);
					}
				}
			}
		}

		return false;
	}

	/**
	 * Retrieves the current url that is being accessed.
	 *
	 * @since   4.0
	 * @access  public
	 * @param   bool    Determines if we should append this as a callback url.
	 * @return  string  The current url.
	 */
	public static function current($isCallback = false)
	{
		$uri = JURI::current();

		if ($isCallback) {
			return '&callback=' . base64_encode($uri);
		}

		return $uri;
	}

	/**
	 * Determine if given view is the current active menu
	 *
	 * @since   4.0
	 * @access  public
	 * @param   string currrent view
	 * @return  int / string  object id
	 */
	public static function isCurrentActiveMenu($view, $id = 0, $idPrefix = 'id', $layout = '')
	{
		$app = JFactory::getApplication();
		$menu = $app->getMenu()->getActive();

		if (!$menu) {
			return false;
		}

		if ($id) {
			if (strpos($menu->link, 'view=' . $view) !== false && strpos($menu->link, $idPrefix . '=' . $id) !== false) {
				return true;
			} else {
				// for a specific id, if we canot find, just stop here.
				return false;
			}
		}

		if ($layout && strpos($menu->link, 'view=' . $view) !== false && strpos($menu->link, 'layout=' . $layout) !== false) {
			return true;
		}

		if (strpos($menu->link, 'view=' . $view) !== false) {
			return true;
		}

		return false;
	}

}


class DiscussRouter extends EDR {}
