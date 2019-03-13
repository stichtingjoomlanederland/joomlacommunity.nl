<?php
/**
* @package		EasyDiscuss
* @copyright	Copyright (C) 2010 - 2018 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasyDiscuss is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');

class EasyDiscussConnector extends EasyDiscuss
{
	public $adapter	= null;

	public function __construct()
	{
		parent::__construct();

		$path = dirname( __FILE__ ) . '/adapters/curl.php';

		require_once($path);
		
		$this->adapter = new EasyDiscussConnectorCurl();
	}

	public function getResult($url = null, $withHeaders = false)
	{
		return $this->adapter->getResult($url, $withHeaders);
	}

	public function getResults()
	{
		return $this->adapter->getResults();
	}

	public function addUrl( $url )
	{
		$this->adapter->addUrl( $url );

		return $this;
	}

	public function setMethod( $method = 'GET' )
	{
		$this->adapter->setMethod( $method );

		return $this;
	}

	public function addLength( $length )
	{
		$this->adapter->addLength( $length );

		return $this;
	}

	public function useHeaders()
	{
		$this->adapter->useHeadersOnly();

		return $this;
	}

	public function connect()
	{
		// dump( $this->adapter->args );
		$this->adapter->execute();

		return $this;
	}

	/**
	 * Maps back the call method functions to the helper.
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string	Method's name.
	 * @param	mixed	Arguments
	 * @return
	 */
	public function __call( $method , $args )
	{
		$refArray	= array();

		if( $args )
		{
			foreach( $args as &$arg )
			{
				$refArray[]	=& $arg;
			}
		}
		return call_user_func_array( array( $this->adapter , $method ) , $refArray );
	}

}
