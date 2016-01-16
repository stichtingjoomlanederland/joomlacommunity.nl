<?php
/**
* @package		Komento
* @copyright	Copyright (C) 2012 Stack Ideas Private Limited. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* Komento is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Restricted access');

require_once( dirname( __FILE__ ) . DIRECTORY_SEPARATOR . 'parent.php' );

class KomentoModelAdminMailq extends KomentoParentModel
{
	private $_pagination	= '';
	private $_total			= '';
	private $items			= array();

	public function __construct()
	{
		parent::__construct();

		$app		= JFactory::getApplication();

		$limit		= $app->getUserStateFromRequest( 'com_komento.mailq.limit' , 'limit' , $app->getCfg( 'list_limit') , 'int' );
		$limitstart	= $app->getUserStateFromRequest( 'com_komento.mailq.limitstart' , 'limitstart' , 0 , 'int' );

		$this->setState('limit', $limit);
		$this->setState('limitstart', $limitstart);
	}

	private function getTotal()
	{
		// Lets load the content if it doesn't already exist
		if (empty($this->_total))
		{
			$query = $this->buildQuery();
			$this->_total = $this->_getListCount($query);
		}

		return $this->_total;
	}

	private function buildQuery()
	{
		$db			= Komento::getDBO();
		$query		= 'SELECT * FROM ' . $db->nameQuote( '#__komento_mailq' );
		$query		.= ' ORDER BY ' . $db->nameQuote( 'created' ) . ' DESC';

		return $query;
	}

	public function getItems()
	{
		if( empty( $this->items) )
		{
			$query			= $this->buildQuery();
			$this->items	= $this->_getList( $query , $this->getState( 'limitstart' ) , $this->getState( 'limit' ) );
		}

		return $this->items;
	}

	public function purge()
	{
		$db		= Komento::getDBO();

		$query	= 'DELETE FROM ' . $db->nameQuote( '#__komento_mailq' );

		$db->setQuery( $query );
		$db->Query();
	}

	public function getPagination()
	{
		if (empty($this->_pagination))
		{
			jimport('joomla.html.pagination');
			$this->_pagination = new JPagination( $this->_total, $this->getState('limitstart'), $this->getState('limit') );
		}

		return $this->_pagination;
	}
}
