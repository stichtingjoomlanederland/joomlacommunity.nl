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

class EasyDiscussStylesheet extends EasyDiscuss
{
	public $location = '';
	private $theme = null;
	private $type = 'themes';

	public function __construct($location = 'site')
	{
		parent::__construct();
		
		$this->location = $location;
		$this->theme = 'wireframe';

		if ($this->location == 'admin') {
			$this->theme = 'default';
		}
	}

	/**
	 * Centralized method to add stylsheet from EasyDiscuss
	 *
	 * @since	5.0.3
	 * @access	public
	 */
	public function addStylesheet($url)
	{
		$this->doc->addStylesheet($url);
	}

	/**
	 * Attaches the stylesheet to the head of the document
	 *
	 * @since	4.0
	 * @access	public
	 */
	public function attach()
	{
		// Attach font awesome
		if ($this->location == 'admin' || ($this->location == 'site' && $this->config->get('layout_fontawesome'))) {
			$this->attachFontAwesome();
		}

		// Attach Prism code syntax highlighter
		if ($this->location == 'site' && $this->config->get('layout_prism')) {
			$this->attachPrism();
		}

		// Attach mmenu css
		if (ED::responsive()->isMobile() || ED::responsive()->isTablet()) {
			$this->attachMobileMenu();
		}

		$url = $this->getStylesheetUri();
		$this->addStylesheet($url);

		// Attach custom css codes on the page
		if ($this->location == 'site' && $this->config->get('layout_customcss')) {
			$this->attachCustomCss();
		}

		// Add print css
		$print = JFactory::getApplication()->input->get('print', 0, 'int');

		if ($this->location == 'site' && $print) {
			$this->addStyleSheet(JURI::root(true) . '/media/com_easydiscuss/themes/wireframe/css/print.min.css');
		}
	}

	/**
	 * if there is a custom.css overriding, we need to attach this custom.css file.
	 *
	 * @since	5.0.0
	 * @access	public
	 */
	private function attachCustomCss()
	{
		$path = JPATH_ROOT . '/templates/' . $this->app->getTemplate() . '/html/com_easydiscuss/css/custom.css';
		$exists = JFile::exists($path);

		if (!$exists) {
			return;
		}

		$customURI = JURI::root(true) . '/templates/' . $this->app->getTemplate() . '/html/com_easydiscuss/css/custom.css';
		$this->addStyleSheet($customURI);
	}

	/**
	 * if there is a custom.css overriding, we need to attach this custom.css file.
	 *
	 * @since	5.0.0
	 * @access	public
	 */
	private function attachFontAwesome()
	{
		static $loaded = null;

		if (is_null($loaded)) {
			$path = JURI::root(true) . '/media/com_easydiscuss/fonts/font-awesome/css/all.min.css';

			$this->addStylesheet($path);

			$loaded = true;
		}

		return $loaded;
	}

	/**
	 * Determine the site whether need to load built-in Prism stylesheets
	 *
	 * @since	5.0.0
	 * @access	public
	 */
	private function attachPrism()
	{
		static $loaded = null;

		if (is_null($loaded)) {
			$path = JURI::root(true) . '/media/com_easydiscuss/vendors/prism/prism.css';

			$this->addStyleSheet($path);

			$loaded = true;
		}

		return $loaded;
	}
	
	/**
	 * Renders the mobile menu css
	 *
	 * @since	5.0.0
	 * @access	public
	 */
	private function attachMobileMenu()
	{
		$config = ED::config();

		if (!defined('SI_MMENU')) {
			$css = JURI::root(true) . '/media/com_easydiscuss/vendors/mmenu/mmenu.css';

			$this->addStylesheet($css);

			define('SI_MMENU', true);

			return false;
		}

		return true;
	}

	/**
	 * Generates the url to the stylesheet file
	 *
	 * @since	5.0.0
	 * @access	public
	 */
	public function getStylesheetUri()
	{
		$theme = $this->location == 'admin' ? 'admin' : $this->theme;
		$uri = JURI::root(true) . '/media/com_easydiscuss/' . $this->type . '/' . $theme . '/css/';
		$file = 'style';

		if ($this->isRTL()) {
			// RTL only use minified version regardless development mode or not
			$file .= '-rtl.min';
		}

		if (!$this->isDevelopment() && !$this->isRTL()) {
			$file .= '.min';
		}

		$file .= '.css';

		$hash = md5(ED::getLocalVersion());
		$uri .= $file . '?' . $hash . '=1';

		return $uri;
	}

	/**
	 * Determines if the site is in RTL mode
	 *
	 * @since	4.2.0
	 * @access	public
	 */
	public function isRTL()
	{
		static $rtl = null;

		if (is_null($rtl)) {
			$lang = JFactory::getLanguage();
			$rtl = $lang->isRTL();
		}

		return $rtl;
	}

	/**
	 * Determines if the site is under development mode
	 *
	 * @since	4.2.0
	 * @access	public
	 */
	public function isDevelopment()
	{
		return $this->config->get('system_environment') == 'development';
	}
}