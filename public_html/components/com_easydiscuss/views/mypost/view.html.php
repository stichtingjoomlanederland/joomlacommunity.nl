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

class EasyDiscussViewMypost extends EasyDiscussView
{
	public function display($tpl = null)
	{
		// Ensure that the user is logged in
		ED::requireLogin();

		ED::setPageTitle(JText::_('COM_EASYDISCUSS_PAGETITLE_MYPOST'));

		if (! EDR::isCurrentActiveMenu('mypost')) {
			$this->setPathway( JText::_('COM_EASYDISCUSS_BREADCRUMB_MYPOST'));
		}

		// Load the user's profile
		$profile = ED::profile($this->my->id);

		// If profile is invalid, throw an error.
		if (!$profile->id || !$this->my->id) {
			throw ED::exception('COM_EASYDISCUSS_USER_ACCOUNT_NOT_FOUND', ED_MSG_ERROR);
		}

		$postsModel = ED::model('Posts');

		$options = array('filter' => 'questions', 'userId' => $profile->id, 'includeCluster' => true, 'private' => true);
		$posts = $postsModel->getDiscussions($options);

		// format resultset.
		$posts = ED::formatPost($posts);

		$pagination	= $postsModel->getPagination();

		$this->set('posts', $posts);
		$this->set('pagination', $pagination);

		parent::display('mypost/listing/default');
	}
}
