<?php
/**
* @package RSEvents!Pro
* @copyright (C) 2015 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/
defined( '_JEXEC' ) or die( 'Restricted access' ); 
$eventOptions = $this->eventClass->getEventOptions(); ?>

<div class="row-fluid">
	<div class="span4">
		<legend><?php echo JText::_('COM_RSEVENTSPRO_SHARING_OPTIONS'); ?></legend>
		
		<div class="control-group">
			<label class="checkbox">
				<input type="checkbox" name="jform[options][enable_rating]" value="1" <?php echo (isset($eventOptions['enable_rating']) && $eventOptions['enable_rating'] == 1) ? 'checked="checked"' : ''; ?> />
				<?php echo JText::_('COM_RSEVENTSPRO_ENABLE_EVENT_RATING'); ?>
			</label>
			
			<label class="checkbox">
				<input type="checkbox" name="jform[options][enable_fb_like]" value="1" <?php echo (isset($eventOptions['enable_fb_like']) && $eventOptions['enable_fb_like'] == 1) ? 'checked="checked"' : ''; ?> />
				<?php echo JText::_('COM_RSEVENTSPRO_ENABLE_FACEBOOK_LIKE'); ?>
			</label>
			
			<label class="checkbox">
				<input type="checkbox" name="jform[options][enable_twitter]" value="1" <?php echo (isset($eventOptions['enable_twitter']) && $eventOptions['enable_twitter'] == 1) ? 'checked="checked"' : ''; ?> />
				<?php echo JText::_('COM_RSEVENTSPRO_ENABLE_TWITTER'); ?>
			</label>
			
			<label class="checkbox">
				<input type="checkbox" name="jform[options][enable_gplus]" value="1" <?php echo (isset($eventOptions['enable_gplus']) && $eventOptions['enable_gplus'] == 1) ? 'checked="checked"' : ''; ?> />
				<?php echo JText::_('COM_RSEVENTSPRO_ENABLE_GOOGLEPLUS'); ?>
			</label>
			
			<label class="checkbox">
				<input type="checkbox" name="jform[options][enable_linkedin]" value="1" <?php echo (isset($eventOptions['enable_linkedin']) && $eventOptions['enable_linkedin'] == 1) ? 'checked="checked"' : ''; ?> />
				<?php echo JText::_('COM_RSEVENTSPRO_ENABLE_LINKEDIN'); ?>
			</label>
		</div>
	</div>
	
	<div class="span4">
		<legend><?php echo JText::_('COM_RSEVENTSPRO_EVENT_OPTIONS_DETAIL'); ?></legend>
		
		<div class="control-group">
			<label class="checkbox">
				<input type="checkbox" name="jform[options][start_date]" value="1" <?php echo (isset($eventOptions['start_date']) && $eventOptions['start_date'] == 1) ? 'checked="checked"' : ''; ?> />
				<?php echo JText::_('COM_RSEVENTSPRO_SHOW_START_DATE'); ?>
			</label>
			
			<label class="checkbox">
				<?php
					if (!isset($eventOptions['start_time'])) {
						$start_time_checked = true;
					} else {
						if ($eventOptions['start_time'] == 1)
							$start_time_checked = true;
						else 
							$start_time_checked = false;
					}
				?>
				<input type="checkbox" name="jform[options][start_time]" value="1" <?php echo $start_time_checked ? 'checked="checked"' : ''; ?> />
				<?php echo JText::_('COM_RSEVENTSPRO_SHOW_START_TIME'); ?>
			</label>
			
			<label class="checkbox">
				<input type="checkbox" name="jform[options][end_date]" value="1" <?php echo (isset($eventOptions['end_date']) && $eventOptions['end_date'] == 1) ? 'checked="checked"' : ''; ?> />
				<?php echo JText::_('COM_RSEVENTSPRO_SHOW_END_DATE'); ?>
			</label>
			
			<label class="checkbox">
				<?php
					if (!isset($eventOptions['end_time'])) {
						$end_time_checked = true;
					} else {
						if ($eventOptions['end_time'] == 1)
							$end_time_checked = true;
						else 
							$end_time_checked = false;
					}
				?>
				<input type="checkbox" name="jform[options][end_time]" value="1" <?php echo $end_time_checked ? 'checked="checked"' : ''; ?> />
				<?php echo JText::_('COM_RSEVENTSPRO_SHOW_END_TIME'); ?>
			</label>
			
			<label class="checkbox">
				<input type="checkbox" name="jform[options][show_description]" value="1" <?php echo (isset($eventOptions['show_description']) && $eventOptions['show_description'] == 1) ? 'checked="checked"' : ''; ?> />
				<?php echo JText::_('COM_RSEVENTSPRO_SHOW_DESCRIPTION'); ?>
			</label>
			
			<label class="checkbox">
				<input type="checkbox" name="jform[options][show_location]" value="1" <?php echo (isset($eventOptions['show_location']) && $eventOptions['show_location'] == 1) ? 'checked="checked"' : ''; ?> />
				<?php echo JText::_('COM_RSEVENTSPRO_SHOW_LOCATION'); ?>
			</label>
			
			<label class="checkbox">
				<input type="checkbox" name="jform[options][show_categories]" value="1" <?php echo (isset($eventOptions['show_categories']) && $eventOptions['show_categories'] == 1) ? 'checked="checked"' : ''; ?> />
				<?php echo JText::_('COM_RSEVENTSPRO_SHOW_CATEGORIES'); ?>
			</label>
			
			<label class="checkbox">
				<input type="checkbox" name="jform[options][show_tags]" value="1" <?php echo (isset($eventOptions['show_tags']) && $eventOptions['show_tags'] == 1) ? 'checked="checked"' : ''; ?> />
				<?php echo JText::_('COM_RSEVENTSPRO_SHOW_TAGS'); ?>
			</label>
			
			<label class="checkbox">
				<input type="checkbox" name="jform[options][show_files]" value="1" <?php echo (isset($eventOptions['show_files']) && $eventOptions['show_files'] == 1) ? 'checked="checked"' : ''; ?> />
				<?php echo JText::_('COM_RSEVENTSPRO_SHOW_FILES'); ?>
			</label>
			
			<label class="checkbox">
				<input type="checkbox" name="jform[options][show_contact]" value="1" <?php echo (isset($eventOptions['show_contact']) && $eventOptions['show_contact'] == 1) ? 'checked="checked"' : ''; ?> />
				<?php echo JText::_('COM_RSEVENTSPRO_SHOW_CONTACT'); ?>
			</label>
			
			<label class="checkbox">
				<input type="checkbox" name="jform[options][show_map]" value="1" <?php echo (isset($eventOptions['show_map']) && $eventOptions['show_map'] == 1) ? 'checked="checked"' : ''; ?> />
				<?php echo JText::_('COM_RSEVENTSPRO_SHOW_MAP'); ?>
			</label>
			
			<label class="checkbox">
				<input type="checkbox" name="jform[options][show_export]" value="1" <?php echo (isset($eventOptions['show_export']) && $eventOptions['show_export'] == 1) ? 'checked="checked"' : ''; ?> />
				<?php echo JText::_('COM_RSEVENTSPRO_SHOW_EXPORT'); ?>
			</label>
			
			<label class="checkbox">
				<input type="checkbox" name="jform[options][show_invite]" value="1" <?php echo (isset($eventOptions['show_invite']) && $eventOptions['show_invite'] == 1) ? 'checked="checked"' : ''; ?> />
				<?php echo JText::_('COM_RSEVENTSPRO_SHOW_INVITE'); ?>
			</label>
			
			<label class="checkbox">
				<input type="checkbox" name="jform[options][show_postedby]" value="1" <?php echo (isset($eventOptions['show_postedby']) && $eventOptions['show_postedby'] == 1) ? 'checked="checked"' : ''; ?> />
				<?php echo JText::_('COM_RSEVENTSPRO_SHOW_POSTEDBY'); ?>
			</label>
			
			<label class="checkbox">
				<input type="checkbox" name="jform[options][show_repeats]" value="1" <?php echo (isset($eventOptions['show_repeats']) && $eventOptions['show_repeats'] == 1) ? 'checked="checked"' : ''; ?> />
				<?php echo JText::_('COM_RSEVENTSPRO_SHOW_REPEATS'); ?>
			</label>
			
			<label class="checkbox">
				<input type="checkbox" name="jform[options][show_hits]" value="1" <?php echo (isset($eventOptions['show_hits']) && $eventOptions['show_hits'] == 1) ? 'checked="checked"' : ''; ?> />
				<?php echo JText::_('COM_RSEVENTSPRO_SHOW_HITS'); ?>
			</label>
			
			<label class="checkbox">
				<input type="checkbox" name="jform[options][show_print]" value="1" <?php echo (isset($eventOptions['show_print']) && $eventOptions['show_print'] == 1) ? 'checked="checked"' : ''; ?> />
				<?php echo JText::_('COM_RSEVENTSPRO_SHOW_PRINT'); ?>
			</label>
			
		</div>
	</div>
	
	<div class="span4">
		<legend><?php echo JText::_('COM_RSEVENTSPRO_EVENT_OPTIONS_LISTINGS'); ?></legend>
		
		<div class="control-group">
			<label class="checkbox">
				<input type="checkbox" name="jform[options][start_date_list]" value="1" <?php echo (isset($eventOptions['start_date_list']) && $eventOptions['start_date_list'] == 1) ? 'checked="checked"' : ''; ?> />
				<?php echo JText::_('COM_RSEVENTSPRO_SHOW_START_DATE'); ?>
			</label>
			
			<label class="checkbox">
				<?php
					if (!isset($eventOptions['start_time_list'])) {
						$start_time_list_checked = true;
					} else {
						if ($eventOptions['start_time_list'] == 1)
							$start_time_list_checked = true;
						else 
							$start_time_list_checked = false;
					}
				?>
				<input type="checkbox" name="jform[options][start_time_list]" value="1" <?php echo $start_time_list_checked ? 'checked="checked"' : ''; ?> />
				<?php echo JText::_('COM_RSEVENTSPRO_SHOW_START_TIME'); ?>
			</label>
			
			<label class="checkbox">
				<input type="checkbox" name="jform[options][end_date_list]" value="1" <?php echo (isset($eventOptions['end_date_list']) && $eventOptions['end_date_list'] == 1) ? 'checked="checked"' : ''; ?> />
				<?php echo JText::_('COM_RSEVENTSPRO_SHOW_END_DATE'); ?>
			</label>
			
			<label class="checkbox">
				<?php
					if (!isset($eventOptions['end_time_list'])) {
						$end_time_list_checked = true;
					} else {
						if ($eventOptions['end_time_list'] == 1)
							$end_time_list_checked = true;
						else 
							$end_time_list_checked = false;
					}
				?>
				<input type="checkbox" name="jform[options][end_time_list]" value="1" <?php echo $end_time_list_checked ? 'checked="checked"' : ''; ?> />
				<?php echo JText::_('COM_RSEVENTSPRO_SHOW_END_TIME'); ?>
			</label>
			
			<label class="checkbox">
				<input type="checkbox" name="jform[options][show_location_list]" value="1" <?php echo (isset($eventOptions['show_location_list']) && $eventOptions['show_location_list'] == 1) ? 'checked="checked"' : ''; ?> />
				<?php echo JText::_('COM_RSEVENTSPRO_SHOW_LOCATION'); ?>
			</label>
			
			<label class="checkbox">
				<input type="checkbox" name="jform[options][show_categories_list]" value="1" <?php echo (isset($eventOptions['show_categories_list']) && $eventOptions['show_categories_list'] == 1) ? 'checked="checked"' : ''; ?> />
				<?php echo JText::_('COM_RSEVENTSPRO_SHOW_CATEGORIES'); ?>
			</label>
			
			<label class="checkbox">
				<input type="checkbox" name="jform[options][show_tags_list]" value="1" <?php echo (isset($eventOptions['show_tags_list']) && $eventOptions['show_tags_list'] == 1) ? 'checked="checked"' : ''; ?> />
				<?php echo JText::_('COM_RSEVENTSPRO_SHOW_TAGS'); ?>
			</label>
			
			<label class="checkbox">
				<input type="checkbox" name="jform[options][show_icon_list]" value="1" <?php echo (isset($eventOptions['show_icon_list']) && $eventOptions['show_icon_list'] == 1) ? 'checked="checked"' : ''; ?> />
				<?php echo JText::_('COM_RSEVENTSPRO_SHOW_ICON'); ?>
			</label>
			
		</div>
	</div>
</div>

<div class="form-actions">
	<button class="btn btn-success rsepro-event-update" type="button"><?php echo JText::_('COM_RSEVENTSPRO_UPDATE_EVENT'); ?></button>
	<button class="btn btn-danger rsepro-event-cancel" type="button"><?php echo JText::_('COM_RSEVENTSPRO_GLOBAL_CANCEL_BTN'); ?></button>
</div>