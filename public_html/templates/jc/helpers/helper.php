<?php
/**
 * @package     perfecttemplate
 * @copyright   Copyright (c) Perfect Web Team / perfectwebteam.nl
 * @license     GNU General Public License version 3 or later
 */


defined('_JEXEC') or die();

use Joomla\CMS\Document\HtmlDocument;
use Joomla\CMS\Environment\Browser;
use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\MVC\Model\BaseDatabaseModel;
use Joomla\CMS\Uri\Uri;

/**
 * Class PWTTemplateHelper
 *
 * @since 1.0.0
 */
class PWTTemplateHelper
{
	/**
	 * @var   array
	 * @since 1.0.0
	 */
	static private $fields;

	/**
	 * @var   array
	 * @since 1.0.0
	 */
	static private $rawfields;

	/**
	 * @var   array
	 * @since 1.0.0
	 */
	static private $data;

	/**
	 * Method to get current Template Name
	 *
	 * @return string
	 * @throws Exception
	 * @since 1.0.0
	 */
	static public function template()
	{
		return Factory::getApplication()->getTemplate();
	}

	/**
	 * Method to get current Page Option
	 *
	 * @access public
	 *
	 * @return mixed
	 * @throws  Exception
	 * @since  1.0
	 */
	static public function getPageOption()
	{
		return Factory::getApplication()->input->getCmd('option', '');
	}

	/**
	 * Method to get current Page View
	 *
	 * @access public
	 *
	 * @return mixed
	 * @throws  Exception
	 * @since  1.0
	 */
	static public function getPageView()
	{
		return Factory::getApplication()->input->getCmd('view', '');
	}

	/**
	 * Method to get current Page Layout
	 *
	 * @access public
	 *
	 * @return mixed
	 * @throws  Exception
	 * @since  version
	 */
	static public function getPageLayout()
	{
		return Factory::getApplication()->input->getCmd('layout', '');
	}

	/**
	 * Method to get current Page Task
	 *
	 * @access public
	 *
	 * @return mixed
	 * @throws  Exception
	 * @since  1.0
	 */
	static public function getPageTask()
	{
		return Factory::getApplication()->input->getCmd('task', '');
	}

	/**
	 * Method to get the current Menu Item ID
	 *
	 * @access public
	 *
	 * @return integer
	 * @throws  Exception
	 * @since  1.0
	 */
	static public function getItemId()
	{
		return Factory::getApplication()->input->getInt('Itemid');
	}

	/**
	 * Method to get PageClass set with Menu Item
	 *
	 * @return mixed
	 * @throws  Exception
	 * @since  1.0
	 */
	static public function getPageClass()
	{
		$activeMenu = Factory::getApplication()->getMenu()->getActive();
		$pageClass = ($activeMenu) ? $activeMenu->params->get('pageclass_sfx', '') : '';

		return $pageClass;
	}

	/**
	 * get Subsite from active menu
	 *
	 * @return mixed
	 */
	static public function getSubSite()
	{
		$activeMenu = Factory::getApplication()->getMenu()->getActive();
		$route_parts = explode('/', $activeMenu->route);

		return $route_parts[0];
	}

	/**
	 * Method to determine whether the current page is the Joomla! homepage
	 *
	 * @access public
	 *
	 * @return boolean
	 * @throws  Exception
	 * @since  1.0
	 */
	static public function isHome()
	{
		// Fetch the active menu-item
		$activeMenu = Factory::getApplication()->getMenu()->getActive();

		// Return whether this active menu-item is home or not
		return (boolean)($activeMenu) ? $activeMenu->home : false;
	}

	/**
	 * Method to fetch the current path
	 *
	 * @access public
	 *
	 * @param string $output Output type
	 *
	 * @return mixed
	 * @since  1.0
	 */
	static public function getPath($output = 'array')
	{
		$path = Uri::getInstance()->getPath();
		$path = preg_replace('/^\//', '', $path);

		if ($output == 'array') {
			$path = explode('/', $path);

			return $path;
		}

		return $path;
	}

