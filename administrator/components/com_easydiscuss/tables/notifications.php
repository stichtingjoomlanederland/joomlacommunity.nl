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

require_once(JPATH_ADMINISTRATOR . '/components/com_easydiscuss/includes/date/date.php');

ED::import('admin:/tables/table');

class DiscussNotifications extends EasyDiscussTable
{
	public $id = null;
	public $title = null;
	public $cid	= null;
	public $type = null;
	public $created	= null;
	public $target = null;
	public $author = null;
	public $permalink = null;
	public $state = null;
	public $favicon = null;
	public $component = 'com_easydiscuss';
	public $anonymous = 0;

	/**
	 * Constructor for this class.
	 *
	 * @return
	 * @param object $db
	 */
	public function __construct(&$db)
	{
		parent::__construct('#__discuss_notifications', 'id', $db);
	}

	/**
	 * Override parents implementation
	 **/
	public function store($updateNulls = false)
	{
		if (is_null($this->created)) {
			$this->created = ED::date()->toSql();
		}

		// Set the state to new
		if (is_null($this->state)) {
			$this->state = DISCUSS_NOTIFICATION_NEW;
		}

		return parent::store($updateNulls);
	}
}
