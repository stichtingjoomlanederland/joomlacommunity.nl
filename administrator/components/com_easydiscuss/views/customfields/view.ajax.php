<?php
/**
* @package		EasyDiscuss
* @copyright	Copyright (C) 2010 - 2015 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasyDiscuss is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');

require_once(DISCUSS_ADMIN_ROOT . '/views/views.php');

class EasyDiscussViewCustomFields extends EasyDiscussAdminView
{
	/**
	 * Retrieves a list of available options for a custom field type
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return	
	 */
	public function getOptions()
	{
		// Get the active field type
		$type = $this->input->get('type', '', 'word');

		// This custom field could have been edited.
		$id = $this->input->get('id', 0, 'int');

		if (!$type) {
			return $this->ajax->reject(JText::_('Invalid field type provided'));
		}

		$field = ED::field($id);
		$field->setType($type);

		$theme = ED::themes();
		$theme->set('field', $field);

		$output = $theme->output('admin/fields/options');

		return $this->ajax->resolve($output);
	}
}
