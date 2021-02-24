<?php
/**
* @package RSEvents!Pro
* @copyright (C) 2020 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/
defined( '_JEXEC' ) or die( 'Restricted access' ); ?>

<fieldset class="options-form">
	<legend><?php echo JText::_('COM_RSEVENTSPRO_EVENT_INFORMATION'); ?></legend>
	
	<?php echo $this->form->renderField('name'); ?>
	<?php echo $this->form->renderField('published'); ?>
	<?php echo $this->form->renderField('featured'); ?>
	<?php echo $this->form->renderField('start'); ?>
	<div id="rsepro-end-date-id"><?php echo $this->form->renderField('end'); ?></div>
	<?php echo $this->form->renderField('allday'); ?>
	<?php if (empty($this->item->parent)) echo $this->form->renderField('recurring'); ?>
	<?php if (empty($this->item->parent)) { ?>
	<div id="rsepro-recurring-info" style="display: <?php echo $this->item->recurring ? 'inline-block' : 'none'; ?>"><?php echo RSEventsproAdapterGrid::renderField('', '<div class="alert alert-success"><i class="fa fa-lightbulb-o"></i> '.JText::sprintf('COM_RSEVENTSPRO_EVENT_RECURING_TIMES','<span id="rs_repeating_event_total">'.$this->eventClass->getChild().'</span>').'</div>'); ?></div>
	<?php } ?>
	<?php echo $this->form->renderField('comments'); ?>
	<?php echo $this->form->renderField('registration'); ?>
	<?php echo $this->form->renderField('rsvp'); ?>
	
	<div class="control-group">
		<div class="control-label">
			<label for="jform_location"><?php echo JText::_('COM_RSEVENTSPRO_EVENT_LOCATION'); ?></label>
		</div>
		<div class="controls">
			<input class="span10 form-control" type="text" value="<?php echo $this->escape($this->item->locationname); ?>" id="rsepro-location" autocomplete="off" />
			<?php echo $this->form->getInput('location'); ?>
			
			<div class="rsepro-locations-container" style="visibility: hidden;">
				<div class="<?php echo RSEventsproAdapterGrid::card(); ?> well-small">
					<div class="card-body">
						<ul id="rsepro-locations" class="<?php echo RSEventsproAdapterGrid::styles(array('unstyled')); ?> rsepro-well"></ul>
					</div>
				</div>
			</div>
			
			<div class="rsepro-location-container" style="visibility: hidden; overflow: hidden;">
				<div class="<?php echo RSEventsproAdapterGrid::card(); ?> well-small rsepro-well">
					<div class="card-body">
						<div class="control-group">
							<div class="control-label">
								<label for="location_address"><?php echo JText::_('COM_RSEVENTSPRO_EVENT_LOCATION_ADDRESS'); ?></label>
							</div>
							<div class="controls">
								<input class="span10 form-control" type="text" value="" id="location_address" name="location_address" />
							</div>
						</div>
						<div class="control-group">
							<div class="control-label">
								<label for="location_URL"><?php echo JText::_('COM_RSEVENTSPRO_LOCATION_URL'); ?></label>
							</div>
							<div class="controls">
								<input class="span10 form-control" type="text" value="" id="location_URL" name="location_URL" />
							</div>
						</div>
						<div class="control-group">
							<div class="control-label">
								<label for="location_description"><?php echo JText::_('COM_RSEVENTSPRO_EVENT_LOCATION_DESCRIPTION'); ?></label>
							</div>
							<div class="controls">
								<textarea id="location_description" name="location_description" class="span10 form-control"></textarea>
							</div>
						</div>
						<?php if ($this->config->map) { ?>
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
								<button type="button" class="btn btn-danger" id="rsepro-cancel-location"><?php echo JText::_('COM_RSEVENTSPRO_GLOBAL_CANCEL_BTN'); ?></button>
							</div>
						</div>
					</div>
				</div>
			</div>
			
		</div>
	</div>

	<?php echo $this->dependencies->renderField('speakers'); ?>
	<?php echo $this->dependencies->renderField('sponsors'); ?>
	<?php echo $this->dependencies->renderField('groups'); ?>
	<?php echo $this->form->renderField('itemid'); ?>
	<?php echo $this->form->renderField('small_description'); ?>
	<?php echo $this->form->getInput('description'); ?>

	<div class="clearfix"></div>

	<div class="form-actions">
		<button class="btn btn-success rsepro-event-update" type="button"><?php echo JText::_('COM_RSEVENTSPRO_UPDATE_EVENT'); ?></button>
		<button class="btn btn-danger rsepro-event-cancel" type="button"><?php echo JText::_('COM_RSEVENTSPRO_GLOBAL_CANCEL_BTN'); ?></button>
	</div>
</fieldset>