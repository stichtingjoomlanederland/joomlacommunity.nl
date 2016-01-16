<?php
/**
* @package		Komento
* @copyright	Copyright (C) 2012 Stack Ideas Private Limited. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* Komento is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Restricted access');

require_once( dirname( __FILE__ ) . DIRECTORY_SEPARATOR . 'parent.php' );

class KomentoTableHashkeys extends KomentoParentTable
{
	public $id		= null;
	public $uid		= null;
	public $type	= null;
	public $key		= null;
	public $state	= null;

	/**
	 * Constructor for this class.
	 *
	 * @return
	 * @param object $db
	 */
	function __construct( &$db )
	{
		parent::__construct( '#__komento_hashkeys' , 'id' , $db );
	}

	public function loadByKey( $key )
	{
		$sql = Komento::getSql();

		$sql->select( '#__komento_hashkeys' )
			->where( 'key', $key );

		$data	= $sql->loadObject();

		return parent::bind( $data );
	}

	public function store( $updateNulls = false )
	{
		if( empty( $this->key ) )
		{
			$this->key	= $this->generate();
		}

		return parent::store( $updateNulls );
	}

	/**
	 * Verify response
	 * @param	$response	The response code given.
	 * @return	boolean		True on success, false otherwise.
	 **/
	function verify( $response )
	{
	}

	/*
	 * Generates a hashkey
	 *
	 * @param	null
	 * @return	string	Returns an md5 generated key.
	 */
	public function generate()
	{
		return JString::substr( md5( $this->uid . $this->type . Komento::getDate()->toMySQL() ) , 0 , 12 );
	}
}
