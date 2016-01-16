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

class KomentoModelComments extends KomentoModel
{
	public $_total = null;
	public $_comments = null;

	// set views without depth
	// move this to hidden config?
	private $viewWithoutDepth = array('rss', 'dashboard', 'pending');

	public function __construct()
	{
		$this->db = $this->getDBO();

		parent::__construct();
	}

	function getPagination()
	{
		// Lets load the content if it doesn't already exist
		if (empty($this->_pagination))
		{
			jimport('joomla.html.pagination');
			$view				= JRequest::getVar('view');
			$mainframe			= JFactory::getApplication();
			$limit				= $mainframe->getUserStateFromRequest( 'com_komento.' . $view . '.limit', 'limit', $mainframe->getCfg('list_limit'), 'int');
			$limitstart 		= $mainframe->getUserStateFromRequest( 'com_komento.' . $view . '.limitstart', 'limitstart', 0, 'int' );
			$this->_pagination	= new JPagination( $this->_total, $limitstart, $limit );
		}

		return $this->_pagination;
	}

	public function getCount( $component = 'all', $cid = 'all', $options = array() )
	{
		// define default values
		$defaultOptions	= array(
			'sort'			=> 'default',
			'limit'			=> 0,
			'limitstart'	=> 0,
			'search'		=> '',
			'sticked'		=> 'all',
			'published'		=> 1,
			'userid'		=> 'all',
			'parentid'		=> 'all',
			'threaded'		=> 0,
			'random'		=> 0
		);
		$options = Komento::mergeOptions( $defaultOptions, $options );

		$queryTotal	= $this->buildTotal( $component, $cid, $options );
		$queryWhere = $this->buildWhere( $component, $cid, $options );

		$query = $queryTotal . $queryWhere;

		$this->db->setQuery( $query );
		return $this->db->loadResult();
	}

	public function getOverallRatings( $component = 'all', $cid = 'all', $options = array() )
	{
		$config		= Komento::getConfig();
		$userId		= JFactory::getUser()->id;

		// define default values
		$defaultOptions	= array(
			'sort'			=> 'default',
			'limit'			=> 0,
			'limitstart'	=> 0,
			'search'		=> '',
			'sticked'		=> 'all',
			'published'		=> 1,
			'userid'		=> 'all',
			'parentid'		=> 'all',
			'threaded'		=> 0,
			'random'		=> 0,
			'ratings'		=> true
		);

		// take the input values and clear unexisting keys
		$options = Komento::mergeOptions( $defaultOptions, $options );

		// the actuall data query
		$query 		= 'SELECT AVG(' . $this->db->nameQuote('ratings') . ') as ' . $this->db->nameQuote('value') . ', COUNT(1) AS ' . $this->db->nameQuote('total') . ' FROM ' . $this->db->nameQuote('#__komento_comments');
		$queryWhere = $this->buildWhere( $component, $cid, $options );
		$queryOrder = $this->buildOrder( $component, $cid, $options );
		$queryLimit = $this->buildLimit( $component, $cid, $options );

		$query		= $query . $queryWhere . $queryOrder . $queryLimit;
		$this->db->setQuery( $query );

		$ratings	= $this->db->loadObject();

		if (!$ratings) {
			return false;
		}

		$ratings->value	= round($ratings->value);

		return $ratings;
	}

	public function getComments( $component = 'all', $cid = 'all', $options = array() )
	{
		$config		= Komento::getConfig();
		$userId		= JFactory::getUser()->id;

		// define default values
		$defaultOptions	= array(
			'sort'			=> 'default',
			'limit'			=> 0,
			'limitstart'	=> 0,
			'search'		=> '',
			'sticked'		=> 'all',
			'published'		=> 1,
			'userid'		=> 'all',
			'parentid'		=> 'all',
			'threaded'		=> 0,
			'random'		=> 0
		);

		// take the input values and clear unexisting keys
		$options = Komento::mergeOptions( $defaultOptions, $options );

		// the actuall data query
		$query = $this->buildQuery( $component, $cid, $options );

		$this->db->setQuery( $query );

		$result = $this->db->loadObjectList();

		$comments = array();

		// bind it to table object
		foreach( $result as $row )
		{
			$table = Komento::getTable( 'comments' );
			$table->bind( $row );

			$comments[] = $table;
		}

		// build random
		if( $options['random'] )
		{
			$comments	= $this->buildRandom( $comments, $options );
		}

		if( $this->db->getErrorNum() > 0 )
		{
			JError::raiseError( $this->db->getErrorNum() , $this->db->getErrorMsg() . $this->db->stderr());
		}

		return $comments;
	}

	private function buildQuery( $component = 'all', $cid = 'all', $options = array() )
	{
		$querySelect = $this->buildSelect( $component, $cid, $options );
		$queryWhere = $this->buildWhere( $component, $cid, $options );
		$queryOrder = $this->buildOrder( $component, $cid, $options );
		$queryLimit = $this->buildLimit( $component, $cid, $options );

		$query	= $querySelect . $queryWhere . $queryOrder . $queryLimit;

		return $query;
	}

