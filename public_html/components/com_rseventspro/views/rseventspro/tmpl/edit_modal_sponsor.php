<?php
/**
* @package RSEvents!Pro
* @copyright (C) 2020 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/
defined( '_JEXEC' ) or die( 'Restricted access' );
JHtml::_('behavior.formvalidator');
JFactory::getApplication()->input->set('tmpl', 'component');
$form = JForm::getInstance('location', JPATH_ADMINISTRATOR.'/components/com_rseventspro/models/forms/sponsor.xml', array('control' => 'jform')); ?>

<script type="text/javascript">
	Joomla.submitbutton = function(task) {
		if (document.formvalidator.isValid(document.getElementById('sponsorForm'))) {
			Joomla.submitform(task, document.getElementById('sponsorForm'));
		}
	}
</script>

<form class="form-validate form-horizontal" id="sponsorForm" name="sponsorForm" method="post" action="<?php echo rseventsproHelper::route('index.php?option=com_rseventspro'); ?>" enctype="multipart/form-data">
	<div class="container-fluid">
		<?php foreach ($form->getFieldset() as $field) { ?>
		<?php if ($field->fieldname == 'published' || $field->fieldname == 'id') continue; ?>
		<?php echo $form->renderField($field->fieldname); ?>
		<?php } ?>
		<?php if ($this->config->modaltype == 2) { ?>
		<div class="control-group">
			<div class="control-label"><label>&nbsp;</label></div>
			<div class="controls"><button type="button" onclick="Joomla.submitbutton('rseventspro.savesponsor')" class="btn btn-primary"><?php echo JText::_('COM_RSEVENTSPRO_GLOBAL_SAVE'); ?></button></div>
		</div>
		<?php } ?>
	</div>
	
	<button id="rsepro-save-sponsor" type="button" onclick="Joomla.submitbutton('rseventspro.savesponsor')" style="display:none;"></button>
	<input type="hidden" name="task" value="rseventspro.savesponsor" />
	<?php echo JHTML::_('form.token'); ?>
</form>