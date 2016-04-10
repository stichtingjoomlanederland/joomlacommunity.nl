<?php
/**
* @package RSForm! Pro
* @copyright (C) 2007-2014 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/

defined('_JEXEC') or die('Restricted access');

class TableRSForm_Mappings extends JTable
{
	public $id;
	
	public $formId;
	public $connection = 0;
	public $host;
	public $driver;
	public $port = 3306;
	public $username;
	public $password;
	public $database;
	public $method = 0;
	public $table;
	public $data;
	public $wheredata;
	public $extra;
	public $andor;
	public $ordering;
	
	public function __construct(& $db) {
		parent::__construct('#__rsform_mappings', 'id', $db);
	}
}