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

ED::import('admin:/tables/table');

class DiscussMailQueue extends EasyDiscussTable
{
	public $id = null;
	public $mailfrom = null;
	public $fromname = null;
	public $recipient = null;
	public $subject = null;
	public $body = null;
	public $created = null;
	public $ashtml = null;

	public function __construct(&$db)
	{
		parent::__construct('#__discuss_mailq', 'id', $db);
	}

	/**
	 * Retrieves the body of the email.
	 *
	 * @since	4.0
	 * @access	public
	 */
	public function getBody()
	{
		// if this object is not valid, do not futher process this item.
		if (!$this->id) {
			return false;
		}

		$body = $this->body;

		// If the body is not empty, we should just use this
		if (!empty($body)) {
			$body = ED::parser()->convert2validImgLink($body);
			
			return $body;
		}

		return false;
	}
}
