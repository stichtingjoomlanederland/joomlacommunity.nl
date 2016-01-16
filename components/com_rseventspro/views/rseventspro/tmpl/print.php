<?php
/**
* @package RSEvents!Pro
* @copyright (C) 2015 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/
defined( '_JEXEC' ) or die( 'Restricted access' );
$details	= rseventsproHelper::details($this->event->id);
$event		= $details['event'];
$categories = $details['categories'];
$tags		= $details['tags']; ?>

<script type="text/javascript">
window.onload = function() { window.print(); }
</script>

<div class="rse_print">

	<h1 class="center"><?php echo $this->escape($event->name); ?></h1>

	<!-- Image -->
	<?php if (!empty($details['image_b'])) { ?>
	<p class="rs_image">
		<img src="<?php echo $details['image_b']; ?>" alt="<?php echo $this->escape($event->name); ?>" width="<?php echo rseventsproHelper::getConfig('icon_big_width','int'); ?>px" />
	</p>
	<?php } ?>
	<!--//end Image -->

	<!-- Start / End date -->
	<?php if ($event->allday) { ?>
		<?php if (!empty($this->options['start_date'])) { ?>
		<p class="rsep_date">
			<?php echo JText::_('COM_RSEVENTSPRO_GLOBAL_ON'); ?> <?php echo rseventsproHelper::showdate($event->start,$this->config->global_date,true); ?> 
		</p>
		<?php } ?>
	<?php } else { ?>
		<?php if (!empty($this->options['start_date']) || !empty($this->options['start_time']) || !empty($this->options['end_date']) || !empty($this->options['end_time'])) { ?>
		<p class="rsep_date">
			
			<?php if (!empty($this->options['start_date']) || !empty($this->options['start_time'])) { ?>
				
				<?php if ((!empty($this->options['start_date']) || !empty($this->options['start_time'])) && empty($this->options['end_date']) && empty($this->options['end_time'])) { ?>
				<?php echo JText::_('COM_RSEVENTSPRO_EVENT_STARTING_ON'); ?>
				<?php } else { ?>
				<?php echo JText::_('COM_RSEVENTSPRO_EVENT_FROM'); ?> 
				<?php } ?>
				
				<?php echo rseventsproHelper::showdate($event->start,rseventsproHelper::showMask('start',$this->options),true); ?>
			<?php } ?>
			
			<?php if (!empty($this->options['end_date']) || !empty($this->options['end_time'])) { ?>
				<?php if ((!empty($this->options['end_date']) || !empty($this->options['end_time'])) && empty($this->options['start_date']) && empty($this->options['start_time'])) { ?>
				<?php echo JText::_('COM_RSEVENTSPRO_EVENT_ENDING_ON'); ?>
				<?php } else { ?>
				<?php echo JText::_('COM_RSEVENTSPRO_EVENT_UNTIL'); ?>
				<?php } ?>
			
				<?php echo rseventsproHelper::showdate($event->end,rseventsproHelper::showMask('end',$this->options),true); ?>
			<?php } ?>
			
		</p>
		<?php } ?>
	<?php } ?>
	<!--//end Start / End date -->

	<div class="rsep_contact_block">
		<!-- Location -->
		<?php if (!empty($event->lpublished) && !empty($this->options['show_location'])) { ?>
		<p class="rsep_location">
			<?php echo JText::_('COM_RSEVENTSPRO_GLOBAL_AT'); ?> <?php echo $event->location; ?>
		</p>
		<?php } ?>
		<!--//end Location -->

		<!-- Posted By -->
		<?php if (!empty($this->options['show_postedby'])) { ?>
		<p class="rsep_posted">
			<?php echo JText::_('COM_RSEVENTSPRO_EVENT_POSTED_BY'); ?> 
			<?php echo $event->ownername; ?>
		</p>
		<?php } ?>
		<!--//end Posted By -->

		<!--Contact information -->
		<?php if (!empty($this->options['show_contact'])) { ?>
		<?php if (!empty($event->email)) { ?>
		<p class="rsep_mail">
			<?php echo $event->email; ?>
		</p>
		<?php } ?>
		<?php if (!empty($event->phone)) { ?>
		<p class="rsep_phone">	
			<?php echo $event->phone; ?>
		</p>
		<?php } ?>
		<?php if (!empty($event->URL)) { ?>
		<p class="rsep_url">
			<?php echo $event->URL; ?>
		</p>
		<?php } ?>
		<?php } ?>
		<!--//end Contact information -->
	</div>

	<div class="rsep_taxonomy_block">

		<!-- Categories -->
		<?php if (!empty($categories) && !empty($this->options['show_categories'])) { ?>
		<p class="rsep_categories">
			<?php echo JText::_('COM_RSEVENTSPRO_GLOBAL_CATEGORIES'); ?>: <?php echo $categories; ?>
		</p>
		<?php } ?>

		<!-- Tags -->
		<?php if (!empty($tags) && !empty($this->options['show_tags'])) { ?>
		<p class="rsep_tags">
			<?php echo JText::_('COM_RSEVENTSPRO_GLOBAL_TAGS'); ?>: <?php echo $tags; ?>
		</p>
		<?php } ?>
		<!--//end Tags -->

		<?php if (!empty($this->options['show_hits'])) { ?>
		<!-- Hits -->
		<p class="rsep_hits">
			<?php echo JText::_('COM_RSEVENTSPRO_GLOBAL_HITS'); ?>: <?php echo $event->hits; ?>
		</p>
		<!--//end Hits -->
		<?php } ?>
	</div>

	<!-- Description -->
	<?php if (!empty($this->options['show_description']) && !empty($event->description)) { ?>
		<hr />
		<div class="description"><?php echo $event->description; ?></div>
		<div class="rs_clear"></div>
	<?php } ?>
	<!--//end Description -->
</div>