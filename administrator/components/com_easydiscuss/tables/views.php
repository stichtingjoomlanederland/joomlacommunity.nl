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

class DiscussViews extends JTable
{
	public $id		= null;
	public $user_id	= null;
	public $hash	= null;
	public $created	= null;
	public $ip		= null;

	/**
	 * Constructor for this class.
	 *
	 * @return
	 * @param object $db
	 */
	public function __construct(& $db )
	{
		parent::__construct( '#__discuss_views' , 'id' , $db );
	}

	public function loadByUser( $id )
	{
		$db		= DiscussHelper::getDBO();
		$query	= 'SELECT * FROM ' . $db->nameQuote( $this->_tbl ) . ' '
				. 'WHERE ' . $db->nameQuote( 'user_id' ) . '=' . $db->Quote( $id );
		$db->setQuery( $query );
		$result	= $db->loadObject();

		if( !$result )
		{
			return false;
		}

		return parent::bind( $result );
	}

	public function updateView( $userId , $url )
	{
		if( !$this->loadByUser( $userId ) )
		{
			// Store a new record for the user.
			$this->set( 'user_id'	, $userId );
		}

		$this->set( 'hash'		, $this->getHash( $url ) );
		$this->set( 'created'	, DiscussHelper::getDate()->toMySQL() );
		$this->set( 'ip'		, $_SERVER['REMOTE_ADDR' ] );

		return $this->store();
	}

	public function getHash( $url )
	{
		return md5( $url );
	}
}
