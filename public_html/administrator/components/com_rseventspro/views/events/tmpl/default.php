<?php
/**
* @package RSEvents!Pro
* @copyright (C) 2020 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/
defined( '_JEXEC' ) or die( 'Restricted access' );
JHtml::_('behavior.keepalive'); ?>

<script type="text/javascript">
window.addEventListener('DOMContentLoaded', function() {
	var options = {};
	options.condition = '.rsepro-filter-operator';
	options.events = [{'#rsepro-filter-from' : 'rsepro_select'}];
	
	jQuery().rsjoomlafilter(options);
	
	<?php if ($this->tpl == 'timeline') { ?>	
	<?php if ($this->total_past > count($this->past)) { ?>
	jQuery('#rsepro_loadmore_past').on('click', function() {
		rspagination('events',jQuery('#rseprocontainer_past > tr').length,'past');
	});
	<?php } ?>
	<?php if ($this->total_ongoing > count($this->ongoing)) { ?>
	jQuery('#rsepro_loadmore_ongoing').on('click', function() {
		rspagination('events',jQuery('#rseprocontainer_ongoing > tr').length - 1,'ongoing');
	});
	<?php } ?>
	<?php if ($this->total_thisweek > count($this->thisweek)) { ?>
	jQuery('#rsepro_loadmore_thisweek').on('click', function() {
		rspagination('events',jQuery('#rseprocontainer_thisweek > tr').length - 1,'thisweek');
	});
	<?php } ?>
	<?php if ($this->total_thismonth > count($this->thismonth)) { ?>
	jQuery('#rsepro_loadmore_thismonth').on('click', function() {
		rspagination('events',jQuery('#rseprocontainer_thismonth > tr').length - 1,'thismonth');
	});
	<?php } ?>
	<?php if ($this->total_nextmonth > count($this->nextmonth)) { ?>
	jQuery('#rsepro_loadmore_nextmonth').on('click', function() {
		rspagination('events',jQuery('#rseprocontainer_nextmonth > tr').length - 1,'nextmonth');
	});
	<?php } ?>
	<?php if ($this->total_upcoming > count($this->upcoming)) { ?>
	jQuery('#rsepro_loadmore_upcoming').on('click', function() {
		rspagination('events',jQuery('#rseprocontainer_upcoming > tr').length - 1,'upcoming');
	});
	<?php } ?>
	<?php } ?>
});

function rsepro_show_preview() {
	if (typeof jQuery('input[name="cid[]"]:checked:first').val() != 'undefined') {
		window.open('<?php echo JURI::root(); ?>index.php?option=com_rseventspro&layout=show&id=' + jQuery('input[name="cid[]"]:checked:first').val());
	}
}
</script>

<form method="post" action="<?php echo JRoute::_('index.php?option=com_rseventspro&view=events'); ?>" name="adminForm" id="adminForm" autocomplete="off">
	<?php echo RSEventsproAdapterGrid::sidebar(); ?>
			
		<div class="rsepro-filter-container">
			<?php echo $this->loadTemplate('filter_'.$this->version); ?>
		</div>
		
		<?php echo $this->loadTemplate($this->tpl); ?>
	</div>
	
	<?php $selector = rseventsproHelper::isJ4() ? 'batchevents' : 'modal-batchevents'; ?>
	<?php echo JHtml::_('bootstrap.renderModal', $selector, array('title' => JText::_('COM_RSEVENTSPRO_BATCH_EVENTS'), 'footer' => $this->loadTemplate('batch_footer'), 'bodyHeight' => 70, 'height' => '100%', 'width'  => '100%', 'modalWidth'  => '80'), $this->loadTemplate('batch')); ?>

	<?php echo JHTML::_( 'form.token' ); ?>
	<?php if ($this->tpl == 'timeline') { ?>
	<input type="hidden" name="total_past" id="total_past" value="<?php echo $this->total_past; ?>" />
	<input type="hidden" name="total_ongoing" id="total_ongoing" value="<?php echo $this->total_ongoing; ?>" />
	<input type="hidden" name="total_thisweek" id="total_thisweek" value="<?php echo $this->total_thisweek; ?>" />
	<input type="hidden" name="total_thismonth" id="total_thismonth" value="<?php echo $this->total_thismonth; ?>" />
	<input type="hidden" name="total_nextmonth" id="total_nextmonth" value="<?php echo $this->total_nextmonth; ?>" />
	<input type="hidden" name="total_upcoming" id="total_upcoming" value="<?php echo $this->total_upcoming; ?>" />
	<?php } ?>
	<input type="hidden" name="boxchecked" value="0" />
	<input type="hidden" name="task" id="task" value="" />
	<input type="hidden" name="filter_order" id="filter_order" value="<?php echo $this->sortColumn; ?>" />
	<input type="hidden" name="filter_order_Dir" id="filter_order_Dir" value="<?php echo $this->sortOrder; ?>" />
	<input type="hidden" name="filter_from[]" value="">
	<input type="hidden" name="filter_condition[]" value="">
	<input type="hidden" name="search[]" value="">
	<input type="hidden" name="filter_status[]" value="">
	<input type="hidden" name="filter_featured[]" value="">
	<input type="hidden" name="filter_child[]" value="">
	<input type="hidden" name="filter_start[]" value="">
	<input type="hidden" name="filter_end[]" value="">
</form>