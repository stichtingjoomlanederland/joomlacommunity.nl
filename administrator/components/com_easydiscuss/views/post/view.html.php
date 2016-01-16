<?php
/**
 * @package		EasyDiscuss
 * @copyright	Copyright (C) 2010 Stack Ideas Private Limited. All rights reserved.
 * @license		GNU/GPL, see LICENSE.php
 *
 * EasyDiscuss is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * See COPYRIGHT.php for copyright notices and details.
 */
defined('_JEXEC') or die('Restricted access');

require_once DISCUSS_ADMIN_ROOT . '/views.php';
require_once DISCUSS_HELPERS . '/parser.php';

class EasyDiscussViewPost extends EasyDiscussAdminView
{
	public function display($tpl = null)
	{
		// Initialise variables
		$doc	= JFactory::getDocument();
		$doc->addScript( JURI::root() . 'administrator/components/com_easydiscuss/assets/js/admin.js' );

		// Load front end language file.
		JFactory::getLanguage()->load( 'com_easydiscuss' , JPATH_ROOT );

		$postId		= JRequest::getInt('id', 0);
		$parentId	= JRequest::getString('pid', '');
		$source		= JRequest::getVar('source', 'posts');

		$post		= JTable::getInstance( 'Posts' , 'Discuss' );
		$post->load($postId);

		$post->content_raw = $post->content;

		// Get post's tags
		$postModel		= $this->getModel( 'Posts' );
		$post->tags		= $postModel->getPostTags( $post->id );
		$post->content	= EasyDiscussParser::html2bbcode( $post->content );

		// Select top 20 tags.
		$tagmodel		= $this->getModel( 'Tags' );
		$populartags	= $tagmodel->getTagCloud('20','post_count','DESC');

		$repliesCnt = $postModel->getPostRepliesCount( $post->id );

		$nestedCategories	= DiscussHelper::populateCategories('', '', 'select', 'category_id', $post->category_id, true, true);

		$config	= DiscussHelper::getConfig();

		// Get's the creator's name
		$creatorName		= $post->poster_name;

		if( $post->user_id )
		{
			$author 			= DiscussHelper::getTable( 'Profile' );
			$author->load( $post->user_id );

			$creatorName 		= $author->getName();
		}

		require_once DISCUSS_CLASSES . '/composer.php';
		$composer = new DiscussComposer("creating", $post);

		$this->assignRef( 'creatorName'		, $creatorName );
		$this->assignRef( 'config'			, $config );
		$this->assignRef( 'post'			, $post );
		$this->assignRef( 'populartags'		, $populartags );
		$this->assignRef( 'repliesCnt'		, $repliesCnt );
		$this->assignRef( 'source'			, $source );
		$this->assignRef( 'parentId'		, $parentId );
		$this->assignRef( 'nestedCategories', $nestedCategories );
		$this->assignRef( 'composer'		, $composer );
		$this->assign( 'joomlaversion'		, DiscussHelper::getJoomlaVersion() );

		//load require javascript string
		DiscussHelper::loadString( JRequest::getVar('view') );

		parent::display($tpl);
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
		JToolBarHelper::title( JText::_( 'COM_EASYDISCUSS_EDITING_POST' ), 'discussions' );

		JToolbarHelper::apply();
		JToolbarHelper::save();
		JToolBarHelper::divider();
		JToolBarHelper::cancel();
	}
}
