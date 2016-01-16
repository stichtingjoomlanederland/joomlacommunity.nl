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

class DiscussAttachments extends JTable
{
	public $id			= null;
	public $uid			= null;
	public $title		= null;
	public $type		= null;
	public $path		= null;
	public $created		= null;
	public $published	= null;
	public $mime		= null;

	/**
	 * Constructor for this class.
	 *
	 * @return
	 * @param object $db
	 */
	public function __construct(& $db )
	{
		parent::__construct( '#__discuss_attachments' , 'id' , $db );
	}

	/**
	 * Returns html formatted data of this current attachment
	 *
	 * @param	none
	 * @return	string	$html	HTML formatted data.
	 **/
	public function toHTML( $isEmail = false )
	{
		require_once DISCUSS_CLASSES . '/themes.php';

		$type		= explode( '/' , $this->mime );

		switch( $type[0] )
		{
			case 'image':
				$childtheme	= 'attachment.image.php';
				break;
			case 'application':
			case 'audio':
			case 'message':
			case 'multipart':
			case 'text':
			case 'video':
			default :
				$childtheme	= 'attachment.download.php';	
				break;
		}

		$theme	= new DiscussThemes();
		$theme->set( 'childtheme' , $childtheme );
		$theme->set( 'attachment' , $this );
		$theme->set( 'isEmail' , $isEmail );

		return $theme->fetch( 'attachment.default.php' );
	}

	public function getType()
	{
		$type = explode("/", $this->mime);

		return $type[0];
	}

	public function delete( $pk = null )
	{
		// @rule: Test if deletion is possible.
		if( !$this->deleteable() )
		{
			return false;
		}

		// @rule: Delete the files.
		$config		= DiscussHelper::getConfig();
		$storage	= DISCUSS_MEDIA . '/' . rtrim( $config->get( 'attachment_path' ) , '/' );
		$file		= $storage . '/' . $this->path;

		JFile::delete( $file );

		// Check if there's a _thumb file.
		$thumb 		= $file . '_thumb';

		if( JFile::exists( $thumb ) )
		{
			JFile::delete( $thumb );
		}

		return parent::delete();
	}

	/*
	 * Determines whether or not this current attachment is deleteable
	 *
	 * @param   none
	 * @return  boolean     True if allowed false otherwise.
	 */
	public function deleteable()
	{
		$config		= DiscussHelper::getConfig();
		$storage	= DISCUSS_MEDIA . '/' . rtrim( $config->get( 'attachment_path' ) , '/' );
		$file		= $storage . '/' . $this->path;

		// @rule: Test for file existance
		if( !JFile::exists( $file ) )
		{
			return false;
		}

		$acl		= DiscussHelper::getHelper( 'ACL' );
		$postHelper	= DiscussHelper::getHelper( 'Post' );
		$my			= JFactory::getUser();

		$deleteOwn	= ($my->id == $postHelper->getAttachmentOwner( $this->id ));
		$postId		= $postHelper->getAttachmentPostId( $this->id );

		$post = DiscussHelper::getTable('Post');
		$post->load($postId);

		$isModerator	= DiscussHelper::getHelper( 'Moderator' )->isModerator( $post->category_id );

		// @rule: Test if the user is allowed to delete attachments.
		// if( $my->id &&
		// 		( DiscussHelper::isSiteAdmin()
		// 			|| $acl->allowed( 'delete_attachment')
		// 			|| $deleteOwn
		// 			|| $isModerator
		// 		)
		// 	)
		// {
		// 	return true;
		// }
		if($my->id && ($acl->allowed('delete_attachment') && $deleteOwn) || DiscussHelper::isSiteAdmin() || $isModerator)
		{
			return true;
		}		

		return false;
	}


	public function preview()
	{
		$config	= DiscussHelper::getConfig();
		$path	= $config->get( 'attachment_path' );
		$file	= JPATH_ROOT . '/media/com_easydiscuss/' . $path . '/' . $this->path;

		if (!JFile::exists($file))
		{
			return false;
		}

		switch( $this->getType() )
		{
			case 'image':
				echo '<img src="' . DiscussRouter::_( 'index.php?option=com_easydiscuss&controller=attachment&task=displayFile&tmpl=component&id=' . $this->id , false , true ) . '" />';
				exit;
			break;
			default:
				return false;
			break;
		}
	}

	public function download()
	{
		$config	= DiscussHelper::getConfig();
		$path	= $config->get( 'attachment_path' );
		$file	= JPATH_ROOT . '/media/com_easydiscuss/' . $path . '/' . $this->path;

		if (!JFile::exists($file))
		{
			return false;
		}

		$length = filesize($file);
		switch( $this->getType() )
		{
			case 'image':
				echo '<img src="' . JRoute::_( 'index.php?option=com_easydiscuss&controller=attachment&task=displayFile&tmpl=component&id=' . $this->id ) . '" />';
				exit;
			break;
			default:
				header('Content-Description: File Transfer');
				header('Content-Type: ' . $this->mime);
				header("Content-Disposition: attachment; filename=\"".basename($this->title)."\";" );
				header('Content-Transfer-Encoding: binary');
				header('Expires: 0');
				header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
				header('Pragma: public');
				header('Content-Length: ' . $length );
			break;
		}

		ob_clean();
		flush();
		readfile($file);
		exit;
	}
}
