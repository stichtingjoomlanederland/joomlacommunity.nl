<?php
/**
 * @package		Komento
 * @copyright	Copyright (C) 2012 Stack Ideas Private Limited. All rights reserved.
 * @license		GNU/GPL, see LICENSE.php
 *
 * Komento is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * See COPYRIGHT.php for copyright notices and details.
 */

defined('_JEXEC') or die('Restricted access');

require_once( dirname( __FILE__ ) . DIRECTORY_SEPARATOR . 'parent.php' );

class KomentoModelActions extends KomentoModel
{
	var $_total = null;
	var $_pagination = null;
	var $_data = null;
	var $flags = array('spam', 'offensive', 'offtopic');

	function __construct()
	{
		$this->db = $this->getDBO();

		parent::__construct();
	}

	function getData()
	{
		// Lets load the content ifit doesn't already exist
		if(empty($this->_data))
		{
			$mainframe	= JFactory::getApplication();
			$limit		= $mainframe->getUserStateFromRequest( 'com_komento.report.limit', 'limit', $mainframe->getCfg('list_limit'), 'int');
			$limitstart = $mainframe->getUserStateFromRequest( 'com_komento.report.limitstart', 'limitstart', 0, 'int' );

			$query = $this->_buildQuery();
			$this->_data = $this->_getList( $query, $limitstart, $limit) ;
		}

		return $this->_data;
	}

	function getPagination()
	{
		// Lets load the content ifit doesn't already exist
		if(empty($this->_pagination))
		{
			jimport('joomla.html.pagination');
			$this->_pagination = new JPagination( $this->getTotal(), $this->getState('limitstart'), $this->getState('limit') );
		}

		return $this->_pagination;
	}

	function getTotal()
	{
		// Lets load the content if it doesn't already exist
		if(empty($this->_total))
		{
			$query = 'SELECT COUNT(1) FROM (' . $this->_buildQuery() . ') as x';

			$this->db->setQuery($query);
			$this->_total = $this->db->loadResult();
		}

		return $this->_total;
	}

	function _buildQuery()
	{
		$mainframe	= JFactory::getApplication();

		$filter_publish 	= $mainframe->getUserStateFromRequest( 'com_komento.reports.filter_publish', 'filter_publish', '*', 'string' );
		$filter_component	= $mainframe->getUserStateFromRequest( 'com_komento.reports.filter_component', 'filter_component', '*', 'string' );
		$search 			= $mainframe->getUserStateFromRequest( 'com_komento.reports.search', 'search', '', 'string' );
		$search 			= $this->db->getEscaped( trim(JString::strtolower( $search ) ) );

		$filter_order		= $mainframe->getUserStateFromRequest( 'com_komento.reports.filter_order', 'filter_order', 'created', 'cmd' );
		$filter_order_Dir	= $mainframe->getUserStateFromRequest( 'com_komento.reports.filter_order_Dir',	'filter_order_Dir',	'DESC', 'word' );

		$querySelect  = 'SELECT a.*';
		$querySelect .= ', IFNULL(actions.reports, 0) AS reports';
		$querySelect .= ' FROM ' . $this->db->namequote('#__komento_comments') . ' AS a';

		$querySelect .= ' LEFT JOIN (';
		$querySelect .= ' SELECT comment_id as actionid,';
		$querySelect .= ' SUM(type = ' . $this->db->quote('report') . ') as reports';
		$querySelect .= ' FROM `#__komento_actions` ';
		$querySelect .= ' GROUP BY comment_id)';
		$querySelect .= ' AS actions on a.id = actions.actionid';

		$queryWhere[] = 'reports > 0';

		// filter by component
		if($filter_component != '*')
		{
			$queryWhere[] = 'component = ' . $this->db->quote($filter_component);
		}

		// filter by publish state
		if($filter_publish != '*')
		{
			$queryWhere[] = 'published = ' . $this->db->quote($filter_publish);
		}

		if($search)
		{
			$queryWhere[] = 'LOWER( comment ) LIKE \'%' . $search . '%\' ';
		}

		if(count($queryWhere) > 0)
		{
			$queryWhere = ' WHERE ' . implode(' AND ', $queryWhere);
		}

		$queryOrder			= ' GROUP BY a.id ORDER BY '.$filter_order.' '.$filter_order_Dir;

		$query = $querySelect . $queryWhere . $queryOrder;

		return $query;
	}

	function clearReports($comments)
	{
		$allComments = implode( ',', $comments);
		$query  = 'DELETE FROM ' . $this->db->namequote( '#__komento_actions' );
		$query .= ' WHERE ' . $this->db->namequote( 'comment_id' ) . ' IN (' . $allComments . ')';
		$query .= ' AND ' . $this->db->namequote( 'type' ) . ' = ' . $this->db->quote( 'report' );

		$this->db->setQuery( $query );

		if( !$this->db->query() )
		{
			$this->setError($this->db->getErrorMsg());
			return false;
		}

		return true;
	}

