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

class EasyDiscussViewAssigned extends EasyDiscussView
{
	public function display($tpl = null)
	{
		// Ensure that the user is logged in
		ED::requireLogin();

		ED::setPageTitle(JText::_('COM_EASYDISCUSS_PAGETITLE_ASSIGNED'));

		if (! EDR::isCurrentActiveMenu('assigned')) {
			$this->setPathway( JText::_('COM_EASYDISCUSS_BREADCRUMB_ASSIGNED'));
		}

		if (!ED::isModerator()) {
			throw ED::exception('COM_EASYDISCUSS_YOU_ARE_NOT_ALLOWED_HERE', ED_MSG_ERROR);
		}

		// Load the user's profile
		$profile = ED::profile($this->my->id);

		// If profile is invalid, throw an error.
		if (!$profile->id || !$this->my->id) {
			throw ED::exception('COM_EASYDISCUSS_USER_ACCOUNT_NOT_FOUND', ED_MSG_ERROR);
		}

		// [Model:Assigned]
		$model = ED::model('Assigned');

		// retrieve the assiged post
		$posts = $model->getPosts($this->my->id);
		$pagination = $model->getPagination();

		// format the post
		$posts = ED::formatPost($posts);

		$this->set('posts', $posts);
		$this->set('pagination', $pagination);

		parent::display('assigned/listings/default');
	}
}
