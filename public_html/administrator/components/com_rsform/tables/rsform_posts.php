<?php
/**
* @package RSForm! Pro
* @copyright (C) 2007-2019 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/

defined('_JEXEC') or die('Restricted access');

class TableRSForm_Posts extends JTable
{
	/**
	 * Primary Key
	 *
	 * @var int
	 */
	public $form_id 	= null;
	public $enabled 	= 0;
	public $method	 	= 1;
	public $fields		= null;
	public $silent	 	= 1;
	public $url	 		= 'http://';

	protected $_jsonEncode = array('fields');
		
	/**
	 * Constructor
	 *
	 * @param object Database connector object
	 */
	public function __construct(& $db)
	{
		parent::__construct('#__rsform_posts', 'form_id', $db);
	}

	public function store($updateNulls = false)
	{
		$db = JFactory::getDbo();

		$query = $db->getQuery(true)
			->select($db->qn('form_id'))
			->from($db->qn('#__rsform_posts'))
			->where($db->qn('form_id') . ' = ' . $db->q($this->form_id));

		if (!$db->setQuery($query)->loadResult())
		{
			$object = (object) array(
				'form_id' => $this->form_id
			);

			$db->insertObject('#__rsform_posts', $object);
		}

		return parent::store($updateNulls);
	}

	public function load($keys = null, $reset = true)
	{
		$result = parent::load($keys, $reset);

		if (!empty($this->fields))
		{
			$this->fields = json_decode($this->fields);

			if (!is_array($this->fields))
			{
				$this->fields = array();
			}
		}

		return $result;
	}
}