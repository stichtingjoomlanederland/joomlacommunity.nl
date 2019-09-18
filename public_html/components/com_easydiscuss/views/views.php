<?php
/**
* @package		EasyDiscuss
* @copyright	Copyright (C) 2010 - 2018 Stack Ideas Sdn Bhd. All rights reserved.
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
		$this->ajax = ED::ajax();
		$this->config = ED::config();
		$this->jconfig = ED::jconfig();
		$this->doc = JFactory::getDocument();
		$this->app = JFactory::getApplication();
		$this->input = ED::request();
		$this->theme = ED::themes();
		$this->my = JFactory::getUser();
		$this->profile = ED::profile();
		$this->acl = ED::acl();
		$this->isAdmin = ED::isSiteAdmin();

		// If there is a check feature method on subclasses, we need to call it
		if (method_exists($this, 'isFeatureAvailable')) {
			$available = $this->isFeatureAvailable();

			if (!$available) {
				return JError::raiseError(500, JText::_('COM_EASYDISCUSS_FEATURE_IS_NOT_ENABLED'));
			}
		}
		parent::__construct();
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

		// Only proceed here when we know this is a html request
		if ($format == 'html') {

			// Initialize whatever that is necessary
			ED::init('site');

			// Render custom css
			$this->renderCustomCss();

			// If integrations with ES conversations is enabled, we need to render it's scripts
			$easysocial = ED::easysocial();
			
			if ($this->config->get('integration_easysocial_messaging') && $easysocial->exists()) {
				$easysocial->init();
			}

			$bbcodeSettings = $this->theme->output('admin/structure/settings');

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

			// Retrieve the toolbar for EasyDiscuss
			$toolbar = $this->getToolbar();

			// Jomsocial toolbar
			$jsToolbar = ED::jomsocial()->getToolbar();

			// Set the ajax url
			$ajaxUrl = ED::getAjaxUrl();

			// Load easysocial headers when viewing posts of another person
			$miniheader = '';
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

			// Only work for Easysocial 2.0. 
			// Only display if there is no cluster header.
			if ($view == 'post' && $this->config->get('integration_easysocial_mini_header', true) && $easysocial->exists() && !$easysocial->isLegacy() && !$clusterHeader && !$post->isAnonymous()) {

				ES::initialize();

				if (ES::user()->hasCommunityAccess()) {
					$user = ES::user($post->getOwner()->id);

					$miniheader = ES::themes()->html('html.miniheader', $user);
				}
			}

			$theme->set('miniheader', $miniheader);
			$theme->set('toolbar', $toolbar);
			$theme->set('categoryClass', $categoryClass);
			$theme->set('suffix', $suffix);
			$theme->set('rtl', $rtl);
			$theme->set('contents', $contents);
			$theme->set('layout', $layout);
			$theme->set('view', $view);
			$theme->set('ajaxUrl', $ajaxUrl);
			$theme->set('jsToolbar', $jsToolbar);

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
	 * Renders custom css on the page
	 *
	 * @since	4.2.0
	 * @access	public
	 */
	public function renderCustomCss()
	{
		// Render the custom styles
		$theme = ED::themes();
		$customCss = $theme->output('site/structure/css');

		// This custom css doesn't need to render on the composer page
		$customCss = ED::minifyCSS($customCss);

		$this->doc->addCustomTag($customCss);
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

	public function logView()
	{
		$my		= JFactory::getUser();

		if( $my->id > 0 )
		{
			$db 		= DiscussHelper::getDBO();
			$query 		= 'SELECT `id` FROM ' . $db->nameQuote( '#__discuss_views' );
			$query 		.= ' WHERE ' . $db->nameQuote( 'user_id' ) . '=' . $db->Quote( $my->id );

			$db->setQuery( $query );
			$id		= $db->loadResult();

			$hash 		= md5( JRequest::getURI() );
			if( !$id )
			{
				// Create a new log view
				$view 	= DiscussHelper::getTable( 'Views' );
				$view->updateView( $my->id , $hash );
			}
			else
			{
				$query 	= 'UPDATE ' . $db->nameQuote( '#__discuss_views' );
				$query 	.= ' SET ' . $db->nameQuote( 'hash' ) . '=' . $db->Quote( $hash );
				$query	.= ', ' . $db->nameQuote( 'created' ) . '=' . $db->Quote( ED::date()->toSql() );
				$query	.= ', ' . $db->nameQuote( 'ip' ) . '=' . $db->Quote( $_SERVER[ 'REMOTE_ADDR' ] );
				$query  .= ' WHERE ' . $db->nameQuote( 'id' ) . '=' . $db->Quote( $id );

				$db->setQuery( $query );
				$db->query();
			}

		}
	}
}
