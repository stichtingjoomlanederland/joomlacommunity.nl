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

class EasyDiscussModelSubscribe extends EasyDiscussAdminModel
{
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

	function __construct()
	{
		parent::__construct();


		$mainframe	= JFactory::getApplication();

		$limit			= $mainframe->getUserStateFromRequest( 'com_easydiscuss.subscription.limit', 'limit', $mainframe->getCfg('list_limit'), 'int');
		$limitstart		= JRequest::getVar('limitstart', 0, '', 'int');

		$this->setState('limit', $limit);
		$this->setState('limitstart', $limitstart);
	}

	function getSubscription()
	{

		if(empty($this->_data) )
		{
			$query = $this->_buildQuery();

			$this->_data	= $this->_getList( $this->_buildQuery() , $this->getState('limitstart'), $this->getState('limit') );

		}

		return $this->_data;
	}

	function getTotal()
	{
		// Lets load the content if it doesn't already exist
		if (empty($this->_total))
		{
			$query = $this->_buildQuery();
			$this->_total = $this->_getListCount($query);
		}

		return $this->_total;
	}

	function getPagination()
	{
		// Lets load the content if it doesn't already exist
		if (empty($this->_pagination))
		{
			jimport('joomla.html.pagination');
			$this->_pagination = new JPagination( $this->getTotal(), $this->getState('limitstart'), $this->getState('limit') );
		}

		return $this->_pagination;
	}

	function _buildQuery()
	{

		$db			= DiscussHelper::getDBO();
		$mainframe	= JFactory::getApplication();

		$filter		= $mainframe->getUserStateFromRequest( 'com_easydiscuss.subscription.filter', 'filter', 'site', 'word' );


		// Get the WHERE and ORDER BY clauses for the query
		$where		= $this->_buildQueryWhere();
		$orderby	= $this->_buildQueryOrderBy();



		$query  = '';

		$query	.= 'SELECT a.*, b.`name`, b.`username`, c.`title` as `bname`';
		$query	.= '  FROM `#__discuss_subscription` a';
		$query	.= '    left join `#__users` b on a.`userid` = b.`id`';

		if( $filter == 'category' )
		{
			$query .= '    left join `#__discuss_category` c on a.`cid` = c.`id`';
		}
		else
		{
			$query .= '    left join `#__discuss_posts` c on a.`cid` = c.`id`';
		}

		$query	.= ' WHERE a.`type` = ' . $db->Quote( $filter );
		$query	.= $where;

		$query	.= $orderby;

		return $query;
	}

	function _buildQueryOrderBy()
	{
		$mainframe			= JFactory::getApplication();

		$filter_order		= $mainframe->getUserStateFromRequest( 'com_easydiscuss.subscription.filter_order', 		'filter_order', 	'fullname', 'cmd' );
		$filter_order_Dir	= $mainframe->getUserStateFromRequest( 'com_easydiscuss.subscription.filter_order_Dir',	'filter_order_Dir',	'', 'word' );

		$orderby			= ' ORDER BY '.$filter_order.' '.$filter_order_Dir;

		return $orderby;
	}

	function _buildQueryWhere()
	{
		$mainframe	= JFactory::getApplication();
		$db			= DiscussHelper::getDBO();

		$search 	= $mainframe->getUserStateFromRequest( 'com_easydiscuss.subscription.search', 'search', '', 'string' );
		$search 	= $db->getEscaped( trim(JString::strtolower( $search ) ) );

		$where = array();

		if ($search)
		{
			$where[] = ' LOWER( a.`email` ) LIKE \'%' . $search . '%\'';
			$where[] = ' LOWER( a.`fullname` ) LIKE \'%' . $search . '%\'';
		}

		$where 		= ( count( $where ) ? ' AND (' . implode( ' OR ', $where ) . ')' : '' );

		return $where;
	}



	function getSiteSubscribers($interval='daily', $now='')
	{
		$db = DiscussHelper::getDBO();

		$query  = 'SELECT * FROM `#__discuss_subscription` '
				. ' WHERE `interval` = ' . $db->Quote($interval)
				. ' AND `type` = ' . $db->Quote('site');

		if(!empty($now))
		{
			switch($interval)
			{
				case 'weekly':
					$days = '7';
					break;
				case 'monthly':
					$days = '30';
					break;
				case 'daily':
					$days = '1';
				default :
					break;
			}

			$query	.= ' AND DATEDIFF(' . $db->Quote($now) . ', `sent_out`) >= ' . $db->Quote($days);
		}

		$db->setQuery($query);

		$result = $db->loadObjectList();
		return $result;
	}

