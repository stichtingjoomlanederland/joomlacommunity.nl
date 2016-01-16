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

class EasyDiscussModelCustomFields extends EasyDiscussAdminModel
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

		$filter_order		= $mainframe->getUserStateFromRequest( 'com_easydiscuss.customs.filter_order', 		'filter_order', 	'a.ordering', 'cmd' );
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
	public function publish( &$customs = array(), $publish = 1 )
	{
		if( count( $customs ) > 0 )
		{
			$db		= DiscussHelper::getDBO();

			$ids	= implode( ',' , $customs );

			$query	= 'UPDATE ' . $db->nameQuote( '#__discuss_customfields' ) . ' '
					. 'SET ' . $db->nameQuote( 'published' ) . '=' . $db->Quote( $publish ) . ' '
					. 'WHERE ' . $db->nameQuote( 'id' ) . ' IN (' . $ids . ')';
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

	public function getAllCustomFieldsACL()
	{
		$db = DiscussHelper::getDBO();
		$query = 'SELECT * FROM ' . $db->nameQuote( '#__discuss_customfields_acl' )
				. ' WHERE ' . $db->nameQuote( 'acl_published' ) . '=' . $db->Quote( '1' );

		$db->setQuery( $query );
		$results = $db->loadObjectList();

		if( !$results )
		{
			return false;
		}

		return $results;
	}

	public function saveCustomFieldRule( $fieldId = null, $form = null )
	{


		if( $fieldId == null || $form == null )
		{
			return false;
		}

		$db = DiscussHelper::getDBO();
		$types = array( 'user', 'group' );
		$acls = $this->getAllCustomFieldsACL();

		$query = 'DELETE FROM ' . $db->nameQuote( '#__discuss_customfields_rule' )
				. ' WHERE ' . $db->nameQuote( 'field_id' ) . '=' . $db->quote( $fieldId );
		$db->setQuery( $query );
		$db->Query();

		// If nobody assign any permission in the permission tab, everybody can use.
		$allowAll = true;

		foreach( $types as $type )
		{
			foreach( $acls as $acl )
			{
				// Check is it set and count does it have items inside the array
				if( isset( $form[ 'acl_'.$type.'_'.$acl->action ] ) && count( $form[ 'acl_'.$type.'_'.$acl->action ] ) > 0 )
				{
					// EG: $form[ 'acl_user_view' ]
					foreach( $form[ 'acl_'.$type.'_'.$acl->action ] as $myContentId )
					{
						$table = DiscussHelper::getTable( 'CustomFieldsRule' );
						$table->field_id		= $fieldId;
						$table->acl_id			= $acl->id;
						$table->content_id		= $myContentId;
						$table->content_type	= $type;
						$table->status			= '1';

						$table->store();

						// If there is at least one permission is set, do not allow eveybody to use.
						$allowAll = false;
					}
				}
			}
		}

		if( $allowAll )
		{
			foreach( $acls as $acl )
			{
				// Allow all group to use
				$joomlaGroups	= DiscussHelper::getJoomlaUserGroups();
				foreach( $joomlaGroups as $joomlaGroup )
				{
					$table = DiscussHelper::getTable( 'CustomFieldsRule' );
					$table->field_id		= $fieldId;
					$table->acl_id			= $acl->id;
					$table->content_id		= $joomlaGroup->id;
					$table->content_type	= 'group';
					$table->status			= '1';

					$table->store();
				}
			}
		}

		return true;
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