	private function buildTotal( $component = 'all', $cid = 'all', $options = array() )
	{
		$queryTotal = 'SELECT COUNT(1) FROM ' . $this->db->nameQuote( '#__komento_comments' );

		return $queryTotal;
	}

	private function buildSelect( $component = 'all', $cid = 'all', $options = array() )
	{
		$querySelect = 'SELECT * FROM ' . $this->db->nameQuote( '#__komento_comments' );

		return $querySelect;
	}

	private function buildWhere( $component = 'all', $cid = 'all', $options = array() )
	{
		$queryWhere = array();

		if( $component !== 'all' )
		{
			$queryWhere[] = $this->db->nameQuote( 'component' ) . ' = ' . $this->db->quote( $component );
		}

		if( $cid !== 'all' && !empty( $cid ) )
		{
			if( is_array( $cid ) )
			{
				$cid = implode( ',', $cid );
				$queryWhere[] = $this->db->nameQuote( 'cid' ) . ' IN (' . $cid . ')';
			}
			else
			{
				$queryWhere[] = $this->db->nameQuote( 'cid' ) . ' = ' . $this->db->quote( $cid );
			}
		}

		if ($options['published'] !== 'all') {
			$queryWhere[] = $this->db->nameQuote( 'published' ) . ' = ' . $this->db->quote( $options['published'] );
		}

		if ($options['sticked'] !== 'all') {
			$queryWhere[] = $this->db->nameQuote( 'sticked' ) . ' = ' . $this->db->quote( 1 );
		}

		if ($options['userid'] !== 'all') {
			
			if (is_array($options['userid'])) {
				$userId = implode(',', $options['userid']);
				$queryWhere[] = $this->db->nameQuote('created_by') . ' IN (' . $userId . ')';
			} else {
				$queryWhere[] = $this->db->nameQuote('created_by') . ' = ' . $this->db->quote($options['userid']);
			}
		}

		if( $options['parentid'] !== 'all' )
		{
			$queryWhere[] = $this->db->nameQuote( 'parent_id' ) . ' = ' . $this->db->quote( $options['parentid'] );
		}

		if( $options['search'] !== '' )
		{
			$queryWhere[] = $this->db->nameQuote( 'comment' ) . ' LIKE ' . $this->db->quote( '%' . $options['search'] . '%' );
		}

		if (isset($options['ratings']) && $options['ratings']) {
			$queryWhere[]	= $this->db->nameQuote('ratings') . ' !=' . $this->db->Quote('');
		}

		if( count( $queryWhere ) > 0 )
		{
			$queryWhere = ' WHERE ' . implode(' AND ', $queryWhere);
		}
		else
		{
			$queryWhere = '';
		}

		return $queryWhere;
	}

	private function buildOrder( $component = 'all', $cid = 'all', $options = array() )
	{
		$queryOrder = '';

		if( $options['threaded'] )
		{
			switch( strtolower( $options['sort'] ) )
			{
				case 'latest' :
					$queryOrder = ' ORDER BY ' . $this->db->nameQuote( 'rgt' ) . ' DESC';
					break;
				case 'oldest' :
				default :
					$queryOrder = ' ORDER BY ' . $this->db->nameQuote( 'lft' ) . ' ASC';
			}
		}
		else
		{
			switch( strtolower( $options['sort'] ) )
			{
				case 'latest' :
					$queryOrder = ' ORDER BY ' . $this->db->nameQuote( 'created' ) . ' DESC';
					break;
				case 'oldest' :
				default :
					$queryOrder = ' ORDER BY ' . $this->db->nameQuote( 'created' ) . ' ASC';
					break;
			}
		}

		return $queryOrder;
	}

	private function buildLimit( $component = 'all', $cid = 'all', $options = array() )
	{
		$config = Komento::getConfig();
		$queryLimit = '';

		// if random is on, then don't parse limit here
		if($options['random'] == 1)
		{
			return $queryLimit;
		}

		// todo: this model shouldn't listen to jrequest for limit, should just obey the options passed in
		if($options['limit'] > 0)
		{
			$queryLimit = ' LIMIT ' . $options['limitstart'] . ',' . $options['limit'];
		}
		else
		{
			$jLimit		= JFactory::getConfig()->get('list_limit');
			$limit		= JRequest::getInt('kmt-limit', null) !== null ? JRequest::getInt('kmt-limit') : $config->get('max_comments_per_page', $jLimit);
			$limitstart = JRequest::getInt('kmt-limitstart', null) !== null ? JRequest::getInt('kmt-limitstart') : $options['limitstart'];
			$queryLimit	= ' LIMIT ' . $limitstart . ',' . $limit;
		}

		return $queryLimit;
	}

	private function buildRandom( $comments, $options = array() )
	{
		$limit = 0;

		if( $options['limit'] > 0 )
		{
			$limit = $options['limit'];
		}
		else
		{
			$jLimit		= JFactory::getConfig()->get('list_limit');
			$limit		= JRequest::getInt('limit', null) !== null ? JRequest::getInt('limit') : $config->get('max_comments_per_page', $jLimit);
		}

		if( count( $comments ) <= 1 ) {
			return $comments;
		}

		$limit = $limit > count( $comments ) ? count( $comments ) : $limit;

		$indexes = array_rand( $comments, $limit );

		$tmp = array();

		if( is_array( $indexes ) )
		{
			foreach( $indexes as $index )
			{
				$tmp[] = $comments[$index];
			}
		}
		else
		{
			$tmp[] = $comments[$indexes];
		}


		return $tmp;
	}