	/**
	 * Generate a list of useful CSS classes for the body
	 *
	 * @access public
	 *
	 * @return boolean
	 * @throws  Exception
	 * @since  1.0
	 */
	static public function setBodyClass()
	{
		$classes = array();
		$classes[] = 'base';
		$classes[] = self::getSubSite();
		$classes[] = 'option-' . self::getPageOption();
		$classes[] = 'view-' . self::getPageView();
		$classes[] = self::getPageLayout() ? 'layout-' . self::getPageLayout() : 'no-layout';
		$classes[] = self::getPageTask() ? 'task-' . self::getPageTask() : 'no-task';
		$classes[] = 'itemid-' . self::getItemId();
		$classes[] = self::getPageClass();
		$classes[] = self::isHome() ? 'path-home' : 'path-' . implode('-', self::getPath('array'));

		return implode(' ', $classes);
	}

	/**
	 * Method to manually override the META-generator
	 *
	 * @access public
	 *
	 * @param string $generator Generator tag in html source
	 *
	 * @return null
	 *
	 * @since  1.0
	 */
	static public function setGenerator($generator)
	{
		Factory::getDocument()->setGenerator($generator);

		return null;
	}

	/**
	 * Method to get the current sitename
	 *
	 * @access public
	 *
	 * @return string
	 * @since  1.0
	 */
	static public function getSitename()
	{
		return Factory::getConfig()->get('sitename');
	}

	/**
	 * Method to set some Meta data
	 *
	 * @access public
	 *
	 * @param string $favicolorMS Color for Favicon
	 * @param string $favicolorTheme Color for Favicon Background
	 *
	 * @return void
	 * @throws Exception
	 * @since  1.0
	 */
	static public function setMetadata($favicolorMS, $favicolorTheme)
	{
		/** @var HtmlDocument $doc */
		$doc = Factory::getDocument();

		$doc->setHtml5(true);
		$doc->setMetaData('X-UA-Compatible', 'IE=edge', true);
		$doc->setMetaData('viewport', 'width=device-width, initial-scale=1.0');
		$doc->setMetaData('mobile-web-app-capable', 'yes');
		$doc->setMetaData('apple-mobile-web-app-capable', 'yes');
		$doc->setMetaData('apple-mobile-web-app-status-bar-style', 'black');
		$doc->setMetaData('apple-mobile-web-app-title', self::getSitename());
		$doc->setMetaData('msapplication-TileColor', $favicolorMS);
		$doc->setMetaData('msapplication-config', '/templates/' . self::template() . '/images/favicon/browserconfig.xml');
		$doc->setMetaData('theme-color', $favicolorTheme);
		self::setGenerator(self::getSitename());
		$doc->setMediaVersion(self::getFileMTime(JPATH_ROOT)); // set the date/time for versioning css/js files

		if (self::getPageOption() === 'com_search' || self::getPageOption() === 'com_finder') {
			$doc->setMetaData('robots', 'noindex,follow');
		}
	}

	/**
	 * Method to set additional Meta data for Twitter
	 *
	 * @access public
	 *
	 * @param string $twitterSite Twitter username of website
	 * @param string $twitterCreator Twitter username of content creator
	 *
	 * @return void
	 * @throws Exception
	 * @since  2.0
	 */
	static public function setMetadataTwitter($twitterSite, string $twitterCreator)
	{
		/** @var HtmlDocument $doc */
		$doc = Factory::getDocument();

		$doc->setMetaData('twitter:site', '@' . $twitterSite, 'property');
		$doc->setMetaData('twitter:creator', '@' . $twitterCreator, 'property');
		$doc->setMetaData('twitter:card', 'summary_large_image', 'property');
	}

