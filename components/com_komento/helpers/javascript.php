<?php
/**
 * @package		Komento
 * @copyright	Copyright (C) 2012 Stack Ideas Private Limited. All rights reserved.
 * @license		GNU/GPL, see LICENSE.php
 *
 * Komento is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * See COPYRIGHT.php for copyright notices and details.
 */

defined('_JEXEC') or die('Restricted access');

class KomentoJavascriptHelper
{
	private $chain;

	public function __construct( &$chain )
	{
		$this->chain	=& $chain;

		return $this;
	}

	public function __set($property, $value)
	{
		$this->chain[] = array(
			'type'     => 'set',
			'property' => $property,
			'value'    => $value
		);

		return $this;
	}

	public function __get($property)
	{
		$this->chain[] = array(
			'type'     => 'get',
			'property' => $property
		);
		return $this;
	}

	public function __call($method, $args)
	{
		$this->chain[] = array(
			'type'     => 'call',
			'method'   => $method,
			'args'     => $args
		);

		return $this;
	}
}
