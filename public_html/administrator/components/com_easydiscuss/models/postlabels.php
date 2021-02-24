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

require_once dirname( __FILE__ ) . '/model.php';

class EasyDiscussModelPostLabels extends EasyDiscussAdminModel
{
	protected $_data = null;
	protected $_total = null;
	protected $_pagination = null;

	public function __construct()
	{
		parent::__construct();
	}

	/**
	 * Method to get the total number of the post labels
	 *
	 * @since   5.0.0
	 * @access  public
	 */
	public function getTotal()
	{
		// Lets load the content if it doesn't already exist
		if (empty($this->_total)) {
			$query = $this->_buildQuery();

			$this->_total = $this->_getListCount($query);
		}

		return $this->_total;
	}

	/**
	 * Method to build the query for the labels
	 *
	 * @since   5.0.0
	 * @access  protected
	 */
	protected function _buildQuery()
	{
		// Get the WHERE and ORDER BY clauses for the query
		$where = $this->_buildQueryWhere();
		$orderby = $this->_buildQueryOrderBy();
		$db = ED::db();

		$query = array();
		$query[] = 'SELECT * FROM ' . $db->nameQuote('#__discuss_post_labels') . ' AS a';
		$query[] = $where;
		$query[] = $orderby;

		$query = implode(' ', $query);

		return $query;
	}

	/**
	 * Method to build where query
	 *
	 * @since   5.0.0
	 * @access  protected
	 */
	protected function _buildQueryWhere()
	{
		$db = ED::db();

		$filter_state = $this->app->getUserStateFromRequest('com_easydiscuss.labels.filter_state', 'filter_state', '', 'word');
		$search = $this->app->getUserStateFromRequest('com_easydiscuss.labels.search', 'search', '', 'string');
		$search = $db->getEscaped(trim(EDJString::strtolower($search)));

		$where = array();

		if ($filter_state) {
			if ($filter_state == 'P') {
				$where[] = $db->nameQuote( 'a.published' ) . '=' . $db->Quote( '1' );
			}

			if ($filter_state == 'U' ) {
				$where[] = $db->nameQuote( 'a.published' ) . '=' . $db->Quote( '0' );
			}
		}

		if ($search) {
			$where[] = ' LOWER( title ) LIKE \'%' . $search . '%\' ';
		}

		$where = (count($where) ? ' WHERE ' . implode(' AND ', $where) : '');

		return $where;
	}

	/**
	 * Method to build order by query
	 *
	 * @since   5.0.0
	 * @access  protected
	 */
	protected function _buildQueryOrderBy()
	{
		$mainframe = JFactory::getApplication();

		$filter_order = $mainframe->getUserStateFromRequest('com_easydiscuss.labels.filter_order', 'filter_order', 'a.id', 'cmd');
		$filter_order_Dir = $mainframe->getUserStateFromRequest('com_easydiscuss.labels.filter_order_Dir', 'filter_order_Dir', '', 'word');

		$orderby = 'ORDER BY '.$filter_order.' '.$filter_order_Dir;

		return $orderby;
	}

	/**
	 * Method to get post labels data
	 *
	 * @since   5.0.0
	 * @access  public
	 */
	public function getData($usePagination = true)
	{
		// Lets load the content if it doesn't already exist
		if (empty($this->_data)) {
			$query = $this->_buildQuery();

			if ($usePagination) {
				$this->_data = $this->_getList($query, $this->getState('limitstart'), $this->getState('limit'));
			} else {
				$this->_data = $this->_getList($query);
			}
		}

		return $this->_data;
	}

	/**
	 * Method to get a pagination object for the post labels
	 *
	 * @since   5.0.0
	 * @access  public
	 */
	public function getPagination()
	{
		if (empty($this->_pagination)) {
			jimport('joomla.html.pagination');

			$this->_pagination = new JPagination($this->getTotal(), $this->getState('limitstart'), $this->getState('limit'));
		}

		return $this->_pagination;
	}

	/**
	 * Method to get the labels for the post
	 *
	 * @since   5.0.0
	 * @access  public
	 */
	public function getLabels($exclusion = [])
	{
		$db = ED::db();

		$query = array();
		$query[] = 'SELECT * FROM `#__discuss_post_labels`';
		$query[] = 'WHERE `published` = 1';

		if ($exclusion) {
			$excludedIds = ED::quoteArray($exclusion);

			$query[] = 'AND `id` NOT IN(' . implode(',', $excludedIds) . ')';
		}

		$query[] = 'ORDER BY `created` ASC';

		$query = implode(' ', $query);

		$db->setQuery($query);
		$result = $db->loadObjectList();

		if (!$result) {
			return $result;
		}

		$labels = [];

		foreach ($result as $row) {
			$labels[] = ED::label($row);
		}

		return $labels;
	}

	/**
	 * Retrieve the total number of thread which associated with this post label id
	 *
	 * @since   5.0.0
	 * @access  public
	 */
	public function getTotalPosts($id)
	{
		$db = ED::db();

		$query = array();
		$query[] = 'SELECT COUNT(1) FROM `#__discuss_thread`';
		$query[] = 'WHERE `post_status` = ' . $db->quote($id);

		$query = implode(' ', $query);

		$db->setQuery($query);
		$result = $db->loadResult();

		return $result;
	}

	/**
	 * Determine if the specific value for the column of the label table exists already or not
	 *
	 * @since   5.0.0
	 * @access  public
	 */
	public function isExists($column, $value, $id = null)
	{
		$db = ED::db();

		$query = array();
		$query[] = 'SELECT COUNT(1) FROM `#__discuss_post_labels`';
		$query[] = 'WHERE ' . $db->nameQuote($column) . ' = ' . $db->quote($value);

		if ($id) {
			$query[] = 'AND `id` != ' . $db->quote($id);
		}

		$query = implode(' ', $query);

		$db->setQuery($query);
		$result = $db->loadResult();

		return $result;
	}

	/**
	 * Retrieve list of post labels for filter
	 *
	 * @since   5.0.0
	 * @access  public
	 */
	public function getPostLabelFilterList()
	{
		$db = ED::db();

		$query = array();
		$query[] = 'SELECT * FROM `#__discuss_post_labels`';
		$query[] = 'ORDER BY ' . $db->nameQuote('id') . 'ASC';

		$query = implode(' ', $query);

		$db->setQuery($query);
		$result = $db->loadObjectList();

		return $result;
	}
}
