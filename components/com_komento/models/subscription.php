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

class KomentoModelSubscription extends KomentoModel
{
	public $_total		= null;
	public $_pagination	= null;
	public $_data		= null;

	public function __construct()
	{
		parent::__construct();

		$mainframe	= JFactory::getApplication();

		$limit		= $mainframe->getUserStateFromRequest( 'com_komento.subscribers.limit', 'limit', $mainframe->getCfg('list_limit'), 'int');
		$limitstart = $mainframe->getUserStateFromRequest( 'com_komento.subscribers.limitstart', 'limitstart', 0, 'int' );
		$this->setState('limit', $limit);
		$this->setState('limitstart', $limitstart);
	}

	public function checkSubscriptionExist($component, $cid, $userid = 0, $email = '', $type = 'comment')
	{
		$sql = Komento::getSql();

		$sql->select( '#__komento_subscription' )
			->column( 'published' )
			->where( 'component', $component )
			->where( 'cid', $cid )
			->where( 'type', $type );

		if( $userid )
		{
			$sql->where( 'userid', $userid );
		}
		else
		{
			$sql->where( 'email', $email );
		}

		// $result = null ( no subscription )
		// $result = 0 ( subscribed but not confirmed )
		// $result = 1 ( subscribed and confirmed )
		$result = $sql->loadResult();

		return $result;
	}

	public function getSubscribers($component, $cid)
	{
		$sql = Komento::getSql();

		$sql->select( '#__komento_subscription' )
			->column( 'fullname' )
			->column( 'email' )
			->where( 'component', $component )
			->where( 'cid', $cid )
			->where( 'published', 1 );

		return $sql->loadObjectList();
	}

	public function unsubscribe($component, $cid, $userid, $email = '', $type = 'comment')
	{
		$sql = Komento::getSql();

		$sql->delete( '#__komento_subscription' )
			->where( 'component', $component )
			->where( 'cid', $cid )
			->where( 'type', $type );

		if( $userid )
		{
			$sql->where( 'userid', $userid );
		}
		else
		{
			$sql->where( 'email', $email );
		}

		$state = $sql->query();

		if( !$state )
		{
			$this->setError( $sql->db->getErrorMsg() );
			return false;
		}

		return true;
	}

	public function confirmSubscription( $id )
	{
		if( !$id )
		{
			return false;
		}

		$subscriptionTable = Komento::getTable( 'subscription' );
		$subscriptionTable->load( $id );
		$subscriptionTable->published = 1;
		return $subscriptionTable->store();
	}

	public function remove( $subscribers = array() )
	{
		if( $subscribers == null )
		{
			return false;
		}

		if( !is_array( $subscribers ) )
		{
			$subscribers = array($subscribers);
		}

		if( count( $subscribers ) < 1 )
		{
			return false;
		}

		$sql = Komento::getSql();

		$sql->delete( '#__komento_subscription' )
			->where( 'id', $subscribers, 'in' );

		$state = $sql->query();

		if( !$state )
		{
			$this->setError( $sql->db->getErrorMsg() );
			return false;
		}

		return true;
	}

	public function getUniqueComponents()
	{
		$sql = Komento::getSql();

		$sql->select( '#__komento_subscription' )
			->column( 'component', 'component', 'distinct' )
			->order( 'component' );

		return $sql->loadResultArray();
	}

	public function getData()
	{
		if( empty( $this->_data ) )
		{
			$sql = $this->buildQuery();

			$sql->limit( $this->getState( 'limitstart' ), $this->getState( 'limit' ) );

			$this->_data = $sql->loadObjectList();
		}

		return $this->_data;
	}

	public function getPagination()
	{
		// Lets load the content ifit doesn't already exist
		if(empty($this->_pagination))
		{
			jimport('joomla.html.pagination');
			$this->_pagination = new JPagination( $this->getTotal(), $this->getState('limitstart'), $this->getState('limit') );
		}

		return $this->_pagination;
	}

	public function getTotal()
	{
		if( empty( $this->_total ) )
		{
			$sql = $this->buildQuery();

			$query = $sql->getTotalSql();

			$sql->db->setQuery( $query );
			$this->_total = $sql->db->loadResult();
		}

		return $this->_total;
	}

	public function buildQuery()
	{
		$mainframe	= JFactory::getApplication();

		$filter_component	= $mainframe->getUserStateFromRequest( 'com_komento.subscribers.filter_component', 'filter_component', '*', 'string' );
		$filter_type		= $mainframe->getUserStateFromRequest( 'com_komento.subscribers.filter_type', 'filter_type', '*', 'string' );
		$filter_order		= $mainframe->getUserStateFromRequest( 'com_komento.subscribers.filter_order', 'filter_order', 'created', 'cmd' );
		$filter_order_Dir	= $mainframe->getUserStateFromRequest( 'com_komento.subscribers.filter_order_Dir',	'filter_order_Dir',	'DESC', 'word' );

		$sql = Komento::getSql();

		$sql->select( '#__komento_subscription' );

		if( $filter_component != '*' )
		{
			$sql->where( 'component', $filter_component );
		}

		if( $filter_type != '*' )
		{
			$sql->where( 'type', $filter_type );
		}

		$sql->order( $filter_order, $filter_order_Dir );

		return $sql;
	}
}