	function getData($options = array())
	{
		$mainframe	= JFactory::getApplication();
		$view		= JRequest::getVar('view');

		// define default values
		$defaultOptions	= array(
			'no_tree'	=> 0,
			'component' => '*',
			'published'	=> '*',
			'userid'	=> '',
			'parent_id'	=> 0,
			'no_search' => 0,
			'no_child'	=> 0
		);

		// take the input values and clear unexisting keys
		$options = Komento::mergeOptions( $defaultOptions, $options );

		$querySelect		= '';
		$querySelectCount	= '';
		$queryWhere			= array();
		$queryOrder			= '';
		$queryLimit			= '';
		$queryTotal			= '';

		$filter_publish 	= $mainframe->getUserStateFromRequest( 'com_komento.' . $view . '.filter_publish', 'filter_publish', $options['published'], 'string' );
		$filter_component	= $mainframe->getUserStateFromRequest( 'com_komento.' . $view . '.filter_component', 'filter_component', $options['component'], 'string' );
		$filter_order		= $mainframe->getUserStateFromRequest( 'com_komento.' . $view . '.filter_order', 'filter_order', 'created', 'string' );
		$filter_order_Dir	= $mainframe->getUserStateFromRequest( 'com_komento.' . $view . '.filter_order_Dir',	'filter_order_Dir',	'DESC', 'word' );
		$search 			= $mainframe->getUserStateFromRequest( 'com_komento.' . $view . '.search', 'search', '', 'string' );
		$limit				= $mainframe->getUserStateFromRequest( 'com_komento.' . $view . '.limit', 'limit', $mainframe->getCfg('list_limit'), 'int');
		$limitstart 		= $mainframe->getUserStateFromRequest( 'com_komento.' . $view . '.limitstart', 'limitstart', 0, 'int' );

		Komento::import( 'helper', 'string' );
		$search 			= KomentoStringHelper::escape( trim( JString::strtolower( $search ) ) );

		// clear search if nosearch = 1
		// for view parent purposes during search
		if( $options['no_search'] )
		{
			$search = '';
		}

		/*if( $options['no_tree'] == 0 )
		{
			// $querySelect  = 'SELECT x.*, COUNT(y.id) - 1 AS childs FROM ' . $this->db->namequote('#__komento_comments') . ' AS x';
			// $querySelect .= ' INNER JOIN ' . $this->db->namequote('#__komento_comments') . ' AS y';
			// $querySelect .= ' ON x.component = y.component';
			// $querySelect .= ' AND x.cid = y.cid';
			// $querySelect .= ' AND x.lft BETWEEN y.lft AND y.rgt';

			$querySelect = 'SELECT * FROM ' . $this->db->nameQuote( '#__komento_comments' );
		}
		else
		{
			$querySelect  = 'SELECT x.*, y.depth FROM (';
			$querySelect .= ' SELECT a.*, COUNT(a.id) - 1 AS childs FROM ' . $this->db->namequote('#__komento_comments') . ' AS a';
			$querySelect .= ' INNER JOIN ' . $this->db->namequote('#__komento_comments') . ' AS b';
			$querySelect .= ' WHERE a.component = b.component';
			$querySelect .= ' AND a.cid = b.cid';
			$querySelect .= ' AND b.lft BETWEEN a.lft AND a.rgt';
			$querySelect .= ' GROUP BY a.id) AS x';
			$querySelect .= ' LEFT JOIN (';
			$querySelect .= ' SELECT a.*, COUNT(c.id) - 1 AS depth FROM ' . $this->db->namequote('#__komento_comments') . ' AS a';
			$querySelect .= ' INNER JOIN ' . $this->db->namequote('#__komento_comments') . ' AS c';
			$querySelect .= ' WHERE a.component = c.component';
			$querySelect .= ' AND a.cid = c.cid';
			$querySelect .= ' AND a.lft BETWEEN c.lft AND c.rgt';
			$querySelect .= ' GROUP BY a.id) AS y ON x.id = y.id';
		}*/

		$querySelect = 'SELECT * FROM ' . $this->db->nameQuote( '#__komento_comments' );

		$querySelectCount = 'SELECT COUNT(1) FROM ' . $this->db->nameQuote( '#__komento_comments' );

		// filter by component
		if( $filter_component != '*' )
		{
			$queryWhere[] = $this->db->nameQuote( 'component' ) . ' = ' . $this->db->quote( $filter_component );
		}

		// filter by publish state
		if( $filter_publish != '*' )
		{
			$queryWhere[] = $this->db->nameQuote( 'published' ) . ' = ' . $this->db->quote( $filter_publish );
		}

		/*
		// filter by user
		// not implemented yet
		$filter_user		= $mainframe->getUserStateFromRequest( 'com_komento.comments.filter_user', 'filter_user', '*', 'string' );
		if($filter_user != '*')
		{
			$queryWhereForA[] = 'a.created_by = ' . $this->db->quote($filter_user);
		}
		*/

		if( $search )
		{
			$queryWhere[] = 'LOWER( ' . $this->db->nameQuote( 'comment' ) . ' ) LIKE \'%' . $search . '%\' ';
		}
		else
		{
			if( $options['no_tree'] == 0 )
			{
				$queryWhere[] = $this->db->nameQuote( 'parent_id' ) . ' = ' . $this->db->quote( $options['parent_id'] );
			}
		}

		if(count($queryWhere) > 0)
		{
			$queryWhere  = ' WHERE ' . implode(' AND ', $queryWhere);
		}
		else
		{
			$queryWhere = '';
		}

		$queryOrder = ' ORDER BY ' . $filter_order . ' ' . $filter_order_Dir;

		if( $options['parent_id'] == 0 && $limit != 0 )
		{
			$queryLimit = ' LIMIT ' . $limitstart . ',' . $limit;
		}

		$queryTotal = $querySelectCount . $queryWhere;

		// set pagination
		$this->db->setQuery( $queryTotal );
		$this->_total = $this->db->loadResult();

		jimport('joomla.html.pagination');
		$this->_pagination	= new JPagination( $this->_total, $limitstart, $limit );

		// actual query
		$query = $querySelect . $queryWhere . $queryOrder . $queryLimit;

		$this->db->setQuery($query);
		$result = $this->db->loadObjectList();

		if($this->db->getErrorNum() > 0)
		{
			JError::raiseError( $this->db->getErrorNum() , $this->db->getErrorMsg() . $this->db->stderr());
		}

		if( !empty( $result ) && $options['no_child'] == 0 )
		{
			$ids = array();
			foreach( $result as $row )
			{
				$ids[] = $row->id;
			}

			$childCount = $this->getChildCount( $ids );

			foreach( $result as &$row )
			{
				$row->childs = isset( $childCount[$row->id] ) ? $childCount[$row->id] : 0;
			}
		}

		return $result;
	}

