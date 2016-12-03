<?php
/**
* @package RSEvents!Pro
* @copyright (C) 2015 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/
defined( '_JEXEC' ) or die( 'Restricted access' );
JHtml::_('behavior.formvalidation');
JHtml::_('behavior.keepalive'); ?>

<script type="text/javascript">
	Joomla.submitbutton = function(task) {
		if (task == 'location.cancel' || document.formvalidator.isValid(document.id('adminForm'))) {
			Joomla.submitform(task, document.getElementById('adminForm'));
		} else {
			alert('<?php echo $this->escape(JText::_('JGLOBAL_VALIDATION_FORM_FAILED'));?>');
		}
	}
	
	<?php if ($this->config->enable_google_maps) { ?>
	jQuery(document).ready(function (){
		jQuery('#map-canvas').rsjoomlamap({
			address: 'jform_address',
			coordinates: 'jform_coordinates',
			pinpointBtn: 'rsepro-pinpoint',
			zoom: <?php echo (int) $this->config->google_map_zoom ?>,
			center: '<?php echo $this->config->google_maps_center; ?>',
			markerDraggable: true,
			resultsWrapperClass: 'rsepro-locations-results-wrapper',
			resultsClass: 'rsepro-locations-results'
		});
	});
	<?php } ?>
</script>

<form action="<?php echo JRoute::_('index.php?option=com_rseventspro&view=location&layout=edit&id='.(int) $this->item->id); ?>" method="post" name="adminForm" id="adminForm" autocomplete="off" class="form-validate form-horizontal">
	<div class="row-fluid">
		<div class="span7 rswidth-50 rsfltlft">
			<?php $input = $this->config->enable_google_maps ? ' <button type="button" id="rsepro-pinpoint" class="btn button">'.JText::_('COM_RSEVENTSPRO_LOCATION_PINPOINT').'</button>' : ''; ?>
			<?php echo JHtml::_('rsfieldset.start', 'adminform'); ?>
			<?php echo JHtml::_('rsfieldset.element', $this->form->getLabel('published'), $this->form->getInput('published')); ?>
			<?php echo JHtml::_('rsfieldset.element', $this->form->getLabel('name'), $this->form->getInput('name')); ?>
			<?php echo JHtml::_('rsfieldset.element', $this->form->getLabel('url'), $this->form->getInput('url')); ?>
			<?php echo JHtml::_('rsfieldset.element', $this->form->getLabel('address'), $this->form->getInput('address').$input); ?>
			<?php if (rseventsproHelper::isGallery()) { ?>
			<?php echo JHtml::_('rsfieldset.element', $this->form->getLabel('gallery_tags'), $this->form->getInput('gallery_tags')); ?>
			<?php } ?>
			<?php if ($this->config->enable_google_maps) { ?>
			<?php echo JHtml::_('rsfieldset.element', $this->form->getLabel('coordinates'), $this->form->getInput('coordinates')); ?>
			<?php } ?>
			<?php echo JHtml::_('rsfieldset.end'); ?>
			
			<?php echo $this->form->getInput('description'); ?>
		</div>
		
		<div class="span5 rsfltrgt rswidth-50">
			<?php if ($this->config->enable_google_maps) { ?>
			<div style="margin-left:60px;">
				<div id="map-canvas" style="width: 100%; height: 400px"></div>
			</div>
			<?php } else { ?>
			<?php echo JHtml::_('rsfieldset.start', 'adminform'); ?>
			<?php echo JHtml::_('rsfieldset.element', $this->form->getLabel('enable_google_maps','config'), $this->form->getInput('enable_google_maps','config')); ?>
			<?php echo JHtml::_('rsfieldset.end'); ?>
			<div style="margin-top:5px; text-align:center;">
				<img src="<?php echo JURI::root(); ?>administrator/components/com_rseventspro/assets/images/map.png" alt="" />
			</div>
			<?php } ?>
		</div>
	</div>

	<?php echo JHTML::_('form.token'); ?>
	<input type="hidden" name="task" value="" />
	<?php echo $this->form->getInput('id'); ?>
	<?php echo JHTML::_('behavior.keepalive'); ?>
</form>