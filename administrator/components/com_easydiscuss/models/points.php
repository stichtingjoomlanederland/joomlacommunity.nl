<?php
/**
 * @package		EasyDiscuss
 * @copyright	Copyright (C) 2010 Stack Ideas Private Limited. All rights reserved.
 * @license		GNU/GPL, see LICENSE.php
 *
 * EasyDiscuss is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * See COPYRIGHT.php for copyright notices and details.
 */
defined('_JEXEC') or die('Restricted access');

require_once dirname( __FILE__ ) . '/model.php';

class EasyDiscussModelPoints extends EasyDiscussAdminModel
{
	/**
	 * Blogs data array
	 *
	 * @var array
	 */
	var $_data = null;

	/**
	 * Pagination object
	 *
	 * @var object
	 */
	var $_pagination = null;

	/**
	 * Configuration data
	 *
	 * @var int	Total number of rows
	 **/
	var $_total;

	/**
	 * Constructor
	 *
	 * @since 1.5
	 */
	function __construct()
	{
		parent::__construct();

		$mainframe 	= JFactory::getApplication();

		//get the number of events from database
		$limit		= $mainframe->getUserStateFromRequest('com_easydiscuss.points.limit', 'limit', $mainframe->getCfg('list_limit') , 'int');
		$limitstart	= JRequest::getVar('limitstart', 0, '', 'int');

		$this->setState('limit', $limit);
		$this->setState('limitstart', $limitstart);
	}

	public function getPoints()
	{
		if(empty($this->_data) )
		{
			$this->_data	= $this->_getList( $this->buildQuery() , $this->getState('limitstart'), $this->getState('limit') );
		}
		return $this->_data;
	}

	private function buildQuery()
	{
		// Get the WHERE and ORDER BY clauses for the query
		$where		= $this->_buildQueryWhere();
		$orderby	= $this->_buildQueryOrderBy();
		$db			= DiscussHelper::getDBO();

		$query	= 'SELECT * FROM ' . $db->nameQuote( '#__discuss_points' ) . ' AS a ';
		$query	.= $where . ' ';
		$query	.= $orderby;

		return $query;
	}

	function _buildQueryWhere()
	{
		$mainframe		= JFactory::getApplication();
		$db				= DiscussHelper::getDBO();

		$filter_state	= $mainframe->getUserStateFromRequest( 'com_easydiscuss.points.filter_state', 'filter_state', '', 'word' );
		$search			= $mainframe->getUserStateFromRequest( 'com_easydiscuss.points.search', 'search', '', 'string' );
		$search			= $db->getEscaped( trim(JString::strtolower( $search ) ) );

		$where = array();

		if ( $filter_state )
		{
			if ( $filter_state == 'P' )
			{
				$where[] = $db->nameQuote( 'a.published' ) . '=' . $db->Quote( '1' );
			}
			else if ($filter_state == 'U' )
			{
				$where[] = $db->nameQuote( 'a.published' ) . '=' . $db->Quote( '0' );
			}
		}

		if ($search)
		{
			$where[] = ' LOWER( a.title ) LIKE \'%' . $search . '%\' ';
		}

		$where 		= count( $where ) ? ' WHERE ' . implode( ' AND ', $where ) : '' ;

		return $where;
	}

	function _buildQueryOrderBy()
	{
		$mainframe			= JFactory::getApplication();

		$filter_order		= $mainframe->getUserStateFromRequest( 'com_easydiscuss.points.filter_order', 		'filter_order', 	'a.created', 'cmd' );
		$filter_order_Dir	= $mainframe->getUserStateFromRequest( 'com_easydiscuss.points.filter_order_Dir',	'filter_order_Dir',	'ASC', 'word' );

		$orderby			= ' ORDER BY '.$filter_order.' '.$filter_order_Dir;

		return $orderby;
	}


