<?php
/**
* @package		EasyDiscuss
* @copyright	Copyright (C) 2010 - 2017 Stack Ideas Sdn Bhd. All rights reserved.
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

	public function __construct($location = 'site')
	{
		parent::__construct();

		$this->location = $location;
	}

	/**
	 * Attaches the stylesheet to the head of the document
	 *
	 * @since	4.0
	 * @access	public
	 */
	public function attach()
	{
		$themeName = ED::themes()->getName();
		$rtl = $this->isRTL();

		if ($rtl && $this->location == 'site') {

			// check if site is now runing on production or not.
			$filename = 'style-rtl';

			if (!$this->isDevelopment()) {
				$filename .= '.min';
			}

			$uri = DISCUSS_MEDIA_URI . '/themes/' . $themeName . '/css/' . $filename . '.css';
			$this->doc->addStyleSheet($uri);

			$this->attachCustomCss();

			return;
		}

		$uri = $this->compile();

		$this->doc->addStyleSheet($uri);

		if ($this->location == 'site') {
			$this->attachCustomCss();
		}
	}

	/**
	 * if there is a custom.css overriding, we need to attach this custom.css file.
	 *
	 * @since	3.0.0
	 * @access	public
	 */
	private function attachCustomCss()
	{
		$path = JPATH_ROOT . '/templates/' . $this->app->getTemplate() . '/html/com_easydiscuss/css/custom.css';
		$exists = JFile::exists($path);

		if (!$exists) {
			return;
		}

		$customURI = JURI::root() . 'templates/' . $this->app->getTemplate() . '/html/com_easydiscuss/css/custom.css';
		$this->doc->addStyleSheet($customURI);
	}

	/**
	 * Responsible to compile the LESS > CSS file
	 *
	 * @since	4.0
	 * @access	public
	 */
	public function compile()
	{
		if ($this->location == 'site') {
			return $this->compileSiteStylesheet();
		}

		if ($this->location == 'admin') {
			return $this->compileAdminStylesheet();
		}
	}

	/**
	 * Compiles the stylesheet for the site
	 *
	 * @since	4.0
	 * @access	public
	 */
	public function compileSiteStylesheet($theme = null)
	{
		// Allow caller to specify a different stylesheet to compile
		if (is_null($theme)) {
			$theme = ED::themes()->getName();
		}

		$options = array(
					'source' => DISCUSS_MEDIA . '/themes/' . $theme . '/less/style.less',
					'output' => DISCUSS_MEDIA . '/themes/' . $theme . '/css/style.css',
					'compressed' => DISCUSS_MEDIA . '/themes/' . $theme . '/css/style.min.css',
					'compressed_rtl' => DISCUSS_MEDIA . '/themes/' . $theme . '/css/style-rtl.min.css',
				);

		// For production mode, we simply just include the minified css file. Don't render anything else
		// Request compiler to compile less files
		if (!defined('ED_CLI') && !$this->isDevelopment()) {
			$rtl = $this->isRTL();
			$url = $options['compressed'];

			if ($rtl) {
				$url = $options['compressed_rtl'];
			}

			$url = str_ireplace(DISCUSS_MEDIA, rtrim(JURI::root(), '/') . '/media/com_easydiscuss', $url);

			// Hash version to avoid cache
			$hash = md5(ED::getLocalVersion());

			$url = $url . '?' . $hash . '=1';

			return $url;
		}


		// Compile
		$less = ED::less();
		$result = $less->compileStylesheet($options);

		// System encountered error while compiling the admin themes
		if (!$result) {
			ED::setMessageQueue('Could not load stylesheet for default theme.', 'error');
		}

		// If the compilation failed, we need to capture the errors
		if ($result->failed) {

			// Use last compiled stylesheet.
			if (JFile::exists($result->out)) {
				ED::setMessageQueue('Could not compile stylesheet for default theme. Using last compiled stylesheet.', 'error');

				return $result->out_uri;
			}

			// Use failsafe stylesheet
			if (JFile::exists($result->failsafe)) {
				ED::setMessageQueue('Could not compile stylesheet for default theme. Using failsafe stylesheet.', 'error');
				return $result->failsafe_uri;
			}

			return false;
		}

		// Here we assume that the process was successful
		return $result->out_uri;
	}

	/**
	 * Compiles the stylesheet for the admin
	 *
	 * @since	4.0
	 * @access	public
	 */
	public function compileAdminStylesheet()
	{
		$less = ED::less();

		// Request compiler to compile less files
		$options = array(
					'source' => DISCUSS_MEDIA . '/themes/admin/less/style.less',
					'output' => DISCUSS_MEDIA . '/themes/admin/css/style.css',
					'compressed' => DISCUSS_MEDIA . '/themes/admin/css/style.min.css',
					'compressed_rtl' => DISCUSS_MEDIA . '/themes/admin/css/style-rtl.min.css'
				);

		// For production mode, we simply just include the minified css file. Don't render anything else
		// Request compiler to compile less files
		if (!defined('ED_CLI') && !$this->isDevelopment()) {
			$rtl = $this->isRTL();
			$url = $options['compressed'];

			if ($rtl) {
				$url = $options['compressed_rtl'];
			}

			$url = str_ireplace(DISCUSS_MEDIA, rtrim(JURI::root(), '/') . '/media/com_easydiscuss', $url);

			// Hash version to avoid cache
			$hash = md5(ED::getLocalVersion());

			$url = $url . '?' . $hash . '=1';

			return $url;
		}

		$result = $less->compileStylesheet($options);

		// System encountered error while compiling the admin themes
		if (!$result) {
			ED::setMessageQueue('Could not load stylesheet for default theme.', 'error');
		}

		// If the compilation failed, we need to capture the errors
		if ($result->failed) {

			// Use last compiled stylesheet.
			if (JFile::exists($result->out)) {
				ED::setMessageQueue('Could not compile stylesheet for default theme. Using last compiled stylesheet.', 'error');

				return $result->out_uri;
			}

			// Use failsafe stylesheet
			if (JFile::exists($result->failsafe)) {
				ED::setMessageQueue('Could not compile stylesheet for default theme. Using failsafe stylesheet.', 'error');
				return $result->failsafe_uri;
			}

			return false;
		}

		// Here we assume that the process was successful
		return $result->out_uri;
	}

	/**
	 * Determines if the site is in RTL mode
	 *
	 * @since	4.2.0
	 * @access	public
	 */
	public function isRTL()
	{
		$lang = JFactory::getLanguage();
		$rtl = $lang->isRTL();

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