	/**
	 * Method to set Favicon
	 *
	 * @param string $favicolorSVG Color for Favicon
	 *
	 * @return  void
	 * @throws  Exception
	 * @since   PerfectSite 2.1.0
	 */
	static public function setFavicon($favicolorSVG)
	{
		/** @var HtmlDocument $doc */
		$doc = Factory::getDocument();
		$path = 'templates/' . self::template() . '/images/favicon/';

		$doc->addHeadLink($path . 'apple-touch-icon.png', 'apple-touch-icon', 'rel', array('sizes' => '180x180'));
		$doc->addHeadLink($path . 'favicon-32x32.png', 'icon', 'rel', array('type' => 'image/png', 'sizes' => '32x32'));
		$doc->addHeadLink($path . 'favicon-16x16.png', 'icon', 'rel', array('type' => 'image/png', 'sizes' => '16x16'));
		$doc->addHeadLink($path . 'site.webmanifest', 'manifest', 'rel');
		$doc->addHeadLink($path . 'safari-pinned-tab.svg', 'mask-icon', 'rel', array('color' => $favicolorSVG));
		$doc->addHeadLink($path . 'favicon.ico', 'shortcut icon', 'rel');
	}

	/**
	 * Method to get wether site is in development
	 *
	 * @access public
	 *
	 * @param string $name Name of last word in site title
	 *
	 * @return string
	 * @since  PerfectSite 2.1.0
	 */
	static public function isDevelopment($name = '[dev]')
	{
		return boolval(strpos(self::getSitename(), $name));
	}

	/**
	 * Method to determine whether the current page is the requested page
	 *
	 * @access  public
	 *
	 * @param string $request Requested page
	 *
	 * @return  boolean
	 * @since   PerfectSite 2.1.0
	 */
	static public function isPage($request = 'home')
	{
		return URI::getInstance()->getPath() == $request;
	}

	/**
	 * Remove unwanted CSS
	 *
	 * @access  public
	 *
	 * @param array $unset_css Requested page
	 *
	 * @return  boolean
	 * @since   PerfectSite 2.1.0
	 */
	static public function unloadCss($unset_css = array())
	{
		/** @var HtmlDocument $doc */
		$doc = Factory::getDocument();

		if (empty($unset_css)) {
			return false;
		}

		foreach ($doc->_styleSheets as $name => $style) {
			foreach ($unset_css as $css) {
				if (strpos($name, $css) !== false) {
					unset($doc->_styleSheets[$name]);
				}
			}
		}
	}

	/**
	 * Load CSS
	 *
	 * @return void
	 * @throws Exception
	 * @since  PerfectSite 2.1.0
	 */
	static public function loadCss()
	{
		HTMLHelper::_('stylesheet', 'style.css', ['version' => self::getFileMTime(JPATH_THEMES . '/' . self::template() . '/css/style.css'), 'relative' => true]);
		HTMLHelper::_('stylesheet', 'template.css', ['version' => self::getFileMTime(JPATH_THEMES . '/' . self::template() . '/css/template.css'), 'relative' => true]);

		if (self::getPageOption() === 'com_easydiscuss') {
			//HTMLHelper::_('stylesheet', 'easydiscuss.css', ['version' => self::getFileMTime(JPATH_THEMES . '/' . self::template() . '/css/easydiscuss.css'), 'relative' => true]);
		}

		// Check for a custom CSS file
		$userCss = JPATH_SITE . '/templates/' . self::template() . '/css/user.css';

		if (file_exists($userCss) && filesize($userCss) > 0) {
			HTMLHelper::_('stylesheet', 'user.css', ['version' => self::getFileMTime(JPATH_THEMES . '/' . self::template() . '/css/user.css'), 'relative' => true]);
		}
	}

