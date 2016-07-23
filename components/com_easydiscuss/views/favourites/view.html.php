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
defined('_JEXEC') or die('Unauthorized Access');

require_once(DISCUSS_ROOT . '/views/views.php');

class EasyDiscussViewFavourites extends EasyDiscussView
{
	/**
	 * Renders the favourites listing page
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return	
	 */
	public function display($tpl = null)
	{
		// Ensure that this feature is enabled
		if (!$this->config->get('main_favorite')) {
			ED::setMessage('COM_EASYDISCUSS_FEATURE_IS_DISABLED', 'error');
			return $this->app->redirect(EDR::_('index.php?option=com_easydiscuss&view=index', false));
		}

		ED::setPageTitle('COM_EASYDISCUSS_FAVOURITES_TITLE');
		ED::setMeta();

		// Load the user's profile
		$profile = ED::profile($this->my->id);

		// If profile is invalid, throw an error.
		if (!$profile->id || !$this->my->id) {
			return JError::raiseError(404, JText::_('COM_EASYDISCUSS_USER_ACCOUNT_NOT_FOUND'));
		}

		// Get user badges
		$badges = $profile->getBadges();

		// Add view
		$this->logView();

		$model = ED::model('Posts');

		$options = array(
			'userId' => $this->my->id,
			'filter' => 'favourites'
			);

		// $posts = $model->getData(true, 'latest', null, 'favourites');
		$posts = $model->getDiscussions($options);
		$posts = ED::formatPost($posts);

		$pagination = $model->getPagination();
		$pagination = $pagination->getPagesLinks();

		$this->set('posts', $posts);
		$this->set('profile', $profile);
		$this->set('badges', $badges);
		$this->set('pagination', $pagination);
		
		parent::display('favourites/default');
	}
}
