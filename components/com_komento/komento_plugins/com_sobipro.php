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

class KomentoComsobipro extends KomentoExtension
{
	public $_item;
	public $_map = array(
		'id'			=> 'id',
		'title'			=> 'title',
		'hits'			=> 'counter',
		'created_by'	=> 'owner',
		'catid'			=> 'parent'
		);

	public function __construct( $component )
	{
		if( !defined( 'SOBIPRO' ) )
		{
			define( 'SOBIPRO', true );
			defined( 'SOBI_CMS' ) || version_compare( JVERSION,'1.6.0','ge') ? define( 'SOBI_CMS', 'joomla16' ) : define( 'SOBI_CMS', 'joomla15' );
			defined( 'SOBI_TASK' ) || define( 'SOBI_TASK', 'task' );
			defined( 'SOBI_DEFLANG' ) || define( 'SOBI_DEFLANG', JFactory::getConfig()->get( 'config.language' ) );
			defined( 'SOBI_ACL' ) || define( 'SOBI_ACL', 'front' );
			defined( 'SOBI_ROOT' ) || define( 'SOBI_ROOT', JPATH_ROOT );
			defined( 'SOBI_MEDIA' ) || define( 'SOBI_MEDIA', implode( DIRECTORY_SEPARATOR, array( JPATH_ROOT, 'media', 'sobipro' ) ) );
			defined( 'SOBI_MEDIA_LIVE' ) || define( 'SOBI_MEDIA_LIVE', JURI::root().'/media/sobipro' );
			defined( 'SOBI_PATH' ) || define( 'SOBI_PATH', SOBI_ROOT.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_sobipro' );
			defined( 'SOBI_LIVE_PATH' ) || define( 'SOBI_LIVE_PATH', 'components/com_sobipro' );

			$this->addFile( SOBI_PATH.DIRECTORY_SEPARATOR.'lib'.DIRECTORY_SEPARATOR.'base'.DIRECTORY_SEPARATOR.'fs'.DIRECTORY_SEPARATOR.'loader.php' );
		}

		SPLoader::loadController( 'interface' );
		SPLoader::loadClass( 'base.filter' );
		SPLoader::loadClass( 'base.request' );
		SPLoader::loadClass( 'base.const' );
		SPLoader::loadClass( 'base.factory' );
		SPLoader::loadClass( 'base.object' );
		SPLoader::loadClass( 'base.filter' );
		SPLoader::loadClass( 'base.request' );
		SPLoader::loadClass( 'sobi' );
		SPLoader::loadClass( 'base.config' );
		SPLoader::loadClass( 'base.exception' );
		SPLoader::loadClass( 'cms.base.lang' );
		SPLoader::loadClass( 'mlo.input' );

		parent::__construct( $component );
	}

	public function load( $cid )
	{
		static $instances = array();

		if( !isset( $instances[$cid] ) )
		{
			$db		= Komento::getDBO();
			$query	= ' SELECT a.id, a.counter, a.owner, a.parent, a.name, b.baseData AS title'
					. ' FROM ' . $db->nameQuote( '#__sobipro_object' ) . ' AS a'
					. ' LEFT JOIN ' . $db->nameQuote( '#__sobipro_field_data') . ' AS b ON b.sid = a.id'
					. ' LEFT JOIN ' . $db->nameQuote( '#__sobipro_field') . ' AS c ON c.fid = b.fid'
					. ' WHERE a.id = ' . $db->quote( $cid )
					. ' AND a.oType = ' . $db->quote( 'entry' )
					. ' AND c.nid = ' . $db->quote( 'field_title' );
					//. ' AND c.nid = ' . $db->quote( 'field_name' );
			$db->setQuery($query);

			$result = $db->loadObject();

			if( !$result)
			{
				return $this->onLoadArticleError( $cid );
			}

			if( !empty( $result->name ) )
			{
				$result->title = $result->name;
				unset( $result->name );
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
			$query = 'SELECT `id` FROM ' . $db->nameQuote( '#__sobipro_object' ) . ' WHERE `oType` = ' . $db->quote( 'entry' ) . ' ORDER BY `id`';
		}
		else
		{
			if( is_array( $categories ) )
			{
				$categories = implode( ',', $categories );
			}

			$query = 'SELECT `id` FROM ' . $db->nameQuote( '#__sobipro_object' ) . ' WHERE `parent` IN (' . $categories . ') AND `oType` = ' . $db->quote( 'entry' ) . ' ORDER BY `id`';
		}

		$db->setQuery( $query );
		return $db->loadResultArray();
	}

	public function getCategories()
	{
		$db		= Komento::getDBO();
		$query	= 'SELECT id, name as title, parent as parent_id, parent, name'
				. ' FROM `#__sobipro_object`'
				. ' WHERE oType IN (' . $db->quote( 'category' ) . ',' . $db->quote( 'section' ) .')';

		$db->setQuery( $query );
		$categories = $db->loadObjectList();

		$children = array();

		foreach ($categories as $row)
		{
			$pt		= $row->parent_id;
			$list	= @$children[$pt] ? $children[$pt] : array();
			$list[] = $row;
			$children[$pt] = $list;
		}

		$categories	= JHTML::_('menu.treerecurse', 0, '', array(), $children, 9999, 0, 0);

		return $categories;
	}

	public function isListingView()
	{
		return false;
	}

	public function isEntryView()
	{
		return JRequest::getCmd('task') == 'entry.details';
	}

	public function onExecute( &$article, $html, $view, $options = array() )
	{
		if( $view == 'entry' )
		{
			if( $options['trigger'] == 'ContentDisplayEntryView' )
			{
				$article->text .= $html;
			}
			elseif( $options['trigger'] == 'AfterDisplayEntryView' )
			{
				echo $html;
			}

		}
	}

	public function getEventTrigger()
	{
		return 'AfterDisplayEntryView';
	}

	public function getContentPermalink()
	{
		// $sid = SPRequest::sid();
		$sid = $this->getContentId();
		// $pid = SPRequest::int( 'pid' ) ? SPRequest::int( 'pid' ) : Sobi::Section();
		$pid = JRequest::getInt( 'pid' ) ? JRequest::getInt( 'pid' ) : Sobi::Section();
		$link = Sobi::Url( array( 'pid' => $pid, 'sid' => $sid, 'title' => $this->_item->title ) );

		$link = $this->prepareLink( $link );
		// end untested codes

		return $link;
	}
}
