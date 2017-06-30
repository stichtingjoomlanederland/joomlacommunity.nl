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
defined('_JEXEC') or die('Restricted access');

require_once(DISCUSS_ROOT . '/views/views.php');

class EasyDiscussViewCategories extends EasyDiscussView
{
	function display($tmpl = null)
	{
		if (!$this->config->get('main_rss')) {
			return;
		}

		$sort = $this->input->get('sort', 'latest', 'default');
		$filter	= $this->input->get('filter', 'allposts', 'default');
		$category = $this->input->get('category_id', 0, 'int');

		$this->doc->link	= JRoute::_('index.php?option=com_easydiscuss&view=categories&layout=listings&category_id=' . $category);

		$this->doc->setTitle($this->escape($this->doc->getTitle()));

		$model = ED::model('Posts');
		$posts = $model->getData(true, $sort, null, $filter, $category);

		$posts = ED::formatPost($posts);

		foreach ($posts as $row) {

			// load individual item creator class
			$item = new JFeedItem();

			$category = $row->getCategory();

			$item->title = $row->getTitle();

			$item->link	= JRoute::_('index.php?option=com_easydiscuss&view=post&id=' . $row->id);

			// Problems with other language
			$item->description = $row->getContent();

			$item->date	= ED::date($row->created)->toSql();
			$item->author = $row->getOwner()->getName();
			$item->category	= $category->getTitle();

			if ($this->jconfig->get('feed_email') != 'none') {
				if ($this->jconfig->get('feed_email' ) == 'author') {
					$item->authorEmail = $row->getOwner()->getEmail();
				} else {
					$item->authorEmail = $this->jconfig->get('mailfrom');
				}
			}

			$this->doc->addItem($item);
		}
	}
}
