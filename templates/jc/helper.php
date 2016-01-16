<?php
/*
 * @package     perfecttemplate
 * @copyright   Copyright (c) Perfect Web Team / perfectwebteam.nl
 * @license     GNU General Public License version 3 or later
 */

// Prevent direct access
defined('_JEXEC') or die();

// Define the base-path of this template
define('TEMPLATE_BASE', dirname(__FILE__));

/*
 * ThisTemplate class
 */

class ThisTemplateHelper
{
	/*
	 * Document instance
	 */
	protected $doc = null;
	/*
	 * Application instance
	 */
	protected $app = null;
	/*
	 * JInput instance
	 */
	protected $input = null;
	/*
	 * Menu instance
	 */
	protected $menu = null;

	/*
	 * Constructor called when instantiating this class
	 */
	public function __construct()
	{
		// Fetch system variables
		$this->doc      = JFactory::getDocument();
		$this->app      = JFactory::getApplication();
		$this->head     = $this->doc->getHeadData();
		$this->input    = $this->app->input;
		$this->menu     = $this->app->getMenu();
		$this->template = $this->app->getTemplate();
		$this->itemid   = $this->getItemId();

		// Automatically reset the generator
		$this->doc->setGenerator(JFactory::getConfig()->get('config.sitename'));
	}

	/*
	 * Method to manually override the META-generator
	 *
	 * @access public
	 * @param string $generator
	 * @return null
	 */
	public function setGenerator($generator)
	{
		$this->doc->setGenerator($generator);
	}

	/**
	 * Method to set some Meta data
	 *
	 * @param $template
	 */
	public function setMetadata($template)
	{
		$this->doc->setCharset('utf8');
		$this->doc->setMetaData('viewport', 'width=device-width, initial-scale=1.0');
		$this->doc->setMetaData('mobile-web-app-capable', 'yes');
		$this->doc->setMetaData('apple-mobile-web-app-capable', 'yes');
		$this->doc->setMetaData('apple-mobile-web-app-status-bar-style', 'black');
		$this->doc->setMetaData('apple-mobile-web-app-title', $template->params->get('sitetitle'));
		$this->doc->setMetaData('X-UA-Compatible', 'IE=edge', true);
		$this->doc->setGenerator($template->params->get('sitetitle'));
	}

	/**
	 * Method to set Favicon
	 *
	 * @param $template
	 */
	public function setFavicon()
	{
		$this->doc->addHeadLink('templates/' . $this->template . '/images/favicon.ico', 'shortcut icon', 'rel', array('type' => 'image/ico'));
		$this->doc->addHeadLink('templates/' . $this->template . '/images/favicon.png', 'shortcut icon', 'rel', array('type' => 'image/png'));
		$this->doc->addHeadLink('templates/' . $this->template . '/images/xtouch-icon.png', 'apple-touch-icon', 'rel', array('type' => 'image/png'));
	}

	/*
	 * Method to return the current Menu Item ID
	 *
	 * @access public
	 * @param null
	 * @return int
	 */
	public function getItemId()
	{
		return $this->input->getInt('Itemid');
	}

	/*
	 * Method to fetch the current path
	 *
	 * @access public
	 * @param string $output Output type
	 * @return mixed
	 */
	public function getPath($output = 'array')
	{
		$uri  = JURI::getInstance();
		$path = $uri->getPath();
		$path = preg_replace('/^\//', '', $path);
		if ($output == 'array')
		{
			$path = explode('/', $path);

			return $path;
		}

		return $path;
	}

	/*
	 * Method to get the current sitename
	 *
	 * @access public
	 * @param null
	 * @return string
	 */
	public function getSitename()
	{
		return JFactory::getConfig()->get('config.sitename');
	}

	/*
	 * Generate a list of useful CSS classes for the body
	 *
	 * @param null
	 * @return bool
	 */
	public function getBodySuffix()
	{
		$classes   = array();
		$classes[] = 'option-' . str_replace('_', '-', $this->input->getCmd('option'));
		$classes[] = 'view-' . $this->input->getCmd('view');
		//$classes[] = 'layout-' . $this->input->getCmd('layout');
		$classes[] = 'page-' . $this->getItemId();
		if ($this->isHome())
		{
			$classes[] = 'path-home';
		}
		else
		{
			$classes[] = 'path-' . implode('-', $this->getPath('array'));
		}
		$classes[] = 'home-' . (int) $this->isHome();

		return implode(' ', $classes);
	}

