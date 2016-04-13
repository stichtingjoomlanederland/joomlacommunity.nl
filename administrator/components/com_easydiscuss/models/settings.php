<?php
/**
* @package		EasyDiscuss
* @copyright	Copyright (C) 2010 Stack Ideas Private Limited. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasyDiscuss is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Restricted access');

require_once dirname( __FILE__ ) . '/model.php';

jimport('joomla.filesystem.folder');
jimport('joomla.filesystem.file');

class EasyDiscussModelSettings extends EasyDiscussAdminModel
{
	function &getThemes()
	{
		static $themes	= null;

		if( is_null( $themes ) )
		{
			$themes	= JFolder::folders( DISCUSS_THEMES );
		}

		return $themes;
	}

	/**
	 * Saves the settings
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return
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

	function &_getParams( $key = 'config' )
	{
		static $params	= null;

		if( is_null( $params ) )
		{
			$db		= DiscussHelper::getDBO();

			$query	= 'SELECT ' . $db->nameQuote( 'params' ) . ' '
					. 'FROM ' . $db->nameQuote( '#__discuss_configs' ) . ' '
					. 'WHERE ' . $db->nameQuote( 'name' ) . '=' . $db->Quote( $key );
			$db->setQuery( $query );

			$params	= $db->loadResult();
		}

		return $params;
	}

	function getConfig()
	{
		static $config	= null;

		if( is_null( $config ) )
		{
			$params		= $this->_getParams( 'config' );


			$config		= DiscussHelper::getRegistry( $params );
		}

		return $config;
	}
}
