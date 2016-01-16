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

// No direct access
defined('_JEXEC') or die('Restricted access');

require_once( dirname( __FILE__ ) . DIRECTORY_SEPARATOR . 'abstract.php' );

class KomentoComredshop extends KomentoExtension
{
	public $_item;
	public $_map = array(
		'id'			=> 'product_id',
		'title'			=> 'product_name',
		'hits'			=> 'visited',
		'created_by'	=> 'created_by',
		'catid'			=> 'category_id',
		'permalink'		=> 'permalink'
		);

	public function __construct( $component )
	{
		parent::__construct( $component );
	}

	public function load( $cid )
	{
		static $instances = array();

		if( !isset( $instances[$cid] ) )
		{
			$db		= Komento::getDBO();
			$query	= 'SELECT p.*, c.category_id, c.category_name, c.category_back_full_image, c.category_full_image, m.manufacturer_name, pcx.ordering '
					. ' FROM `#__redshop_product` AS p'
					. ' LEFT JOIN `#__redshop_product_category_xref` AS pcx ON pcx.product_id = p.product_id'
					. ' LEFT JOIN `#__redshop_manufacturer` AS m ON m.manufacturer_id = p.manufacturer_id'
					. ' LEFT JOIN `#__redshop_category` AS c ON c.category_id = pcx.category_id'
					. ' WHERE p.product_id = ' . $db->quote( $cid );
					//. ' AND pcx.category_id = ' . $db->quote( JRequest::getInt('cid', 0) );
			$db->setQuery( $query );

			if( !$this->_item = $db->loadObject() )
			{
				return $this->onLoadArticleError( $cid );
			}

			$link = 'index.php?opiton=com_redshop&view=product&pid=' . $this->_item->product_id . '&cid=' . $this->_item->category_id;
			$this->_item->permalink = $this->prepareLink( $link );

			$instances[$cid] = $this->_item;
		}

		$this->_item = $instances[$cid];

		return $this;
	}

	public function getContentIds( $categories = '' )
	{
		$db		= Komento::getDBO();
		$query = '';

		if( empty( $categories ) )
		{
			$query = 'SELECT `product_id` FROM ' . $db->nameQuote( '#__redshop_product_category_xref' ) . ' ORDER BY `product_id`';
		}
		else
		{
			if( is_array( $categories ) )
			{
				$categories = implode( ',', $categories );
			}

			$query = 'SELECT `product_id` FROM ' . $db->nameQuote( '#__redshop_product_category_xref' ) . ' WHERE `category_id` IN (' . $categories . ') ORDER BY `product_id`';
		}

		$db->setQuery( $query );
		return $db->loadResultArray();
	}

	public function getCategories()
	{
		$db		= Komento::getDBO();
		$query	= 'SELECT c.category_id, cx.category_child_id, cx.category_child_id AS id, cx.category_parent_id,'
				. ' cx.category_parent_id AS parent_id, c.category_name, c.category_name AS title,'
				. ' c.category_description, c.published,ordering,'
				. ' c.category_name AS name, cx.category_parent_id AS parent'
				. ' FROM `#__redshop_category` AS c,'
				. ' `#__redshop_category_xref` AS cx'
				. ' WHERE c.category_id = cx.category_child_id'
				. ' ORDER BY c.ordering';

		$db->setQuery( $query );
		$categories = $db->loadObjectList();

		$children = array();

		foreach( $categories as $row )
		{
			$pt		= $row->parent_id;
			$list	= @$children[$pt] ? $children[$pt] : array();
			$list[] = $row;
			$children[$pt] = $list;
		}

		$categories	= JHTML::_( 'menu.treerecurse', 0, '', array(), $children, 9999, 0, 0 );

		return $categories;
	}

	public function isListingView()
	{
		$views = array('featured', 'category', 'categories', 'archive' );

		return in_array(JRequest::getCmd('view'), $views);
	}

	public function isEntryView()
	{
		return JRequest::getCmd('view') == 'product';
	}

	public function onExecute( &$article, $html, $view, $options = array() )
	{
		if( $view == 'listing' )
		{
			$article->introtext	.= $html;
			$article = $article->introtext;
			return $html;
		}

		if( $view == 'entry' )
		{
			$article->text	.= $html;
			$article = $article->text;
			return $html;
		}
	}

	public function getEventTrigger()
	{
		return 'onAfterDisplayProduct';
	}

	public function onBeforeLoad( $eventTrigger, $context, &$article, &$params, &$page, &$options )
	{
		// @task: variable type check
		if( !is_string( $article ) || !is_object( $params ) || !is_object( $page ) )
		{
			return false;
		}

		// @task: prepare everything else before execute
		$obj		= new stdClass;
		$obj->text	= $article;
		$obj->introtext	= '';
		$obj->id	= JRequest::getInt( 'pid' );
		$article	= $obj;

		$this->_item = $page;

		return true;
	}

	public function onRollBack( $eventTrigger, $context, &$article, &$params, &$page, &$options )
	{
		$article = $article->text;

		return true;
	}
}
