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

class DiscussCustomFields extends JTable
{
	public $id				= null;
	public $type			= null;
	public $title			= null;
	public $ordering		= null;
	public $published		= null;
	public $params			= null;

	/**
	 * Constructor for this class.
	 *
	 * @return
	 * @param object $db
	 */
	public function __construct(& $db )
	{
		parent::__construct( '#__discuss_customfields' , 'id' , $db );
	}

	/**
	 * Overrides parent's bind method to add our own logic.
	 *
	 * @param Array $data
	 **/
	public function bind( $data , $ignore = array() )
	{
		parent::bind( $data );
	}


	public function move($direction, $where = '')
	{
		$db = DiscussHelper::getDBO();

		$currentOrdering = $this->ordering;

		if( $direction == -1) //moving up
		{
			$newOrdering    = $currentOrdering - 1;
			if( $this->ordering > 0 )
			{

				$query	= 'UPDATE `#__discuss_customfields` SET `ordering` = `ordering` + 1 WHERE `ordering` = ' . $db->quote( $newOrdering );
				$db->setQuery($query);
				$db->query();

				$query	= 'UPDATE `#__discuss_customfields` SET `ordering` = ' . $db->Quote( $newOrdering );
				$query	.= ' WHERE `id` = ' . $db->quote($this->id);
				$db->setQuery($query);
				$db->query();
			}
		}
		else
		{
			$newOrdering    = $currentOrdering + 1;

			$query	= 'UPDATE `#__discuss_customfields` SET `ordering` = `ordering` - 1 WHERE `ordering` = ' . $db->quote( $newOrdering );
			$db->setQuery($query);
			$db->query();

			$query	= 'UPDATE `#__discuss_customfields` SET `ordering` = ' . $db->Quote( $newOrdering );
			$query	.= ' WHERE `id` = ' . $db->quote($this->id);
			$db->setQuery($query);
			$db->query();
		}

		return $this->rebuild();
	}


	public function moveOld($direction, $where = '')
	{
		$db = DiscussHelper::getDBO();

		if( $direction == -1) //moving up
		{
			if( $this->ordering > 0 )
			{
				$query	= 'UPDATE `#__discuss_customfields` SET `ordering` = `ordering` + 5 WHERE `ordering` >= ' . $db->quote( $this->ordering + 2 );
				$db->setQuery($query);
				$db->query();

				$query	= 'UPDATE `#__discuss_customfields` SET `ordering` = `ordering` + 1 WHERE `ordering` = ' . $db->quote( $this->ordering + 1 );
				$db->setQuery($query);
				$db->query();

				$query	= 'UPDATE `#__discuss_customfields` SET `ordering` = `ordering` + 3 WHERE `id` = ' . $db->quote($this->id);
				$db->setQuery($query);
				$db->query();
			}
		}
		else
		{
			$query	= 'UPDATE `#__discuss_customfields` SET `ordering` = `ordering` - 5 WHERE `ordering` <= ' . $db->quote( $this->ordering - 2 );

			$db->setQuery($query);
			$db->query();

			$query	= 'UPDATE `#__discuss_customfields` SET `ordering` = `ordering` - 1 WHERE `ordering` = ' . $db->quote( $this->ordering - 1 );
			$db->setQuery($query);
			$db->query();

			$query	= 'UPDATE `#__discuss_customfields` SET `ordering` = `ordering` - 3 WHERE `id` = ' . $db->quote($this->id);
			$db->setQuery($query);
			$db->query();
		}

		return $this->rebuild();
	}

	public function rebuildOrdering()
	{
		// Get the input
		$pks	= JRequest::getVar('cid', null, 'post', 'array');
		$order	= JRequest::getVar('order', null, 'post', 'array');

		// Sanitize the input
		JArrayHelper::toInteger($pks);
		JArrayHelper::toInteger($order);

		if (is_array($pks) && is_array($order) && count($pks) == count($order))
		{
			$db = DiscussHelper::getDBO();

			for ($i = 0, $count = count($pks); $i < $count; $i++)
			{
				$query	= 'UPDATE ' . $db->nameQuote( '#__discuss_customfields' )
						. ' SET ' . $db->nameQuote( 'ordering' ) . '=' . $db->Quote( $order[$i] )
						. ' WHERE ' . $db->nameQuote( 'id' ) . '=' . $db->Quote( $pks[$i] );
				$db->setQuery($query);

				if( !$db->query() )
				{
					return false;
				}
			}

			return $this->rebuild();
		}
		else
		{
			return false;
		}
	}

