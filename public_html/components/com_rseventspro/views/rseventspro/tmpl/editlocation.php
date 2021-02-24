<?php
/**
* @package RSEvents!Pro
* @copyright (C) 2020 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/
defined( '_JEXEC' ) or die( 'Restricted access' ); ?>

<h1><?php echo JText::sprintf('COM_RSEVENTSPRO_EDIT_LOCATION',$this->row->name); ?></h1>

<form action="<?php echo rseventsproHelper::route('index.php?option=com_rseventspro&layout=editlocation'); ?>" method="post" name="locationForm" id="locationForm" class="rsepro-horizontal">
	
	<?php echo $this->form->renderField('name'); ?>
	<?php echo $this->form->renderField('url'); ?>
	<?php $input = $this->config->map ? ' <button type="button" id="rsepro-pinpoint" class="btn btn-secondary">'.JText::_('COM_RSEVENTSPRO_LOCATION_PINPOINT').'</button>' : ''; ?>
	<?php $inputGroup = $input ? RSEventsproAdapterGrid::inputGroup($this->form->getInput('address'), null, $input) : $this->form->getInput('address'); ?>
	<?php echo RSEventsproAdapterGrid::renderField($this->form->getLabel('address'), $inputGroup); ?>
	<?php if (rseventsproHelper::isGallery()) echo $this->form->renderField('gallery_tags'); ?>
	
	<div class="clearfix"></div>
	<?php echo $this->form->getInput('description'); ?>
	<div class="clearfix"></div>
	
	<?php if ($this->config->map) { ?>
	<div id="map-canvas" style="width:100%;height: 400px" class="mb-2 mt-2"></div>
	<?php echo $this->form->renderField('coordinates'); ?>
	<?php echo $this->form->renderField('marker'); ?>
	<?php } ?>
	
	<div class="form-actions">
		<button type="button" class="btn btn-primary" onclick="document.locationForm.submit();"><?php echo JText::_('COM_RSEVENTSPRO_GLOBAL_SAVE'); ?></button>
		<a class="btn btn-danger" href="<?php echo rseventsproHelper::route('index.php?option=com_rseventspro&layout=locations'); ?>"><?php echo JText::_('COM_RSEVENTSPRO_GLOBAL_CANCEL'); ?></a>
	</div>
	
	<?php echo JHTML::_('form.token')."\n"; ?>
	<input type="hidden" name="option" value="com_rseventspro" />
	<input type="hidden" name="task" value="rseventspro.savelocations" />
	<input type="hidden" name="jform[id]" value="<?php echo $this->row->id; ?>" />
</form>