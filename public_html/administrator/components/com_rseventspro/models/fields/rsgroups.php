<?php
/**
* @package RSEvents!Pro
* @copyright (C) 2020 www.rsjoomla.com
* @license     GNU General Public License version 2 or later; see LICENSE
*/

defined('JPATH_PLATFORM') or die;
JFormHelper::loadFieldClass('list');

class JFormFieldRSGroups extends JFormFieldList
{
	/**
	 * The form field type.
	 *
	 * @var    string
	 * @since  11.1
	 */
	public $type = 'RSGroups';
	
	protected function getOptions() {
		$options = array();
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);
		
		$disabled = $this->element['used'];
		$disabled = explode(',', $disabled);
		
		$query->select('a.id AS value, a.title AS text, COUNT(DISTINCT b.id) AS level');
		$query->from($db->qn('#__usergroups') . ' AS a');
		$query->join('LEFT', $db->qn('#__usergroups') . ' AS b ON a.lft > b.lft AND a.rgt < b.rgt');
		$query->group('a.id, a.title, a.lft, a.rgt');
		$query->order('a.lft ASC');
		$db->setQuery($query);
		$options = $db->loadObjectList();

		for ($i = 0, $n = count($options); $i < $n; $i++) {
			$options[$i]->text = str_repeat('- ', $options[$i]->level) . $options[$i]->text;
			if (in_array($options[$i]->value, $disabled))
				$options[$i]->disable = true;
		}

		return $options;
	}
}