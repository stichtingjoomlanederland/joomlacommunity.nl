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

class KomentoTableIpfilter extends KomentoParentTable
{
	public $id			= null;
	public $component	= null;
	public $ip			= null;
	public $rules		= null;

	public function __construct( &$db )
	{
		parent::__construct( '#__komento_ipfilter' , 'id' , $db );
	}

	public function loadComposite( $component, $ip )
	{
		$sql = Komento::getSql();

		$sql->select( '#__komento_ipfilter' )
			->where( 'component', $component )
			->where( 'ip', $ip );

		$data	= $sql->loadObject();

		return parent::bind( $data );
	}

	public function getRule()
	{
		if( is_string( $this->rules ) )
		{
			$this->rules = json_decode( $this->rules );
		}
		if( is_array( $this->rules ) )
		{
			$this->rules = (object) $this->rules;
		}

		return $this->rules;
	}

	public function store()
	{
		if( is_object( $this->rules ) || is_array( $this->rules ) )
		{
			$this->rules = json_encode( ( $this->rules ) );
		}

		parent::store();
	}
}
