<?php
/**
* @package      EasyBlog
* @copyright    Copyright (C) 2010 - 2015 Stack Ideas Sdn Bhd. All rights reserved.
* @license      GNU/GPL, see LICENSE.php
* EasyBlog is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/

// No direct access
defined('_JEXEC') or die('Restricted access');

require_once( dirname( __FILE__ ) . DIRECTORY_SEPARATOR . 'abstract.php' );

class KomentoComeasyblog5 extends KomentoExtension
{
	public $_item;
	public $_map = array(
		'id' => 'id',
		'title' => 'title',
		'hits' => 'hits',
		'created_by' => 'created_by',
		'catid' => 'category_id',
		'permalink' => 'permalink'
		);

	public function __construct($component)
	{
		$this->addFile(JPATH_ADMINISTRATOR . '/components/com_easyblog/includes/easyblog.php');

		parent::__construct($component);
	}

	public function load($cid)
	{
		static $instances = array();

		if (!isset($instances[$cid])) {
			$this->_item = EB::post($cid);

			if (!$this->_item) {
				return $this->onLoadArticleError($cid);
			}

			$blogger = EB::user($this->_item->created_by);

			$this->_item->blogger = $blogger;

			$link = 'index.php?option=com_easyblog&view=entry&id=' . $this->getContentId();

			// forcefully get item id if request is ajax
			$format = JRequest::getString('format', 'html');

			if ($format === 'ajax') {
				$itemid = JRequest::getInt( 'pageItemId' );

				if (!empty($itemid)) {
					$link .= '&Itemid=' . $itemid;
				}
			}

			$link	= EBR::_($link);
			$this->_item->permalink = $this->prepareLink($link);

			$instances[$cid] = $this->_item;
		}

		$this->_item = $instances[$cid];

		return $this;
	}

	public function getContentIds($categories = '')
	{
		$db = Komento::getDBO();
		$query = '';

		if (empty($categories)) {
			$groups = Komento::getUsergroups();
			$gids = array();

			foreach ($groups as $group) {
				$gids[] = $group->id;
			}
			$user = JFactory::getUser();

			$result = array();

			// Get all the categories first
			$query = 'SELECT ' . $db->nameQuote('id') . ' FROM ' . $db->nameQuote('#__easyblog_category') . ' WHERE ' . $db->nameQuote('private') . ' = ' . $db->quote('0');

			if (!$user->guest) {
				$query .= ' OR ' . $db->nameQuote('private') . ' = ' . $db->quote('1');
			}

			$db->setQuery($query);
			$result = array_merge($result, $db->loadResultArray());

			$query = 'SELECT DISTINCT (' . $db->nameQuote('a') . '.' . $db->nameQuote('id') . ') FROM ' . $db->nameQuote('#__easyblog_category') . ' AS ' . $db->nameQuote('a');
			$query .= ' LEFT JOIN ' . $db->nameQuote( '#__easyblog_category_acl' ) . ' AS ' . $db->nameQuote('b');
			$query .= ' ON ' . $db->nameQuote('b') . '.' . $db->nameQuote('category_id') . ' = ' . $db->nameQuote('a') . '.' . $db->nameQuote('id');
			$query .= ' WHERE ' . $db->nameQuote('a') . '.' . $db->nameQuote('private') . ' = ' . $db->quote('2');
			$query .= ' AND ' . $db->nameQuote('b') . '.' . $db->nameQuote('acl_id') . ' = ' . $db->quote('1');
			$query .= ' AND ' . $db->nameQuote('b') . '.' . $db->nameQuote('content_id') . ' IN (' . $gids . ')';

			$db->setQuery($query);
			$result = array_merge($result, $db->loadResultArray());

			$categories = implode(',', $result);

			$query = 'SELECT ' . $db->nameQuote('id') . ' FROM ' . $db->nameQuote('#__easyblog_post');
			$query .= ' WHERE ' . $db->nameQuote('category_id') . ' IN (' . $categories . ')';
			$query .= ' ORDER BY ' . $db->nameQuote('id');
		} else {
			if ( is_array($categories)) {
				$categories = implode(',', $categories);
			}

			$query = 'SELECT `id` FROM ' . $db->nameQuote('#__easyblog_post') . ' WHERE `category_id` IN (' . $categories . ') ORDER BY `id`';
		}

		$db->setQuery($query);
		return $db->loadResultArray();
	}

	public function getCategories()
	{
		$db = Komento::getDBO();
		$query = 'SELECT a.id, a.title, a.parent_id, count(b.id) - 1 AS level'
				. ' FROM `#__easyblog_category` AS a'
				. ' INNER JOIN `#__easyblog_category` AS b ON a.lft BETWEEN b.lft and b.rgt'
				. ' GROUP BY a.id'
				. ' ORDER BY a.lft ASC';

		$db->setQuery($query);
		$categories = $db->loadObjectList();

		if (Komento::joomlaVersion() >= '1.6') {
			foreach ($categories as &$row) {
				$repeat = ($row->level - 1 >= 0)? $row->level - 1 : 0;
				$row->treename = str_repeat('.&#160;&#160;&#160;', $repeat) . ($row->level - 1 > 0 ? '|_&#160;' : '') . $row->title;
			}
		}

		return $categories;
	}

	public function getCommentAnchorId()
	{
		return 'comments';
	}

	public function isListingView()
	{
		// integration done in Easyblog
		return false;
	}

	public function isEntryView()
	{
		return JRequest::getCmd('view') == 'entry';
	}

	public function onExecute( &$article, $html, $view, $options = array() )
	{
		if ($view == 'entry' && $options['trigger']  == 'onDisplayComments') {
			return $html;
		}
	}

	public function getEventTrigger()
	{
		return 'onDisplayComments';
	}

	public function getAuthorName()
	{
		return $this->_item->blogger->getName();
	}

	public function getAuthorPermalink()
	{
		return $this->_item->blogger->getProfileLink();
	}

	public function getAuthorAvatar()
	{
		return $this->_item->blogger->getAvatar();
	}
}
