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

jimport('joomla.filesystem.folder');
jimport('joomla.filesystem.file');

require_once(__DIR__ . '/model.php');

class EasyDiscussModelSettings extends EasyDiscussAdminModel
{
	public function getThemes()
	{
		static $themes = null;

		if (is_null($themes)) {
			$themes	= JFolder::folders(DISCUSS_THEMES);
		}

		return $themes;
	}

	/**
	 * Saves the settings
	 *
	 * @since	4.0
	 * @access	public
	 */
	public function save($data)
	{
		$config = ED::table('Configs');
		$config->load('config');

		$registry = new JRegistry();
		$registry->loadString($this->_getParams());

		foreach ($data as $index => $value) {

			// If the value is an array, we would assume that it should be comma separated
			if (is_array($value)) {
				$value = implode(',', $value);
			}

			$registry->set($index, $value);
		}

		// Get the complete INI string
		$config->name = 'config';
		$config->params	= $registry->toString('INI');

		// Save it
		if (!$config->store()) {
			return false;
		}

		return true;
	}

	public function _getParams($key = 'config')
	{
		static $params	= null;

		if( is_null( $params ) )
		{
			$db		= ED::db();

			$query	= 'SELECT ' . $db->nameQuote( 'params' ) . ' '
					. 'FROM ' . $db->nameQuote( '#__discuss_configs' ) . ' '
					. 'WHERE ' . $db->nameQuote( 'name' ) . '=' . $db->Quote( $key );
			$db->setQuery( $query );

			$params	= $db->loadResult();
		}

		return $params;
	}

	public function getConfig()
	{
		static $config = null;

		if (is_null($config)) {
			$params	= $this->_getParams('config');


			$config = ED::getRegistry($params);
		}

		return $config;
	}
}
