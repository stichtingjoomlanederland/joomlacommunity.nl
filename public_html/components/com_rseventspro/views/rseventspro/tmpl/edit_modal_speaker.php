<?php
/**
* @package RSEvents!Pro
* @copyright (C) 2015 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/
defined( '_JEXEC' ) or die( 'Restricted access' );
JHtml::_('behavior.formvalidator');
JFactory::getApplication()->input->set('tmpl', 'component');
$form = JForm::getInstance('location', JPATH_ADMINISTRATOR.'/components/com_rseventspro/models/forms/speaker.xml', array('control' => 'jform')); ?>

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
	</div>
	
	<button id="rsepro-save-speaker" type="button" onclick="Joomla.submitbutton('rseventspro.savespeaker')" style="display:none;"></button>
	<input type="hidden" name="task" value="rseventspro.savespeaker" />
	<?php echo JHTML::_('form.token'); ?>
</form>