	/**
	 * Remove unwanted JS
	 *
	 * @return void
	 * @since  PerfectSite 2.1.0
	 */
	static public function unloadJs()
	{
		/** @var HtmlDocument $doc */
		$doc = Factory::getDocument();

		// Call JavaScript to be able to unset it correctly
		HTMLHelper::_('behavior.framework');
		HTMLHelper::_('bootstrap.framework');
		HTMLHelper::_('jquery.framework');
		HTMLHelper::_('bootstrap.tooltip');

		// Unset unwanted JavaScript
		unset($doc->_scripts[$doc->baseurl . '/media/system/js/mootools-core.js']);
		unset($doc->_scripts[$doc->baseurl . '/media/system/js/mootools-more.js']);
		unset($doc->_scripts[$doc->baseurl . '/media/system/js/caption.js']);
		unset($doc->_scripts[$doc->baseurl . '/media/system/js/modal.js']);

		if (self::getPageOption() !== 'com_users') {
			unset($doc->_scripts[$doc->baseurl . '/media/system/js/core.js']);
		}

//		if (self::getPageOption() !== 'com_users') {
//			unset($doc->_scripts[$doc->baseurl . '/media/jui/js/jquery.min.js']);
//		}

		if (self::getPageClass() !== 'comments') {
			unset($doc->_scripts[$doc->baseurl . '/media/jui/js/bootstrap.min.js']);
		}

		unset($doc->_scripts[$doc->baseurl . '/media/jui/js/jquery-noconflict.js']);
		unset($doc->_scripts[$doc->baseurl . '/media/jui/js/jquery-migrate.min.js']);
		unset($doc->_scripts[$doc->baseurl . '/media/jui/js/bootstrap.min.js']);
		unset($doc->_scripts[$doc->baseurl . '/media/system/js/tabs-state.js']);
		unset($doc->_scripts[$doc->baseurl . '/media/system/js/validate.js']);

		if (isset($doc->_script['text/javascript'])) {
			$doc->_script['text/javascript'] = preg_replace(
				'%jQuery\(window\)\.on\(\'load\'\,\s*function\(\)\s*\{\s*new\s*JCaption\(\'img.caption\'\);\s*}\s*\);\s*%', '',
				$doc->_script['text/javascript']
			);
			$doc->_script['text/javascript'] = preg_replace(
				'%\s*jQuery\(function\(\$\)\{\s*[initTooltips|initPopovers].*?\}\);\}\s*\}\);%', '', $doc->_script['text/javascript']
			);

			// Unset completly if empty
			if (empty($doc->_script['text/javascript'])) {
				unset($doc->_script['text/javascript']);
			}
		}

		self::fixGoogleMapsScripts();
	}

	/**
	 * unset Squeezebox
	 *
	 */
	static public function unsetSqueezeBox()
	{
		/** @var HtmlDocument $doc */
		$doc = Factory::getDocument();

		if (isset($doc->_script['text/javascript'])) {
			$doc->_script['text/javascript'] = preg_replace('%jQuery\(function\(\$\) {\s*SqueezeBox.initialize\(\{\}\);\s*SqueezeBox.assign\(\$\(\'.rs_modal\'\).get\(\), \{\s*parse: \'rel\'\s*\}\);\s*\}\);\s*window.jModalClose = function \(\) \{\s*SqueezeBox.close\(\);\s*\}%', '', $doc->_script['text/javascript']);

			// Unset completly if empty
			if (empty($doc->_script['text/javascript'])) {
				unset($doc->_script['text/javascript']);
			}
		}
	}

	static public function fixGoogleMapsScripts()
	{
		/** @var HtmlDocument $doc */
		$doc = Factory::getDocument();

		$googleMaps = false;
		$libraries = [];

		foreach ($doc->_scripts as $scriptName => $scriptArgs) {
			if (!stristr($scriptName, 'maps.google.com/maps/api/js')) {
				continue;
			}

			$googleMaps = true;

			if (preg_match('/libraries=(.*)/', $scriptName, $match)) {
				$matchLibraries = explode(',', $match[1]);
				$libraries = array_merge($matchLibraries, $libraries);
			}

			unset($doc->_scripts[$scriptName]);
		}

		if ($googleMaps === false) {
			return;
		}

		$googleMapsScript = 'https://maps.google.com/maps/api/js';

		if (!empty($libraries)) {
			$googleMapsScript .= '?libraries=' . implode(',', $libraries);
		}

		$newScript = array($googleMapsScript => $scriptArgs);
		$doc->_scripts = array_merge($newScript, $doc->_scripts);
	}