	function addAction($type, $comment_id, $user_id)
	{
		$comment	= Komento::getComment( $comment_id );

		$now			= Komento::getDate()->toMySQL();

		$actionsTable	= Komento::getTable( 'actions' );

		$actionsTable->type			= $type;
		$actionsTable->comment_id	= $comment_id;
		$actionsTable->action_by	= $user_id;
		$actionsTable->actioned		= $now;

		if(!$actionsTable->store())
		{
			return false;
			// return JText::_( 'COM_KOMENTO_LIKES_ERROR_SAVING_LIKES' );
		}

		return $actionsTable->id;
	}

	function removeAction($type = 'all', $comment_id, $user_id = 'all')
	{
		$where = array();
		$query  = 'DELETE FROM `#__komento_actions`';

		if($type !== 'all')
		{
			$where[] = '`type` = ' . $this->db->quote($type);
		}

		if($comment_id)
		{
			$where[] = '`comment_id` = ' . $this->db->quote($comment_id);
		}

		if($user_id !== 'all')
		{
			$where[] = '`action_by` = ' . $this->db->quote($user_id);
		}

		if(count($where))
		{
			$query .= ' WHERE ' . implode(' AND ', $where);
		}

		if( JFactory::getUser()->id === 0 )
		{
			$query .= ' LIMIT 1';
		}

		$this->db->setQuery($query);
		return $this->db->query();
	}

	function countAction($type, $comment_id, $user_id = 0)
	{
		$where = array();
		$query  = 'SELECT COUNT(1) FROM `#__komento_actions`';

		if($type)
		{
			$where[] = '`type` = ' . $this->db->quote($type);
		}

		if($comment_id)
		{
			$where[] = '`comment_id` = ' . $this->db->quote($comment_id);
		}

		if($user_id)
		{
			$where[] = '`action_by` = ' . $this->db->quote($user_id);
		}

		if(count($where))
		{
			$query .= ' WHERE ' . implode(' AND ', $where);
		}

		$this->db->setQuery($query);
		$result = $this->db->loadResult();

		return $result;
	}

	function liked($commentId, $userId)
	{
		if( $userId == 0 )
		{
			return 0;
		}

		return $this->countAction('likes', $commentId, $userId);
	}

	function reported($commentId, $userId)
	{
		if( $userId == 0 )
		{
			return 0;
		}

		return $this->countAction('report', $commentId, $userId);
	}

	function unlikeComment($commentId, $userId)
	{
		if( $userId == 0 )
		{
			return 0;
		}

		return $this->removeAction('likes', $commentId, $userId);
	}

	function getLikesReceived( $userId, $type = 'likes' )
	{
		$query  = 'SELECT COUNT(1) FROM ' . $this->db->namequote( '#__komento_actions' );
		$query .= ' WHERE ' . $this->db->namequote( 'comment_id' ) . ' IN (';
		$query .= ' SELECT ' . $this->db->namequote( 'id' ) . ' FROM ' . $this->db->namequote( '#__komento_comments' );
		$query .= ' WHERE ' . $this->db->namequote( 'created_by' ) . ' = ' . $this->db->quote( $userId );
		$query .= ' ) AND ' . $this->db->namequote( 'type' ) . ' = ' . $this->db->quote( $type );

		$this->db->setQuery( $query );
		return $this->db->loadResult();
	}

	function getLikesGiven( $userId, $type = 'likes' )
	{
		$query  = 'SELECT COUNT(1) FROM ' . $this->db->namequote( '#__komento_actions' );
		$query .= ' WHERE ' . $this->db->namequote( 'action_by' ) . ' = ' . $this->db->quote( $userId );
		$query .= ' AND ' . $this->db->namequote( 'type' ) . ' = ' . $this->db->quote( $type );

		$this->db->setQuery( $query );
		return $this->db->loadResult();
	}

	function getLikedUsers( $id )
	{
		$query  = 'SELECT * FROM ' . $this->db->nameQuote( '#__komento_actions' );
		$query .= ' WHERE ' . $this->db->nameQuote( 'comment_id' ) . ' = ' . $this->db->quote( $id );
		$query .= ' AND ' . $this->db->nameQuote( 'type' ) . ' = ' . $this->db->quote( 'likes' );

		$this->db->setQuery( $query );
		$result = $this->db->loadObjectList();

		foreach( $result as &$row )
		{
			$row->author = Komento::getProfile( $row->action_by );
		}

		return $result;
	}
}
