<?php
/**
* @package RSEvents!Pro
* @copyright (C) 2020 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/
defined( '_JEXEC' ) or die( 'Restricted access' ); ?>

<script type="text/javascript">
function rs_clear() {
	jQuery('#searchstring').val('');
	jQuery('#state').val('-');
	document.adminForm.submit();
}
</script>

<form method="post" action="<?php echo rseventsproHelper::route('index.php?option=com_rseventspro&layout=unsubscribers&id='.rseventsproHelper::sef($this->row->id,$this->row->name)); ?>" name="adminForm" id="adminForm">
	
	<h1><?php echo JText::sprintf('COM_RSEVENTSPRO_UNSUBSCRIBERS_FOR',$this->row->name); ?></h1>
	
	<div class="<?php echo RSEventsproAdapterGrid::row(); ?>">
		<div class="<?php echo RSEventsproAdapterGrid::column(12); ?>">
			<?php echo RSEventsproAdapterGrid::inputGroup('<input type="text" name="search" id="searchstring" onchange="adminForm.submit();" value="'.$this->filter_word.'" class="form-control" />', null, '<button type="button" class="btn btn-primary hasTooltip" title="'.JText::_('COM_RSEVENTSPRO_GLOBAL_SEARCH').'" onclick="adminForm.submit();"><i class="fa fa-search"></i></button> <button type="button" class="btn btn-danger hasTooltip" title="'.JText::_('COM_RSEVENTSPRO_GLOBAL_CLEAR').'" onclick="rs_clear();"><i class="fa fa-times"></i></button>'); ?>
		</div>
	</div>
	
	<br /><br />
	
	<a href="<?php echo rseventsproHelper::route('index.php?option=com_rseventspro&layout=show&id='.rseventsproHelper::sef($this->row->id,$this->row->name),false,rseventsproHelper::itemid($this->row->id)); ?>"><?php echo JText::_('COM_RSEVENTSPRO_GLOBAL_BACK'); ?></a> <?php echo JText::_('COM_RSEVENTSPRO_GLOBAL_OR'); ?> <a href="<?php echo rseventsproHelper::route('index.php?option=com_rseventspro&task=rseventspro.exportunsubscribers&id='.rseventsproHelper::sef($this->row->id,$this->row->name)); ?>"><?php echo JText::_('COM_RSEVENTSPRO_SUBSCRIBERS_EXPORT_UNSUBSCRIBERS'); ?></a> <br />
	<div class="rs_clear"></div>

	<?php $count = count($this->data); ?>
	<?php if (!empty($this->data)) { ?>
	<ul class="rs_events_container" id="rs_events_container">
	<?php foreach($this->data as $row) { ?>
	<li class="rs_event_detail">
		<div class="rs_options" style="display:none;">
			<a href="<?php echo rseventsproHelper::route('index.php?option=com_rseventspro&task=rseventspro.removeunsubscriber&id='.rseventsproHelper::sef($row->id,$row->name).'&ide='.rseventsproHelper::sef($this->row->id,$this->row->name),false); ?>" onclick="return confirm('<?php echo JText::_('COM_RSEVENTSPRO_DELETE_UNSUBSCRIBER_CONFIRMATION'); ?>');">
				<i class="fa fa-trash fa-fw"></i>
			</a>
		</div>
		<div class="rs_event_details rs_inline">
			<?php echo $row->name; ?> <br />
			<?php echo $row->email; ?> <br />
			<?php echo rseventsproHelper::showdate($row->date,null,true); ?>
		</div>
	</li>
	<?php } ?>
	</ul>
	<div class="rs_loader" id="rs_loader" style="display:none;">
		<?php echo JHtml::image('com_rseventspro/loader.gif', '', array(), true); ?> 
	</div>
	<?php if ($this->total > $count) { ?>
		<p id="rsepro_number_events"><?php echo JText::sprintf('COM_RSEVENTSPRO_SHOWING_UNSUBSCRIBERS','<span>'.$count.'</span>',$this->total); ?></p>
		<a class="rs_read_more" id="rsepro_loadmore"><?php echo JText::_('COM_RSEVENTSPRO_GLOBAL_LOAD_MORE'); ?></a>
	<?php } ?>
	<span id="total" class="rs_hidden"><?php echo $this->total; ?></span>
	<span id="Itemid" class="rs_hidden"><?php echo JFactory::getApplication()->input->getInt('Itemid'); ?></span>
	<?php } else echo JText::_('COM_RSEVENTSPRO_NO_UNSUBSCRIBERS'); ?>
	
	<?php echo JHTML::_( 'form.token' ); ?>
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="option" value="com_rseventspro" />
	<input type="hidden" name="view" value="rseventspro" />
</form>

<script type="text/javascript">
	jQuery(document).ready(function(){
		<?php if ($this->total > $count) { ?>
		jQuery('#rsepro_loadmore').on('click', function() {
			rspagination('unsubscribers',jQuery('#rs_events_container > li').length,<?php echo $this->row->id; ?>);
		});
		<?php } ?>
		
		<?php if (!empty($count)) { ?>
		jQuery('#rs_events_container li').on({
			mouseenter: function() {
				jQuery(this).find('div.rs_options').css('display','');
			},
			mouseleave: function() {
				jQuery(this).find('div.rs_options').css('display','none');
			}
		});
		<?php } ?>
	});
</script>