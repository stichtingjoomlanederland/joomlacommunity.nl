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

require_once(DISCUSS_ROOT . '/views/views.php');

class EasyDiscussViewTags extends EasyDiscussView
{
	public function display($tmpl = null)
	{
		// Set the page title
		ED::setPageTitle('COM_EASYDISCUSS_TAGS_TITLE');

		// Set the breadcrumbs
		$this->setPathway('COM_EASYDISCUSS_TOOLBAR_TAGS');

		// Determines if we should display a single tag result.
		$id = $this->input->get('id', 0, 'int');

		if ($id) {
			return $this->tag($tmpl);
		}

		$model = ED::model("Tags");
		$tags = $model->getTagCloud('', '', '');

		$this->set('tags', $tags);

		parent::display('tags/default');
	}

	/**
	 * Displays a list of discussions from a particular tag
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function tag($tmpl = null)
	{
		// Get the tag id
		$id = $this->input->get('id', 0, 'int');

		if (!$id) {
			return JError::raiseError(404, JText::_('COM_EASYDISCUSS_INVALID_TAG'));
		}

		$tag = ED::table('Tags');
		$tag->load($id);

		// Set meta tags.
		ED::setMeta();

		// Set the page title
		ED::setPageTitle(JText::sprintf('COM_EASYDISCUSS_VIEWING_TAG_TITLE', $this->escape($tag->title)));
		$this->setPathway($tag->title);

		$concatCode = ED::jconfig()->getValue('sef') ? '?' : '&';
		$this->doc->addHeadLink(JRoute::_( 'index.php?option=com_easydiscuss&view=tags&id=' . $tag ) . $concatCode . 'format=feed&type=rss' , 'alternate' , 'rel' , array('type' => 'application/rss+xml', 'title' => 'RSS 2.0') );
		$this->doc->addHeadLink(JRoute::_( 'index.php?option=com_easydiscuss&view=tags&id=' . $tag ) . $concatCode . 'format=feed&type=atom' , 'alternate' , 'rel' , array('type' => 'application/atom+xml', 'title' => 'Atom 1.0') );

		$filteractive = JRequest::getString('filter', 'allposts');
		$sort = JRequest::getString('sort', 'latest');

		if ($filteractive == 'unanswered' && ($sort == 'active' || $sort == 'popular')) {
			//reset the active to latest.
			$sort = 'latest';
		}

		// Get the list of posts for this tag
		$postModel = ED::model('Posts');
		$posts = $postModel->getTaggedPost($tag->id, $sort, $filteractive);
		$pagination = $postModel->getPagination($sort, $filteractive);

		$authorIds  = array();
		$topicIds 	= array();

		if (count($posts) > 0 ) {
			
			foreach ($posts as $item) {
				$authorIds[] = $item->user_id;
				$topicIds[] = $item->id;
			}
		}

		$lastReplyUser = $postModel->setLastReplyBatch($topicIds);
		$authorIds = array_merge($lastReplyUser, $authorIds);

		// Reduce SQL queries by pre-loading all author object.
		$authorIds = array_unique($authorIds);
		ED::user($authorIds);

		$postLoader = ED::table('Posts');
		$postLoader->loadBatch($topicIds);

		$postTagsModel = ED::model('PostsTags');
		$postTagsModel->setPostTagsBatch($topicIds);

		// Format the posts now
		$posts = ED::formatPost($posts, false , true);
		$posts = ED::getPostStatusAndTypes($posts);

		$this->set('tag', $tag);
		$this->set('rssLink', JRoute::_('index.php?option=com_easydiscuss&view=tags&id=' . $tag . '&format=feed'));
		$this->set('posts', $posts);
		$this->set('paginationType', DISCUSS_TAGS_TYPE);
		$this->set('pagination', $pagination);
		$this->set('sort', $sort);
		$this->set('filter', $filteractive);
		$this->set('showEmailSubscribe', true);
		$this->set('parent_id', $tag);

		parent::display('tags/item');
	}
}