	/**
	 * get PageClass set with Menu Item
	 *
	 * @return mixed
	 */
	public function getPageClass()
	{
		$activeMenu = $this->menu->getActive();
		$pageclass  = ($activeMenu) ? $activeMenu->params->get('pageclass_sfx', '') : '';

		return $pageclass;
	}

	/*
	 * Method to determine whether the current page is the Joomla! homepage
	 *
	 * @access public
	 * @param null
	 * @return bool
	 */
	public function isHome()
	{
		// Fetch the active menu-item
		$activeMenu = $this->menu->getActive();

		// Return whether this active menu-item is home or not
		return (boolean) ($activeMenu) ? $activeMenu->home : false;
	}

	/**
	 * Remove unwanted CSS
	 */
	public function unloadCss()
	{
		$unset_css = array('com_finder', 'foundry');
		foreach ($this->doc->_styleSheets as $name => $style)
		{
			foreach ($unset_css as $css)
			{
				if (strpos($name, $css) !== false)
				{
					unset($this->doc->_styleSheets[$name]);
				}
			}
		}
	}

	/**
	 * Load CSS
	 */
	public function loadCss()
	{
		$this->doc->addStyleSheet('templates/' . $this->template . '/css/template.css');
	}

	/**
	 * Remove unwanted JS
	 */
	public function unloadJs()
	{
		// Call JavaScript to be able to unset it correctly
		JHtml::_('behavior.framework');
		JHtml::_('bootstrap.framework');
		JHtml::_('jquery.framework');
		JHtml::_('bootstrap.tooltip');

		// Unset unwanted JavaScript
		unset($this->doc->_scripts[$this->doc->baseurl . '/media/system/js/mootools-core.js']);
		unset($this->doc->_scripts[$this->doc->baseurl . '/media/system/js/mootools-more.js']);
		unset($this->doc->_scripts[$this->doc->baseurl . '/media/system/js/caption.js']);
		unset($this->doc->_scripts[$this->doc->baseurl . '/media/system/js/core.js']);
		//unset($this->doc->_scripts[$this->doc->baseurl . '/media/jui/js/jquery.min.js']);
		unset($this->doc->_scripts[$this->doc->baseurl . '/media/jui/js/jquery-noconflict.js']);
		unset($this->doc->_scripts[$this->doc->baseurl . '/media/jui/js/jquery-migrate.min.js']);
		unset($this->doc->_scripts[$this->doc->baseurl . '/media/jui/js/bootstrap.min.js']);
		unset($this->doc->_scripts[$this->doc->baseurl . '/media/system/js/tabs-state.js']);
		unset($this->doc->_scripts[$this->doc->baseurl . '/media/system/js/validate.js']);

		if (isset($this->doc->_script['text/javascript']))
		{
			$this->doc->_script['text/javascript'] = preg_replace('%jQuery\(window\)\.on\(\'load\'\,\s*function\(\)\s*\{\s*new\s*JCaption\(\'img.caption\'\);\s*}\s*\);\s*%', '', $this->doc->_script['text/javascript']);
			$this->doc->_script['text/javascript'] = preg_replace("%\s*jQuery\(document\)\.ready\(function\(\)\{\s*jQuery\('\.hasTooltip'\)\.tooltip\(\{\"html\":\s*true,\"container\":\s*\"body\"\}\);\s*\}\);\s*%", '', $this->doc->_script['text/javascript']);

			// Unset completly if empty
			if (empty($this->doc->_script['text/javascript']))
			{
				unset($this->doc->_script['text/javascript']);
			}
		}
	}

	/**
	 * Load JS
	 *
	 */
	public function loadJs()
	{
		$this->doc->addScript('templates/' . $this->template . '/js/modernizr.js');
		$this->doc->addScript('templates/' . $this->template . '/js/scripts.js');
		$this->doc->addScript('templates/' . $this->template . '/js/bootstrap.min.js');
	}


	/**
	 * Load custom font in localstorage
	 *
	 * @param $fontname
	 */

	public function localstorageFont($fontname)
	{
		$javascript = "<!-- Local Storage for font -->
  !function () {
    function addFont(font) {
      var style = document.createElement('style');
      style.rel = 'stylesheet';
      document.head.appendChild(style);
      style.textContent = font
    }
    var font = '" . $fontname . "';
    try {
      if (localStorage[font])addFont(localStorage[font]); else {
        var request = new XMLHttpRequest;
        request.open('GET', 'templates/" . $this->template . "/css/font.css', !0);
        request.onload = function () {
          request.status >= 200 && request.status < 400 && (localStorage[font] = request.responseText, addFont(request.responseText))
        }, request.send()
      }
    } catch (d) {
    }
  }();";
		$this->doc->addScriptDeclaration($javascript);
	}