	function publish( $comments = array(), $publish = 1 )
	{
		if( $comments == null )
		{
			return false;
		}

		if( !is_array($comments) )
		{
			$comments = array($comments);
		}

		$affectChild = JRequest::getInt('affectchild', 0);

		if( count( $comments ) > 0 )
		{
			$now = Komento::getDate()->toMySql();

			$publishDateColumn = '';

			if($publish == 0)
			{
				$publishDateColumn = 'publish_down';
			}
			else
			{
				$publishDateColumn = 'publish_up';
			}

			$nodes = $comments;

			foreach($nodes as $comment)
			{
				$related = array();

				if($publish == 1)
				{
					$related = array_merge($related, self::getParents($comment));
				}

				if($publish == 0 || ($publish == 1 && $affectChild))
				{
					$related = array_merge($related, self::getChilds($comment));
				}

				if(count($related) > 0)
				{
					$comments = array_merge($comments, $related);
				}
			}

			$comments		= array_unique($comments);
			$allComments	= implode( ',' , $comments );

			foreach( $comments as $comment )
			{
				if( !Komento::getComment( $comment )->publish( $publish ) )
				{
					return false;
				}
			}

			return true;
		}
		return false;
	}

	function unpublish($comments = array(), $publish = 0)
	{
		return self::publish($comments, $publish);
	}

	function remove($comments = array())
	{
		if( $comments == null )
		{
			return false;
		}

		if( !is_array($comments) )
		{
			$comments = array($comments);
		}

		$affectChild = JRequest::getInt('affectchild', 0);

		if( count( $comments ) > 0 )
		{
			$node = $comments;

			foreach($node as $comment)
			{
				if($affectChild)
				{
					$childs = self::getChilds($comment);
					if(count($childs) > 0)
					{
						$comments = array_merge($comments, $childs);
					}
				}
				else
				{
					self::moveChildsUp($comment);
				}
			}

			$comments		= array_unique($comments);

			foreach($comments as $comment)
			{
				$obj = Komento::getComment($comment);
				$obj->delete();
			}

			return true;
		}
		return false;
	}

	function stick($comments = array(), $stick = 1)
	{
		if( !is_array($comments) )
		{
			$comments = array($comments);
		}

		if( count( $comments) > 0 )
		{
			$allComments = implode( ',', $comments );

			$query  = 'UPDATE ' . $this->db->namequote( '#__komento_comments' );
			$query .= ' SET ' . $this->db->namequote( 'sticked' ) . ' = ' . $this->db->quote( $stick );
			$query .= ' WHERE ' . $this->db->namequote( 'id' ) . ' IN (' . $allComments . ')';

			$this->db->setQuery( $query );

			if( !$this->db->query() )
			{
				$this->setError($this->db->getErrorMsg());
				return false;
			}

			foreach( $comments as $comment )
			{
				// process all activities
				if( $stick )
				{
					$activity = Komento::getHelper( 'activity' )->process( 'stick', $comment );
				}
				else
				{
					$activity = Komento::getHelper( 'activity' )->process( 'unstick', $comment );
				}
			}

			return true;
		}
		return false;
	}

	function unstick($comments = array())
	{
		return self::stick( $comments, 0 );
	}

