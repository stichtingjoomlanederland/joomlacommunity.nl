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

class EasyDiscussThemesHelperForm
{
	/**
	 * Renders a colour picker input
	 *
	 * @since	4.2.0
	 * @access	public
	 */
	public static function colorpicker($name, $value = '', $revert = '')
	{
		static $script = null;

		$loadScript = false;
		
		if (is_null($script)) {
			$loadScript = true;
			$script = true;
		}

		JHTML::_('behavior.colorpicker');

		$theme = ED::themes();
		$theme->set('loadScript', $loadScript);
		$theme->set('name', $name);
		$theme->set('value', $value);
		$theme->set('revert', $revert);

		$output = $theme->output('admin/html/form/colorpicker');

		return $output;
	}

	/**
	 * Floating label with input form
	 *
	 * @since	4.1.0
	 * @access	public
	 */
	public static function floatinglabel($label, $name, $type = 'textbox', $value = '')
	{
		// This currently only supports textbox and password
		$supported = array('textbox', 'password');

		if (!in_array($type, $supported)) {
			return "";
		}

		$label = JText::_($label);
		$id = 'ed-' . str_ireplace(array('.'), '', $name);

		$theme = ED::themes();
		$theme->set('type', $type);
		$theme->set('value', $value);
		$theme->set('label', $label);
		$theme->set('name', $name);
		$theme->set('id', $id);

		$output = $theme->output('site/helpers/form/' . __FUNCTION__);

		return $output;
	}

	/**
	 * Renders a standard hidden input set for a form
	 *
	 * @since	4.0
	 * @access	public
	 */
	public static function hidden($controller, $view = '', $task = '')
	{
		$theme = ED::themes();
		$theme->set('view', $view);
		$theme->set('controller', $controller);
		$theme->set('task', $task);

		$output = $theme->output('admin/html/form.hidden');

		return $output;
	}

	/**
	 * Renders the editor settings
	 *
	 * @since	4.0
	 * @access	public
	 */
	public static function editor($name, $selected = '')
	{
		// Get a list of editors on the site
		$editors = self::getEditors();

		$theme = ED::themes();
		$theme->set('name', $name);
		$theme->set('selected', $selected);
		$theme->set('editors', $editors);

		$output = $theme->output('admin/html/form.editor');

		return $output;
	}

	/**
	 * Renders a form for theme selection
	 *
	 * @since	4.0
	 * @access	public
	 */
	public static function themes($name, $selected = "")
	{
		$themes = JFolder::folders(DISCUSS_THEMES);

		if (!$selected) {
			$config = ED::config();
			$selected = $config->get('layout_site_theme');
		}

		$theme = ED::themes();
		$theme->set('name', $name);
		$theme->set('themes', $themes);
		$theme->set('selected', $selected);
		
		$output = $theme->output('admin/html/form.themes');

		return $output;
	}

	/**
	 * Generates the hidden token in a form
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return	
	 */
	public static function token()
	{
		$theme = ED::themes();
		$token = JFactory::getSession()->getFormToken();

		$theme->set('token', $token);
		$output  = $theme->output('admin/html/form.token');

		return $output;
	}

	/**
	 * Renders the popover html contents
	 *
	 * @since	4.0
	 * @access	public
	 */
	public static function label($label, $desc = '')
	{
		if (!$desc) {
			$desc = $label . '_DESC';
			$desc = JText::_($desc);
		}

		$label = JText::_($label);

		$theme = ED::themes();
		$theme->set('label', $label);
		$theme->set('desc', $desc);

		return $theme->output('admin/html/form/label');
	}

	/**
	 * Generates a textarea
	 *
	 * @since	4.0
	 * @access	public
	 */
	public static function textarea($name, $value = '', $rows = null)
	{
		if (is_null($rows)) {
			$rows = 5;
		}

		$theme = ED::themes();
		$theme->set('name', $name);
		$theme->set('value', $value);
		$theme->set('rows', $rows);

		$output = $theme->output('admin/html/form/textarea');

		return $output;
	}

