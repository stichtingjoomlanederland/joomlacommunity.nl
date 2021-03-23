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

class EasyDiscussComposer
{
	public $id;

	private $post;
	public  $parent;
	private $isDiscussion;

	public $content = '';

	public $renderMode = 'onload'; // onload|explicit
	public $theme;

	public $classname;
	public $selector;

	public $editor;
	public $editorType = 'bbcode';

	public $operation;

	private $tabs = array(
						'attachments',
						'fields',
						'password',
						'polls',
						'location'
					);

	public function __construct($opts = array())
	{
		$this->acl = ED::acl();
		$this->my = JFactory::getUser();
		$this->app = JFactory::getApplication();
		$this->input = $this->app->input;
		$this->config = ED::config();

		$operation = $opts[0];
		$post = $opts[1];

		// Generate a random uid
		$this->uuid = rand();
		$this->uid = 'data-ed-composer-wrapper-' . $this->uuid;
		$this->operation = $operation;

		// Bind the properties accordingly
		$this->$operation($post);

		// Determines the editor to use
		$this->editorType = $this->config->get("layout_editor", "bbcode");
		$this->editor = $this->editorType;

		// If the editor type is not bbcode, we should get the correct bbcode
		if ($this->editorType != 'bbcode') {
			JHtml::_('behavior.core');

			$this->editor = ED::getEditor($this->editorType);
		}

		// Names
		$this->classname = $this->id;
		$this->selector = '.' . $this->id;

		// Load frontend language
		ED::loadLanguages();
	}

	/**
	 * Method to overrie the uuid of this composer
	 *
	 * @since	5.0
	 * @access	public
	 */
	public function setComposerUuid($uuid)
	{
		$this->uuid = $uuid;
		// update the uid ba
		$this->uid = 'data-ed-composer-wrapper-' . $this->uuid;
	}

	/**
	 * When replying a post
	 *
	 * @since	4.0
	 * @access	public
	 */
	public function replying($question)
	{
		$this->post = ED::post();
		$this->parent = $question;
	}

	/**
	 * When editing a reply item
	 *
	 * @since	4.0
	 * @access	public
	 */
	public function editing($post)
	{
		$this->post = $post;
		$this->parent = ED::post($post->parent_id);
		$this->content = $post->content;
	}

	/**
	 * During post creation
	 *
	 * @since	4.0
	 * @access	public
	 */
	public function creating($post)
	{
		$this->post = $post;
		$this->parent = $post;
		$this->content = $post->content;
	}

	/**
	 * For Default Purpose
	 *
	 * @since	4.0
	 * @access	public
	 */
	public function defaults($post)
	{
		$this->content = $post;
	}

	/**
	 * Renders the composer wrapper
	 *
	 * @since	4.0
	 * @access	public
	 */
	public function getComposer($currentCatId = null)
	{
		$theme = ED::themes();

		// Render the captcha image
		$captcha = ED::captcha();
		$hasTabs = $this->hasTabs($currentCatId);

		$appendAjaxIndex = $this->config->get('system_ajax_index') ? 'index.php' : '';

		$theme->set('hasTabs', $hasTabs);
		$theme->set('currentCatId', $currentCatId);
		$theme->set('captcha', $captcha);
		$theme->set('editorId', $this->uid);
		$theme->set('editorUuid', $this->uuid);
		$theme->set('composer', $this);
		$theme->set('post', $this->post);
		$theme->set('parent', $this->parent);
		$theme->set('content', $this->content);
		$theme->set('editor', $this->editor);
		$theme->set('isDiscussion', $this->isDiscussion);
		$theme->set('operation', $this->operation);
		$theme->set('renderMode', $this->renderMode);
		$theme->set('appendAjaxIndex', $appendAjaxIndex);

		$output = $theme->output('site/composer/composer');
		return $output;
	}

