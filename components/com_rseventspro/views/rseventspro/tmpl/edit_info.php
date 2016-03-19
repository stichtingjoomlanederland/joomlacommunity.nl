<?php
/**
* @package RSEvents!Pro
* @copyright (C) 2015 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/
defined( '_JEXEC' ) or die( 'Restricted access' ); ?>
<legend><?php echo JText::_('COM_RSEVENTSPRO_EVENT_INFORMATION'); ?></legend>

<div class="control-group">
	<div class="control-label">
		<label for="jform_name"><?php echo JText::_('COM_RSEVENTSPRO_EVENT_NAME'); ?></label>
	</div>
	<div class="controls">
		<input type="text" value="<?php echo $this->escape($this->item->name); ?>" class="span10" name="jform[name]" id="jform_name" tabindex="1" />
	</div>
</div>

<?php if (empty($this->permissions['event_moderation']) || $this->admin) { ?>
<div class="control-group">
	<div class="control-label">
		<label for="jform_published"><?php echo JText::_('COM_RSEVENTSPRO_PUBLISH_EVENT'); ?></label>
	</div>
	<div class="controls">
		<select name="jform[published]" id="jform_published" class="input-medium">
			<?php echo JHtml::_('select.options', JHtml::_('jgrid.publishedOptions', $this->states), 'value', 'text', $this->item->published, true); ?>
		</select>
	</div>
</div>
<?php } ?>

<div class="control-group">
	<div class="control-label">
		<label for="jform_start"><?php echo JText::_('COM_RSEVENTSPRO_EVENT_STARTING'); ?></label>
	</div>
	<div class="controls">
		<?php echo JHTML::_('rseventspro.rscalendar', 'jform[start]', $this->item->start, $this->item->allday); ?>
	</div>
</div>

<div class="control-group" id="rsepro-end-date-id">
	<div class="control-label">
		<label for="jform_end"><?php echo JText::_('COM_RSEVENTSPRO_EVENT_ENDING'); ?></label>
	</div>
	<div class="controls">
		<?php echo JHTML::_('rseventspro.rscalendar', 'jform[end]', $this->item->end, $this->item->allday); ?>
	</div>
</div>

<div class="control-group">
	<label class="checkbox">
		<?php $alldaychecked = $this->item->allday ? 'checked="checked"' : ''; ?>
		<input type="checkbox" id="jform_allday" name="jform[allday]" value="1" <?php echo $alldaychecked; ?> /> <?php echo JText::_('COM_RSEVENTSPRO_EVENT_ALL_DAY'); ?>
	</label>

	<?php if (empty($this->item->parent) && (!empty($this->permissions['can_repeat_events']) || $this->admin)) { ?>
	<label class="checkbox">
		<?php $recurringchecked = $this->item->recurring ? 'checked="checked"' : ''; ?>
		<input type="checkbox" id="jform_recurring" name="jform[recurring]" value="1" <?php echo $recurringchecked; ?> /> <?php echo JText::_('COM_RSEVENTSPRO_EVENT_RECURRING'); ?>
		<small class="text-info" style="display: <?php echo $this->item->recurring ? 'inline-block' : 'none'; ?>"><i class="fa fa-lightbulb-o"></i> <?php echo JText::sprintf('COM_RSEVENTSPRO_EVENT_RECURING_TIMES','<span id="rs_repeating_event_total">'.$this->eventClass->getChild().'</span>'); ?></small>
	</label>
	<?php } ?>
	
	<label class="checkbox">
		<?php $commentschecked = $this->item->comments ? 'checked="checked"' : ''; ?>
		<input type="checkbox" id="jform_comments" name="jform[comments]" value="1" <?php echo $commentschecked; ?> /> <?php echo JText::_('COM_RSEVENTSPRO_EVENT_ENABLE_COMMENTS'); ?>
	</label>

	<label class="checkbox">
		<?php $registrationchecked = $this->item->registration ? 'checked="checked"' : ''; ?>
		<input type="checkbox" id="jform_registration" name="jform[registration]" value="1" <?php echo $registrationchecked; ?> /> <?php echo JText::_('COM_RSEVENTSPRO_EVENT_ENABLE_REGISTRATION'); ?>
	</label>
</div>

<div class="control-group">
	<div class="control-label">
		<label for="jform_location"><?php echo JText::_('COM_RSEVENTSPRO_EVENT_LOCATION'); ?></label>
	</div>
	<div class="controls">
		<input class="span10" type="text" value="<?php echo $this->escape($this->item->locationname); ?>" id="rsepro-location" autocomplete="off" />
		<input type="hidden" name="jform[location]" id="jform_location" value="<?php echo $this->item->location; ?>" />
		
		<div class="rsepro-locations-container" style="visibility: hidden;">
			<ul id="rsepro-locations" class="unstyled well well-small rsepro-well"></ul>
		</div>
		
		<?php if (!empty($this->permissions['can_add_locations']) || $this->admin) { ?>
		<div class="rsepro-location-container" style="visibility: hidden; overflow: hidden;">
			<div class="well well-small rsepro-well">
				<div class="control-group">
					<div class="control-label">
						<label for="location_address"><?php echo JText::_('COM_RSEVENTSPRO_EVENT_LOCATION_ADDRESS'); ?></label>
					</div>
					<div class="controls">
						<input class="span10" type="text" value="" id="location_address" name="location_address" />
					</div>
				</div>
				<div class="control-group">
					<div class="control-label">
						<label for="location_description"><?php echo JText::_('COM_RSEVENTSPRO_EVENT_LOCATION_DESCRIPTION'); ?></label>
					</div>
					<div class="controls">
						<textarea id="location_description" name="location_description" class="span10"></textarea>
					</div>
				</div>
				<?php if ($this->config->enable_google_maps) { ?>
				<div class="control-group">
					<div class="controls">
						<div class="rsepro-location-map" id="rsepro-location-map"></div>
						<input type="hidden" name="location_coordinates" value="" id="location_coordinates" />
					</div>
				</div>
				<?php } ?>
				<div class="control-group">
					<div class="controls">
						<button type="button" class="btn btn-primary" id="rsepro-save-location"><?php echo JText::_('COM_RSEVENTSPRO_EVENT_LOCATION_ADD_LOCATION'); ?></button>
						<button type="button" class="btn" id="rsepro-cancel-location"><?php echo JText::_('COM_RSEVENTSPRO_GLOBAL_CANCEL'); ?></button>
					</div>
				</div>
			</div>
		</div>
		<?php } ?>
		
	</div>
</div>

<?php if ($this->admin) { ?>
<div class="control-group">
	<div class="control-label">
		<label for="groups"><?php echo JText::_('COM_RSEVENTSPRO_EVENT_GROUPS'); ?></label>
	</div>
	<div class="controls">
		<select name="groups[]" id="groups" multiple="multiple" class="rsepro-chosen">
			<?php echo JHtml::_('select.options', $this->eventClass->groups(),'value','text',$this->eventClass->getGroups()); ?>
		</select>
	</div>
</div>
<?php } ?>

<div class="control-group clearfix">
	<div class="controls">
		<?php echo JEditor::getInstance(JFactory::getConfig()->get('editor'))->display('jform[description]',$this->escape($this->item->description),'100%', '50%', 20, 7, rseventsproHelper::getConfig('enable_buttons','bool')); ?>
	</div>
</div>

<div class="form-actions">
	<button class="btn btn-success rsepro-event-update" type="button"><?php echo JText::_('COM_RSEVENTSPRO_UPDATE_EVENT'); ?></button>
	<button class="btn btn-danger rsepro-event-cancel" type="button"><?php echo JText::_('COM_RSEVENTSPRO_GLOBAL_CANCEL'); ?></button>
</div>