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

require_once(__DIR__ . '/helpers/abstract.php');

class EasyDiscussThemes
{
	public $vars = array();
	public $params = null;
	public $admin = false;
	public $view = null;

	public function __construct($overrideTheme = null, $options = array())
	{
		$this->config = ED::config();
		$this->jconfig = ED::jConfig();
		$this->my = JFactory::getUser();
		
		// Determine if this is an admin location
		if (isset($options['admin']) && $options['admin']) {
			$this->admin = true;
		}

		$this->theme = 'wireframe';

		// If a view is provided into the theme, the theme files could call methods from a view
		if (isset($options['view']) && is_object($options['view'])) {
			$this->view = $options['view'];
		}

		$obj = new stdClass();
	}

	public function __get($key)
	{
		if ($key == 'acl') {
			return ED::acl();
		}

		if ($key == 'profile') {
			return ED::profile();
		}

		return $this->$key;
	}

	/**
	 * Escapes a string
	 *
	 * @since	5.0
	 * @access	public
	 */
	public function escape($val)
	{
		return ED::string()->escape($val);
	}

	/**
	 * Retrieves the path to the current theme.
	 *
	 * @since	5.0
	 * @access	public
	 */
	public function getPath()
	{
		$theme 	= (string) trim(strtolower($this->theme));

		return EBLOG_THEMES . '/' . $theme;
	}


	/**
	 * Renders module in a template
	 *
	 * @since	5.0
	 * @access	public
	 */
	public function renderModule($position, $attributes = array(), $content = null)
	{
		$doc = JFactory::getDocument();
		$renderer = $doc->loadRenderer('module');

		$buffer = '';
		$modules = JModuleHelper::getModules($position);

		foreach ($modules as $module) {
			$theme = EB::template();

			$theme->set('position', $position);
			$theme->set('output', $renderer->render($module, $attributes, $content));

			$buffer .= $theme->output('site/modules/item');
		}

		return $buffer;
	}

	/**
	 * Retrieves the document direction. Whether this is rtl or ltr
	 *
	 * @since	4.0
	 * @access	public
	 */
	public function getDirection()
	{
		$document	= JFactory::getDocument();
		return $document->getDirection();
	}

	/**
	 * Converts the entire output into a JSON object.
	 *
	 * @since	4.0
	 * @access	public
	 */
	public function toJSON()
	{
		$output = json_encode($this->vars);

		return $output;
	}

	public function getNouns($text , $count , $includeCount = false )
	{
		return ED::string()->getNoun($text, $count, $includeCount);
	}

	public function getParam($key, $default = null)
	{
		return $this->params->get($key, $default);
	}

	/**
	 * Determines if this is a mobile layout
	 *
	 * @since	4.2.0
	 * @access	public
	 */
	public function isMobile()
	{
		static $mobile = null;

		if (is_null($mobile)) {
			$mobile = ED::responsive()->isMobile();
		}

		return $mobile;
	}

	/**
	 * Determines if this is a mobile layout
	 *
	 * @since	4.2.0
	 * @access	public
	 */
	public function isTablet()
	{
		static $tablet = null;

		if (is_null($tablet)) {
			$tablet = ED::responsive()->isTablet();
		}

		return $tablet;
	}

	/**
	 * Returns the class for related devices.
	 *
	 * @since	2.2
	 * @access	public
	 */
	public function responsiveClass()
	{
		static $loaded = false;

		if (!$loaded) {
			$loaded = 'is-desktop';

			if ($this->isMobile()) {
				$loaded = 'is-mobile';
			}

			if ($this->isTablet()) {
				$loaded = 'is-tablet';
			}
		}

		return $loaded;
	}

	/**
	 * Retrieves the theme's name.
	 *
	 * @since	4.0
	 * @access	public
	 */
	public function getName()
	{
		return $this->theme;
	}

	/**
	 * New method to display contents from template files
	 *
	 * @since	4.0
	 * @access	public
	 */
	public function output($namespace, $vars = array(), $extension = 'php')
	{
		$path = $this->resolve($namespace);
		$extension = '.' . $extension;

		// Extract internal vars first
		if (isset($this->vars)) {
			extract($this->vars);
		}

		// Extract template variables
		if (!empty($vars)) {
			extract($vars);
		}

		$templateFile = $path . $extension;
		$templateContent = '';

		ob_start();
		include($templateFile);
		$templateContent = ob_get_contents();
		ob_end_clean();

		// Embed script for template
		$scriptFile = $path . '.js';

		// Generate a uid for the script file
		$uid = md5($scriptFile);

		// Check if the script already exists on the namespace so we don't run it multiple times
		$exists = ED::scripts()->exists($uid);

		if (!$exists) {

			$scriptFileExists = file_exists($scriptFile);

			if ($scriptFileExists) {

				ob_start();

					if ($this->config->get('system_environment') == 'development') {
						echo '<script data-src="' . $scriptFile . '">';
					} else {
						echo '<script>';
					}

					include($scriptFile);
					echo '</script>';
					$scriptContent = ob_get_contents();
				ob_end_clean();

				// Add to collection of scripts
				$doc = JFactory::getDocument();
				
				if ($doc->getType() == 'html') {
					ED::scripts()->add($uid, $scriptContent);
				} else {

					// Append script to template content
					// if we're not on html document (ajax).
					$templateContent .= $scriptContent;
				}
			}
		}


		return $templateContent;
	}