	function flag($comments = array(), $flag)
	{
		$affectChild = JRequest::getInt('affectchild', 0);

		if( count( $comments ) > 0 )
		{
			$user = JFactory::getUser()->id;

			if($affectChild)
			{
				$node = $comments;

				foreach($node as $comment)
				{
					$childs = self::getChilds($comment);
					if(count($childs) > 0)
					{
						$comments = array_merge($comments, $childs);
					}
				}
			}

			$comments		= array_unique($comments);
			$allComments	= implode( ',', $comments);

			$query  = 'UPDATE ' . $this->db->namequote( '#__komento_comments' );
			$query .= ' SET ' . $this->db->namequote( 'flag' ) . ' = ' . $this->db->quote($flag);
			$query .= ', ' . $this->db->namequote('flag_by') . ' = ' .$this->db->quote($user);
			$query .= ' WHERE ' . $this->db->namequote( 'id' ) . ' IN (' . $allComments . ')';

			$this->db->setQuery( $query );

			if( !$this->db->query() )
			{
				$this->setError($this->db->getErrorMsg());
				return false;
			}

			return true;
		}
		return false;
	}

	// todo: should support options/component/cid filtering as well
	function getTotalComment( $userId = 0 )
	{
		$config	= Komento::getConfig();

		$sql = Komento::getSql();

		$sql->select( '#__komento_comments' )
			->column( '1', 'total', 'count', true )
			->where( 'published', '1' );

		if( !empty( $userId ) )
		{
			$sql->where( 'created_by', $userId );
		}

		$result	= $sql->loadResult();

		return (empty($result)) ? 0 : $result;
	}

	function getTotalReplies( $userId = 0 )
	{
		$config	= Komento::getConfig();

		$where  = array();

		$query	= 'SELECT COUNT(1) FROM ' . $this->db->nameQuote( '#__komento_comments' );

		$where[] = '`parent_id` <> 0';

		if(! empty($userId))
			$where[]  = '`created_by` = ' . $this->db->Quote($userId);

		$extra 		= ( count( $where ) ? ' WHERE ' . implode( ' AND ', $where ) : '' );
		$query      = $query . $extra;

		$this->db->setQuery( $query );

		$result	= $this->db->loadResult();

		return (empty($result)) ? 0 : $result;
	}

	function getUniqueComponents()
	{
		$query = 'SELECT DISTINCT ' . $this->db->namequote('component') . ' FROM ' . $this->db->namequote( '#__komento_comments' ) . ' ORDER BY ' . $this->db->namequote('component');
		$this->db->setQuery($query);
		$components = $this->db->loadResultArray();

		return $components;
	}

	function getLatestComment($component, $cid, $parentId = 0)
	{
		$query  = 'SELECT `id`, `lft`, `rgt` FROM `#__komento_comments`';
		$query .= ' WHERE `component` = ' . $this->db->Quote($component);
		$query .= ' AND `cid` = ' . $this->db->Quote($cid);
		$query .= ' AND `parent_id` = ' . $this->db->Quote($parentId);
		$query .= ' ORDER BY `lft` DESC LIMIT 1';

		$this->db->setQuery($query);
		$result	= $this->db->loadObject();

		return $result;
	}

	function getCommentDepth($id)
	{
		$comment = Komento::getComment( $id );
		$component = $comment->component;
		$cid = $comment->cid;

		$query  = 'SELECT COUNT(`parent`.`id`)-1 AS `depth`';
		$query .= ' FROM `#__komento_comments` AS `node`';
		$query .= ' INNER JOIN `#__komento_comments` AS `parent` on parent.component = node.component and node.cid = parent.cid';
		$query .= ' WHERE `node`.`component` = ' . $this->db->Quote($component);
		$query .= ' AND `node`.`cid` = ' . $this->db->Quote($cid);
		$query .= ' AND `node`.`id` = ' . $this->db->Quote($id);
		$query .= ' AND `node`.`lft` BETWEEN `parent`.`lft` AND `parent`.`rgt`';
		$query .= ' GROUP BY `node`.`id`';

		$this->db->setQuery($query);
		$result = $this->db->loadObject();

		return $result->depth;
	}

	function updateCommentSibling($component, $cid, $nodeValue)
	{
		$query  = 'UPDATE `#__komento_comments` SET `rgt` = `rgt` + 2';
		$query .= ' WHERE `component` = ' . $this->db->Quote($component);
		$query .= ' AND `cid` = ' . $this->db->Quote($cid);
		$query .= ' AND `rgt` > ' . $this->db->Quote($nodeValue);
		$this->db->setQuery($query);
		$this->db->query();

		$query  = 'UPDATE `#__komento_comments` SET `lft` = `lft` + 2';
		$query .= ' WHERE `component` = ' . $this->db->Quote($component);
		$query .= ' AND `cid` = ' . $this->db->Quote($cid);
		$query .= ' AND `lft` > ' . $this->db->Quote($nodeValue);
		$this->db->setQuery($query);
		$this->db->query();
	}