	public function rebuild()
	{
		$db		= DiscussHelper::getDBO();
		$query	= 'SELECT ' . $db->nameQuote( 'id' ) . ', ' . $db->nameQuote( 'ordering' )
				. ' FROM ' .  $db->nameQuote( '#__discuss_customfields' )
				. ' ORDER BY ' . $db->nameQuote( 'ordering' ) . ', ' . $db->nameQuote( 'id' ) . ' DESC';
		$db->setQuery($query);
		$rows	= $db->loadObjectList();

		foreach ($rows as $i => $row)
		{
			$order	= $i + 1;
			$query	= 'UPDATE ' . $db->nameQuote( '#__discuss_customfields' )
					. ' SET ' . $db->nameQuote( 'ordering' ) . '=' . $db->Quote( $order )
					. ' WHERE ' . $db->nameQuote( 'id' ) .  '=' . $db->Quote( $row->id );
			$db->setQuery($query);
			if( !$db->query() )
			{
				return false;
			}
		}

		return true;
	}

	public function getAssignedACL( $type = 'group' )
	{
		$db		= DiscussHelper::getDBO();
		$acl	= array();

		$query	= 'SELECT'
				. ' a.' . $db->nameQuote( 'field_id' ) . ','
				. ' a.' . $db->nameQuote( 'content_id' ) . ','
				. ' a.' . $db->nameQuote( 'status' ) . ','
				. ' b.' . $db->nameQuote( 'id' ) . ' AS `acl_id`'
				. ' FROM ' . $db->nameQuote( '#__discuss_customfields_rule' ) . ' AS a'
				. ' LEFT JOIN ' . $db->nameQuote( '#__discuss_customfields_acl' ) . ' AS b'
				. ' ON a.' . $db->nameQuote( 'acl_id' ) . '=' . 'b.' . $db->nameQuote( 'id' )
				. ' WHERE a.' . $db->nameQuote( 'field_id' ) . '=' . $db->Quote( $this->id )
				. ' AND a.' . $db->nameQuote( 'content_type' ) . '=' . $db->Quote( $type );

		$db->setQuery( $query );
		$result = $db->loadObjectList();

		if( count($result) > 0 )
		{
			$acl = null;
			if( $type == 'group' )
			{
				$joomlaGroups = DiscussHelper::getJoomlaUserGroups();
				$acl	= $this->_mapRules($result, $joomlaGroups);
			}
			else
			{
				$users	= $this->getAclUsers( $result );
				$acl	= $this->_mapRules($result, $users);
			}

			return $acl;
		}
		else
		{
			return null;
		}
	}

	public function _mapRules( $customRules, $joomlaGroups)
	{
		$db		= DiscussHelper::getDBO();
		$acl	= array();

		$query	= 'SELECT *'
				. ' FROM ' . $db->nameQuote( '#__discuss_customfields_acl' )
				. ' ORDER BY ' . $db->nameQuote( 'id' );
		$db->setQuery( $query );

		$result = $db->loadObjectList();

		if( !$result )
		{
			return $result;
		}

		foreach( $result as $item )
		{
			$aclId		= $item->id;
			$default	= $item->default;

			foreach( $joomlaGroups as $joomla )
			{
				$groupId		= $joomla->id;
				$customRulesCnt	= count($customRules);
				//now match each of the customRules
				if( $customRulesCnt > 0)
				{
					$cnt = 0;
					foreach( $customRules as $rule)
					{
						if($rule->acl_id == $aclId && $rule->content_id == $groupId)
						{
							$acl[$aclId][$groupId]				= new stdClass();
							$acl[$aclId][$groupId]->status		= $rule->status;
							$acl[$aclId][$groupId]->acl_id		= $aclId;
							$acl[$aclId][$groupId]->groupname	= $joomla->name;
							$acl[$aclId][$groupId]->groupid		= $groupId;
							break;
						}
						else
						{
							$cnt++;
						}
					}

					if( $cnt == $customRulesCnt)
					{
						//this means the rules not exist in this joomla group.
						$acl[$aclId][$groupId]				= new stdClass();
						$acl[$aclId][$groupId]->status		= '0';
						$acl[$aclId][$groupId]->acl_id		= $aclId;
						$acl[$aclId][$groupId]->groupname	= $joomla->name;
						$acl[$aclId][$groupId]->groupid		= $groupId;
					}
				}
				else
				{
					$acl[$aclId][$groupId]->status		= $default;
					$acl[$aclId][$groupId]->acl_id		= $aclId;
					$acl[$aclId][$groupId]->groupname	= $joomla->name;
					$acl[$aclId][$groupId]->groupid		= $groupId;
				}
			}
		}

		return $acl;
	}

	public function getAclUsers( $aclUsers )
	{
		$db = DiscussHelper::getDBO();

		$users  = array();

		foreach( $aclUsers as $item)
		{
			$users[] = $item->content_id;
		}

		$userlist   = '';

		foreach($users as $user)
		{
			$userlist .= ( $userlist == '') ? $db->Quote($user) : ', ' . $db->Quote($user);
		}


		$query  = 'SELECT '
				. $db->nameQuote( 'id' ) . ', ' . $db->nameQuote( 'name' )
				. ' FROM ' . $db->nameQuote( '#__users' )
				. ' WHERE ' . $db->nameQuote( 'id' )
				. ' IN (' . $userlist . ')';
		$db->setQuery($query);

		$result = $db->loadObjectList();

		return $result;
	}


