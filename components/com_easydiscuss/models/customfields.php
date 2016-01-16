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

class EasyDiscussModelCustomFields extends EasyDiscussModel
{
	/**
	 * Category total
	 *
	 * @var integer
	 */
	protected $_total = null;

	/**
	 * Pagination object
	 *
	 * @var object
	 */
	protected $_pagination = null;

	/**
	 * Category data array
	 *
	 * @var array
	 */
	protected $_data = null;


	public function __construct()
	{
		parent::__construct();

		$mainframe	= JFactory::getApplication();

		$limit		= $mainframe->getUserStateFromRequest( 'com_easydiscuss.customs.limit', 'limit', $mainframe->getCfg('list_limit'), 'int');
		$limitstart	= JRequest::getVar('limitstart', 0, '', 'int');

		$this->setState('limit', $limit);
		$this->setState('limitstart', $limitstart);
	}

	/**
	 * Method to get the total nr of the categories
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
	 * Method to get a pagination object for the categories
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
	 * Method to build the query for the customs
	 *
	 * @access private
	 * @return string
	 */
	protected function _buildQuery()
	{
		// Get the WHERE and ORDER BY clauses for the query
		$where		= $this->_buildQueryWhere();
		$orderby	= $this->_buildQueryOrderBy();
		$db			= DiscussHelper::getDBO();

		$query	= 'SELECT a.* FROM `#__discuss_customfields` AS a '
				. $where . ' '
				. $orderby;

		return $query;
	}

	protected function _buildQueryWhere()
	{
		$mainframe		= JFactory::getApplication();
		$db				= DiscussHelper::getDBO();

		$filter_state	= $mainframe->getUserStateFromRequest( 'com_easydiscuss.customs.filter_state', 'filter_state', '', 'word' );

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

		$where 		= ( count( $where ) ? ' WHERE ' . implode( ' AND ', $where ) : '' );

		return $where;
	}

	protected function _buildQueryOrderBy()
	{
		$mainframe			= JFactory::getApplication();

		$filter_order		= $mainframe->getUserStateFromRequest( 'com_easydiscuss.customs.filter_order', 		'filter_order', 	'a.id', 'cmd' );
		$filter_order_Dir	= $mainframe->getUserStateFromRequest( 'com_easydiscuss.customs.filter_order_Dir',	'filter_order_Dir',		'', 'word' );

		$orderby 			= ' ORDER BY '.$filter_order.' '.$filter_order_Dir;

		return $orderby;
	}

	/**
	 * Method to get categories item data
	 *
	 * @access public
	 * @return array
	 */
	public function getData( $usePagination = true)
	{
		// Lets load the content if it doesn't already exist
		if (empty($this->_data))
		{
			$query = $this->_buildQuery();
			if($usePagination)
				$this->_data = $this->_getList($query, $this->getState('limitstart'), $this->getState('limit'));
			else
				$this->_data = $this->_getList($query);
		}

		return $this->_data;
	}

	/**
	 * Method to publish or unpublish customs
	 *
	 * @access public
	 * @return array
	 */
	public function publish( $customs = array(), $publish = 1 )
	{
		if( count( $customs ) > 0 )
		{
			$db		= DiscussHelper::getDBO();

			$customs	= implode( ',' , $customs );

			$query	= 'UPDATE ' . $db->nameQuote( '#__discuss_customfields' ) . ' '
					. 'SET ' . $db->nameQuote( 'published' ) . '=' . $db->Quote( $publish ) . ' '
					. 'WHERE ' . $db->nameQuote( 'id' ) . ' IN (' . $customs . ')';
			$db->setQuery( $query );

			if( !$db->query() )
			{
				$this->setError($this->_db->getErrorMsg());
				return false;
			}
			return true;
		}
		return false;
	}

	public function sortDescending($a, $b)
	{
		// Descending sort based on the object property "ordering"
		return ($a->ordering < $b->ordering) ? 1 : -1;
	}

	public function getMyFields( $postId = null, $aclId = null )
	{

		$db = DiscussHelper::getDBO();
		$my = JFactory::getUser();

		if( $aclId == null )
		{
			return false;
		}

		// NEW POST
		if( $postId == null )
		{
			$newFields = $this->setNewFields($aclId);

			// sort the custom fields
			if (!empty($newFields)) {
				usort($newFields, array('EasyDiscussModelCustomFields', 'sortDescending'));
			}

			return $newFields;
		}

		$myResults = $this->checkMyFields( $postId, $aclId );

		if( !empty($myResults) )
		{
			usort($myResults, array('EasyDiscussModelCustomFields', 'sortDescending'));
		}

		return $myResults;
	}

