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

class EasyDiscussViewComments extends EasyDiscussAdminView
{
	/**
	 * Renders the display
	 *
	 * @since	5.0
	 * @access	public
	 */
	public function display($tpl = null)
	{
		$this->checkAccess('discuss.manage.posts');

		// Set page properties here
		$this->title('COM_ED_COMMENTS_TITLE');

		JToolbarHelper::publishList();
		JToolbarHelper::unpublishList();
		JToolBarHelper::divider();
		JToolbarHelper::deleteList();

		// Get the filter states
		$filter_state = $this->getUserState('comments.filter_state', 'filter_state', '*', 'word');

		// Search query
		$search = $this->getUserState('comments.search', 'search', '', 'string');
		$search = trim(strtolower($search));

		// Ordering
		$order = $this->getUserState('comments.filter_order', 'filter_order', 'id', 'cmd');
		$orderDirection = $this->getUserState('comments.filter_order_Dir', 'filter_order_Dir', '', 'word');

		$model = ED::model('Comments');
		$comments = $model->getComments();

		if ($comments) {
			foreach ($comments as $row) {
				$post = ED::post($row->post_id);

				$row->postTitle = $post->getTitle();
				$row->postLabel = $post->isQuestion() ? JText::_('COM_EASYDISCUSS_POST') : JText::_('COM_ED_REPLY');
				$row->postLink = $post->isQuestion() ? $post->getPermalink(true) : EDR::getRoutedURL($post->getReplyPermalink(), false, true);
			}
		}

		$pagination = $model->getPagination();

		$this->set('state', $filter_state);
		$this->set('comments', $comments);
		$this->set('pagination', $pagination);
		$this->set('search', $search);
		$this->set('order', $order);
		$this->set('orderDirection', $orderDirection);

		parent::display('comments/default');
	}

	/**
	 * Renders the tag form
	 *
	 * @since	5.0
	 * @access	public
	 */
	public function form($tpl = null)
	{
		$this->checkAccess('discuss.manage.posts');

		$id = $this->input->get('id', 0, 'int');
		$redirect = 'index.php?option=com_easydiscuss&view=comments';

		$comment = ED::table('Comment');
		$comment->load($id);

		if (!$comment->id) {
			ED::setMessage('COM_ED_COMMENT_NOT_EXIST', ED_MSG_ERROR);
			return ED::redirect($redirect);	
		}

		$this->title('COM_ED_COMMENTS_EDIT');

		JToolbarHelper::apply();
		JToolbarHelper::save();
		JToolBarHelper::cancel();

		$this->set('comment', $comment);

		parent::display('comments/form');
	}
}
