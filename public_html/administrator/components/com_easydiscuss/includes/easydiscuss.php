<?php
/**
* @package		EasyDiscuss
* @copyright	Copyright (C) Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasyDiscuss is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');

jimport('joomla.filesystem.file');
jimport('joomla.filesystem.folder');
jimport('joomla.html.parameter');
jimport('joomla.access.access');
jimport('joomla.application.component.model');

require_once(__DIR__ . '/dependencies.php');
require_once(__DIR__ . '/compatibility.php');

class ED
{
	/**
	 * Initializes the css, js and necessary dependencies for EasyDiscuss
	 *
	 * @since	4.0
	 * @access	public
	 */
	public static function init($location = 'site')
	{
		static $loaded = array();

		if (!isset($loaded[$location])) {

			$input = JFactory::getApplication()->input;

			// Determines if we should force compilationg (Only allow for super admin)
			$recompile = false;

			if (ED::isSiteAdmin()) {
				$recompile = $input->get('compile', false, 'bool');
			}

			// If location is provided, we should respect the location
			$customLocation = $input->get('location', $location, 'word');
			$locations = array($location);

			if ($recompile && $customLocation == 'all') {
				$locations = array('site', 'admin');
			}

			$minify = $input->get('minify', true, 'bool');

			foreach ($locations as $location) {
				$compiler = ED::compiler($location);

				if ($recompile) {
					$compiler->compile($minify, true);
				}
			}

			// Attach those scripts onto the head of the page now.
			$compiler->attach();

			// Attach stylesheets
			$stylesheet = ED::stylesheet($location);
			$stylesheet->attach();

			$loaded[$location] = true;
		}

		return $loaded[$location];
	}

	/**
	 * Formats and returns the appropriate cdn url
	 *
	 * @since	4.0
	 * @access	public
	 */
	public static function getCdnUrl()
	{
		static $cdnUrl = false;

		if (!$cdnUrl) {
			$config = ED::config();
			$cdnUrl = $config->get('system_cdn_url');

			if (!$cdnUrl) {
				return $cdnUrl;
			}

			if (stristr($cdnUrl, 'http://') === false && stristr($cdnUrl, 'https://') === false) {
				$cdnUrl = '//' . $cdnUrl;
			}
		}

		return $cdnUrl;
	}

	/**
	 * Singleton version for the ajax library
	 *
	 * @since	4.0
	 * @access	public
	 */
	public static function ajax()
	{
		static $ajax = null;

		if (!$ajax) {

			require_once(__DIR__ . '/ajax/ajax.php');

			$ajax = new EasyDiscussAjax();
		}

		return $ajax;
	}

	public static function _()
	{
		return ED::getHelper( func_get_args() );
	}

	/**
	 * Retrieves the token
	 *
	 * @since	4.0
	 * @access	public
	 */
	public static function getToken($contents = '')
	{
		$token = JFactory::getSession()->getFormToken();

		return $token;
	}

	/**
	 * Provides a secure hash based on a seed
	 *
	 * @access  public
	 * @since   5.0.0
	 */
	public static function getHash($seed = '')
	{
		return EDApplicationHelper::getHash($seed);
	}

	/**
	 * Retrieves a jdate object with the correct speficied timezone offset
	 *
	 * @since	4.0
	 * @access	public
	 */
	public static function dateWithOffSet($str = '')
	{
		$date = ED::date($str);

		$userTimeZone = ED::getTimeZone();

		$dateTime = new DateTimeZone($userTimeZone);
		$date->setTimeZone($dateTime);

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

		if (func_num_args() == 0 || empty($args) || empty($args[0])) {
			return false;
		}

		$sig = md5(serialize($args));

		if (!array_key_exists($sig, $helpers)) {
			$helper	= preg_replace('/[^A-Z0-9_\.-]/i', '', $args[0]);
			$file = DISCUSS_HELPERS . '/' . EDJString::strtolower($helper) . '.php';

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
			$file	= DISCUSS_HELPERS . '/' . EDJString::strtolower( $helper ) . '.php';

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

	public static function getRegistry($data = '')
	{
		$registry = new JRegistry($data);

		return $registry;
	}

	/**
	 * Reads a XML file.
	 *
	 * @since   5.0.0
	 * @access  public
	 */
	 public static function getXml($data, $isFile = true)
	 {
		$class = 'SimpleXMLElement';

		if (class_exists('JXMLElement')) {
			$class = 'JXMLElement';
		}

		if ($isFile) {
			// Try to load the XML file
			$xml = simplexml_load_file($data, $class);

		} else {
			// Try to load the XML string
			$xml = simplexml_load_string($data, $class);
		}

		if ($xml === false) {
			foreach (libxml_get_errors() as $error) {
				echo "\t", $error->message;
			}
		}

		return $xml;
	 }

	public static function getUnansweredCount( $categoryId = '0', $excludeFeatured = false )
	{
		$db		= ED::db();

		$excludeCats	= ED::getPrivateCategories();
		$catModel		= ED::model('Categories');

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

		if (!ED::isSiteAdmin() && !ED::isModerator()) {
			$query	.= ' AND a.`private`=' . $db->Quote(0);
		}


		$db->setQuery( $query );

		return $db->loadResult();
	}

	public static function getFeaturedCount( $categoryId )
	{
		$db = ED::db();

		$query  = 'SELECT COUNT(1) as `CNT` FROM `#__discuss_posts` AS a';

		$query  .= ' WHERE a.`featured` = ' . $db->Quote('1');
		$query  .= ' AND a.`parent_id` = ' . $db->Quote('0');
		$query  .= ' AND a.`published` = ' . $db->Quote('1');
		$query	.= ' AND a.`category_id`= ' . $db->Quote( $categoryId );

		$db->setQuery($query);

		$result = $db->loadResult();

		return $result;
	}

	/**
	 * Allows caller to queue a message
	 *
	 * @since	4.0
	 * @access	public
	 */
	public static function setMessage($message, $type = 'info')
	{
		$session = JFactory::getSession();

		$msgObj = new stdClass();
		$msgObj->message = JText::_($message);
		$msgObj->type = strtolower($type);

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

	public static function getAlias($title, $type ='post', $id ='0')
	{
		$items = explode(' ', $title);

		foreach ($items as $index => $item) {
			if (strpos($item, '*' ) !== false) {
				$items[$index] = str_replace('*', '-', $items[$index]);
			}
		}

		$title = implode(' ', $items);
		$alias	= ED::permalinkSlug($title);

		// Skip this if the alias from reply post
		if ($type == 'reply') {
			return $alias;
		}

		$tmp = $alias;

		// Make sure no such alias exists.
		$i	= 1;

		while (EDR::_isAliasExists($alias, $type, $id)) {
			$alias = $tmp . '-' . $i;
			$i++;
		}

		return $alias;
	}

	public static function permalinkSlug($string, $uid = null)
	{
		$config	= ED::config();
		if ($config->get('main_sef_unicode')) {

			if ($uid && is_numeric($uid)) {
				$string = $uid . ':' . $string;
			}

			// Unicode support.
			$alias = ED::permalinkUnicodeSlug($string);

		} else {
			// Replace accents to get accurate string
			//$alias	= DiscussRouter::replaceAccents( $string );
			// hÃ¤llÃ¶ wÃ¶rldÃŸ became hallo-world instead haelloe-woerld thus above line is commented
			// for consistency with joomla

			$alias	= JFilterOutput::stringURLSafe( $string );

			// check if anything return or not. If not, then we give a date as the alias.
			if(trim(str_replace('-', '', $alias)) == '') {
				$alias = ED::date()->format("Y-m-d-H-i-s");
			}
		}
		return $alias;
	}

	public static function permalinkUnicodeSlug( $string )
	{
		$slug	= '';
		if(ED::getJoomlaVersion() >= '1.6')
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
			$slug = trim(EDJString::strtolower($slug));

			// remove any duplicate whitespace and replace whitespaces by hyphens
			$slug =preg_replace('#\x20+#','-', $slug);
		}

		return $slug;
	}

	public static function getNotification()
	{
		static $notify = false;

		if (!$notify) {
			$notify	= ED::notifications();
		}

		return $notify;
	}

	public static function getMailQueue()
	{
		static $mailq = false;

		if (!$mailq) {
			$mailq = ED::mailqueue();
		}

		return $mailq;
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

	public static function getLoginHTML( $returnURL )
	{
		$tpl	= new DiscussThemes();
		$tpl->set( 'return'	, base64_encode( $returnURL ) );

		return $tpl->fetch( 'ajax.login.php' );
	}

	public static function getLocalParser()
	{
		$data = new stdClass();

		$contents = file_get_contents(DISCUSS_ADMIN_ROOT . '/easydiscuss.xml');

		$parser = new DiscussXMLHelper($contents);

		return $parser;
	}

	/**
	 * Retrieves the current version of EasyDiscuss
	 *
	 * @since	4.2.0
	 * @access	public
	 */
	public static function getLocalVersion()
	{
		static $version = null;

		if (is_null($version)) {

			$manifest = DISCUSS_ADMIN_ROOT . '/easydiscuss.xml';

			$parser = self::getXml($manifest, true);

			$version = (string) $parser->version;
		}

		return $version;
	}

	/**
	 * Retrieves the server's version of EasyDiscuss
	 *
	 * @since	4.0
	 * @access	public
	 */
	public static function getVersion()
	{
		static $version = null;

		if (is_null($version)) {

			$connector = ED::connector();
			$connector->addUrl(ED_UPDATER);
			$connector->connect();

			$contents = $connector->getResult(ED_UPDATER);

			if (!$contents) {
				$version = false;

				return $version;
			}

			$obj = json_decode($contents);

			if (!$obj) {
				$version = false;

				return $version;
			}

			$version = $obj->version;
		}

		return $version;
	}

	/**
	 * Retrieves the default value from the configuration file
	 *
	 * @since	4.0
	 * @access	public
	 */
	public static function getDefaultConfigValue($key, $defaultVal = null)
	{
		static $defaults = null;

		if (is_null($defaults)) {

			$file = DISCUSS_ADMIN_ROOT . '/defaults/configuration.ini';
			$contents = file_get_contents($file);

			$defaults = new JRegistry($contents);
		}

		return $defaults->get($key, $defaultVal);
	}

	/**
	 * Retrieves the core configuration object for EasyDiscuss.
	 *
	 * @since	1.0
	 * @access	public
	 */
	public static function config()
	{
		if (defined('ED_CLI')) {
			return false;
		}

		static $config = null;

		if (is_null($config)) {

			// Render the data from the ini first.
			$raw = file_get_contents(DISCUSS_ADMIN_ROOT . '/defaults/configuration.ini');

			$config = new JRegistry($raw);

			// Retrieve the data from the db
			$db = ED::db();

			$query = 'SELECT ' . $db->qn('params') . ' FROM ' . $db->qn('#__discuss_configs');
			$query .= 'WHERE ' . $db->qn('name') . '=' . $db->Quote('config');

			$db->setQuery($query);
			$result = $db->loadResult();

			$config->loadString($result);
		}

		return $config;
	}

	/**
	 * Retrieves Joomla's configuration object
	 *
	 * @since	5.0.0
	 * @access	public
	 */
	public static function jConfig()
	{
		static $config = null;

		if (is_null($config)) {
			require_once(__DIR__ . '/jconfig/jconfig.php');
			$config = new EasyDiscussJConfig();
		}

		return $config;
	}

	/**
	 * If the current user is a super admin, allow them to change the environment via the query string
	 *
	 * @since	4.2.0
	 * @access	public
	 */
	public static function checkEnvironment()
	{
		if (!ED::isSiteAdmin()) {
			return;
		}

		$app = JFactory::getApplication();
		$environment = $app->input->get('ed_env', '', 'word');
		$allowed = array('production', 'development');

		// Nothing has changed
		if (!$environment || !in_array($environment, $allowed)) {
			return;
		}

		// We also need to update the database value
		$config = ED::table('Configs');
		$config->load(array('name' => 'config'));

		$params = new JRegistry($config->params);
		$params->set('system_environment', $environment);

		$config->params = $params->toString();
		$config->store();

		ED::setMessage('Updated system environment to <b>' . $environment . '</b> mode', 'success');

		return self::redirect('index.php?option=com_easydiscuss');
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

	/**
	 * Determines if the user is a site admin
	 *
	 * @since	5.0.0
	 * @access	public
	 */
	public static function isSiteAdmin($userId = null)
	{
		static $cache = array();
		
		$key = is_null($userId) ? 'me' : $userId;

		if (!isset($cache[$key])) {
			$user = JFactory::getUser($userId);
			$cache[$key] = $user->authorise('core.admin');
		}

		return $cache[$key];
	}

	/**
	 * Determines if the item belongs to the current viewer
	 *
	 * @since	5.0.0
	 * @access	public
	 */
	public static function isMine($uid)
	{
		static $cache = [];

		if (!isset($cache[$uid])) {
			$my	= JFactory::getUser();

			if ($my->id == 0 || empty($uid)) {
				$cache[$uid] = false;
				return false;
			}

			$cache[$uid] = $my->id == $uid ? 1 : 0;
		}

		return $cache[$uid];
	}

	public static function getUserId($username, $isAlias = false)
	{
		static $userids = [];
		$type = $isAlias ? 'alias' : 'nameFormat';

		if (!isset($userids[$type][$username]) || empty($userids[$type][$username])) {
			$db = ED::db();

			$config = ED::config();
			$nameFormat = $config->get('layout_nameformat');

			if ($isAlias) {
				$query = 'SELECT `id` FROM `#__discuss_users` WHERE `alias` = ' . $db->quote($username);
			}

			if (!$isAlias && $nameFormat == 'nickname') {
				$query = 'SELECT `id` FROM `#__discuss_users` WHERE `nickname` = ' . $db->quote($username);
			}

			if (!$isAlias && $nameFormat == 'username') {
				$query = 'SELECT `id` FROM `#__users` WHERE `username`=' . $db->quote($username);
			}

			if (!$isAlias && $nameFormat == 'name') {
				$query = 'SELECT `id` FROM `#__users` WHERE `name`=' . $db->quote($username);
			}

			$db->setQuery($query);
			$userid	= $db->loadResult();

			$userids[$type][$username] = $userid;
		}

		return $userids[$type][$username];
	}

	public static function getAjaxURL()
	{
		static $url;

		if (isset($url)) {
			return $url;
		}

		$uri = EDFactory::getURI();
		$language = $uri->getVar('lang', 'none');

		// Remove any ' or " from the language because language should only have -
		$app = JFactory::getApplication();
		$input = $app->input;

		$language = $input->get('lang', '', 'cmd');

		$jConfig = ED::jconfig();

		// Get the router
		$router = $app->getRouter();

		// It could be admin url or front end url
		$url = rtrim(JURI::base(), '/') . '/';

		// Determines if we should use index.php for the url
		$config = ED::config();

		if ($config->get('system_ajax_index')) {
			$url .= 'index.php';
		}

		// Append the url with the extension
		$url = $url . '?option=com_easydiscuss&lang=' . $language;

		// During SEF mode, we need to ensure that the URL is correct.
		$languageFilterEnabled = JPluginHelper::isEnabled("system", "languagefilter");

		if (EDRouter::getMode() == ED_JROUTER_MODE_SEF && $languageFilterEnabled) {

			$sefs = JLanguageHelper::getLanguages('sef');
			$lang_codes   = JLanguageHelper::getLanguages('lang_code');

			$plugin = JPluginHelper::getPlugin('system', 'languagefilter');
			$params = new JRegistry();
			$params->loadString(empty($plugin) ? '' : $plugin->params);
			$removeLangCode = is_null($params) ? 'null' : $params->get('remove_default_prefix', 'null');


			// Determines if the mod_rewrite is enabled on Joomla
			$rewrite = $jConfig->getValue('sef_rewrite');

			if ($removeLangCode) {
				$defaultLang = JComponentHelper::getParams('com_languages')->get('site', 'en-GB');
				$currentLang = $app->input->cookie->getString(JApplicationHelper::getHash('language'), $defaultLang);

				$defaultSefLang = $lang_codes[$defaultLang]->sef;
				$currentSefLang = $lang_codes[$currentLang]->sef;

				if ($defaultSefLang == $currentSefLang) {
					$language = '';
				} else {
					$language = $currentSefLang;
				}

			} else {
				// Replace the path if it's on subfolders
				$base = str_ireplace(JURI::root(true), '', $uri->getPath());

				if ($rewrite) {
					$path = $base;
				} else {
					$path = EDJString::substr($base, 10);
				}

				// Remove trailing / from the url
				$path = EDJString::trim($path, '/');
				$parts = explode('/', $path);

				if ($parts) {
					// First segment will always be the language filter.
					$language = reset($parts);
				} else {
					$language = 'none';
				}
			}

			if ($rewrite) {
				$url = rtrim(JURI::root(), '/') . '/' . $language . '?option=com_easydiscuss';

			} else {
				$url = rtrim(JURI::root(), '/') . '/index.php/' . $language . '?option=com_easydiscuss';
			}
		}

		$menu = JFactory::getApplication()->getmenu();

		if (!empty($menu)) {
			$item = $menu->getActive();

			if (isset($item->id)) {
				$url .= '&Itemid=' . $item->id;
			}
		}

		// Some SEF components tries to do a 301 redirect from non-www prefix to www prefix. Need to sort them out here.
		$currentURL = isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : '';

		if (!empty($currentURL)) {

			// When the url contains www and the current accessed url does not contain www, fix it.
			if (stristr($currentURL, 'www') === false && stristr($url, 'www') !== false) {
				$url = str_ireplace('www.', '', $url);
			}

			// When the url does not contain www and the current accessed url contains www.
			if (stristr($currentURL, 'www') !== false && stristr($url, 'www') === false) {
				$url = str_ireplace('://', '://www.', $url);
			}
		}

		return $url;
	}

	public static function getBaseUrl()
	{
		static $url;

		if (isset($url)) return $url;

		if( ED::getJoomlaVersion() >= '1.6' )
		{
			$uri		= JFactory::getURI();
			$language	= $uri->getVar( 'lang' , 'none' );
			$app		= JFactory::getApplication();
			$config		= ED::jConfig();
			$router		= $app->getRouter();
			$url		= rtrim( JURI::base() , '/' );

			$url 		= $url . '/index.php?option=com_easydiscuss&lang=' . $language;

			if( $router->getMode() == JROUTER_MODE_SEF && JPluginHelper::isEnabled("system","languagefilter") )
			{
				$rewrite	= $config->get('sef_rewrite');

				$base		= str_ireplace( JURI::root( true ) , '' , $uri->getPath() );
				$path		=  $rewrite ? $base : EDJString::substr( $base , 10 );
				$path		= EDJString::trim( $path , '/' );
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

	/**
	 * Loads the default languages for EasyDiscuss
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public static function loadLanguages($path = JPATH_ROOT)
	{
		static $loaded = array();

		if (!isset($loaded[$path])) {
			$lang = JFactory::getLanguage();

			// Load site's default language file.
			$lang->load('com_easydiscuss', $path);

			$loaded[$path] = true;
		}

		return $loaded[$path];
	}

	public static function getDurationString($dateTimeDiffObj)
	{
		$lang = JFactory::getLanguage();
		$lang->load('com_easydiscuss', JPATH_ROOT);

		$data = $dateTimeDiffObj;
		$returnStr = '';

		if ($data->daydiff <= 0) {
			$timeDate = explode(':', $data->timediff);

			// ensure all has a colon
			if (!isset($timeDate[1])) {
				$timeDate[1] = null;
			}

			if (intval($timeDate[0], 10) >= 1) {
				$returnStr = ED::string()->getNoun('COM_EASYDISCUSS_HOURS_AGO', intval($timeDate[0], 10), true);

			} else if(intval($timeDate[1], 10) >= 2) {
				$returnStr = ED::string()->getNoun('COM_EASYDISCUSS_MINUTES_AGO', intval($timeDate[1], 10), true);

			} else {
				$returnStr = JText::_('COM_EASYDISCUSS_LESS_THAN_A_MINUTE_AGO');
			}

		} else if (($data->daydiff >= 1) && ($data->daydiff < 7)) {
			$returnStr = ED::string()->getNoun('COM_EASYDISCUSS_DAYS_AGO', $data->daydiff, true);

		} else if ($data->daydiff >= 7 && $data->daydiff <= 30) {
			$returnStr = (intval($data->daydiff/7, 10) == 1 ? JText::_('COM_EASYDISCUSS_ONE_WEEK_AGO') : JText::sprintf('COM_EASYDISCUSS_WEEKS_AGO', intval($data->daydiff/7, 10)));

		} else {
			$returnStr = JText::_('COM_EASYDISCUSS_MORE_THAN_A_MONTH_AGO');
		}

		return $returnStr;
	}

	/**
	 * Sets data into the session
	 *
	 * @since	5.0.0
	 * @access	public
	 */
	public static function storeSession($data, $key, $ns = 'com_easydiscuss')
	{
		$mySess	= JFactory::getSession();
		$mySess->set($key, $data, $ns);
	}

	public static function getSession($key, $ns = 'com_easydiscuss')
	{
		$data = null;

		$mySess = JFactory::getSession();
		if ($mySess->has($key, $ns)) {
			$data = $mySess->get($key, '', $ns);
			$mySess->clear($key, $ns);
			return $data;
		}

		return $data;
	}

	public static function isTwoFactorEnabled()
	{
		$twoFactorMethods = JAuthenticationHelper::getTwoFactorMethods();

		return count($twoFactorMethods) > 1;
	}

	/**
	 * Determines if the item is considered as new
	 *
	 * @since	5.0.0
	 * @access	public
	 */
	public static function isNew($noofdays)
	{
		static $cache = [];

		if (!isset($cache[$noofdays])) {
			$config	= ED::config();

			$days = (int) $config->get('layout_daystostaynew', 7);
			$isNew = false;

			if ($days > 0) {
				$isNew	= ($noofdays <= $config->get('layout_daystostaynew', 7)) ? true : false;
			}

			$cache[$noofdays] = $isNew;
		}

		return $cache[$noofdays];
	}

	public static function getExternalLink($link)
	{
		$uri = JURI::getInstance();
		$domain	= $uri->toString(array('scheme', 'host', 'port'));

		return $domain . '/' . ltrim(EDR::_($link, false), '/');
	}

	public static function uploadCategoryAvatar( $category, $isFromBackend = false )
	{
		return ED::uploadMediaAvatar('category', $category, $isFromBackend);
	}

	public static function uploadMediaAvatar($mediaType, $mediaTable, $isFromBackend = false)
	{
		jimport('joomla.utilities.error');
		jimport('joomla.filesystem.file');
		jimport('joomla.filesystem.folder');

		$my = JFactory::getUser();
		$mainframe = JFactory::getApplication();
		$config = ED::config();

		// required params
		$layout_type = ($mediaType == 'category') ? 'categories' : 'teamblogs';
		$view_type = ($mediaType == 'category') ? 'categories' : 'teamblogs';
		$default_avatar_type = ($mediaType == 'category') ? 'default_category.png' : 'default_team.png';

		if (!$isFromBackend && $mediaType == 'category') {
			$url = 'index.php?option=com_easydiscuss&view=categories';
			ED::setMessage(JText::_('COM_EASYDISCUSS_NO_PERMISSION_TO_UPLOAD_AVATAR') , 'warning');
			self::redirect(EDR::_($url, false));
		}

		$avatar_config_path	= ($mediaType == 'category') ? $config->get('main_categoryavatarpath') : $config->get('main_teamavatarpath');
		$avatar_config_path	= rtrim($avatar_config_path, '/');
		$avatar_config_path	= str_replace('/', DIRECTORY_SEPARATOR, $avatar_config_path);

		$upload_path = JPATH_ROOT . '/' . $avatar_config_path;
		$rel_upload_path = $avatar_config_path;

		$err = null;
		$file = $mainframe->input->files->get('Filedata', '');

		//check whether the upload folder exist or not. if not create it.
		if (!JFolder::exists($upload_path)) {
			if (!JFolder::create($upload_path)) {
				// Redirect
				if(!$isFromBackend) {
					ED::setMessage(JText::_('COM_EASYDISCUSS_IMAGE_UPLOADER_FAILED_TO_CREATE_UPLOAD_FOLDER') , ED_MSG_ERROR);
					self::redirect(EDR::_('index.php?option=com_easydiscuss&view=categories', false));
				} else {
					//from backend
					self::redirect(EDR::_('index.php?option=com_easydiscuss&view=categories', false), JText::_('COM_EASYDISCUSS_IMAGE_UPLOADER_FAILED_TO_CREATE_UPLOAD_FOLDER'), ED_MSG_ERROR);
				}
				return;
			} else {
				// folder created. now copy index.html into this folder.
				if (!JFile::exists( $upload_path . '/index.html')) {
					$targetFile	= DISCUSS_ROOT . '/index.html';
					$destFile = $upload_path . '/index.html';

					if(JFile::exists($targetFile))
						JFile::copy($targetFile, $destFile);
				}
			}
		}

		//makesafe on the file
		$file['name'] = $mediaTable->id . '_' . JFile::makeSafe($file['name']);

		if (isset($file['name'])) {
			$target_file_path = $upload_path;
			$relative_target_file = $rel_upload_path . '/' . $file['name'];
			$target_file = JPath::clean($target_file_path . '/' . JFile::makeSafe($file['name']));
			$isNew = false;

			require_once(__DIR__ . '/image/image.php');
			require_once(__DIR__ . '/simpleimage/simpleimage.php');

			if (!EasyDiscussImage::canUpload($file, $err)) {
				if(!$isFromBackend) {
					ED::setMessage( JText::_($err), ED_MSG_ERROR);
					self::redirect(EDR::_('index.php?option=com_easydiscuss&view=categories', false));
				} else {
					// From backend
					self::redirect(EDR::_('index.php?option=com_easydiscuss&view=categories'), JText::_($err), ED_MSG_ERROR);
				}
				return;
			}

			if (0 != (int)$file['error']) {
				if (!$isFromBackend) {
					ED::setMessage($file['error'], ED_MSG_ERROR);
					self::redirect(EDR::_('index.php?option=com_easydiscuss&view=categories', false));
				} else {
					// From backend
					self::redirect(EDR::_('index.php?option=com_easydiscuss&view=categories', false), $file['error'], ED_MSG_ERROR);
				}
				return;
			}

			// Rename the file 1st.
			$oldAvatar = (empty($mediaTable->avatar)) ? $default_avatar_type : $mediaTable->avatar;
			$tempAvatar = '';
			if ($oldAvatar != $default_avatar_type) {
				$session = JFactory::getSession();
				$sessionId = $session->getToken();

				$fileExt = JFile::getExt(JPath::clean($target_file_path . '/' . $oldAvatar));
				$tempAvatar = JPath::clean($target_file_path . '/' . $sessionId . '.' . $fileExt);

				JFile::move($target_file_path . '/' . $oldAvatar, $tempAvatar);
			} else {
				$isNew  = true;
			}


			if (JFile::exists($target_file)) {
				if ($oldAvatar != $default_avatar_type) {
					//rename back to the previous one.
					JFile::move($tempAvatar, $target_file_path . '/' . $oldAvatar);
				}

				if (!$isFromBackend) {
					ED::setMessage(JText::sprintf('ERROR.FILE_ALREADY_EXISTS', $relative_target_file), ED_MSG_ERROR);
					self::redirect(EDR::_('index.php?option=com_easydiscuss&view=categories', false));
				} else {
					//from backend
					self::redirect(EDR::_('index.php?option=com_easydiscuss&view=categories', false), JText::sprintf('ERROR.FILE_ALREADY_EXISTS', $relative_target_file), ED_MSG_ERROR);
				}
				return;
			}

			if (JFolder::exists($target_file)) {

				if ($oldAvatar != $default_avatar_type) {
					//rename back to the previous one.
					JFile::move($tempAvatar, $target_file_path . '/' . $oldAvatar);
				}

				if (!$isFromBackend) {
					ED::setMessage(JText::sprintf('ERROR.FOLDER_ALREADY_EXISTS', $relative_target_file), ED_MSG_ERROR);
					self::redirect(EDR::_('index.php?option=com_easydiscuss&view=categories', false));
				} else {
					//from backend
					self::redirect(EDR::_('index.php?option=com_easydiscuss&view=categories', false), JText::sprintf('ERROR.FILE_ALREADY_EXISTS', $relative_target_file), ED_MSG_ERROR);
				}
				return;
			}

			$configImageWidth  = DISCUSS_AVATAR_LARGE_WIDTH;
			$configImageHeight = DISCUSS_AVATAR_LARGE_HEIGHT;

			$image = new EasyDiscussSimpleImage();
			$image->load($file['tmp_name']);
			$image->resize($configImageWidth, $configImageHeight);
			$image->save($target_file, $image->image_type);

			//now we update the user avatar. If needed, we remove the old avatar.
			if ($oldAvatar != $default_avatar_type) {
				if (JFile::exists($tempAvatar)) {
					JFile::delete($tempAvatar);
				}
			}

			return JFile::makeSafe($file['name']);
		} else {
			return $default_avatar_type;
		}

	}

	/**
	 * Applies word filtering
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public static function wordFilter($text)
	{
		$config = ED::Config();

		if (empty($text)) {
			return $text;
		}

		if (trim($text) == '') {
			return $text;
		}

		if ($config->get('main_filterbadword', 1) && $config->get('main_filtertext', '') != '') {

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
					if( EDJString::stristr($item, ' ') !== false )
					{
						$newKeyWord 	= EDJString::str_ireplace(' ', '', $item);
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
	 * Formats a discussion object
	 *
	 * @since	4.0
	 * @access	public
	 */
	public static function formatPost($rows, $isSearch = false , $isFrontpage = false)
	{
		// If there is no items, skip this altogether
		if (!$rows) {
			return $rows;
		}

		$posts = array();

		foreach ($rows as $row) {

			// Load it into our post library
			$post = ED::post($row);
			
			if ($isFrontpage) {
				$model = ED::model('Posts');
				$post->lastReply = $model->getLastReply($post->id);
			}

			$posts[] = $post;
		}

		return $posts;
	}

	/**
	 * Given a list of comments, format into the EasyDiscussComment object
	 *
	 * @since	5.0.0
	 * @access	public
	 */
	public static function formatComments($comments)
	{
		if (!$comments) {
			return false;
		}

		$result = array();

		foreach ($comments as $row) {
			$comment = ED::comment($row);
			$result[] = $comment;
		}

		return $result;
	}

	/**
	 * Formats the necessary output for reply items
	 *
	 * @since	4.0
	 * @access	public
	 */
	public static function formatReplies($result, $category = null, $pagination = true, $acceptedReply = false)
	{
		$config = ED::config();

		if (!$result) {
			return $result;
		}

		$limitstart = JFactory::getApplication()->input->get('limitstart', 0, 'int');
		$replies = array();

		foreach ($result as $key => $row) {
			$reply = ED::post($row);

			$reply->permalink = EDR::getReplyRoute($reply->parent_id, $reply->id);

			// Default reply permalink title; specifically for accepted answer.
			$reply->seq = JText::_('COM_EASYDISCUSS_REPLY_PERMALINK_TITLE');

			if (!$acceptedReply) {
				$reply->seq = $key + 1;

				if ($pagination) {
					$reply->seq = $limitstart ? $key + $limitstart + 1 : $key + 1;
				}
			}

			if ($config->get('main_comment')) {
				$commentLimit = $config->get('main_comment_pagination') ? $config->get('main_comment_pagination_count') : null;
				$reply->comments = $reply->getComments($commentLimit);

				// get post comments count
				$reply->commentsCount = $reply->getTotalComments();
			}

			// this flag is needed in order to distinguish between reply and activity logs.
			$reply->isActivity = false;

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
		ED::user($authorIds);

		$users	= array();
		for( $i =0 ; $i < $total; $i++ )
		{
			$row	=& $result[ $i ];

			$user = ED::user($row->id);
			$users[] = $user;
		}

		return $users;
	}

	/**
	 * function to the column collation that compatible with Joomla 3.5 jos_users table collation.
	 *
	 * @since	4.1.12
	 * @access	public
	 */
	public static function getUsersTableCollation($from)
	{
		static $collationType = array();

		if (!isset($collationType[$from])) {

			// Default value
			$collationType[$from] = '';

			$jVersion = ED::getJoomlaVersion();

			if ($jVersion >= '3.5') {
				$jConfig = ED::jconfig();
				$dbType = $jConfig->get('dbtype');

				if ($dbType == 'mysql' || $dbType == 'mysqli' || $dbType == 'pdomysql') {
					$db = ED::db();
					$dbversion = $db->getVersion();
					$dbversion = (float) $dbversion;

					if ($dbversion >= '5.1') {

						$prefix = $db->getPrefix();

						$tableName = $prefix . 'users';

						$query = "SHOW TABLE STATUS WHERE `Name` = " . $db->Quote($tableName);
						$db->setQuery($query);
						$result = $db->loadObject();

						$collation = $result->Collation;

						if (strpos($collation, 'mb4_') !== false) {

							if ($from == 'ed') {
								$tableName2 = $prefix . 'discuss_subscription';
								// dump($tableName2);

								$query = "SHOW TABLE STATUS WHERE `Name` = " . $db->Quote($tableName2);
								$db->setQuery($query);
								$result = $db->loadObject();

								$collation2 = $result->Collation;

								if (strpos($collation2, 'mb4_') !== false) {
									$collationType[$from] = 'COLLATE utf8mb4_unicode_ci';
								} else {
									$collationType[$from] = 'COLLATE utf8_unicode_ci';
								}
							} else {
								$collationType[$from] = 'COLLATE utf8mb4_unicode_ci';
							}

						} else if ($db->hasUTFSupport()) {
							$collationType[$from] = 'COLLATE utf8_unicode_ci';
						}
					}
				}
			}
		}

		return $collationType[$from];
	}

	/**
	 * Determines if this is from the Joomla backend
	 *
	 * @since	5.0.0
	 * @access	public
	 */
	public static function isFromAdmin()
	{
		$isFromAdmin = null;

		if (is_null($isFromAdmin)) {
			$isFromAdmin = EDCompat::isFromAdmin();
		}

		return $isFromAdmin;
	}

	/**
	 * Determines if the current Joomla version is 4.0
	 *
	 * @since	5.0.0
	 * @access	public
	 */
	public static function isJoomla4()
	{
		static $isJoomla4 = null;

		if (is_null($isJoomla4)) {
			$currentVersion = self::getJoomlaVersion();
			$isJoomla4 = version_compare($currentVersion, '4.0') !== -1;

			return $isJoomla4;
		}

		return $isJoomla4;
	}

	/**
	 * Retrieves Joomla's version
	 *
	 * @since	5.0.0
	 * @access	public
	 */
	public static function getJoomlaVersion()
	{
		static $version = null;

		if (is_null($version)) {
			$jVerArr = explode('.', JVERSION);
			$version = $jVerArr[0] . '.' . $jVerArr[1];
		}

		return $version;
	}

	public static function isJoomla40()
	{
		return ED::getJoomlaVersion() >= '4.0';
	}

	public static function isJoomla31()
	{
		return ED::getJoomlaVersion() >= '3.1';
	}

	public static function isJoomla30()
	{
		return ED::getJoomlaVersion() >= '3.0';
	}

	public static function isJoomla25()
	{
		return ED::getJoomlaVersion() >= '1.6' && ED::getJoomlaVersion() <= '2.5';
	}

	public static function isJoomla15()
	{
		return ED::getJoomlaVersion() == '1.5';
	}

	public static function getDefaultSAIds()
	{
		$saUserId	= '62';

		if(ED::getJoomlaVersion() >= '1.6')
		{
			$saUsers	= ED::getSAUsersIds();
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
		$db = ED::db();

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
	 *
	 * @since	1.0
	 * @access	public
	 */	
	public static function getSAUsersIds()
	{
		if (ED::getJoomlaVersion() < '1.6') {
			return ED::getSAUsersIds15();
		}

		$db = ED::db();

		$query	= 'SELECT a.`id`, a.`title`';
		$query	.= ' FROM `#__usergroups` AS a';
		$query	.= ' LEFT JOIN `#__usergroups` AS b ON a.lft > b.lft AND a.rgt < b.rgt';
		$query	.= ' GROUP BY a.id';
		$query	.= ' ORDER BY a.lft ASC';

		$db->setQuery($query);
		$result = $db->loadObjectList();

		$saGroup = array();
		
		foreach ($result as $group) {

			if (JAccess::checkGroup($group->id, 'core.admin')) {
				$saGroup[] = $group;
			}
		}

		//now we got all the SA groups. Time to get the users
		$saUsers = array();

		if (count($saGroup) > 0) {

			foreach ($saGroup as $sag) {

				$userArr = JAccess::getUsersByGroup($sag->id);
				
				if (count($userArr) > 0) {

					foreach ($userArr as $user) {

						$saUsers[] = $user;
					}
				}
			}
		}

		return $saUsers;
	}

	/**
	 * Generates a html code for category selection.
	 *
	 * @access	public
	 * @param	int		$parentId	if this option spcified, it will list the parent and all its childs categories.
	 * @param	int		$userId		if this option specified, it only return categories created by this userId
	 * @param	string	$outType	The output type. Currently supported links and drop down selection
	 * @param	string	$eleName	The element name of this populated categeries provided the outType os dropdown selection.
	 * @param	string	$default	The default selected value. If given, it used at dropdown selection (auto select)
	 * @param	boolean	$isWrite	Determine whether the categories list used in write new page or not.
	 * @param	boolean	$isPublishedOnly	If this option is true, only published categories will fetched.
	 * @param	array 	$exclusion	A list of excluded categories that it should not be including
	 * @param	boolean $multiple	The select type (multi-selection)
	 */
	public static function populateCategories($parentId, $userId, $outType, $eleName, $default = false, $isWrite = false, $isPublishedOnly = false, $showPrivateCat = true, $disableContainers = false, $customClass = 'form-control', $exclusion = array(), $aclType = DISCUSS_CATEGORY_ACL_ACTION_VIEW, $sorting = false, $multiple = false, $containerOnly = false, $attributes = array())
	{
		$model = ED::model('Categories');
		$parentCat	= null;

		if (!empty($userId)) {
			// Only get parent categories created by this user
			$parentCat = $model->getParentCategories($userId, 'poster', $isPublishedOnly, $showPrivateCat, $exclusion, $aclType, $sorting);
		} else if (!empty($parentId)) {
			// Get a specified parent category only
			$parentCat = $model->getParentCategories($parentId, 'category', $isPublishedOnly, $showPrivateCat, $exclusion, $aclType, $sorting);
		} else if ($containerOnly) {
			$parentCat = $model->getCatContainer();
		} else {
			// Get all parent categories
			$parentCat = $model->getParentCategories('', 'all', $isPublishedOnly, $showPrivateCat, $exclusion, $aclType, $sorting);
		}

		// If no parent categories found, skip.
		if (empty($parentCat)) {
			return;
		}

		$ignorePrivate = true;

		if ($outType == 'link') {
			$ignorePrivate = false;
		}

		$selectACLOnly = false;

		if ($isWrite && !self::isFromAdmin()) {
			$ignorePrivate = false;
			$selectACLOnly = true;
		}

		for ($i = 0; $i < count($parentCat); $i++) {

			$parent =& $parentCat[$i];

			$parent->childs = null;

			// Get childs for this parent category
			if (!$containerOnly) {
				ED::buildNestedCategories($parent->id, $parent, $ignorePrivate, $isPublishedOnly, $showPrivateCat, $selectACLOnly, $exclusion, $sorting);
			}

		}

		$formEle = '';

		if (!is_array($default)) {
			$default = array($default);
		}

		// It is time to build the form
		foreach ($parentCat as $category) {

			$selected = (in_array($category->id, $default)) ? ' selected="selected"' : '';

			if ($default === false) {
				$selected = $category->default ? ' selected="selected"' : '';
			}

			$style = '';
			$disabled = '';

			// @rule: Test if the category should just act as a container
			if ($disableContainers) {
				$disabled = $category->container ? ' disabled="disabled"' : '';
				$style = $disabled ? ' style="font-weight:700;"' : '';
			}

			// This is for parent categories
			$formEle .= '<option value="' . $category->id . '" ' . ' data-ed-move-post-category-id=' . $category->id . ' ' . $selected . $disabled . $style . '>' . JText::_($category->title) . '</option>';

			// This is for childs
			ED::accessNestedCategories($category, $formEle, '0', $default, $outType, '', $disableContainers);
		}

		$selected = empty($default) ? ' selected="selected"' : '';
		$multiple = $multiple ? 'multiple style="height:150px"' : '';

		$name = $multiple ? $eleName . '[]' : $eleName;

		$dataAttributes = '';

		if ($attributes) {
			$dataAttributes = implode(' ', $attributes);
		}

		$html = '<select ' . $multiple . ' name="' . $name . '" id="' . $eleName .'" class="' . $customClass . '" ' . $dataAttributes . '>';

		if (!$isWrite) {
			$html .= '<option value="0">' . JText::_('COM_EASYDISCUSS_SELECT_PARENT_CATEGORY') . '</option>';
		} else {
			if (!$multiple) {
				$html .= '<option value="0" ' . $selected . '>' . JText::_('COM_EASYDISCUSS_SELECT_CATEGORY') . '</option>';
			}

			$html .= $formEle;
			$html .= '</select>';
		}

		return $html;
	}

	public static function buildNestedCategories($parentId, $parent, $ignorePrivate = false, $isPublishedOnly = false, $showPrivate = true, $selectACLOnly = false, $exclusion = array(), $ordering = false)
	{
		$aclType = ($selectACLOnly) ? DISCUSS_CATEGORY_ACL_ACTION_SELECT : DISCUSS_CATEGORY_ACL_ACTION_VIEW;

		// [model:category]
		$catModel = ED::model('Category');

		$catsModel = ED::model('Categories');
		$childs	= $catsModel->getChildCategories($parentId, $isPublishedOnly, $showPrivate, $exclusion, $ordering, $aclType);

		$accessibleCatsIds = ED::getAccessibleCategories($parentId, $aclType);

		if (!empty($childs)) {

			for ($j = 0; $j < count($childs); $j++) {
				$child = $childs[$j];

				$postCount = $aclType == DISCUSS_CATEGORY_ACL_ACTION_SELECT ? 0 : $catModel->getTotalPostCount($child->id);
				$child->count = $postCount;
				$child->childs = null;

				if (!$ignorePrivate) {

					if (count($accessibleCatsIds) > 0) {

						$access = false;

						foreach ($accessibleCatsIds as $canAccess) {

							if ($canAccess->id == $child->id) {
								$access = true;
							}
						}

						if (!$access)
							continue;
					} else {
						continue;
					}
				}

				if (!ED::buildNestedCategories($child->id, $child, $ignorePrivate, $isPublishedOnly, $showPrivate, $selectACLOnly, $exclusion)) {
					$parent->childs[] = $child;
				}
			}
		} else {
			return false;
		}
	}

	public static function accessNestedCategories($arr, &$html, $deep='0', $default='0', $type='select', $linkDelimiter = '' , $disableContainers = false)
	{
		$config = ED::config();

		// Making sure this $default is an array()
		if (!is_array($default)) {
			$default = array($default);
		}

		if (isset($arr->childs) && is_array($arr->childs)) {
			$sup = '<sup>|_</sup>';
			$space = '';
			$ld = (empty($linkDelimiter)) ? '>' : $linkDelimiter;

			if ($type == 'select' || $type == 'list') {
				$deep++;

				for ($d=0; $d < $deep; $d++) {
					$space .= '&nbsp;&nbsp;&nbsp;';
				}
			}

			if ($type == 'list' && !empty($arr->childs)) {
				$html .= '<ul>';
			}

			for ($j	= 0; $j < count($arr->childs); $j++) {
				$child = $arr->childs[$j];
				$child->title = JText::_($child->title);

				switch ($type) {
					case 'select':
						$selected = (in_array($child->id, $default)) ? ' selected="selected"' : '';

						if (!$default) {
							$selected = $child->default ? ' selected="selected"' : '';
						}

						$disabled = '';
						$style = '';

						// @rule: Test if the category should just act as a container
						if ($disableContainers) {
							$disabled = $child->container ? ' disabled="disabled"' : '';
							$style = $disabled ? ' style="font-weight:700;"' : '';
						}

						$html .= '<option value="'.$child->id.'" ' . $selected . $disabled . $style . '>' . $space . $sup . $child->title . '</option>';
						break;
					case 'list':
						$expand = !empty($child->childs) ? '<span onclick="EasyDiscuss.$(this).parents(\'li:first\').toggleClass(\'expand\');">[+] </span>' : '';
						$html .= '<li><div>' . $space . $sup . $expand . '<a href="' . EDR::getCategoryRoute($child->id) . '">' . $child->title . '</a> <b>(' . $child->count . ')</b></div>';
						break;
					case 'listlink':
						$str = '<li><a href="' . EDR::getCategoryRoute($child->id) . '">';
						$str .= (empty($html)) ? $child->title : $ld . '&nbsp;' . $child->title;
						$str .= '</a></li>';
						$html .= $str;
						break;
					default:
						$str = '<a href="' . EDR::getCategoryRoute($child->id) . '">';
						$str .= (empty($html)) ? $child->title : $ld . '&nbsp;' . $child->title;
						$str .= '</a></li>';
						$html .= $str;
				}

			
				ED::accessNestedCategories($child, $html, $deep, $default, $type, $linkDelimiter, $disableContainers);

				if ($type == 'list') {
					$html .= '</li>';
				}
			}

			if ($type == 'list' && !empty($arr->childs)) {
				$html .= '</ul>';
			}
		} else {
			return false;
		}
	}

	public static function accessNestedCategoriesId($arr, &$newArr)
	{
		if(isset($arr->childs) && is_array($arr->childs))
		{
			//$modelSubscribe	= ED::model('Subscribe' );
			//$subscribers	= $modelSubscribe->getSiteSubscribers('instant');

			for($j = 0; $j < count($arr->childs); $j++)
			{
				$child = $arr->childs[$j];

				$newArr[] = $child->id;
				ED::accessNestedCategoriesId($child, $newArr);
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
		$category	= ED::table( 'Category' );
		$category->load($childId);

		$obj		= new stdClass();
		$obj->id	= $category->id;
		$obj->title	= $category->title;
		$obj->alias	= $category->alias;

		$arr[]  = $obj;

		if((!empty($category->parent_id)))
		{
			ED::accessCategoryLinkage($category->parent_id, $arr);
		}

		$arr    = array_reverse($arr);
		return $arr;

	}

	public static function accessCategoryLinkage($childId, &$arr)
	{
		$category	= ED::table( 'Category' );

		$category->load($childId);

		$obj		= new stdClass();
		$obj->id	= $category->id;
		$obj->title	= $category->title;
		$obj->alias	= $category->alias;

		$arr[]  = $obj;

		if((!empty($category->parent_id)))
		{
			ED::accessCategoryLinkage($category->parent_id, $arr);
		}
		else
		{
			return false;
		}
	}


	/**
	 * Generates a html code for category selection in backend
	 *
	 * @since	4.0.22
	 * @access	public
	 */
	public static function populateCategoryFilter($eleName, $catId, $attributes, $defaultText = 'COM_EASYDISCUSS_SELECT_CATEGORY', $className = '')
	{
		$model = ED::model('Category');
		$categories = $model->generateCategoryFilterList();

		$options = "";

		if ($categories) {

			$selected = !$catId ? ' selected="selected"' : '';
			$options .= '<option value="0"' . $selected . '>' . JText::_($defaultText) . '</option>';


			foreach ($categories as $category) {

				$selected = $category->id == $catId ? ' selected="selected"' : '';

				$space = '';
				$sup = '';

				if ($category->depth > 0) {

					$sup	= '<sup>|_</sup>';

					for ($d = 0; $d < $category->depth; $d++) {
						$space .= '&nbsp;&nbsp;&nbsp;';
					}
				}

				$options .= '<option value="' . $category->id . '"' . $selected . '>' . $space . $sup . JText::_($category->title) . '</option>';
			}
		}

		$html = '';
		$html .= '<select name="' . $eleName . '" id="' . $eleName .'" class="' . $className . '" ' . $attributes . '>';
		$html .= $options;
		$html .= '</select>';

		return $html;
	}

	/**
	 * Generates a html code for post status selection in backend
	 *
	 * @since	4.1.7
	 * @access	public
	 */
	public static function populatePostLabelFilter($element, $selectedPostLabel)
	{
		$model = ED::model('PostLabels');
		$labels = $model->getPostLabelFilterList();

		$toFilter = array();

		foreach ($labels as $label) {
			$toFilter[$label->id] = $label->title;
		}

		$defaultSelection = JText::_('COM_ED_POST_LABEL_FILTER_DEFAULT');
		$theme = ED::themes();

		return $theme->html('table.filter', $element, $selectedPostLabel, $toFilter, $defaultSelection);
	}

	/**
	 * $post - post jtable object
	 * $parent - post's parent id.
	 * $isNew - indicate this is a new post or not.
	 */
	public static function sendNotification($post, $parent, $isNew, $postOwner, $prevPostStatus)
	{
		JFactory::getLanguage()->load( 'com_easydiscuss' , JPATH_ROOT );

		$config = ED::config();
		$notify	= ED::getNotification();

		$emailPostTitle = $post->title;
		$modelSubscribe		= ED::model( 'Subscribe' );

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
			$parentTable		= ED::table( 'Post' );
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
		$authorAvatar 			= DISCUSS_JURIROOT . '/media/com_easydiscuss/images/default_avatar.png';



		// @rule: Only process author items that belongs to a valid user.
		if (!empty($postOwner)) {
			$user = ED::user($postOwner);

			$authorName 		= $user->getName();
			$authorAvatar 		= $user->getAvatar();
		}

		if( $config->get('main_sitesubscription') && ($isNew || $prevPostStatus == DISCUSS_ID_PENDING) )
		{
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
			$participantEmails = ED::getHelper( 'Mailer' )->_getParticipants( $post->parent_id );

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
				ED::getHelper( 'Mailer' )->notifyAllMembers( $emailData, $newPostOwnerEmails );
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
									$emailData['unsubscribeLink']	= ED::getUnsubscribeLink( $subscribers[$key], true, true);
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

									$emailData['unsubscribeLink']	= ED::getUnsubscribeLink( $postSubscribers[$key], true, true);
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

								//$emailData['unsubscribeLink']	= ED::getUnsubscribeLink( $ownerEmail, true, true);
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
		$model		= ED::model( 'Posts' );
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
		$model	= JED::model( 'Posts' );
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
		$model	= ED::model( 'Subscribe' );

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
		$model	= ED::model( 'Subscribe' );

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
		$model 		= ED::model( 'Subscribe' );
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
		return ( ED::getJoomlaVersion() >= '1.6' ) ? 'com_users' : 'com_user';
	}

	public static function getUserComponentLoginTask()
	{
		return ( ED::getJoomlaVersion() >= '1.6' ) ? 'user.login' : 'login';
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

			$db	= ED::db();

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

			if( ED::getJoomlaVersion() >= '1.6' )
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
				$gid	= ED::getUserGids();
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
		$db 			= ED::db();
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


				if( ED::getJoomlaVersion() >= '1.6' )
				{
					// $gid	= JAccess::getGroupsByUser(0, false);

					$gid	= ED::getUserGroupId($my);
				}
				else
				{
					$gid	= ED::getUserGids();
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
				$result = ED::getAclCategories ( $acltype, $my->id );
			}

			for($i=0; $i < count($result); $i++)
			{
				$item =& $result[$i];
				$item->childs = null;

				ED::buildNestedCategories($item->id, $item, true);

				$catIds		= array();
				$catIds[]	= $item->id;
				ED::accessNestedCategoriesId($item, $catIds);

				$excludeCats	= array_merge($excludeCats, $catIds);
			}

			$result[ $sig ] = $excludeCats;
		}

		return $result[ $sig ];
	}

	public static function getAclCategories ($type = DISCUSS_CATEGORY_ACL_ACTION_VIEW, $userId = '', $parentId = false)
	{
		static $categories = array();

		//$sig = serialize( array($type, $userId, $parentId) );
		$sig = (int) $type . '-' . (int) $userId . '-' . (int) $parentId;

		//if( !array_key_exists($sig, $categories) )
		if (!isset($categories[$sig])) {
			$db = ED::db();
			$gid = JAccess::getGroupsByUser($userId, false);

			if (ED::getJoomlaVersion() >= '1.6') {
				if ($userId == '') {
					$gid = JAccess::getGroupsByUser(0, false);
				} else {
					$gid = JAccess::getGroupsByUser($userId, false);
				}
			}

			$gids = '';
			if (count($gid) > 0) {
				$gids = implode( ',', $gid );
			}

			$query = 'select c.`id` from `#__discuss_category` as c';
			$query .= ' where not exists (';
			$query .= '		select b.`category_id` from `#__discuss_category_acl_map` as b';
			$query .= '			where b.`category_id` = c.`id` and b.`acl_id` = '. $db->Quote($type);
			$query .= '			and b.`type` = ' . $db->Quote('group');
			$query .= '			and b.`content_id` IN (' . $gids . ')';

			$query .= '      )';
			$query .= ' and c.`private` = ' . $db->Quote(DISCUSS_PRIVACY_ACL);
			if( $parentId !== false )
				$query .= ' and c.`parent_id` = ' . $db->Quote($parentId);

			$db->setQuery($query);

			$categories[$sig] = $db->loadObjectList();
		}

		return $categories[$sig];
	}

	/**
	 * Renders a JTable object
	 *
	 * @since	4.0
	 * @access	public
	 */
	public static function table($name, $prefix = 'Discuss', $config = array())
	{
		// Sanitize and prepare the table class name.
		$type = preg_replace('/[^A-Z0-9_\.-]/i', '', $name);
		$className = $prefix . ucfirst($type);

		ED::import('admin:/tables/table');
		
		// Only try to load the class if it doesn't already exist.
		if (!class_exists($className)) {

			// Search for the class file in the JTable include paths.
			$path = DISCUSS_TABLES . '/' . strtolower($type) . '.php';

			// Import the class file.
			include_once($path);
		}

		return JTable::getInstance($type, $prefix, $config);
	}

	/**
	 * Retrieves the model
	 *
	 * @since	4.0
	 * @access	public
	 */
	public static function model($name)
	{
		static $models = array();

		$key = $name;

		if (!isset($models[$key])) {

			$file = strtolower($name) . '.php';

			$path = ED_MODELS . '/' . $file;

			// Include main model file
			require_once(ED_MODELS . '/model.php');

			if (!JFile::exists($path)) {
				throw ED::exception(JText::sprintf('Requested model %1$s is not found.', $file), ED_MSG_ERROR);
			}

			$className = 'EasyDiscussModel' . ucfirst($name);

			if (!class_exists($className)) {
				require_once($path);
			}

			$models[$key] = new $className();
		}

		return $models[$key];
	}

	/**
	 * Simple way to minify css codes
	 *
	 * @since	4.2.0
	 * @access	public
	 */
	public static function minifyCSS($css)
	{
		$css = preg_replace('!/\*[^*]*\*+([^/][^*]*\*+)*/!', '', $css);
		$css = str_replace(': ', ':', $css);
		$css = str_replace(array("\r\n", "\r", "\n", "\t", '  ', '    ', '    '), '', $css);

		return $css;
	}

	/**
	 * Retrieves the pagination object
	 *
	 * @since	4.0
	 * @access	public
	 */
	public static function getPagination($total, $limitstart, $limit, $prefix = '')
	{
		static $instances;

		if (!isset($instances)) {
			$instances = array();
		}

		$signature = serialize(array($total, $limitstart, $limit, $prefix));

		if (empty($instances[$signature])) {
			$pagination = ED::pagination($total, $limitstart, $limit, $prefix);

			$instances[$signature] = $pagination;
		}

		return $instances[$signature];
	}

	/**
	 * Retrieve @JUser object based on the given email address.
	 *
	 * @access	public
	 **/
	public static function getUserByEmail( $email )
	{
		$email	= strtolower( $email );

		$db		= ED::db();

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

	public static function getJoomlaUserGroups($cid = '', $excludeIds = array())
	{
		$db = ED::db();

		if (ED::getJoomlaVersion() >= '1.6') {
			$query = 'SELECT a.id, a.title AS `name`, COUNT(DISTINCT b.id) AS level';
			$query .= ' , GROUP_CONCAT(b.id SEPARATOR \',\') AS parents';
			$query .= ' FROM #__usergroups AS a';
			$query .= ' LEFT JOIN `#__usergroups` AS b ON a.lft > b.lft AND a.rgt < b.rgt';
		} else {
			$query	= 'SELECT `id`, `name`, 0 as `level` FROM ' . $db->nameQuote('#__core_acl_aro_groups') . ' a ';
		}

		// Condition
		$where  = array();

		// We need to filter out the ROOT and USER dummy records.
		if (ED::getJoomlaVersion() < '1.6') {
			$where[] = '(a.`id` > 17 AND a.`id` < 26)';
		}

		if (!empty($cid)) {
			$where[] = ' a.`id` = ' . $db->quote($cid);
		}

		if (!empty($excludeIds)) {
			$where[] = 'a.`id` NOT IN (' .implode(',', $db->quote($excludeIds)) . ')';
		}

		$where = (count($where) ? ' WHERE ' .implode(' AND ', $where) : '' );

		$query  .= $where;

		// Grouping and ordering
		if (ED::getJoomlaVersion() >= '1.6') {
			$query .= ' GROUP BY a.id';
			$query .= ' ORDER BY a.lft ASC';
		} else {
			$query .= ' ORDER BY a.id';
		}

		$db->setQuery($query);
		$result = $db->loadObjectList();

		if (ED::getJoomlaVersion() < '1.6') {
			$guest = new stdClass();
			$guest->id = '0';
			$guest->name = 'Public';
			$guest->level = '0';

			array_unshift( $result, $guest );
		}

		return $result;
	}

	public static function getUnsubscribeLink($subdata, $external = false, $html = false)
	{
		$unsubdata	= base64_encode("type=".$subdata->type."\r\nsid=".$subdata->id."\r\nuid=".$subdata->userid."\r\ntoken=".md5($subdata->id.$subdata->created));

		$link = EDR::getRoutedURL('index.php?option=com_easydiscuss&controller=subscription&task=unsubscribe&data='.$unsubdata, false, $external);

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
	 * Ensures that the user is logged in
	 *
	 * @since	4.0
	 * @access	public
	 */
	public static function requireLogin()
	{
		$app = JFactory::getApplication();
		$user = JFactory::getUser();

		if ($user->guest) {

			$config = ED::config();
			$provider = $config->get('main_login_provider');

			// Set the callback so it can be use later.
			$currentUrl = EDR::current();

			// default login page
			$url = EDR::_('view=login', false);

			// this one for default login session
			ED::setCallback($currentUrl);

			// if the login provider set to other extension
			if ($provider == 'easysocial' && ED::easysocial()->exists()) {

				$returnURL = '?return=' . base64_encode($currentUrl);	
				
				$url = ESR::login(array(), false) . $returnURL;
			}

			return $app->redirect($url);
		}
	}

	/**
	 * Give a proper redirection when the user does not have the permission to view the item.
	 *
	 * @since	4.0
	 * @access	public
	 */
	public static function getErrorRedirection($message = null)
	{
		$config = ED::config();
		$user = JFactory::getUser();

		if (!$message) {
			$message = JText::_('COM_EASYDISCUSS_SYSTEM_INSUFFICIENT_PERMISSIONS');
		}

		$redirection = $config->get('system_error_redirection', true);

		if ($redirection) {
			// If user haven't logged in, redirect them to login page.
			ED::requireLogin();

			// If it reached here means the user is already logged in.
			ED::setMessage($message, ED_MSG_ERROR);

			return self::redirect(EDR::_('view=index'), false);
		}

		throw ED::exception($message, ED_MSG_ERROR);
	}

	/**
	 * Method to retrieve a EasyBlogUser object
	 *
	 * @since	1.0
	 * @access	public
	 */
	public static function user($ids = null, $debug = false)
	{
		require_once(__DIR__ . '/user/user.php');

		return EasyDiscussUser::factory($ids, $debug);
	}

	/**
	 * Retrieve the html block for who's viewing this page.
	 *
	 * @access	public
	 * @param	string	$url
	 */
	public static function getWhosOnline($uri = '', $useCache = true)
	{
		$config = ED::config();
		$enabled = $config->get('main_viewingpage');

		if (!$enabled) {
			return;
		}

		// Default hash
		$hash = md5(EDFactory::getURI());

		if (!empty($uri)) {
			$hash = md5($uri);
		}

		$model = ED::model('Users');
		$users = $model->getPageViewers($hash);

		if (!$users) {
			return;
		}

		$theme = ED::themes();
		$theme->set('users', $users);
		$theme->set('useCache', $useCache);

		return $theme->output('site/post/default.viewers');
	}

	/* 
	 * Determine which limit should ED used.
	 * -1 - Use Joomla settings
	 * -2 - Use EasyDiscuss settings
	 *
	*/
	public static function getListLimit()
	{
		$default = ED::jconfig()->get('list_limit');

		if (ED::isFromAdmin()) {
			return $default;
		}

		$app = JFactory::getApplication();
		$menus = $app->getMenu();
		$menu = $menus->getActive();

		// By default ED should use default ED setting
		$limit = -2;

		if (is_object($menu)) {
			$params = ED::getRegistry($menu->getParams());
			$limit = $params->get('limit', '-2');
		}

		if ($limit == '-2') {
			// Use default configurations.
			$config = ED::config();
			$limit = $config->get('layout_list_limit', '-2');
		}

		// Revert to joomla's pagination if configured to inherit from Joomla
		if ($limit == '0' || $limit == '-1' || $limit == '-2') {
			$limit = $default;
		}

		return $limit;
	}

	public static function getRegistrationLink()
	{
		$config	= ED::config();

		$default	= JRoute::_( 'index.php?option=com_user&view=register' );
		if( ED::getJoomlaVersion() >= '1.6' )
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

				if (ED::easysocial()->exists()) {
					$link = FRoute::registration();
				} else {
					$link = $default;
				}

				break;

			case 'jomsocial':
				$link	= JRoute::_( 'index.php?option=com_community&view=register' );
				$file 	= JPATH_ROOT . '/components/com_community/libraries/core.php';

				if (JFile::exists($file)) {
					require_once( $file );
					$link 	= CRoute::_( 'index.php?option=com_community&view=register' );
				}
			break;
		}

		return $link;
	}

	public static function getLoginForm($text, $return)
	{
		$config = ED::config();

		$title = JText::_($text . '_TITLE');
		$info = JText::_($text . '_INFO');

		$usernameField = 'COM_EASYDISCUSS_USERNAME';

		if (ED::easysocial()->exists() && $config->get('main_login_provider') == 'easysocial') {
			$usernameField = ED::easysocial()->getUsernameField();
		}

		$theme = ED::themes();
		$theme->set('title', $title);
		$theme->set('info', $info);
		$theme->set('usernameField', $usernameField);
		$theme->set('return', $return);

		return $theme->output('site/login/form');
	}

	public static function getEditProfileLink()
	{
		return ED::user()->getEditProfileLink();
	}

	/**
	 * Generate reset password link
	 *
	 * @since	4.0
	 * @access	public
	 */
	public static function getResetPasswordLink()
	{
		$config = ED::config();

		$default = JRoute::_('index.php?option=com_user&view=reset');

		if (ED::getJoomlaVersion() >= '1.6') {
			$default = JRoute::_('index.php?option=com_users&view=reset');
		}

		switch ($config->get('main_login_provider')) {
			case 'easysocial':

				if (ED::easysocial()->exists()) {
					$link = ESR::account(array('layout' => 'forgetPassword'));
				} else {
					$link = $default;
				}
				break;
			case 'joomla':
			case 'cb':
			case 'jomsocial':
			default:
				$link = $default;
				break;
		}

		return $link;
	}

	/**
	 * Shorten a given number into its format accordingly
	 *
	 * @since	5.0.0
	 * @access	public
	 */
	public static function formatNumbers($n, $precision = 1)
	{
		if ($n < 900) {
			// 0 - 900
			$n_format = number_format($n, $precision);
			$suffix = '';
		} else if ($n < 900000) {
			// 0.9k-850k
			$n_format = number_format($n / 1000, $precision);
			$suffix = 'K';
		} else if ($n < 900000000) {
			// 0.9m-850m
			$n_format = number_format($n / 1000000, $precision);
			$suffix = 'M';
		} else if ($n < 900000000000) {
			// 0.9b-850b
			$n_format = number_format($n / 1000000000, $precision);
			$suffix = 'B';
		} else {
			// 0.9t+
			$n_format = number_format($n / 1000000000000, $precision);
			$suffix = 'T';
		}

	  // Remove unecessary zeroes after decimal. "1.0" -> "1"; "1.00" -> "1"
	  // Intentionally does not affect partials, eg "1.50" -> "1.50"
		if ( $precision > 0 ) {
			$dotzero = '.' . str_repeat( '0', $precision );
			$n_format = str_replace( $dotzero, '', $n_format );
		}

		return $n_format . $suffix;
	}


	/**
	 * Generate forgot username link
	 *
	 * @since	4.0
	 * @access	public
	 */
	public static function getRemindUsernameLink()
	{
		$config = ED::config();

		$default = JRoute::_('index.php?option=com_user&view=remind');

		if (ED::getJoomlaVersion() >= '1.6') {
			$default = JRoute::_('index.php?option=com_users&view=remind');
		}


		switch ($config->get('main_login_provider'))
		{
			case 'easysocial':

				if (ED::easysocial()->exists()) {
					$link = ESR::account(array('layout' => 'forgetUsername'));
				} else {
					$link = $default;
				}

				break;
			case 'joomla':
			case 'cb':
			case 'jomsocial':
			default:
				$link	= $default;
				break;
		}

		return $link;
	}

	public static function getDefaultRepliesSorting()
	{
		$config = ED::config();
		$defaultFilter = $config->get('layout_replies_sorting');

		if ($defaultFilter == 'voted' || $defaultFilter == 'likes') {
			$defaultFilter = 'oldest';
		}

		return $defaultFilter;
	}

	/**
	 * Allows caller to set the page title.
	 *
	 * @since	4.0
	 * @access	public
	 */
	public static function setPageTitle($text = '', $pagination = null, $options = array())
	{
		$originalText = JText::_($text);
		$text = JText::_($text);

		$doc = JFactory::getDocument();
		$app = JFactory::getApplication();
		$itemid = $app->input->get('Itemid', '');

		$menu = JFactory::getApplication()->getMenu();
		$item = $menu->getItem($itemid);

		if (is_object($item)) {

			$params = $item->getParams();

			if (!$params instanceof JRegistry) {
				$params = new JRegistry($item->getParams());
			}

			$customPageTitle = $params->get('page_title', '');

			if ($customPageTitle) {
				$text = $customPageTitle;
			}

			// if that is menu ask item with category id
			if (isset($item->query['view']) && $item->query['view'] == 'ask' && (isset($options['category']) && $options['category'])) {
				$text = $customPageTitle;

			} elseif (isset($item->query['view']) && $item->query['view'] != 'ask' && (isset($options['category']) && $options['category'])) {
				$text = $originalText;
			}
		}

		// Prepare Joomla's site title if necessary.
		$jConfig = ED::jConfig();
		$addTitle = $jConfig->get('sitename_pagetitles');

		// Only add Joomla's site title if it was configured to.
		if ($addTitle) {

			$siteTitle = $jConfig->get('sitename');

			// append the site name before the page title
			if ($addTitle == 1) {
				$text = JText::sprintf('COM_ED_PAGE_TITLE', $siteTitle, $text);
			}

			// append the site name after the page title
			if ($addTitle == 2) {
				$text = JText::sprintf('COM_ED_PAGE_TITLE', $text, $siteTitle);
			}
		}

		if ($pagination) {
			$paginationNumber = $pagination->getPageNumber();

			if ($paginationNumber > 1) {
				$paginationText = JText::sprintf('COM_EASYDISCUSS_PAGINATION_TEXT', $paginationNumber);
				$text = JText::sprintf('COM_ED_PAGE_TITLE', $text, $paginationText);
			}
		}

		$doc->setTitle($text);
	}

	/**
	 * Allows caller to set the meta data
	 *
	 * @since	5.0
	 * @access	public
	 */
	public static function setMeta($id = null, $type = null, $defaultDesc = null, $defaultKeyword = null)
	{
		$config	= ED::config();
		$jConfig = ED::jconfig();
		$document = JFactory::getDocument();

		$menu = JFactory::getApplication()->getMenu();
		$item = $menu->getActive();

		$result	= new stdClass();
		$result->description = '';
		$result->keywords = '';

		$joomlaDesc	= $jConfig->get('MetaDesc');
		$joomlaRobots = $jConfig->get('robots');

		// Check for the single post
		if ($type == ED_META_TYPE_POST && $id) {

			$post = ED::post($id);

			$postTitle = $post->getTitle();
			$pageTitle = htmlspecialchars_decode($postTitle, ENT_QUOTES);

			$pageContent = strip_tags($post->preview);
			$pageContent = EDJString::substr($pageContent, 0, 160);

			$pageDescription = preg_replace('/\s+/', ' ', $pageContent);
			
			$result->keywords = $pageTitle;
			$result->description =  $pageDescription;

			// Set page title.
			ED::setPageTitle($pageTitle);
		}

		// check for the forum category
		if ($type == ED_META_TYPE_FORUM_CATEGORY && $id) {

			$activeCategory = ED::category($id);

			if (!$result->keywords && $activeCategory->title) {
				$result->keywords = $activeCategory->title;
			}

			if (!$result->description && $activeCategory->description) {
				$result->description = strip_tags($activeCategory->description);
			}
		}

		// check for the category
		if ($type == ED_META_TYPE_CATEGORY && $id) {

			$activeCategory = ED::category($id);

			if (!$result->keywords && $activeCategory->title) {
				$result->keywords = $activeCategory->title;
			}

			if (!$result->description && $activeCategory->description) {
				$result->description = strip_tags($activeCategory->description);
			}
		}

		// check for the tag and user profile
		if ($type == ED_META_TYPE_TAG || $type == ED_META_TYPE_PROFILE || $type == ED_META_TYPE_BADGES) {

			if ($defaultDesc) {
				$result->description = strip_tags($defaultDesc);
			}
		}

		if (is_object($item) && !$result->keywords && !$result->description) {
			$params	= $item->getParams();

			if (!$params instanceof JRegistry) {
				$params = ED::getRegistry($item->getParams());
			}

			$description = $params->get('menu-meta_description' , '');
			$keywords = $params->get('menu-meta_keywords' , '');
			$robots = $params->get('robots');

			if (!empty($description)) {
				$result->description = $description;
			}

			if (!empty($keywords)) {
				$result->keywords = $keywords;
			}

			if (!empty($robots)) {
				$result->robots	= $robots;
			}
		}

		// if still dont have that meta description content then get it from Jooma global configuration
		if (empty($result->description)) {
			$result->description = $joomlaDesc;
		}

		if (!empty($result->keywords)) {
			$document->setMetadata('keywords', $result->keywords);
		}

		if (!empty($result->description)) {
			$document->setMetadata('description', $result->description);
		}

		if (!empty($result->robots)) {
			$document->setMetadata('robots', $result->robots);
		} else {
			$document->setMetadata('robots', $joomlaRobots);
		}
	}

	/**
	 * Loads a library from EasyDiscuss libraries
	 *
	 * @since	5.0.0
	 * @access	public
	 */
	public static function load($library)
	{
		$file = __DIR__ . '/' . strtolower($library) . '/' . strtolower($library) . '.php';

		require_once($file);
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

	/**
	 * Determines if the user is a moderator of the forum or a given category
	 *
	 * @since	5.0.0
	 * @access	public
	 */
	public static function isModerator($categoryId = null, $userId = null)
	{
		static $cache = [];

		if (is_null($userId)) {
			$userId = JFactory::getUser()->id;
		}

		$key = $categoryId . $userId;

		if (!isset($cache[$key])) {
			$cache[$key] = ED::moderator()->isModerator($categoryId, $userId);	
		}

		return $cache[$key];
	}

	public static function getUserGroupId(JUser $user, $recursive = true)
	{
		$groups = JAccess::getGroupsByUser($user->id, $recursive);

		return $groups;
	}

	/**
	 * Method determines if the content needs to be parsed through any parser or not.
	 *
	 * @since	3.0
	 * @access	public
	 */
	public static function parseContent( $content, $forceBBCode=false )
	{
		$config = ED::config();

		$content = ED::string()->escape($content);

		// Pass it to bbcode parser.
		$content = ED::parser()->bbcode( $content );
		$content = nl2br($content);

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
		$path = JPATH_ADMINISTRATOR . '/components/com_easydiscuss/includes/events/events.php';
		include_once($path);

		EasyDiscussEvents::importPlugin($type);

		$args = array( 'post' , &$data );

		$returnValue = call_user_func_array( 'EasyDiscussEvents::' . $eventName , $args );

		if ($hasReturnValue) {
			return trim( implode( "\n" , $returnValue ) );
		}

		return;
	}

	/**
	 * Renders a module in the component
	 *
	 * @since	4.0
	 * @access	public
	 */
	public static function renderModule($position, $attributes = array(), $content = null)
	{
		static $cache = [];

		if (!isset($cache[$position])) {
			$cache[$position] = JModuleHelper::getModules($position);
		}

		jimport('joomla.application.module.helper');

		$modules = $cache[$position];
		$buffer = '';

		$doc = JFactory::getDocument();
		$renderer = $doc->loadRenderer('module');
		
		if ($modules) {
			foreach ($modules as $module) {
				// Get the module output
				$output = $renderer->render($module, $attributes, $content);

				$theme = ED::themes();
				$theme->set('position', $position);
				$theme->set('output', $output);

				$buffer .= $theme->output('site/widgets/module');
			}
		}

		return $buffer;
	}

	/**
	 * Render Joomla editor.
	 *
	 * @since   5.0.0
	 * @access  public
	 */
	public static function getEditor($editorType = null)
	{
		$editor = EDCompat::getEditor($editorType);

		return $editor;
	}

	/**
	 * Simple implementation to extract keywords from a string
	 *
	 * @since	5.0.0
	 * @access	public
	 */
	public static function extractKeyWords($string)
	{
		mb_internal_encoding('UTF-8');

		$stopwords = array();
		$string = preg_replace('/[\pP]/u', '', trim(preg_replace('/\s\s+/iu', '', mb_strtolower($string))));
		$matchWords = array_filter(explode(' ',$string), function ($item) use ($stopwords) { return !($item == '' || in_array($item, $stopwords) || mb_strlen($item) <= 2 || is_numeric($item));});
		$wordCountArr = array_count_values($matchWords);

		arsort($wordCountArr);
		return array_keys(array_slice($wordCountArr, 0, 10));
	}

	public static function getEditorType( $type = '' )
	{
		// Cater for #__discuss_posts column content_type
		$config = ED::config();

		if ($config->get('layout_editor') == 'bbcode') {
			return 'bbcode';
		} else {
			return 'html';
		}
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
		$config = ED::config();

		// @ 4.0 we need to check if this post has the 'preview' or not. if yes, we just use it. if not, lets format it.
		if ($post->preview) {

			// Apply word censorship on the content
			$content = ED::badwords()->filter($post->preview);

			return $content;
		}

		// Determine the current editor
		$editor = $config->get('layout_editor', 'bbcode');

		// If the post is bbcode source and the current editor is bbcode
		if (($post->content_type == 'bbcode' || is_null($post->content_type)) && $editor == 'bbcode') {

			$content = $post->content;

			// Allow syntax highlighter even on html codes.
			$content = ED::parser()->replaceCodes($content);

			$content = ED::parser()->bbcode($content);

			// Since this is a bbcode content and source, we want to replace \n with <br /> tags.
			$content = nl2br($content);
		}

		// If the admin decides to switch from bbcode to wysiwyg editor, we need to format it back
		if( $post->content_type == 'bbcode' && $editor != 'bbcode' )
		{
			$content 	= $post->content;

			//strip this kind of tag -> &nbsp; &amp;
			$content = strip_tags(html_entity_decode($content));

			// Since the original content is bbcode, we don't really need to do any replacements.
			// Just feed it in through bbcode formatter.
			$content	= ED::parser()->bbcode( $content );
		}

		// If the admin decides to switch from wysiwyg to bbcode, we need to fix the content here.
		if( $post->content_type != 'bbcode' && !is_null($post->content_type) && $editor == 'bbcode' )
		{
			$content	= $post->content;

			// Switch html back to bbcode
			$content 	= ED::parser()->html2bbcode( $content );

			// Update the quote messages
			$content 	= ED::parser()->quoteBbcode( $content );
		}

		// If the content is from wysiwyg and editor is also wysiwyg, we only do specific formatting.
		if( $post->content_type != 'bbcode' && $editor != 'bbcode' )
		{
			$content 	= $post->content;

			// Allow syntax highlighter even on html codes.
			$content 	= ED::parser()->replaceCodes( $content );
		}

		// Apply word censorship on the content
		$content = ED::badwords()->filter($content);

		return $content;
	}

	/**
	 * cache for post related items.
	 *
	 * @since	4.0
	 * @access	public
	 */
	public static function cache()
	{
		static $cache = null;

		if (!$cache) {
			require_once(__DIR__ . '/cache/cache.php');

			$cache = new EasyDiscussCache();
		}

		return $cache;
	}

	/**
	 * Clears the cache in the CMS
	 *
	 * @since   5.0.0
	 * @access  public
	 */
	public static function clearCache()
	{
		$arguments = func_get_args();

		$cache = JFactory::getCache();

		foreach ($arguments as $argument) {
			$cache->clean($argument);
		}

		return true;
	}

	/**
	 * Request library
	 *
	 * @since	5.0.0
	 * @access	public
	 */
	public static function request()
	{
		static $lib = null;

		if (is_null($lib)) {
			ED::load('request');

			$lib = new EasyDiscussRequest();
		}

		return $lib;
	}

	/**
	 * Request library
	 *
	 * @since	5.0.0
	 * @access	public
	 */
	public static function themes()
	{
		static $lib = null;

		if (is_null($lib)) {
			ED::load('themes');

			$lib = new EasyDiscussThemes();
		}

		return $lib;
	}

	/**
	 * cache for post related items.
	 *
	 * @since	4.0
	 * @access	public
	 */
	public static function rest()
	{
		static $rest = null;

		if (!$rest) {
			require_once(__DIR__ . '/rest/rest.php');

			$rest = new EasyDiscussRest();
		}

		return $rest;
	}


	/**
	 * include akisment class file
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public static function akismet()
	{
		if (! class_exists('Akismet')) {
			require_once(__DIR__ . '/akismet/akismet.php');
		}
	}

	/**
	 * Ensures that the token is valid
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public static function checkToken()
	{
		JSession::checkToken('request') or die('Invalid Token');
	}

	// For displaying on frontend
	public static function bbcodeHtmlSwitcher( $post = '', $type = '', $isEditing = false )
	{
		$config = ED::config();

		if( $type == 'signature' || $type == 'description' ) {
			$temp = $post;
			$post = new stdClass();
			$post->content = $temp;
			$post->content_type = 'bbcode';
			$editor = 'bbcode';
		} else {
			$editor = $config->get( 'layout_editor' );
		}

		if ($editor != 'bbcode') {
			$editor = 'html';
		}

		if ($post->content_type == 'bbcode') {
			if ($editor == 'bbcode') {

				$content = $post->content;

				//If content_type is bbcode and editor is bbcode
				if (! $isEditing) {
					$content = ED::parser()->bbcode($content);
					$content = ED::parser()->removeBrTag($content);
				}
			} else {
				//If content_type is bbcode and editor is html
				// Need content raw to work
				$content = $post->post->content;
				$content = ED::parser()->bbcode($content);
				$content = ED::parser()->removeBrTag($content);
			}
		} else {
			// content_type is html

			if ($editor == 'bbcode') {

				$content = $post->content;

				//If content_type is html and editor is bbcode
				if ($isEditing) {
					$content = ED::parser()->quoteBbcode($content);
					$content = ED::parser()->smiley2bbcode($content); // we need to parse smiley 1st before we parse htmltobbcode.
					$content = ED::parser()->html2bbcode($content);
				} else {

					//Quote all bbcode here
					$content = ED::parser()->quoteBbcode($content);
				}
			} else {
				//If content_type is html and editor is html
				$content = $post->content;
			}
		}

		// Apply censorship
		$content = ED::badwords()->filter($content);

		return $content;
	}

	/**
	 * Redirect to login provider link if those page required to login first.
	 *
	 * @since	4.0.19
	 * @access	public
	 */
	public static function getLoginLink($returnURL = '')
	{
		$config = ED::config();

		if (!empty($returnURL)) {
			$returnURL = '&return=' . $returnURL;
		}

		$default = EDR::_('index.php?option=com_users&view=login' . $returnURL, false);

		// Default link
		$link = $default;
		$loginProvider = $config->get('main_login_provider');

		if ($loginProvider == 'easysocial') {
			$easysocial = ED::easysocial();

			if ($easysocial->exists()) {

				// We need to decode back the return url since easysocial only accept full url as return url
				$link = ESR::login(array('return' => $returnURL));
			}
		}

		if ($loginProvider == 'cb') {
			$link = JRoute::_('index.php?option=com_comprofiler&task=login' . $returnURL);
		}

		if ($loginProvider == 'easyblog') {
			$link = JRoute::_('index.php?option=com_easyblog&view=login' . $returnURL);
		}

		return $link;
	}

	public static function getPostStatusAndTypes( $posts = null)
	{
		if (empty($posts)) {
			return;
		}

		foreach ($posts as $post) {
			$user = ED::user($post->getOwner()->id);

			$post->badges = $user->getBadges();

			// Translate post status from integer to string
			switch($post->post_status) {
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
			$modelPostTypes = ED::model('Posttypes');

			// Get each post's post status title
			$title = $modelPostTypes->getTitle($alias);
			$post->post_type = $title;

			// Get each post's post status suffix
			$suffix = $modelPostTypes->getSuffix($alias);
			$post->suffix = $suffix;
		}

		return $posts;
	}

	/**
	 * Determines if the user falls under the moderation threshold
	 *
	 * @since	4.0
	 * @access	public
	 */
	public static function isModerateThreshold($userId, $isQuestion)
	{
		$user = ED::user($userId);

		return $user->moderateUsersPost($isQuestion);
	}

	/**
	 * Backwards compatibility purpose
	 *
	 * @since	4.0
	 * @access	public
	 */
	public static function __callStatic($method, $args)
	{
		// Here, we are under the assumption, the library exists
		$file = __DIR__ . '/' . strtolower($method) . '/' . strtolower($method) . '.php';

		require_once($file);

		$class = 'EasyDiscuss' . ucfirst($method);

		if (count($args) == 1) {
			$args = $args[0];
		}

		if (!$args) {
			$args = null;
		}

		$obj = new $class($args);

		return $obj;
	}

	/**
	 * Gets the current timezone of the site
	 *
	 * @since	4.0
	 * @access	public
	 */
	public static function getTimeZone($userId = null)
	{
		static $cache = [];

		if (!isset($cache[$userId])) {
			$user = JFactory::getUser($userId);
			
			$jconfig = ED::jconfig();
			$timezone = $jconfig->get('offset');

			if ($user->id) {
				$timezone = $user->getParam('timezone', $timezone);
			}

			$cache[$userId] = $timezone;
		}

		return $cache[$userId];
	}

	/**
	 * Gets the current timezone of the site
	 *
	 * @since	4.0
	 * @access	public
	 */
	public static function getTimeZoneOffset()
	{
		$date = ED::date();

		$timezone = ED::getTimeZone();
		$timezone = new DateTimeZone($timezone);

		$date->setTimezone($timezone);

		$offset = $date->getOffsetFromGmt(true);

		return $offset;
	}


	/**
	 * Renders the DiscussProfile table
	 *
	 * @since	4.0
	 * @access	public
	 */
	public static function profile($id = null)
	{
		static $cache = array();

		if (!isset($cache[$id])) {
			$cache[$id] = ED::user($id);
		}

		return $cache[$id];
	}

	public static function validateUserType($usertype)
	{
		$config = ED::config();
		$acl = ED::acl();

		switch($usertype)
		{
			case 'guest':
				$enable = $acl->allowed('add_reply', 0);
				break;
			case 'twitter':
				$enable = $config->get('integration_twitter_enable');
				break;
			case 'facebook':
				$enable = $config->get('integration_facebook_enable1');
				break;
			case 'linkedin':
				$enable = $config->get('integration_linkedin_enable1');
				break;
			default:
				$enable = false;
		}

		return $enable;
	}

	/**
	 * Retrieves the default avatar
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public static function getDefaultAvatar()
	{
		$uri = rtrim(JURI::root(), '/');
		$file = '/media/com_easydiscuss/images/default_avatar.png';

		// @TODO: Allow overrides

		$uri = $uri . $file;

		return $uri;
	}

	public static function getThemeObject($name)
	{
		jimport('joomla.filesystem.file');
		jimport('joomla.filesystem.folder');

		$file = DISCUSS_THEMES . '/' . $name . '/config.xml';
		$exists = JFile::exists($file);

		if (!$exists) {
			return false;
		}

		$parser = self::getXML($file);

		$obj = new stdClass();
		$obj->element = $name;
		$obj->name = $name;
		$obj->path = $file;
		$obj->writable = is_writable($file);
		$obj->created = JText::_('Unknown');
		$obj->updated = JText::_('Unknown');
		$obj->author = JText::_('Unknown');
		$obj->version = JText::_('Unknown');
		$obj->desc = JText::_('Unknown');

		if (ED::isJoomla30()) {

			$childrens = $parser->children();

			foreach ($childrens as $key => $value) {
				if ($key == 'description') {
					$key = 'desc';
				}

				$obj->$key = (string) $value;
			}

			$obj->path = $file;
		} else {

			$contents = file_get_contents($file);

			$parser = JFactory::getXMLParser('Simple');
			$parser->loadString($contents);

			$created = $parser->document->getElementByPath('created');
			if ($created) {
				$obj->created = $created->data();
			}

			$updated = $parser->document->getElementByPath('updated');
			if ($updated) {
				$obj->updated = $updated->data();
			}

			$author = $parser->document->getElementByPath('author');
			if ($author) {
				$obj->author = $author->data();
			}

			$version = $parser->document->getElementByPath('version');
			if ($version) {
				$obj->version = $version->data();
			}

			$description = $parser->document->getElementByPath('description');
			if ($description)
			{
				$obj->desc = $description->data();
			}

			$obj->path = $file;
		}

		return $obj;
	}

	/**
	 * Parses a csv file to array of data
	 *
	 * @since	4.0
	 * @param	string	Filename to parse
	 * @return	Array	Arrays of the data
	 */
	public static function parseCSV($file, $firstRowName = true, $firstColumnKey = true)
	{
		if (!JFile::exists($file)) {
			return array();
		}

		$handle = fopen($file, 'r');

		$line = 0;

		$columns = array();

		$data = array();

		while (($row = fgetcsv($handle)) !== false) {

			if ($firstRowName && $line === 0) {
				$columns = $row;
			} else {
				$tmp = array();

				if ($firstRowName) {
					foreach ($row as $i => $v) {
						$tmp[$columns[$i]] = $v;
					}
				} else {
					$tmp = $row;
				}

				if ($firstColumnKey) {
					if ($firstRowName) {
						$data[$tmp[$columns[0]]] = $tmp;
					} else {
						$data[$tmp[0]] = $tmp;
					}
				} else {
					$data[] = $tmp;
				}
			}

			$line++;
		}

		fclose($handle);

		return $data;
	}

	/**
	 * Includes a file given a particular namespace in POSIX format.
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string	$file		Eg: admin:/tables/table will include /administrator/components/com_easydiscuss/tables/table.php
	 * @return	boolean				True on success false otherwise
	 */
	public static function import( $namespace )
	{
		static $locations	= array();

		if( !isset( $locations[ $namespace ] ) )
		{
			// Explode the parts to know exactly what to lookup for
			$parts		= explode( ':' , $namespace );

			// Non POSIX standard.
			if( count( $parts ) <= 1 )
			{
				return false;
			}

			$base 		= $parts[ 0 ];

			switch( $base )
			{
				case 'admin':
					$basePath	= DISCUSS_ADMIN_ROOT;
				break;
				case 'site':
				default:
					$basePath	= DISCUSS_ROOT;
				break;
			}

			// Replace / with proper directory structure.
			$path 		= str_ireplace( '/' , DIRECTORY_SEPARATOR , $parts[ 1 ] );

			// Get the absolute path now.
			$path 		= rtrim($basePath, '/') . '/' . $path . '.php';

			// Include the file now.
			include_once( $path );

			$locations[ $namespace ]	= true;
		}

		return true;
	}

	/**
	 * Generates the query string for language selection.
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public static function getLanguageQuery($column = 'language')
	{
		$language = JFactory::getLanguage();
		$tag = $language->getTag();
		$query = '';

		$column = (!$column)? 'language' : $column;

		if (!empty($tag) && $tag != '*') {
			$db = ED::db();
			$query = ' (' . $db->qn($column) . '=' . $db->Quote($tag) . ' OR ' . $db->qn($column) . '=' . $db->Quote('') . ' OR ' . $db->qn($column) . '=' . $db->Quote('*') . ')';
		}

		return $query;
	}

	/**
	 * Generates the query string for language selection.
	 *
	 * @since	5.0.0
	 * @access	public
	 */
	public static function getKnownLanguages()
	{
		$language = EDJLanguage::getKnownLanguages();

		return $language;
	}

	/**
	 * Determine whether the site enable multilingual.
	 *
	 * @since	4.1.19
	 * @access	public
	 */
	public static function isSiteMultilingualEnabled()
	{
		// check if the languagefilter plugin enabled
		$pluginEnabled = JPluginHelper::isEnabled('system', 'languagefilter');

		return $pluginEnabled;
	}

	/**
	 * Sets some callback data into the current session
	 *
	 * @since	1.0
	 * @access	public
	 */
	public static function setCallback($data)
	{
		$session = JFactory::getSession();

		// Serialize the callback data.
		$data = serialize( $data );

		// Store the profile type id into the session.
		$session->set('easydiscuss.callback', $data, 'com_easydiscuss');
	}

	/**
	 * Retrieves stored callback data.
	 *
	 * @since	1.0
	 * @access	public
	 */
	public static function getCallback($default = '', $resetSession = true)
	{
		$session = JFactory::getSession();
		$data = $session->get('easydiscuss.callback', '', 'com_easydiscuss');

		$data = unserialize($data);

		// Clear off the session once it's been picked up.
		if ($resetSession) {
			$session->clear('easydiscuss.callback', 'com_easydiscuss');
		}

		if (!$data && $default) {
			return $default;
		}

		return $data;
	}

	/**
	 * Retrieves external conversation link
	 *
	 * @since   4.0
	 * @access  public
	 */
	public static function getConversationsRoute()
	{
		$config = ED::config();

		if (ED::easysocial()->exists() && $config->get('integration_easysocial_messaging')) {
			$link = ED::easysocial()->getConversationsRoute();
		}

		if (ED::jomsocial()->exists() && $config->get('integration_jomsocial_messaging')) {
			$link = ED::jomsocial()->getConversationsRoute();
		}

		return $link;
	}

	/**
	 * Get current joomla template
	 *
	 * @since 4.0
	 * @access public
	 */
	public static function getCurrentTemplate($client = 'site')
	{
		static $template = array();

		if (!isset($template[$client])) {
			$assets = ED::assets();
			$template[$client] = $assets->getJoomlaTemplate($client);
		}

		return $template[$client];
	}

	/**
	 * Detects if the folder exist based on the path given. If it doesn't exist, create it.
	 *
	 * @since   4.1
	 * @access  public
	 */
	public static function makeFolder($path, $createIndex = true)
	{
		jimport('joomla.filesystem.folder');

		// If folder exists, we don't need to do anything
		if (JFolder::exists($path)) {
			return true;
		}

		// Folder doesn't exist, let's try to create it.
		$state = JFolder::create($path);

		if ($state && $createIndex) {
			ED::createIndex($path);
			return true;
		}

		return false;
	}

	/**
	 * Generates a blank index.html file into a specific target location.
	 *
	 * @since   4.1
	 * @access  public
	 */
	public static function createIndex($targetLocation)
	{
		$targetLocation = $targetLocation . '/index.html';

		jimport('joomla.filesystem.file');

		$contents = "<html><body bgcolor=\"#FFFFFF\"></body></html>";

		return JFile::write($targetLocation, $contents);
	}

	/**
	 * Allows caller to pass in an array of data to normalize the data
	 *
	 * @since	5.0.0
	 * @access	public
	 */
	public static function normalize($data, $key, $default = null)
	{
		if (!$data) {
			return $default;
		}

		// $key cannot be an array
		if (is_array($key)) {
			$key = $key[0];
		}

		// Object datatype
		if (is_object($data) && isset($data->$key)) {
			return $data->$key;
		}

		// Array datatype
		if (is_array($data) && isset($data[$key])) {
			return $data[$key];
		}

		return $default;
	}

	/**
	 * Normalize directory separator
	 *
	 * @since   4.1.0
	 * @access  public
	 */
	public static function normalizeSeparator($path)
	{
		$path = str_ireplace(array( '\\' ,'/' ) , '/' , $path);
		return $path;
	}

	/**
	 * Generate link attributes
	 *
	 * @since   4.1
	 * @access  public
	 */
	public static function getLinkAttributes()
	{
		$config = ED::config();

		$relValue = $config->get('main_link_rel_nofollow') ? array('nofollow') : array();
		$targetBlank = '';

		if ($config->get('main_link_new_window')) {
			$relValue[] = 'noreferrer';
			$targetBlank = ' target="_blank"';
		}

		$relAttr = !empty($relValue) ? ' rel="' . implode(' ', $relValue) . '"' : '';

		return $targetBlank . $relAttr;
	}

	/**
	 * Retrieves the logo that should be used site wide
	 *
	 * @since   4.1.12
	 * @access  public
	 */
	public static function getLogo($type)
	{
		static $logo = [];

		if (!isset($logo[$type])) {
			$defaultJoomlaTemplate = self::getCurrentTemplate();

			// Set the logo for the generic email template
			$override = JPATH_ROOT . '/templates/' . $defaultJoomlaTemplate . '/html/com_easydiscuss/' . $type . '/logo.png';
			$logo[$type] = rtrim(JURI::root(), '/') . '/media/com_easydiscuss/images/' . $type . '/logo.png';

			if (JFile::exists($override)) {
				$logo[$type] = rtrim(JURI::root(), '/') . '/templates/' . $defaultJoomlaTemplate . '/html/com_easydiscuss/' . $type . '/logo.png?' . time();
			}
		}

		return $logo[$type];
	}

	/**
	 * Determine if custom logo is exists
	 *
	 * @since	5.0
	 * @access	public
	 */
	public static function hasOverrideLogo($type)
	{
		$path = JPATH_ROOT . self::getOverrideLogo($type);

		if (JFile::exists($path)) {
			return true;
		}

		return false;
	}

	/**
	 * Get override path for logo
	 *
	 * @since	5.0
	 * @access	public
	 */
	public static function getOverrideLogo($type)
	{
		// Get current template
		$defaultJoomlaTemplate = self::getCurrentTemplate();

		$path = '/templates/' . $defaultJoomlaTemplate . '/html/com_easydiscuss/' . $type . '/logo.png';

		return $path;
	}

	/**
	 * Converts an argument into an array.
	 *
	 * @since   4.1
	 * @access  public
	 */
	public static function makeArray($item, $delimeter = null)
	{
		// If this is already an array, we don't need to do anything here.
		if (is_array($item)) {
			return $item;
		}

		// Test if source is a SocialRegistry/JRegistry object
		if ($item instanceof EasyDiscussRegistry || $item instanceof JRegistry) {
			return $item->toArray();
		}

		// Test if source is an object.
		if (is_object($item)) {
			return EDArrayHelper::fromObject($item);
		}

		if (is_integer($item)) {
			return array($item);
		}

		// Test if source is a string.
		if (is_string($item)) {
			if ($item == '') {
				return array();
			}

			// Test for comma separated values.
			if (!is_null($delimeter) && stristr($item , $delimeter) !== false) {
				$data   = explode($delimeter, $item);
				return $data;
			}

			// Test for JSON array string
			$pattern = '#^\s*//.+$#m';
			$item = trim(preg_replace($pattern, '', $item));
			if ((substr($item, 0, 1) === '[' && substr($item, -1, 1) === ']')) {
				return FD::json()->decode($item);
			}

			// Test for JSON object string, but convert it into array
			if ((substr($item, 0, 1) === '{' && substr($item, -1, 1) === '}')) {
				$result = FD::json()->decode($item);

				return EDArrayHelper::fromObject($result);
			}

			return array( $item );
		}

		return false;
	}

	/**
	 * Generates a default placeholder image from the discussion post
	 *
	 * @since	4.1.15
	 * @access	public
	 */
	public static function getPlaceholderImage()
	{
		static $image = null;
	
		if (is_null($image)) {

			$app = JFactory::getApplication();

			$image = rtrim(JURI::root(), '/') . '/components/com_easydiscuss/themes/wireframe/images/placeholder-facebook.png';

			// Default post image if the post doesn't contain any image
			$override = JPATH_ROOT . '/templates/' . $app->getTemplate() . '/html/com_easydiscuss/images/placeholder-facebook.png';

			$exists = JFile::exists($override);

			if ($exists) {
				$image = rtrim(JURI::root(), '/') . '/templates/' . $app->getTemplate() . '/html/com_easydiscuss/images/placeholder-facebook.png';
			}
		}

		return $image;
	}

	/**
	 * Maps a limit value with a proper value.
	 *
	 * -2 is limit from EasyDiscuss
	 * -1 is limit from Joomla
	 *
	 * @since	5.0.0
	 * @access	public
	 */
	public static function getLimitValue($limit)
	{
		// Limit from EasyDiscuss
		if ($limit == '-2') {
			return (int) ED::getListLimit();
		}

		// Limit from Joomla
		if ($limit == '-1') {
			return (int) ED::jConfig()->get('list_limit');
		}

		return (int) $limit;
	}

	/**
	 * Retrieve the map location request URL
	 *
	 * @since	4.1.16
	 * @access	public
	 */
	public static function getMapRequestURL($post, $scale = false)
	{
		$config = ED::config();

		$scaleParameter = '';
		$mapType = $config->get('main_location_map_type');
		$zoom = $config->get('main_location_default_zoom', 15);
		$gMapKey = $config->get('main_location_gmaps_key');
		$mapLanguage = $config->get('main_location_language');
		$useStaticMap = $config->get('main_location_static');

		if (!$useStaticMap) {

			$mapUrl = "https://www.google.com/maps/embed/v1/place?key=" . $gMapKey . "&q=" . str_replace(' ', '%20', $post->address);
			$mapUrl .= '&center=' . $post->latitude . "," . $post->longitude;
			$mapUrl .= '&zoom=' . $zoom;
			$mapUrl .= '&language=' . $mapLanguage;

			// only two kind of map type is supported, roadmap and satellite.
			$nonStaticMapType = 'ROADMAP';

			if ($mapType == 'SATELLITE') {
				$nonStaticMapType = $mapType;
			}

			$mapUrl .= "&maptype=" . strtolower($nonStaticMapType);

			return $mapUrl;
		}

		// Render static map
		$requestURL = "https://maps.googleapis.com/maps/api/staticmap?center=";

		if ($scale) {
			$scaleParameter = "&scale=2";
		}

		$additionalParams = "&size=800x200" . $scaleParameter . "&sensor=true&markers=color:red|label:S|";

		$mapUrl = $requestURL . $post->latitude . "," . $post->longitude . "&maptype=" . strtolower($mapType) . "&zoom=" . $zoom . $additionalParams . $post->latitude . "," . $post->longitude . "&key=" . $gMapKey;

		if ($mapLanguage) {
			$mapUrl .= "&language=" . $mapLanguage;
		}

		return $mapUrl;
	}

	/**
	 * Add sync request.
	 *
	 * @since	4.1.18
	 * @access	public
	 */
	public static function addSyncRequest($command, $userId = 0, $total = 0)
	{
		$supportedCommands = array(DISCUSS_SYNC_THREAD_REPLY);

		if (!in_array($command, $supportedCommands)) {
			// not supported
			return false;
		}

		$config = ED::config();
		$now = ED::date();
		$userIds = array();
		$params = new JRegistry();

		// lets add into sync request table.
		$table = ED::table('SyncRequest');
		$table->load(array('command' => $command));

		if ($table && $table->id) {
			$params->loadString($table->params);
		}

		if ($userId) {
			// if user id is supplied, lets add into params.
			$userIds = $params->get('user_id', array());
			$userIds = ED::makeArray($userIds);

			$exists = false;
			if ($userIds && array_key_exists($userId, $userIds)) {
				$exists = true;
			}

			if (!$exists) {
				// add new user id.
				$obj = array('id' => $userId, 'total' => $total, 'current' => 0);
				$userIds[$userId] = $obj;
			}

			// now set the params
			$params->set('user_id', $userIds);
		}

		$table->params = $params->toString();
		$table->command = $command;
		$table->created = $table->id ? $table->created : $now->toSql();
		$table->total = count($userIds);
		$state = $table->store();

		return $state;
	}

	/**
	 * Loads up EasySocial library
	 *
	 * @since	5.0.0
	 * @access	public
	 */
	public static function easysocial()
	{
		static $easysocial = null;

		if (is_null($easysocial)) {
			ED::load('easysocial');

			$easysocial = new EasyDiscussEasySocial();
		}

		return $easysocial;
	}

	/**
	 * Loads up EasySocial library
	 *
	 * @since	5.0.0
	 * @access	public
	 */
	public static function jomsocial()
	{
		static $jomsocial = null;

		if (is_null($jomsocial)) {
			ED::load('jomsocial');

			$jomsocial = new EasyDiscussJomsocial();
		}

		return $jomsocial;
	}

	/**
	 * Creates a new instance of the exception library.
	 *
	 * @since	5.0.0
	 * @access	public
	 */
	public static function exception($message = '', $type = ED_MSG_ERROR, $previous = null)
	{
		require_once(dirname(__FILE__) . '/exception/exception.php');

		$exception = new EasyDiscussException($message, $type, $previous);

		return $exception;
	}

	/**
	 * Loads up Decoda library
	 *
	 * @since	5.0.0
	 * @access	public
	 */
	public static function decoda()
	{
		static $lib = null;

		if (is_null($lib)) {
			ED::load('decoda');

			$lib = new EasyDiscussDecoda();
		}

		return $lib;
	}

	/**
	 * Loads up the db
	 *
	 * @since	5.0.0
	 * @access	public
	 */
	public static function db()
	{
		static $db = null;

		if (is_null($db)) {
			ED::load('db');

			$db = new EasyDiscussDb();
		}

		return $db;
	}
	
	/**
	 * Creates a new instance of the GIPHY library.
	 *
	 * @since	5.0.0
	 * @access	public
	 */
	public static function giphy()
	{
		require_once(dirname(__FILE__) . '/giphy/giphy.php');

		$giphy = new EasyDiscussGiphy();

		return $giphy;
	}

	/**
	 * Given an array of values, run a database quote on each of the result
	 *
	 * @since	5.0.0
	 * @access	public
	 */
	public static function quoteArray($data)
	{
		if (!$data) {
			return array();
		}

		$result = [];
		$db = ED::db();

		// Ensure that the given parameter is an array
		if (!is_array($data)) {
			$data = [$data];
		}

		foreach ($data as $value) {
			$result[] = $db->Quote($value);
		}

		return $result;
	}

	/**
	 * Retrieve the site name from Joomla
	 *
	 * @since	5.0.0
	 * @access	public
	 */
	public static function getSiteName()
	{
		$jConfig = self::jConfig();
		$siteName = $jConfig->get('sitename');

		return $siteName;
	}

	/**
	 * Retrieve logo for schema
	 *
	 * @since   5.0.0
	 * @access  public
	 */
	public static function getSchemaLogo()
	{
		$absoluteUrl = self::getLogo('schema');

		// Retrieve the current domain
    	$domain = rtrim(JURI::root(), '/');
    
    	// Convert to use relative path check for the image size
    	$schemaLogoPath = str_replace($domain, '', $absoluteUrl);
    	$schemaLogoPath = JPATH_ROOT . $schemaLogoPath;
    
		$data = @getimagesize($schemaLogoPath);

		if (!$data) {
			return false;
		}

		$logo = array('@type' => 'ImageObject', 'url' => $absoluteUrl, 'width' => $data[0], 'height' => $data[1]);

		return json_encode($logo);
	}

	 /**
	  * Converts characters to HTML entities for Schema structure data
	  *
	  * @since	5.0.0
	  * @access	public
	  */
	 public static function normalizeSchema($schemaContent)
	 {
		// Converts characters to HTML entities
		$schemaContent = htmlentities($schemaContent, ENT_QUOTES);

		// Remove backslash symbol since this will caused invalid JSON data
		$schemaContent = stripslashes($schemaContent);

		return $schemaContent;
	 }

	/**
	 * Set the SEE header
	 *
	 * @since	5.0.0
	 * @access	public
	 */
	public static function setSSEHeader()
	{
		header("Cache-Control: no-cache");
		header("Content-Type: text/event-stream");
		header("X-Accel-Buffering: no");
	}

	/**
	 * Set a SSE response
	 *
	 * @since	5.0.0
	 * @access	public
	 */
	public static function responseSSE($event, $data)
	{
		ob_start();
?>
event: <?php echo $event;?>

<?php if (is_object($data) || is_array($data)) { ?>
data: <?php echo json_encode($data); ?>
<?php } else { ?>
data: <?php echo $data; ?>
<?php } ?>

<?php // Required to fulfill minimum buffer size on certain server. #4181 from ES ?>
buffer: <?php echo str_repeat(' ', 1024 * 64); ?>
<?php
		$contents = ob_get_contents();
		ob_end_clean();

		return $contents;
	}

	/**
	 * Redirects to a given link
	 *
	 * @since	5.0.0
	 * @access	public
	 */
	public static function redirect($link, $message = '', $class = '')
	{
		$app = JFactory::getApplication();

		if ($message) {
			$message = JText::_($message);
		}

		if (self::isJoomla4()) {
			if ($message) {
				$app->enqueueMessage($message, $class);
			}

			$app->redirect($link);
			return $app->close();
		}

		$app->redirect($link, $message, $class);
		return $app->close();
	}

	/**
	 * Ride on Joomla's User helper library to obtain two factor methods
	 *
	 * @since	5.0.0
	 * @access	public
	 */
	public static function getTwoFactorMethods()
	{
		JLoader::register('UsersHelper', JPATH_ADMINISTRATOR . '/components/com_users/helpers/users.php');

		$methods = UsersHelper::getTwoFactorMethods();

		return $methods;
	}


	/**
	 * Retrieve the otpConfig from Joomla users
	 *
	 * @since	5.0.0
	 * @access	public
	 */
	public static function getOtpConfig($userId = null)
	{
		$user = JFactory::getUser($userId);

		$model = ED::getJoomlaUserModel();

		return $model->getOtpConfig($user->id);
	}

	/**
	 * Loads the user model from Joomla
	 *
	 * @since	5.0.0
	 * @access	public
	 */
	public static function getJoomlaUserModel()
	{
		static $model = null;

		if (is_null($model)) {

			if (ED::isJoomla4()) {
				$app = JFactory::getApplication();
				$model = $app->bootComponent('com_users')->getMVCFactory()->createModel('User', 'Administrator', ['ignore_request' => true]);

				return $model;
			}

			JLoader::register('UsersModelUser', JPATH_ADMINISTRATOR . '/components/com_users/models/user.php');

			$model = new UsersModelUser;
		}

		return $model;
	}

	/**
	 * Prepares the HTML to render two factor forms on the page
	 *
	 * @since	5.0.0
	 * @access	public
	 */
	public static function getTwoFactorForms($otpConfig, $userId = null)
	{
		return EDCompat::getTwoFactorForms($otpConfig, $userId);
	}

	/**
	 * Retrieve two factor user's configuration
	 *
	 * @since	5.0.0
	 * @access	public
	 */
	public static function getTwoFactorConfig($twoFactorMethod)
	{
		return EDCompat::getTwoFactorConfig($twoFactorMethod);
	}
}

// Backwards compatibility
class DiscussHelper extends ED {}
