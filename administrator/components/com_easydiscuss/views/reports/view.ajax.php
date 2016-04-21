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

require_once DISCUSS_ADMIN_ROOT . '/views/views.php';

class EasyDiscussViewReports extends EasyDiscussAdminView
{
	/**
	 * Previews an reports
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function preview()
	{
		$id = $this->input->get('id', 0, 'int');

		if (!$id) {
			return $this->ajax->reject();
		}

		$url = JURI::root() . 'administrator/index.php?option=com_easydiscuss&view=reports&layout=preview&tmpl=component&id=' . $id;

		$theme = ED::themes();
		$theme->set('url', $url);

		$output = $theme->output('admin/reports/dialog.reasons');

		return $this->ajax->resolve($output);
	}

	public function submitEmail()
	{
		$ajax = ED::ajax();
		$my		= JFactory::getUser();

		$id = $this->input->get('id', '0', 'int');
		$content = $this->input->get('content', '', 'raw');

		if ($my->id == 0) {
			$ajax->reject(JText::_('COM_EASYDISCUSS_YOU_DO_NOT_HAVE_PERMISION_TO_SUBMIT_REPORT'));
			return $ajax->send();
		}

		if (! $id) {
			$ajax->reject(JText::_('COM_EASYDISCUSS_INVALID_POST_ID'));
			return $ajax->send();
		}

		$post = ED::table('Posts');
		$post->load($id);

		$moderator = ED::user($my->id);
		$author = JFactory::getUser($post->user_id);

		$emailData = array();
		$emailData['postAuthor'] = $moderator->getName();
		$emailData['postAuthorAvatar'] = $moderator->getAvatar();
		$emailData['postDate'] = ED::date()->toFormat($post->created);
		$emailData['postLink'] = JURI::root() . 'index.php?option=com_easydiscuss&view=post&id=' . $post->id;
		$emailData['postTitle'] = $post->title;
		$emailData['messages'] = $content;

		if (! empty($post->parent_id)) {
			$parentTbl = ED::table('Posts');
			$parentTbl->load( $post->parent_id );

			$emailData['postTitle'] = $parentTbl->title;
			$emailData['postLink'] = JURI::root() . 'index.php?option=com_easydiscuss&view=post&id=' . $parentTbl->id;
		}

		$noti	= ED::getNotification();
		$noti->addQueue( $author->email , JText::sprintf('COM_EASYDISCUSS_REQUIRED_YOUR_ATTENTION', $emailData['postTitle']), '', 'email.report.attention', $emailData);

		$ajax->resolve(JText::_( 'COM_EASYDISCUSS_EMAIL_SENT_TO_AUTHOR'));
		return $ajax->send();
	}


	public function deleteConfirm()
	{
		$ajax = ED::ajax();
		$my		= JFactory::getUser();

		$postId = $this->input->get('id', 0, 'int');

		if ($my->id == 0) {
			$ajax->reject(JText::_('COM_EASYDISCUSS_YOU_DO_NOT_HAVE_PERMISION_TO_SUBMIT_REPORT'));
			return $ajax->send();
		}

		if (!$postId) {
			$ajax->reject(JText::_('COM_EASYDISCUSS_INVALID_POST_ID'));
			return $ajax->send();
		}

		$theme = ED::themes();
		$theme->set('id', $postId);
		$contents = $theme->output('admin/reports/dialog.delete.post');

		$ajax->resolve($contents);
		return $ajax->send();
	}
}
