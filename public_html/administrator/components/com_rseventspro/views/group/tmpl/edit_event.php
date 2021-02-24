<?php
/**
* @package RSEvents!Pro
* @copyright (C) 2020 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/
defined( '_JEXEC' ) or die( 'Restricted access' ); ?>

<div class="<?php echo RSEventsproAdapterGrid::row(); ?>">
	<div class="<?php echo RSEventsproAdapterGrid::column(4); ?>">
		<fieldset class="options-form">
			<legend><?php echo JText::_('COM_RSEVENTSPRO_EVENT_OPTIONS_SHARING'); ?></legend>
			<?php echo $this->form->renderField('enable_rating','event'); ?>
			<?php echo $this->form->renderField('enable_fb_like','event'); ?>
			<?php echo $this->form->renderField('enable_twitter','event'); ?>
			<?php echo $this->form->renderField('enable_linkedin','event'); ?>
		</fieldset>
	</div>
	<div class="<?php echo RSEventsproAdapterGrid::column(4); ?>">
		<fieldset class="options-form">
			<legend><?php echo JText::_('COM_RSEVENTSPRO_EVENT_OPTIONS_DETAIL'); ?></legend>
			<?php echo $this->form->renderField('start_date','event'); ?>
			<?php echo $this->form->renderField('start_time','event'); ?>
			<?php echo $this->form->renderField('end_date','event'); ?>
			<?php echo $this->form->renderField('end_time','event'); ?>
			<?php echo $this->form->renderField('show_description','event'); ?>
			<?php echo $this->form->renderField('show_location','event'); ?>
			<?php echo $this->form->renderField('show_categories','event'); ?>
			<?php echo $this->form->renderField('show_tags','event'); ?>
			<?php echo $this->form->renderField('show_files','event'); ?>
			<?php echo $this->form->renderField('show_contact','event'); ?>
			<?php echo $this->form->renderField('show_map','event'); ?>
			<?php echo $this->form->renderField('show_export','event'); ?>
			<?php echo $this->form->renderField('show_invite','event'); ?>
			<?php echo $this->form->renderField('show_postedby','event'); ?>
			<?php echo $this->form->renderField('show_repeats','event'); ?>
			<?php echo $this->form->renderField('show_active_child_events','event'); ?>
			<?php echo $this->form->renderField('show_hits','event'); ?>
			<?php echo $this->form->renderField('show_print','event'); ?>
		</fieldset>
	</div>
	<div class="<?php echo RSEventsproAdapterGrid::column(4); ?>">
		<fieldset class="options-form">
			<legend><?php echo JText::_('COM_RSEVENTSPRO_EVENT_OPTIONS_LISTINGS'); ?></legend>
			<?php echo $this->form->renderField('start_date_list','event'); ?>
			<?php echo $this->form->renderField('start_time_list','event'); ?>
			<?php echo $this->form->renderField('end_date_list','event'); ?>
			<?php echo $this->form->renderField('end_time_list','event'); ?>
			<?php echo $this->form->renderField('show_location_list','event'); ?>
			<?php echo $this->form->renderField('show_categories_list','event'); ?>
			<?php echo $this->form->renderField('show_tags_list','event'); ?>
			<?php echo $this->form->renderField('show_icon_list','event'); ?>
		</fieldset>
	</div>
</div>