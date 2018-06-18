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

jimport('joomla.plugin.plugin');
jimport('joomla.filesystem.file');

class plgContentEasyDiscuss extends JPlugin
{
	var $extension = null;
	var $view = null;
	var $loaded = null;

	public function __construct(&$subject , $params)
	{
		$this->extension = JRequest::getString('option');
		$this->view = JRequest::getString('view');

		// Load language file for use throughout the plugin
		JFactory::getLanguage()->load('com_easydiscuss', JPATH_ROOT);

		parent::__construct($subject, $params);
	}

	/**
	 * Tests if EasyBlog exists
	 *
	 * @since	4.0
	 * @access	private
	 */
	private function exists()
	{
		static $exists = null;

		if (is_null($exists)) {
			$file = JPATH_ADMINISTRATOR . '/components/com_easydiscuss/includes/easydiscuss.php';
			$exists = JFile::exists($file);

			if ($exists) {
				require_once($file);
			}
		}

		return $exists;
	}

	/**
	 * Update the content of discussion whenever the article is being edited
	 *
	 * @since	4.0
	 * @access	public
	 */
	public function onAfterContentSave(&$article, $isNew)
	{
		if (!$this->exists()) {
			return;
		}

		// If this post coming from EasyArticle, we should store it as com_content
		if ($this->extension == 'com_easyarticles') {
			$this->extension = 'com_content';
		}

		// If the current page is easydiscuss, we want to skip this altogether.
		// We also need to skip this when the plugins are being triggered in the discussion replies otherwise it will
		// be in an infinite loop generating all contents.
		if($this->extension == 'com_easydiscuss' || $this->loaded || (isset($article->easydiscuss) && $article->easydiscuss == true) || (isset($article->state) && !$article->state && $this->extension == 'com_content')) {
			return;
		}

		$params = $this->getParams();

		$allowed = $params->get('allowed_components' , 'com_content,com_easyblog');
		$allowed = explode(',' , $allowed);

		// Include com_easydiscuss
		$allowed[] = 'com_easydiscuss';

		if (!in_array($this->extension, $allowed)) {
			return;
		}

		if (!$this->categoryCheck($article)) {
			return;
		}

		$this->mapExisting($article);

		return;
	}

	/**
	 * Check for categories
	 *
	 * @since   4.0
	 * @access  public
	 */
	public function categoryCheck($article)
	{
		// Only check for joomla article
		if ($this->extension != 'com_content') {
			return true;
		}
		
		$params = $this->getParams();

		// Check for category exclusion/inclusion
		$excludedCategories = $params->get('exclude_category');

		if (!is_array($excludedCategories)) {
			$excludedCategories	= explode(',', $excludedCategories);
		}

		if (in_array($article->catid, $excludedCategories)) {
			return false;
		}

		$allowedCategories = $params->get('include_category');

		if ($allowedCategories || !empty($allowedCategories)) {

			if (!is_array($allowedCategories)) {
				$allowedCategories 	= explode(',', $allowedCategories);
			}

			if (!in_array($article->catid , $allowedCategories)) {
				return false;
			}
		}

		return true;
	}

	/**
	 * onContentAfterSave trigger for Joomla 1.6 onwards.
	 *
	 **/
	public function onContentAfterSave($context, $article, $isNew)
	{
		return $this->onAfterContentSave($article , $isNew);
	}

	/**
	 * onContentAfterDisplay trigger for Joomla 1.6 onwards.
	 *
	 **/
	public function onContentAfterDisplay($context , &$article, &$params, $page = 0)
	{
		// Since easyblog is already triggering onDisplayComments , ride on that trigger instead
		if ($this->extension == 'com_easyblog') {
			return;
		}

		return $this->onAfterDisplayContent($article , $params , $page);
	}

	/**
	 * Triggers for EasyBlog comment integration
	 *
	 * @since	4.0
	 * @access	public
	 */
	public function onDisplayComments(&$blog , &$params)
	{
		$blog->catid = $blog->category_id;

		return $this->onAfterDisplayContent($blog, $params, 0, __FUNCTION__);
	}

