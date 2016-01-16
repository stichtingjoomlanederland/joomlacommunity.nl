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

class DiscussComposer
{
	public $id;

	private $post;
	public  $parent;
	private $isDiscussion;

	public $content = '';

	public $renderMode = 'onload'; // onload|explicit
	public $theme;

	public $classname;
	public $selector;

	public $editor;
	public $editorType = 'bbcode';

	public $operation;

	public function __construct($operation, $post)
	{
		$config = DiscussHelper::getConfig();

		$this->id = 'composer_' . rand();
		$this->operation = $operation;

		switch ($operation) {

			// Editing and creating a post goes here
			case "creating":
				$this->post = $post;
				$this->parent = $post;
				$this->content = $post->content;
				$this->isDiscussion = true;
				$this->editorType = $config->get('layout_editor', 'bbcode');
				break;

			//Editing a reply
			case "editing":
				$this->post = $post;
				$this->parent = DiscussHelper::getTable( 'Post' );
				$this->parent->load($post->parent_id);
				$this->content = $post->content;
				$this->isDiscussion = false;
				$this->editorType = $config->get('layout_reply_editor', 'bbcode');
				break;

			case "replying":
				$this->post = DiscussHelper::getTable( 'Post' );
				$this->parent = $post;
				$this->isDiscussion = false;
				$this->editorType = $config->get('layout_reply_editor', 'bbcode');
				break;
		}

		if ($this->editorType!='bbcode') {

			$this->editor = JFactory::getEditor($this->editorType);
		}

		// Names
		$this->classname  = $this->id;
		$this->selector   = '.' . $this->id;
	}

	public function getComposer()
	{
		$theme = new DiscussThemes();
		$theme->set('composer'		, $this);
		$theme->set('post'			, $this->post);
		$theme->set('parent'		, $this->parent);
		$theme->set('content'		, $this->content);
		$theme->set('editor'		, $this->editor);
		$theme->set('isDiscussion'	, $this->isDiscussion);
		$theme->set('renderMode'	, $this->renderMode);

		switch ($this->operation) {

			case "creating":
				$html = $theme->fetch('form.new.php');
				break;

			case "editing":
					$html = $theme->fetch('form.edit.php');
				break;

			case "replying":
				$html = $theme->fetch('form.reply.php');
				break;
		}

		return $html;
	}

	public function getEditor()
	{
		if( $this->operation == 'creating' )
		{
			$type = 'question';
		}
		else
		{
			$type = 'reply';
		}

		if( !empty($this->content) )
		{
			// No need switch when is a new post/reply
			$this->content = DiscussHelper::bbcodeHtmlSwitcher( $this->post, $type, true );
		}

		if ($this->editorType=='bbcode')
		{
			$html = '<div id="discuss-bbcode">';
			$html .= '<textarea class="dc_reply_content full-width" name="dc_reply_content" class="full-width">' . $this->content . '</textarea>';
			$html .= '</div>';
		}
		else
		{
			$html = '<div id="discuss-tinymce">';
			$html .= $this->editor->display("dc_reply_content", $this->content, '100%', '350', '10', '10', array('pagebreak', 'readmore'));
			$html .= '</div>';
		}

		return $html;
	}

	public function getFields()
	{
		// select top 20 tags.
		$tagmodel	= DiscussHelper::getModel( 'Tags' );
		$tags		= $tagmodel->getTagCloud('','post_count','DESC');

		$theme = new DiscussThemes();

		$theme->set('tags'			, $tags);
		$theme->set('composer'		, $this);
		$theme->set('post'			, $this->post);
		$theme->set('parent'		, $this->parent);
		$theme->set('isDiscussion'	, $this->isDiscussion);
		$theme->set('renderMode'	, $this->renderMode);

		return $theme->fetch('form.tabs.php');
	}

	public function setIsDiscussion( $value )
	{
		$this->isDiscussion = (bool) $value;
	}
}
