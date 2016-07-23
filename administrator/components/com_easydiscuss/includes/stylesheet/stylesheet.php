<?php
/**
* @package		EasyDiscuss
* @copyright	Copyright (C) 2010 - 2015 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasyBlog is free software. This version may have been modified pursuant
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
	 * @param	string
	 * @return
	 */
	public function attach()
	{
        // RTL support
        $lang = JFactory::getLanguage();
        $rtl = $lang->isRTL();

        if ($rtl && $this->location == 'site') {
            $themeName = ED::themes()->getName();

            // check if site is now runing on production or not.
            $filename = 'style-rtl';
            if ($this->config->get('system_environment') == 'production') {
                $filename .= '.min';
            }

            $uri = DISCUSS_MEDIA_URI . '/themes/' . $themeName . '/css/' . $filename . '.css';
            $this->doc->addStyleSheet($uri);

            $this->attachCustomCss();

            return;
        }

		$uri = $this->compile();

		if ($this->location == 'site') {

			// @TODO:
			// 1. Check for template overrides
			// 2. Check settings to see if easydiscuss css files should be rendered at all

			// @TODO: During production mode, nothing should be executed at all. Just attach the css to the document.


			$this->attachCustomCss();
		}

		if ($this->location == 'admin') {
		}

		$this->doc->addStyleSheet($uri);
	}

	/**
	 * if there is a custom.css overriding, we need to attach this custom.css file.
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	private function attachCustomCss()
	{
		$path = JPATH_ROOT . '/templates/' . $this->app->getTemplate() . '/html/com_easydiscuss/css/custom.css';

		if (JFile::exists($path)) {
			$customURI = JURI::root() . 'templates/' . $this->app->getTemplate() . '/html/com_easydiscuss/css/custom.css';
			$this->doc->addStyleSheet($customURI);
		}
	}

	/**
	 * Responsible to compile the LESS > CSS file
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return
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
	 * @param	string
	 * @return
	 */
	public function compileSiteStylesheet($theme = null)
	{
		// Allow caller to specify a different stylesheet to compile
		if (is_null($theme)) {
			$theme = $this->config->get('layout_site_theme');
		}

		$options = array(
					'source' => DISCUSS_MEDIA . '/themes/' . $theme . '/less/style.less',
					'output' => DISCUSS_MEDIA . '/themes/' . $theme . '/css/style.css',
					'compressed' => DISCUSS_MEDIA . '/themes/' . $theme . '/css/style.min.css'
				);

		// For production mode, we simply just include the minified css file. Don't render anything else
		// Request compiler to compile less files
		if (!defined('ED_CLI') && $this->config->get('system_environment') == 'production') {
			return ED::assets()->toUri($options['compressed']);
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
	 * @param	string
	 * @return
	 */
	public function compileAdminStylesheet()
	{
		$less = ED::less();

		// Request compiler to compile less files
		$options = array(
					'source' => DISCUSS_MEDIA . '/themes/admin/less/style.less',
					'output' => DISCUSS_MEDIA . '/themes/admin/css/style.css',
					'compressed' => DISCUSS_MEDIA . '/themes/admin/css/style.min.css'
				);

		// For production mode, we simply just include the minified css file. Don't render anything else
		// Request compiler to compile less files
		if (!defined('ED_CLI') && $this->config->get('system_environment') == 'production') {
			return ED::assets()->toUri($options['compressed']);
		}

		// Compile
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
}
