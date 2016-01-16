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

class DiscussMessageMeta extends JTable
{
	/**
	 * The unique id for the message.
	 * @var int
	 */
	public $id			= null;

	/**
	 * The unique id for the message.
	 * @var int
	 */
	public $messaging_id	= null;

	/**
	 * The message text
	 * @var string
	 */
	public $message		= null;

	/**
	 * The creation date of the message.
	 * @var datetime
	 */
	public $created		= null;

	/**
	 * Creator's user id.
	 * @var int
	 */
	public $created_by	= null;

	/**
	 * Determines if the message is the main message.
	 * @var int
	 */
	public $isparent = null;

	public function __construct(& $db )
	{
		parent::__construct( '#__discuss_messages_meta' , 'id' , $db );
	}
}
