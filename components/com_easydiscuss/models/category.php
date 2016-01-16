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

class EasyDiscussModelCategory extends EasyDiscussModel
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

		$limit		= ($mainframe->getCfg('list_limit') == 0) ? 5 : DiscussHelper::getListLimit();
		$limitstart = JRequest::getVar('limitstart', 0, 'REQUEST');

		// In case limit has been changed, adjust it
		$limitstart = ($limit != 0 ? (floor($limitstart / $limit) * $limit) : 0);

		$this->setState('limit', $limit);
		$this->setState('limitstart', $limitstart);
	}


	/**
	 * Method to get a pagination object for the categories
	 *
	 * @access public
	 * @return integer
	 */
	public function getPagination()
	{
		return $this->_pagination;
	}

	protected function _getParentIdsWithPost()
	{
		$db	= DiscussHelper::getDBO();
		$my = JFactory::getUser();

		$query	= 'select * from `#__discuss_category`';
		$query	.= ' where `published` = 1';
		$query	.= ' and `parent_id` = 0';
		if($my->id == 0)
		{
			$query	.= ' and `private` = 0';
		}


		$db->setQuery($query);
		$result = $db->loadObjectList();


		$validCat	= array();

		if(count($result) > 0)
		{
			for($i = 0; $i < count($result); $i++)
			{
				$item =& $result[$i];

				$item->childs = null;
				DiscussHelper::buildNestedCategories($item->id, $item);

				$catIds		= array();
				$catIds[]	= $item->id;
				DiscussHelper::accessNestedCategoriesId($item, $catIds);

				$item->cnt	= $this->getTotalPostCount($catIds);

				if($item->cnt > 0)
				{
					$validCat[] = $item->id;
				}

			}
		}

		return $validCat;
	}

	/*
	 * Retrieves the default category
	 */
	public function getDefaultCategory()
	{
		$db 	= DiscussHelper::getDBO();

		$query 	= 'SELECT * FROM ' . $db->nameQuote( '#__discuss_category' ) . ' '
				. 'WHERE ' . $db->nameQuote( 'default' ) . '=' . $db->Quote( 1 );
		$db->setQuery($query);
		$result = $db->loadObject();

		if( !$result )
		{
			return false;
		}

		$category 	= DiscussHelper::getTable( 'Category' );
		$category->bind( $result );

		return $category;
	}

	public function getCategories($sort = 'latest', $hideEmptyPost = true, $limit = 0)
	{
		$db	= DiscussHelper::getDBO();

		//blog privacy setting
		$my = JFactory::getUser();

		$orderBy	= '';
		$limit		= ($limit == 0) ? $this->getState('limit') : $limit;
		$limitstart	= $this->getState('limitstart');
		$limitSQL	= ' LIMIT ' . $limitstart . ',' . $limit;

		$andWhere	= array();

		$andWhere[]	= ' a.`published` = 1';
		$andWhere[]	= ' a.`parent_id` = 0';
		if($my->id == 0)
			$andWhere[]	= ' a.`private` = 0';

		if($hideEmptyPost)
		{
			$arrParentIds	= $this->_getParentIdsWithPost();

			if(! empty($arrParentIds))
			{
				$tmpParentId	= implode(',', $arrParentIds);
				$andWhere[]		= ' a.`id` IN (' . $tmpParentId . ')';
			}

			if($my->id == 0)
				$andWhere[]	= ' a.`private` = 0';

			$this->_total	= count($arrParentIds);
		}
		else
		{
			$extra	= ( count( $andWhere ) ? ' WHERE ' . implode( ' AND ', $andWhere ) : '' );

			$query	= 'SELECT a.`id` FROM ' . $db->nameQuote( '#__discuss_category' ) . ' AS a';
			$query	.= ' LEFT JOIN '. $db->nameQuote( '#__discuss_posts' ) . ' AS b';
			$query	.= ' ON a.`id` = b.`category_id`';
			$query	.= ' AND b.`published` = ' . $db->Quote('1');

			$query	.= $extra;
			$query	.= ' GROUP BY a.`id`';

			$db->setQuery( $query );
			$result	= $db->loadResultArray();

			$this->_total	= count($result);

			if($db->getErrorNum())
			{
				JError::raiseError( 500, $db->stderr());
			}
		}

		if( empty($this->_pagination) )
		{
			jimport('joomla.html.pagination');
			$this->_pagination	= new JPagination( $this->_total , $limitstart , $limit);
		}

		$extra 		= ( count( $andWhere ) ? ' WHERE ' . implode( ' AND ', $andWhere ) : '' );

		$query	= 'SELECT a.`id`, a.`title`, a.`alias`, COUNT(b.`id`) AS `cnt`, a.`description`';
		$query	.= ' FROM ' . $db->nameQuote( '#__discuss_category' ) . ' AS `a`';
		$query	.= ' LEFT JOIN '. $db->nameQuote( '#__discuss_posts' ) . ' AS b';
		$query	.= ' ON a.`id` = b.`category_id`';
		$query	.= ' AND b.`published` = ' . $db->Quote('1');
		$query	.= $extra;
		$query	.= ' GROUP BY a.`id`';

		switch($sort)
		{
			case 'popular' :
				$orderBy	= ' ORDER BY `cnt` DESC';
				break;
			case 'alphabet' :
				$orderBy = ' ORDER BY a.`title` ASC';
				break;
			case 'ordering' :
				$orderBy = ' ORDER BY a.`ordering` ASC';
				break;
			case 'latest' :
			default	:
				$orderBy = ' ORDER BY a.`created` DESC';
				break;
		}
		$query	.= $orderBy;
		$query	.= $limitSQL;

		$db->setQuery( $query );
		$result	= $db->loadObjectList();

		if($db->getErrorNum())
		{
			JError::raiseError( 500, $db->stderr());
		}

		return $result;

	}

	public function getTotalPostCount($catIds)
	{
		$db	= DiscussHelper::getDBO();

		//blog privacy setting
		$my = JFactory::getUser();

		$categoryId = '';
		$isIdArray  = false;

		if(is_array($catIds))
		{
			if(count($catIds) > 1)
			{
				$categoryId	= implode(',', $catIds);
				$isIdArray  = true;
			}
			else
			{
				$categoryId	= $catIds[0];
			}
		}
		else
		{
			$categoryId  = $catIds;
		}


		// $query	= 'SELECT COUNT(b.`id`) AS `cnt`';
		// $query	.= ' FROM ' . $db->nameQuote( '#__discuss_category' ) . ' AS `a`';
		// $query	.= ' LEFT JOIN '. $db->nameQuote( '#__discuss_posts' ) . ' AS b';
		// $query	.= ' ON a.`id` = b.`category_id`';
		// $query	.= ' AND b.`published` = ' . $db->Quote('1');
		// $query	.= ' WHERE a.`published` = 1';
		// $query	.= ($isIdArray) ? ' AND a.`id` IN (' . $categoryId. ')' :  ' AND a.`id` = ' . $db->Quote($categoryId);
		// $query	.= ' GROUP BY a.`id` HAVING (COUNT(b.`id`) > 0)';
		//

		$query = 'select count(1) from `#__discuss_posts` as a';
		$query .= ($isIdArray) ? ' WHERE a.`category_id` IN (' . $categoryId. ')' : ' where a.`category_id` = ' . $db->Quote($categoryId);
		$query .= ' and a.published = 1';

		$db->setQuery($query);
		$result = $db->loadResult();

		return ($result) ? $result : 0;
	}

	/**
	 * Method to get total category created so far iregardless the status.
	 *
	 * @access public
	 * @return integer
	 */
	public function getTotalCategory( $userId = 0 )
	{
		$db		= DiscussHelper::getDBO();
		$where	= array();

		$query	= 'SELECT COUNT(1) FROM ' . $db->nameQuote( '#__discuss_category' );

		if(! empty($userId))
			$where[]  = '`created_by` = ' . $db->Quote($userId);

		$extra	= ( count( $where ) ? ' WHERE ' . implode( ' AND ', $where ) : '' );
		$query	= $query . $extra;

		$db->setQuery( $query );

		$result	= $db->loadResult();

		return (empty($result)) ? 0 : $result;
	}

	public function isExist($categoryName, $excludeCatIds='0')
	{
		$db = DiscussHelper::getDBO();

		$query  = 'SELECT COUNT(1) FROM #__discuss_category';
		$query  .= ' WHERE `title` = ' . $db->Quote($categoryName);
		if($excludeCatIds != '0')
			$query  .= ' AND `id` != ' . $db->Quote($excludeCatIds);

		$db->setQuery($query);
		$result = $db->loadResult();

		return (empty($result)) ? 0 : $result;
	}
}
