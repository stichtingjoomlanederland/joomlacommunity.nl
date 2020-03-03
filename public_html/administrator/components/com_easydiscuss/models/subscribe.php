<?php
/**
* @package		EasyDiscuss
* @copyright	Copyright (C) 2010 - 2018 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasyDiscuss is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');

require_once dirname( __FILE__ ) . '/model.php';

class EasyDiscussModelSubscribe extends EasyDiscussAdminModel
{
	public $_data = null;
	public $_pagination = null;
	public $_total;

	public function __construct()
	{
		parent::__construct();

		$limit = $this->app->getUserStateFromRequest( 'com_easydiscuss.subscription.limit', 'limit', $this->app->getCfg('list_limit'), 'int');
		$limitstart	= $this->input->get('limitstart', 0, 'int');

		$this->setState('limit', $limit);
		$this->setState('limitstart', $limitstart);
	}

	public function getSubscription()
	{
		if(empty($this->_data) )
		{
			$query = $this->_buildQuery();

			$this->_data	= $this->_getList( $this->_buildQuery() , $this->getState('limitstart'), $this->getState('limit') );

		}

		return $this->_data;
	}

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

	public function getPagination()
	{
		// Lets load the content if it doesn't already exist
		if (empty($this->_pagination)) {
			jimport('joomla.html.pagination');
			$this->_pagination = new JPagination($this->getTotal(), $this->getState('limitstart'), $this->getState('limit'));
			//$this->_pagination = ED::getPagination($this->getTotal(), $this->getState('limitstart'), $this->getState('limit'));
		}

		return $this->_pagination;
	}

	public function _buildQuery()
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

	public function _buildQueryOrderBy()
	{
		$mainframe			= JFactory::getApplication();

		$filter_order		= $mainframe->getUserStateFromRequest( 'com_easydiscuss.subscription.filter_order', 		'filter_order', 	'fullname', 'cmd' );
		$filter_order_Dir	= $mainframe->getUserStateFromRequest( 'com_easydiscuss.subscription.filter_order_Dir',	'filter_order_Dir',	'', 'word' );

		$orderby			= ' ORDER BY '.$filter_order.' '.$filter_order_Dir;

		return $orderby;
	}

	public function _buildQueryWhere()
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

	public function getSiteSubscribers($interval='daily', $now='', $categoryId = null)
	{
		$db = JFactory::getDBO();

		$timeQuery      = '';
		$categoryGrps   = array();

		if(! is_null( $categoryId ) )
		{
			$query  = 'SELECT `content_id` FROM `#__discuss_category_acl_map`';
			$query	.= ' WHERE `category_id` = ' . $db->Quote($categoryId);
			$query	.= ' AND `acl_id` = ' . $db->Quote(DISCUSS_CATEGORY_ACL_ACTION_VIEW);
			$query	.= ' AND `type` = ' . $db->Quote('group');

			$db->setQuery( $query );
			$categoryGrps   = $db->loadResultArray();
		}

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

			$timeQuery	= ' AND DATEDIFF(' . $db->Quote($now) . ', `sent_out`) >= ' . $db->Quote($days);
		}


		if(! empty($categoryGrps) )
		{
			$result 		= array();
			$aclItems   	= array();
			$nonAclItems    = array();

			// site members
			$queryCatIds = implode( ',', $categoryGrps );

			$query  = 'SELECT * FROM `#__discuss_subscription` AS ds';
			$query	.= ' INNER JOIN `#__user_usergroup_map` as um on um.`user_id` = ds.`userid`';
			$query	.= ' WHERE ds.`interval` = ' . $db->Quote($interval);
			$query	.= ' AND ds.`type` = ' . $db->Quote('site');
			$query	.= ' AND um.`group_id` IN (' . $queryCatIds. ')';

			$db->setQuery( $query );
			$aclItems  = $db->loadObjectList();

			if( count( $aclItems ) > 0 )
			{
				foreach( $aclItems as $item )
				{
					$result[] = $item;
				}
			}

			//now get the guest subscribers
			if( in_array( '1', $categoryGrps ) || in_array( '0', $categoryGrps ) )
			{

				$query  = 'SELECT * FROM `#__discuss_subscription` AS ds';
				$query	.= ' WHERE ds.`interval` = ' . $db->Quote($interval);
				$query	.= ' AND ds.`type` = ' . $db->Quote('site');
				$query	.= ' AND ds.`userid` = ' . $db->Quote('0');

				$db->setQuery( $query );
				$nonAclItems  = $db->loadObjectList();

				if( count( $nonAclItems ) > 0 )
				{
					foreach( $nonAclItems as $item )
					{
						$result[] = $item;
					}
				}

			}
		}
		else
		{
			$query  = 'SELECT * FROM `#__discuss_subscription` AS ds'
					. ' WHERE ds.`interval` = ' . $db->Quote($interval)
					. ' AND ds.`type` = ' . $db->Quote('site');

			$query  .= $timeQuery;
			$query .= 'AND ' . $db->quoteName('state') . ' = ' . $db->Quote(1);

			$db->setQuery($query);

			$result = $db->loadObjectList();

		}

		return $result;
	}

	public function getPostSubscribers($postid = '')
	{
		if (empty($postid)) {
			return false;
		}

		$db = ED::db();

		$query = 'SELECT * FROM `#__discuss_subscription` WHERE `type` = ' . $db->Quote('post') . ' AND `cid` = ' . $db->Quote($postid);

		$db->setQuery($query);
		$result	= $db->loadObjectList();

		$emails = array();
		$subscribers = array();

		foreach ($result as $row) {

			if (!in_array($row->email, $emails)) {

				$subscribers[$row->email] = $row;
			}

			$emails[] = $row->email;
		}

		return $subscribers;
	}

	public function getTagSubscribers($tagid='')
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

		$result			= $db->loadObjectList();
		$emails			= array();
		$subscribers	= array();

		foreach( $result as $row )
		{
			if( !in_array( $row->email , $emails ) )
			{
				$subscribers[]	= $row;
			}
			$emails[]	= $row->email;
		}
		return $subscribers;
	}

	public function getCreatedPostByInterval($sent_out, $now='')
	{
		$db = $this->db;
		$date = ED::date();

		if (empty($now)) {
			$now 	= $date->toMySQL();
		}

		$query	= 'SELECT '
				. ' DATEDIFF(' . $db->Quote($now) . ', a.`created`) as `daydiff`, '
				. ' TIMEDIFF(' . $db->Quote($now). ', a.`created`) as `timediff`, a.* '
				. ' FROM `#__discuss_posts` as a '
				. ' WHERE a.`published` = 1 and a.`parent_id` = 0 AND ( a.`created` > ' . $db->Quote($sent_out) . ' AND a.`created` < ' . $db->Quote($now) . ')'
				. ' ORDER BY a.`created` ASC';

		$db->setQuery($query);

		$result = $db->loadAssocList();

		return $result;
	}

	public function isMySubscription( $userid, $type, $subId )
	{
		$db = DiscussHelper::getDBO();

		$query  = 'SELECT `id` FROM `#__discuss_subscription`';
		$query  .= ' WHERE `type` = ' . $db->Quote( $type );
		$query  .= ' AND `id` = ' . $db->Quote( $subId );
		$query  .= ' AND `userid` = ' . $db->Quote( $userid );

		$db->setQuery( $query );
		$result = $db->loadResult();

		return ( empty($result) ) ? false : true;
	}

	public function getSubscriptions()
	{
		$db = $this->db;
		$date = ED::date();
		$my		= JFactory::getUser();
		$userid	= $my->id;

		$email	= JRequest::getVar('email');
		$extra	= $email ? ' AND s.`email` = ' . $db->quote($email) : '';

		$query	= 'SELECT DATEDIFF('. $db->Quote($date->toMySQL()) . ', s.`created` ) AS `noofdays`,'
				. ' DATEDIFF(' . $db->Quote($date->toMySQL()) . ', IF(s.`sent_out` = '.$db->Quote('0000-00-00 00:00:00') . ', s.`created`, s.`sent_out`) ) AS `daydiff`, '
				. ' TIMEDIFF(' . $db->Quote($date->toMySQL()). ', IF(s.`sent_out` = '.$db->Quote('0000-00-00 00:00:00') . ', s.`created`, s.`sent_out`) ) AS `timediff`,'
				. ' IF(s.`sent_out` = '.$db->Quote('0000-00-00 00:00:00') . ', s.`created`, s.`sent_out`) as `lastsent`,'
				. ' s.*'
				. ' FROM `#__discuss_subscription` AS s'
				. ' WHERE s.`userid` = ' . $db->quote( (int) $userid )
				. $extra;

		$db->setQuery($query);

		$result	= $db->loadObjectList();

		$subscriptions	= array();

		foreach( $result as $row )
		{
			if( $row->type == 'post' )
			{
				// Test if the post still exists on the site.
				$query	= 'SELECT COUNT(1) FROM ' . $db->nameQuote( '#__discuss_posts' ) . ' '
						. 'WHERE ' . $db->nameQuote( 'id' ) . ' = ' . $db->Quote( $row->cid );
				$db->setQuery( $query );
				$exists	= $db->loadResult();

				if( $exists )
				{
					$subscriptions[]	= $row;
				}
			}
			else
			{
				$subscriptions[]	= $row;
			}
		}
		return $subscriptions;
	}

	public function getCategorySubscribers($postid='')
	{
		if(empty($postid))
		{
			return false;
		}

		// get category id
		$table = DiscussHelper::getTable( 'post' );
		$table->load( $postid );

		$categoryid = $table->category_id;

		$db = DiscussHelper::getDBO();

		$query  = 'SELECT * FROM `#__discuss_subscription` '
				. ' WHERE `type` = ' . $db->Quote('category')
				. ' AND `cid` = ' . $db->Quote($categoryid);

		$db->setQuery($query);

		$result			= $db->loadObjectList();

		$emails			= array();
		$subscribers	= array();

		foreach( $result as $row )
		{
			if( !in_array( $row->email , $emails ) )
			{
				$subscribers[$row->email]	= $row;
			}
			$emails[]	= $row->email;
		}

		return $subscribers;
	}

	/**
	 * Inserts a new subscriber
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function addSubscriber($data)
	{
		// For logged in users
		if ($data['userid']) {

			// Check if the user previously subscribed before
			$sid = $this->isPostSubscribedUser($data);

			if (empty($sid['id'])) {
				$this->addSubscription($data);
			}
		}

		// For non logged in users
		if (!$data['userid']) {

			// Check if the user previously subscribed before
			$sid = $this->isPostSubscribedEmail($data);

			if (empty($sid)) {
				$this->addSubscription($data);
			}
		}

	}

	public function addSubscription($subscription_info)
	{
		$config = ED::config();
		$my = JFactory::getUser();

		if ($config->get('main_allowguestsubscribe') || ($my->id && !$config->get('main_allowguestsubscribe'))) {
			$date = ED::date();
			$now = $date->toMySQL();
			$subscriber	= ED::table('Subscribe');

			$subscriber->userid = $subscription_info['userid'];
			$subscriber->member = $subscription_info['member'];
			$subscriber->type = $subscription_info['type'];
			$subscriber->cid = $subscription_info['cid'];
			$subscriber->email = $subscription_info['email'];
			$subscriber->fullname = $subscription_info['name'];
			$subscriber->interval = $subscription_info['interval'];
			$subscriber->created = $now;
			$subscriber->sent_out = $now;

			return $subscriber->store();
		}

		return false;
	}

	/**
	 * Updates an existing subscription.
	 *
	 * @since	3.0
	 * @access	public
	 */
	public function updateSiteSubscription( $subscriptionId , $data = array() )
	{
		$config = DiscussHelper::getConfig();
		$my = JFactory::getUser();

		if($config->get('main_allowguestsubscribe') || ($my->id && !$config->get('main_allowguestsubscribe')))
		{
			$date = ED::date();
			$subscriber	= DiscussHelper::getTable( 'Subscribe' );

			$subscriber->load($subscriptionId);
			$subscriber->userid		= $data['userid'];
			$subscriber->member		= $data['member'];
			$subscriber->cid		= $data['cid'];
			$subscriber->fullname	= $data['name'];
			$subscriber->interval	= $data['interval'];
			$subscriber->sent_out	= $date->toMySQL();
			return $subscriber->store();
		}

		return false;
	}

	public function updatePostSubscription($sid, $subscription_info)
	{
		$config = DiscussHelper::getConfig();
		$my = JFactory::getUser();

		if($config->get('main_allowguestsubscribe') || ($my->id && !$config->get('main_allowguestsubscribe')))
		{
			$db	= DiscussHelper::getDBO();

			$query  = 'DELETE FROM `#__discuss_subscription` '
					. ' WHERE `type` = ' . $db->Quote('post')
					. ' AND `cid` = ' . $db->Quote($subscription_info['cid'])
					. ' AND `email` = ' . $db->Quote($subscription_info['email'])
					. ' AND `id` != ' . $db->Quote($sid);

			$db->setQuery($query);
			$result = $db->query();

			if ($result) {
				$date = ED::date();
				$subscriber	= DiscussHelper::getTable( 'Subscribe' );

				$subscriber->load($sid);
				$subscriber->userid		= $subscription_info['userid'];
				$subscriber->member		= $subscription_info['member'];
				$subscriber->cid		= $subscription_info['cid'];
				$subscriber->fullname	= $subscription_info['name'];
				$subscriber->interval	= $subscription_info['interval'];
				$subscriber->sent_out	= $date->toMySQL();
				return $subscriber->store();
			}
		}

		return false;
	}


	public function isPostSubscribedEmail($subscription_info)
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

	public function isPostSubscribedUser($subscription_info)
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
	public function updateSubscriberEmail( $data, $isNew )
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

	public function isTagSubscribedUser($subscription_info)
	{
		$db	= DiscussHelper::getDBO();

		$query  = 'SELECT `id` FROM `#__discuss_subscription`';
		$query  .= ' WHERE `type` = ' . $db->Quote('tag');
		$query  .= ' AND (`userid` = ' . $db->Quote($subscription_info['userid']) . ' OR `email` = ' . $db->Quote($subscription_info['email']) . ')';
		$query  .= ' AND `cid` = ' . $db->Quote($subscription_info['cid']);

		$db->setQuery($query);
		$result = $db->loadAssoc();

		return $result;
	}

	public function isTagSubscribedEmail($subscription_info)
	{
		$db	= DiscussHelper::getDBO();

		$query  = 'SELECT `id` FROM `#__discuss_subscription`';
		$query  .= ' WHERE `type` = ' . $db->Quote('tag');
		$query  .= ' AND `email` = ' . $db->Quote($subscription_info['email']);
		$query  .= ' AND `cid` = ' . $db->Quote($subscription_info['cid']);

		$db->setQuery($query);
		$result = $db->loadAssoc();

		return $result;
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

	public function isSiteSubscribed( $type , $email , $cid )
	{
		$db	= DiscussHelper::getDBO();

		$query  = 'SELECT * FROM `#__discuss_subscription`';
		$query  .= ' WHERE `type` = ' . $db->Quote( $type );
		$query  .= ' AND `email` = ' . $db->Quote( $email );
		$query	.= ' AND `cid` = ' . $db->quote( $cid );

		$db->setQuery($query);

		$result 	= $db->loadObject();

		return $result;
	}

	/**
	 * Remove all subscriptions for particular post
	 *
	 * @since   4.0
	 * @access  public
	 * @param   string
	 * @return
	 */
	public function removeSubscription($postId)
	{
		$query  = 'DELETE FROM ' . $this->db->nameQuote('#__discuss_subscription')
				. ' WHERE ' . $this->db->nameQuote('cid') . ' = ' . $this->db->quote($postId)
				. ' AND ' . $this->db->nameQuote('type') . ' = ' . $this->db->quote('post');
		$this->db->setQuery($query);
		$this->db->query();
	}

	public function getSubscriptionBy($options = array())
	{
		$db = ED::db();

		$userId = isset($options['userid']) ? (int) $options['userid'] : null;
		$type = isset($options['type']) ? $options['type'] : 'post';
		$limit = isset($options['limit']) ? $options['limit'] : null;
		$limitstart = isset($options['limitstart']) ? $options['limitstart'] : null;
		$pagination = isset($options['pagination']) ? $options['pagination'] : true;

		$query  = '';

		$query	.= 'SELECT SQL_CALC_FOUND_ROWS a.*, b.`name`, b.`username`, c.`title` as `bname`';
		$query	.= '  FROM `#__discuss_subscription` a';
		$query	.= '    left join `#__users` b on a.`userid` = b.`id`';

		if ($type == 'category') {
			$query .= '    left join `#__discuss_category` c on a.`cid` = c.`id`';
		} else {
			$query .= '    left join `#__discuss_posts` c on a.`cid` = c.`id`';
		}

		$query	.= ' WHERE a.`type` = ' . $db->Quote($type);

		if ($userId) {
			$query	.= ' AND a.`userid` = ' . $userId;
		}

		$query	.= ' ORDER BY a.`created` DESC';

		$limitstart = is_null($limitstart) ? $this->getState('limitstart') : $limitstart;
		$limit = is_null($limit) ? $this->getState('limit') : $limit;

		if ($pagination) {
			$query .= ' LIMIT ' . $limitstart . ',' . $limit;
		}

		$db->setQuery($query);

		$result = $db->loadObjectList();

		if ($limit != DISCUSS_NO_LIMIT && $pagination) {
			// now lets get the row_count() for pagination.
			$cntQuery = "select FOUND_ROWS()";
			$db->setQuery($cntQuery);
			$this->_data = $db->loadResult();
			$this->_pagination = ED::pagination($this->_data, $limitstart, $limit);
		}

		return $result;
	}

	public function getSubscribeGDPR($options = array())
	{
		$db = ED::db();

		$userId = isset($options['userId']) ? $options['userId'] : null;
		$limit = isset($options['limit']) ? $options['limit'] : null;
		$exclude = isset($options['exclude']) ? $options['exclude'] : null;

		$query = 'SELECT a.*, b.`name`, b.`username`, c.`title` as `categoryTitle`, p.`title` as `postTitle`';
		$query .= ' FROM `#__discuss_subscription` a';
		$query .= ' LEFT JOIN `#__users` b on a.`userid` = b.`id`';
		$query .= ' LEFT JOIN `#__discuss_category` as c on a.`cid` = c.`id` and a.`type` = ' . $db->Quote('category');
		$query .= ' LEFT JOIN `#__discuss_posts` as p on a.`cid` = p.`id` and a.`type` = ' . $db->Quote('post');

		$query .= ' WHERE a.`userid` = ' . $db->Quote($userId);
		$query .= ' AND a.`type` != ' . $db->Quote('site');

		if ($exclude) {
			$query .= ' AND a.`id` NOT IN(' . implode(',', $exclude) . ')';
		}

		$query .= ' ORDER BY a.`type`, a.`created` DESC';
		$query .= ' LIMIT 0,' . $limit;

		$db->setQuery($query);

		$result = $db->loadObjectList();

		return $result;
	}

	/**
	 * Method to get the posts subscription graph data
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function getSubscriptionGraph($userId, $type)
	{
		// Get dbo
		$db = ED::db();

		// Get the past 6 months
		$dates = array();

		for ($i = 0 ; $i < 6; $i++) {

			$date = JFactory::getDate('-' . $i . ' month');
			$dates[] = $date->format('Y-m-d');
		}

		// Reverse the dates
		$dates = array_reverse($dates);

		// Prepare the main result
		$result = new stdClass();
		$result->dates = $dates;
		$result->count = array();

		$i = 0;
		foreach ($dates as $date) {

			$month = ED::date($date)->format('n');
			$year = ED::date($date)->format('Y');

			$query   = array();
			$query[] = 'SELECT COUNT(1) FROM ' . $db->quoteName('#__discuss_subscription');
			$query[] = 'WHERE MONTH(' . $db->quoteName('created') . ') =' . $db->Quote($month);
			$query[] = 'AND YEAR(' .$db->quoteName('created'). ') =' . $db->Quote($year);
			$query[] = 'AND ' . $db->quoteName('userid') . '=' . $db->Quote($userId);
			$query[] = 'AND ' . $db->quoteName('type') . '=' . $db->Quote($type);

			$query = implode(' ', $query);

			$db->setQuery($query);
			$total = $db->loadResult();

			$result->count[$i] = $total;

			$i++;
		}

		return $result;
	}

	/**
	 * Method to get total number of subscriptions of the user
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function getTotalSubscriptions($userId)
	{
		$db = ED::db();

		$query = array();
		$query[] = 'SELECT COUNT(*) total,';
		$query[] = 'SUM(case when ' . $db->quoteName('type') . '= ' . $db->Quote('post') . 'then 1 else 0 end) postCount,';
		$query[] = 'SUM(case when ' . $db->quoteName('type') . '= ' . $db->Quote('category') . 'then 1 else 0 end) categoryCount';
		$query[] = 'FROM ' . $db->quoteName('#__discuss_subscription');
		$query[] = 'WHERE' . $db->quoteName('userid') . ' = ' . $db->Quote($userId);
		$query[] = 'AND ' . $db->quoteName('type') . ' != ' . $db->Quote('site');
		$query[] = 'GROUP BY ' . $db->quoteName('userid');

		$query = implode(' ', $query);

		$db->setQuery($query);
		$total = $db->loadObject();

		return $total;
	}

	public function updateSubscriptionInterval($id, $interval)
	{
		$db = ED::db();
		$query = 'UPDATE ' . $db->nameQuote('#__discuss_subscription')
				. ' SET ' . $db->nameQuote('interval') . '=' . $db->Quote($interval)
				. ' WHERE ' . $db->nameQuote('id') . '=' . $db->Quote($id);

		$db->setQuery($query);

		if (!$db->query()) {
			return false;
		}

		return true;
	}

	public function updateSubscriptionSort($id, $sort)
	{
		$db = ED::db();
		$query = 'UPDATE ' . $db->nameQuote('#__discuss_subscription')
				. ' SET ' . $db->nameQuote('sort') . '=' . $db->Quote($sort)
				. ' WHERE ' . $db->nameQuote('id') . '=' . $db->Quote($id);

		$db->setQuery($query);

		if (!$db->query()) {
			return false;
		}

		return true;
	}

	public function updateSubscriptionCount($id, $count)
	{
		$db = ED::db();
		$query = 'UPDATE ' . $db->nameQuote('#__discuss_subscription')
				. ' SET ' . $db->nameQuote('count') . '=' . $db->Quote($count)
				. ' WHERE ' . $db->nameQuote('id') . '=' . $db->Quote($id);

		$db->setQuery($query);

		if (!$db->query()) {
			return false;
		}

		return true;
	}

	public function subscribeToggle($userId)
	{
		$db = ED::db();
		$query = 'UPDATE ' . $db->nameQuote('#__discuss_subscription')
				. ' SET ' . $db->nameQuote('state') . '= ABS(' . $db->quoteName('state') . ' - 1)'
				. ' WHERE ' . $db->nameQuote('userid') . '=' . $db->Quote($userId)
				. ' AND' . $db->nameQuote('type') . '=' . $db->Quote('site');

		$db->setQuery($query);

		if (!$db->query()) {
			return false;
		}

		return true;
	}

	/**
	 * Method to get total number of subscriptions of the user
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function getDigestSubscribers($now, $limit = 5)
	{
		$db = ED::db();

		$intervals = array('daily' => 1,
							'weekly' => 7,
							'monthly' => 30);

		$unions = array();

		$query = "select * from (";
		foreach($intervals as $key => $days) {
			$uQuery = " (select `email` from `#__discuss_subscription` where `state` = '1' and `type` = 'site' and `interval` = '$key' and `sent_out` <= date_sub('$now', INTERVAL $days DAY))";
			$uQuery .= " union ";
			$uQuery .= " (select `email` from `#__discuss_subscription` where `state` = '1' and `type` = 'category' and `interval` = '$key' and `sent_out` <= date_sub('$now', INTERVAL $days DAY))";

			$unions[] = $uQuery;
		}
		$query .= implode(" union ", $unions);
		$query .= ") as x limit $limit";


		$db->setQuery($query);

		$results = $db->loadColumn();

		return $results;
	}

	/**
	 * Method to get user's subscriptions based on the user's email
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function getDigestEmailSubscriptions($now, $email)
	{
		$db = ED::db();

		$intervals = array('daily' => 1,
							'weekly' => 7,
							'monthly' => 30);

		$unions = array();

		$query = "";
		foreach($intervals as $key => $days) {
			$uQuery = "select a.*, 'Site' as `subtitle`, 'site' as `subalias`, `count` from `#__discuss_subscription` as a";
			$uQuery .= " where a.`state` = '1' and a.`type` = 'site' and a.`interval` = '$key' and a.`email` = " . $db->Quote($email) . " and a.`sent_out` <= date_sub('$now', INTERVAL $days DAY)";
			$uQuery .= " union ";
			$uQuery .= "select a.*, c.`title` as `subtitle`, c.`alias` as `subalias`, `count` from `#__discuss_subscription` as a";
			$uQuery .= "    inner join `#__discuss_category` as c on a.cid = c.id";
			$uQuery .= " where a.`state` = '1' and a.`type` = 'category' and a.`interval` = '$key' and a.`email` = " . $db->Quote($email) . " and a.`sent_out` <= date_sub('$now', INTERVAL $days DAY)";

			$unions[] = $uQuery;
		}
		$query .= implode(" union ", $unions);

		$db->setQuery($query);

		$results = $db->loadObjectList();

		return $results;
	}


	public function updateDigestSentOut($subs)
	{
		if (! $subs) {
			// do nothing
			return true;
		}

		$db = ED::db();

		$now = ED::date()->toSql();

		$ids = array();
		foreach($subs as $sub) {
			$ids[] = $sub->id;
		}

		$query = "update `#__discuss_subscription` set `sent_out` = " . $db->Quote($now);
		$query .= " where `id` IN (" . implode(',', $ids) . ")";

		$db->setQuery($query);
		$db->query();

		return true;
	}


	/**
	 * Method to get user's subscriptions based on the user's email
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function getDigestPosts($subscriptions, $now)
	{
		$db = ED::db();

		if (! $subscriptions) {
			return array();
		}


		$unions = array();

		foreach($subscriptions as $sub) {

			$days = 1;
			if ($sub->interval == 'weekly') {
				$days = 7;
			} else if ($sub->interval == 'monthly') {
				$days = 30;
			}

			$uQuery = "(select " . $db->Quote($sub->type) . " as `subs_type`, " . $db->Quote($sub->cid) . " as `subs_cid`,";
			$uQuery .= " b.*, a.`has_polls` as `polls_cnt`, a.`num_fav` as `totalFavourites`, a.`num_replies`, a.`num_attachments` as attachments_cnt,";
			$uQuery	.= " a.`num_likes` as `likeCnt`, a.`sum_totalvote` as `VotedCnt`,";
			$uQuery	.=  " a.`replied` as `lastupdate`, a.vote as `total_vote_cnt`,";
			$uQuery	.= ' DATEDIFF('. $db->Quote($now) . ', a.`created`) as `noofdays`, ';
			$uQuery	.= ' DATEDIFF(' . $db->Quote($now) . ', a.`created`) as `daydiff`, TIMEDIFF(' . $db->Quote($now). ', a.`created`) as `timediff`,';
			$uQuery .= " 0 as `isVoted`,";
			$uQuery	.= " a.`post_status`, a.`post_type`,";
			$uQuery	.= " e.`title` AS `category`";
			$uQuery .= " from " . $db->nameQuote('#__discuss_thread') . " as a";
			$uQuery .= "     INNER JOIN " . $db->nameQuote('#__discuss_posts') . " as b on a.post_id = b.id";
			$uQuery	.= "     INNER JOIN " . $db->nameQuote('#__discuss_category') . " AS e ON a.`category_id` = e.`id`";
			$uQuery .= " WHERE a.`published` = " . $db->Quote('1');

			// we do not show private posts to subscribers. #481
			$uQuery .= " and a.`private` = " . $db->Quote('0');

			$uQuery .= " and a.`created` >= " . $db->Quote($sub->sent_out) . " and a.created <= " . $db->Quote($now);
			if ($sub->type == 'category') {
				$uQuery .= " and a.`category_id` = " . $db->Quote($sub->cid);
			}


			// category ACL:
			$catOptions = array();
			$catOptions['idOnly'] = true;
			$catOptions['includeChilds'] = false;
			$catOptions['useUserId'] = $sub->userid;

			$catAccessSQL = ED::category()->genCategoryAccessSQL('a.category_id', $catOptions);
			$uQuery .= " and " . $catAccessSQL;

			$uQuery .= " ORDER BY a.id desc";

			// the limit and ordering should respect from subscription.
			if ($sub->count) {
				$uQuery .= " LIMIT " . $sub->count . ")";
			} else {
				$uQuery .= " LIMIT 10)";
			}

			// echo $uQuery;
			// echo '<br><br><br>';

			$unions[] = $uQuery;
		}

		$query = implode(" UNION ", $unions);
		// echo $query;exit;


		$db->setQuery($query);

		$results = $db->loadObjectList();

		return $results;
	}


	/**
	 * Method to get user's subscriptions based on the user's email
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function getDigestReplies($subscriptions, $now)
	{
		$db = ED::db();

		if (! $subscriptions) {
			return array();
		}

		$unions = array();

		foreach($subscriptions as $sub) {

			// $uQuery = "(select " . $db->Quote($sub->type) . " as `subs_type`, " . $db->Quote($sub->cid) . " as `subs_cid`,";
			$uQuery = "(select";
			$uQuery .= " a.*, 0 as `polls_cnt`, 0 as `totalFavourites`, 0 as `num_replies`, 0 as attachments_cnt, 0 as `likeCnt`, 0 as `VotedCnt`, b.`created` as `lastupdate`, 0 `total_vote_cnt`,";
			$uQuery	.= ' DATEDIFF('. $db->Quote($now) . ', a.`created`) as `noofdays`, ';
			$uQuery	.= ' DATEDIFF(' . $db->Quote($now) . ', a.`created`) as `daydiff`, TIMEDIFF(' . $db->Quote($now). ', a.`created`) as `timediff`,';
			$uQuery .= " 0 as `isVoted`,";
			$uQuery	.= " a.`post_status`, a.`post_type`,";
			$uQuery	.= " e.`title` AS `category`";
			$uQuery .= " from " . $db->nameQuote('#__discuss_posts') . " as a";
			// we only want to process replies from public post #481
			$uQuery .= "     INNER JOIN " . $db->nameQuote('#__discuss_thread') . " as b on a.thread_id = b.id and b.`private` = 0";
			$uQuery	.= "     INNER JOIN " . $db->nameQuote('#__discuss_category') . " AS e ON b.`category_id` = e.`id`";
			$uQuery .= " WHERE a.`published` = " . $db->Quote('1');
			$uQuery .= " and a.`parent_id` > 0";
			$uQuery .= " and a.`created` >= " . $db->Quote($sub->sent_out) . " and a.created <= " . $db->Quote($now);
			if ($sub->type == 'category') {
				$uQuery .= " and a.`category_id` = " . $db->Quote($sub->cid);
			}

			// category ACL:
			$catOptions = array();
			$catOptions['idOnly'] = true;
			$catOptions['includeChilds'] = false;
			$catOptions['useUserId'] = $sub->userid;

			$catAccessSQL = ED::category()->genCategoryAccessSQL('b.category_id', $catOptions, DISCUSS_CATEGORY_ACL_ACTION_VIEWREPLY);
			$uQuery .= " and " . $catAccessSQL;


			$uQuery .= " ORDER BY a.id desc";

			// the limit and ordering should respect from subscription.
			if ($sub->count) {
				$uQuery .= " LIMIT " . $sub->count . ")";
			} else {
				$uQuery .= " LIMIT 10)";
			}

			// echo $uQuery;
			// echo '<br><br><br>';
			// exit;

			$unions[] = $uQuery;
		}

		$query = implode(" UNION ", $unions);

		$db->setQuery($query);

		$results = $db->loadObjectList();

		return $results;
	}

	/**
	 * Method to get user's subscriptions based on the user's email
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function getDigestComments($subscriptions, $now)
	{
		$db = ED::db();

		if (! $subscriptions) {
			return array();
		}

		$unions = array();

		foreach($subscriptions as $sub) {

			$uQuery = "(select a.`id`, a.`comment`, a.`name`, a.`email`, a.`post_id`, c.`post_id` as `question_id`";
			$uQuery .= " FROM `#__discuss_comments` as a";
			$uQuery .= " INNER JOIN `#__discuss_posts` as b on a.`post_id` = b.`id`";
			// we only want to process comments from replies that belong to public posts. #481
			$uQuery .= " INNER JOIN `#__discuss_thread` as c on b.`thread_id` = c.`id` and c.`private` = 0";
			$uQuery .= " INNER JOIN `#__discuss_category` as d on c.`category_id` = d.`id`";
			$uQuery .= " WHERE a.`published` = " . $db->Quote('1');
			$uQuery .= " and a.`created` >= " . $db->Quote($sub->sent_out) . " and a.created <= " . $db->Quote($now);

			if ($sub->type == 'category') {
				$uQuery .= " and c.`category_id` = " . $db->Quote($sub->cid);
			}


			// category ACL:
			$catOptions = array();
			$catOptions['idOnly'] = true;
			$catOptions['includeChilds'] = false;
			$catOptions['useUserId'] = $sub->userid;

			$catAccessSQL = ED::category()->genCategoryAccessSQL('b.category_id', $catOptions);
			$uQuery .= " and " . $catAccessSQL;


			$uQuery .= " ORDER BY a.id desc";

			// the limit and ordering should respect from subscription.
			if ($sub->count) {
				$uQuery .= " LIMIT " . $sub->count . ")";
			} else {
				$uQuery .= " LIMIT 10)";
			}

			// echo $uQuery;
			// echo '<br><br><br>';
			// exit;

			$unions[] = $uQuery;
		}

		$query = implode(" UNION ", $unions);

		$db->setQuery($query);

		$results = $db->loadObjectList();

		return $results;
	}


	/**
	 * Performs checking if the interval all set to instant
	 *
	 * @since   4.0
	 * @access  public
	 * @param   string
	 * @return
	 */
	public function allInstantSubscription($userId)
	{
		$db = ED::db();

		$query  = '';

		$query	.= 'SELECT count(1)';
		$query	.= '  FROM `#__discuss_subscription` a';
		$query	.= '    left join `#__users` b on a.`userid` = b.`id`';
		$query .= '    left join `#__discuss_category` c on a.`cid` = c.`id`';

		$query	.= ' WHERE a.`interval` != ' . $db->Quote('instant') ;

		if ($userId) {
			$query	.= ' AND a.`userid` = ' . $userId;
		}

		$db->setQuery($query);

		$result = $db->loadResult();

		if ($result) {
			return false;
		}

		return true;
	}
}