	function updateCommentLftRgt(&$commentObj)
	{
		$commentsModel = Komento::getModel( 'comments' );

		$latestComment = $commentsModel->getLatestComment( $commentObj->component, $commentObj->cid, $commentObj->parent_id );
		if( $commentObj->parent_id != 0 )
		{
			$parentComment = Komento::getTable( 'comments' );
			$parentComment->load( $commentObj->parent_id );

			//adding new child comment
			$lft = $parentComment->lft + 1;
			$rgt = $parentComment->lft + 2;
			$nodeVal = $parentComment->lft;

			if( ! empty( $latestComment ) )
			{
				$lft = $latestComment->rgt + 1;
				$rgt = $latestComment->rgt + 2;
				$nodeVal = $latestComment->rgt;
			}

			$commentsModel->updateCommentSibling( $commentObj->component, $commentObj->cid, $nodeVal );

			$commentObj->lft = $lft;
			$commentObj->rgt = $rgt;
		}
		else
		{
			//adding new comment
			$lft = 1;
			$rgt = 2;

			if( ! empty( $latestComment ) )
			{
				$lft = $latestComment->rgt + 1;
				$rgt = $latestComment->rgt + 2;
				$nodeVal = $latestComment->rgt;

				$commentsModel->updateCommentSibling( $commentObj->component, $commentObj->cid, $nodeVal );
			}

			$commentObj->lft = $lft;
			$commentObj->rgt = $rgt;
		}
	}

	function getChilds($id)
	{
		$commentTable = Komento::getTable('comments');
		$commentTable->load($id);

		$component	= $commentTable->component;
		$cid		= $commentTable->cid;
		$lft		= $commentTable->lft;
		$rgt		= $commentTable->rgt;

		$query = 'SELECT ' . $this->db->namequote('id') . ' FROM ' . $this->db->namequote( '#__komento_comments' );
		$query .= ' WHERE ' . $this->db->namequote('component') . ' = ' . $this->db->quote($component);
		$query .= ' AND ' . $this->db->namequote('cid') . ' = ' . $this->db->quote($cid);
		$query .= ' AND ' . $this->db->namequote('lft') . ' BETWEEN ' . $this->db->quote($lft) . ' AND ' . $this->db->quote($rgt);
		$this->db->setQuery($query);

		return $this->db->loadResultArray();
	}

	function getParents($id)
	{
		$commentTable = Komento::getTable('comments');
		$commentTable->load($id);

		$component	= $commentTable->component;
		$cid		= $commentTable->cid;
		$lft		= $commentTable->lft;

		$query = 'SELECT ' . $this->db->namequote('id') . ' FROM ' . $this->db->namequote( '#__komento_comments' );
		$query .= ' WHERE ' . $this->db->namequote('component') . ' = ' . $this->db->quote($component);
		$query .= ' AND ' . $this->db->namequote('cid') . ' = ' . $this->db->quote($cid);
		$query .= ' AND ' . $this->db->quote($lft) . ' BETWEEN ' . $this->db->namequote('lft') . ' AND ' . $this->db->namequote('rgt');

		$this->db->setQuery($query);

		return $this->db->loadResultArray();
	}

	function getTotalChilds($id)
	{
		// CANNOT RELY ON JUST RGT-LFT
		$commentTable = Komento::getTable('comments');
		$commentTable->load($id);

		$component	= $commentTable->component;
		$cid		= $commentTable->cid;
		$lft		= $commentTable->lft;
		$rgt		= $commentTable->rgt;

		$query = 'SELECT COUNT(1) FROM ' . $this->db->namequote( '#__komento_comments' );
		$query .= ' WHERE ' . $this->db->namequote('component') . ' = ' . $this->db->quote($component);
		$query .= ' AND ' . $this->db->namequote('cid') . ' = ' . $this->db->quote($cid);
		$query .= ' AND ' . $this->db->namequote('lft') . ' BETWEEN ' . $this->db->quote($lft) . ' AND ' . $this->db->quote($rgt);
		$query .= ' AND ' . $this->db->namequote('lft') . ' != ' .$this->db->quote($lft);
		$this->db->setQuery($query);

		return $this->db->loadResult();
	}

	function moveChildsUp($id)
	{
		$commentTable = Komento::getTable('comments');
		$commentTable->load($id);

		$query = 'UPDATE ' . $this->db->namequote( '#__komento_comments' );
		$query .= ' SET ' . $this->db->namequote('parent_id') . ' = ' . $this->db->quote($commentTable->parent_id);
		$query .= ' WHERE ' . $this->db->namequote('parent_id') . ' = ' . $this->db->quote($commentTable->id);

		$this->db->setQuery($query);

		if( !$this->db->query() )
		{
			$this->setError($this->db->getErrorMsg());
			return false;
		}

		return true;
	}

	function isSticked($id)
	{
		$commentTable = Komento::getTable('comments');
		$commentTable->load($id);
		return $commentTable->sticked;
	}