	public function getAdvanceOption( $active = '' )
	{
		$html   = '';

		if( empty($active) )
		{
			//Default selection when user first creating a new field.
			$active = 'text';
		}

		$newField = false;
		$loadField = false;

		switch( $active )
		{
			case 'text':

				$fieldId = 'text';
				$fieldName = 'textValue[]';

				if( empty($this->id) || $this->type != 'text')
				{
					$newField = true;
				}
				else if( $this->id && $this->type =='text' )
				{
					$loadField = true;
				}
			break;

			case 'area':

				$fieldId = 'textArea';
				$fieldName = 'textAreaValue[]';

				if( empty($this->id) || $this->type != 'area')
				{
					$newField = true;
				}
				else if( $this->id && $this->type =='area' )
				{
					$loadField = true;
				}
			break;

			case 'radio':
				if( empty($this->id) || $this->type != 'radio')
				{
					$newField = true;
					$fieldType = 'radiobtn';
				}
				else if( $this->id && $this->type == 'radio' )
				{
					$loadField = true;
					$fieldType = 'radiobtn';
					$fieldName = 'radioBtnValue[]';
				}
			break;

			case 'check':
				if( empty($this->id) || $this->type != 'check')
				{
					$newField = true;
					$fieldType = 'checkbox';
				}
				else if( $this->id && $this->type =='check' )
				{
					$loadField = true;
					$fieldType = 'checkbox';
					$fieldName = 'checkBoxValue[]';
				}
			break;

			case 'select':
				if( empty($this->id) || $this->type != 'select')
				{
					$newField = true;
					$fieldType = 'selectlist';
				}
				else if( $this->id && $this->type =='select' )
				{
					$loadField = true;
					$fieldType = 'selectlist';
					$fieldName = 'selectValue[]';
				}
			break;

			case 'multiple':
				if( empty($this->id) || $this->type != 'multiple')
				{
					$newField = true;
					$fieldType = 'multiplelist';
				}
				else if( $this->id && $this->type =='multiple' )
				{
					$loadField = true;
					$fieldType = 'multiplelist';
					$fieldName = 'multipleValue[]';
				}
			break;

			default:
				break;
		}

		if( $newField == true )
		{
			$count = 0;
			// New Field
			if( $active == 'text' || $active == 'area' )
			{
				$addButton = '';
				$html = '<label>'. JText::_( 'COM_EASYDISCUSS_CUSTOMFIELDS_PLACEHOLDER' ) .'</label><input type="text" class="input-full" id="'.$fieldId.'" name="'.$fieldName.'" value="" />';
			}
			else
			{
				$addButton = '<button type="button" class="btn" name="Add" data-fieldtype="'.$fieldType.'" >' . JText::_( 'COM_EASYDISCUSS_CUSTOMFIELDS_ADD' ) . '</button>';
			}
		}

		if( $loadField == true )
		{
			$params = unserialize($this->params);

			// Load previous
			if( $active == 'text' || $active == 'area' )
			{
				foreach( $params as $count => $param )
				{
					$addButton = '';
					$html = '<label>'. JText::_( 'COM_EASYDISCUSS_CUSTOMFIELDS_PLACEHOLDER' ) .'</label><input type="text" class="input-full" id="'.$fieldId.'" name="'.$fieldName.'" value="'.DiscussHelper::getHelper('String')->escape($param).'" />';
				}
			}
			else
			{
				$addButton 	= '<button type="button" class="btn" name="Add" data-fieldtype="'.$fieldType.'">' . JText::_( 'COM_EASYDISCUSS_CUSTOMFIELDS_ADD' ) . '</button>';
				foreach( $params as $count => $param )
				{
					$html 	.= '<li class="remove'.$fieldType.'_'.$count.'">'
							. '<div class="span10 remove'.$fieldType.'_'.$count.'">'
							. '<div class="input-append remove'.$fieldType.'_'.$count.'">'
							. '<input type="text" class="input-full '.$fieldType.'" id="'.$fieldType.'_'.$count.'" name="'.$fieldName.'" value="'.DiscussHelper::getHelper('String')->escape($param).'" />'
							. '<button type="button" class="btn btn-danger btn-customfield-remove" id="'.$count.'" name="Remove" data-removetype="'.$fieldType.'"><i class="icon-remove"></i></button>'
							. '</div>'
							. '</div>'
							. '</li>';
				}
			}
		}

		return $result = array( 'addButton'=>$addButton, 'html'=>$html, 'count'=>$count );
	}

}
