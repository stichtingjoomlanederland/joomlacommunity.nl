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

class KomentoDocumentHelper
{
	static $loaded;

	public static function loadHeaders()
	{
		if( !self::$loaded )
		{
			$url = KomentoDocumentHelper::getBaseUrl();

			$resourcePath = $url . '&tmpl=component&no_html=1&controller=foundry&task=getResource&kmtcomponent=' . JRequest::getCmd( 'option', '' );

			$document	= JFactory::getDocument();
			$config = Komento::getConfig();
			$konfig = Komento::getKonfig();
			$acl = Komento::getAcl();
			$guest = Komento::getProfile()->guest ? 1 : 0;

			if( $document->getType() != 'html' )
			{
				return true;
			}

			// only temporary to load development css
			// waiting chang to finalise reset.css and comments.css 
			// UPDATE: only load common.css for other themes than Wireframe.
			if ($config->get('layout_theme') != 'wireframe') {
				self::addTemplateCss( 'common.css' );
			}
			
			// self::addTemplateCss( 'comments.css' );

			// Load KomentoConfiguration class
			require_once(KOMENTO_CLASSES . DIRECTORY_SEPARATOR . 'configuration.php');

			// Get configuration instance
			$configuration = KomentoConfiguration::getInstance();

			// Attach configuration to headers
			$configuration->attach();

			if ($config->get('layout_inherit_kuro_css', 1) && $config->get('layout_theme') != 'wireframe') {
				$document->addStylesheet( JURI::root() . 'components/com_komento/themes/kuro/css/style.css' );
			}

			// support for RTL sites
			// forcertl = 1 for dev purposes
			$rtlPath = rtrim(JURI::root(), '/') . '/components/com_komento/themes';

			if (($document->direction == 'rtl' || JRequest::getInt('forcertl') == 1) ) {

				if ($config->get('layout_theme') != 'wireframe') {
					$rtlPath .= '/kuro/css/style-rtl.css';	
				} else {
					$rtlPath .= '/wireframe/css/style-rtl.css';	
				}
				
				$document->addStylesheet($rtlPath);
			}

			

			$document->addStylesheet(JURI::root() . 'media/foundry/3.1/styles/dialog/default.css');
			$document->addStylesheet(JURI::root() . 'media/foundry/3.1/styles/fancybox/default.css');

			self::load('style', 'css', 'themes');

			// load SH css if config is on
			if( $config->get( 'enable_syntax_highlighting' ) )
			{
				$shtheme = $config->get( 'syntaxhighlighter_theme', 'default' );
				self::load('syntaxhighlighter/' . $shtheme, 'css', 'assets');
			}

			self::$loaded		= true;
		}
		return self::$loaded;
	}

	/**
	 * Function to add js file, js script block and css file
	 * to HEAD section
	 */
	public static function load( $list, $type='js', $location='themes' )
	{
		$mainframe	= JFactory::getApplication();
		$document	= JFactory::getDocument();
		$config		= Komento::getConfig();
		$kApp		= Komento::loadApplication();

		// Always load mootools first so it will not conflict.
		// JHTML::_('behavior.mootools');

		$files		= explode( ',', $list );
		$dir		= JURI::root() . 'components/com_komento/assets';
		$pathdir	= KOMENTO_ASSETS;
		$theme		= $config->get( 'layout_theme' );
		$version	= str_ireplace( '.' , '' , Komento::komentoVersion() );

		if ( $location != 'assets' )
		{
			$dir	= JURI::root() . 'components/com_komento/themes/' . $theme;
			$pathdir = KOMENTO_THEMES . DIRECTORY_SEPARATOR . $theme;
		}

		foreach( $files as $file )
		{
			if ( $type == 'js' )
			{
				$file .= '.js?' . $version;
			}
			elseif ( $type == 'css' )
			{
				$file .= '.css';
			}

			$path = '';
			if( $location == 'themes' )
			{
				$checkOverride	= JPATH_ROOT . DIRECTORY_SEPARATOR . 'templates' . DIRECTORY_SEPARATOR . $mainframe->getTemplate() . DIRECTORY_SEPARATOR . 'html' . DIRECTORY_SEPARATOR . 'com_komento' . DIRECTORY_SEPARATOR . $type . DIRECTORY_SEPARATOR . $file;
				$checkComponent	= $kApp->getComponentThemePath() . DIRECTORY_SEPARATOR . $type . DIRECTORY_SEPARATOR . $file;
				$checkSelected	= KOMENTO_THEMES . DIRECTORY_SEPARATOR . $theme . DIRECTORY_SEPARATOR . $type . DIRECTORY_SEPARATOR . $file;
				$checkDefault	= KOMENTO_THEMES . DIRECTORY_SEPARATOR . 'kuro' . DIRECTORY_SEPARATOR . $type . DIRECTORY_SEPARATOR . $file;

				$overridePath	= JURI::root() . 'templates/' . $mainframe->getTemplate() . '/html/com_komento/' . $type . '/' . $file;
				$componentPath	= $kApp->getComponentThemeURI() . '/' . $type . '/' . $file;
				$selectedPath	= $dir . '/' . $type . '/' . $file;
				$defaultPath	= JURI::root() . 'components/com_komento/themes/kuro/' . $type . '/' . $file;

				// 1. Template overrides
				if( JFile::exists( $checkOverride ) )
				{
					$path = $overridePath;
					$pathdir = $checkOverride;
				}
				// 2. Selected themes
				elseif( JFile::exists( $checkSelected ) )
				{
					$path = $selectedPath;
					$pathdir = $checkSelected;
				}
				// 3. Default system theme
				else
				{
					$path = $defaultPath;
					$path = $checkDefault;
				}
			}
			else
			{
				$path = $dir . '/' . $type . '/' . $file;
				$pathdir = $pathdir . DIRECTORY_SEPARATOR . $type . DIRECTORY_SEPARATOR . $file;
			}

			if ( $type == 'js' )
			{
				$document->addScript( $path );
			}
			elseif ( $type == 'css' )
			{
				if( JFile::exists($pathdir) )
				{
					$document->addStylesheet( $path );
				}
			}
		}
	}