	/**
	 * Trigger after the content is displayed.
	 *
	 * @since	4.0
	 * @access	public
	 */
	public function onAfterDisplayContent(&$article, &$articleParams, $limitstart , $trigger = '')
	{
		if (!$this->exists()) {
			return;
		}

		$app = JFactory::getApplication();
		$params = $this->getParams();

		$allowed = $params->get('allowed_components', 'com_content,com_easyblog');
		$allowed = explode(',' , $allowed);

		if (!in_array($this->extension, $allowed) || !$article->id) {
			return '';
		}

		// If the current page is easydiscuss, we want to skip this altogether.
		// We also need to skip this when the plugins are being triggered in the discussion replies otherwise it will
		// be in an infinite loop generating all contents.
		if ($this->extension == 'com_easydiscuss' ||
			$this->loaded ||
			(isset($article->easydiscuss) && $article->easydiscuss == true) ||
			($this->extension == 'com_content' && !$article->state)) {
			return;
		}

		if ($this->extension == 'com_easyblog') {
			$inputs = ED::request();
			$view = $inputs->get('view', '', 'cmd');
			$id = $inputs->get('id', 0, 'int');

			// We only process the discussion in entry view
			if ($view != 'entry' || !$id) {
				return;
			}
		}

		// @rule: Test for exclusions on the categories
		$excludedCategories	= $params->get('exclude_category');

		if (!is_array($excludedCategories)) {
			$excludedCategories	= explode(',', $excludedCategories);
		}

		if (in_array($article->catid , $excludedCategories)) {
			return '';
		}

		// @rule: Test for exclusions on the article id.
		$excludedArticles = trim($params->get('exclude_articles'));

		if (!empty($excludedArticles)) {
			$excludedArticles = explode(',', $excludedArticles);

			if (in_array($article->id, $excludedArticles)) {
				return '';
			}
		}

		// @rule: Test for inclusions on the categories
		$allowedCategories = $params->get('include_category');

		if (is_array($allowedCategories)) {
			$allowedCategories = implode(',', $allowedCategories);
		}

		$allowedCategories = trim($allowedCategories);

		if ($allowedCategories != 'all' && !empty($allowedCategories) && $this->extension == 'com_content') {
			$allowedCategories 	= explode(',', $allowedCategories);

			if (!in_array($article->catid , $allowedCategories)) {
				return '';
			}
		}

		// Get the mapping
		$ref = ED::table('PostsReference');
		$exists = $ref->loadByExtension($article->id, $this->extension);

		if (!$exists) {
			// Map the article into EasyDiscuss
			$this->mapExisting($article);

			$ref = ED::table('PostsReference');
			$ref->loadByExtension($article->id, $this->extension);
		}

		// Load the discussion item
		$post = ED::post($ref->post_id);

		if (!$post->published) {
			return;
		}

		// Load css file
		$this->attachHeaders();

		if ($this->isFrontpage()) {
			$this->addFrontpageTools($article , $post);

		} else {
			$this->loaded	= true;

			// Exception to easyblog
			if ($this->extension == 'com_easyblog') {
				$html = $this->addResponses($article, $post);

				return $html;
			} else {
				// Show normal discussions data
				$this->addResponses($article, $post);
			}
		}

		return '';
	}

	/**
	 * Return paramenter of the plugins in form of object
	 *
	 * @since	4.0
	 * @access	public
	 */
	public function getParams()
	{
		static $params = null;

		if (!$params) {
			$plugin = JPluginHelper::getPlugin('content', 'easydiscuss');
			$params = DiscussHelper::getRegistry($plugin->params);
		}

		return $params;
	}

	/**
	 * Attaches the plugin's css file.
	 *
	 * @since	4.0
	 * @access	private
	 */
	private function attachHeaders()
	{
		static $loaded = false;

		if (!isset($loaded[$this->extension])) {
			ED::init();

			$doc = JFactory::getDocument();
			$path = rtrim(JURI::root() , '/') . '/plugins/content/easydiscuss/css/styles.css';
			$doc->addStyleSheet($path);

			$loaded[$this->extension] = true;
		}

		return $loaded[$this->extension];
	}

	private function isFrontpage()
	{
		switch($this->extension)
		{
			case 'com_content':
				return ($this->view == 'frontpage') || $this->view == 'featured';
			break;
			case 'com_k2':
				return $this->view == 'latest' || $this->view == 'itemlist';
			break;
			case 'com_easyblog':
				return ($this->view == 'latest');
			break;
		}
		return false;
	}

