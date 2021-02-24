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

// This is an alternate to html.settings . We cannot use html.form since they are already used as inputs.
class EasyDiscussAbstractForm
{
	/**
	 * Given the title used for the form label, generate the tooltip for the label
	 *
	 * @since	5.0.0
	 * @access	public
	 */
	protected function getDescription($title)
	{
		$desc = $title . '_DESC';

		return $desc;
	}

	/**
	 * Renders a colorpicker settings
	 *
	 * @since	5.0.0
	 * @access	public
	 */
	final public function renderColorpicker($name, $title, $value, $restoreColor)
	{
		$desc = $this->getDescription($title);

		$theme = ED::themes();
		$theme->set('value', $value);
		$theme->set('restoreColor', $restoreColor);
		$theme->set('name', $name);
		$theme->set('title', $title);
		$theme->set('desc', $desc);

		$contents = $theme->output('admin/html/forms/colorpicker');

		return $contents;
	}

	/**
	 * Renders a dropdown in a form
	 *
	 * @since	5.0.0
	 * @access	public
	 */
	final public function renderDropdown($name, $title, $value, $options = array(), $attributes = '', $notes = '')
	{
		$desc = $this->getDescription($title);

		if ($notes) {
			$notes = JText::_($notes);
		}

		$theme = ED::themes();
		$theme->set('options', $options);
		$theme->set('notes', $notes);
		$theme->set('name', $name);
		$theme->set('title', $title);
		$theme->set('desc', $desc);
		$theme->set('attributes', $attributes);
		$theme->set('value', $value);

		$contents = $theme->output('admin/html/forms/dropdown');

		return $contents;
	}

	/**
	 * Renders a colorpicker settings
	 *
	 * @since	5.0.0
	 * @access	public
	 */
	final public function renderIconpicker($name, $title, $value, $defaultIcon)
	{
		$desc = $this->getDescription($title);

		$theme = ED::themes();
		$theme->set('value', $value);
		$theme->set('defaultIcon', $defaultIcon);
		$theme->set('name', $name);
		$theme->set('title', $title);
		$theme->set('desc', $desc);

		$contents = $theme->output('admin/html/forms/iconpicker');

		return $contents;
	}

	/**
	 * Renders a joomla editor
	 *
	 * @since	5.0.0
	 * @access	public
	 */
	final public function renderEditor($name, $title, $value, $options = array())
	{
		$desc = $this->getDescription($title);
		$jConfig = ED::jConfig();
		$editor = ED::getEditor($jConfig->get('editor'));

		$data = array(
			'attributes' => '',
			'class' => '',
			'instructions' => '',
			'size' => ''
		);

		$options = array_merge($data, $options);
		$options = (object) $options;

		$theme = ED::themes();
		$theme->set('editor', $editor);
		$theme->set('value', $value);
		$theme->set('name', $name);
		$theme->set('title', $title);
		$theme->set('desc', $desc);
		$theme->set('options', $options);
		
		$contents = $theme->output('admin/html/forms/editor');

		return $contents;
	}

	/**
	 * Renders a textbox for settings
	 *
	 * @since	5.0.0
	 * @access	public
	 */
	final public function renderTextbox($name, $title, $value, $options = array())
	{
		$desc = $this->getDescription($title);

		$data = array(
			'attributes' => '',
			'class' => '',

			'inputWrapperClass' => '',
			'instructions' => '',

			'postfix' => '',
			'prefix' => '',
			
			'placeholder' => '',
			'size' => '',
			'type' => 'text',

			'wrapperClass' => '',
			'wrapperAttributes' => ''
		);

		$options = array_merge($data, $options);
		$options = (object) $options;

		$theme = ED::themes();
		$theme->set('name', $name);
		$theme->set('title', $title);
		$theme->set('desc', $desc);
		$theme->set('value', $value);
		$theme->set('options', $options);
		
		$contents = $theme->output('admin/html/forms/textbox');

		return $contents;
	}

	/**
	 * Renders a textbox for settings
	 *
	 * @since	5.0.0
	 * @access	public
	 */
	final public function renderTextarea($name, $title, $value, $options = array())
	{
		$desc = $this->getDescription($title);
		
		$data = array(
			'attributes' => '',
			'class' => '',
			'instructions' => '',
			'rows' => null,
			'size' => ''
		);

		$options = array_merge($data, $options);
		$options = (object) $options;

		$theme = ED::themes();
		$theme->set('value', $value);
		$theme->set('name', $name);
		$theme->set('title', $title);
		$theme->set('desc', $desc);
		$theme->set('options', $options);
		
		$contents = $theme->output('admin/html/forms/textarea');

		return $contents;
	}


	/**
	 * Renders a password field for a form
	 *
	 * @since	5.0.0
	 * @access	public
	 */
	final public function renderPassword($name, $title, $value, $options = array())
	{
		$desc = $this->getDescription($title);

		$data = array(
			'attributes' => '',
			'class' => '',
			'instructions' => '',
			'placeholder' => '',
			'size' => ''
		);

		$options = array_merge($data, $options);
		$options = (object) $options;

		$theme = ED::themes();
		$theme->set('name', $name);
		$theme->set('title', $title);
		$theme->set('desc', $desc);
		$theme->set('value', $value);
		$theme->set('options', $options);
		
		$contents = $theme->output('admin/html/forms/password');

		return $contents;
	}

	/**
	 * Renders a toggle button settings
	 *
	 * @since	5.0.0
	 * @access	public
	 */
	final public function renderToggle($name, $title, $value, $attributes = '', $instructions = '', $wrapperAttributes = '')
	{
		$desc = $this->getDescription($title);

		if (is_array($wrapperAttributes)) {
			$wrapperAttributes = implode(' ', $wrapperAttributes);
		}

		$theme = ED::themes();
		$theme->set('value', $value);
		$theme->set('instructions', $instructions);
		$theme->set('name', $name);
		$theme->set('title', $title);
		$theme->set('desc', $desc);
		$theme->set('attributes', $attributes);
		$theme->set('wrapperAttributes', $wrapperAttributes);

		$contents = $theme->output('admin/html/forms/toggle');

		return $contents;
	}	
}