	/**
	 * Renders a text input
	 *
	 * @since	4.0
	 * @access	public
	 */
	public static function textbox($name, $value = '', $placeholder = '', $class = '', $options = array())
	{
		if ($placeholder) {
			$placeholder = JText::_($placeholder);
		}
		
		if (isset($options['class'])) {
			$class = $options['class'];
		}

		$attributes = '';

		if (isset($options['attr'])) {
			$attributes = $options['attr'];
		}


		$theme = ED::themes();
		$theme->set('attributes', $attributes);
		$theme->set('name', $name);
		$theme->set('value', $value);
		$theme->set('class', $class);
		$theme->set('placeholder', $placeholder);

		$output = $theme->output('admin/html/form.textbox');

		return $output;
	}

	/**
	 * Renders a dropdown
	 *
	 * @since	4.0
	 * @access	public
	 */
	public static function dropdown($name, $items, $selected = '', $attributes = '')
	{
		$theme = ED::themes();
		$theme->set('name', $name);
		$theme->set('items', $items);
		$theme->set('selected', $selected);
		$theme->set('attributes', $attributes);

		$output = $theme->output('admin/html/form.dropdown');

		return $output;
	}

	/**
	 * Renders a dialog to browse for posts
	 *
	 * @since	4.0.14
	 * @access	public
	 */
	public static function posts($name, $selected = null, $id = null)
	{
		if (is_null($id)) {
			$id = $name;
		}

		$title = '';

		if ($selected) {
			$post = ED::post($selected);
			$title = $post->getTitle();
		}

		$theme = ED::themes();
		$theme->set('title', $title);
		$theme->set('id', $id);
		$theme->set('name', $name);
		$theme->set('selected', $selected);

		$output = $theme->output('admin/html/form/posts');

		return $output;
	}

	/**
	 * Renders a category dropdown
	 *
	 * @since	4.0.14
	 * @access	public
	 */
	public static function categories($name, $selected = array(), $multiple = false)
	{
		$model = ED::model('Categories');
		$categories = $model->getAllCategories();

		if ($multiple) {
			$name = $name . '[]';
		}
		
		if (!is_array($selected)) {
			$selected = array();
		}

		$theme = ED::themes();
		$theme->set('name', $name);
		$theme->set('multiple', $multiple);
		$theme->set('selected', $selected);
		$theme->set('categories', $categories);

		$output = $theme->output('admin/html/form/categories');

		return $output;
	}

	/**
	 * Renders a Yes / No input.
	 *
	 * @since	1.0
	 * @access	public
	 */
	public static function boolean($name, $value, $id = '', $attributes = '', $tips = array() , $text = array() )
	{
		// Ensure that id is set.
		$id = empty($id) ? $name : $id;

		// Determine if the input should be checked.
		$checked = $value ? true : false;

		$theme = ED::themes();

		if (is_array($attributes)) {
			$attributes = implode(' ', $attributes);
		}

		$onText = JText::_('COM_EASYDISCUSS_YES_OPTION');
		$offText = JText::_('COM_EASYDISCUSS_NO_OPTION');

		// Overriding the on / off text
		if (isset($text['on'])) {
			$onText = $text['on'];
		}

		if (isset($text['off'])) {
			$offText = $text['off'];
		}

		$theme->set('onText', $onText);
		$theme->set('offText', $offText);
		$theme->set('attributes', $attributes);
		$theme->set('tips', $tips);
		$theme->set('id', $id);
		$theme->set('name', $name);
		$theme->set('checked', $checked);

		return $theme->output('admin/html/form.boolean');
	}

