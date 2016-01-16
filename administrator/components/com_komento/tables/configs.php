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

class KomentoTableConfigs extends KomentoParentTable
{
	/**
	 * The key of the current config
	 * @var string
	 */
	public $component = null;

	/**
	 * Raw parameters values
	 * @var string
	 */
	public $params	= null;


	/**
	 * Constructor for this class.
	 *
	 * @return
	 * @param object $this->_db
	 */
	public function __construct( &$db )
	{
		parent::__construct( '#__komento_configs' , 'component' , $db );
	}

	public function store( $updateNulls = false )
	{
		$sql = Komento::getSql();

		$sql->select( '#__komento_configs' )
			->column( '1', '', 'count', true )
			->where( 'component', $this->component );

		$exists	= ( $sql->loadResult() > 0 ) ? true : false;

		$data				= new stdClass();
		$data->component	= $this->component;
		$data->params		= trim( $this->params );

		$database = Komento::getDBO();

		if( $exists )
		{
			return $database->updateObject( '#__komento_configs' , $data , 'component' );
		}

		return $database->insertObject( '#__komento_configs' , $data );
	}
}
