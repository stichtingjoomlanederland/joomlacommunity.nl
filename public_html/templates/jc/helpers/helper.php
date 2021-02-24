<?php
/**
 * @package     perfecttemplate
 * @copyright   Copyright (c) Perfect Web Team / perfectwebteam.nl
 * @license     GNU General Public License version 3 or later
 */

// Prevent direct access
use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;

defined('_JEXEC') or die();

// Define the base-path of this template
define('TEMPLATE_BASE', dirname(__FILE__));

// Instantiate the helper class
$helper = new ThisTemplateHelper();

// changes to HEAD
$helper->setMetadata($this);
$helper->setFavicon();
$helper->unloadCss();
//$helper->unloadJs();
$helper->unsetSqueezeBox();
$helper->loadCss();
$helper->loadJs();

// Font
$helper->localstorageFont();

// Analytics
$analyticsData = $helper->getAnalytics($this);

// changes to Body
$pageclass  = $helper->getPageClass();
$debug      = $helper->settings['debug'];

/**
 * ThisTemplate class
 */
class ThisTemplateHelper
{
	/**
	 * Template settings
	 */
	public $settings = array(
		'debug'       => true,
		//'unset_css'   => array('com_finder', 'foundry', 'com_rseventspro', 'com_rscomments', 'com_easydiscuss'),
		'unset_css'   => array('com_finder', 'foundry', 'com_rseventspro', 'com_rscomments'),
		'analytics'   => 0, // 0 = none, GA = Universal Google Analytics, GTM = Google Tag Manager, Mix = Mixpanel
		'analyticsid' => '',
	);

	/**
	 * Document instance
	 */
	protected $doc = null;
	/**
	 * Application instance
	 */
	protected $app = null;
	/**
	 * JInput instance
	 */
	protected $input = null;
	/**
	 * Menu instance
	 */
	protected $menu = null;

	/**
	 * Constructor called when instantiating this class
	 */
	public function __construct()
	{
		// Fetch system variables
		$this->doc      = Factory::getDocument();
		$this->app      = Factory::getApplication();
		$this->head     = $this->doc->getHeadData();
		$this->input    = $this->app->input;
		$this->menu     = $this->app->getMenu();
		$this->template = $this->app->getTemplate();
		$this->itemid   = $this->getItemId();

		// Automatically reset the generator
		$this->doc->setGenerator(Factory::getConfig()->get('config.sitename'));
	}

	/**
	 * Method to manually override the META-generator
	 *
	 * @access public
	 *
	 * @param string $generator
	 *
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
		$this->doc->setCharset('utf-8');
		$this->doc->setMetaData('viewport', 'width=device-width, initial-scale=1.0');
		$this->doc->setMetaData('mobile-web-app-capable', 'yes');
		$this->doc->setMetaData('apple-mobile-web-app-capable', 'yes');
		$this->doc->setMetaData('apple-mobile-web-app-status-bar-style', 'black');
		$this->doc->setMetaData('apple-mobile-web-app-title', $template->params->get('sitetitle'));
		$this->doc->setMetaData('X-UA-Compatible', 'IE=edge', true);
		$this->doc->setGenerator($template->params->get('sitetitle'));
		$this->doc->setMediaVersion($this->getFileMTime(JPATH_ROOT));
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
		$this->doc->addHeadLink('templates/' . $this->template . '/images/favicon.png', 'apple-touch-icon', 'rel', array('type' => 'image/png'));
	}

	/**
	 * Method to return the current Menu Item ID
	 *
	 * @access public
	 *
	 * @param null
	 *
	 * @return int
	 */
	public function getItemId()
	{
		return $this->input->getInt('Itemid');
	}

	/**
	 * Method to fetch the current path
	 *
	 * @access public
	 *
	 * @param string $output Output type
	 *
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

	/**
	 * Method to get the current sitename
	 *
	 * @access public
	 *
	 * @param null
	 *
	 * @return string
	 */
	public function getSitename()
	{
		return Factory::getConfig()->get('sitename');
	}