	/**
	 * Adds some nifty contents into the frontpage listing of com_content.
	 *
	 * @since	4.0
	 * @access	public
	 **/
	public function addFrontpageTools(&$article , &$post)
	{
		$params = $this->getParams();

		// Just return if it's not needed.
		if (!$params->get('frontpage_tools', true)) {
			return $article;
		}

		$total = $post->getTotalReplies();
		$url = $this->getArticleURL($article);
		$hits = $this->getArticleHits($article);
		$config = ED::config();
		$my = JFactory::getUser();

		ob_start();
		include($this->getTemplatePath('frontpage.php'));
		$contents = ob_get_contents();
		ob_end_clean();

		// EasyBlog specifically uses 'text'
		if ($this->extension == 'com_easyblog') {
			$article->text .= $contents;
			return $article;
		}

		$article->introtext .= $contents;

		return $article;
	}

	/**
	 * Returns the formatted date which is required during the output.
	 * The resultant date includes the offset.
	 *
	 * @since	4.0
	 * @access	public
	 */
	public function formatDate($format, $dateString)
	{
		$output = ED::date($dateString)->display($format);
		return $output;
	}

	/**
	 * Attaches the response and form in the article.
	 *
	 * @since	4.0
	 * @access	public
	 */
	public function addResponses(&$article, &$post)
	{
		if (!$this->exists()) {
			return false;
		}

		$my = ED::user();

		// Check for permission to view the discussion
		if (!$post->canView($my->id) || !$post->isPublished()) {
			return false;
		}		

		$params = $this->getParams();

		$model = ED::model('Posts');
		$config = ED::config();
		// $my = JFactory::getUser();
		$acl = ED::acl();

		// Get composer
		$opts = array('replying', $post);
		$composer = ED::composer($opts);

		$repliesLimit = $params->get('items_count', 5);

		$totalReplies = $post->getTotalReplies();

		$hasMoreReplies = false;

		$limitstart = null;
		$limit = null;

		if ($repliesLimit) {
			$limit = $repliesLimit;
		}

		$sort = ED::request()->get('sort', ED::getDefaultRepliesSorting(), 'word');
		$limitstart = ED::request()->get('limitstart', 0);

		$replies = $post->getReplies(true, $limit, $sort, $limitstart);

		// Get the pagination for replies
		$pagination = $model->getPagination();

		$isMainLocked 	= false;
		$canDeleteReply = false;

		// Load the category.
		$category = ED::table('Category');
		$category->load((int) $post->category_id);

		$canReply = ((($my->id != 0) || ($my->id == 0 && $config->get('main_allowguestpost'))) && $acl->allowed('add_reply', '0')) ? true : false;

		$system = new stdClass();
		$system->config	= ED::config();
		$system->my = $my;
		$system->acl = $acl;

		// add bbcode settings - DO NOT MOVE THIS LINE
		$bbcodeSettings = ED::themes()->output('admin/structure/settings');

		// DO NOT MOVE THESE LINE
		ob_start();
		include(dirname(__FILE__) . '/tmpl/default.php');
		$contents	= ob_get_contents();
		ob_end_clean();

		// DO NOT MOVE THIS LINE
		$scripts = ED::scripts()->getScripts();

		$htmlContent = $bbcodeSettings . $contents . $scripts;

		$article->text .= $htmlContent;

		return $htmlContent;
	}

	/**
	 * Returns the URL to a specific article in Joomla.
	 *
	 * @since	4.0
	 * @access	private
	 */
	private function getArticleURL(&$article)
	{
		$uri = JURI::getInstance();
		$sefEnabled = EDR::isSefEnabled();

		switch($this->extension)
		{
			 case 'com_content':

				require_once(JPATH_ROOT . '/components/com_content/helpers/route.php');

				JTable::addIncludePath(JPATH_ROOT . '/libraries/joomla/database/table');

				$category = JTable::getInstance('Category' , 'JTable');
				$category->load($article->catid);

				$url = ContentHelperRoute::getArticleRoute($article->id . ':' . $article->alias , $article->catid);

				// Check for SEF enabled
				if ($sefEnabled) {

					$router = new JRouterSite(array('mode'=>JROUTER_MODE_SEF));
					$url = $router->build($url)->toString(array('path', 'query', 'fragment'));
				
				} else {

					$url = JRoute::_(ContentHelperRoute::getArticleRoute($article->id . ':' . $article->alias , $article->catid));
				}

				// SEF url
				$url = str_replace('/administrator/', '', $url);

				// Tidying up the url
				$url = str_replace('component/content/article/', '', $url);

				$domain = $uri->toString(array('scheme', 'host', 'port'));

				$permalink = $domain . '/' . ltrim($url , '/');

				return $permalink;

			 break;
			 case 'com_easyblog':
				require_once(JPATH_ADMINISTRATOR . '/components/com_easyblog/includes/easyblog.php');
				return EBR::getRoutedURL('index.php?option=com_easyblog&view=entry&id=' . $article->id, false, true);
			 break;
			 case 'com_k2':
				require_once(JPATH_ROOT . '/components/com_k2/helpers/route.php');

				JTable::addIncludePath(JPATH_ROOT . '/libraries/joomla/database/table');

				$category = JTable::getInstance('Category' , 'JTable');
				$category->load($article->catid);

				$url = K2HelperRoute::getItemRoute($article->id . ':' . $article->alias , $article->catid . ':' . $category->alias);

				return $uri->toString(array('scheme', 'host', 'port')) . '/' . ltrim($url , '/');
			 break;
		}
	}