	public function setNewFields( $aclId )
	{
		$results = $this->getNewFields( $aclId );
		return $results;
	}

	public function getNewFields( $aclId = null )
	{
		static $loaded = array();

		$sig    = (int) $aclId;

		if( ! isset( $loaded[ $sig ] ) )
		{
			$db = DiscussHelper::getDBO();
			$my = JFactory::getUser();

			$myUserGroups = (array) DiscussHelper::getUserGroupId($my);

			if( empty($myUserGroups) )
			{
				$loaded[ $sig ] = array();
			}
			else
			{
				$query = 'SELECT a.*, b.`acl_id`'
						. ' FROM ' . $db->nameQuote( '#__discuss_customfields' ) . ' AS a'
						. ' LEFT JOIN ' . $db->nameQuote( '#__discuss_customfields_rule' ) . ' AS b'
						. ' ON a.' . $db->nameQuote( 'id' ) . ' = ' . 'b.'  . $db->nameQuote( 'field_id' )
						. ' WHERE a.' . $db->nameQuote( 'published' ) . ' = ' . $db->Quote( '1' )
						. ' AND b.' . $db->nameQuote( 'acl_id' ) . ' = ' . $db->Quote( $aclId );

				$userQuery = $query;
				$userQuery .= ' AND b.' . $db->nameQuote( 'content_type' ) . ' = ' . $db->Quote( 'user' );
				$userQuery .= ' AND b.' . $db->nameQuote( 'content_id' ) . ' = ' . $db->Quote( $my->id );

				$groupQuery = $query;
				$groupQuery .= ' AND b.' . $db->nameQuote( 'content_type' ) . ' = ' . $db->Quote( 'group' );

				if( count($myUserGroups) == 1 )
				{
					$gid    = array_pop($myUserGroups);
					$groupQuery .= ' AND b.' . $db->nameQuote( 'content_id' ) . ' = ' . $db->Quote( $gid );
				}
				else
				{
					$groupQuery .= ' AND b.' . $db->nameQuote( 'content_id' ) . ' IN(' . implode( ', ', $myUserGroups ) . ')';
				}

				$masterQuery    = $userQuery;
				$masterQuery    .= ' UNION ';
				$masterQuery    .= $groupQuery;

				$db->setQuery( $masterQuery );
				$result = $db->loadObjectList();

				$loaded[ $sig ] = $result;
			}
		}

		return $loaded[ $sig ];

	}

	public function checkMyFields( $postId, $aclId )
	{
		$db = DiscussHelper::getDBO();
		$my = JFactory::getUser();

		// GET MY VALUE
		$myResults = $this->getAllFields( $postId, $aclId );
		return $myResults;
	}

