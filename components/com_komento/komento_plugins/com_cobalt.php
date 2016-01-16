<?php
/**
 * @package        Komento
 * @copyright      Copyright (C) 2012 Stack Ideas Private Limited. All rights reserved.
 * @license        GNU/GPL, see LICENSE.php
 *                 Komento is free software. This version may have been modified pursuant
 *                 to the GNU General Public License, and as distributed it includes or
 *                 is derivative of works licensed under the GNU General Public License or
 *                 other free or open source software licenses.
 *                 See COPYRIGHT.php for copyright notices and details.
 */

// No direct access
defined('_JEXEC') or die('Restricted access');

require_once(JPATH_ROOT . '/components/com_komento/komento_plugins/abstract.php');
require_once(JPATH_ROOT . '/components/com_cobalt/api.php');

class KomentoComCobalt extends KomentoExtension
{
	public $_item = NULL;

	public $_map = array(
		'id'         => 'id',
		'title'      => 'title',
		'hits'       => 'hits',
		'created_by' => 'user_id',
		'catid'      => 'section_id',
		'permalink'  => 'url'
	);

	public function __construct($component)
	{
		$this->addFile(JPATH_ROOT . '/components/com_cobalt/api.php');
		parent::__construct($component);
	}

	public function getComponentName()
	{
		return '<i class="icon-puzzle"></i> Cobalt 8';
	}

	public function getComponentIcon()
	{
		return JUri::root(TRUE) . '/administrator/components/com_cobalt/images/cobalt48.png';
	}

	public function load($cid)
	{
		static $instances = array();

		if(empty($cid))
		{
			return NULL;
		}

		if(!isset($instances[$cid]))
		{
			// Check if this record exist or not
			$sql = Komento::getSql();

			$sql->select( '#__js_res_record' )
				->column( 'id', 'id', 'count' )
				->where( 'id', $cid );

			$result = $sql->loadResult();

			if( $result < 1 )
			{
				return $this->onLoadArticleError( $cid );
			}

			$item      = ItemsStore::getRecord($cid);
			$item->url = $this->prepareLink( Url::record($item) );

			$instances[$cid] = $item;
		}

		$this->_item = $instances[$cid];

		return $this;
	}

	public function getContentIds($categories = '')
	{
		$db = JFactory::getDbo();

		if(empty($categories))
		{
			$query = 'SELECT `id` FROM ' . $db->nameQuote('#__js_res_record');
		}
		else
		{
			if(is_array($categories))
			{
				$categories = implode(',', $categories);
			}

			$query = 'SELECT `id` FROM ' . $db->nameQuote('#__js_res_record') . ' WHERE `section_id` IN (' . $categories . ')';
		}

		$db->setQuery($query);

		return $db->loadColumn();
	}

	public function getCategories()
	{
		$db    = JFactory::getDbo();
		$query = 'SELECT a.id, a.name as title'
			. ' FROM `#__js_res_sections` AS a'
			. ' WHERE a.published = 1'
			. ' ORDER BY a.title ASC';

		$db->setQuery($query);

		return $db->loadObjectList();
	}

	public function isListingView()
	{
		return JFactory::getApplication()->input->getCmd('view') == 'records';
	}

	public function isEntryView()
	{
		return JFactory::getApplication()->input->getCmd('view') == 'record';
	}


	public function onExecute(&$article, $html, $view, $options = array())
	{
		return $html;
	}

	public function onBeforeLoad($eventTrigger, $context, &$article, &$params, &$page, &$options)
	{
		if($context == 'text')
		{
			return FALSE;
		}

		return TRUE;
	}

	public function onAfterSaveComment($comment)
	{
		CSubscriptionsHelper::subscribe_record($this->_item->id);
		ATlog::log($this->_item, ATlog::COM_NEW, $comment->id);

		CEventsHelper::notify('record', CEventsHelper::_COMMENT_NEW, $this->_item->id, $this->_item->section_id,
			JFactory::getApplication()->input->getInt('cat_id'), $comment->id, 0, $this->_item);

		if($comment->parent_id > 0)
		{
			$parent = Komento::getComment($comment->parent_id);
			if($parent->created_by)
			{
				CEventsHelper::notify('record', CEventsHelper::_COMMENT_REPLY, $this->_item->id, $this->_item->section_id,
					JFactory::getApplication()->input->getInt('cat_id'), $parent->id, 0, $this->_item, 2, $parent->created_by);
			}
		}
		$this->_update_comments($comment->cid);
	}

	public function onAfterDeleteComment($comment)
	{
		$this->_update_comments($comment->cid);
	}

	public function getComponentThemePath()
	{
		return JPATH_ROOT . '/components/com_cobalt/library/php/comments/komento/tmpl';
	}

	public function onAfterPublishComment($comment)
	{
		$this->_update_comments($comment->cid);
	}

	public function onAfterUnpublishComment($comment)
	{
		$this->_update_comments($comment->cid);
	}

	private function _update_comments($record_id)
	{
		$db = JFactory::getDbo();
		$db->setQuery("SELECT COUNT(*) FROM #__komento_comments WHERE published = 1 AND component = 'com_cobalt' AND cid = {$record_id}");
		$comments = $db->loadResult();
		settype($comments, 'integer');

		$record = JTable::getInstance('Record', 'CobaltTable');
		$record->load($record_id);
		$record->comments = $comments;
		$record->index();
	}
}