	/**
	 * Retrieves the images path for the current template
	 *
	 * @since	4.0
	 * @access	public
	 */
	public function getPathUri($location)
	{
		if ($this->admin) {

			$path = rtrim(JURI::root(), '/') . '/administrator/components/com_easyblog/themes/default/' . ltrim($location, '/');

			return $path;
		}
	}

	/**
	 * Template helper
	 *
	 * @since	1.0
	 * @access	public
	 */
	public function html($namespace)
	{
		static $classes = array();

		$helper = explode('.', $namespace);
		$helperName	= $helper[0];
		$methodName	= $helper[1];

		// Remove the first 2 arguments from the args.
		$args = func_get_args();
		$args = array_splice($args, 1);

		if (!isset($classes[$helperName])) {
			$file = __DIR__ . '/helpers/' . strtolower($helperName) . '.php';

			include_once($file);

			$class = 'EasyDiscussThemesHelper' . ucfirst($helperName);

			$classes[$helperName] = new $class();
		}

		$object = $classes[$helperName];

		if (!method_exists($object, $methodName)) {
			return false;
		}

		return call_user_func_array(array($object, $methodName), $args);
	}

	public function __call($method, $args)
	{
		if (is_null($this->view)) {
			return false;
		}

		if (!method_exists($this->view, $method)) {
			return false;
		}

		return call_user_func_array(array($this->view, $method), $args);
	}

	/**
	 * Resolves a given namespace to the appropriate path
	 *
	 * @since	4.0
	 * @access	public
	 */
	public function resolve($namespace = '', $checkOverridden = true)
	{
		$parts = explode('/', $namespace);
		$explodedParts = $parts;
		$location = $explodedParts[0];
		$path = '';
		$extension = '.php';

		// Remove the first porition from the exploded parts since we don't need this
		unset($parts[0]);

		// For admin theme files
		if ($location == 'admin') {
			$path = DISCUSS_ADMIN_THEMES . '/default/' . implode('/', $parts);

			return $path;
		}

		// For site theme folders
		// Implode the parts back to form the namespace
		$namespace = implode('/', $parts);

		// Get the default template from frontend
		$defaultJoomlaTemplate = ED::getCurrentTemplate();

		if ($checkOverridden) {
			// Overriden Theme
			$path = JPATH_ROOT . '/templates/' . $defaultJoomlaTemplate . '/html/com_easydiscuss/' . $namespace;
			$exists = file_exists($path . $extension);

			if ($exists) {
				return $path;
			}
		}

		// Current Theme
		$path = DISCUSS_THEMES . '/' . $this->theme . '/' . $namespace;
		$exists = file_exists($path . $extension);

		if (!$exists) {

			// lets fall back to wireframe theme
			$path = DISCUSS_THEMES . '/wireframe/' . $namespace;
			$exists = file_exists($path . $extension);

			if (!$exists) {
				throw ED::exception(JText::sprintf('The file you requested for does not exists, %1$s', $path . $extension), ED_MSG_ERROR);
			}

		}

		return $path;
	}

	/**
	 * Sets a variable on the template
	 *
	 * @since	4.0
	 * @access	public
	 */
	public function set($name, $value)
	{
		$this->vars[$name] = $value;
	}

	/**
	 * Allows caller to set a custom theme
	 *
	 * @since	4.0
	 * @access	public
	 */
	public function setCategoryTheme($theme)
	{
		$this->categoryTheme = $theme;
	}

	/**
	 * Generates text/avatar image class 
	 *
	 * @since	4.1.9
	 * @access	public	
	 */
	public function renderAvatarClass($user)
	{
		static $cache = [];

		if (!isset($cache[$user->id])) {
			$config = ED::config();
			$useTextAvatar = $config->get('layout_text_avatar');

			$avatarClass = '';

			if ($useTextAvatar) {
				$avatarClass = 'o-avatar--text ' . 'o-avatar--bg-' .  $user->getNameInitial()->code;
			}

			$cache[$user->id] = $avatarClass;
		}

		return $cache[$user->id];
	}	
}
