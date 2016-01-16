<?php
/**
* @package RSEvents!Pro
* @copyright (C) 2015 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/
defined( '_JEXEC' ) or die( 'Restricted access' );?>
<h1><?php echo JText::sprintf('COM_RSEVENTSPRO_SUBSCRIBERS',$this->row->name); ?></h1>

<script type="text/javascript">
function rs_clear() {
	jQuery('#searchstring').val('');
	jQuery('#state').val('-');
	jQuery('#ticket').val('-');
	document.adminForm.submit();
}
</script>

<form method="post" action="<?php echo rseventsproHelper::route('index.php?option=com_rseventspro&layout=subscribers&id='.rseventsproHelper::sef($this->row->id,$this->row->name)); ?>" name="adminForm" id="adminForm">
	<div class="input-append">
		<input type="text" name="search" id="searchstring" onchange="adminForm.submit();" value="<?php echo $this->filter_word; ?>" size="35" /> 
		<button type="button" class="button btn" onclick="adminForm.submit();"><?php echo JText::_('COM_RSEVENTSPRO_GLOBAL_SEARCH'); ?></button> 
		<button type="button" class="button btn" onclick="rs_clear();"><?php echo JText::_('COM_RSEVENTSPRO_GLOBAL_CLEAR'); ?></button>
	</div>

	
	<?php echo $this->lists['tickets']; ?>
	<?php echo $this->lists['state']; ?>
	

	<div class="rs_clear"></div>
	
	<br /><br />
	
	<a href="<?php echo rseventsproHelper::route('index.php?option=com_rseventspro&layout=show&id='.rseventsproHelper::sef($this->row->id,$this->row->name),false,rseventsproHelper::itemid($this->row->id)); ?>"><?php echo JText::_('COM_RSEVENTSPRO_GLOBAL_BACK'); ?></a> <?php echo JText::_('COM_RSEVENTSPRO_GLOBAL_OR'); ?> <a href="<?php echo rseventsproHelper::route('index.php?option=com_rseventspro&task=rseventspro.exportguests&id='.rseventsproHelper::sef($this->row->id,$this->row->name)); ?>"><?php echo JText::_('COM_RSEVENTSPRO_SUBSCRIBERS_EXPORT_SUBSCRIBERS'); ?></a> <br />
	<div class="rs_clear"></div>
	
	<?php $count = count($this->data); ?>
	<?php if (!empty($this->data)) { ?>
	<ul class="rs_events_container" id="rs_events_container">
	<?php foreach($this->data as $row) { ?>
	<li class="rs_event_detail">
		<div class="rs_options" style="display:none;">
			<a href="<?php echo rseventsproHelper::route('index.php?option=com_rseventspro&layout=editsubscriber&id='.rseventsproHelper::sef($row->id,$row->name)); ?>">
				<i class="fa fa-pencil fa-fw"></i>
			</a>
			<a href="<?php echo rseventsproHelper::route('index.php?option=com_rseventspro&task=rseventspro.removesubscriber&id='.rseventsproHelper::sef($row->id,$row->name)); ?>"  onclick="return confirm('<?php echo JText::_('COM_RSEVENTSPRO_DELETE_SUBSCRIBER_CONFIRMATION'); ?>');">
				<i class="fa fa-trash fa-fw"></i>
			</a>
			<a href="<?php echo rseventsproHelper::route('index.php?option=com_rseventspro&task=rseventspro.approve&id='.rseventsproHelper::sef($row->id,$row->name)); ?>" title="<?php echo JText::_('COM_RSEVENTSPRO_GLOBAL_APPROVE'); ?>">
				<i class="fa fa-check fa-fw"></i>
			</a>
			<a href="<?php echo rseventsproHelper::route('index.php?option=com_rseventspro&task=rseventspro.pending&id='.rseventsproHelper::sef($row->id,$row->name)); ?>" title="<?php echo JText::_('COM_RSEVENTSPRO_GLOBAL_PENDING'); ?>">
				<i class="fa fa-exclamation-triangle fa-fw"></i>
			</a>
			<a href="<?php echo rseventsproHelper::route('index.php?option=com_rseventspro&task=rseventspro.denied&id='.rseventsproHelper::sef($row->id,$row->name)); ?>" title="<?php echo JText::_('COM_RSEVENTSPRO_GLOBAL_DENIED'); ?>">
				<i class="fa fa-minus-circle fa-fw"></i>
			</a>
		</div>
		<div class="rs_event_details rs_inline">
			<a href="<?php echo rseventsproHelper::route('index.php?option=com_rseventspro&layout=editsubscriber&id='.rseventsproHelper::sef($row->id,$row->name)); ?>"><?php echo $row->name; ?></a> 
			<?php if ($row->gateway) { ?>(<?php echo rseventsproHelper::getPayment($row->gateway); ?>)<?php } ?> <br />
			<?php echo rseventsproHelper::showdate($row->date,null,true); ?> <br />
			<a href="<?php echo rseventsproHelper::route('index.php?option=com_rseventspro&layout=editsubscriber&id='.rseventsproHelper::sef($row->id,$row->name)); ?>"><?php echo $row->email; ?></a> - <?php echo $this->getUser($row->idu); ?> - <?php echo $row->ip; ?>
		</div>
		<div class="rs_status"><?php echo JText::_('COM_RSEVENTSPRO_GLOBAL_STATUS'); ?>: <?php echo $this->getStatus($row->state); ?></div>
	</li>
	<?php } ?>
	</ul>
	<div class="rs_loader" id="rs_loader" style="display:none;">
		<img src="<?php echo JURI::root(); ?>components/com_rseventspro/assets/images/loader.gif" alt="" />
	</div>
	<?php if ($this->total > $count) { ?>
		<a class="rs_read_more" id="rsepro_loadmore"><?php echo JText::_('COM_RSEVENTSPRO_GLOBAL_LOAD_MORE'); ?></a>
	<?php } ?>
	<span id="total" class="rs_hidden"><?php echo $this->total; ?></span>
	<span id="Itemid" class="rs_hidden"><?php echo JFactory::getApplication()->input->getInt('Itemid'); ?></span>
	<?php } else echo JText::_('COM_RSEVENTSPRO_NO_SUBSCRIBERS'); ?>
	
	<?php echo JHTML::_( 'form.token' ); ?>
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="option" value="com_rseventspro" />
	<input type="hidden" name="view" value="rseventspro" />
</form>

<script type="text/javascript">
	jQuery(document).ready(function(){
		<?php if ($this->total > $count) { ?>
		jQuery('#rsepro_loadmore').on('click', function() {
			rspagination('subscribers',jQuery('#rs_events_container > li').length,<?php echo $this->row->id; ?>);
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