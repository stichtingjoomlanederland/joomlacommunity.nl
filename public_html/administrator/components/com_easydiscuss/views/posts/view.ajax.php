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

class EasyDiscussViewPosts extends EasyDiscussAdminView
{
	/**
	 * Confirmation to reject a pending post
	 *
	 * @since   5.0
	 * @access  public
	 */
	public function confirmReject()
	{
		$ids = $this->input->get('ids', array(), 'array');

		$theme = ED::themes();
		$theme->set('ids', $ids);

		$output = $theme->output('admin/dialogs/post.reject');

		return $this->ajax->resolve($output);
	}

	/**
	 * The dialog to allow site admin to change author of the post
	 *
	 * @since   5.0
	 * @access  public
	 */
	public function showAuthorDialog()
	{
		$ids = $this->input->get('cid', array(), 'array');

		if ($ids) {
			$ids = implode(',', $ids);
			$ids = base64_encode($ids);
		}

		$url = JURI::root() . 'administrator/index.php?option=com_easydiscuss&view=users&tmpl=component&browse=1&browsefunction=selectUser&cid=' . $ids;

		$theme = ED::themes();
		$theme->set('url', $url);

		$output = $theme->output('admin/dialogs/post.update.author');

		return $this->ajax->resolve($output);
	}

	/**
	 * Confirmation to approve a pending post
	 *
	 * @since   5.0
	 * @access  public
	 */
	public function confirmApprove()
	{
		$ids = $this->input->get('ids', array(), 'array');

		$theme = ED::themes();
		$theme->set('ids', $ids);

		$output = $theme->output('admin/dialogs/post.approve');

		return $this->ajax->resolve($output);
	}

	/**
	 * Confirmation to delete a pending post
	 *
	 * @since   5.0
	 * @access  public
	 */
	public function confirmDelete()
	{
		$ids = $this->input->get('ids', array(), 'array');

		$theme = ED::themes();
		$theme->set('ids', $ids);

		$output = $theme->output('admin/dialogs/post.delete');

		return $this->ajax->resolve($output);
	}

	/**
	 * Generate pagination for the page.
	 *
	 * @since	4.0.22
	 * @access	public
	 */
	public function pagination()
	{
		$type = $this->input->get('type', '', 'default');
		$search = $this->input->get('search', '', 'default');
		$state = $this->input->get('state', '', 'default');
		$label = $this->input->get('label', 0, 'int');

		$model = ED::model("Threaded");

		$options = array();

		$options['search'] = $search;
		$options['filter'] = $state;

		if ($type == 'questions') {
			$category = $this->input->get('category', 0, 'int');

			$options['questions'] = true;
			$options['category'] = $category;
			$options['stateKey'] ='posts';
			$options['postLabel'] = $label;
		}

		if ($type == 'replies') {
			$options['replies'] = true;
			$options['stateKey'] ='replies';
		}

		$pagination = $model->getPostPagination($options);

		$html = $pagination->getListFooter();

		return $this->ajax->resolve($html);

	}

	/**
	 * Displays honeypot data
	 *
	 * @since	5.0.0
	 * @access	public
	 */
	public function honeypot()
	{
		$id = $this->input->get('id', 0, 'int');

		$honeypot = ED::table('Honeypot');
		$honeypot->load($id);

		$theme = ED::themes();
		$theme->set('honeypot', $honeypot);

		$output = $theme->output('admin/posts/dialogs/honeypot');

		return $this->ajax->resolve($output);
	}

	/**
	 * display move dialog
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	public function showMoveDialog()
	{

		$categories	= ED::populateCategories( '' , '' , 'select' , 'new_category', '' , true, true , true , true );

		$theme = ED::themes();
		$theme->set('categories', $categories);
		$contents = $theme->output('admin/dialogs/post.move.confirmation');

		return $this->ajax->resolve($contents);
	}

	/**
	 * display approval dialog
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	public function showApproveDialog()
	{
		$id = $this->input->get('id', 0, 'int');

		// Test if a valid post id is provided.
		if (!$id) {
			$this->ajax->reject( JText::_('COM_EASYDISCUSS_INVALID_POST_ID'));
			return $this->ajax->send();
		}

		$theme = ED::themes();
		$theme->set('id', $id);
		$contents = $theme->output('admin/dialogs/post.moderate.confirmation');

		return $this->ajax->resolve($contents);
	}
}
