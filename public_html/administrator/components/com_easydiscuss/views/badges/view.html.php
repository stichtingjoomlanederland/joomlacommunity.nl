<?php
/**
* @package		EasyDiscuss
* @copyright	Copyright (C) 2010 - 2017 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasyDiscuss is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');

require_once(DISCUSS_ADMIN_ROOT . '/views/views.php');

class EasyDiscussViewBadges extends EasyDiscussAdminView
{
	/**
	 * Renders the output of badges listings
	 *
	 * @since	4.0
	 * @access	public
	 */
	public function display($tpl = null)
	{
		$this->checkAccess('discuss.manage.badges');

		JToolbarHelper::addNew();
		JToolBarHelper::divider();
		JToolbarHelper::publishList();
		JToolbarHelper::unpublishList();
		JToolBarHelper::divider();
		JToolbarHelper::deleteList();
		JToolbarHelper::divider();
		JToolBarHelper::custom( 'rules' , 'cog' , '' , JText::_( 'COM_EASYDISCUSS_MANAGE_RULES_BUTTON' ) , false );
		JToolBarHelper::custom('assign', 'checkbox-checked', '' , JText::_('COM_EASYDISCUSS_BADGES_MASS_ASSIGN_BUTTON'), false);

		// Get the states
		$filter = $this->getUserState('badges.filter_state', 'filter_state', '*', 'word');

		// Search requests
		$search = $this->getUserState('badges.search', 'search', '', 'string');
		$search = trim(strtolower($search));

		// Ordering
		$order = $this->getUserState('badges.filter_order', 'filter_order', 'a.id', 'cmd');
		$orderDirection = $this->getUserState('badges.filter_order_Dir', 'filter_order_Dir', 'word');

		// Exclusions
		$exclusion = $this->input->get('exclude', '', 'default');

		$model = ED::model('Badges', true);
		$badges = $model->getBadges($exclusion);
		$pagination = $model->getPagination();

		// Determines if the current request is shown in a modal window.
		$browse = $this->input->get('browse', 0, 'int');
		$browseFunction = $this->input->get('browseFunction', '', 'default');

		if (!$browse) {
			$this->title('COM_EASYDISCUSS_BADGES_TITLE');
		}

		if ($badges) {
			foreach ($badges as &$badge) {

				$badge->date = ED::date($badge->created);
				$badge->totalUsers = $this->getTotalUsers($badge->id);

				if ($browse) {
					$badge->editLink = 'javascript:parent.' . $browseFunction . '(' . $badge->id . ')';
				} else {
					$badge->editLink = JRoute::_('index.php?option=com_easydiscuss&view=badges&layout=form&id=' . $badge->id);
				}
			}
		}

		$this->set('filter', $filter);
		$this->set('browseFunction', $browseFunction);
		$this->set('browse', $browse);
		$this->set('badges', $badges);
		$this->set('pagination', $pagination);
		$this->set('search', $search);
		$this->set('order', $order);
		$this->set('orderDirection', $orderDirection);

		parent::display('badges/default');
	}

	/**
	 * Renders the badge form
	 *
	 * @since	4.0
	 * @access	public
	 */
	public function form($tpl = null)
	{
		$this->checkAccess('discuss.manage.badges');
		$this->title('COM_EASYDISCUSS_BADGES_TITLE');

		JToolBarHelper::apply();
		JToolBarHelper::save();
		JToolBarHelper::save2new();
		JToolBarHelper::cancel();

		$id = $this->input->get('id', 0, 'int');

		$badge = ED::table('Badges');
		$badge->load($id);

		if (!$badge->created) {
			$date = ED::date();
			$badge->created	= $date->toMySQL();
		}

		// There could be some errors here.
		if (JRequest::getMethod() == 'POST') {

			$post = JRequest::get('post');
			$badge->bind($post);

			// Description might contain html codes
			$description = JRequest::getVar('description' , '' , 'post' , 'string' , JREQUEST_ALLOWRAW );
			$badge->description = $description;
		}


		// Get the editor
		$editor = JFactory::getEditor($this->jconfig->get('editor'));

		$model	= ED::model('Badges');
		$rules	= $model->getRules();
		$badges	= $this->getBadges();

		$this->set('editor', $editor);
		$this->set('badges', $badges);
		$this->set('rules', $rules);
		$this->set('badge', $badge);

		parent::display('badges/form');
	}

	public function getBadges()
	{
		$exclude = array('.svn', 'CVS', '.DS_Store', '__MACOSX', 'index.html');
		$badges	= JFolder::files(DISCUSS_BADGES_PATH, '.', false, false, $exclude);

		return $badges;
	}


	public function getTotalUsers( $badgeId )
	{
		$db		= DiscussHelper::getDBO();
		$query	= 'SELECT COUNT(1) FROM ' . $db->nameQuote( '#__discuss_badges_users' ) . ' '
				. 'WHERE ' . $db->nameQuote( 'badge_id' ) . '=' . $db->Quote( $badgeId );
		$db->setQuery( $query );

		return $db->loadResult();
	}

	public function assign()
	{
		$this->checkAccess('discuss.manage.badges');
		$this->title('COM_EASYDISCUSS_BADGES_MASS_ASSIGN_TITLE');

		parent::display('badges/assign');
	}
}
