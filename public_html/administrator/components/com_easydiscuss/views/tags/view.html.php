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

class EasyDiscussViewTags extends EasyDiscussAdminView
{
	/**
	 * Renders the display
	 *
	 * @since	4.0
	 * @access	public
	 */
	public function display($tpl = null)
	{
		$this->checkAccess('discuss.manage.tags');

		// Set page properties here
		$this->title('COM_EASYDISCUSS_TAGS_TITLE');

		JToolbarHelper::addNew();
		JToolbarHelper::publishList();
		JToolbarHelper::unpublishList();
		JToolBarHelper::divider();
		JToolbarHelper::deleteList();

		// Get the filter states
		$filter_state = $this->getUserState('tags.filter_state', 'filter_state', '*', 'word');

		// Search query
		$search = $this->getUserState('tags.search', 'search', '', 'string');
		$search = trim(strtolower($search));

		// Ordering
		$order = $this->getUserState('tags.filter_order', 'filter_order', 'id', 'cmd');
		$orderDirection = $this->getUserState('tags.filter_order_Dir', 'filter_order_Dir', '', 'word');

		// Get data from the model
		$model = ED::model('Tags');
		$tags = $model->getData();

		foreach ($tags as $tag) {
			$tag->count = $model->getUsedCount($tag->id);
			$tag->title	= JString::trim($tag->title);
			$tag->alias	= JString::trim($tag->alias);
			$tag->user = JFactory::getUser($tag->user_id);
		}

		$pagination = $model->getPagination();

		$this->set('state', $filter_state);
		$this->set('tags', $tags);
		$this->set('pagination', $pagination);
		$this->set('search', $search);
		$this->set('order', $order);
		$this->set('orderDirection', $orderDirection);

		parent::display('tags/default');
	}

	/**
	 * Renders the tag form
	 *
	 * @since	4.0
	 * @access	public
	 */
	public function form($tpl = null)
	{
		$this->checkAccess('discuss.manage.tags');

		$id = $this->input->get('id', 0, 'int');

		$tag = ED::table('Tags');
		$tag->load($id);

		$this->title('COM_EASYDISCUSS_EDITING_TAG');

		JToolbarHelper::apply();
		JToolbarHelper::save();
		JToolbarHelper::save2new();
		JToolBarHelper::cancel();

		if (!$tag->id) {
			$this->title('COM_EASYDISCUSS_ADD_NEW_TAG');
		}

		$tag->title	= JString::trim($tag->title);
		$tag->alias	= JString::trim($tag->alias);

		// Generate All tags for merging selections
		$model = ED::model('Tags', true);
		$rows = $model->getData(false);

		$tagList = array();
		array_push($tagList, JHTML::_('select.option', 0, 'Select tag', 'value', 'text', false));

		if (!empty($rows)) {
			foreach ($rows as $row) {

				if ($row->id != $tag->id) {
					$tagList[] = JHTML::_('select.option', $row->id, $row->title);
				}
			}
		}

		// If this is a new entry, we need to set the default properties
		if (!$tag->id) {

			$date = ED::date();

			$tag->created = $date->toSql();
			$tag->published	= true;
		}

		$this->set('tag', $tag);
		$this->set('tagList', $tagList);

		parent::display('tags/form');
	}

}
