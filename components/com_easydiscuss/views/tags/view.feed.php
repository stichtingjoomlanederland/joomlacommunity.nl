<?php
/**
* @package		EasyDiscuss
* @copyright	Copyright (C) 2010 Stack Ideas Private Limited. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasyDiscuss is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Restricted access');

require_once(DISCUSS_ROOT . '/views.php');

class EasyDiscussViewTags extends EasyDiscussView
{
	public function display($tmpl = null)
	{
		$config = DiscussHelper::getConfig();
		$jConfig = DiscussHelper::getJConfig();

		if (!$config->get('main_rss')) {
			return;
		}

		$filteractive	= JRequest::getString('filter', 'allposts');
		$sort			= JRequest::getString('sort', 'latest');

		if ($filteractive == 'unanswered' && ($sort == 'active' || $sort == 'popular')) {
			//reset the active to latest.
			$sort = 'latest';
		}

		$doc = JFactory::getDocument();
		$doc->link = JRoute::_('index.php?option=com_easydiscuss&view=index');

		// Load up the tag
		$tag = JRequest::getInt('id', 0);
		$table = DiscussHelper::getTable('Tags');
		$table->load($tag);

		// Set the title of the document
		$doc->setTitle($table->title);
		$doc->setDescription(JText::sprintf('COM_EASYDISCUSS_DISCUSSIONS_TAGGED_IN', $table->title));

		$postModel	= $this->getModel('Posts');
		$posts		= $postModel->getTaggedPost( $tag , $sort, $filteractive );
		$pagination	= $postModel->getPagination( '0' , $sort, $filteractive);
		$jConfig 	= DiscussHelper::getJConfig();
		$posts		= DiscussHelper::formatPost($posts);

		foreach ($posts as $row) {
			
			// Assign to feed item
			$title = $this->escape( $row->title );
			$title = html_entity_decode( $title );

			// load individual item creator class
			$item				= new JFeedItem();
			$item->title 		= $title;
			$item->link 		= JRoute::_('index.php?option=com_easydiscuss&view=post&id=' . $row->id );
			$item->description 	= $row->content;
			$item->date			= DiscussHelper::getDate( $row->created )->toMySQL();

			if( !empty( $row->tags ) )
			{
				$tagData	= array();
				foreach( $row->tags as $tag )
				{
					$tagData[] = '<a href="' . JRoute::_('index.php?option=com_easydiscuss&view=tags&id=' . $tag->id ) . '">' . $tag->title . '</a>';
				}
				$row->tags	= implode(', ', $tagData);
			}
			else
			{
				$row->tags	= '';
			}

			$item->category		= $row->tags;
			$item->author		= $row->user->getName();

			if( $jConfig->get( 'feed_email' ) != 'none' )
			{
				if( $jConfig->get( 'feed_email' ) == 'author' )
				{
					$item->authorEmail	= $row->user->email;
				}
				else
				{
					$item->authorEmail	= $jConfig->get( 'mailfrom' );
				}
			}

			$doc->addItem($item);
		}
	}
}
