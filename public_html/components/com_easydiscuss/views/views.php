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

jimport('joomla.application.component.view');

class EasyDiscussView extends JViewLegacy
{
	/**
	 * Main definitions for view should be here
	 *
	 * @since	4.0
	 * @access	public
	 */
	public function __construct()
	{
		$this->app = JFactory::getApplication();
		$this->doc = JFactory::getDocument();
		$this->input = ED::request();
		$this->config = ED::config();
		$this->jconfig = ED::jconfig();
		$this->my = JFactory::getUser();
		$this->theme = ED::themes();

		if ($this->doc->getType() == 'ajax') {
			$this->ajax = ED::ajax();
		}

		// $this->isAdmin = ED::isSiteAdmin();

		// If there is a check feature method on subclasses, we need to call it
		if (method_exists($this, 'isFeatureAvailable')) {
			$available = $this->isFeatureAvailable();

			if (!$available) {
				throw ED::exception('COM_EASYDISCUSS_FEATURE_IS_NOT_ENABLED', ED_MSG_ERROR);
			}
		}
		parent::__construct();
	}

	public function __get($key)
	{
		if ($key == 'acl' && !isset($this->acl) || !$this->acl) {
			$this->acl = ED::acl();
		}

		if (isset($this->$key)) {
			return $this->$key;
		}
	}

	/**
	 * Allows child to set variables
	 *
	 * @since	4.0
	 * @access	public
	 */
	public function set($key, $value = '')
	{
		$this->theme->set($key, $value);
	}

	/**
	 * Allows child classes to set the pathway
	 *
	 * @since	4.0
	 * @access	public
	 */
	public function setPathway($title, $link = '')
	{
		JFactory::getLanguage()->load('com_easydiscuss', JPATH_ROOT);

		// Always translate the title
		$title = JText::_($title);

		$pathway = $this->app->getPathway();

		return $pathway->addItem($title, $link);
	}

	/**
	 * Generate a rel tag on the header of the page
	 *
	 * @since   5.0.0
	 * @access  public
	 */
	public function amp($url, $route = true)
	{
		if ($route) {
			$url = EDR::_($url);
		}

		$this->doc->addHeadLink($this->escape($url), 'amphtml');
	}

	/**
	 * The main invocation should be here.
	 *
	 * @since	4.0
	 * @access	public
	 */
	public function display($tpl = null)
	{
		$docType = $this->doc->getType();
		$format = $this->input->get('format', 'html', 'word');
		$view = $this->getName();
		$layout = $this->getLayout();

		// If the document type is not html based, we don't want to include other stuffs
		if ($format == 'json') {
			header('Content-type: text/x-json; UTF-8');
			echo $this->theme->toJSON();
			exit;
		}

		$tpl = 'site/' . $tpl;
		$config = ED::config();

		// Only proceed here when we know this is a html request
		if ($format == 'html') {

			// Initialize whatever that is necessary
			ED::init('site');

			// If integrations with ES conversations is enabled, we need to render it's scripts
			$easysocial = ED::easysocial();

			if ($this->config->get('integration_easysocial_messaging') && $easysocial->exists()) {
				$easysocial->init();
			}

			$bbcodeSettings = $this->theme->output('site/composer/editors/bbcode.settings');

			// Get the contents of the view.
			$contents = $this->theme->output($tpl);

			// attached bbcode settings
			$contents = $bbcodeSettings . $contents;

			// We need to output the structure
			$theme = ED::themes();

			// RTL support
			$lang = JFactory::getLanguage();
			$rtl = $lang->isRTL();

			// Class suffix
			$suffix = $this->config->get('layout_wrapper_sfx', '');

			// Category classes
			$categoryId = $this->input->get('category_id', 0, 'int');
			$categoryClass = $categoryId ? ' category-' . $categoryId : '';

			$toolbar = '';

			if ($config->get('layout_enabletoolbar')) {
				// Retrieve the toolbar for EasyDiscuss
				$toolbar = $this->getToolbar();
			}

			// Set the ajax url
			$ajaxUrl = ED::getAjaxUrl();

			// Load easysocial headers when viewing posts of another person
			$clusterHeader = '';
			$clusterId = '';

			if ($view == 'post') {
				$id = $this->input->get('id', 0, 'int');
				$post = ED::post($id);

				$clusterId = $post->cluster_id;
			}

			if ($clusterId) {
				$clusterHeader = $easysocial->renderMiniHeader($clusterId, $view);
			}

			// Message queue
			$messageObject = ED::getMessageQueue();

			// Remap message object
			if ($messageObject) {
				if ($messageObject->type == 'error') {
					$messageObject->type = 'danger';
				}
			}

			$heading = $this->getHeading();

			$theme->set('toolbar', $toolbar);
			$theme->set('heading', $heading);
			$theme->set('messageObject', $messageObject);
			$theme->set('categoryClass', $categoryClass);
			$theme->set('suffix', $suffix);
			$theme->set('rtl', $rtl);
			$theme->set('contents', $contents);
			$theme->set('layout', $layout);
			$theme->set('view', $view);
			$theme->set('ajaxUrl', $ajaxUrl);

			$output = $theme->output('site/structure/default');

			// Get the scripts
			$scripts = ED::scripts()->getScripts();


			echo $output;
			echo $scripts;
			return;
		}

		return parent::display($tpl);
	}

	/**
	 * Generate a canonical tag on the header of the page
	 *
	 * @since	4.0
	 * @access	public
	 */
	public function canonical($url, $route = true)
	{
		if ($route) {
			$url = EDR::getRoutedURL($url, true, true);
		}
		
		$this->doc->addHeadLink($this->escape($url), 'canonical');
	}

	/**
	 * Retrieves heading from Joomla menu
	 *
	 * @since	5.0.0
	 * @access	public
	 */
	public function getHeading()
	{
		static $heading = null;

		if (is_null($heading)) {

			$app = JFactory::getApplication();
			$params = $app->getParams();
			$heading = $params->get('show_page_heading', false);

			if ($heading) {
				$heading = $params->get('page_heading', '');
			}
		}

		return $heading;
	}

	/**
	 * Generates the toolbar's html code
	 *
	 * @since	4.0
	 * @access	public
	 */
	public function getToolbar()
	{
		$toolbar = ED::toolbar();
		return $toolbar->render();
	}

	/**
	 * Log viewers
	 *
	 * @since	4.0
	 * @access	public
	 */
	public function logView($uri = '')
	{
		$my	= JFactory::getUser();

		if ($my->id > 0) {
			$db = ED::db();
			$query = 'SELECT `id` FROM ' . $db->nameQuote('#__discuss_views');
			$query .= ' WHERE ' . $db->nameQuote('user_id') . '=' . $db->Quote($my->id);

			$db->setQuery($query);
			$id = $db->loadResult();

			$hash = md5(EDFactory::getURI(true));

			if ($uri) {
				$hash = md5($uri);
			}

			if (!$id) {
				// Create a new log view
				$view = ED::table('Views');
				$view->updateView($my->id, $hash);
			} else {
				$query = 'UPDATE ' . $db->nameQuote('#__discuss_views');
				$query .= ' SET ' . $db->nameQuote('hash') . '=' . $db->Quote($hash);
				$query .= ', ' . $db->nameQuote('created') . '=' . $db->Quote(ED::date()->toSql());
				$query .= ', ' . $db->nameQuote('ip') . '=' . $db->Quote($_SERVER[ 'REMOTE_ADDR' ]);
				$query .= ' WHERE ' . $db->nameQuote('id') . '=' . $db->Quote($id);

				$db->setQuery($query);
				$db->query();
			}
		}
	}
}