	/**
	 * Allows caller to detect specific css files from site's template
	 * and load it into the headers if necessary.
	 *
	 * @param	string $fileName
	 */
	public static function addTemplateCss( $fileName )
	{
		$document		= JFactory::getDocument();
		$document->addStyleSheet( rtrim(JURI::root(), '/') . '/components/com_komento/assets/css/' . $fileName );

		$mainframe		= JFactory::getApplication();
		$templatePath	= JPATH_ROOT . DIRECTORY_SEPARATOR . 'templates' . DIRECTORY_SEPARATOR . $mainframe->getTemplate() . DIRECTORY_SEPARATOR . 'html' . DIRECTORY_SEPARATOR . 'com_komento' . DIRECTORY_SEPARATOR . 'assets' . DIRECTORY_SEPARATOR . 'css' . DIRECTORY_SEPARATOR . $fileName;

		if( JFile::exists($templatePath) )
		{
			$document->addStyleSheet( rtrim(JURI::root(), '/') . '/templates/' . $mainframe->getTemplate() . '/html/com_komento/assets/css/' . $fileName );

			return true;
		}

		return false;
	}

	/*
	 * Method for broswer detection
	 */
	public static function getBrowserUserAgent()
	{
		$browser = new stdClass;

		// set to lower case to avoid errors, check to see if http_user_agent is set
		$navigator_user_agent = ( isset( $_SERVER['HTTP_USER_AGENT'] ) ) ? strtolower( $_SERVER['HTTP_USER_AGENT'] ) : '';

		// run through the main browser possibilities, assign them to the main $browser variable
		if (stristr($navigator_user_agent, "opera"))
		{
			$browser->userAgent = 'opera';
			$browser->dom = true;
		}
		elseif (stristr($navigator_user_agent, "msie 8"))
		{
			$browser->userAgent = 'msie8';
			$browser->dom = false;
		}
		elseif (stristr($navigator_user_agent, "msie 7"))
		{
			$browser->userAgent = 'msie7';
			$browser->dom = false;
		}
		elseif (stristr($navigator_user_agent, "msie 4"))
		{
			$browser->userAgent = 'msie4';
			$browser->dom = false;
		}
		elseif (stristr($navigator_user_agent, "msie"))
		{
			$browser->userAgent = 'msie';
			$browser->dom = true;
		}
		elseif ((stristr($navigator_user_agent, "konqueror")) || (stristr($navigator_user_agent, "safari")))
		{
			$browser->userAgent = 'safari';
			$browser->dom = true;
		}
		elseif (stristr($navigator_user_agent, "gecko"))
		{
			$browser->userAgent = 'mozilla';
			$browser->dom = true;
		}
		elseif (stristr($navigator_user_agent, "mozilla/4"))
		{
			$browser->userAgent = 'ns4';
			$browser->dom = false;
		}
		else
		{
			$browser->dom = false;
			$browser->userAgent = 'Unknown';
		}

		return $browser;
	}

	// Add canonical URL to satify Googlebot. Incase they think it's duplicated content.
	public static function addCanonicalURL( $extraFishes = array() )
	{
		if (empty( $extraFishes ))
		{
			return;
		}

		$juri		= JURI::getInstance();

		foreach( $extraFishes as $fish )
		{
			$juri->delVar( $fish );
		}

		$preferredURL	= $juri->toString();

		$document	= JFactory::getDocument();
		$document->addHeadLink( $preferredURL, 'canonical', 'rel');
	}

	public static function getBaseUrl()
	{
		static $url;

		if (isset($url)) return $url;

		if( Komento::joomlaVersion() >= '1.6' )
		{
			$uri 		= JFactory::getURI();
			$language	= $uri->getVar( 'lang' , 'none' );
			$app		= JFactory::getApplication();
			$jconfig	= JFactory::getConfig();
			$router		= $app->getRouter();
			$url		= rtrim( JURI::root() , '/' ) . '/index.php?option=com_komento&lang=' . $language;

			if( $router->getMode() == JROUTER_MODE_SEF && JPluginHelper::isEnabled("system","languagefilter") )
			{
				$rewrite	= $jconfig->get('sef_rewrite');

				$base		= str_ireplace( JURI::root( true ) , '' , $uri->getPath() );
				$path		= $rewrite ? $base : JString::substr( $base , 10 );
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
					$url		= rtrim( JURI::root() , '/' ) . '/' . $language . '/?option=com_komento';
					$language	= 'none';
				}
				else
				{
					$url		= rtrim( JURI::root() , '/' ) . '/index.php/' . $language . '/?option=com_komento';
				}
			}
		}
		else
		{

			$url		= rtrim( JURI::root() , '/' ) . '/index.php?option=com_komento';
		}

		$menu	= JFactory::getApplication()->getMenu();
		$item	= $menu->getActive();
		if( isset( $item->id) )
		{
			$url    .= '&Itemid=' . $item->id;
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
}
