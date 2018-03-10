<?php
/**
* @package		EasyDiscuss
* @copyright	Copyright (C) 2010 ~ 2018 Stack Ideas Private Limited. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasyDiscuss is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Restricted access');

require_once DISCUSS_ADMIN_ROOT . '/views/views.php';

class EasyDiscussViewPosts extends EasyDiscussAdminView
{
	/**
	 * display move dialog
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	public function showMoveDialog()
	{

		$categories	= DiscussHelper::populateCategories( '' , '' , 'select' , 'new_category', '' , true, true , true , true );

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

		$model = ED::model("Threaded");

		$options = array();

		$options['search'] = $search;
		$options['filter'] = $state;

		if ($type == 'questions') {
			$category = $this->input->get('category', 0, 'int');

			$options['questions'] = true;
			$options['category'] = $category;
			$options['stateKey'] ='posts';
		}

		if ($type == 'replies') {
			$options['replies'] = true;
			$options['stateKey'] ='replies';
		}

		$pagination = $model->getPostPagination($options);

		$html = $pagination->getListFooter();

		return $this->ajax->resolve($html);

	}
}
