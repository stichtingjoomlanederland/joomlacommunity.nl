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

class EasyDiscussModelBadges extends EasyDiscussAdminModel
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
		$limit		= $mainframe->getUserStateFromRequest('com_easydiscuss.badges.limit', 'limit', $mainframe->getCfg('list_limit') , 'int');
		//$limit			= $mainframe->getUserStateFromRequest( 'com_easydiscuss.badges.limit', 'limit', DiscussHelper::getListLimit(), 'int');

		$limitstart	= JRequest::getVar('limitstart', 0, '', 'int');

		$this->setState('limit', $limit);
		$this->setState('limitstart', $limitstart);
	}

	public function getBadges( $exclusion = false )
	{
		if(empty($this->_data) )
		{
			$this->_data	= $this->_getList( $this->buildQuery( $exclusion ) , $this->getState('limitstart'), $this->getState('limit') );
		}
		return $this->_data;
	}

	private function buildQuery( $exclusion = false )
	{
		// Get the WHERE and ORDER BY clauses for the query
		$where		= $this->_buildQueryWhere( $exclusion );
		$orderby	= $this->_buildQueryOrderBy();
		$db			= DiscussHelper::getDBO();

		$query	= 'SELECT * FROM ' . $db->nameQuote( '#__discuss_badges' ) . ' AS a ';
		$query	.= $where . ' ';
		$query	.= $orderby;

		return $query;
	}

	function _buildQueryWhere( $exclusion = false )
	{
		$mainframe		= JFactory::getApplication();
		$db				= DiscussHelper::getDBO();

		$filter_state	= $mainframe->getUserStateFromRequest( 'com_easydiscuss.badges.filter_state', 'filter_state', '', 'word' );
		$search			= $mainframe->getUserStateFromRequest( 'com_easydiscuss.badges.search', 'search', '', 'string' );
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

		$exclusion	= trim( $exclusion );

		if( $exclusion )
		{
			$exclusion 	= explode( ',' , $exclusion );

			$query	= ' a.' . $db->nameQuote( 'id' ) . ' NOT IN(';

			for( $i = 0; $i < count( $exclusion); $i++ )
			{
				$query	.= $db->Quote( $exclusion[ $i ] );

				if( next( $exclusion ) !== false )
				{
					$query	.= ',';
				}
			}
			$query 	.= ')';

			$where[]	= $query;
		}

		$where 		= count( $where ) ? ' WHERE ' . implode( ' AND ', $where ) : '' ;

		return $where;
	}

	function _buildQueryOrderBy()
	{
		$mainframe			= JFactory::getApplication();

		$filter_order		= $mainframe->getUserStateFromRequest( 'com_easydiscuss.badges.filter_order', 		'filter_order', 	'a.created', 'cmd' );
		$filter_order_Dir	= $mainframe->getUserStateFromRequest( 'com_easydiscuss.badges.filter_order_Dir',	'filter_order_Dir',	'ASC', 'word' );

		$orderby 	= ' ORDER BY '.$filter_order.' '.$filter_order_Dir;

		return $orderby;
	}


	/**
	 * Get a list of user badge history
	 *
	 **/
	public function getBadgesHistory( $userId )
	{
		$db		= DiscussHelper::getDBO();
		$query	= 'SELECT * FROM ' . $db->nameQuote( '#__discuss_users_history' ) . ' '
				. 'WHERE ' . $db->nameQuote( 'user_id' ) . '=' . $db->Quote( $userId ) . ' '
				. 'LIMIT 0,20';

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
