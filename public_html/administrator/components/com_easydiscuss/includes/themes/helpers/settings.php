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

class EasyDiscussThemesHelperSettings extends EasyDiscussAbstractForm
{
	/**
	 * Render the colorpicker used in settings
	 *
	 * @since	5.0.0
	 * @access	public
	 */
	public function colorpicker($name, $title, $restoreColor)
	{
		$config = ED::config();
		$value = $config->get($name);

		return $this->renderColorpicker($name, $title, $value, $restoreColor);
	}

	/**
	 * Render the dropdown form used in settings
	 *
	 * @since	5.0.0
	 * @access	public
	 */
	public function dropdown($name, $title, $desc = '', $options = array(), $attributes = '', $notes = '')
	{
		$config = ED::config();
		$value = $config->get($name);

		return $this->renderDropdown($name, $title, $value, $options, $attributes, $notes);
	}

	/**
	 * Render the textbox form used in settings
	 *
	 * @since	5.0.0
	 * @access	public
	 */
	public function password($name, $title, $options = array())
	{
		$config = ED::config();
		$value = $config->get($name);

		return $this->renderPassword($name, $title, $value, $options);
	}

	/**
	 * Render the textarea form used in settings
	 *
	 * @since	5.0.0
	 * @access	public
	 */
	public function textarea($name, $title, $desc = '', $options = array(), $instructions = '', $class = '', $textboxClass = '')
	{
		$config = ED::config();
		$value = $config->get($name);
	
		// Ensure that the options is an array
		if (!is_array($options)) {
			$options = array();
		}

		if (isset($options['value'])) {
			$value = $options['value'];
		}

		if (isset($options['defaultValue'])) {
			$value = $config->get($name, $options['defaultValue']);
		}


		$data = array(
			'attributes' => '',
			'size' => '',
			'instructions' => $instructions,
			'class' => $textboxClass,
			'rows' => null
		);

		$options = array_merge($data, $options);

		return $this->renderTextarea($name, $title, $value, $options);
	}

	/**
	 * Render the textbox form used in settings
	 *
	 * @since	5.0.0
	 * @access	public
	 */
	public function textbox($name, $title, $desc = '', $options = array(), $instructions = '', $class = '', $textboxClass = '')
	{
		$config = ED::config();
		$value = $config->get($name);

		if (isset($options['defaultValue'])) {
			$value = $config->get($name, $options['defaultValue']);
		}


		$data = array(
			'attributes' => '',
			'size' => '',
			'postfix' => '',
			'prefix' => '',
			'instructions' => $instructions,
			'inputWrapperClass' => $class,
			'class' => $textboxClass
		);

		$options = array_merge($data, $options);

		return $this->renderTextbox($name, $title, $value, $options);
	}

	/**
	 * Render the toggle form used in settings
	 *
	 * @since	5.0.0
	 * @access	public
	 */
	public function toggle($name, $title, $desc = '', $attributes = '', $instructions = '', $wrapperAttributes = '')
	{
		$config = ED::config();
		$value = $config->get($name);

		return $this->renderToggle($name, $title, $value, $attributes, $instructions, $wrapperAttributes);
	}
}
