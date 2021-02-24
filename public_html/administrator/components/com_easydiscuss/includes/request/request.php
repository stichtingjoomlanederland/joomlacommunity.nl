<?php
/**
* @package		EasyDiscuss
* @copyright	Copyright (C) Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasyDiscuss is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');

class EasyDiscussRequest
{
	public function __construct()
	{
		$this->app = JFactory::getApplication();
		$this->input = $this->app->input;
	}

	/**
	 * Creates a copy of it self and return to the caller.
	 *
	 * @since	1.0
	 * @access	public
	 *
	 */
	public static function factory()
	{
		return new self();
	}

	public function init()
	{
	    return $this;
	}

	public function getArray($type)
	{
		if (ED::isJoomla31()) {
			return $this->input->$type->getArray(array());
		}

		return JRequest::get($type);
	}

	/**
	 * Override the input's get method
	 *
	 * @param  [type] $name    [description]
	 * @param  [type] $default [description]
	 * @param  string $filter  [description]
	 * @return [type]          [description]
	 */
	public function get($name, $default = null, $filter = 'cmd')
	{
		return $this->input->get($name, $default, $filter);
	}


	/**
	 * Magic method to get an input object
	 *
	 * @param   mixed  $name  Name of the input object to retrieve.
	 *
	 * @return  JInput  The request input object
	 *
	 * @since   11.1
	 */
	public function __get($property)
	{
		return $this->input->$property;
	}

	public function __call($func, $args)
	{
		return call_user_func_array(array($this->input, $func), $args);
	}
}
