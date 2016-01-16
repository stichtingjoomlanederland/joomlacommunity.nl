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

class DiscussAjaxHelper
{
	public function addCommand($type, &$data)
	{
		$this->commands[] = array(
			'type' => $type,
			'data' =>& $data
		);

		return $this;
	}

	/* This will handle all ajax commands e.g. success/fail/script */
	public function __call($method, $args)
	{
		$this->addCommand($method, $args);

		return $this;
	}

	public function Foundry($selector=null)
	{
		$chain = array();

		$this->addCommand('script', $chain);

		// Because we need to maintain the variable to be passed by reference,
		// we need to use an array instead as arguments.
		require_once DISCUSS_HELPERS . '/javascript.php';
		$js 	= new DiscussJavascriptHelper( $chain );

		if (isset($selector))
		{
			$js->Foundry($selector);
		}
		else
		{
			$js->Foundry;
		}

		return $js;
	}

	public function send()
	{
		// If no commands were added, just resolve this ajax request.
		if( empty( $this->commands ) )
		{
			$nulldata = array();
			$this->addCommand( 'resolve', $nulldata );
		}

		require_once DISCUSS_CLASSES . '/json.php';
		$json = new Services_JSON();

		$transport = JRequest::getVar('transport');

		$response = $json->encode( $this->commands );

		switch ($transport)
		{

			case "jsonp":
				header('Content-type: text/javascript; UTF-8');
				$callback = JRequest::getVar('callback');
				echo $callback . '(' . $response . ');';
				break;

			case "iframe":
				header('Content-type: text/html; UTF-8');
				echo '<textarea data-type="application/json" data-status="200" data-statusText="OK">' . $response . '</textarea>';
				break;

			default:
				header('Content-type: text/javascript; UTF-8');
				echo $response;
		}

		exit;
	}

	/**
	 * Processes all AJAX calls here
	 */
	public function process()
	{
		$namespace		= JRequest::getCmd( 'namespace' , '' );
		$isAjaxCall 	= JRequest::getCmd( 'format' ) == 'ajax' && !empty( $namespace );

		if( !$isAjaxCall )
		{
			return false;
		}

		//@task: Process namespace
		$namespace		= explode( '.' , $namespace );

		if( !JRequest::checkToken() && !JRequest::checkToken( 'get' ) )
		{
			echo 'Invalid token';
			exit;
		}

		// @rule: All calls should be made a minimum out of 3 parts of dots (.)
		if( count( $namespace ) < 4 )
		{
			$this->fail( JText::_( 'Invalid calls') );
			return $this->send();
		}

		/**
		 * Namespaces are broken into the following
		 *
		 * site.views.viewname.methodname - Front end ajax calls
		 * admin.views.viewname.methodname - Back end ajax calls
		 */
		list( $location , $type , $view , $method ) = $namespace;

		if( $type != 'views' )
		{
			$this->fail( JText::_( 'Currently only serving views' ) );
			return $this->send();
		}

		$location	= strtolower( $location );
		$view		= strtolower( $view );

		$path		= $location == 'admin' ? JPATH_ADMINISTRATOR : JPATH_ROOT;
		$path		.=  '/components/com_easydiscuss/views/' . $view . '/view.ajax.php';

		$class		= 'EasyDiscussView' . preg_replace( '/[^A-Z0-9_]/i' , '' , $view );

		if( !class_exists( $class ) )
		{
			jimport( 'joomla.filesystem.file' );

			if( !JFile::exists( $path ) )
			{
				$this->fail( JText::_( 'View file does not exist.') );
				return $this->send();
			}

			require_once( $path );
		}

		$object 	= new $class();
		$args 		= JRequest::getVar( 'args' , '' );

		if(!method_exists( $object , $method ) )
		{
			$this->fail( JText::sprintf( 'The method %1s does not exists.' , $method ) );
			return $this->send();
		}

		if( !empty( $args ) )
		{
			require_once DISCUSS_CLASSES . '/json.php';
			$json 	= new Services_JSON();
			$args 	= $json->decode( $args );

			if( !is_array( $args ) )
			{
				$args 	= array( $args );
			}

			call_user_func_array( array( $object , $method ) , $args );
		}
		else
		{
			$object->$method();
		}
		$this->send();
	}
}
