<?php
/**
* @version 1.0.0
* @package RSEvents!Pro 1.0.0
* @copyright (C) 2011 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/

defined('_JEXEC') or die('Restricted access');?>

<?php if ($this->params->get('show_page_heading', 1)) { ?>
<?php $title = $this->params->get('page_heading', ''); ?>
<h1><?php echo !empty($title) ? $this->escape($title) : JText::_('COM_RSEVENTSPRO_EVENTS'); ?></h1>
<?php } ?>

<?php if ($this->params->get('show_category_title', 0) && $this->category) { ?>
<h2>
	<span class="subheading-category"><?php echo $this->category->title; ?></span>
</h2>
<?php } ?>

<?php if (($this->params->get('show_category_description', 0) || $this->params->def('show_category_image', 0)) && $this->category) { ?>
	<div class="category-desc">
	<?php if ($this->params->get('show_category_image') && $this->category->getParams()->get('image')) { ?>
		<img src="<?php echo $this->category->getParams()->get('image'); ?>" alt="" />
	<?php } ?>
	<?php if ($this->params->get('show_category_description') && $this->category->description) { ?>
		<?php echo JHtml::_('content.prepare', $this->category->description, '', 'com_content.category'); ?>
	<?php } ?>
	<div class="clr"></div>
	</div>
<?php } ?>

<?php if ($this->params->get('rss',1)) { ?>
<div class="rs_rss">
	<a href="<?php echo rseventsproHelper::route('index.php?option=com_rseventspro&format=feed&type=rss'); ?>">
		<img src="<?php echo JURI::root(); ?>components/com_rseventspro/assets/images/rss.png" />
	</a>
</div>
<?php } ?>

<?php if ($this->params->get('search',1)) { ?>
<form method="post" action="<?php echo JRoute::_(JURI::getInstance()); ?>" name="adminForm" id="adminForm">
	<div class="rs_search">
		<div class="rs_select_top" id="rs_select_top1">
			<?php echo $this->lists['filter_from']; ?>
		</div>
		
		<div class="rs_select_top" id="rs_select_top2">
			<?php echo $this->lists['filter_condition']; ?>
		</div>
		
		<div class="rs_select_top" style="position: relative;">
			<input type="text" name="search[]" id="rseprosearch" onkeyup="rs_search();" onkeydown="rs_stop();" value="" size="30" autocomplete="off" class="rs_input" />
			<button type="button" onclick="rs_add_filter();" class="rs_search_button"><span id="search_btn"></span></button>
			<ul class="rs_results" id="rs_results"></ul>
		</div>
		<div class="rs_clear"></div>
		
		<div class="rs_filter">
			<ul id="rs_filters">
				<?php if (!empty($this->columns)) { ?>
				<?php for ($i=0; $i<count($this->columns); $i++) { ?>
				<li>
					<span><?php echo rseventsproHelper::translate($this->columns[$i]); ?></span>
					<span><?php echo rseventsproHelper::translate($this->operators[$i]); ?></span>
					<strong><?php echo $this->escape($this->values[$i]); ?></strong>
					<a class="rsepro_close" href="javascript: void(0);" onclick="rs_remove_filter(<?php echo $i; ?>)"></a>
					<input type="hidden" name="filter_from[]" value="<?php echo $this->escape($this->columns[$i]); ?>" />
					<input type="hidden" name="filter_condition[]" value="<?php echo $this->escape($this->operators[$i]); ?>" />
					<input type="hidden" name="search[]" value="<?php echo $this->escape($this->values[$i]); ?>" />
				</li>
				<?php } ?>
				<li><a href="javascript:void(0)" onclick="rs_clear_filters();"><?php echo JText::_('COM_RSEVENTSPRO_GLOBAL_CLEAR_FILTER'); ?></a></li>
				<?php } ?>
			</ul>
		</div>
	</div>
	<input type="hidden" name="rs_clear" id="rs_clear" value="0" />
	<input type="hidden" name="rs_remove" id="rs_remove" value="" />
</form>
<?php } else { ?>
<?php if (!empty($this->columns)) { ?>
<a href="<?php echo rseventsproHelper::route('index.php?option=com_rseventspro&task=clear'); ?>" class="rs_filter_clear"><?php echo JText::_('COM_RSEVENTSPRO_GLOBAL_CLEAR_FILTER'); ?></a>
<div class="rs_clear"></div>
<?php } ?>
<?php } ?>

<?php $count = count($this->events); ?>
<?php if (!empty($this->events)) { ?>
<ul class="rs_events_container" id="rs_events_container">
	<?php foreach($this->events as $eventid) { ?>
	<?php $details = rseventsproHelper::details($eventid->id); ?>
	<?php if (isset($details['event']) && !empty($details['event'])) $event = $details['event']; else continue; ?>
	<?php if (!rseventsproHelper::canview($eventid->id) && $event->owner != $this->user) continue; ?>
	<?php $full = rseventsproHelper::eventisfull($event->id); ?>
	<?php $ongoing = rseventsproHelper::ongoing($event->id); ?>
	<?php $categories = (isset($details['categories']) && !empty($details['categories'])) ? JText::_('COM_RSEVENTSPRO_GLOBAL_CATEGORIES').': '.$details['categories'] : '';  ?>
	<?php $tags = (isset($details['tags']) && !empty($details['tags'])) ? JText::_('COM_RSEVENTSPRO_GLOBAL_TAGS').': '.$details['tags'] : '';  ?>
	<?php $incomplete = !$event->completed ? ' rs_incomplete' : ''; ?>
	<?php $featured = $event->featured ? ' rs_featured' : ''; ?>
	<?php $repeats = rseventsproHelper::getRepeats($event->id); ?>
	<li class="rs_event_detail<?php echo $incomplete.$featured; ?>" id="rs_event<?php echo $event->id; ?>" itemscope itemtype="http://schema.org/Event">
		
		<div class="rs_options" style="display:none;">
			<?php if ((!empty($this->permissions['can_edit_events']) || $event->owner == $this->user || $event->sid == $this->user || $this->admin) && !empty($this->user)) { ?>
				<a href="<?php echo rseventsproHelper::route('index.php?option=com_rseventspro&layout=edit&id='.rseventsproHelper::sef($event->id,$event->name)); ?>">
					<img src="<?php echo JURI::root(); ?>components/com_rseventspro/assets/images/edit.png" alt="<?php echo JText::_('COM_RSEVENTSPRO_GLOBAL_EDIT'); ?>" />
				</a>
			<?php } ?>
			<?php if ((!empty($this->permissions['can_delete_events']) || $event->owner == $this->user || $event->sid == $this->user || $this->admin) && !empty($this->user)) { ?>
				<a href="<?php echo rseventsproHelper::route('index.php?option=com_rseventspro&task=rseventspro.remove&id='.rseventsproHelper::sef($event->id,$event->name)); ?>" onclick="return confirm('<?php echo JText::_('COM_RSEVENTSPRO_GLOBAL_DELETE_CONFIRMATION'); ?>');">
					<img src="<?php echo JURI::root(); ?>components/com_rseventspro/assets/images/delete.png" alt="<?php echo JText::_('COM_RSEVENTSPRO_GLOBAL_DELETE'); ?>" />
				</a>
			<?php } ?>
		</div>
		
		<div class="rs_event_image" itemprop="image">
			<a href="<?php echo rseventsproHelper::route('index.php?option=com_rseventspro&layout=show&id='.rseventsproHelper::sef($event->id,$event->name)); ?>" class="rs_event_link">
				<?php if (!empty($event->icon)) { ?>
					<img src="<?php echo JURI::root(); ?>components/com_rseventspro/assets/images/events/thumbs/s_<?php echo $event->icon.'?nocache='.uniqid(''); ?>" alt="" width="<?php echo $this->config->icon_small_width; ?>" />
				<?php } else { ?>
					<img src="<?php echo JURI::root(); ?>components/com_rseventspro/assets/images/blank.png" alt="" width="70" />
				<?php }  ?>
			</a>
		</div>
		
		<div class="rs_event_details">
			<span itemprop="name">
				<a itemprop="url" href="<?php echo rseventsproHelper::route('index.php?option=com_rseventspro&layout=show&id='.rseventsproHelper::sef($event->id,$event->name)); ?>" class="rs_event_link<?php echo $full ? ' rs_event_full' : ''; ?><?php echo $ongoing ? ' rs_event_ongoing' : ''; ?>"><?php echo $event->name; ?></a> <?php if (!$event->completed) echo JText::_('COM_RSEVENTSPRO_GLOBAL_INCOMPLETE_EVENT'); ?> <?php if (!$event->published) echo JText::_('COM_RSEVENTSPRO_GLOBAL_UNPUBLISHED_EVENT'); ?>
			</span>
			<span>
				<?php if ($event->allday) { ?>
				<?php echo JText::_('COM_RSEVENTSPRO_GLOBAL_ON'); ?> <b><?php echo rseventsproHelper::date($event->start,$this->config->global_date,true); ?></b>
				<?php } else { ?>
				<?php echo JText::_('COM_RSEVENTSPRO_GLOBAL_FROM'); ?> <b><?php echo rseventsproHelper::date($event->start,rseventsproHelper::showMask('start',$event->options),true); ?></b> <?php echo JText::_('COM_RSEVENTSPRO_GLOBAL_TO_LOWERCASE'); ?> <b><?php echo rseventsproHelper::date($event->end,rseventsproHelper::showMask('end',$event->options),true); ?></b>
				<?php } ?>
			</span>
			<span>
				<?php if ($event->locationid && $event->lpublished) { echo JText::_('COM_RSEVENTSPRO_GLOBAL_AT'); ?> <a href="<?php echo rseventsproHelper::route('index.php?option=com_rseventspro&layout=location&id='.rseventsproHelper::sef($event->locationid,$event->location)); ?>"><?php echo $event->location; ?></a> <?php } ?>
				<?php echo $categories.' '.$tags; ?>
			</span>
			<?php if ($this->params->get('repeatcounter',1)) { ?>
			<span class="rs_event_repeats">
				<?php if ($repeats) { ?> 
				(<a href="<?php echo rseventsproHelper::route('index.php?option=com_rseventspro&layout=default&parent='.rseventsproHelper::sef($event->id,$event->name)); ?>"><?php echo JText::sprintf('COM_RSEVENTSPRO_GLOBAL_REPEATS',$repeats); ?></a>) 
				<?php } ?>
			</span>
			<?php } ?>
		</div>
		
		<div style="display:none"><span itemprop="startDate"><?php echo rseventsproHelper::date($event->start,'Y-m-d H:i:s'); ?></span></div>
		<div style="display:none"><span itemprop="endDate"><?php echo rseventsproHelper::date($event->end,'Y-m-d H:i:s'); ?></span></div>
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
<span id="parent" class="rs_hidden"><?php echo JFactory::getApplication()->input->getInt('parent'); ?></span>
<?php } else echo JText::_('COM_RSEVENTSPRO_GLOBAL_NO_EVENTS'); ?>

<script type="text/javascript">
	window.addEvent('domready', function(){
		<?php if ($this->total > $count) { ?>
		$('rsepro_loadmore').addEvent('click', function(el) {
			var lstart = $$('#rs_events_container > li');
			rspagination('events',lstart.length);
		});
		<?php } ?>
		
		<?php if ($this->params->get('search',1)) { ?>
		new elSelect( {container : 'rs_select_top1'} );
		new elSelect( {container : 'rs_select_top2'} );
		<?php } ?>
		
		<?php if (!empty($count)) { ?>
		$$('#rs_events_container li').addEvents({
			mouseenter: function(){ 
				if (isset($(this).getElement('div.rs_options')))
					$(this).getElement('div.rs_options').style.display = '';
			},
			mouseleave: function(){      
				if (isset($(this).getElement('div.rs_options')))
					$(this).getElement('div.rs_options').style.display = 'none';
			}
		});
		<?php } ?>
	});
</script>