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

class KomentoComflexicontent extends KomentoExtension
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
		$this->addFile( JPATH_ADMINISTRATOR . DIRECTORY_SEPARATOR . 'components' . DIRECTORY_SEPARATOR . 'com_flexicontent' . DIRECTORY_SEPARATOR . 'defineconstants.php' );

		parent::__construct( $component );
	}

	public function load( $cid )
	{
		static $instances = array();

		if( !isset( $instances[$cid] ) )
		{
			$db		= Komento::getDBO();
			$query	= 'SELECT a.id, a.title, a.hits, a.created_by, a.created_by_alias, u.name AS author, c.id AS catid,'
					. ' CASE WHEN CHAR_LENGTH(a.alias) THEN CONCAT_WS(\':\', a.id, a.alias) ELSE a.id END as slug,'
					. ' CASE WHEN CHAR_LENGTH(c.alias) THEN CONCAT_WS(\':\', c.id, c.alias) ELSE c.id END as categoryslug'
					. ' FROM `#__content` AS a'
					. ' LEFT JOIN `#__flexicontent_cats_item_relations` AS rel ON rel.itemid = a.id'
					. ' LEFT JOIN `#__categories` AS c on c.id = rel.catid'
					. ' LEFT JOIN `#__users` AS u on u.id = a.created_by'
					. ' WHERE a.id = ' . $db->quote( (int) $cid );
			$db->setQuery( $query );

			if( !$this->_item = $db->loadObject() )
			{
				return $this->onLoadArticleError( $cid );
			}

			$link = 'index.php?option=com_flexicontent&view=item&cid=' . $this->_item->categoryslug . '&id=' . $this->_item->slug;
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
			$query = 'SELECT `itemid` FROM ' . $db->nameQuote( '#__flexicontent_cats_item_relations' ) . ' ORDER BY `itemid`';
		}
		else
		{
			if( is_array( $categories ) )
			{
				$categories = implode( ',', $categories );
			}

			$query = 'SELECT `itemid` FROM ' . $db->nameQuote( '#__flexicontent_cats_item_relations' ) . ' WHERE `catid` IN (' . $categories . ') ORDER BY `itemid`';
		}

		$db->setQuery( $query );
		return $db->loadResultArray();
	}

	public function getCategories()
	{
		$db		= Komento::getDBO();
		$query	= 'SELECT c.id, c.title, c.parent_id, c.title AS name, c.parent_id AS parent'
				. ' FROM #__categories AS c'
				. ' WHERE c.extension = ' . $db->quote( FLEXI_CAT_EXTENSION )
				. ' AND (c.lft > '.$db->quote(FLEXI_LFT_CATEGORY).' AND c.rgt < '.$db->quote(FLEXI_RGT_CATEGORY).') GROUP BY c.id ORDER BY c.lft ';

		$db->setQuery( $query );
		$categories = $db->loadObjectList();

		foreach( $categories as &$row )
		{
			$repeat = ( $row->level - 1 >= 0 ) ? $row->level - 1 : 0;
			$row->treename = str_repeat( '.&#160;&#160;&#160;', $repeat ) . ( $row->level - 1 > 0 ? '|_&#160;' : '' ) . $row->title;
		}

		return $categories;
	}

	public function isListingView()
	{
		$views = array('category', 'flexicontent', 'favourites');

		return in_array(JRequest::getCmd('view'), $views);
	}

	public function isEntryView()
	{
		// tricky here, the url states &view=item
		return JRequest::getCmd('view') == 'article';
	}

	public function onExecute( &$article, $html, $view, $options = array() )
	{
		if( $view == 'listing' )
		{
			return $html;
		}

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
