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

class KomentoComcontent extends KomentoExtension
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

			if( !$result = $db->loadObject() )
			{
				return $this->onLoadArticleError( $cid );
			}

			$instances[$cid] = $result;
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
			$config = Komento::getConfig( 'com_content' );

			if( !$config->get( 'layout_frontpage_readmore_use_joomla' ) )
			{
				$article->readmore = false;
			}
			else
			{
				if( $config->get( 'layout_frontpage_readmore' ) == 2 )
				{
					$article->readmore = true;
				}

				if( $config->get( 'layout_frontpage_readmore' ) == 0 )
				{
					$article->readmore = false;
				}
			}
			if( Komento::isJoomla15() )
			{
				$article->text .= $html;
			}
			return $html;
		}

		if( $view == 'entry' )
		{
			if( Komento::isJoomla15() )
			{
				$article->text .= $html;
			}
			return $html;
		}
	}

	public function getEventTrigger()
	{
		$entryTrigger = ( Komento::isJoomla15() ) ? 'onPrepareContent' : 'onContentAfterDisplay';

		return $entryTrigger;
	}

	public function getContext()
	{
		if( Komento::isJoomla15() )
		{
			return true;
		}

		// Entry view's context is definitely com_content.article
		if( $this->isEntryView() )
		{
			return 'com_content.article';
		}

		// Due to a change in the latest Joomla (Joomla 2.5.14 and Joomla 3.1.5)
		// The context in listing pages is no longer com_content.article
		// Return array with all 3 context here to support prior Joomla version, as well as the latest Joomla version
		if( $this->isListingView() )
		{
			return array( 'com_content.article', 'com_content.category', 'com_content.featured' );
		}

		return false;
	}

	public function getAuthorName()
	{
		return $this->_item->created_by_alias ? $this->_item->created_by_alias : $this->_item->author;
	}

	public function getContentPermalink()
	{
		$slug = $this->_item->alias ? ($this->_item->id.':'.$this->_item->alias) : $this->_item->id;
		$catslug = $this->_item->category_alias ? ($this->_item->catid.':'.$this->_item->category_alias) : $this->_item->catid;
		$parent_slug = $this->_item->category_alias ? ($this->_item->parent_id.':'.$this->_item->parent_alias) : $this->_item->parent_id;

		$link = ContentHelperRoute::getArticleRoute($slug, $catslug);

		$link = $this->prepareLink( $link );

		return $link;
	}

	public function onBeforeLoad( $eventTrigger, $context, &$article, &$params, &$page, &$options )
	{
		if( $this->isEntryView() )
		{
			$config = Komento::getConfig( 'com_content' );

			if( $config->get( 'pagebreak_load' ) == 'all' || JRequest::getInt( 'showall', 0 ) == 1 )
			{
				return true;
			}

			$regex = '#<hr(.*)class="system-pagebreak"(.*)\/>#iU';

			$matches = array();
			$count = 0;

			preg_match_all($regex, $article->introtext, $matches, PREG_SET_ORDER);
			$count += count( $matches );

			preg_match_all($regex, $article->fulltext, $matches, PREG_SET_ORDER);
			$count += count( $matches );

			preg_match_all($regex, $article->text, $matches, PREG_SET_ORDER);
			$count += count( $matches );

			if( $count === 0 )
			{
				return true;
			}
			else
			{
				if( $config->get( 'pagebreak_load' ) == 'first' && $page == 0 )
				{
					return true;
				}

				if( $config->get( 'pagebreak_load' ) == 'last' && $count == $page )
				{
					return true;
				}

				return false;
			}
		}
		else
		{
			return true;
		}
	}
}
