<?php
/**
* @package RSJoomla! Adapter
* @copyright (C) 2015 www.rsjoomla.com
* @license GPL, http://www.gnu.org/licenses/gpl-2.0.html
*/

defined('_JEXEC') or die('Restricted access');

/**
 * Utility class for Tabs.
 *
 * @package     RSJoomla!
 */
class RSFilterBar {
	
	// Show search bar
	public $search;
	// show additional items located in the right
	public $rightItems = array();
	// Ordering 
	public $listDirn = '';
	public $listOrder = '';
	
	public function __construct($options = array()) {
		foreach ($options as $k => $v) {
			$this->{$k} = $v;
		}
	}
	
	protected function escape($string) {
		return htmlentities($string, ENT_COMPAT, 'utf-8');
	}
	
	public function show() {
		?>
		<fieldset id="filter-bar">
			<?php if ($this->search) { ?>
			<div class="filter-search fltlft">
				<label class="filter-search-lbl" for="filter_search"><?php echo $this->search['label']; ?></label>
				<input type="text" name="filter_search" id="filter_search" value="<?php echo $this->escape($this->search['value']); ?>" />
				<button type="submit"><?php echo JText::_('JSEARCH_FILTER_SUBMIT'); ?></button>
				<button type="button" onclick="document.id('filter_search').value='';this.form.submit();"><?php echo JText::_('JSEARCH_RESET'); ?></button>
			</div>
			<?php } ?>
			<?php if ($this->rightItems) { ?>
				<?php foreach ($this->rightItems as $item) { ?>
				<div class="filter-select fltrt">
					<?php echo $item['input']; ?>
				</div>
				<?php } ?>
			<?php } ?>
		</fieldset>
		<input type="hidden" name="filter_order" value="<?php echo $this->escape($this->listOrder); ?>" />
		<input type="hidden" name="filter_order_Dir" value="<?php echo $this->escape($this->listDirn); ?>" />
		<div class="clr"> </div>
		<?php
	}
	
	public function orderingHead($items, $task) {
		$html	= array();
		$html[] = '<th width="6%" class="center hidden-phone" nowrap="nowrap">';
		$html[] = JHtml::_('grid.sort', 'COM_RSEVENTSPRO_GLOBAL_ORDERING', 'ordering', $this->listDirn, $this->listOrder);
		$html[] = JHtml::_('grid.order',$items, 'filesave.png', $task.'.saveorder');
		$html[] = '</th>';
		
		return implode(' ',$html);
	}
	
	public function orderingBody($order, $default, $pagination, $i, $total, $task) {
		$disabled = true;
		$html	= array();
		$html[] = '<td class="order nowrap center hidden-phone">';
		if ($this->listOrder == $default) {
			$html[] = '<span>'.$pagination->orderUpIcon($i, true, $task.'.orderup', 'JLIB_HTML_MOVE_UP', 'ordering').'</span>';
			$html[] = '<span>'.$pagination->orderDownIcon($i, $total, true, $task.'.orderdown', 'JLIB_HTML_MOVE_DOWN', 'ordering').'</span>';
			$disabled = false;
		}
		
		$disabled = $disabled ? 'disabled="disabled"' : '';
		$html[] = '<input type="text" name="order[]" size="5" '.$disabled.' value="'.$order.'" class="text-area-order" />';
		$html[] = '</td>';
		
		return implode(' ',$html);
	}
}