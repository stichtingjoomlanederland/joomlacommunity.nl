<?php
/**
* @package		EasyDiscuss
* @copyright	Copyright (C) 2010 Stack Ideas Private Limited. All rights reserved.
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
	public function showMoveDialog()
	{

		$categories	= DiscussHelper::populateCategories( '' , '' , 'select' , 'new_category', '' , true, true , true , true );

		$theme = ED::themes();
		$theme->set('categories', $categories);
		$contents = $theme->output('admin/dialogs/post.move.confirmation');

		return $this->ajax->resolve($contents);
	}

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
