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

class EasyDiscussThemesHelperForm
{
	/**
	 * Renders a standard hidden input set for a form
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return
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
	 * @param	string
	 * @return	
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
	 * @param	string
	 * @return	
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
	 * @param	string
	 * @return	
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

		return $theme->output('admin/html/form.label');
	}

	/**
	 * Generates a textarea
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return	
	 */
	public static function textarea($name, $value = '')
	{
		$theme = ED::themes();
		$theme->set('name', $name);
		$theme->set('value', $value);

		$output = $theme->output('admin/html/form.textarea');

		return $output;
	}

	/**
	 * Renders a text input
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return	
	 */
	public static function textbox($name, $value = '', $placeholder = '', $class = '')
	{
		if ($placeholder) {
			$placeholder = JText::_($placeholder);
		}
		
		$theme = ED::themes();
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
	 * @param	string
	 * @return
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
	 * Renders a Yes / No input.
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string			The key name for the input.
	 * @param	bool			The value of the current item. Should be either false / true.
	 * @param	string			The id of the input (Optional).
	 * @param	string/array	The attributes to add to the select list.
	 * @param	array			The tooltips data.
	 *
	 * @return	string	The html output.
	 *
	 * @author	Mark Lee <mark@stackideas.com>
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
	 * @param	string
	 * @return	
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
	 * Renders the user group form
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return	
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
}