	/**
	 * Renders captcha output
	 *
	 * @since	5.0.0
	 * @access	public
	 */
	public function renderCaptcha(EasyDiscussCaptcha $captcha)
	{
		$theme = ED::themes();
		$theme->set('captcha', $captcha);
		$output = $theme->output('site/composer/forms/captcha');

		return $output;
	}

	/**
	 * Renders the name and email field
	 *
	 * @since	5.0.0
	 * @access	public
	 */
	public function renderAnonymousField($anonymous = false)
	{
		$theme = ED::themes();
		$theme->set('anonymous', $anonymous);
		$theme->set('operation', $this->operation);

		$output = $theme->output('site/composer/forms/anonymous');

		return $output;
	}

	/**
	 * Renders the priority field
	 *
	 * @since	5.0.0
	 * @access	public
	 */
	public function renderPriorityField($selected = null)
	{
		// Get post priorities
		$priorities = array();

		if ($this->config->get('post_priority')) {
			$prioritiesModel = ED::model('Priorities');
			$priorities = $prioritiesModel->getPriorities();
		}

		$theme = ED::themes();
		$theme->set('selected', $selected);
		$theme->set('priorities', $priorities);
		$output = $theme->output('site/composer/forms/priority');

		return $output;
	}

	/**
	 * Renders the post types field
	 *
	 * @since	5.0.0
	 * @access	public
	 */
	public function renderPostTypesField($categoryId, $selected = null)
	{
		// Get post types list
		$model = ED::model('PostTypes');
		$postTypes = $model->getPostTypes($categoryId);

		$theme = ED::themes();
		$theme->set('selected', $selected);
		$theme->set('postTypes', $postTypes);
		$output = $theme->output('site/composer/forms/post.types');

		return $output;
	}

	/**
	 * Renders the post labels field
	 *
	 * @since	5.0.0
	 * @access	public
	 */
	public function renderPostLabelsField($selected = null)
	{
		$model = ED::model('PostLabels');
		$labels = $model->getLabels();

		$theme = ED::themes();
		$theme->set('selected', $selected);
		$theme->set('labels', $labels);
		$output = $theme->output('site/composer/forms/post.labels');

		return $output;
	}

	/**
	 * Renders the name and email field
	 *
	 * @since	5.0.0
	 * @access	public
	 */
	public function renderNameField($type)
	{
		$theme = ED::themes();
		$theme->set('type', $type);
		$output = $theme->output('site/composer/forms/name');

		return $output;
	}

	/**
	 * Renders terms and conditions
	 *
	 * @since	5.0.0
	 * @access	public
	 */
	public function renderTnc($type)
	{
		$theme = ED::themes();
		$theme->set('type', $type);
		$output = $theme->output('site/composer/forms/tnc');

		return $output;
	}

	/**
	 * Renders the editor for the discussion
	 *
	 * @since	4.0
	 * @access	public
	 */
	public function renderEditor($name = 'dc_content', $content = '', $resetContent = false)
	{
		// See if we need to reset the $this->content
		// This is to avoid the situation where we load two composer one after another.
		// If the content of a second composer is empty, it will use the previous content
		if ($resetContent) {
			$this->content = '';
		}

		// We need to know which theme file to load
		$editorType = 'bbcode';

		if ($this->editorType != 'bbcode') {
			$editorType = 'joomla';
		}

		// Check if should show video in BBCode or not.
		$video = $this->config->get('bbcode_video');

		// Contstruct the namespace
		$namespace = 'site/composer/editors/' . $editorType;

		// Here we check if there is a smiley override for the composer
		$overridePath = '/'.$this->app->getTemplate() . '/html/com_easydiscuss/smileys/image.png';

		// If got, we add css to override it
		$overrideExists = JFile::exists(DISCUSS_JOOMLA_SITE_TEMPLATES . $overridePath);

		if ($overrideExists) {
			$style = '#ed .markItUp .markItUpButton a { background-image: url("' . DISCUSS_JOOMLA_SITE_TEMPLATES_URI . $overridePath . '") !important; }';
			$this->doc->addStyleDeclaration($style);
		}

		if ($content) {
			$this->content = $content;
		}

		$theme = ED::themes();
		$theme->set('editorId', $this->uid);
		$theme->set('editorUuid', $this->uuid);
		$theme->set('operation', $this->operation);
		$theme->set('content', $this->content);
		$theme->set('editor', $this->editor);
		$theme->set('video', $video);
		$theme->set('name', $name);

		// GIPHY is only allowed on bbcode
		if ($this->editorType == 'bbcode') {
			$giphy = ED::giphy();

			$theme->set('giphy', $giphy);
		}

		$canSlashCommand = $this->canSlashCommand();

		if ($canSlashCommand) {
			$commands = $this->parent->getSlashCommands();
			$theme->set('commands', $commands);
		}

		$theme->set('canSlashCommand', $canSlashCommand);

		$html = $theme->output($namespace);

		return $html;
	}

