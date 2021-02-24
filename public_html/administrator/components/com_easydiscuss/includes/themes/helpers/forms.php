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

require_once(__DIR__ . '/abstractform.php');

// This is an alternate to html.settings . We cannot use html.form since they are already used as inputs.
class EasyDiscussThemesHelperForms extends EasyDiscussAbstractForm
{
	/**
	 * Since the implementation is the same as the parent, just load the parent
	 *
	 * @since	5.0.0
	 * @access	public
	 */
	public function colorpicker($name, $title, $value, $restoreColor)
	{
		return $this->renderColorpicker($name, $title, $value, $restoreColor);
	}

	/**
	 * Renders a dropdown button settings
	 *
	 * @since	5.0.0
	 * @access	public
	 */
	public function dropdown($name, $title, $value, $options = array(), $attributes = '', $notes = '')
	{
		return $this->renderDropdown($name, $title, $value, $options, $attributes, $notes);
	}

	/**
	 * Renders an editor
	 *
	 * @since	5.0.0
	 * @access	public
	 */
	public function editor($name, $title, $value, $options = array())
	{
		return $this->renderEditor($name, $title, $value, $options);
	}

	/**
	 * Renders an icon picker for font awesome
	 *
	 * @since	5.0.0
	 * @access	public
	 */
	public function iconpicker($name, $title, $value, $defaultIcon)
	{
		return $this->renderIconpicker($name, $title, $value, $defaultIcon);
	}

	/**
	 * Renders a password field for a form
	 *
	 * @since	5.0.0
	 * @access	public
	 */
	public function password($name, $title, $value, $options = array())
	{
		return $this->renderPassword($name, $title, $value, $options);
	}

	/**
	 * Renders a textbox for settings
	 *
	 * @since	5.0.0
	 * @access	public
	 */
	public function textarea($name, $title, $value, $options = array())
	{
		return $this->renderTextarea($name, $title, $value, $options);
	}

	/**
	 * Renders a textbox for forms
	 *
	 * @since	5.0.0
	 * @access	public
	 */
	public function textbox($name, $title, $value, $options = array())
	{
		return $this->renderTextbox($name, $title, $value, $options);
	}

	/**
	 * Renders a toggle button settings
	 *
	 * @since	5.0.0
	 * @access	public
	 */
	public function toggle($name, $title, $value, $attributes = '', $instructions = '', $wrapperAttributes = '')
	{
		return $this->renderToggle($name, $title, $value, $attributes, $instructions, $wrapperAttributes);
	}
}
