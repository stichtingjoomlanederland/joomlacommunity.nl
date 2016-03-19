<?php
/**
 * @package     FOF
 * @copyright   2010-2016 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license     GNU GPL version 2 or later
 */

namespace FOF30\Form\Field;

use FOF30\Form\Exception\DataModelRequired;
use FOF30\Form\Exception\GetStaticNotAllowed;
use FOF30\Form\FieldInterface;
use FOF30\Form\Form;
use FOF30\Model\DataModel;
use \JHtml;
use \JText;

defined('_JEXEC') or die;

/**
 * Form Field class for FOF
 * Renders the row ordering interface checkbox in browse views
 */
class Ordering extends \JFormField implements FieldInterface
{
	/**
	 * @var  string  Static field output
	 */
	protected $static;

	/**
	 * @var  string  Repeatable field output
	 */
	protected $repeatable;

	/**
	 * The Form object of the form attached to the form field.
	 *
	 * @var    Form
	 */
	protected $form;

	/**
	 * A monotonically increasing number, denoting the row number in a repeatable view
	 *
	 * @var  int
	 */
	public $rowid;

	/**
	 * The item being rendered in a repeatable form field
	 *
	 * @var  DataModel
	 */
	public $item;

	/**
	 * Method to get certain otherwise inaccessible properties from the form field object.
	 *
	 * @param   string  $name  The property name for which to the the value.
	 *
	 * @return  mixed  The property value or null.
	 *
	 * @since   2.0
	 */
	public function __get($name)
	{
		switch ($name)
		{
			case 'static':
				if (empty($this->static))
				{
					$this->static = $this->getStatic();
				}

				return $this->static;
				break;

			case 'repeatable':
				if (empty($this->repeatable))
				{
					$this->repeatable = $this->getRepeatable();
				}

				return $this->repeatable;
				break;

			default:
				return parent::__get($name);
		}
	}

	/**
	 * Method to get the field input markup for this field type.
	 *
	 * @since 2.0
	 *
	 * @return  string  The field input markup.
	 */
	protected function getInput()
	{
		$html = array();
		$attr = '';

		// Initialize some field attributes.
		$attr .= !empty($this->class) ? ' class="' . $this->class . '"' : '';
		$attr .= $this->disabled ? ' disabled' : '';
		$attr .= !empty($this->size) ? ' size="' . $this->size . '"' : '';

		// Initialize JavaScript field attributes.
		$attr .= !empty($this->onchange) ? ' onchange="' . $this->onchange . '"' : '';

		$this->item = $this->form->getModel();

		$keyfield = $this->item->getKeyName();
		$itemId   = $this->item->$keyfield;

		$query = $this->getQuery();

		// Create a read-only list (no name) with a hidden input to store the value.
		if ($this->readonly)
		{
			$html[] = JHtml::_('list.ordering', '', $query, trim($attr), $this->value, $itemId ? 0 : 1);
			$html[] = '<input type="hidden" name="' . $this->name . '" value="' . $this->value . '"/>';
		}
		else
		{
			// Create a regular list.
			$html[] = JHtml::_('list.ordering', $this->name, $query, trim($attr), $this->value, $itemId ? 0 : 1);
		}

		return implode($html);
	}

	/**
	 * Get the rendering of this field type for static display, e.g. in a single
	 * item view (typically a "read" task).
	 *
	 * @since 2.0
	 *
	 * @return  string  The field HTML
	 *
	 * @throws  \LogicException
	 */
	public function getStatic()
	{
		throw new GetStaticNotAllowed(__CLASS__);
	}

	/**
	 * Get the rendering of this field type for a repeatable (grid) display,
	 * e.g. in a view listing many item (typically a "browse" task)
	 *
	 * @since 2.0
	 *
	 * @return  string  The field HTML
	 *
	 * @throws  DataModelRequired
	 */
	public function getRepeatable()
	{
		if (!($this->item instanceof DataModel))
		{
			throw new DataModelRequired(__CLASS__);
		}

		$class = isset($this->class) ? $this->class : 'input-mini';
		$icon  = isset($this->element['icon']) ? $this->element['icon'] : 'icon-menu';

		$html = '';

		$view = $this->form->getView();

		$ordering = $view->getLists()->order == $this->item->getFieldAlias('ordering');

		if (!$view->hasAjaxOrderingSupport())
		{
			// Ye olde Joomla! 2.5 method
			$disabled = $ordering ? '' : 'disabled="disabled"';
			$html .= '<span>';
			$html .= $view->getPagination()->orderUpIcon($this->rowid, true, 'orderup', 'Move Up', $ordering);
			$html .= '</span><span>';
			$html .= $view->getPagination()->orderDownIcon($this->rowid, $view->getPagination()->total, true, 'orderdown', 'Move Down', $ordering);
			$html .= '</span>';
			$html .= '<input type="text" name="order[]" size="5" value="' . $this->value . '" ' . $disabled;
			$html .= 'class="text-area-order" style="text-align: center" />';
		}
		else
		{
			// The modern drag'n'drop method
			if ($view->getPerms()->editstate)
			{
				$disableClassName = '';
				$disabledLabel = '';

				$hasAjaxOrderingSupport = $view->hasAjaxOrderingSupport();

				if (!$hasAjaxOrderingSupport['saveOrder'])
				{
					$disabledLabel = JText::_('JORDERINGDISABLED');
					$disableClassName = 'inactive tip-top';
				}

				$orderClass = $ordering ? 'order-enabled' : 'order-disabled';

				$html .= '<div class="' . $orderClass . '">';
				$html .= '<span class="sortable-handler ' . $disableClassName . '" title="' . $disabledLabel . '" rel="tooltip">';
				$html .= '<i class="' . $icon . '"></i>';
				$html .= '</span>';

				if ($ordering)
				{
					$html .= '<input type="text" name="order[]" size="5" class="' . $class . ' text-area-order" value="' . $this->value . '" />';
				}

				$html .= '</div>';
			}
			else
			{
				$html .= '<span class="sortable-handler inactive" >';
				$html .= '<i class="' . $icon . '"></i>';
				$html .= '</span>';
			}
		}

		return $html;
	}

	/**
	 * Builds the query for the ordering list.
	 *
	 * @since 2.3.2
	 *
	 * @return \JDatabaseQuery  The query for the ordering form field
	 */
	protected function getQuery()
	{
		$ordering = $this->name;
		$title    = $this->element['ordertitle'] ? (string) $this->element['ordertitle'] : $this->item->getFieldAlias('title');

		$db = $this->form->getContainer()->platform->getDbo();
		$query = $db->getQuery(true);
		$query->select(array($db->quoteName($ordering, 'value'), $db->quoteName($title, 'text')))
				->from($db->quoteName($this->item->getTableName()))
				->order($ordering);

		return $query;
	}
}