	/**
	 * Renders a list of editors on the site.
	 *
	 * @since	4.0
	 * @access	public
	 */
	public static function getEditors()
	{
		$db = ED::db();
		$query = 'SELECT `element` AS value, `name` AS text'
				.' FROM `#__extensions`'
				.' WHERE `folder` = "editors"'
				.' AND `type` = "plugin"'
				.' AND `enabled` = 1'
				.' ORDER BY ordering, name';

		$db->setQuery($query);
		$editors = $db->loadObjectList();

		if (!$editors) {
			return array();
		}

		// We need to load the language file since we need to get the correct title
		$language = JFactory::getLanguage();

		foreach ($editors as $editor) {
			$language->load($editor->text . '.sys', JPATH_ADMINISTRATOR, null, false, false);
			$editor->text = JText::_($editor->text);
		}

		return $editors;
	}

	/**
	 * Renders a simple password input
	 *
	 * @since   4.1.0
	 * @access  public
	 */
	public static function password($name, $id = null, $value = '', $options = array())
	{
		$class = 'form-control';
		$placeholder = '';
		$attributes = '';

		if (isset($options['attr']) && $options['attr']) {
			$attributes = $options['attr'];
		}

		if (isset($options['class']) && $options['class']) {
			$class = $options['class'];
		}

		if (isset($options['placeholder']) && $options['placeholder']) {
			$placeholder = JText::_($options['placeholder']);
		}

		$theme = ED::themes();
		$theme->set('attributes', $attributes);
		$theme->set('name', $name);
		$theme->set('id', $id);
		$theme->set('value', $value);
		$theme->set('class', $class);
		$theme->set('placeholder', $placeholder);

		return $theme->output('site/helpers/form/password');
	}

	/**
	 * Renders the user group form
	 *
	 * @since	4.0
	 * @access	public
	 */
	public static function usergroups($name = 'gid' , $selected = '' , $exclude = array(), $checkSuperAdmin = false)
	{
		// If selected value is a string, we assume that it's a json object.
		if (is_string($selected)) {
			$selected = json_decode($selected);
		}

		$groups = self::getGroups();

		if (!is_array($selected)) {
			$selected = array($selected);
		}

		$isSuperAdmin = JFactory::getUser()->authorise('core.admin');
		
		// Generate a unique id
		$uid = uniqid();

		$theme = ED::themes();
		$theme->set('name', $name);
		$theme->set('selected', $selected);
		$theme->set('groups', $groups);
		$theme->set('uid', $uid);

		return $theme->output('admin/html/form.usergroups');
	}

	private static function getGroups()
	{
		$db = ED::db();

		$query 	= 'SELECT a.*, COUNT(DISTINCT(b.`id`)) AS `level` FROM ' . $db->quoteName('#__usergroups') . ' AS a';
		$query .= ' LEFT JOIN ' . $db->quoteName('#__usergroups') . ' AS b';
		$query .= ' ON a.`lft` > b.`lft` AND a.`rgt` < b.`rgt`';
		$query .= ' GROUP BY a.`id`, a.`title`, a.`lft`, a.`rgt`, a.`parent_id`';
		$query .= ' ORDER BY a.`lft` ASC';

		$db->setQuery($query);
		$groups 	= $db->loadObjectList();

		return $groups;
	}

	/**
	 * Render a lists of languages which installed in joomla
	 *
	 * @since	4.0.16
	 * @access	public
	 */
	public static function languages($name, $languages, $selected = '', $attributes = '')
	{
		$languages = JLanguage::getKnownLanguages();

		$theme = ED::themes();
		$theme->set('name', $name);
		$theme->set('languages', $languages);
		$theme->set('selected', $selected);
		$theme->set('attributes', $attributes);

		$output = $theme->output('admin/html/form.languages');

		return $output;
	}

	/**
	 * Displays dropdown list for the Facebook scopes permission
	 *
	 * @since	4.1.4
	 * @access	public
	 */
	public static function scopes($name, $id, $selected = null)
	{
		// Get the list of Facebook scope permission
		$scopes = array('publish_pages', 'manage_pages', 'publish_to_groups');
		$scopes = array_combine($scopes, $scopes);

		$theme = ED::themes();
		$theme->set('name', $name);
		$theme->set('scopes', $scopes);
		$theme->set('id', $id);
		$theme->set('selected', $selected);

		$output = $theme->output('admin/html/form.scopes');

		return $output;
	}			
}