	/**
	 * Gets the total hit count for the specific article.
	 *
	 * @since	4.0
	 * @access	private
	 */
	private function getArticleHits(&$article)
	{
		$db = ED::db();

		$query = 'SELECT ' . $db->nameQuote('hits') . ' FROM ' . $db->nameQuote('#__content') . ' '
				. 'WHERE ' . $db->nameQuote('id') . '=' . $db->Quote($article->id);

		$db->setQuery($query);
		$hits = (int) $db->loadResult();

		return $hits;
	}

	/**
	 * Map existing article to easydiscuss post table
	 *
	 * @since	4.0
	 * @access	public
	 */
	public function mapExisting(&$article)
	{
		if (!$this->exists() || $this->extension == 'com_easydiscuss') {
			return false;
		}

		// @rule: If article is not published, do not try to process anything
		if ($this->extension == 'com_content' && !$article->state) {
			return false;
		}

		// Ensure that the article contains contents
		if ($this->extension == 'com_k2' && !$article->introtext && !$article->fulltext) {
			return;
		}
		
		// Since easyblog has their own unpublishing state, we need to respect it.
		if ($this->extension == 'com_easyblog') {

			// Unpublished state for easyblog in general
			$unpublishedState = array(
								'0', // unpublished
								'2', // scheduled
								'3', // draft
								'4', // pending
								'9' // blank post
							);

			if (in_array($article->published, $unpublishedState)) {
				return false;
			}
		}

		// Check if the discussion already exists before
		$ref = ED::table('PostsReference');
		$exists = $ref->loadByExtension($article->id , $this->extension);
		$isNew = !$exists;

		// @rule: Only append discussions that are already added into the reference table.
		$post = $this->createDiscussion($article , $isNew);

		// If this post coming from EasyArticle, we should store it as com_content
		if ($this->extension == 'com_easyarticles') {
			$this->extension = 'com_content';
		}

		if (!$exists) {
			// @rule: Store the references
			$ref->set('post_id' , $post->id);
			$ref->set('reference_id', $article->id);
			$ref->set('extension', $this->extension);
			$ref->store();
		}
	}

	/**
	 * Get template path for the plugin
	 *
	 * @since	4.0
	 * @access	public
	 */
	public function getTemplatePath($file)
	{
		return dirname(__FILE__) . '/tmpl/' . $file;
	}

