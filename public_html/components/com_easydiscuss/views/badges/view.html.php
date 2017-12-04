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

class EasyDiscussViewBadges extends EasyDiscussView
{
	/**
	 * Renders a list of badges available in EasyDiscuss
	 * and badges achived by the particular user.
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function display($tmpl = null)
	{
		$id = $this->input->get('userid', null);

		$model = ED::Model('Badges');
		$options = '';

		$profile = ED::user($id);

		$title = JText::_('COM_EASYDISCUSS_BADGES_TITLE');
		ED::setPageTitle(JText::_('COM_EASYDISCUSS_BADGES_TITLE'));

		if ($id) {
			$title = JText::sprintf('COM_EASYDISCUSS_BADGES_USER_TITLE', $profile->getName());
			if ($this->my->id == $profile->id) {
				$title = JText::_('COM_EASYDISCUSS_BADGES_USER_TITLE_MY_BADGE');
			}

			ED::setPageTitle($title);

			$options = array('user' => $id);
		}

		$badges = $model->getSiteBadges($options);

		if (! EDR::isCurrentActiveMenu('badges')) {
			$this->setPathway(JText::_('COM_EASYDISCUSS_BADGES'));
		}

		// Add canonical tag for this page
		$this->canonical('index.php?option=com_easydiscuss&view=badges&userid=' . $id);

		$this->set('title', $title);
		$this->set('badges', $badges);
		// This user is used for my achived badge.
		$this->set('user', $this->my->id);

		parent::display('badges/default');
	}

	/**
	 * Retrieves information about a single badge
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function listings()
	{
		$id = $this->input->get('id');

		if (!$id) {
			return $this->app->redirect(EDR::_('index.php?option=com_easydiscuss&view=badges', false), JText::_('COM_EASYDISCUSS_INVALID_BADGE'));
		}

		$badge = ED::table('Badges');
		$badge->load($id);

		if (! EDR::isCurrentActiveMenu('badges')) {
			$this->setPathway(JText::_('COM_EASYDISCUSS_BADGES'), EDR::_('index.php?option=com_easydiscuss&view=badges'));
		}

		$this->setPathway(JText::_($badge->get('title')));

		ED::setPageTitle(JText::sprintf('COM_EASYDISCUSS_VIEWING_BADGE_TITLE', $this->escape($badge->title)));

		$users = $badge->getUsers();

		$this->set('badge', $badge);
		$this->set('users', $users);

		parent::display('badges/item');
	}
}