	function getConversationBarAuthors($component, $cid)
	{
		$config = Komento::getConfig();

		$limit = ' LIMIT ' . $config->get( 'conversation_bar_max_authors', 10 );
		$order = ' ORDER BY ' . $this->db->namequote( 'created' ) . ' DESC';

		$main  = 'SELECT `name`, `created_by`, `created`, `email` FROM ' . $this->db->namequote( '#__komento_comments' );
		$main .= ' WHERE ' . $this->db->namequote( 'component' ) . ' = ' . $this->db->quote( $component );
		$main .= ' AND ' . $this->db->namequote( 'cid' ) . ' = ' . $this->db->quote( $cid );
		$main .= ' AND ' . $this->db->namequote( 'published' ) . ' = ' . $this->db->quote( '1' );

		$query  = $main . ' AND ' . $this->db->namequote( 'created_by' ) . ' <> ' . $this->db->quote( '0' ) . ' AND ' . $this->db->namequote( 'created_by' ) . ' <> ' . $this->db->quote( '' );
		$query .= ' GROUP BY ' . $this->db->namequote( 'created_by' ) . $order . $limit;

		if( $config->get( 'conversation_bar_include_guest' ) )
		{
			$temp  = $main . ' AND ' . $this->db->namequote( 'created_by' ) . ' = ' . $this->db->quote( '0' );
			$temp .= ' GROUP BY ' . $this->db->namequote( 'name' ) . $order . $limit;

			$query = '(' . $query . ') UNION (' . $temp . ')';
		}

		$query = 'SELECT `name`, `created_by`, `email` FROM (' . $query . ') AS x' . $order . $limit;
		$this->db->setQuery( $query );
		$result = $this->db->loadObjectList();

		$authors = new stdClass();
		$authors->guest = array();
		$authors->registered = array();

		foreach( $result as $item )
		{
			$type = $item->created_by == '0' ? 'guest' : 'registered';

			array_push( $authors->$type, $item );
		}

		return $authors;
	}

	function getPopularComments( $component = 'all', $cid = 'all', $options = array() )
	{
		// define default values
		$defaultOptions	= array(
			'start'			=> 0,
			'limit'			=> 10,
			'userid'		=> 'all',
			'sticked'		=> 'all',
			// 'search'	=> '', future todo
			'published'		=> 1,
			'minimumlikes'	=> 0,
			'random'		=> 0,
			'threaded'		=> 0
		);

		$querySelect = '';
		$queryWhere = array();
		$queryGroup = '';
		$queryOrder = '';
		$queryLimit = '';

		// take the input values and clear unexisting keys
		$options = Komento::mergeOptions( $defaultOptions, $options );

		$querySelect  = 'SELECT comments.*, COUNT(actions.comment_id) AS likes FROM ' . $this->db->nameQuote( '#__komento_comments' ) . ' AS comments';
		$querySelect .= ' LEFT JOIN ' . $this->db->nameQuote( '#__komento_actions' ) . ' AS actions ON comments.id = actions.comment_id';

		if( $component !== 'all' )
		{
			$queryWhere[] = 'comments.component = ' . $this->db->quote( $component );
		}

		if( $cid !== 'all' )
		{
			if( is_array( $cid ) )
			{
				$cid = implode( ',', $cid );
			}

			if( empty( $cid ) )
			{
				$queryWhere[] = 'comments.cid = 0';
			}
			else
			{
				$queryWhere[] = 'comments.cid IN (' . $cid . ')';
			}
		}

		if( $options['userid'] !== 'all' )
		{
			$queryWhere[] = 'comments.created_by = ' . $this->db->quote( $options['userid'] );
		}

		if( $options['published'] !== 'all' )
		{
			$queryWhere[] = 'comments.published = ' . $this->db->quote( $options['published'] );
		}

		if( $options['sticked'] !== 'all' )
		{
			$queryWhere[] = 'comments.sticked = ' . $this->db->quote( 1 );
		}

		$queryWhere[] = 'actions.type = ' . $this->db->quote( 'likes' );

		if( count($queryWhere) > 0 )
		{
			$queryWhere = ' WHERE ' . implode(' AND ', $queryWhere);
		}
		else
		{
			$queryWhere = '';
		}

		$queryGroup = ' GROUP BY actions.comment_id';

		if( $options['minimumlikes'] > 0 )
		{
			$queryGroup .= ' HAVING likes >= ' . $options['minimumlikes'];
		}

		$queryOrder = ' ORDER BY likes DESC, created DESC';
		$queryLimit = ' LIMIT ' . $options['start'] . ',' . $options['limit'];

		$query = $querySelect . $queryWhere . $queryGroup . $queryOrder . $queryLimit;

		$this->db->setQuery( $query );

		$result = $this->db->loadObjectList();

		// build random
		if( $options['random'] )
		{
			$result	= $this->buildRandom( $result, $options );
		}

		$comments = array();

		foreach( $result as $row )
		{
			if( !$options['threaded'] )
			{
				$row->depth = 0;
			}

			$table = Komento::getTable( 'comments' );
			$table->bind( $row );

			$comments[] = $table;
		}

		return $comments;
	}