	/**
	 * Method to create the discussion that are linked to the article object and the content.
	 *
	 * @since	4.0
	 * @access	public
	 */
	public function createDiscussion(&$article , $isNew = true)
	{
		if (!$this->exists()) {
			return false;
		}

		$post = ED::post();
		$params = $this->getParams();

		if (!$isNew) {
			// Get the mapping
			$ref = ED::table('PostsReference');
			$ref->loadByExtension($article->id , $this->extension);

			// Load the discussion item
			$post = ED::post($ref->post_id);
		}

		$data = array();

		// Only assign the category if the post is new
		if ($isNew) {

			$data['category_id'] = $params->get('category_storage', 1);

			// Map discussion category with article category if alias are the same
			if ($params->get('map_category_storage')) {

				$mapcategory = $this->mapCategory($article->catid);

				if (!empty($mapcategory)) {
					$data['category_id'] = $mapcategory->id;
				}
			}
		}

		$data['title'] = $article->title;

		// @rule: Set the creation date
		$data['created'] = $article->created;

		// @rule: Set the publishing state
		$data['published'] = DISCUSS_ID_PUBLISHED;

		// @rule: Set the modified date
		$data['modified'] = $article->modified;

		// @rule: Set the user id
		$data['user_id'] = $article->created_by;

		// @rule: Set the user type
		$data['user_type'] = 'member';

		// @rule: Set the hits
		$data['hits'] = $article->hits;

		// @rule: We only take the introtext part.
		$text = $article->introtext;

		// Get full content for easyblog content
		if ($this->extension == 'com_easyblog') {
			$text = $article->original->content;

			if (!$text) {
				$text = $article->original->intro;
			}
		}

		$config = ED::config();
		$contentType = 'html';

		if ($config->get('layout_editor') == 'bbcode') {
			$text = ED::parser()->convert2validImgLink($text);
			$text = ED::parser()->html2bbcode($text);

			// Remove excess html tags
			$text = strip_tags($text);

			$contentType = 'bbcode';
		}

		// @rule: Add a read more text that links to the article.
		if ($params->get('readmore_in_post' , true)) {

			$url = $this->getArticleURL($article);

			ob_start();
			include($this->getTemplatePath('readmore.' . $contentType . '.php'));
			$readmore = ob_get_contents();
			ob_end_clean();

			$text .= $readmore;
		}

		$data['content'] = $text;
		$data['content_type'] = $contentType;

		$post->bind($data);
		$state = $post->save();

		return $post;
	}

	/**
	 * Map discussion category with article category if title are the same
	 *
	 * @since	4.1
	 * @access	public
	 */
	public function mapCategory($articleCatId)
	{
		$db = ED::db();

		// load the Joomla category alias
		$query = 'SELECT a.`alias` FROM `#__categories` AS a';
		$query .= ' WHERE a.`id` = ' . $db->Quote($articleCatId);
		$db->setQuery($query);
		$articleCat = $db->loadObject();		

		// load Easydiscuss category table and see whether it did match with joomla category alias
		$query = 'SELECT b.`id` FROM `#__discuss_category` AS b';
		$query .= ' WHERE b.`alias` = ' . $db->Quote($articleCat->alias);
		$db->setQuery($query);
		$category = $db->loadObject();

		return $category;
	}

	/**
	 * Get registration link based on the provider
	 *
	 * @since	4.0
	 * @access	public
	 */
	public function getRegistrationLink()
	{
		$params = $this->getParams();

		$id = JRequest::getInt('id');

		$article = JTable::getInstance('Content', 'JTable');
		$article->load($id);

		$return = base64_encode($this->getArticleURL($article));

		// Default url
		$url = JRoute::_('index.php?option=com_users&view=registration&return=' . $return);

		switch($params->get('login_provider' , 'joomla'))
		{
			case 'cb':
				$url = JRoute::_('index.php?option=com_comprofiler&task=registers');
				break;

			case 'jomsocial':
				include_once JPATH_ROOT . '/components/com_community/libraries/core.php';
				$url = CRoute::_('index.php?option=com_community&view=register');
				break;

			case 'easysocial':

				if (ED::easysocial()->exists()) {
					$url = ESR::registration();
				}

				break;
			default:
				$url = JRoute::_('index.php?option=com_users&view=registration');
				break;
		}

		return $url;
	}

	/**
	 * Get login link based on the provider
	 *
	 * @since	4.0
	 * @access	public
	 */
	public function getLoginLink()
	{
		$params = $this->getParams();

		$id = JRequest::getInt('id');

		$article = JTable::getInstance('Content', 'JTable');
		$article->load($id);

		$return = base64_encode($this->getArticleURL($article));

		// Default url
		$url = JRoute::_('index.php?option=com_users&view=login&return=' . $return);

		switch ($params->get('login_provider', 'joomla'))
		{
			case 'jomsocial':
				include_once JPATH_ROOT . '/components/com_community/libraries/core.php';
				$url 	= CRoute::_('index.php?option=com_community');
				break;

			case 'easysocial':
				$easysocial = ED::easysocial();

				if ($easysocial->exists()) {
					$url = FRoute::login();
				}

				break;

			case 'cb':
			default:
				$url = $url;
				break;
		}

		return $url;
	}
}
