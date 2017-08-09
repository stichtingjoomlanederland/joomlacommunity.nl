<?php
/**
* @package		EasyDiscuss
* @copyright	Copyright (C) 2010 - 2015 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasyDiscuss is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');

class plgSearchEasyDiscuss extends JPlugin
{

	public function __construct(&$subject, $params)
	{
		parent::__construct($subject, $params);
	}

	public function exists()
	{
		$path = JPATH_ROOT . '/administrator/components/com_easydiscuss/easydiscuss.xml';
		$engine = JPATH_ADMINISTRATOR . '/components/com_easydiscuss/includes/easydiscuss.php';

		if (!JFile::exists($engine) || !JFile::exists($path)) {
		    return false;
		}

		require_once($engine);		

		jimport('joomla.filesystem.file');

		return true;
	}	

	/** 1.6 **/
	public function onContentSearchAreas()
	{
		JFactory::getLanguage()->load('com_easydiscuss', JPATH_ROOT);
		$title = JText::_('PLG_EASYDISCUSS_SEARCH_FILTER_TITLE');

		$areas = array('discussions' => $title);

		return $areas;
	}

	/** 1.5 **/
	public function onSearchAreas()
	{
		JFactory::getLanguage()->load('com_easydiscuss', JPATH_ROOT);
		$title = JText::_('PLG_EASYDISCUSS_SEARCH_FILTER_TITLE');

		$areas = array('discussions' => $title);

		return $areas;
	}

	/** 1.6 **/
	public function onContentSearch($text, $phrase='', $ordering='', $areas=null)
	{
		return $this->onSearch($text, $phrase, $ordering, $areas);
	}

	/** 1.5 **/
	public function onSearch($text, $phrase='', $ordering='', $areas=null)
	{
		$plugin	= JPluginHelper::getPlugin('search', 'easydiscuss');

		if (!$this->exists()) {
			return array();
		}

		if (is_array($areas)) {

			if (!array_intersect($areas, array_keys($this->onContentSearchAreas()))) {
				return array();
			}
		}

		$text = trim($text);

		if ($text == '') {
			return array();
		}

		$result	= $this->getResult($text, $phrase);

		if (!$result) {
			return array();
		}

		foreach($result as $row) {

			$link = EDR::_('view=post&id=' . $row->id);

			if ($row->parent_id != 0) {
				$link = EDR::_('view=post&id=' . $row->parent_id) . '#' . JText::_('COM_EASYDISCUSS_REPLY_PERMALINK') . '-' . $row->id;
			}

			// Determine that is reply then populate the post title
			if (empty($row->title) && $row->parent_id != 0) {

				$post = ED::post($row->parent_id);

				$row->title = JText::_('COM_EASYDISCUSS_SEARCH_REPLY_TITLE_PREFIX') . $post->title;
			}

			$category = $this->getCategory($row->category_id);
			$row->section = JText::sprintf('PLG_EASYDISCUSS_SEARCH_SECTION', $category);

			$row->href = $link;
		}

		return $result;
	}

	public function getCategory($categoryId)
	{
		$db = ED::db();
		$query = 'SELECT `title` FROM `#__discuss_category` WHERE id=' . $db->Quote($categoryId);

		$db->setQuery($query);

		return $db->loadResult();
	}

	public function getResult($text, $phrase)
	{
		$my = JFactory::getUser();

		$db = ED::db();
		$where = array();

		// used for privacy
		$excludeCats = array();

		switch ($phrase) {

			case 'exact':
				$where[] = 'a.`title` LIKE ' . $db->Quote( '%'.$db->getEscaped( $text, true ).'%', false );
				$where[] = 'a.`content` LIKE ' . $db->Quote( '%'.$db->getEscaped( $text, true ).'%', false );

				$where = '(' . implode( ') OR (', $where ) . ')';
				break;
			case 'all':
			case 'any':
			default:
				$words = explode(' ', $text);
				$wheres = array();

				foreach ($words as $word) {
					$word = $db->Quote('%'.$db->getEscaped($word, true).'%', false);

					$where[] = 'a.`title` LIKE ' . $word;
					$where[] = 'a.`content` LIKE ' . $word;

					$wheres[] = implode(' OR ', $where);
				}
				$where = '(' . implode(($phrase == 'all' ? ') AND (' : ') OR ('), $wheres ) . ')';
				break;
		}

		// get all private categories id
		$excludeCats = ED::getPrivateCategories();

		$query	= 'SELECT a.*, a.`content` AS text , "2" as browsernav';
		$query	.= ' FROM `#__discuss_posts` as a ';
		$query	.= ' WHERE (' . $where . ') ';
		$query	.= ' AND a.`published` = ' . $db->Quote(DISCUSS_ID_PUBLISHED);

		if (!empty($excludeCats)) {
			$query .= ' AND a.`category_id` NOT IN (' . implode(',', $excludeCats) . ')';
		}

		$db->setQuery($query);

		return $db->loadObjectList();
	}
}
