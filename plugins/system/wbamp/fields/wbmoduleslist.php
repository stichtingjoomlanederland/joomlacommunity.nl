<?php
/**
 * wbAMP - Accelerated Mobile Pages for Joomla!
 *
 * @author       Yannick Gaultier
 * @copyright    (c) Yannick Gaultier - Weeblr llc - 2016
 * @package      wbAmp
 * @license      http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @version      1.6.0.607
 * @date        2016-10-31
 */

defined('_JEXEC') or die();

JFormHelper::loadFieldClass('list');


/**
 * Select list with installed and published components
 *
 */
class JFormFieldWbmoduleslist extends JFormFieldList
{
	/**
	 * The form field type.
	 *
	 * @var    string
	 */
	protected $type = 'wbmenumoduleslist';

	private $_items = null;

	private $_hiddenModules = array();

	protected function getOptions()
	{
		$options = array();

		$items = array();
		$moduleType = $this->element['moduletype'] ? (string) $this->element['moduletype'] : '';
		$items = array_merge($items, (array) $this->getItems($moduleType));
		foreach ($items as $key => $item)
		{
			$tmp = array(
				'value' => $item['id'],
				'text' => $item['title']
			);
			$options[] = (object) $tmp;
		}

		$placeholder = $this->element['placeholder'] ? (string) $this->element['placeholder'] : 'PLG_SYSTEM_WBAMP_RULES_SELECT_ANY_MODULE';
		$default = array(
			'value' => '',
			'text' => JText::_($placeholder)
		);
		array_unshift($options, $default);
		reset($options);

		return $options;
	}

	private function getItems($moduleType)
	{
		if (is_null($this->_items))
		{
			try
			{
				$this->_items = array();
				$this->_items = ShlDbHelper::selectAssocList(
					'#__modules',
					array('id', 'title'),
					array('module' => $moduleType, 'published' => 1, 'client_id' => 0),
					$aWhereData = array(), $orderBy = array('title' => 'asc'), $offset = 0, $lines = 0,
					$key = 'id',
					$opType = ''
				);

				// remove common extensions:
				if (!empty($this->_items))
				{
					foreach ($this->_items as $key => $value)
					{
						if (in_array($key, $this->_hiddenModules))
						{
							unset($this->_items[$key]);
						}
					}
				}
			}
			catch (Exception $e)
			{
				ShlSystem_Log::error('wbamp', __METHOD__ . ' ' . $e->getMessage());
			}
		}

		return $this->_items;
	}
}
