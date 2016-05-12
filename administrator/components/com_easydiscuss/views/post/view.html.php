<?php
/**
* @package		EasyDiscuss
* @copyright	Copyright (C) 2010 - 2015 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasyBlog is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Restricted access');

require_once(DISCUSS_ADMIN_ROOT . '/views/views.php');

class EasyDiscussViewPost extends EasyDiscussAdminView
{
	public function display($tpl = null)
	{
		$this->checkAccess('discuss.manage.posts');

		// Load post item
		$id = $this->input->get('id', 0, 'int');

		$post = ED::post($id);

		// Select top 20 tags.
		$tagmodel = ED::model('Tags');
		$tags = $tagmodel->getTagCloud('20','post_count','DESC');

		$categoryModel = ED::model('Category');
		$defaultCategory = $categoryModel->getDefaultCategory();

		$nestedCategories = ED::populateCategories('', '', 'select', 'category_id', $defaultCategory->id, true, true, true, true, 'form-control');

		// Get the composer library
		$operation = $post->isNew() ? 'creating' : 'editing';
		$composer = ED::composer($operation, $post);

		$author = ED::user();
		$creatorName = $author->getName();

		$this->set('post', $post);
		$this->set('creatorName', $creatorName);
		$this->set('tags', $tags);
		$this->set('nestedCategories', $nestedCategories);
		$this->set('operation', $operation);
		$this->set('composer', $composer);

		//load require javascript string
		//ED::loadString(JRequest::getVar('view'));

		parent::display('post/default');
	}

	public function edit($tpl = null)
	{
		$this->checkAccess('discuss.manage.posts');

		// Load front end language file.
		JFactory::getLanguage()->load('com_easydiscuss', JPATH_ROOT);

		$postId = $this->input->get('id', 0);
		$parentId = $this->input->get('pid', '');
		$source = $this->input->get('source', 'posts');

		$post = ED::post($postId);

		// Get post's tags
		$postModel = ED::model('Post');
		$post->tags = $postModel->getPostTags($post->id);
		$post->content = ED::parser()->html2bbcode($post->content);

		// Select top 20 tags.
		$tagmodel = ED::model('Tags');
		$populartags = $tagmodel->getTagCloud('20','post_count','DESC');

		$repliesCnt = $postModel->getPostRepliesCount($post->id);
		$nestedCategories = ED::populateCategories('', '', 'select', 'category_id', $post->category_id, true, true, true, true, 'form-control');

		// Get's the creator's name
		$creatorName = $post->poster_name;

		if ($post->user_id) {
			$author = ED::user($post->user_id);
			$creatorName = $author->getName();
		}

		// Get a list of tags on the site
		$tagsModel = ED::model('Tags');
		$tags = $tagsModel->getTags();		

		// Render new composer
		// Get the composer library
		$operation = $post->isNew() ? 'creating' : 'editing';
		$composer = ED::composer($operation, $post);

		$this->set('creatorName', $creatorName);
		$this->set('post', $post);
		$this->set('populartags', $populartags);
		$this->set('repliesCnt', $repliesCnt);
		$this->set('source', $source);
		$this->set('parentId', $parentId);
		$this->set('nestedCategories', $nestedCategories);
		$this->set('operation', $operation);
		$this->set('composer', $composer);
		$this->set('tags', $tags);

		//load require javascript string
		//ED::loadString(JRequest::getVar('view'));

		parent::display('post/default');
	}

	public function getFieldForms( $isDiscussion = false , $postObj = false )
	{
		$theme 	= new DiscussThemes();

		return $theme->getFieldForms( $isDiscussion , $postObj );
	}

	public function getFieldTabs( $isDiscussion = false , $postObj = false )
	{
		$theme 	= new DiscussThemes();

		return $theme->getFieldTabs( $isDiscussion , $postObj );
	}

	public function registerToolbar()
	{
		$layout = $this->getLayout();

		JToolBarHelper::title( JText::_( 'COM_EASYDISCUSS_NEW_POST' ), 'discussions' );

		if ($layout == 'edit') {
			JToolBarHelper::title( JText::_( 'COM_EASYDISCUSS_EDITING_POST' ), 'discussions' );
		}


		JToolbarHelper::apply();
		JToolbarHelper::save();
		JToolBarHelper::divider();
		JToolBarHelper::cancel();
	}
}
