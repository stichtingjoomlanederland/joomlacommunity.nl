<?php
/**
* @package RSEvents!Pro
* @copyright (C) 2020 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/
defined( '_JEXEC' ) or die( 'Restricted access' );
JHtml::_('behavior.formvalidator');
JFactory::getApplication()->input->set('tmpl', 'component');
$form = JForm::getInstance('speaker', JPATH_ADMINISTRATOR.'/components/com_rseventspro/models/forms/speaker.xml', array('control' => 'jform')); ?>

<script type="text/javascript">
	Joomla.submitbutton = function(task) {
		if (document.formvalidator.isValid(document.getElementById('speakerForm'))) {
			Joomla.submitform(task, document.getElementById('speakerForm'));
		}
	}
</script>

<form class="form-validate form-horizontal" id="speakerForm" name="speakerForm" method="post" action="<?php echo rseventsproHelper::route('index.php?option=com_rseventspro'); ?>" enctype="multipart/form-data">
	<div class="container-fluid">
		<?php foreach ($form->getFieldset() as $field) { ?>
		<?php if ($field->fieldname == 'published' || $field->fieldname == 'id') continue; ?>
		<?php echo $form->renderField($field->fieldname); ?>
		<?php } ?>
		
		<div class="control-group">
			<div class="controls">
				<fieldset class="options-form">
					<legend>
						<?php echo JText::_('COM_RSEVENTSPRO_CUSTOM_SOCIAL_LINKS'); ?>
						<div class="<?php echo RSEventsproAdapterGrid::styles(array('pull-right')); ?>"><button type="button" class="<?php echo RSEventsproAdapterGrid::styles(array('btn')); ?>" onclick="addCustomSocial()">+</button></div>
					</legend>
					<table class="table table-striped">
						<thead>
							<tr>
								<th width="30%"><?php echo JText::_('COM_RSEVENTSPRO_CUSTOM_SOCIAL_LINK_CLASS'); ?></th>
								<th><?php echo JText::_('COM_RSEVENTSPRO_CUSTOM_SOCIAL_LINK_URL'); ?></th>
								<th width="5%">&nbsp;</th>
							</tr>
						</thead>
						<tbody id="socialLinks">
						<tbody>
					</table>
				</fieldset>
			</div>
		</div>
		
		<?php if ($this->config->modaltype == 2) { ?>
		<div class="control-group">
			<div class="control-label"><label>&nbsp;</label></div>
			<div class="controls"><button type="button" onclick="Joomla.submitbutton('rseventspro.savespeaker')" class="btn btn-primary"><?php echo JText::_('COM_RSEVENTSPRO_GLOBAL_SAVE'); ?></button></div>
		</div>
		<?php } ?>
	</div>
	
	<button id="rsepro-save-speaker" type="button" onclick="Joomla.submitbutton('rseventspro.savespeaker')" style="display:none;"></button>
	<input type="hidden" name="task" value="rseventspro.savespeaker" />
	<?php echo JHTML::_('form.token'); ?>
</form>