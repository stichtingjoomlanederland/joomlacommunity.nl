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

class DiscussMessage extends JTable
{
	/**
	 * The unique id for the message.
	 * @var int
	 */
	public $id			= null;

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
	 * Recipient's user id.
	 * @var int
	 */
	public $recipient 	= null;

	/**
	 * The last replied date for this message.
	 * @var datetime
	 */
	public $lastreplied	= null;

	/**
	 * These variables are only available when it's being loaded.
	 */
	protected $messageTable 	= null;

	public function __construct(& $db )
	{
		parent::__construct( '#__discuss_messages' , 'id' , $db );
	}

	/**
	 * Override parent's load behavior as we need to attach messageTable here.
	 *
	 * @since	3.0
	 * @access	public
	 */
	public function load( $id = null , $ignore = array() )
	{
		$state 	= parent::load( $id );

		if( !$state )
		{
			return $state;
		}

		return $state;
	}

	/**
	 * Override paren's delete implementation. Real deletion only occurs when 
	 * both the state records in {#__discuss_messages_states} has `deleted` marked as true.
	 *
	 * @since	3.0
	 * @access	public
	 * @param	null
	 */
	public function delete( $userId )
	{
		$model	= DiscussHelper::getModel( 'Messaging' );
		
		return $model->delete( $this->id , $userId );
	}

	/**
	 * Determines if the provided user id is a valid participant.
	 *
	 * @since	3.0
	 * @access	public
	 * @param	id 		The unique user id.
	 */
	public function isParticipant( $userId )
	{
		$model 	= DiscussHelper::getModel( 'Messaging' );
		return $model->isParticipant( $userId , $this->id );
	}

	/**
	 * Override parent's store implementation to add necessary logics.
	 *
	 * @since	3.0
	 * @access	public
	 * @param	null
	 */
	public function store()
	{
		$state 	= parent::store();

		// Now we need to create the necessary user states for
		// the author and the recipient.
		$model 	= DiscussHelper::getModel( 'Messaging' );
		$model->initStates( $this );

		return $state;
	}
}
