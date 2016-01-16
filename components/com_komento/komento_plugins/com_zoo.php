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

// include_once( JPATH_ROOT . DIRECTORY_SEPARATOR . 'includes' . DIRECTORY_SEPARATOR . 'application.php' );

class KomentoComzoo extends KomentoExtension
{
	public $_item = null;
	public $_map = array(
		'id'			=> 'id',
		'title'			=> 'name',
		'hits'			=> 'hits',
		'created_by'	=> 'created_by',
		'permalink'		=> 'permalink'
		);

	public function __construct( $component )
	{
		$this->addFile( JPATH_ADMINISTRATOR . DIRECTORY_SEPARATOR . 'components' . DIRECTORY_SEPARATOR . 'com_zoo' . DIRECTORY_SEPARATOR . 'config.php' );

		parent::__construct( $component );
	}

	public function load( $cid )
	{
		static $instances = null;

		// @task: If we don't have the id, we really can't do anything here.
		if( !$cid )
		{
			return false;
		}

		if( is_null($instances) )
		{
			$instances = array();
		}

		if( !array_key_exists($cid, $instances) )
		{
			$zooApp = App::getInstance('zoo');
			$this->_item = $zooApp->table->item->get($cid);

			if( !$this->_item)
			{
				return $this->onLoadArticleError( $cid );
			}

			$this->_item->permalink	= $zooApp->route->item($this->_item);
			$this->_item->permalink = $this->prepareLink( $this->_item->permalink );

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
			$query = 'SELECT DISTINCT `item_id` FROM ' . $db->nameQuote( '#__zoo_category_item' ) . ' ORDER BY `item_id`';
		}
		else
		{
			if( is_array( $categories ) )
			{
				$categories = implode( ',', $categories );
			}

			$query = 'SELECT DISTINCT `item_id` FROM ' . $db->nameQuote( '#__zoo_category_item' ) . ' WHERE `category_id` IN (' . $categories . ') ORDER BY `item_id`';
		}

		$db->setQuery( $query );
		return $db->loadResultArray();
	}

	public function getCategories()
	{
		$db		= Komento::getDBO();
		$query	= 'SELECT c.id, c.name AS title, c.parent AS parent_id, c.name, c.parent'
				. ' FROM `#__zoo_category` as c USE INDEX (APPLICATIONID_ID_INDEX)'
				. ' ORDER BY c.ordering';
		$db->setQuery( $query );
		$categories	= $db->loadObjectList();

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
		$views	= array('frontpage', 'category', 'alphaindex', 'tag');

		if( in_array(JRequest::getCmd('view'), $views) )
		{
			return true;
		}

		if( in_array(JRequest::getCmd('task'), $views) )
		{
			return true;
		}

		return false;
	}

	public function isEntryView()
	{
		$task		= JRequest::getCmd('task');
		$view 		= JRequest::getVar( 'view' );

		if( $task == 'item' || $view == 'item' )
		{
			return true;
		}
		return false;
	}

	public function onExecute( &$article, $html, $view, $options = array() )
	{
		if( $view == 'listing' )
		{
			$article->text	.= $html;
			return $html;
		}

		if( $view == 'entry' )
		{
			$article->text	.= $html;
			return $html;
		}
	}

	public function getCategoryId()
	{
		return $this->_item->getParams()->get('config.primary_category', null);
	}

	public function getContext()
	{
		return 'com_zoo';
	}

	public function getAuthorName()
	{
		return $this->_item->created_by_alias ? $this->_item->created_by_alias : $this->_item->author->name;
	}
}
