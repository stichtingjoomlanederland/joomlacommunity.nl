<?php
/**
* @package		EasyDiscuss
* @copyright	Copyright (C) Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasyDiscuss is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');

ED::import('admin:/tables/table');

class DiscussActivity extends EasyDiscussTable
{
	public $id = null;
	public $utype = null;
	public $uid = null;
	public $user_id = null;
	public $action = null;
	public $old = null;
	public $new = null;
	public $created = null;

	public function __construct(& $db)
	{
		parent::__construct('#__discuss_activities', 'id', $db);
	}

	/**
	 * Override's parent's implementation of the store
	 *
	 * @since	5.0
	 * @access	public
	 */
	public function store($alterOrdering = false)
	{
		// ensure the created date has value
		if (!$this->created) {
			$this->created = ED::date()->toSql();
		}

		// ensure the user_id (person who perform the action) is logged
		if (!$this->user_id) {
			$this->user_id = JFactory::getUser()->id;
		}

		$state = parent::store();
		return $state;
	}

	/**
	 * method to return icon associated with this activity log
	 *
	 * @since	5.0
	 * @access	public
	 */
	public function getIcon()
	{
		$icon = '';
		if ($this->utype && $this->action) {
			$lib = ED::activity();
			$icon = $lib->getActionIcon($this->utype, $this->action);
		}

		return $icon;
	}

	/**
	 * method to return activity log content
	 *
	 * @since	5.0
	 * @access	public
	 */
	public function getMessage()
	{
		$lib = ED::Activity();
		$content = $lib->translate($this);

		return $content;
	}

	/**
	 * method to return activity log content
	 *
	 * @since	5.0
	 * @access	public
	 */
	public function getLabel()
	{
		$label = ED::label($this->new);

		return $label->getTitle();
	}
}
