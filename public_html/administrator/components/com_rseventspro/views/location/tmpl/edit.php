<?php
/**
* @package RSEvents!Pro
* @copyright (C) 2020 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/
defined( '_JEXEC' ) or die( 'Restricted access' );
JHtml::_('behavior.formvalidator');
JHtml::_('behavior.keepalive'); ?>

<script type="text/javascript">
	Joomla.submitbutton = function(task) {
		if (task == 'location.cancel' || document.formvalidator.isValid(document.getElementById('adminForm'))) {
			Joomla.submitform(task, document.getElementById('adminForm'));
		} else {
			alert('<?php echo $this->escape(JText::_('JGLOBAL_VALIDATION_FORM_FAILED'));?>');
		}
	}
</script>

<form action="<?php echo JRoute::_('index.php?option=com_rseventspro&view=location&layout=edit&id='.(int) $this->item->id); ?>" method="post" name="adminForm" id="adminForm" autocomplete="off" class="form-validate form-horizontal">
	<div class="row-fluid">
		<div class="span<?php echo empty($this->config->map) ? 12 : 7; ?>">
			<?php $input = $this->config->map ? ' <button type="button" id="rsepro-pinpoint" class="btn button">'.JText::_('COM_RSEVENTSPRO_LOCATION_PINPOINT').'</button>' : ''; ?>
			<?php echo JHtml::_('rsfieldset.start', 'adminform'); ?>
			<?php echo JHtml::_('rsfieldset.element', $this->form->getLabel('published'), $this->form->getInput('published')); ?>
			<?php echo JHtml::_('rsfieldset.element', $this->form->getLabel('name'), $this->form->getInput('name')); ?>
			<?php echo JHtml::_('rsfieldset.element', $this->form->getLabel('url'), $this->form->getInput('url')); ?>
			<?php echo JHtml::_('rsfieldset.element', $this->form->getLabel('address'), $this->form->getInput('address').$input); ?>
			<?php if (rseventsproHelper::isGallery()) { ?>
			<?php echo JHtml::_('rsfieldset.element', $this->form->getLabel('gallery_tags'), $this->form->getInput('gallery_tags')); ?>
			<?php } ?>
			<?php if (!empty($this->config->map)) { ?>
			<?php echo JHtml::_('rsfieldset.element', $this->form->getLabel('coordinates'), $this->form->getInput('coordinates')); ?>
			<?php echo JHtml::_('rsfieldset.element', $this->form->getLabel('marker'), $this->form->getInput('marker')); ?>
			<?php } ?>
			<?php echo JHtml::_('rsfieldset.end'); ?>
			<?php echo $this->form->getInput('description'); ?>
		</div>
		
		<?php if (!empty($this->config->map)) { ?>
		<div class="span5">
			<div style="margin-left:60px;">
				<div id="map-canvas" style="width: 100%; height: 400px"></div>
			</div>
		</div>
		<?php } ?>
	</div>

	<?php echo JHTML::_('form.token'); ?>
	<input type="hidden" name="task" value="" />
	<?php echo $this->form->getInput('id'); ?>
	<?php echo JHTML::_('behavior.keepalive'); ?>
</form>