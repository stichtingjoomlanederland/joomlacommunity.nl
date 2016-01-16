<?php
/**
* @version 1.0.0
* @package RSEvents!Pro 1.0.0
* @copyright (C) 2011 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/
defined( '_JEXEC' ) or die( 'Restricted access' ); ?>
<div class="span6 rswidth-50 rsfltlft">
	<?php echo JHtml::_('rsfieldset.start', 'adminform', JText::_('COM_RSEVENTSPRO_SHARING_OPTIONS')); ?>
	<?php echo JHtml::_('rsfieldset.element', $this->form->getLabel('enable_rating','event'), $this->form->getInput('enable_rating','event')); ?>
	<?php echo JHtml::_('rsfieldset.element', $this->form->getLabel('enable_fb_like','event'), $this->form->getInput('enable_fb_like','event')); ?>
	<?php echo JHtml::_('rsfieldset.element', $this->form->getLabel('enable_twitter','event'), $this->form->getInput('enable_twitter','event')); ?>
	<?php echo JHtml::_('rsfieldset.element', $this->form->getLabel('enable_gplus','event'), $this->form->getInput('enable_gplus','event')); ?>
	<?php echo JHtml::_('rsfieldset.element', $this->form->getLabel('enable_linkedin','event'), $this->form->getInput('enable_linkedin','event')); ?>
	<?php echo JHtml::_('rsfieldset.end'); ?>
</div>
<div class="span6 rswidth-50 rsfltlft">
	<?php echo JHtml::_('rsfieldset.start', 'adminform', JText::_('COM_RSEVENTSPRO_EVENT_OPTIONS')); ?>
	<?php echo JHtml::_('rsfieldset.element', $this->form->getLabel('start_date','event'), $this->form->getInput('start_date','event')); ?>
	<?php echo JHtml::_('rsfieldset.element', $this->form->getLabel('start_time','event'), $this->form->getInput('start_time','event')); ?>
	<?php echo JHtml::_('rsfieldset.element', $this->form->getLabel('end_date','event'), $this->form->getInput('end_date','event')); ?>
	<?php echo JHtml::_('rsfieldset.element', $this->form->getLabel('end_time','event'), $this->form->getInput('end_time','event')); ?>
	<?php echo JHtml::_('rsfieldset.element', $this->form->getLabel('show_description','event'), $this->form->getInput('show_description','event')); ?>
	<?php echo JHtml::_('rsfieldset.element', $this->form->getLabel('show_location','event'), $this->form->getInput('show_location','event')); ?>
	<?php echo JHtml::_('rsfieldset.element', $this->form->getLabel('show_categories','event'), $this->form->getInput('show_categories','event')); ?>
	<?php echo JHtml::_('rsfieldset.element', $this->form->getLabel('show_tags','event'), $this->form->getInput('show_tags','event')); ?>
	<?php echo JHtml::_('rsfieldset.element', $this->form->getLabel('show_files','event'), $this->form->getInput('show_files','event')); ?>
	<?php echo JHtml::_('rsfieldset.element', $this->form->getLabel('show_contact','event'), $this->form->getInput('show_contact','event')); ?>
	<?php echo JHtml::_('rsfieldset.element', $this->form->getLabel('show_map','event'), $this->form->getInput('show_map','event')); ?>
	<?php echo JHtml::_('rsfieldset.element', $this->form->getLabel('show_export','event'), $this->form->getInput('show_export','event')); ?>
	<?php echo JHtml::_('rsfieldset.element', $this->form->getLabel('show_invite','event'), $this->form->getInput('show_invite','event')); ?>
	<?php echo JHtml::_('rsfieldset.element', $this->form->getLabel('show_postedby','event'), $this->form->getInput('show_postedby','event')); ?>
	<?php echo JHtml::_('rsfieldset.element', $this->form->getLabel('show_repeats','event'), $this->form->getInput('show_repeats','event')); ?>
	<?php echo JHtml::_('rsfieldset.end'); ?>
</div>
<div class="clr"></div>