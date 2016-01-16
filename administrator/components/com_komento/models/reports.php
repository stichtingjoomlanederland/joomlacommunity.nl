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

class KomentoModelAdminReports extends KomentoParentModel
{
	public $_data;
	public $_total;
	public $_pagination;

	public $order;
	public $order_dir;
	public $limit;
	public $limitstart;
	public $filter_publish;
	public $filter_component;
	public $search;

	function __construct()
	{
		$mainframe				= JFactory::getApplication();

		$db						= Komento::getDBO();

		$this->limit			= $mainframe->getUserStateFromRequest( 'com_komento.reports.limit', 'limit', $mainframe->getCfg('list_limit'), 'int');
		$this->limitstart		= $mainframe->getUserStateFromRequest( 'com_komento.reports.limitstart', 'limitstart', 0, 'int' );
		$this->filter_publish 	= $mainframe->getUserStateFromRequest( 'com_komento.reports.filter_publish', 'filter_publish', '*', 'string' );
		$this->filter_component	= $mainframe->getUserStateFromRequest( 'com_komento.reports.filter_component', 'filter_component', '*', 'string' );
		$this->order			= $mainframe->getUserStateFromRequest( 'com_komento.reports.filter_order', 'filter_order', 'created', 'cmd' );
		$this->order_dir		= $mainframe->getUserStateFromRequest( 'com_komento.reports.filter_order_Dir',	'filter_order_Dir',	'DESC', 'word' );
		$this->search 			= $mainframe->getUserStateFromRequest( 'com_komento.reports.search', 'search', '', 'string' );

		Komento::import( 'helper', 'string' );
		$this->search 			= KomentoStringHelper::escape( trim( JString::strtolower( $this->search ) ) );

		parent::__construct();
	}

	function getData()
	{
		// Lets load the content ifit doesn't already exist
		if( empty( $this->_data ) )
		{
			$sql = $this->buildQuery();

			$this->_data = $sql->loadObjectList();
		}

		return $this->_data;
	}

	function buildQuery()
	{
		$sql = Komento::getSql();

		$sql->select( '#__komento_comments', 'a' )
			->column( 'a.*' )
			->column( 'b.comment_id', 'reports', 'count' )
			->rightjoin( '#__komento_actions', 'b' )
			->on( 'a.id', 'b.comment_id' )
			->where( 'type', 'report' );

		// filter by component
		if( $this->filter_component != '*' )
		{
			$sql->where( 'component', $this->filter_component );
		}

		// filter by publish state
		if( $this->filter_publish != '*' )
		{
			$sql->where( 'published', $this->filter_publish );
		}

		if( $this->search )
		{
			$sql->where( 'LOWER(`comment`)', '%' . $this->search . '%', 'LIKE' );
		}

		$sql->group( 'comment_id' )
			->order( $this->order, $this->order_dir )
			->limit( $this->limitstart, $this->limit );

		return $sql;
	}

	function getPagination()
	{
		// Lets load the content ifit doesn't already exist
		if(empty($this->_pagination))
		{
			jimport('joomla.html.pagination');
			$this->_pagination = new JPagination( $this->getTotal(), $this->limitstart, $this->limit );
		}

		return $this->_pagination;
	}

	function getTotal()
	{
		// Lets load the content if it doesn't already exist
		if(empty($this->_total))
		{
			$sql = Komento::getSql();

			$sql->select( '#__komento_comments', 'a' )
				->column( 'a.id', 'id', 'count distinct' )
				->rightjoin( '#__komento_actions', 'b' )
				->on( 'a.id', 'b.comment_id' )
				->where( 'b.type', 'report' );

			$this->_total = $sql->loadResult();
		}

		return $this->_total;
	}
}
