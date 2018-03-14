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
defined('_JEXEC') or die('Restricted access');

require_once(DISCUSS_ROOT . '/views/views.php');

class EasyDiscussViewAttachments extends EasyDiscussView
{
	/**
	 * Displays confirmation dialog to delete an attachment
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return	
	 */
	public function confirmDelete()
	{
		$id = $this->input->get('id', 0, 'int');

		// @rule: Do not allow empty id or guests to delete files.
		if (!$id || empty( $this->my->id)) {
			return $this->ajax->reject(JText::_('COM_EASYDISCUSS_NOT_ALLOWED'));
		}

		$attachment = ED::attachment($id);

		// Ensure that only post owner or admin can delete it.
		if (!$attachment->canDelete()) {
			return $this->ajax->reject(JText::_('COM_EASYDISCUSS_NOT_ALLOWED_DELETE_ATTACHEMENT_HERE'));
		}

		$theme = ED::themes();
		$theme->set('id', $id);
		$contents = $theme->output('site/attachments/dialogs/delete.confirmation');

		return $this->ajax->resolve($contents);
	}

	/**
	 * Deletes the attachment item
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return	
	 */
	public function delete()
	{
		$id = $this->input->get('id', 0, 'int');

		// @rule: Do not allow empty id or guests to delete files.
		if (!$id || empty($this->my->id)) {
			return $this->ajax->reject(JText::_('COM_EASYDISCUSS_NOT_ALLOWED'));
		}

		$attachment = ED::attachment($id);

		// Ensure that only post owner or admin can delete it.
		if (!$attachment->canDelete()) {
			return $this->ajax->reject(JText::_('COM_EASYDISCUSS_NOT_ALLOWED_DELETE_ATTACHEMENT_HERE'));
		}

		// Delete the attachment
		$attachment->delete();

		$theme = ED::themes();
		$theme->set('id', $id);
		$contents = $theme->output('site/attachments/dialogs/delete.success');

		return $this->ajax->resolve($contents);
	}
}
