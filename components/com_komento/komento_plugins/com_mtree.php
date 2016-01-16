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

class KomentoComMtree extends KomentoExtension
{
	public $_item;
	public $_map = array(
		'id'			=> 'id',
		'title'			=> 'title',
		'hits'			=> 'hits',
		'created_by'	=> 'created_by',
		'catid'			=> 'catid',
		'permalink'		=> 'permalink'
		);

	public function __construct( $component )
	{
		$this->addFile( JPATH_ROOT . DIRECTORY_SEPARATOR . 'components' . DIRECTORY_SEPARATOR . 'com_content' . DIRECTORY_SEPARATOR . 'helpers' . DIRECTORY_SEPARATOR .'route.php' );

		parent::__construct( $component );
	}

	public function load( $cid )
	{
		static $instances = array();

		if( !isset( $instances[$cid] ) )
		{
			$db		= Komento::getDBO();
			$query	= 'SELECT a.`link_id` AS `id`, a.`link_name` AS `title`, a.`alias`, c.`cat_id` AS `catid`, a.`user_id` AS `created_by`, a.`link_hits` AS `hits` , '
					. ' c.`cat_name` AS category_title, c.`alias` AS category_alias,'
					. ' u.`name` AS author'
					. ' FROM ' . $db->nameQuote( '#__mt_links') . ' AS a'
					. ' INNER JOIN ' . $db->nameQuote( '#__mt_cl' ) . ' AS b ON a.`link_id` = b.`link_id`'
					. ' LEFT JOIN ' . $db->nameQuote( '#__mt_cats' ) . ' AS c ON b.`cat_id` = c.`cat_id`'
					. ' LEFT JOIN ' . $db->nameQuote( '#__users') . ' AS u ON u.id = a.`user_id`'
					. ' WHERE a.`link_id` = ' . $db->quote( (int) $cid );
			$db->setQuery( $query );

			if( !$this->_item = $db->loadObject() )
			{
				return $this->onLoadArticleError( $cid );
			}

			$link 	= 'index.php?option=com_mtree&task=viewlink&link_id=' . $this->_item->id;
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
		$db		= Komento::getDBO();
		$query	= 'SELECT a.`cat_id` AS `id`, a.`cat_name` AS `title` , a.`cat_parent` AS `parent_id`'
				. ' FROM `#__mt_cats` AS a'
				. ' ORDER BY a.`lft`';

		$db->setQuery( $query );
		$categories	= $db->loadObjectList();

		$children = array();

		foreach( $categories as $row )
		{
			if( $row->parent_id != -1 )
			{
				$pt		= $row->parent_id;
				$list	= @$children[$pt] ? $children[$pt] : array();
				$list[] = $row;
				$children[$pt] = $list;
			}
		}


		$res	= JHTML::_( 'menu.treerecurse', 0 , '', array(), $children , 9999, 0, 0 );

		return $res;
	}

	public function isListingView()
	{
		return false;
	}

	public function isEntryView()
	{
		return JRequest::getCmd('task') == 'viewlink';
	}

	public function onExecute( &$article, $html, $view, $options = array() )
	{
		if( $view == 'entry' )
		{
			return $html;
		}
	}

	public function getEventTrigger()
	{
		$entryTrigger = ( Komento::joomlaVersion() > '1.5' ) ? 'onContentAfterDisplay' : 'onAfterDisplayContent';

		return $entryTrigger;
	}

	public function getAuthorName()
	{
		return $this->_item->created_by_alias ? $this->_item->created_by_alias : $this->_item->author;
	}
}
