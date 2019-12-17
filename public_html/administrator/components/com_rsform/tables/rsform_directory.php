<?php
/**
* @package RSForm! Pro
* @copyright (C) 2007-2019 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/

defined('_JEXEC') or die('Restricted access');

class TableRSForm_Directory extends JTable
{		
	public $ViewLayoutAutogenerate = 1;
	public $ViewLayoutName = 'dir-inline';
	
	/**
	 * Constructor
	 *
	 * @param object Database connector object
	 */
	 
	public function __construct(& $db) {
		parent::__construct('#__rsform_directory', 'formId', $db);
	}

	public function store($updateNulls = false)
	{
		$db 	= JFactory::getDbo();
		$key 	= $this->getKeyName();
		$table	= $this->getTableName();

		$query = $db->getQuery(true)
			->select($db->qn($key))
			->from($db->qn($table))
			->where($db->qn($key) . ' = ' . $db->q($this->{$key}));

		if (!$db->setQuery($query)->loadResult())
		{
			$object = (object) array(
				$key => $this->{$key}
			);

			$db->insertObject($table, $object);
		}

		return parent::store($updateNulls);
	}
}