	/**
	 * Get a list of user badge history
	 *
	 **/
	public function getPointsHistory( $userId )
	{
		$db		= DiscussHelper::getDBO();
		// $query	= 'SELECT * FROM ' . $db->nameQuote( '#__discuss_users_history' )
		// 		. ' WHERE ' . $db->nameQuote( 'user_id' ) . '=' . $db->Quote( $userId )
		// 		. ' ORDER BY `created` DESC'
		// 		. ' LIMIT 0,20';

		$my = JFactory::getUser();

		// Obtain the category that I can view

		// I am able to view if my usergroup is added in the category permission "view discussion"
		$viewableCats = array();

		// First get all the accessible parentId
		$parentCats = array();
		$childCats = array();

		$parentCats = DiscussHelper::getAccessibleCategories();

		foreach( $parentCats as $parentCat )
		{
			$viewableCats[] = $parentCat->id;
		}

		// Second get the child cats that are accessible
		foreach( $parentCats as $parentCat )
		{
			$childCats = DiscussHelper::getAccessibleCategories( $parentCat->id );
			foreach( $childCats as $childCat )
			{
				$viewableCats[] = $childCat->id;
			}
		}

		$query	= 'SELECT a.*, ' . $db->Quote( 'post' ) . ' as `type`'
				. ' FROM ' . $db->nameQuote( '#__discuss_users_history' ) . ' AS a'
				. ' LEFT JOIN ' . $db->nameQuote( '#__discuss_posts' ) . ' AS b'
				. ' ON a.' . $db->nameQuote( 'content_id' ) . '=' . 'b.' . $db->nameQuote( 'id' )
				. ' WHERE a.' . $db->nameQuote( 'user_id' ) . '=' . $db->Quote( $userId )
				. ' AND a.' . $db->nameQuote( 'command' )

				. ' IN(' . $db->Quote( 'easydiscuss.new.discussion') . ',' . $db->Quote( 'easydiscuss.new.reply' ) . ','
				. $db->Quote( 'easydiscuss.answer.reply' ) . ',' . $db->Quote( 'easydiscuss.new.comment' ) . ','
				. $db->Quote( 'easydiscuss.like.discussion' ) . ',' . $db->Quote( 'easydiscuss.like.reply' ) . ','
				. $db->Quote( 'easydiscuss.resolved.discussion' ) . ',' . $db->Quote( 'easydiscuss.vote.reply' ) . ','
				. $db->Quote( 'easydiscuss.unvote.reply' ) . ')';

		if ($viewableCats) {
			$query .= ' AND b.' . $db->nameQuote( 'category_id' ) . ' IN(' . implode( $viewableCats, ',' ) . ')';
		}

		$query .= ' UNION'
				. ' SELECT a.*, ' . $db->Quote( 'profile' ) . ' as `type`'
				. ' FROM ' . $db->nameQuote( '#__discuss_users_history' ) . ' AS a'
				. ' WHERE a.' . $db->nameQuote( 'user_id' ) . '=' . $db->Quote( $userId )
				. ' AND a.' . $db->nameQuote( 'command' )
				. ' IN(' . $db->Quote( 'easydiscuss.new.avatar') . ' , ' . $db->Quote( 'easydiscuss.update.profile' ) .  ')'
				. ' ORDER BY ' . $db->nameQuote( 'created' ) . ' DESC'
				. ' LIMIT 0,20';



		$db->setQuery( $query );
		$result	= $db->loadObjectList();

		return $result;
	}

	/**
	 * Method to get a pagination object for the events
	 *
	 * @access public
	 * @return integer
	 */
	function &getPagination()
	{
		// Lets load the content if it doesn't already exist
		if ( empty( $this->_pagination ) )
		{
			jimport('joomla.html.pagination');
			$this->_pagination = new JPagination( $this->getTotal() , $this->getState('limitstart'), $this->getState('limit') );
		}

		return $this->_pagination;
	}

	/**
	 * Method to return the total number of rows
	 *
	 * @access public
	 * @return integer
	 */
	function getTotal()
	{
		// Load total number of rows
		if( empty($this->_total) )
		{
			$this->_total	= $this->_getListCount( $this->buildQuery() );
		}

		return $this->_total;
	}

	public function getRules()
	{
		$db		= DiscussHelper::getDBO();
		$query	= 'SELECT * FROM ' . $db->nameQuote( '#__discuss_rules' );
		$db->setQuery( $query );
		return $db->loadObjectList();
	}
}
