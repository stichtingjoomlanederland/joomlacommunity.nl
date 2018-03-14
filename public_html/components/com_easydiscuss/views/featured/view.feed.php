require_once(DISCUSS_ROOT . '/views/views.php');
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

class EasyDiscussViewFeatured extends EasyDiscussView
{
	function display($tmpl = null)
	{
		if (!$this->config->get('main_rss')) {
			return;
		}

		$this->doc->link = JRoute::_('index.php?option=com_easydiscuss&view=featured');

		$sort = $this->input->get('sort', 'latest', 'default');
		$filter = $this->input->get('filter', 'allposts', 'default');
		$category = $this->input->get('category_id', 0, 'int');
		$showFeaturedPost = true;

		$postModel = ED::model('Posts');
		$posts = $postModel->getData(true, $sort, null, $filter, $category, null, $showFeaturedPost);
		$pagination	= $postModel->getPagination('0', $sort, $filter, $category, $showFeaturedPost);

		$posts = ED::formatPost($posts);

		foreach ($posts as $row) {
			// Assign to feed item
			$title = $this->escape($row->title);
			$title = html_entity_decode($title);

			$category = $row->getCategory();

			// load individual item creator class
			$item = new JFeedItem();
			$item->title = $title;
			$item->link	= JRoute::_('index.php?option=com_easydiscuss&view=post&id=' . $row->id);
			$item->description = $row->content;
			$item->date	= ED::date($row->created)->toSql();
			$item->author = $row->user->getName();
			$item->category	= $category->getTitle();

			if ($this->jConfig->get('feed_email') != 'none') {

				if ($this->jConfig->get('feed_email') == 'author') {
					$item->authorEmail = $row->user->user->email;
				} else {
					$item->authorEmail = $this->jConfig->get('mailfrom');
				}
			}
			$this->doc->addItem($item);
		}
	}
}
