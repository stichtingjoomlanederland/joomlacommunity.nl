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
defined('_JEXEC') or die('Restricted access');

ED::import('admin:/tables/table');

class DiscussBan extends EasyDiscussTable
{
  	public $id = null;
  	public $banned_username = null;
  	public $userid = null;
  	public $ip = null;
  	public $blocked = null;
  	public $created_by = null;
  	public $start = null;
  	public $end = null;
  	public $reason = null;

	/**
	 * Constructor for this class.
	 *
	 * @return
	 * @param object $db
	 */
	function __construct(& $db)
	{
		parent::__construct('#__discuss_users_banned' , 'id' , $db);
	}
}
