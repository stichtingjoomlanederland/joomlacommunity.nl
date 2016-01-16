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

class KomentoComHwdmediashare extends KomentoExtension
{
	public $_item;
	public $_map = array(
		'id'			=> 'id',
		'title'			=> 'title',
		'hits'			=> 'hits',
		'created_by'	=> 'created_user_id',
		'catid'			=> 'catid',
		'permalink'		=> 'permalink'
		);

	public function __construct( $component )
	{
		$this->addFile( JPATH_ROOT . DIRECTORY_SEPARATOR . 'components' . DIRECTORY_SEPARATOR . 'com_hwdmediashare' . DIRECTORY_SEPARATOR . 'helpers' . DIRECTORY_SEPARATOR .'route.php' );
		$this->addFile( JPATH_ROOT . DIRECTORY_SEPARATOR . 'components' . DIRECTORY_SEPARATOR . 'com_hwdmediashare' . DIRECTORY_SEPARATOR . 'libraries' . DIRECTORY_SEPARATOR .'factory.php' );

		parent::__construct( $component );
	}

	public function load( $cid )
	{
		static $instances = array();

		if( !isset( $instances[$cid] ) )
		{
			// Get a row instance.
			JTable::addIncludePath(JPATH_ADMINISTRATOR.'/components/com_hwdmediashare/tables');
			$table = JTable::getInstance('Media', 'hwdMediaShareTable');

			// Attempt to load the row.
			if ($table->load((int)$cid))
			{
				// Convert the JTable to a clean JObject.
				$properties = $table->getProperties(1);
				$this->_item = JArrayHelper::toObject($properties, 'JObject');
			}
			else
			{
				return $this->onLoadArticleError( $cid );
			}

			$slug		= $this->_item->alias ? ($this->_item->id.':'.$this->_item->alias) : $this->_item->id;
			$link = hwdMediaShareHelperRoute::getMediaItemRoute($slug);
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
			$query = 'SELECT `id` FROM ' . $db->nameQuote( '#__hwdms_media' ) . ' ORDER BY `id`';
		}
		else
		{
			if( is_array( $categories ) )
			{
				$categories = implode( ',', $categories );
			}

			$query = 'SELECT `id` FROM ' . $db->nameQuote( '#__hwdms_media' ) . ' WHERE `catid` IN (' . $categories . ') ORDER BY `id`';
		}

		$db->setQuery( $query );
		return $db->loadResultArray();
	}

	public function getCategories()
	{
		$db		= Komento::getDBO();
		$query	= 'SELECT a.id, a.title, a.level, a.parent_id'
				. ' FROM `#__categories` AS a'
				. ' WHERE a.extension = ' . $db->quote( 'com_hwdmediashare' )
				. ' AND a.parent_id > 0'
				. ' ORDER BY a.lft';

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
		$views = array('featured', 'category', 'categories', 'archive', 'frontpage' );

		return in_array(JRequest::getCmd('view'), $views);
	}

	public function isEntryView()
	{
		return JRequest::getCmd('view') == 'mediaitem';
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
		return $this->_item->created_user_id_alias ? $this->_item->created_user_id_alias : $this->_item->author;
	}

	public function getCategoryId()
	{
		$db = Komento::getDBO();

		$query = 'SELECT ' . $db->nameQuote( 'category_id' ) . ' FROM ' . $db->nameQuote( '#__hwdms_category_map' ) . ' WHERE ' . $db->nameQuote( 'element_id' ) . ' = ' . $db->quote( $this->_item->id );

		$db->setQuery( $query );

		$result = $db->loadResult();

		if( count( $result ) > 0 )
		{
			return $result;
		}

		return true;
	}

	public function getEventTrigger()
	{
		return false;
	}
}
