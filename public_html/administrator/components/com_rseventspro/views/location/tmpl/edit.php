<?php
/**
* @package RSEvents!Pro
* @copyright (C) 2020 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/
defined( '_JEXEC' ) or die( 'Restricted access' );
JHtml::_('behavior.formvalidator');
JHtml::_('behavior.keepalive'); 
$col = empty($this->config->map) ? 12 : 7; ?>

<form action="<?php echo JRoute::_('index.php?option=com_rseventspro&view=location&layout=edit&id='.(int) $this->item->id); ?>" method="post" name="adminForm" id="adminForm" autocomplete="off" class="form-validate form-horizontal">
	<div class="<?php echo RSEventsproAdapterGrid::row(); ?>">
		<div class="<?php echo RSEventsproAdapterGrid::column($col); ?>">
			<?php $input = $this->config->map ? ' <button type="button" id="rsepro-pinpoint" class="btn btn-secondary">'.JText::_('COM_RSEVENTSPRO_LOCATION_PINPOINT').'</button>' : ''; ?>
			<?php $inputGroup = $input ? RSEventsproAdapterGrid::inputGroup($this->form->getInput('address'), null, $input) : $this->form->getInput('address'); ?>
			<?php echo $this->form->renderField('published'); ?>
			<?php echo $this->form->renderField('name'); ?>
			<?php echo $this->form->renderField('url'); ?>
			<?php echo RSEventsproAdapterGrid::renderField($this->form->getLabel('address'), $inputGroup); ?>
			<?php if (rseventsproHelper::isGallery()) echo $this->form->renderField('gallery_tags'); ?>
			<?php if (!empty($this->config->map)) echo $this->form->renderField('coordinates'); ?>
			<?php if (!empty($this->config->map)) echo $this->form->renderField('marker'); ?>
		</div>
		
		<?php if (!empty($this->config->map)) { ?>
		<div class="<?php echo RSEventsproAdapterGrid::column(5); ?>">
			<div style="margin-left:60px;">
				<div id="map-canvas" style="width: 100%; height: 400px"></div>
			</div>
		</div>
		<?php } ?>
	</div>
	<?php echo $this->form->getInput('description'); ?>

	<?php echo JHTML::_('form.token'); ?>
	<input type="hidden" name="task" value="" />
	<?php echo $this->form->getInput('id'); ?>
	<?php echo JHTML::_('behavior.keepalive'); ?>
</form>