<?php
/**
* @package RSForm! Pro
* @copyright (C) 2007-2019 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/

defined('_JEXEC') or die('Restricted access');

class TableRSForm_Conditions extends JTable
{
	/**
	 * Primary Key
	 *
	 * @var int
	 */
	public $id;

	public $form_id;
	public $action;
	public $block;
	public $component_id;
	public $condition;
	public $lang_code;

	protected $_jsonEncode = array('component_id');
		
	/**
	 * Constructor
	 *
	 * @param object Database connector object
	 */
	public function __construct(& $db)
	{
		parent::__construct('#__rsform_conditions', 'id', $db);
	}

	public function load($keys = null, $reset = true)
	{
		$result = parent::load($keys, $reset);

		if ($result)
		{
			require_once JPATH_ADMINISTRATOR . '/components/com_rsform/helpers/conditions.php';

			$this->component_id = RSFormProConditions::parseComponentIds($this->component_id);

			$query = $this->_db->getQuery(true);
			$query->select('*')
				->from($this->_db->qn('#__rsform_condition_details'))
				->where($this->_db->qn('condition_id') . ' = ' . $this->_db->q($this->id));
			$this->_db->setQuery($query);
			$this->details = $this->_db->loadObjectList();
		}

		return $result;
	}

	public function save($src, $orderingFilter = '', $ignore = '')
	{
		$result = parent::save($src, $orderingFilter, $ignore);

		if ($result)
		{
			// Delete previous condition details
			$query = $this->_db->getQuery(true)
				->delete($this->_db->qn('#__rsform_condition_details'))
				->where($this->_db->qn('condition_id') . ' = ' . $this->_db->q($this->id));
			$this->_db->setQuery($query)
				->execute();

			if (isset($src['detail_component_id'], $src['operator'], $src['value']))
			{
				$component_ids 	= $src['detail_component_id'];
				$operators 		= $src['operator'];
				$values 		= $src['value'];

				for ($i = 0; $i < count($component_ids); $i++)
				{
					if (!isset($component_ids[$i], $operators[$i], $values[$i]))
					{
						continue;
					}

					$detail = JTable::getInstance('RSForm_Condition_Details', 'Table');
					$detail->save(array(
						'condition_id' 	=> $this->id,
						'component_id'	=> $component_ids[$i],
						'operator'		=> $operators[$i],
						'value'			=> $values[$i]
					));
				}
			}
		}

		return $result;
	}

	public function delete($pk = null)
	{
		$result = parent::delete($pk);

		if ($result)
		{
			$query = $this->_db->getQuery(true)
				->delete($this->_db->qn('#__rsform_condition_details'))
				->where($this->_db->qn('condition_id') . ' = ' . $this->_db->q($this->id));
			$this->_db->setQuery($query)
				->execute();
		}

		return $result;
	}
}