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

class EasyDiscussField extends EasyDiscuss
{
	// This is the DiscussConversation table
	public $table = null;

	public function __construct($item)
	{
		parent::__construct();

		$this->table = ED::table('CustomFields');

		// For object that is being passed in
		if (is_object($item) && !($item instanceof DiscussCustomFields)) {
			$this->table->bind($item);
		}

		// If the object is DiscussConversation, just map the variable back.
		if ($item instanceof DiscussCustomFields) {
			$this->table = $item;
		}

		// If this is an integer
		if (is_int($item) || is_string($item)) {
			$this->table->load($item);
		}

		// If this is not being loaded, we need to set the default attributes
		if (!$this->table->id) {
			$this->table->published = true;
			$this->table->required = false;
		}
	}

	/**
	 * Magic method to get properties which don't exist on this object but on the table
	 *
	 * @since	4.0
	 * @access	public
	 */
	public function __get($key)
	{
		if (isset($this->table->$key)) {
			return $this->table->$key;
		}

		if (isset($this->$key)) {
			return $this->$key;
		}

		return $this->table->$key;
	}

	/**
	 * Allows caller to set the type
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return	
	 */
	public function setType($type)
	{
		$this->table->type = $type;
	}
	
	/**
	 * Formats a field value
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return	
	 */
	public function format($value)
	{
		// For textarea's we need to do newline to break
		if ($this->table->type == 'area') {
			return nl2br($value);
		}

		// Try to unserialize the value
		$tmp = @unserialize($value);

		if ($tmp === false) {
			return $value;
		} else {
			// Implode the values if they are an array
			if (is_array($tmp)) {
				
				$value = '';
				$total = count($tmp);
				
				for ($i = 0; $i < $total; $i++) {
					$value .= '<span>' . $tmp[$i] . '</span>';

					if (($i + 1) < $total) {
						$value .= ',';
					}
				}
			}
		}

		return $value;
	}

	/**
	 * Allows caller to bind properties to the table without directly accessing it
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return	
	 */
	public function bind($data)
	{
		$this->table->bind($data);

		$this->bindedData = $data;
	}

	/**
	 * Given an option, bind it on the field
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return	
	 */
	public function bindOptions($options = array())
	{
		$options = serialize($options);

		$this->table->params = $options;

		return true;
	}

	/**
	 * Performs validation of the field data
	 *
	 * @since	4.0
	 * @access	public
	 */
	public function validate()
	{
		// Ensure that the user selected at least a type of the field
		if (!$this->table->type) {
			$this->setError('COM_EASYDISCUSS_INVALID_CUSTOMFIELDS_TYPE');

			return false;
		}

		// Ensure that we at least have the custom field title
		if (!$this->table->title) {
			$this->setError('COM_EASYDISCUSS_INVALID_CUSTOMFIELDS_TITLE');

			return false;
		}

		return true;
	}

	/**
	 * Saves the custom field
	 *
	 * @since	4.0
	 * @access	public
	 */
	public function save()
	{
		// We need to rebuild the custom field's ordering before we save the new field
		$model = ED::model('CustomFields');
		$model->rebuildOrdering();

		// Save the new / edited custom field
		$state = $this->table->store();

		// After the field is saved, saved the permissions
		$this->savePermissions();

		return $state;
	}

	/**
	 * Deletes a custom field
	 *
	 * @since	4.0
	 * @access	public
	 */
	public function delete()
	{
		// Try to delete the field first
		$state = $this->table->delete();

		if (!$state) {
			$this->setError($this->table->getError());

			return false;
		}

		// Delete rules associated with this field
		$model = ED::model('CustomFields');
		$model->deleteCustomFieldsValue($this->table->id, 'field');
		$model->deleteCustomFieldsRule($this->table->id);

		return true;
	}

	/**
	 * This can be called by caller to update the permissions of the custom field
	 *
	 * @since	4.0
	 * @access	public
	 */
	public function savePermissions($data = array())
	{
		if (!$data && isset($this->bindedData)) {
			$data = $this->bindedData;
		}
		
		// Save customfields ACL
		$model = ED::model('CustomFields');

		// Pass in the custom field id and the form information
		$model->saveCustomFieldRule($this->table->id, $data);
	}

	/**
	 * Publishes this custom field
	 *
	 * @since	4.0
	 * @access	public
	 */
	public function publish()
	{
		$this->table->published = true;
		$this->save();
	}

	/**
	 * Unpublishes a field
	 *
	 * @since	4.0
	 * @access	public
	 */
	public function unpublish()
	{
		$this->table->published = false;
		$this->save();
	}

	/**
	 * Determines if this field has tooltips
	 *
	 * @since	4.0
	 * @access	public
	 */
	public function hasTooltips()
	{
		$hasTooltips = !empty($this->table->tooltips);

		return $hasTooltips;
	}
	/**
	 * Determines if this field type has options
	 *
	 * @since	4.0
	 * @access	public
	 */
	public function hasOptions()
	{
		$hasOptions = in_array($this->table->type, array('radio', 'check', 'select', 'multiple'));

		return $hasOptions;
	}

	/**
	 * Get a list of acl that is assigned to this custom field
	 *
	 * @since	4.0
	 * @access	public
	 */
	public function getAssignedGroups($action = 'view')
	{
		$model = ED::model('CustomFields');
		$groups = $model->getAssignedGroups($this->table->id, $action);

		return $groups;
	}

	/**
	 * Returns a friendly type string
	 *
	 * @since	4.0
	 * @access	public
	 */
	public function getFriendlyType()
	{
		$text = 'COM_EASYDISCUSS_CUSTOMFIELDS_' . strtoupper($this->type);

		return JText::_($text);
	}

	/**
	 * Returns a field section string
	 *
	 * @since	4.0
	 * @access	public
	 */
	public function getSection()
	{
		$text = 'COM_EASYDISCUSS_CUSTOMFIELDS_QUESTIONS';

		if ($this->section == DISCUSS_CUSTOMFIELDS_SECTION_REPLIES) {
			$text = 'COM_EASYDISCUSS_CUSTOMFIELDS_REPLIES';
		}

		return JText::_($text);
	}

	/**
	 * Gets a list of options for a custom field
	 *
	 * @since	4.0
	 * @access	public
	 */
	public function getOptions()
	{
		$options = unserialize($this->table->params);
		
		return $options;
	}

	/**
	 * Retrieves the value for a field
	 *
	 * @since	4.0
	 * @access	public
	 */
	public function getValue(EasyDiscussPost $post)
	{
		if (!$post->id) {
			return;
		}

		$model = ED::model('CustomFields');
		$value = $model->getFieldValue($this->id, $post->id);

		// Try to unserialize the value
		$tmp = @unserialize($value);

		if ($tmp === false) {
			return $value;
		}

		$value = $tmp;

		return $value;
	}

	/**
	 * Renders the fields form
	 *
	 * @since	4.0
	 * @access	public
	 */
	public function getForm($value = null)
	{
		$namespace = 'site/fields/' . $this->table->type;

		// Get the list of options
		$options = $this->getOptions();

		$theme = ED::themes();
		$theme->set('value', $value);
		$theme->set('field', $this);
		$theme->set('options', $options);
		
		$output = $theme->output($namespace);

		return $output;
	}

	/**
	 * Allows caller to set the ordering of the custom field
	 *
	 * @since	4.0
	 * @access	public
	 */
	public function move($direction)
	{
		$delta = $direction == 'up' ? -1 : 1;

		return $this->table->move($delta);
	}
}
