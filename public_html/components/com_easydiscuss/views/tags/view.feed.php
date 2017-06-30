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

class EasyDiscussViewTags extends EasyDiscussView
{
	public function display($tmpl = null)
	{
		if (!$this->config->get('main_rss')) {
			return;
		}

		$filteractive = $this->input->get('filter', 'allposts', 'string');
		$sort = $this->input->get('sort', 'latest', 'string');

		if ($filteractive == 'unanswered' && ($sort == 'active' || $sort == 'popular')) {
			//reset the active to latest.
			$sort = 'latest';
		}

		$this->doc->link = JRoute::_('index.php?option=com_easydiscuss&view=index');

		// Load up the tag
		$tag = $this->input->get('id', 0, 'int');

		$table = ED::table('Tags');
		$table->load($tag);

		// Set the title of the document
		$this->doc->setTitle($table->title);
		$this->doc->setDescription(JText::sprintf('COM_EASYDISCUSS_DISCUSSIONS_TAGGED_IN', $table->title));

		$postModel = ED::model('Posts');
		$posts = $postModel->getTaggedPost($tag, $sort, $filteractive);
		$pagination	= $postModel->getPagination('0', $sort, $filteractive);
		$posts = ED::formatPost($posts);

		// if there do not have any post just return it.
		if (empty($posts)) {
			return;
		}

		foreach ($posts as $row) {

			// Assign to feed item
			$title = $this->escape($row->title);
			$title = html_entity_decode($title);

			// load individual item creator class
			$item = new JFeedItem();
			$item->title = $title;
			$item->link = JRoute::_('index.php?option=com_easydiscuss&view=post&id=' . $row->id);
			$item->description = $row->content;
			$item->date = ED::date($row->created)->toMySQL();

			if (!empty($row->tags)) {
				
				$tagData = array();
				
				foreach ($row->tags as $tag) {
					$tagData[] = '<a href="' . JRoute::_('index.php?option=com_easydiscuss&view=tags&id=' . $tag->id) . '">' . $tag->title . '</a>';
				}

				$row->tags = implode(', ', $tagData);
			}
			
			$row->tags	= '';

			$user = ED::user($row->user_id);

			$item->category = $row->tags;
			$item->author = $user->getName();

			if ($this->jconfig->get('feed_email') != 'none') {

				$item->authorEmail = $this->jconfig->get('mailfrom');

				if ($this->jconfig->get('feed_email') == 'author') {
					$item->authorEmail = $user->email;
				}
			}

			$this->doc->addItem($item);
		}
	}
}