	/**
	 * Method to get the title of active menu
	 *
	 * @access public
	 *
	 * @param null
	 *
	 * @return string
	 */
	public function getActiveMenuTitle()
	{
		$activeMenu = $this->menu->getActive();

		return $activeMenu->title;
	}

    /**
     * Method to get current Template Name
     *
     * @return string
     * @since 1.0.0
     * @throws Exception
     */
    static public function template()
    {
        return Factory::getApplication()->getTemplate();
    }

	/**
	 * Generate a list of useful CSS classes for the body
	 *
	 * @param null
	 *
	 * @return bool
	 */
	public function getBodySuffix()
	{
		$classes   = array();
		$classes[] = $this->getSubSite();
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
	 * get Subsite from active menu
	 *
	 * @return mixed
	 */
	public function getSubSite()
	{
		$activeMenu = $this->menu->getActive();
		$route_parts = explode('/', $activeMenu->route);

		return $route_parts[0];
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

	/**
	 * Method to determine whether the current page is the Joomla! homepage
	 *
	 * @access public
	 *
	 * @param null
	 *
	 * @return bool
	 */
	public function isHome()
	{
		$activeMenu = $this->menu->getActive();

		return (boolean) ($activeMenu) ? $activeMenu->home : false;
	}

	/**
	 * Method to determine whether the current page is EasyDiscuss
	 *
	 * @access public
	 *
	 * @param null
	 *
	 * @return bool
	 */
	public function isEasyDiscuss()
	{
		return ($this->input->getCmd('option') == 'com_easydiscuss');
	}

	/**
	 * Remove unwanted CSS
	 */
	public function unloadCss()
	{
		$unset_css = $this->settings['unset_css'];
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
		HTMLHelper::_('stylesheet', 'template.css', ['version' => $this->getFileMTime(JPATH_THEMES . '/' . $this->template() . '/css/template.css'), 'relative' => true]);

		if ($this->isEasyDiscuss())
		{
			//HTMLHelper::_('stylesheet', 'easydiscuss.css', ['version' => $this->getFileMTime(JPATH_THEMES . '/' . $this->template() . '/css/easydiscuss.css'), 'relative' => true]);
		}

        // Check for a custom CSS file
        HTMLHelper::_('stylesheet', 'user.css', ['version' => $this->getFileMTime(JPATH_THEMES . '/' . $this->template() . '/css/user.css'), 'relative' => true]);
	}

	/**
	 * Remove unwanted JS
	 */
	public function unloadJs()
	{
		// Call JavaScript to be able to unset it correctly
		HTMLHelper::_('behavior.framework');
		HTMLHelper::_('bootstrap.framework');
		HTMLHelper::_('jquery.framework');

		// Unset unwanted JavaScript
		unset($this->doc->_scripts[$this->doc->baseurl . '/media/system/js/mootools-core.js']);
		unset($this->doc->_scripts[$this->doc->baseurl . '/media/system/js/mootools-more.js']);
		unset($this->doc->_scripts[$this->doc->baseurl . '/media/system/js/caption.js']);
		//unset($this->doc->_scripts[$this->doc->baseurl . '/media/system/js/core.js']);
		unset($this->doc->_scripts[$this->doc->baseurl . '/media/system/js/modal.js']);
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
			$this->doc->_script['text/javascript'] = preg_replace('%\s*jQuery\(function\(\$\)\{\s*\$\(\"\.hasTooltip\"\)\.tooltip\(\{\"html\":\s*true,\"container\":\s*\"body\"\}\);\s*\}\);\s*%', '', $this->doc->_script['text/javascript']);

			// Unset completly if empty
			if (empty($this->doc->_script['text/javascript']))
			{
				unset($this->doc->_script['text/javascript']);
			}
		}

        $this->fixGoogleMapsScripts();
	}

    public function fixGoogleMapsScripts()
    {
        $googleMaps = false;
        $libraries = [];

        foreach ($this->doc->_scripts as $scriptName => $scriptArgs)
        {
            if (!stristr($scriptName, 'maps.google.com/maps/api/js'))
            {
                continue;
            }

            $googleMaps = true;

            if (preg_match('/libraries=(.*)/', $scriptName, $match))
            {
                $matchLibraries = explode(',', $match[1]);
                $libraries = array_merge($matchLibraries, $libraries);
            }

            unset($this->doc->_scripts[$scriptName]);
        }

        if ($googleMaps === false)
        {
            return;
        }

        $googleMapsScript = 'https://maps.google.com/maps/api/js';

        if (!empty($libraries))
        {
            $googleMapsScript .= '?libraries=' . implode(',', $libraries);
        }

        $newScript = array($googleMapsScript => $scriptArgs);
        $this->doc->_scripts = array_merge($newScript, $this->doc->_scripts);
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
        if (!file_exists($file) || filesize($file) == 0)
        {
            return null;
        }

        return filemtime($file);
    }

	/**
	 * unset Squeezebox
	 *
	 */
	public function unsetSqueezeBox()
	{

		if (isset($this->doc->_script['text/javascript']))
		{
			$this->doc->_script['text/javascript'] = preg_replace('%jQuery\(function\(\$\) {\s*SqueezeBox.initialize\(\{\}\);\s*SqueezeBox.assign\(\$\(\'.rs_modal\'\).get\(\), \{\s*parse: \'rel\'\s*\}\);\s*\}\);\s*window.jModalClose = function \(\) \{\s*SqueezeBox.close\(\);\s*\}%', '', $this->doc->_script['text/javascript']);

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
		//$this->doc->addScript('templates/' . $this->template . '/js/modernizr.js');
		//$this->doc->addScript('templates/' . $this->template . '/js/scripts.js');
		HTMLHelper::_('script', 'bootstrap.min.js', ['version' => 'auto', 'relative' => true]);
	}

	/**
	 * Load custom font in localstorage
	 *
	 * @return void
	 * @since  PerfectSite2.1.0
	 * @throws Exception
	 */
	public function localstorageFont()
	{
		// Keep whitespace below for nicer source code
		$javascript = "    !function(){\"use strict\";function e(e,t,n){e.addEventListener?e.addEventListener(t,n,!1):e.attachEvent&&e.attachEvent(\"on\"+t,n)}function t(e){return window.localStorage&&localStorage.font_css_cache&&localStorage.font_css_cache_file===e}function n(){if(window.localStorage&&window.XMLHttpRequest)if(t(o))c(localStorage.font_css_cache);else{var n=new XMLHttpRequest;n.open(\"GET\",o,!0),e(n,\"load\",function(){4===n.readyState&&(c(n.responseText),localStorage.font_css_cache=n.responseText,localStorage.font_css_cache_file=o)}),n.send()}else{var a=document.createElement(\"link\");a.href=o,a.rel=\"stylesheet\",a.type=\"text/css\",document.getElementsByTagName(\"head\")[0].appendChild(a),document.cookie=\"font_css_cache\"}}function c(e){var t=document.createElement(\"style\");t.innerHTML=e,document.getElementsByTagName(\"head\")[0].appendChild(t)}var o=\"/templates/" . $this->template . "/css/font.css\";window.localStorage&&localStorage.font_css_cache||document.cookie.indexOf(\"font_css_cache\")>-1?n():e(window,\"load\",n)}();";
		Factory::getDocument()->addScriptDeclaration($javascript);
	}

	/**
	 * Method to detect a certain browser type
	 *
	 * @access public
	 *
	 * @param string $shortname
	 *
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
		$analytics   = $template->params->get('analytics', 3);
		$analyticsId = $template->params->get('analyticsid', 'GTM-PTGXDS6');

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
}
