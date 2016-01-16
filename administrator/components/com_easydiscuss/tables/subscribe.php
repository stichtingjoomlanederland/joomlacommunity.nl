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

class DiscussSubscribe extends JTable
{
	public $id			= null;
	public $userid		= null;
	public $member		= null;
	public $type		= null;
	public $cid			= null;
	public $email		= null;
	public $fullname	= null;
	public $interval	= null;
	public $created		= null;
	public $sent_out	= null;

	/**
	 * Constructor for this class.
	 *
	 * @return
	 * @param object $db
	 */
	public function __construct(& $db )
	{
		parent::__construct( '#__discuss_subscription' , 'id' , $db );
	}

	public function store($updateNulls = false)
	{
		if( empty($this->email) && $this->userid ) {
			$user = JFactory::getUser( $this->userid );
			$this->fullname = $user->email();
		}

		if( empty($this->fullname) && $this->userid ) {
			$profile = DiscussHelper::getTable( 'Profile' );
			$profile->load( $this->userid );
			$this->fullname = $profile->getName();
		}

		if( empty($this->member) ) {
			$this->member = $this->userid ? 1 : 0;
		}

		if( empty($this->interval) ) {
			$this->interval = 'instant';
		}

		if( empty($this->created) ) {
			$this->created = DiscussHelper::getDate()->toMySQL();
		}

		if( empty($this->sent_out) ) {
			$this->sent_out = DiscussHelper::getDate()->toMySQL();
		}

		return parent::store( $updateNulls );
	}
}
