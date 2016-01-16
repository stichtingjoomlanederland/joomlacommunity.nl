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

class KomentoComAceshop extends KomentoExtension
{
	public $_item;
	public $_map = array(
		'id'			=> 'product_id',
		'title'			=> 'name',
		'hits'			=> 'viewed',
		'created_by'	=> 'created_by',
		'catid'			=> 'category_id',
		'permalink'		=> 'permalink'
		);

	public function __construct( $component )
	{
		$this->addFile( JPATH_ROOT.'/components/com_aceshop/aceshop/aceshop.php' );

		parent::__construct( $component );
	}

	public function load( $cid )
	{
		static $instances = array();

		if( !isset( $instances[$cid] ) )
		{
			$this->_item = (object) AceShop::get('db')->getRecord($cid);

			if(!$this->_item)
			{
				return $this->onLoadArticleError( $cid );
			}

			$this->_item->category_id = AceShop::get('db')->getProductCategoryId($cid);

			$link = 'index.php?opiton=com_aceshop&route=product/product&product_id=' . $this->_item->product_id . '&path=' . $this->_item->category_id;
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
			$query = 'SELECT `product_id` FROM ' . $db->nameQuote( '#__aceshop_product_to_category' ) . ' ORDER BY `product_id`';
		}
		else
		{
			if( is_array( $categories ) )
			{
				$categories = implode( ',', $categories );
			}

			$query = 'SELECT `product_id` FROM ' . $db->nameQuote( '#__aceshop_product_to_category' ) . ' WHERE `category_id` IN (' . $categories . ') ORDER BY `product_id`';
		}

		$db->setQuery( $query );
		return $db->loadResultArray();
	}

	public function getCategories()
	{
		$db		= Komento::getDBO();
		$query	= 'SELECT c.category_id AS id, cd.name, cd.name AS title, c.parent_id, c.parent_id AS parent'
				. ' FROM `#__aceshop_category` AS c,'
				. ' `#__aceshop_category_description` AS cd'
				. ' WHERE c.category_id = cd.category_id'
				. ' ORDER BY c.sort_order';

		$db->setQuery( $query );
		$rows = $db->loadObjectList();

		$categories	= array();

		if( count($rows) )
		{
			$children = array();

			foreach ($rows as $v )
			{
				$pt		= $v->parent_id;
				$list	= @$children[$pt] ? $children[$pt] : array();
				array_push( $list, $v );
				$children[$pt] = $list;
			}

			$treelist	= JHTML::_('menu.treerecurse', 0, '', array(), $children, 9999, 0, 0 );

			foreach ($treelist as &$row)
			{
				$category->title = $row->treename;
				$categories[] = $row;
			}
		}

		return $categories;
	}

	public function isListingView()
	{
		$state = ((JRequest::getCmd('view') == 'category') || (JRequest::getCmd('route') == 'product/category'));

		return $state;
	}

	public function isEntryView()
	{
		$state = ((JRequest::getCmd('view') == 'product') || (JRequest::getString('route') == 'product/product'));

		return $state;
	}

	public function onExecute( &$article, $html, $view, $options = array() )
	{
		if( $view == 'listing' )
		{
			return $html;
		}

		if( $view == 'entry' )
		{
			$article->text .= $html;
			return $html;
		}
	}

	public function onRollBack( $eventTrigger, $context, &$article, &$params, &$page, &$options )
	{
		$article = $article->text;

		return true;
	}
}
