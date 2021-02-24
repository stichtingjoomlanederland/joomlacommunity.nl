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

class EasyDiscussViewDashboard extends EasyDiscussView
{
	/**
	 * Displays confirmation dialog to delete an holiday
	 *
	 * @since	4.0
	 * @access	public
	 */
	public function confirmDelete()
	{
		$id = $this->input->get('id', 0, 'int');

		// @rule: Do not allow empty id or guests to delete files.
		if (!$id || empty($this->my->id)) {
			return $this->ajax->reject(JText::_('COM_EASYDISCUSS_NOT_ALLOWED'));
		}

		$theme = ED::themes();
		$theme->set('id', $id);
		$contents = $theme->output('site/dashboard/dialogs/delete.confirmation');

		return $this->ajax->resolve($contents);
	}

	/**
	 * Deletes the holiday item
	 *
	 * @since	4.0
	 * @access	public
	 */
	public function deleteHoliday()
	{
		$id = $this->input->get('id', 0, 'int');

		// @rule: Do not allow empty id or guests to delete files.
		if (!$id || empty($this->my->id)) {
			return $this->ajax->reject(JText::_('COM_EASYDISCUSS_NOT_ALLOWED'));
		}

		$holiday = ED::holiday($id);

		if (!$holiday->canDelete()) {
			die();
		}

		// Delete the holiday
		$holiday->delete();

		return $this->ajax->resolve();
	}

	/**
     * Toggle holiday state
     *
     * @since   4.0
     * @access  public
     */
	public function toggleHolidayState()
	{
		$id = $this->input->get('id', 0, 'int');
		$state = $this->input->get('state', 0, 'int');

		$holiday = ED::holiday($id);

		if (!$holiday->canManage()) {
			die();
		}
	
		$holiday->set('published', $state);
		$holiday->save();
		
		return $this->ajax->resolve();	
	}

	/**
	 * Confirmation dialog to approve post
	 *
	 * @since	4.0
	 * @access	public
	 */
	public function confirmApprovePost()
	{
		$id = $this->input->get('id', 0, 'int');

		ED::checkToken();

		if (!$id) {
			return $this->ajax->reject(JText::_('COM_EASYDISCUSS_NOT_ALLOWED'));
		}

		// Load the post
		$post = ED::post($id);

		$themes = ED::themes();
		$themes->set('post', $post);
		$contents = $themes->output('site/dashboard/dialogs/post.approve');

		return $this->ajax->resolve($contents);
	}

	/**
	 * Confirmation dialog to reject post
	 *
	 * @since	4.0
	 * @access	public
	 */
	public function confirmRejectPost()
	{
		$id = $this->input->get('id', 0, 'int');

		ED::checkToken();

		if (!$id) {
			return $this->ajax->reject(JText::_('COM_EASYDISCUSS_NOT_ALLOWED'));
		}

		// Load the post
		$post = ED::post($id);

		$themes = ED::themes();
		$themes->set('post', $post);
		$contents = $themes->output('site/dashboard/dialogs/post.reject');

		return $this->ajax->resolve($contents);
	}
}
