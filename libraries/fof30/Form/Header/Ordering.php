<?php
/**
 * @package     FOF
 * @copyright   2010-2016 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license     GNU GPL version 2 or later
 */

namespace FOF30\Form\Header;

use JHtml;
use JText;

defined('_JEXEC') or die;

/**
 * Ordering field header
 */
class Ordering extends Field
{
	/**
	 * Get the header
	 *
	 * @return  string  The header HTML
	 */
	protected function getHeader()
	{
		$sortable = ($this->element['sortable'] != 'false');

		if (!$sortable)
		{
			// Non sortable?! I'm not sure why you'd want that, but if you insist...
			return JText::_('JGRID_HEADING_ORDERING');
		}

		$iconClass = isset($this->element['iconClass']) ? (string) $this->element['iconClass'] : 'icon-menu-2';
		$class     = isset($this->element['class']) ? (string) $this->element['class'] : 'btn btn-micro pull-right';

		$view  = $this->form->getView();
		$model = $this->form->getModel();

		// Drag'n'drop ordering support WITH a save order button
		$html = JHtml::_(
			'grid.sort',
			'<i class="' . $iconClass . '"></i>',
			'ordering',
			$view->getLists()->order_Dir,
			$view->getLists()->order,
			null,
			'asc',
			'JGRID_HEADING_ORDERING'
		);

		$ordering = $view->getLists()->order == 'ordering';

		if ($ordering)
		{
			$html .= '<a href="javascript:saveorder(' . (count($model->get()) - 1) . ', \'saveorder\')" ' .
				'rel="tooltip" class="save-order ' . $class . '" title="' . JText::_('JLIB_HTML_SAVE_ORDER') . '">'
				. '<span class="icon-ok"></span></a>';
		}

		return $html;
	}
}
