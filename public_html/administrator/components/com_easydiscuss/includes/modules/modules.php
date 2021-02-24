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

class EasyDiscussModules extends EasyDiscuss
{
	// Contains the name of the module
	private $name = '';
	private $module = null;
	private $params = null;
	private $baseurl = null;

	public function __construct($module = '')
	{
		parent::__construct();

		$this->module = $module;

		if ($this->module) {
			$this->name = $this->module->module;
			$this->params = new JRegistry($this->module->params);
		}

		$this->baseurl = JURI::root(true);
	}

	/**
	 * Allows module to attach script files on the site
	 *
	 * @since	5.0.0
	 * @access	public
	 */
	public function addScript($file)
	{
		static $items = array();

		$key = md5($this->name . $file);

		if (!isset($items[$key])) {

			$baseurl = JURI::root(true);
			$uri = $baseurl . '/modules/' . $this->name . '/scripts/' . $file;

			ED::compiler()->addScriptUrl($uri);

			$items[$key] = true;
		}

		return $items[$key];
	}

	/**
     * Format the discussion posts data
     *
     * @since   4.0
     * @access  public
     */
	public function format($posts)
	{
		if (!$posts) {
			return;
		}

		$results = array();

		foreach ($posts as $post) {
			$result = ED::post($post->id);

			// Retrieve the last reply data
			$model = ED::model('Posts');
			$lastReply = $model->getLastReply($result->id);
			
			$result->lastReply = $lastReply;

			// Assign author info
			$result->user = $result->getOwner();

			// Get the post created date
			$date = ED::date($result->created);

			$result->date = $date->display(JText::_('DATE_FORMAT_LC1'));

			$results[] = $result;
		}

		return $results;
	}

    /**
     * Method to get the data from modules
     *
     * @since   4.0
     * @access  public
     */
	public function getData($options = array())
	{
		$params = $options['params'];
		$sort = isset($options['sort']) ? $options['sort'] : 'latest';

		$count = (INT)trim($params->get('count', 0));
		$selectedCategories = $params->get('category_id', 0);
		$categoryIds = is_string($selectedCategories) ? trim($selectedCategories) : $selectedCategories; 

		if (is_string($categoryIds)) {
			// Remove white space
			$categoryIds = preg_replace('/\s+/', '', $categoryIds);
			$categoryIds = explode( ',', $categoryIds );
		}

		$model = ED::model('Posts');

		// If category id is exists, let just load the post by categories.
		if ($categoryIds) {
			$posts = $model->getPostsBy('category', $categoryIds, $sort, null, DISCUSS_FILTER_PUBLISHED, '', $count);
			$posts = $this->format($posts);

			return $posts;
		}

		$posts = $model->getPostsBy('', '', $sort, null, DISCUSS_FILTER_PUBLISHED, '', $count);
		$posts = $this->format($posts);

		return $posts;
	}

	/**
	 * Retrieves the class wrapper for the module
	 *
	 * @since	5.0.0
	 * @access	public
	 */
	public function getModuleWrapperClass($default = '')
	{
		$suffix = '';

		$suffix .= $this->responsiveClass();

		// Dark / light mode
		$mode = $this->config->get('layout_darkmode') ? 'dark' : 'light';
		$suffix .= ' si-theme--' . $mode;

		$moduleSuffix = $this->params->get('suffix', '');

		if ($moduleSuffix) {
			$suffix .= ' ' . $moduleSuffix;
		}

		// Standard suffix
		$standardSuffix = $this->params->get('moduleclass_sfx', '');

		if ($standardSuffix) {
			$suffix .= ' ' . $standardSuffix;
		}

		return $suffix;
	}

	/**
     * Retrieve return URL
     *
     * @since   4.0
     * @access  public
     */
	public function getReturnURL($params, $isLogged = false)
	{
		$type = empty($isLogged) ? 'login' : 'logout';

		$itemId = $params->get($type);

		// Default to stay on the same page.
		$return = EDR::getCurrentURI();

		if ($itemId) {

			$menu = JFactory::getApplication()->getMenu();
			$item = $menu->getItem($itemId);
			
			if ($item) {
				$return = $item->link . '&Itemid=' . $itemId;
			}
		}

		return base64_encode($return);
	}

	/**
     * Get login status
     *
     * @since   4.0
     * @access  public
     */
	public function getLoginStatus()
	{
		$user = JFactory::getUser();
		return (!empty($user->id)) ? true : false;
	}

	/**
	 * Includes the helper for the module
	 *
	 * @since	5.0.0
	 * @access	public
	 */
	public function getHelper($useCache = true)
	{
		static $helpers = array();

		$key = $this->name;

		if (!isset($helpers[$key]) || !$useCache) {
			$path = $this->getPath() . '/helper.php';

			$exists = JFile::exists($path);
			$helpers[$key] = false;

			if (!$exists) {
				return $helpers[$key];
			}

			require_once($path);
			$name = str_ireplace('mod_easydiscuss_', '', $key);
			$name = ucfirst($name);

			$className = 'EasyDiscussMod' . $name . 'Helper';

			if (!class_exists($className)) {
				return $helpers[$key];
			}

			$helpers[$key] = new $className($this->params);
		}

		return $helpers[$key];
	}

	/**
	 * Retrieves the layout set in the module
	 *
	 * @since	5.0.0
	 * @access	public
	 */
	public function getLayout($default = 'default')
	{
		$layout = $this->params->get('layout', $default);

		$output = JModuleHelper::getLayoutPath($this->name, $layout);

		return $output;
	}

	/**
	 * Retrieves the path of the plugin
	 *
	 * @since	5.0.0
	 * @access	public
	 */
	public function getPath($relative = false)
	{
		$path = '';

		if (!$relative) {
			$path .= JPATH_ROOT;
		}

		$path .= '/modules/' . $this->name;

		return $path;
	}

	/**
	 * Determines if this is a mobile layout
	 *
	 * @since	5.0.0
	 * @access	public
	 */
	public function isMobile()
	{
		$responsive = null;

		if (is_null($responsive)) {
			$responsive = ED::responsive()->isMobile();
		}

		return $responsive;
	}

	/**
	 * Determines if this is a mobile layout
	 *
	 * @since	5.0.0
	 * @access	public
	 */
	public function isTablet()
	{
		$responsive = null;

		if (is_null($responsive)) {
			$responsive = ED::responsive()->isTablet();
		}

		return $responsive;
	}

	/**
	 * Load helpers for the module
	 *
	 * @since	5.0.0
	 * @access	public
	 */
	public function html()
	{
		$theme = ED::themes();
		$args = func_get_args();

		$output = call_user_func_array(array($theme, 'html'), $args);

		return $output;
	}

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
}