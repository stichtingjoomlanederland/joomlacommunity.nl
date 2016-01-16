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

class KomentoDatabaseHelper
{
	public $helper = null;

	public function __construct()
	{
		$version 	= Komento::joomlaVersion();
		$className	= $version >= '2.5' ? 'KomentoDatabaseJoomla30' : 'KomentoDatabaseJoomla15';

		$this->helper 	= new $className();
	}

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

		return call_user_func_array( array( $this->helper , $method ) , $refArray );
	}

	function getTables()
	{
		$db = Komento::getDBO();
		$db->setQuery('SHOW TABLES');
		$result = $db->loadResultArray();

		return $result;
	}

	function getColumns( $tableName )
	{
		$db = Komento::getDBO();
		$query	= 'SHOW FIELDS FROM ' . $db->nameQuote( $tableName );
		$db->setQuery( $query );

		$result	= $db->loadObjectList();

		$fields = array();

		foreach( $result as $column )
		{
			$fields[] = $column->Field;
		}

		return $fields;
	}

	function isTableExists( $tableName )
	{
		$db = Komento::getDBO();
		$query	= 'SHOW TABLES LIKE ' . $db->quote($tableName);
		$db->setQuery( $query );

		return (boolean) $db->loadResult();
	}

	function isColumnExists( $tableName, $columnName )
	{
		$db = Komento::getDBO();
		$query	= 'SHOW FIELDS FROM ' . $db->nameQuote( $tableName );
		$db->setQuery( $query );

		$fields	= $db->loadObjectList();

		$result = array();

		foreach( $fields as $field )
		{
			$result[ $field->Field ]	= preg_replace( '/[(0-9)]/' , '' , $field->Type );
		}

		if( array_key_exists($columnName, $result) )
		{
			return true;
		}

		return false;
	}

	function isIndexKeyExists( $tableName, $indexName )
	{
		$db = Komento::getDBO();
		$query	= 'SHOW INDEX FROM ' . $db->nameQuote( $tableName );
		$db->setQuery( $query );
		$indexes	= $db->loadObjectList();

		$result = array();

		foreach( $indexes as $index )
		{
			$result[ $index->Key_name ]	= preg_replace( '/[(0-9)]/' , '' , $index->Column_name );
		}

		if( array_key_exists($indexName, $result) )
		{
			return true;
		}

		return false;
	}
}

class KomentoDatabaseJoomla15 extends KomentoDatabaseHelper
{
	public $db 		= null;

	public function __construct()
	{
		$this->db	= JFactory::getDBO();
	}

	public function loadColumn()
	{
		return $this->loadResultArray();
	}

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

		return call_user_func_array( array( $this->db , $method ) , $refArray );
	}
}

class KomentoDatabaseJoomla30 extends KomentoDatabaseJoomla15
{
	public function loadResultArray()
	{
		return $this->db->loadColumn();
	}

	public function nameQuote( $str )
	{
		return $this->db->quoteName( $str );
	}
}