	/**
	 * Load JS
	 *
	 * @return void
	 * @since  PerfectSite 2.1.0
	 */
	static public function loadJs($loadModernizr = true, $loadScripts = true, $loadBootstrap = true)
	{
		if ($loadModernizr) {
			HTMLHelper::_('script', 'modernizr.js', ['version' => self::getFileMTime(JPATH_THEMES . '/' . self::template() . '/js/modernizr.js'), 'relative' => true]);
		}
		if ($loadScripts) {
			HTMLHelper::_('script', 'scripts.js', ['version' => self::getFileMTime(JPATH_THEMES . '/' . self::template() . '/js/scripts.js'), 'relative' => true]);
		}
		if ($loadBootstrap) {
			HTMLHelper::_('script', 'bootstrap.js', ['version' => self::getFileMTime(JPATH_THEMES . '/' . self::template() . '/js/bootstrap.js'), 'relative' => true]);
		}

		if (self::getPageClass() === 'comments') {
			HTMLHelper::script('com_rscomments/site.js', array('relative' => true, 'version' => 'auto'));
		}
	}

	/**
	 * Get file modification time
	 *
	 * @param $file
	 *
	 * @return false|int|null
	 *
	 * @since PerfectSite 5.11.0
	 */
	static public function getFileMTime($file)
	{
		if (!file_exists($file) || filesize($file) == 0) {
			return null;
		}

		return filemtime($file);
	}

	/**
	 * Load custom font in localstorage
	 *
	 * @return void
	 * @throws Exception
	 * @since  PerfectSite 2.1.0
	 */
	static public function localstorageFont()
	{
		// Keep whitespace below for nicer source code
		$javascript
			= "    !function(){\"use strict\";function e(e,t,n){e.addEventListener?e.addEventListener(t,n,!1):e.attachEvent&&e.attachEvent(\"on\"+t,n)}function t(e){return window.localStorage&&localStorage.font_css_cache&&localStorage.font_css_cache_file===e}function n(){if(window.localStorage&&window.XMLHttpRequest)if(t(o))c(localStorage.font_css_cache);else{var n=new XMLHttpRequest;n.open(\"GET\",o,!0),e(n,\"load\",function(){4===n.readyState&&(c(n.responseText),localStorage.font_css_cache=n.responseText,localStorage.font_css_cache_file=o)}),n.send()}else{var a=document.createElement(\"link\");a.href=o,a.rel=\"stylesheet\",a.type=\"text/css\",document.getElementsByTagName(\"head\")[0].appendChild(a),document.cookie=\"font_css_cache\"}}function c(e){var t=document.createElement(\"style\");t.innerHTML=e,document.getElementsByTagName(\"head\")[0].appendChild(t)}var o=\"/templates/"
			. self::template()
			. "/css/font.css\";window.localStorage&&localStorage.font_css_cache||document.cookie.indexOf(\"font_css_cache\")>-1?n():e(window,\"load\",n)}();";
		Factory::getDocument()->addScriptDeclaration($javascript);
	}


	/**
	 * Ajax for SVG
	 *
	 * @return void
	 * @throws Exception
	 * @since  PerfectSite 2.1.0
	 */
	static public function ajaxSVG()
	{
		$javascript = "var ajax=new XMLHttpRequest;ajax.open(\"GET\",\"" . Uri::Base() . "templates/" . self::template()
			. "/icons/icons.svg\",!0),ajax.send(),ajax.onload=function(a){var b=document.createElement(\"div\");b.className='svg-sprite';b.innerHTML=ajax.responseText,document.body.insertBefore(b,document.body.childNodes[0])};";
		Factory::getDocument()->addScriptDeclaration($javascript);
	}