	/*
	 * Method to detect a certain browser type
	 *
	 * @access public
	 * @param string $shortname
	 * @return string
	 */
	public function isBrowser($shortname = 'ie6')
	{
		jimport('joomla.environment.browser');
		$browser = JBrowser::getInstance();

		$rt = false;
		switch ($shortname)
		{
			case 'edge':
				$rt = (stristr($browser->getAgentString(), 'edge')) ? true : false;
				break;
			case 'firefox':
			case 'ff':
				$rt = (stristr($browser->getAgentString(), 'firefox')) ? true : false;
				break;
			case 'ie':
				$rt = ($browser->getBrowser() == 'msie') ? true : false;
				break;
			case 'ie6':
				$rt = ($browser->getBrowser() == 'msie' && $browser->getVersion() == '6.0') ? true : false;
				break;
			case 'ie7':
				$rt = ($browser->getBrowser() == 'msie' && $browser->getVersion() == '7.0') ? true : false;
				break;
			case 'ie8':
				$rt = ($browser->getBrowser() == 'msie' && $browser->getVersion() == '8.0') ? true : false;
				break;
			case 'ie9':
				$rt = ($browser->getBrowser() == 'msie' && $browser->getVersion() == '9.0') ? true : false;
				break;
			case 'lteie9':
				$rt = ($browser->getBrowser() == 'msie' && $browser->getMajor() <= 9) ? true : false;
				break;
			default:
				$rt = (stristr($browser->getAgentString(), $shortname)) ? true : false;
				break;
		}

		return $rt;
	}

	/**
	 * load Analytics
	 *
	 * @param $template
	 *
	 * @return array
	 */
	public function getAnalytics($template)
	{
		$analytics   = $template->params->get('analytics', 0);
		$analyticsId = $template->params->get('analyticsid');

		// Analytics
		switch ($analytics)
		{
			case 0:
				break;
			case 1:
				// Google Analytics - loaded in head
				if ($analyticsId)
				{
					$analyticsScript = "

        var _gaq = _gaq || [];
        _gaq.push(['_setAccount', '" . $analyticsId . "']);
        _gaq.push(['_trackPageview']);

        (function() {
        var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
        ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
        var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
        })();
      ";
					$this->doc->addScriptDeclaration($analyticsScript);
				}
				break;
			case 2:
				// Universal Google Universal Analytics - loaded in head
				if ($analyticsId)
				{
					$analyticsScript = "

        (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
        (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
        m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
        })(window,document,'script','//www.google-analytics.com/analytics.js','ga');

        ga('create', '" . $analyticsId . "', 'auto');
        ga('send', 'pageview');
      ";
					$this->doc->addScriptDeclaration($analyticsScript);
				}
				break;
			case 3:
				// Google Tag Manager - party loaded in head
				if ($analyticsId)
				{
					$analyticsScript = "

  <!-- Google Tag Manager -->
  (function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src='//www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);})(window,document,'script','dataLayer','" . $analyticsId . "');
  <!-- End Google Tag Manager -->

          ";
					$this->doc->addScriptDeclaration($analyticsScript);

					// Google Tag Manager - partly loaded directly after body
					$analyticsScript = "<!-- Google Tag Manager -->
<noscript><iframe src=\"//www.googletagmanager.com/ns.html?id=" . $analyticsId . "\" height=\"0\" width=\"0\" style=\"display:none;visibility:hidden\"></iframe></noscript>
<!-- End Google Tag Manager -->
";

					return array('script' => $analyticsScript, 'position' => 'after_body_start');
				}
				break;
		}
	}

	public function getPagelayout($template)
	{
		$SidebarA = false;
		$SidebarB = false;

		if ($template->countModules('sidebar-a'))
		{
			$SidebarA = true;
		}

		if ($template->countModules('sidebar-b'))
		{
			$SidebarB = true;
		}

		if ($this->isHome())
		{
			$SidebarA = false;
			$SidebarB = false;
		}

		if ($SidebarA == true)
		{
			return '2column-left';
		}

		if ($SidebarB == true)
		{
			return '2column-right';
		}

		if ($SidebarA == true && $SidebarB == true)
		{
			return '3column';
		}

		if ($SidebarA == false && $SidebarB == false)
		{
			return 'home';
		}

		return $template->params->get('pagelayout', '1column');
	}

}

// Instantiate the helper class
$helper = new ThisTemplateHelper();