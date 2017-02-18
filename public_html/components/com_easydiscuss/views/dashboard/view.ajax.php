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

class EasyDiscussViewDashboard extends EasyDiscussView
{
	/**
	 * Displays confirmation dialog to delete an holiday
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

		$holiday = ED::holiday($id);

		// Delete the holiday
		$holiday->delete();

		$theme = ED::themes();
		$theme->set('id', $id);
		$contents = $theme->output('site/dashboard/dialogs/delete.success');

		return $this->ajax->resolve($contents);
	}

	/**
     * Toggle holiday state
     *
     * @since   4.0
     * @access  public
     * @param   string
     * @return
     */
	public function toggleState()
	{
		$id = $this->input->get('id', 0, 'int');
		$state = $this->input->get('state', 0, 'int');

		$holiday = ED::holiday($id);
	
		$holiday->set('published', $state);
		

		// save the holiday
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
