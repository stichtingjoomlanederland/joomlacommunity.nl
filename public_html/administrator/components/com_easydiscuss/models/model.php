<?php
/**
* @package		EasyDiscuss
* @copyright	Copyright (C) 2010 - 2017 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasyDiscuss is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');

jimport('joomla.application.component.model');

if (class_exists('JModelAdmin')) {
	class EasyDiscussAdminMainModel extends JModelAdmin
	{
		public function getForm($data = array(), $loadData = true)
		{
		}
	}
} else {
	class EasyDiscussAdminMainModel extends JModel { }
}

class EasyDiscussAdminModel extends EasyDiscussAdminMainModel
{
	public function __construct()
	{
		parent::__construct();

		$this->app = JFactory::getApplication();
		$this->input = ED::request();
		$this->config = ED::config();
		$this->my = JFactory::getUser();
		$this->db = ED::db();
	}

	/**
	 * Retrieves the data from the state
	 *
	 * @since	4.0
	 * @access	public
	 */
	public function getStateFromRequest($name, $default, $filter)
	{
		$namespace = 'com_easydiscuss.' . $this->name . '.' . $name;

		$value = $this->app->getUserStateFromRequest($namespace, $name, $default, $filter);

		return $value;
	}

	public function getForm($data = array(), $loadData = true)
	{
	}

	protected function populateState()
	{
		// Load the parameters.
		$value = JComponentHelper::getParams($this->option);
		$this->setState('params', $value);
	}

	/**
	 * Used to split search fragments up
	 *
	 * @since	4.0.15
	 * @access	public
	 */
	protected function getSearchFragments($query)
	{
		$fragments = explode(':', $query);

		$search = new stdClass();

		if (count($fragments) <= 1) {
			$search->type = 'standard';
			$search->query = $query;
			
			return $search;
		}

		$search->type = strtolower($fragments[0]);
		$search->query = $fragments[1];
		
		return $search;
	}
}