	function getPostSubscribers($postid='')
	{
		if(empty($postid))
		{
			//invalid post id
			return false;
		}

		$db = DiscussHelper::getDBO();

		$query  = 'SELECT * FROM `#__discuss_subscription` '
				. ' WHERE `type` = ' . $db->Quote('post')
				. ' AND `cid` = ' . $db->Quote($postid);

		$db->setQuery($query);

		$result = $db->loadObjectList();
		return $result;
	}

	function getTagSubscribers($tagid='')
	{
		if(empty($tagid))
		{
			//invalid tag id
			return false;
		}

		$db = DiscussHelper::getDBO();

		$query  = 'SELECT * FROM `#__discuss_subscription` '
				. ' WHERE `type` = ' . $db->Quote('tag')
				. ' AND `cid` = ' . $db->Quote($tagid);

		$db->setQuery($query);

		$result = $db->loadObjectList();
		return $result;
	}

	function getCategorySubscribers($catid='')
	{
		if(empty($catid))
		{
			//invalid category id
			return false;
		}

		$db = DiscussHelper::getDBO();

		$query  = 'SELECT * FROM `#__discuss_subscription` '
				. ' WHERE `type` = ' . $db->Quote('category')
				. ' AND `cid` = ' . $db->Quote($catid);

		$db->setQuery($query);

		$result = $db->loadObjectList();
		return $result;
	}

	function addSubscription($subscription_info)
	{
		$config = DiscussHelper::getConfig();
		$my = JFactory::getUser();

		if($config->get('main_allowguestsubscribe') || ($my->id && !$config->get('main_allowguestsubscribe')))
		{
			$date		= DiscussHelper::getDate();
			$now		= $date->toMySQL();
			$subscriber	= JTable::getInstance( 'Subscribe', 'Discuss' );

			$subscriber->userid		= $subscription_info['userid'];
			$subscriber->member		= $subscription_info['member'];
			$subscriber->type		= $subscription_info['type'];
			$subscriber->cid		= $subscription_info['cid'];
			$subscriber->email		= $subscription_info['email'];
			$subscriber->fullname	= $subscription_info['name'];
			$subscriber->interval	= $subscription_info['interval'];
			$subscriber->created	= $now;
			$subscriber->sent_out	= $now;
			return $subscriber->store();
		}

		return false;
	}

	function isPostSubscribedEmail($subscription_info)
	{
		$db	= DiscussHelper::getDBO();

		$query  = 'SELECT `id` FROM `#__discuss_subscription`';
		$query  .= ' WHERE `type` = ' . $db->Quote('post');
		$query  .= ' AND `email` = ' . $db->Quote($subscription_info['email']);
		$query  .= ' AND `cid` = ' . $db->Quote($subscription_info['cid']);

		$db->setQuery($query);
		$result = $db->loadAssoc();

		return $result;
	}

	function isPostSubscribedUser($subscription_info)
	{
		$db	= DiscussHelper::getDBO();

		$query  = 'SELECT `id` FROM `#__discuss_subscription`';
		$query  .= ' WHERE `type` = ' . $db->Quote('post');
		$query  .= ' AND (`userid` = ' . $db->Quote($subscription_info['userid']) . ' OR `email` = ' . $db->Quote($subscription_info['email']) . ')';
		$query  .= ' AND `cid` = ' . $db->Quote($subscription_info['cid']);

		$db->setQuery($query);
		$result = $db->loadAssoc();

		return $result;
	}

	// Used in user plugin when user changes email, all previous subscribed email should update to the new email.
	function updateSubscriberEmail( $data, $isNew )
	{
		if( !$isNew )
		{
			$db = DiscussHelper::getDBO();
			$query = 'UPDATE ' . $db->nameQuote( '#__discuss_subscription' )
					. ' SET ' . $db->nameQuote( 'email' ) . '=' . $db->quote( $data['email'] )
					. ' WHERE ' . $db->nameQuote( 'userid' ) . '=' . $db->Quote( $data['id'] );

			$db->setQuery( $query );

			if( !$db->query() )
			{
				return false;
			}
		}
	}

	public function isSubscribed( $userid, $cid, $type = 'post' )
	{
		$db		= DiscussHelper::getDBO();
		$query	= 'SELECT `id` FROM `#__discuss_subscription`'
				. ' WHERE `type` = ' . $db->quote( $type )
				. ' AND `userid` = ' . $db->quote( $userid )
				. ' AND `cid` = ' . $db->quote( $cid );

		$db->setQuery( $query );
		return $db->loadResult();
	}
}
