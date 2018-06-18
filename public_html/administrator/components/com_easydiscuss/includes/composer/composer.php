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

class EasyDiscussComposer extends EasyDiscuss
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

	public function __construct($opts = array())
	{
		parent::__construct();

		$operation = $opts[0];
		$post = $opts[1];

		// Generate a random uid
		$this->uid = 'data-ed-composer-wrapper-' . rand();
		$this->operation = $operation;

		// Bind the properties accordingly
		$this->$operation($post);

		// Determines the editor to use
		$this->editorType = $this->config->get("layout_editor", "bbcode");
		$this->editor = $this->editorType;

		// If the editor type is not bbcode, we should get the correct bbcode
		if ($this->editorType != 'bbcode') {
			JHtml::_('behavior.core');
			
			$this->editor = JFactory::getEditor($this->editorType);
		}

		// Names
		$this->classname = $this->id;
		$this->selector = '.' . $this->id;

		// Load frontend language
		ED::loadLanguages();
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
	public function getComposer()
	{
		$theme = ED::themes();

		// Render the captcha image
		$captcha = ED::captcha();

		// Load the actor
		$my = ED::user();

		$theme->set('my', $my);
		$theme->set('captcha', $captcha);
		$theme->set('editorId', $this->uid);
		$theme->set('composer', $this);
		$theme->set('post', $this->post);
		$theme->set('parent', $this->parent);
		$theme->set('content', $this->content);
		$theme->set('editor', $this->editor);
		$theme->set('isDiscussion', $this->isDiscussion);
		$theme->set('operation', $this->operation);
		$theme->set('renderMode', $this->renderMode);


		$output = $theme->output('site/composer/composer');
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
		$theme->set('operation', $this->operation);
		$theme->set('content', $this->content);
		$theme->set('editor', $this->editor);
		$theme->set('video', $video);
		$theme->set('name', $name);

		$html = $theme->output($namespace);

		return $html;
	}

	/**
	 * Renders the tabs for composing
	 *
	 * @since	4.0
	 * @access	public
	 */
	public function renderTabs()
	{
		$theme = ED::themes();

		// Get a list of available tabs
		$tabs = $this->getTabs();

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
	public function hasTabs()
	{
		static $hasTabs = null;

		if (is_null($hasTabs)) {
			$tabs = $this->getTabs();

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
	public function getTabs()
	{
		static $result = null;

		if (is_null($result)) {
			// Get the path to the tabs
			$path = DISCUSS_ROOT . '/themes/wireframe/composer/tabs';

			// Get a list of files from the defined path
			$folders = JFolder::folders($path, '.', false, true);

			$tabs = array();

			foreach ($folders as $folder) {
				$tab = new stdClass();
				$name = basename($folder);

				// Check if user can really access the tabs
				if (!$this->canAccessTabs($name)) {
					continue;
				}

				// Get the tab heading
				$theme = ED::themes();
				$theme->set('editorId', $this->uid);
				$theme->set('post', $this->post);
				$theme->set('composer', $this);
				$theme->set('operation', $this->operation);

				$heading = $theme->output('site/composer/tabs/' . $name . '/heading');

				// Get the contents of the tab
				$theme = ED::themes();
				$theme->set('editorId', $this->uid);
				$theme->set('post', $this->post);
				$theme->set('composer', $this);
				$theme->set('operation', $this->operation);

				$contents = $theme->output('site/composer/tabs/' . $name . '/contents');

				// If contents is empty we do not display the tabs
				if (!$contents) {
					continue;
				}

				$tab->heading = $heading;
				$tab->contents = $contents;
				$tab->path = $folder;

				$tabs[] = $tab;
			}

			$result = $tabs;
		}

		return $result;
	}

	/**
	 * Determine if user has access to the specified tabs
	 *
	 * @since	4.0
	 * @access	public
	 */
	public function canAccessTabs($name)
	{
		$canAccess = true;

		switch ($name) {
			case 'attachments':
				// Ensure that attachments is enabled
				if ((!$this->config->get('attachment_questions') || (!$this->acl->allowed('add_attachment', false) && !ED::isSiteAdmin()))) {
					$canAccess = false;
				}
				break;
			case 'fields':
				if (!$this->config->get('main_customfields_input')) {
					$canAccess = false;
				} else {
					$model = ED::model('CustomFields');
					$fields = $model->getFields(DISCUSS_CUSTOMFIELDS_ACL_INPUT, $this->operation, $this->post->id);

					// if empty fields then we do not show this tab.
					if (!$fields) {
						$canAccess = false;
					}
				}
				break;
			case 'links':
				// Ensure that this tab is enabled
				if (!$this->config->get('reply_field_references')) {
					$canAccess = false;
				}
				break;
			case 'password':
				if ($this->operation == 'replying' || ($this->operation == 'editing' && $this->post->isReply()) || !$this->config->get('main_password_protection')) {
					$canAccess = false;
				}
				break;
			case 'polls':
				if (($this->post->isQuestion() && !$this->config->get('main_polls')) || (($this->operation == 'replying' || $this->post->isReply()) && !$this->config->get('main_polls_replies'))) {
					$canAccess = false;
				}
				break;
			case 'site':
				if (($this->post->isQuestion() && !$this->config->get('tab_site_question')) || (!$this->post->isQuestion() && !$this->config->get('tab_site_reply'))) {
					$canAccess = false;
				}
				break;
			default:
				break;
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

	public function getFieldData($fieldName, $params)
	{
		$data = array();
		$json = ED::json();

		// this is to support data from older version
		if (!$json->isJsonString($params)) {

			$fieldName = (string) $fieldName;
			$pattern = '/params_' . $fieldName . '[0-9]?=["](.*)["]/i';

			preg_match_all($pattern, $params, $matches);

			if (!empty($matches[1])) {
				foreach ($matches[1] as $match) {
					$data[] = $match;
				}
			}

			return $data;
		}

		// Make it to array
		$params = json_decode($params, true);

		if (!empty($params)) {
			foreach ($params as $key => $val) {

				$fieldName = (string) $fieldName;

				if (JString::strpos($key, 'params_' . $fieldName) !== false) {
					$data[] = $val;
				}
			}

			return $data;
		}

		return false;
	}
}
