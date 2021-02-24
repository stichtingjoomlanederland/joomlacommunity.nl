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

class EasyDiscussViewUsers extends EasyDiscussView
{
	public function isFeatureAvailable()
	{
		if (!$this->config->get('main_user_listings')) {
			return false;
		}

		return true;
	}

	/**
	 * Renders users listing
	 *
	 * @since	5.0.0
	 * @access	public
	 */
	public function display($tmpl = null)
	{
		// Set the page attributes
		$pageTitle = 'COM_ED_USERS_TITLE';

		if (!EDR::isCurrentActiveMenu('users')) {
			$this->setPathway('COM_ED_USERS_BREADCRUMBS');
		}

		// If being searched
		$search = $this->input->get('search', '', 'string');

		// check if the public user have permission to access
		if (!$this->my->id && !$this->config->get('main_profile_public')) {
			ED::setMessage('COM_EASYDISCUSS_LOGIN_TO_VIEW_USER_LISTING_PAGE');
			$redirect = EDR::_('view=index', false);
			return ED::redirect($redirect);
		}

		// Get the list of users
		$model = ED::model('Users');
		$users = $model->getData($search, $this->config->get('main_exclude_members'));
		$pagination	= $model->getPaginationFrontend($search);

		$pagination->setVar('search', $search);

		// Format the result
		$users = ED::formatUsers($users);

		// Append page title
		ED::setPageTitle($pageTitle, $pagination);
			
		// Set the meta for the page
		ED::setMeta();

		$this->set('users', $users);
		$this->set('pagination', $pagination);
		$this->set('search', $search);

		parent::display('users/listings/default');
	}
}