	/**
	 * Determine if user has access to the slash commands
	 *
	 * @since	5.0
	 * @access	public
	 */
	public function canSlashCommand()
	{
		// doesnt apply to profile signature
		if (is_null($this->parent)) {
			return false;
		}

		// Only replying able to use slash command
		if (is_null($this->parent->id)) {
			return false;
		}

		// Only admin/moderator/owner able to use slash command
		if (ED::isSiteAdmin() || ED::isModerator($this->parent->category_id)) {
			return true;
		}

		return false;
	}

	/**
	 * Renders the tabs for composing
	 *
	 * @since	4.0
	 * @access	public
	 */
	public function renderTabs($categoryId = null)
	{
		// Get a list of available tabs
		$tabs = $this->getTabs((int) $categoryId);

		// No tabs to render
		if (!$tabs) {
			return;
		}

		// Get a list of tags
		$model = ED::model('Tags');
		$tags = $model->getTagCloud('', 'post_count', 'DESC');

		$theme = ED::themes();
		$theme->set('editorId', $this->uid);
		$theme->set('tabs', $tabs);
		$theme->set('tags', $tags);
		$theme->set('operation', $this->operation);
		$theme->set('post', $this->post);
		$theme->set('parent', $this->parent);

		$output = $theme->output('site/composer/tabs');

		return $output;
	}

	/**
	 * Determines if there are any tabs used on the site
	 *
	 * @since	4.0
	 * @access	public
	 */
	public function hasTabs($categoryId = null)
	{
		static $hasTabs = null;

		if (is_null($hasTabs)) {
			$tabs = $this->getTabs($categoryId);

			foreach ($tabs as $tab) {

				if ($tab->heading) {
					$hasTabs = true;
					break 1;
				}
			}

			if (is_null($hasTabs)) {
				$hasTabs = false;
			}
		}

		return $hasTabs;
	}

	/**
	 * Retrieves a list of tabs that is available on the site
	 *
	 * @since	4.0
	 * @access	public
	 */
	public function getTabs($categoryId = null)
	{
		static $result = [];

		if (!isset($result[$categoryId])) {

			$tabs = array();

			foreach ($this->tabs as $item) {
				$tab = new stdClass();

				// Check if user can really access the tabs
				if (!$this->canAccessTabs($item, $categoryId)) {
					continue;
				}

				$heading = $this->getTabHeading($item, $this->uid, $this->post, $this, $this->operation);
				$contents = $this->getTabContents($item, $this->uid, $this->post, $this, $this->operation, $categoryId);

				// If contents is empty we do not display the tabs
				if (!$contents) {
					continue;
				}

				$tab->heading = $heading;
				$tab->contents = $contents;

				$tabs[] = $tab;
			}

			// Trigger plugins
			JPluginHelper::importPlugin('easydiscuss');
			$extraTabs = JFactory::getApplication()->triggerEvent('onRenderTabs', array(&$this->uid, &$this->post, &$this, $this->operation));

			if (!empty($extraTabs)) {
				$tabs = array_merge($tabs, $extraTabs);
			}

			$result[$categoryId] = $tabs;
		}

		return $result[$categoryId];
	}

