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

class EasyDiscussViewFavourites extends EasyDiscussView
{
	/**
	 * Renders the favourites listing page
	 *
	 * @since	4.0
	 * @access	public
	 */
	public function display($tpl = null)
	{
		// Ensure that this feature is enabled
		if (!$this->config->get('main_favorite')) {
			return ED::redirect(EDR::_('index.php?option=com_easydiscuss&view=index', false));
		}

		// Ensure that the user is logged in
		ED::requireLogin();

		ED::setPageTitle('COM_EASYDISCUSS_FAVOURITES_TITLE');
		ED::setMeta();

		// If profile is invalid, throw an error.
		if (!$this->my->id) {
			throw ED::exception('COM_EASYDISCUSS_USER_ACCOUNT_NOT_FOUND', ED_MSG_ERROR);
		}

		// Add view
		$this->logView();

		$model = ED::model('Posts');

		$options = array(
			'userId' => $this->my->id,
			'filter' => 'favourites'
		);

		$posts = $model->getDiscussions($options);
		$posts = ED::formatPost($posts);

		$pagination = $model->getPagination();

		$this->set('posts', $posts);
		$this->set('pagination', $pagination);
		
		parent::display('favourites/listings/default');
	}
}
