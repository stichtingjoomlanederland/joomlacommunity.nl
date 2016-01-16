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

class KomentoComContentBuilder extends KomentoExtension
{
	public $_item;
	public $_map = array(
		'id'			=> 'id',
		'title'			=> 'title',
		'hits'			=> 'hits',
		'created_by'	=> 'created_by',
		'catid'			=> 'catid'
		);

	public function __construct( $component )
	{
		$this->addFile( JPATH_ROOT . DIRECTORY_SEPARATOR . 'components' . DIRECTORY_SEPARATOR . 'com_content' . DIRECTORY_SEPARATOR . 'helpers' . DIRECTORY_SEPARATOR .'route.php' );

		parent::__construct( $component );
	}

	public function getEventTrigger()
	{
		$entryTrigger = ( Komento::joomlaVersion() > '1.5' ) ? 'onContentAfterDisplay' : 'onAfterDisplayContent';

		return $entryTrigger;
	}

	public function load( $cid )
	{
		static $instances = array();

		if( !isset( $instances[$cid] ) )
		{
			$db		= Komento::getDBO();
			$query	= 'SELECT a.id, a.title, a.alias, a.catid, a.created_by, a.created_by_alias, a.hits,' //a.attribs
					. ' c.title AS category_title, c.alias AS category_alias,'
					. ' u.name AS author,'
					. ' parent.id AS parent_id, parent.alias AS parent_alias'
					. ' FROM ' . $db->nameQuote( '#__content') . ' AS a'
					. ' LEFT JOIN ' . $db->nameQuote( '#__categories' ) . ' AS c ON c.id = a.catid'
					. ' LEFT JOIN ' . $db->nameQuote( '#__users') . ' AS u ON u.id = a.created_by'
					. ' LEFT JOIN ' . $db->nameQuote( '#__categories') . ' AS parent ON parent.id = c.parent_id'
					. ' WHERE a.id = ' . $db->quote( (int) $cid );
			$db->setQuery( $query );

			if( !$this->_item = $db->loadObject() )
			{
				return $this->onLoadArticleError( $cid );
			}

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
			$query = 'SELECT `id` FROM ' . $db->nameQuote( '#__content' ) . ' ORDER BY `id`';
		}
		else
		{
			if( is_array( $categories ) )
			{
				$categories = implode( ',', $categories );
			}

			$query = 'SELECT `id` FROM ' . $db->nameQuote( '#__content' ) . ' WHERE `catid` IN (' . $categories . ') ORDER BY `id`';
		}

		$db->setQuery( $query );
		return $db->loadResultArray();
	}

	public function getCategories()
	{
		if( !is_array( $selected ) )
		{
			$selected	= explode( ',' , $selected );
		}

		$db		= Komento::getDBO();
		$query	= 'SELECT a.id, a.title, a.level, a.parent_id'
				. ' FROM `#__categories` AS a'
				. ' WHERE a.extension = ' . $db->quote( 'com_content' )
				. ' AND a.parent_id > 0'
				. ' ORDER BY a.lft';

		if( Komento::joomlaVersion() == '1.5' )
		{
			$query	= 'SELECT a.id, a.title'
				. ' FROM `#__categories` AS a'
				. ' ORDER BY a.ordering';
		}

		$db->setQuery( $query );
		$categories = $db->loadObjectList();

		if( Komento::joomlaVersion() >= '1.6' )
		{
			foreach( $categories as &$row )
			{
				$repeat = ( $row->level - 1 >= 0 ) ? $row->level - 1 : 0;
				$row->treename = str_repeat( '.&#160;&#160;&#160;', $repeat ) . ( $row->level - 1 > 0 ? '|_&#160;' : '' ) . $row->title;
			}
		}

		return $categories;
	}

	public function isListingView()
	{
		$views = array('featured', 'category', 'categories', 'archive', 'frontpage' );

		return in_array(JRequest::getCmd('view'), $views);
	}

	public function isEntryView()
	{
		return JRequest::getCmd('view') == 'article';
	}

	public function onExecute( &$article, $html, $view, $options = array() )
	{
		if( $view == 'listing' )
		{
			$article->readmore = false;
			return $html;
		}

		if( $view == 'entry' )
		{
			return $html;
		}
	}

	public function getAuthorName()
	{
		return $this->_item->created_by_alias ? $this->_item->created_by_alias : $this->_item->author;
	}

	public function getContentPermalink()
	{
		$slug		= $this->_item->alias ? ($this->_item->id.':'.$this->_item->alias) : $this->_item->id;
		$catslug	= $this->_item->category_alias ? ($this->_item->catid.':'.$this->_item->category_alias) : $this->_item->catid;
		$parent_slug= $this->_item->category_alias ? ($this->_item->parent_id.':'.$this->_item->parent_alias) : $this->_item->parent_id;

		$link	= ContentHelperRoute::getArticleRoute($slug, $catslug);

		$link = $this->prepareLink( $link );

		return $link;
	}
}
