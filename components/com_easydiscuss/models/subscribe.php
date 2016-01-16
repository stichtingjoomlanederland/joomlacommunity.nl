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

class EasyDiscussModelSubscribe extends EasyDiscussModel
{
	/**
	 * Post total
	 *
	 * @var integer
	 */
	var $_total	= null;

	/**
	 * Pagination object
	 *
	 * @var object
	 */
	var $_pagination = null;

	function __construct()
	{
		parent::__construct();


		$mainframe	= JFactory::getApplication();

		$limit			= $mainframe->getUserStateFromRequest( 'com_easydiscuss.posts.limit', 'limit', DiscussHelper::getListLimit(), 'int');
		$limitstart		= JRequest::getVar('limitstart', 0, '', 'int');

		$this->setState('limit', $limit);
		$this->setState('limitstart', $limitstart);
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
	
	/**
	 * Determines if the particular email is already subscribed in the system.
	 *
	 * @since	3.0
	 * @param	string	Type of subscription.
	 * @param	string	The email address.
	 * @param	int		Unique id.
	 */
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

	function isTagSubscribedEmail($subscription_info)
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

	function isTagSubscribedUser($subscription_info)
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

	function addSubscription($subscription_info)
	{
		$config	= DiscussHelper::getConfig();
		$my		= JFactory::getUser();

		if($config->get('main_allowguestsubscribe') || ($my->id && !$config->get('main_allowguestsubscribe')))
		{
			$date		= DiscussHelper::getDate();
			$now		= $date->toMySQL();
			$subscriber	= DiscussHelper::getTable( 'Subscribe' );

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

	/**
	 * Updates an existing subscription.
	 *
	 * @since	3.0
	 * @access	public
	 */
	function updateSiteSubscription( $subscriptionId , $data = array() )
	{
		$config = DiscussHelper::getConfig();
		$my = JFactory::getUser();

		if($config->get('main_allowguestsubscribe') || ($my->id && !$config->get('main_allowguestsubscribe')))
		{
			$date		= DiscussHelper::getDate();
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

	function updatePostSubscription($sid, $subscription_info)
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

			if($result)
			{
				$date		= DiscussHelper::getDate();
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

	function getCategorySubscribers($postid='')
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

	function getSiteSubscribers($interval='daily', $now='', $categoryId = null)
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

			$db->setQuery($query);

			$result = $db->loadObjectList();

		}

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

	function getCreatedPostByInterval($sent_out, $now='')
	{
		$db = DiscussHelper::getDBO();

		if(empty($now))
		{
			$date 	= DiscussHelper::getDate();
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

	function isMySubscription( $userid, $type, $subId )
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
		$db		= DiscussHelper::getDBO();
		$date	= DiscussHelper::getDate();
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