	function getTotalPopularComments( $component = 'all', $cid = 'all', $options = array() )
	{
		// define default values
		$defaultOptions	= array(
			'start'			=> 0,
			'limit'			=> 10,
			'userid'		=> 'all',
			'sticked'		=> 'all',
			// 'search'		=> '', future todo
			'published'		=> 1,
			'minimumlikes'	=> 0,
			'random'		=> 0
		);

		$querySelect = '';
		$queryWhere = array();
		$queryGroup = '';
		$queryOrder = '';
		$queryLimit = '';

		// take the input values and clear unexisting keys
		$options = Komento::mergeOptions( $defaultOptions, $options );

		$querySelect  = 'SELECT comments.*, COUNT(actions.comment_id) AS likes FROM ' . $this->db->nameQuote( '#__komento_comments' ) . ' AS comments';
		$querySelect .= ' LEFT JOIN ' . $this->db->nameQuote( '#__komento_actions' ) . ' AS actions ON comments.id = actions.comment_id';

		if( $component !== 'all' )
		{
			$queryWhere[] = 'comments.component = ' . $this->db->quote( $component );
		}

		if( $cid !== 'all' )
		{
			if( is_array( $cid ) )
			{
				$cid = implode( ',', $cid );
			}

			if( empty( $cid ) )
			{
				$queryWhere[] = 'comments.cid = 0';
			}
			else
			{
				$queryWhere[] = 'comments.cid IN (' . $cid . ')';
			}
		}

		if( $options['userid'] !== 'all' )
		{
			$queryWhere[] = 'comments.created_by = ' . $this->db->quote( $options['userid'] );
		}

		if( $options['published'] !== 'all' )
		{
			$queryWhere[] = 'comments.published = ' . $this->db->quote( $options['published'] );
		}

		if( $options['sticked'] !== 'all' )
		{
			$queryWhere[] = 'comments.sticked = ' . $this->db->quote( 1 );
		}

		$queryWhere[] = 'actions.type = ' . $this->db->quote( 'likes' );

		if( count($queryWhere) > 0 )
		{
			$queryWhere = ' WHERE ' . implode(' AND ', $queryWhere);
		}
		else
		{
			$queryWhere = '';
		}

		$queryGroup = ' GROUP BY actions.comment_id';

		if( $options['minimumlikes'] > 0 )
		{
			$queryGroup .= ' HAVING likes >= ' . $options['minimumlikes'];
		}

		$query = 'SELECT COUNT(1) FROM (' . $querySelect . $queryWhere . $queryGroup . ') AS x';
		$this->db->setQuery( $query );

		return $this->db->loadResult();
	}

	public function deleteArticleComments( $component, $cid )
	{
		$query  = 'DELETE FROM ' . $this->db->nameQuote( '#__komento_comments' );
		$query .= ' WHERE ' . $this->db->nameQuote( 'component' ) . ' = ' . $this->db->quote( $component);
		$query .= ' AND ' . $this->db->nameQuote( 'cid' ) . ' = ' . $this->db->quote($cid);

		$this->db->setQuery( $query );
		return $this->db->query();
	}

	public function getChildCount( $ids, $nested = false )
	{
		$idsString = '';

		if( is_string( $ids ) || is_integer( $ids ) )
		{
			$idsString = (string) $ids;

			$ids = explode( ',', $idsString );
		}
		else
		{
			if( is_array( $ids ) )
			{
				$idsString = implode( ',', $ids );
			}
		}

		$query = 'SELECT ' . $this->db->nameQuote( 'parent_id' ) . ' AS id, COUNT(' . $this->db->nameQuote( 'parent_id') . ') AS child FROM ' . $this->db->nameQuote( '#__komento_comments' );
		$query .= ' WHERE ' . $this->db->nameQuote( 'parent_id' ) . ' IN(' . $idsString . ')';
		$query .= ' GROUP BY ' . $this->db->nameQuote( 'parent_id' );

		$this->db->setQuery( $query );

		$result = $this->db->loadObjectList();

		$childsCount = array();

		foreach( $ids as $id )
		{
			$match = false;

			foreach( $result as $row )
			{
				if( $row->id == $id )
				{
					$childsCount[$id] = $row->child;
					$match = true;
					break;
				}
			}

			if( !$match )
			{
				$childCount[$id] = 0;
			}
		}

		return $childsCount;
	}

	public function getUserTopCommentCount()
	{
		static $userTopCommentCount = null;

		if( is_null( $userTopCommentCount ) )
		{
			$sql = Komento::getSql();

			$sql->select( '#__komento_comments' )
				->column( 'created_by', 'total', 'count' )
				->where( 'created_by', '0', '<>' )
				->where( 'published', '1' )
				->group( 'created_by' )
				->order( 'total', 'desc' )
				->limit( 1 );

			$userTopCommentCount = $sql->loadResult();
		}

		return $userTopCommentCount;
	}

	public function getUsers( $options = array() )
	{
		$sql = Komento::getSql();

		$sql->select( '#__komento_comments' )
			->column( 'created_by', 'user', 'distinct' );

		if( !empty( $options['noguest'] ) )
		{
			$sql->where('created_by', 0, '>' );
		}

		if( !empty( $options['component'] ) )
		{
			$sql->where( 'component', $options['component'] );
		}

		if( !empty( $options['cid'] ) )
		{
			$sql->where( 'cid', $options['cid'] );
		}

		if( isset( $options['state'] ) )
		{
			$sql->where( 'published', $options['state'] );
		}

		$result = $sql->loadColumn();

		return $result;
	}
}
