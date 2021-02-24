<?php
/**
* @package		EasyBlog
* @copyright	Copyright (C) 2010 - 2014 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasyBlog is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');

require_once(dirname(__FILE__) . '/table.php');

class DiscussPostReject extends EasyDiscussTable
{
	public $id = null;
	public $post_id = null;
	public $created_by = null;
	public $message	= null;
	public $created = null;

	public function __construct(&$db)
	{
		parent::__construct( '#__discuss_post_reject' , 'id' , $db );
	}


	/**
	 * Override the parent's store behavior
	 *
	 * @since	5.0
	 * @access	public
	 */
	public function store($updateNulls = false)
	{

		// Clear any previous messages
		// $model = ED::model('PostReject');
		// $model->clear($this->post_id);

		return parent::store();
	}

	/**
	 * Notifies the author when a post is rejected
	 *
	 * @since	5.0
	 * @access	public
	 */
	public function notify()
	{
		ED::loadLanguages(JPATH_ADMINISTRATOR);

		$post = ED::post($this->post_id);

		$type = $post->isReply() ? 'REPLY' : 'QUESTION';
		$emailData = array();
		$emailData['postTitle'] = $post->getTitle();
		$emailData['postLink'] = EDR::getRoutedURL('view=post&id=' . $post->id, false, true);
		$emailData['owner_email'] = $post->getOwner()->getEmail();
		$emailData['emailTemplate'] = 'email.post.rejected';
		$emailData['emailSubject'] = JText::sprintf('COM_EASYDISCUSS_' . $type . '_ASKED_REJECTED', $post->getTitle());
		$emailData['type'] = $type;
		$emailData['rejectMessage'] = $this->message;

		ED::Mailer()->notifyThreadOwner($emailData, array());
	}
}