	/**
	 * Retrieves the heading of the tab
	 *
	 * @since	5.0.0
	 * @access	public
	 */
	private function getTabHeading($name, $uid, $post, $composer, $operation)
	{
		// Get the tab heading
		$theme = ED::themes();
		$theme->set('editorId', $uid);
		$theme->set('post', $post);
		$theme->set('composer', $composer);
		$theme->set('operation', $operation);

		$heading = $theme->output('site/composer/tabs/' . $name . '/heading');

		return $heading;
	}

	/**
	 * Retrieves the heading of the tab
	 *
	 * @since	5.0.0
	 * @access	public
	 */
	private function getTabContents($name, $uid, $post, $composer, $operation, $categoryId = null)
	{
		$view = $this->input->get('view', '', 'string');

		// Get the contents of the tab
		$theme = ED::themes();
		$theme->set('view', $view);
		$theme->set('editorId', $uid);
		$theme->set('post', $post);
		$theme->set('composer', $composer);
		$theme->set('operation', $operation);
		$theme->set('categoryId', $categoryId);

		$contents = $theme->output('site/composer/tabs/' . $name . '/contents');

		return $contents;
	}

	/**
	 * Determine if the category allows users to upload attachment
	 *
	 * @since	4.0
	 * @access	public
	 */
	public function canUploadAttachments($categoryId = null)
	{
		if (!$categoryId) {
			return true;
		}

		static $cache = [];

		if (!isset($cache[$categoryId])) {
			$category = ED::category($categoryId);
			$cache[$categoryId] = (bool) $category->allowUploadAttachments();
		}

		return $cache[$categoryId];
	}

	/**
	 * Determine if user has access to the specified tabs
	 *
	 * @since	4.0
	 * @access	public
	 */
	public function canAccessTabs($name, $categoryId = null)
	{
		$canAccess = true;

		// Ensure that attachments is enabled
		if ($name == 'attachments' && (!$this->config->get('attachment_questions') || !$this->canUploadAttachments($categoryId) || (!$this->acl->allowed('add_attachment', false) && !ED::isSiteAdmin()))) {
			$canAccess = false;
		}

		// Fields is disabled
		if ($name == 'fields' && !$this->config->get('main_customfields_input')) {
			$canAccess = false;
		}

		if ($name == 'fields' && $this->config->get('main_customfields_input')) {
			$model = ED::model('CustomFields');
			$fields = $model->getFields(DISCUSS_CUSTOMFIELDS_ACL_INPUT, $this->operation, $this->post->id, $categoryId);

			// if empty fields then we do not show this tab.
			if (!$fields) {
				$canAccess = false;
			}
		}

		if ($name == 'password') {
			if ($this->operation == 'replying' || ($this->operation == 'editing' && $this->post->isReply()) || !$this->config->get('main_password_protection')) {
				$canAccess = false;
			}
		}

		if ($name == 'polls') {
			if (($this->post->isQuestion() && !$this->config->get('main_polls')) || (($this->operation == 'replying' || $this->post->isReply()) && !$this->config->get('main_polls_replies'))) {
				$canAccess = false;
			}
		}

		if ($name == 'location') {
			if (($this->post->isQuestion() || $this->operation != 'replying') && !$this->config->get('main_location_discussion')) {
				$canAccess = false;
			}

			if (($this->post->isReply() || $this->operation == 'replying') && !$this->config->get('main_location_reply')) {
				$canAccess = false;
			}
		}

		return $canAccess;
	}

	/**
	 * Retrieves the editor class name
	 *
	 * @since	4.0
	 * @access	public
	 */
	public function getEditorClass()
	{
		if ($this->editorType == 'bbcode') {
			return 'markitup';
		}

		return 'joomla';
	}
}