	/**
	 * Function to get svg icon
	 *
	 * @param string $icon Icon to look for in perfecttemplate/icons folder
	 * @param boolean $fallback Fallback icon if $icon can't be found
	 *
	 * @return string
	 *
	 * @throws Exception
	 * @since 1.0.0
	 *
	 */
	public static function icon($icon, $fallback = false)
	{
		$template = Factory::getApplication()->getTemplate();
		$path = JPATH_THEMES . '/' . $template . '/icons/';
		$url = $path . $icon . '.svg';

		if (file_exists($url)) {
			return file_get_contents($url);
		}

		if ($fallback && file_exists($path . $fallback . '.svg')) {
			return file_get_contents($path . $fallback . '.svg');
		}

		return '';
	}

	/**
	 * Function to check for active component template override
	 *
	 * @return boolean
	 *
	 * @throws Exception
	 * @since PerfectSite 5.12.0
	 *
	 */
	static public function componentOverrideActive()
	{
		// Always return true for custom component
		if (self::getPageOption() === 'com_jc') {
			return true;
		}

		$layout = self::getPageLayout() ? str_replace(self::template(), '', self::getPageLayout()) : 'default';

		// Fix for blog
		if (self::getPageOption() === 'com_content' && $layout === 'blog') {
			$layout = 'default';
		}

		// Template override path
		$file = JPATH_THEMES . '/' . self::template() . '/html/' . self::getPageOption() . '/' . self::getPageView() . '/' . $layout . '.php';

		if (file_exists($file)) {
			return true;
		}

		return false;
	}

	/**
	 * Function to check if external page fragments are requested
	 *
	 * @return string Name of requested fragment
	 *
	 * @throws Exception
	 * @since 1.0
	 */
	static public function checkExternal()
	{
		return Factory::getApplication()->input->getCmd('external', '');
	}


	/**
	 * @param string $key The requested field
	 * @param string $default Optional. The default value when requested key isn't present
	 *
	 * @return mixed|string   The value of the requested field or default
	 *
	 * @throws Exception
	 * @since  1.0.0
	 */
	static public function getItemValue(string $key, int $id, $type = 'Aricle', string $default = ''): string
	{
		if (!isset(self::$data[$id])) {
			// $model = BaseDatabaseModel::getInstance('Article', 'ContentModel', array('ignore_request' => true));
			// J 3.8.13 :: above will result in "__clone method called on non-object"
			$model = BaseDatabaseModel::getInstance($type, 'ContentModel', array());

			/** @var ContentModelArticle $model */
			self::$data[$id] = (array)$model->getItem($id);
		}

		return self::$data[$id][$key] ?? $default;
	}

	/**
	 * @param string $key The requested field
	 * @param int $id The content item id
	 * @param string $context The context string
	 * @param string $default Optional. The default value when requested key isn't present
	 *
	 * @return string          The value of the requested field or default
	 *
	 * @throws Exception
	 * @since  1.0.0
	 */
	static public function getFieldValue(string $key, int $id, $context = 'com_content.article', string $default = ''): string
	{
		if (!isset(self::$fields[$id])) {
			$fields = FieldsHelper::getFields(
				$context,
				(object)array('id' => $id),
				true
			);

			foreach ($fields as $field) {
				self::$fields[$id][$field->name] = $field->value;
			}
		}

		return (string)self::$fields[$id][$key] ?? $default;
	}

	/**
	 * @param string $key The requested field
	 * @param int $id The content item id
	 * @param string $context The context string
	 * @param string $default Optional. The default value when requested key isn't present
	 *
	 * @return array          The value of the requested field or default
	 *
	 * @throws Exception
	 * @since  1.0.0
	 */
	static public function getFieldRawValue(string $key, int $id, $context = 'com_content.article', array $default = []): array
	{
		if (!isset(self::$rawfields[$id])) {
			FieldsHelper::clearFieldsCache();

			$fields = FieldsHelper::getFields(
				$context,
				(object)array('id' => $id),
				true
			);

			foreach ($fields as $field) {
				self::$rawfields[$id][$field->name] = $field->rawvalue;
			}
		}

		return (array)self::$rawfields[$id][$key] ?? $default;
	}
}