	public function getAllFields( $postId = null, $aclId = null )
	{
		if( $aclId == null || $postId == null )
		{
			return false;
		}

		static $loaded = array();

		$sig    = (int) $postId . '-' . (int) $aclId ;

		if( ! isset( $loaded[$sig] ) )
		{
			$my = JFactory::getUser();
			$db = DiscussHelper::getDBO();
			$myUserGroups = (array) DiscussHelper::getUserGroupId($my);

			if( empty($myUserGroups) )
			{
				$loaded[$sig]   = array();
			}
			else
			{
				$query = 'SELECT a.*,'
						. ' b.' . $db->nameQuote( 'field_id' ) . ', b.' . $db->nameQuote( 'acl_id' ) . ', b.' . $db->nameQuote( 'content_id' ) . ','
						. ' b.' . $db->nameQuote( 'content_type' ) . ', b.' . $db->nameQuote( 'status' ) . ','
						. ' c.' . $db->nameQuote( 'field_id' ) . ', c.' . $db->nameQuote( 'value' ) . ', c.' . $db->nameQuote( 'post_id' )
						. ' FROM ' . $db->nameQuote( '#__discuss_customfields' ) . ' a'
						. ' LEFT JOIN ' . $db->nameQuote( '#__discuss_customfields_rule' ) . ' b'
						. ' ON a.' . $db->nameQuote( 'id' ) . '=' . 'b.' . $db->nameQuote( 'field_id' )
						. ' LEFT JOIN ' . $db->nameQuote( '#__discuss_customfields_value' ) . ' c'
						. ' ON a.' . $db->nameQuote( 'id' ) . '=' . 'c.' . $db->nameQuote( 'field_id' )
						. ' AND c.' . $db->nameQuote( 'post_id' ) . '=' . $db->Quote( $postId );


				$userQuery  = $query;
				$userQuery .= ' WHERE a.' . $db->nameQuote( 'published' ) . '=' . $db->Quote( '1' );
				$userQuery .= ' AND b.' . $db->nameQuote( 'content_type' ) . '=' . $db->Quote( 'user' );
				$userQuery .= ' AND b.' . $db->nameQuote( 'acl_id' ) . '=' . $db->Quote( $aclId );
				$userQuery .= ' AND b.' . $db->nameQuote( 'content_id' ) . '=' . $db->Quote( $my->id );

				$groupQuery  = $query;
				$groupQuery .= ' WHERE a.' . $db->nameQuote( 'published' ) . '=' . $db->Quote( '1' );
				$groupQuery .= ' AND b.' . $db->nameQuote( 'content_type' ) . '=' . $db->Quote( 'group' );
				$groupQuery .= ' AND b.' . $db->nameQuote( 'acl_id' ) . '=' . $db->Quote( $aclId );
				if( count( $myUserGroups ) == 1 )
				{
					$gid    = array_pop( $myUserGroups );
					$groupQuery .= ' AND b.' . $db->nameQuote( 'content_id' ) . ' = ' . $db->Quote( $gid );
				}
				else
				{
					$groupQuery .= ' AND b.' . $db->nameQuote( 'content_id' ) . ' IN(' . implode( ', ', $myUserGroups ) . ')';
				}


				$masterQuery    = $userQuery;
				$masterQuery    .= ' UNION ';
				$masterQuery    .= $groupQuery;

				$db->setQuery( $masterQuery );
				$result = $db->loadObjectList();

				// @user with multiple group will generate duplicate result, hence we remove it
				if( !empty($result) )
				{
					$myFinalResults = array();

					// Remove dupes records which have no values
					foreach ($result as $item)
					{
						if ( !array_key_exists($item->id, $myFinalResults) )
						{
							$myFinalResults[$item->id] = $item;
						}
						else
						{
							if( !empty($item->id) )
							{
								// If the pending item have value, replace the existing record
								$myFinalResults[$item->id] = $item;
							}
						}
					}
					$result = $myFinalResults;
				}



				$loaded[$sig]   = $result;
			}
		}

		return $loaded[$sig];
	}

	public function deleteCustomFieldsValue( $id, $type = null )
	{
		if( !$id )
		{
			return false;
		}

		$db = DiscussHelper::getDBO();

		$query = 'DELETE';

		if( $type == 'post' )
		{
			// Delete the particular post's custom field's value when the associate post is deleted.
			$query .= ' FROM ' . $db->nameQuote( '#__discuss_customfields_value' )
					. ' WHERE ' . $db->nameQuote( 'post_id' ) . '=' . $db->Quote( $id );
		}
		if( $type == 'field' )
		{
			// Delete all custom field's value of that particular field.
			$query .= ' FROM ' . $db->nameQuote( '#__discuss_customfields_value' )
					. ' WHERE ' . $db->nameQuote( 'field_id' ) . '=' . $db->Quote( $id );
		}
		if( $type == 'update' )
		{
			// If edit post, when certain custom fields is unpublish, we don't want to delete the unpublish because what if the user publish it back? unless he want to delete post
			// Delete published only
			$query .= ' a.*'
					. ' FROM ' . $db->nameQuote( '#__discuss_customfields_value' ) . ' a'
					. ' LEFT JOIN ' . $db->nameQuote( '#__discuss_customfields' ) . ' b'
					. ' ON a.' . $db->nameQuote( 'field_id' ) . '=' . 'b.' . $db->nameQuote( 'id' )
					. ' WHERE a.' . $db->nameQuote( 'post_id' ) . '=' . $db->Quote( $id )
					. ' AND b.' . $db->nameQuote( 'published' ) . '=' . $db->Quote( '1' );
		}

		$state = $db->setQuery( $query );

		if( !$db->query() )
		{
			return false;
		}

		return true;
	}

	public function deleteCustomFieldsRule( $id )
	{
		if( !$id )
		{
			return false;
		}

		$db = DiscussHelper::getDBO();

		$query = 'DELETE FROM ' . $db->nameQuote( '#__discuss_customfields_rule' )
				. ' WHERE ' . $db->nameQuote( 'field_id' ) . '=' . $db->quote( $id );

		$db->setQuery( $query );

		if( !$db->query() )
		{
			return false;
		}

		return true;
	}
}
