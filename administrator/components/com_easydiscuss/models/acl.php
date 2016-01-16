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

class EasyDiscussModelAcl extends EasyDiscussAdminModel
{
	/**
	 * Category total
	 *
	 * @var integer
	 */
	var $_total = null;

	/**
	 * Pagination object
	 *
	 * @var object
	 */
	var $_pagination = null;

	/**
	 * Category data array
	 *
	 * @var array
	 */
	var $_data = null;

	function __construct()
	{
		parent::__construct();

		$mainframe	= JFactory::getApplication();

		$limit		= $mainframe->getUserStateFromRequest( 'com_easydiscuss.acls.limit', 'limit', $mainframe->getCfg('list_limit'), 'int');
		$limitstart		= JRequest::getVar('limitstart', 0, '', 'int');

		$this->setState('limit', $limit);
		$this->setState('limitstart', $limitstart);
	}

	function getTotal($type)
	{
		// Lets load the content if it doesn't already exist
		if (empty($this->_total))
		{
			$query = $this->_buildQuery($type);
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
	function getPagination($type)
	{
		// Lets load the content if it doesn't already exist
		if (empty($this->_pagination))
		{
			jimport('joomla.html.pagination');
			$this->_pagination = new JPagination( $this->getTotal($type), $this->getState('limitstart'), $this->getState('limit') );
		}

		return $this->_pagination;
	}

	/**
	 * Method to get categories item data
	 *
	 * @access public
	 * @return array
	 */
	function getJoomlaGroupRulesets()
	{
		$sql = 'SELECT * FROM ';

		return $this->_data;
	}

	function getRules($key='', $type='', $cid='')
	{
		$db = DiscussHelper::getDBO();

		if( $type == 'group' && ( $cid == '0' || $cid == '1' ) )
		{
			$sql	= 'SELECT * FROM '.$db->nameQuote('#__discuss_acl');
			$sql	.= ' WHERE `published` = 1';
			$sql	.= ' and `public` = 1';
			$sql	.= ' ORDER BY `id` ASC';
		}
		else
		{
			$sql = 'SELECT * FROM '.$db->nameQuote('#__discuss_acl').' WHERE `published`=1 ORDER BY `id` ASC';
		}

		$db->setQuery($sql);

		return $db->loadObjectList($key);
	}

	function deleteRuleset($cid, $type)
	{
		if( is_null($cid) || empty($type))
		{
			return false;
		}

		$db = DiscussHelper::getDBO();

		$sql = 'DELETE FROM ' . $db->nameQuote('#__discuss_acl_group') . ' WHERE '. $db->nameQuote('content_id') . ' = ' . $db->quote($cid) . ' AND `type` = ' . $db->quote($type);

		$db->setQuery($sql);
		$result = $db->query();

		return $result;
	}

	function insertRuleset($cid, $type, $saveData)
	{
		$db = DiscussHelper::getDBO();

		$rules = $this->getRules('action', $type, $cid);

		$newruleset = array();

		foreach($rules as $rule)
		{
			$action = $rule->action;
			$str = "(".$db->quote($cid).", ".$db->quote($rule->id).", ".$db->quote($saveData[$action]).", ".$db->quote($type).")";
			array_push($newruleset, $str);
		}

		if(!empty($newruleset))
		{
			$sql = 'INSERT INTO ' . $db->nameQuote('#__discuss_acl_group') . ' (`content_id`, `acl_id`, `status`, `type`) VALUES ';
			$sql .= implode(',', $newruleset);
			$db->setQuery($sql);

			return $result = $db->query();
		}

		return true;
	}

	function getRuleSet($type, $cid, $add=false)
	{
		$db = DiscussHelper::getDBO();

		$rulesets = new stdClass();
		$rulesets->rules = new stdClass();

		//get rules
		$rules = $this->getRules('id', $type, $cid);

		foreach($rules as $rule)
		{
			$rulesets->rules->{$rule->action} = (INT)$rule->default;
		}

		if(!$add)
		{
			//get user
			$query = $this->_buildQuery($type, $cid);

			$db->setQuery($query);
			$row				= $db->loadObject();
			$rulesets->id		= $row->id;
			$rulesets->name		= $row->name;
			$rulesets->level	= '0';

			//get acl group ruleset
			$sql = 'SELECT * FROM ' . $db->nameQuote('#__discuss_acl_group') . ' WHERE '. $db->nameQuote('content_id') . ' = ' . $db->quote($cid) .' AND '. $db->nameQuote('type') . ' = ' . $db->quote($type);
			$db->setQuery($sql);
			$row = $db->loadAssocList();

			if(count($row) > 0)
			{
				foreach($row as $data)
				{
					if(isset($rules[$data['acl_id']]))
					{
						$action = $rules[$data['acl_id']]->action;
						$rulesets->rules->{$action} = $data['status'];
					}
				}
			}
		}

		return $rulesets;
	}

	function getRuleSets($type='group', $cid='')
	{
		$db 		= DiscussHelper::getDBO();

		$rulesets	= new stdClass();
		$ids		= array();

		$rules = $this->getRules('id');

		//get user
		$query = $this->_buildQuery($type, $cid);

		$pagination = $this->getPagination( $type );
		$rows = $this->_getList($query, $pagination->limitstart, $pagination->limit );

		if(!empty($rows))
		{
			foreach($rows as $row)
			{
				$rulesets->{$row->id}			= new stdClass();
				$rulesets->{$row->id}->id		= $row->id;
				$rulesets->{$row->id}->name		= $row->name;
				$rulesets->{$row->id}->level	= $row->level;

				foreach($rules as $rule)
				{
					$rulesets->{$row->id}->{$rule->action} = (INT)$rule->default;
				}

				array_push($ids, $row->id);
			}

			//get acl group ruleset
			$sql = 'SELECT * FROM ' . $db->nameQuote('#__discuss_acl_group') . ' WHERE '. $db->nameQuote('type') . ' = ' . $db->quote($type) . ' AND `content_id` IN (' . implode( ' , ', $ids ) . ')';
			$db->setQuery($sql);
			$acl = $db->loadAssocList();

			if( count( $acl ) > 0)
			{
				foreach($acl as $data)
				{
					if(isset($rules[$data['acl_id']]))
					{
						$action = $rules[$data['acl_id']]->action;
						$rulesets->{$data['content_id']}->{$action} = $data['status'];
					}
				}
			}
		}

		return $rulesets;
	}

	function _buildQuery($type='group', $cid='')
	{
		$db			= DiscussHelper::getDBO();

		switch($type)
		{
			case 'group':
				if(DiscussHelper::getJoomlaVersion() >= '1.6')
				{
					//$query	= 'SELECT `id`, `name` FROM ' . $db->nameQuote('#__core_acl_aro_groups') . ' a ';
					$query = 'SELECT a.id, a.title AS `name`, COUNT(DISTINCT b.id) AS level';
					$query .= ' , GROUP_CONCAT(b.id SEPARATOR \',\') AS parents';
					$query .= ' FROM #__usergroups AS a';
					$query .= ' LEFT JOIN `#__usergroups` AS b ON a.lft > b.lft AND a.rgt < b.rgt';
				}
				else
				{
					$query	= 'SELECT `id`, `name`, 0 as `level` FROM ' . $db->nameQuote('#__core_acl_aro_groups') . ' AS a';
				}
				break;
			case 'assigned':
			default:
				$query	= 'SELECT DISTINCT(a.`id`), a.`name`, 0 as `level` FROM ' . $db->nameQuote('#__users') . ' a LEFT JOIN ' . $db->nameQuote('#__discuss_acl_group') . ' b ON a.`id` = b.`content_id` ';
		}

		$where		= $this->_buildQueryWhere($type, $cid);
		$orderby	= $this->_buildQueryOrderBy($type);

		if( $cid == '' )
			$query .= $where;

		if( $type == 'group' )
		{
			if(DiscussHelper::getJoomlaVersion() == '1.5')
			{
				$xQuery = $query;

				$query	= 'select * from (';
				$query	.= 'SELECT 0 as `id`, ' . $db->Quote('Public') . ' as `name`, 0 as `level`';
				$query	.= ' UNION ';
				$query	.= $xQuery;
				$query	.= ') as a';
			}
		}

		if( $cid != '' )
			$query .= $where;

		$query .= ' ' . $orderby;

		return $query;
	}

	function _buildQueryWhere($type='group', $cid='')
	{
		$mainframe	= JFactory::getApplication();
		$db			= DiscussHelper::getDBO();

		$search		= $mainframe->getUserStateFromRequest( 'com_easydiscuss.acls.search', 'search', '', 'string' );
		$search		= $db->getEscaped( trim(JString::strtolower( $search ) ) );

		$where = array();

		if( $cid == '')
		{
			if ( $type )
			{
				if ( $type == 'group' )
				{
					if(DiscussHelper::getJoomlaVersion() < '1.6')
					{
						$where[] = '(a.`id` > 17 AND a.`id` < 26)';
					}
				}
				else if( $type == 'assigned' )
				{
					$where[] = 'b.`type` = '.$db->quote($type);
				}
			}

			if ($search)
			{
				$where[] = ' LOWER( name ) LIKE \'%' . $search . '%\' ';
			}
		}
		else
		{
			if ( $type == 'group' )
			{
				$where[] = 'a.`id` = ' . $db->quote($cid);
			}
			else if( $type == 'assigned' )
			{
				$where[] = 'a.`id` = '.$db->quote($cid);
				$where[] = 'b.`type` = '.$db->quote($type);
			}
		}

		$where = ( count( $where ) ? ' WHERE ' .implode( ' AND ', $where ) : '' );

		return $where;
	}

	function _buildQueryOrderBy($type = 'group')
	{
		$mainframe			= JFactory::getApplication();

		$filter_order		= $mainframe->getUserStateFromRequest( 'com_easydiscuss.acls.filter_order', 'filter_order', 'a.`id`', 'cmd' );
		$filter_order_Dir	= $mainframe->getUserStateFromRequest( 'com_easydiscuss.acls.filter_order_Dir', 'filter_order_Dir', '', 'word' );

		if(($type == 'group') && (DiscussHelper::getJoomlaVersion() >= '1.6'))
		{
			$orderby	 = ' GROUP BY a.id';
			$orderby	.= ' ORDER BY a.lft ASC';
		}
		else
		{
			$orderby 	= ' ORDER BY '.$filter_order.' '.$filter_order_Dir;
		}

		return $orderby;
	}
}
