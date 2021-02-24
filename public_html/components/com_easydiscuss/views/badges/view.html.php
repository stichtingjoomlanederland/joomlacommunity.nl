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

class EasyDiscussViewBadges extends EasyDiscussView
{
	/**
	 * Renders a list of badges available in EasyDiscuss and badges achived by the particular user.
	 *
	 * @since	4.0
	 * @access	public
	 */
	public function display($tmpl = null)
	{
		if (!$this->config->get('main_points')) {
			return ED::redirect(EDR::_('view=index', false));
		}

		$id = $this->input->get('userid', null, 'int');

		$model = ED::model('Badges');

		$profile = ED::user($id);

		$title = JText::_('COM_EASYDISCUSS_BADGES_TITLE');
		
		ED::setPageTitle($title);

		// Set the meta for the page
		ED::setMeta();
		
		$options = [];

		if ($id) {
			$title = JText::sprintf('COM_EASYDISCUSS_BADGES_USER_TITLE', $profile->getName());

			if ($this->my->id == $profile->id) {
				$title = JText::_('COM_EASYDISCUSS_BADGES_USER_TITLE_MY_BADGE');
			}

			ED::setPageTitle($title);

			$options['user'] = (int) $id;
		}

		$badges = $model->getSiteBadges($options);

		if (!EDR::isCurrentActiveMenu('badges')) {
			$this->setPathway(JText::_('COM_EASYDISCUSS_BADGES'));
		}

		// Add canonical tag for this page
		$this->canonical('index.php?option=com_easydiscuss&view=badges');

		$this->set('title', $title);
		$this->set('badges', $badges);

		parent::display('badges/listings/default');
	}

	/**
	 * Retrieves information about a single badge
	 *
	 * @since	4.0
	 * @access	public
	 */
	public function listings()
	{
		$id = $this->input->get('id');

		if (!$this->config->get('main_points')) {
			return ED::redirect(EDR::_('view=index', false));
		}

		if (!$id) {
			return ED::redirect(EDR::_('index.php?option=com_easydiscuss&view=badges', false), JText::_('COM_EASYDISCUSS_INVALID_BADGE'));
		}

		$badge = ED::table('Badges');
		$badge->load($id);

		if (! EDR::isCurrentActiveMenu('badges')) {
			$this->setPathway(JText::_('COM_EASYDISCUSS_BADGES'), EDR::_('index.php?option=com_easydiscuss&view=badges'));
		}

		$this->setPathway(JText::_($badge->get('title')));

		ED::setPageTitle(JText::sprintf('COM_EASYDISCUSS_VIEWING_BADGE_TITLE', $this->escape($badge->title)));

		// Set meta tags.
		ED::setMeta($badge->id, ED_META_TYPE_BADGES, $badge->description);

		$users = $badge->getUsers();
		$prefix = $this->input->get('prefix', '', 'cmd');

		$this->set('prefix', $prefix);
		$this->set('badge', $badge);
		$this->set('users', $users);

		parent::display('badges/item/default');
	}
}
