<?php
/**
* @package RSEvents!Pro
* @copyright (C) 2020 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/
defined( '_JEXEC' ) or die( 'Restricted access' ); ?>

<h1><?php echo JText::sprintf('COM_RSEVENTSPRO_WAITINGLIST',$this->event->name); ?></h1>

<form action="<?php echo rseventsproHelper::route('index.php?option=com_rseventspro'); ?>" method="post" name="adminForm" id="adminForm" class="form-horizontal" autocomplete="off">
	
	<?php $count = count($this->data); ?>
	<?php if (!empty($this->data)) { ?>
	<ul class="rs_events_container" id="rs_events_container">
	<?php foreach($this->data as $row) { ?>
	<li class="rs_event_detail">
		<div class="rs_options" style="display:none;">
			<a class="hasTooltip" href="<?php echo rseventsproHelper::route('index.php?option=com_rseventspro&task=rseventspro.approvewaiting&id='.rseventsproHelper::sef($row->id,$row->name),false); ?>" title="<?php echo JText::_('COM_RSEVENTSPRO_WAITINGLIST_APPROVE'); ?>">
				<i class="fa fa-check fa-fw"></i>
			</a>
			<a class="hasTooltip" href="<?php echo rseventsproHelper::route('index.php?option=com_rseventspro&task=rseventspro.removewaiting&id='.rseventsproHelper::sef($row->id,$row->name),false); ?>" onclick="return confirm('<?php echo JText::_('COM_RSEVENTSPRO_WAITINGLIST_DELETE_CONFIRM'); ?>');" title="<?php echo JText::_('COM_RSEVENTSPRO_GLOBAL_DELETE'); ?>">
				<i class="fa fa-trash fa-fw"></i>
			</a>
		</div>
		<div class="rs_event_details rs_inline">
			<a href="<?php echo rseventsproHelper::route('index.php?option=com_rseventspro&layout=editwaitinglist&id='.rseventsproHelper::sef($row->id,$row->name),false); ?>"><?php echo $row->name; ?></a> (<?php echo $row->email; ?>) <br />
			<?php if ($row->date != '0000-00-00 00:00:00') { echo JText::_('COM_RSEVENTSPRO_WAITINGLIST_ADDED'); ?>: <?php echo rseventsproHelper::showdate($row->date,null,true); ?> <br /><?php } ?>
			<?php if ($row->sent != '0000-00-00 00:00:00') { echo JText::_('COM_RSEVENTSPRO_WAITINGLIST_SENT'); ?>: <?php echo rseventsproHelper::showdate($row->sent,null,true); ?> <br /><?php } ?>
			<?php if ($row->confirmed != '0000-00-00 00:00:00') { echo JText::_('COM_RSEVENTSPRO_WAITINGLIST_CONFIRMED'); ?>: <?php echo rseventsproHelper::showdate($row->confirmed,null,true); ?><?php } ?>
		</div>
	</li>
	<?php } ?>
	</ul>
	<div class="rs_loader" id="rs_loader" style="display:none;">
		<?php echo JHtml::image('com_rseventspro/loader.gif', '', array(), true); ?> 
	</div>
	<?php if ($this->total > $count) { ?>
		<p id="rsepro_number_events"><?php echo JText::sprintf('COM_RSEVENTSPRO_SHOWING_WAITINGLIST','<span>'.$count.'</span>',$this->total); ?></p>
		<a class="rs_read_more" id="rsepro_loadmore"><?php echo JText::_('COM_RSEVENTSPRO_GLOBAL_LOAD_MORE'); ?></a>
	<?php } ?>
	<span id="total" class="rs_hidden"><?php echo $this->total; ?></span>
	<span id="Itemid" class="rs_hidden"><?php echo JFactory::getApplication()->input->getInt('Itemid'); ?></span>
	<?php } else echo JText::_('COM_RSEVENTSPRO_NO_SUBSCRIBERS'); ?>
	
	
	<?php echo JHTML::_('form.token')."\n"; ?>
	<input type="hidden" name="option" value="com_rseventspro" />
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="id" value="<?php echo $this->event->id; ?>" />
</form>

<script type="text/javascript">
	jQuery(document).ready(function(){
		<?php if ($this->total > $count) { ?>
		jQuery('#rsepro_loadmore').on('click', function() {
			rspagination('waitinglist',jQuery('#rs_events_container > li').length,<?php echo $this->event->id; ?>);
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