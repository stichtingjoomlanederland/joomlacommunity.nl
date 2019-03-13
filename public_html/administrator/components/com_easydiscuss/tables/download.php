<?php
/**
* @package		EasyDiscuss
* @copyright	Copyright (C) 2010 - 2018 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasyDiscuss is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');

ED::import('admin:/tables/table');

class DiscussDownload extends EasyDiscussTable
{
	public $id = null;
	public $userid = null;
	public $state = null;
	public $params = null;
	public $created = null;

	public function __construct(&$db)
	{
		parent::__construct('#__discuss_download', 'id', $db);
	}

	/**
	 * Determine whether user has requested.
	 *
	 * @since	4.1
	 * @access	public
	 */
	public function isExists()
	{
		if (is_null($this->id)) {
			return false;
		}

		return true;
	}

	/**
	 * Determine when the state is ready
	 *
	 * @since	4.1
	 * @access	public
	 */
	public function isNew()
	{
		return $this->state == DISCUSS_DOWNLOAD_REQ_NEW;
	}

	/**
	 * Determine when the state is ready
	 *
	 * @since	4.1
	 * @access	public
	 */
	public function isProcessing()
	{
		return $this->state == DISCUSS_DOWNLOAD_REQ_PROCESS;
	}

	/**
	 * Determine when the state is ready
	 *
	 * @since	4.1
	 * @access	public
	 */
	public function isReady()
	{
		return $this->state == DISCUSS_DOWNLOAD_REQ_READY;
	}

	/**
	 * Method used to update the request state.
	 *
	 * @since 2.1.11
	 * @access public
	 */
	public function updateState($state)
	{
		$this->state = $state;

		// debug. need to uncomment.
		return $this->store();
	}

	/**
	 * Method used to set filepath.
	 *
	 * @since 2.1.11
	 * @access public
	 */
	public function setFilePath($filepath)
	{
		$params = new JRegistry($this->params);
		$params->set('path', $filepath);
		$this->params = $params->toString();
	}

	/**
	 * Request state of the download. Return false if not exist.
	 *
	 * @since 4.1
	 * @access public
	 */
	public function getState()
	{
		if (!$this->isExists()) {
			return false;
		}

		return $this->state;
	}

	/**
	 * Retrieves the label for the state (used for display purposes)
	 *
	 * @since	4.1
	 * @access	public
	 */
	public function getStateLabel()
	{
		if ($this->getState() == DISCUSS_DOWNLOAD_REQ_READY) {
			return JText::_('COM_ED_DOWNLOAD_STATE_READY');
		}

		return JText::_('COM_ED_DOWNLOAD_STATE_PROCESSING');
	}

	/**
	 * Retrieves the requester
	 *
	 * @since	4.1
	 * @access	public
	 */
	public function getRequester()
	{
		$user = ED::user($this->userid);

		return $user;
	}

	/**
	 * Method used to send email notification to user who requested to download GDPR details.
	 * @since  4.1
	 * @access public
	 */
	public function sendNotification()
	{
		$jConfig = ED::jconfig();
		$my = ED::user($this->userid);
		$mailer = ED::mailer();

		$emailData = array();
		$emailData['downloadLink'] = $this->getDownloadLink(true);
		$emailData['actorName'] = $my->user->name;
		$emailData['emailTemplate'] = 'email.gdpr.ready.php';

		$body = $mailer->generateEmailBody($emailData);
		$email = $my->user->email;
		$subject = JText::_('COM_ED_EMAILS_GDPR_DOWNLOAD_SUBJECT');

		// add into mail queue
		$mailer->addQueue($email, $subject, $body);
		return true;
	}

	/**
	 * Method to ouput the zip file to browser for download.
	 * @since  4.1
	 * @access public
	 */
	public function showArchiveDownload()
	{
		$param = new JRegistry($this->params);
		$file = $param->get('path', '');

		if (! $file) {
			return false;
		}

		$user = ED::user($this->userid);

		$fileName =  JFilterOutput::stringURLSafe($user->getName());
		$fileName .= '.zip';

		header("Content-Type: application/zip");
		header("Content-Disposition: attachment; filename=$fileName");
		header("Content-Length: " . filesize($file));

		echo JFile::read($file);
		exit;
	}

	/**
	 * Method generate the download link of this request
	 *
	 * @since	4.1
	 * @access	public
	 */
	public function getDownloadLink($external = false)
	{
		$downloadLink = EDR::getRoutedURL('index.php?option=com_easydiscuss&view=download', false, $external);
		return $downloadLink;
	}

	/**
	 * Retrieves the expiration in days
	 *
	 * @since	4.1
	 * @access	public
	 */
	public function getExpireDays()
	{
		$days = ED::config()->get('main_userdownload_expiry');

		return $days;
	}

	/**
	 * Override parent delete method to manually delete archive file as well.
	 *
	 * @since	4.1
	 * @access	public
	 */
	public function delete($pk = null)
	{
		// delete archive file if there is any.
		$param = new JRegistry($this->params);
		$file = $param->get('path', '');

		if ($file) {
			if (JFile::exists($file)) {
				JFile::delete($file);
			}
		}

		return parent::delete($pk);
	}

}
