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

class EasyDiscussModelLabels extends EasyDiscussModel
{
	protected $_total		= null;
	protected $_pagination	= null;
	protected $_data		= null;

	public function __construct()
	{
		parent::__construct( array() );

		$app		= JFactory::getApplication();
		$limit		= $app->getUserStateFromRequest( 'com_easydiscuss.labels.limit', 'limit', DiscussHelper::getListLimit(), 'int');
		$limitstart	= JRequest::getVar('limitstart', 0, '', 'int');

		$this->setState('limit'		, $limit);
		$this->setState('limitstart', $limitstart);
	}

	/**
	 * Method to get the total number of the labels
	 *
	 * @access public
	 * @return integer
	 */
	public function getTotal()
	{
		// Lets load the content if it doesn't already exist
		if (empty($this->_total))
		{
			$query = $this->_buildQuery();
			$this->_total = $this->_getListCount($query);
		}

		return $this->_total;
	}

	/**
	 * Method to get a pagination object for the labels
	 *
	 * @access public
	 * @return integer
	 */
	public function getPagination()
	{
		// Lets load the content if it doesn't already exist
		if (empty($this->_pagination))
		{
			jimport('joomla.html.pagination');
			$this->_pagination = new JPagination( $this->getTotal(), $this->getState('limitstart'), $this->getState('limit') );
		}

		return $this->_pagination;
	}

	/**
	 * Method to build the query for the labels
	 *
	 * @access private
	 * @return string
	 */
	private function _buildQuery()
	{
		// Get the WHERE and ORDER BY clauses for the query
		$where		= $this->_buildQueryWhere();
		$orderby	= $this->_buildQueryOrderBy();
		$db			= DiscussHelper::getDBO();

		$query	= 'SELECT * FROM ' . $db->nameQuote( '#__discuss_posts_labels' )
				. $where . ' '
				. $orderby;

		return $query;
	}

	private function _buildQueryWhere()
	{
		$app			= JFactory::getApplication();
		$db				= DiscussHelper::getDBO();

		$filter_state	= $app->getUserStateFromRequest( 'com_easydiscuss.labels.filter_state', 'filter_state', '', 'word' );
		$search			= $app->getUserStateFromRequest( 'com_easydiscuss.labels.search', 'search', '', 'string' );
		$search			= $db->getEscaped( trim(JString::strtolower( $search ) ) );

		$where = array();

		if ( $filter_state )
		{
			if ( $filter_state == 'P' )
			{
				$where[] = $db->nameQuote( 'published' ) . '=' . $db->Quote( '1' );
			}
			else if ($filter_state == 'U' )
			{
				$where[] = $db->nameQuote( 'published' ) . '=' . $db->Quote( '0' );
			}
		}

		if ($search)
		{
			$where[] = ' LOWER( title ) LIKE \'%' . $search . '%\' ';
		}

		$where 		= ( count( $where ) ? ' WHERE ' . implode( ' AND ', $where ) : '' );

		return $where;
	}

	private function _buildQueryOrderBy()
	{
		$app				= JFactory::getApplication();
		$filter_order		= $app->getUserStateFromRequest( 'com_easydiscuss.labels.filter_order',		'filter_order',		'ordering ASC'	, 'int' );
		$filter_order_Dir	= $app->getUserStateFromRequest( 'com_easydiscuss.labels.filter_order_Dir',	'filter_order_Dir',	''				, 'word' );
		$orderby			= ' ORDER BY '.$filter_order.' '.$filter_order_Dir;

		return $orderby;
	}

	/**
	 * Method to get labels item data
	 *
	 * @access public
	 * @return array
	 */
	public function getData()
	{
		// Lets load the content if it doesn't already exist
		if (empty($this->_data))
		{
			$query = $this->_buildQuery();
			$this->_data = $this->_getList($query, $this->getState('limitstart'), $this->getState('limit'));
		}

		return $this->_data;
	}

	/**
	 * Method to publish or unpublish labels
	 *
	 * @access public
	 * @return boolean
	 */
	public function publish( $labels = array(), $publish = true )
	{
		if( is_integer($labels) ) {
			$labels = array($labels);
		} elseif ( !is_array($labels) || count($labels) < 1 ) {
			return false;
		}

		$labels		= implode( ',' , $labels );
		$publish	= $publish ? 1 : 0;

		$db		= DiscussHelper::getDBO();
		$query	= 'UPDATE ' . $db->nameQuote( '#__discuss_posts_labels' )
				. ' SET ' . $db->nameQuote( 'published' ) . '=' . $db->Quote( $publish )
				. ' WHERE ' . $db->nameQuote( 'id' ) . ' IN (' . $labels . ')';
		$db->setQuery( $query );

		if( !$db->query() )
		{
			$this->setError($this->_db->getErrorMsg());
			return false;
		}
		return true;
	}

	public function searchLabel($title)
	{
		$db		= DiscussHelper::getDBO();
		$query	= 'SELECT ' . $db->nameQuote('id') . ' '
				. 'FROM ' 	. $db->nameQuote('#__discuss_posts_labels') . ' '
				. 'WHERE ' 	. $db->nameQuote('title') . ' = ' . $db->quote($title) . ' '
				. 'LIMIT 1';
		$db->setQuery($query);

		$result	= $db->loadObject();

		return $result;
	}

	public function getLabelTitle($id)
	{
		$db		= DiscussHelper::getDBO();
		$query	= 'SELECT ' . $db->nameQuote('title') . ' '
				. 'FROM ' 	. $db->nameQuote('#__discuss_posts_labels') . ' '
				. 'WHERE ' 	. $db->nameQuote('id') . ' = ' . $db->quote($id) . ' '
				. 'LIMIT 1';
		$db->setQuery($query);

		$result	= $db->loadResult();

		return $result;
	}

	/**
	 * Method to get total labels
	 *
	 * @access public
	 * @return integer
	 */
	public function getTotalLabels( $ignoreUnpublish = false )
	{
		$db		= DiscussHelper::getDBO();
		$query	= 'SELECT COUNT(1) FROM ' . $db->nameQuote( '#__discuss_posts_labels' );

		$query .= $ignoreUnpublish ? '' : ' WHERE `published` = 1';

		$db->setQuery( $query );
		$result	= $db->loadResult();

		return (empty($result)) ? 0 : $result;
	}

	public function getLabels()
	{
		$db		= DiscussHelper::getDBO();
		$query	= ' SELECT `id`, `title` '
				. ' FROM #__discuss_posts_labels '
				. ' WHERE `published` = 1 '
				. ' ORDER BY `ordering`';

		$db->setQuery($query);
		$result = $db->loadObjectList();

		return $result;
	}
}
