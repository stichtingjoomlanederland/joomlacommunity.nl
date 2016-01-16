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

class KomentoTableAcl extends KomentoParentTable
{
	/**
	 * The id of the acl
	 * @var int
	 */
	public $id			= null;

	/**
	 * The id of the user/usergroup
	 * @var int
	 */
	public $cid			= null;

	/**
	 * The component name
	 * @var string
	 */
	public $component	= null;

	/**
	 * The type of the acl
	 * @var int
	 */
	public $type		= null;

	/**
	 * The rules of the acl in json string
	 * @var string/json
	 */
	public $rules		= null;

	/**
	 * Constructor for this class.
	 *
	 * @return
	 * @param object $this->_db
	 */
	public function __construct( &$db )
	{
		parent::__construct( '#__komento_acl' , 'id' , $db );
	}

	public function compositeLoad( $cid, $type, $component, $reset = true )
	{
		if( $reset )
		{
			$this->reset();
		}

		$sql = Komento::getSql();

		$sql->select( '#__komento_acl' )
			->where( 'component', $component )
			->where( 'type', $type )
			->where( 'cid', $cid );

		$result = $sql->loadObject();

		if( empty( $result ) )
		{
			$this->cid = $cid;
			$this->type = $type;
			$this->component = $component;
		}
		else
		{
			$this->bind( $result );
		}

		return $this;
	}
}
