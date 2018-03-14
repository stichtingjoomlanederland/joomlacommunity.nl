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

class EasyDiscussViewProfile extends EasyDiscussView
{
	function display($tmpl = null)
	{
		if (!$this->config('main_rss')) {
			return;
		}

		$userid = $this->input->get('id', null, 'int');
		$user = ED::user($userid);

		$this->doc->link = EDR::_('index.php?option=com_easydiscuss&view=profile&id=' . $user->id);

		$this->doc->setTitle($user->getName());
		$this->doc->setDescription($user->getDescription());

		$model = ED::model('Posts');
		$posts = $model->getPostsBy('user', $user->id);

		$posts = ED::formatPost($posts);

		foreach ($posts as $row) {

			// Assign to feed item
			$title = $this->escape($row->title);
			$title = html_entity_decode($title);

			// load individual item creator class
			$item = new JFeedItem();
			$item->title = $title;
			$item->link = JRoute::_('index.php?option=com_easydiscuss&view=post&id=' . $row->id);
			$item->description = $row->content;
			$item->date = ED::date($row->created)->toSql();

			if (!empty($row->tags)) {

				$tagData = array();

				foreach ($row->tags as $tag) {

					$tagData[] = '<a href="' . JRoute::_('index.php?option=com_easydiscuss&view=tags&id=' . $tag->id) . '">' . $tag->title . '</a>';
				}

				$row->tags = implode(', ', $tagData);
			}

			$row->tags	= '';

			$item->category	= $row->tags;
			$item->author = $row->user->getName();

			if ($this->jConfig->get('feed_email') != 'none') {

				if ($this->jConfig->get('feed_email') == 'author') {
					$item->authorEmail = $row->user->email;
				}

				$item->authorEmail = $this->jConfig->get('mailfrom');
			}

			$this->doc->addItem($item);
		}
	}
}
