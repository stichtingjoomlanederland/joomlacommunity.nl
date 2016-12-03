<?php
/**
* @package RSFiles!
* @copyright (C) 2010-2014 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/

defined('_JEXEC') or die('Restricted access');
JHTML::_('behavior.keepalive');
JHTML::_('behavior.formvalidation'); ?>

<script language="javascript" type="text/javascript">
Joomla.submitbutton = function(task) {
	if (task == 'license.cancel' || document.formvalidator.isValid(document.id('adminForm'))) {
		Joomla.submitform(task, document.getElementById('adminForm'));
	} else {
		alert('<?php echo $this->escape(JText::_('JGLOBAL_VALIDATION_FORM_FAILED'));?>');
	}
}
</script>

<div class="row-fluid">
	<div class="span12">
		<form action="<?php echo JRoute::_('index.php?option=com_rsfiles&view=license&layout=edit&IdLicense='.(int) $this->item->IdLicense); ?>" method="post" name="adminForm" id="adminForm" autocomplete="off" class="form-validate form-horizontal" enctype="multipart/form-data">
		<?php echo JHtml::_('rsfieldset.start', 'adminform'); ?>
		<?php echo JHtml::_('rsfieldset.element', $this->form->getLabel('published'), $this->form->getInput('published')); ?>
		<?php echo JHtml::_('rsfieldset.element', $this->form->getLabel('LicenseName'), $this->form->getInput('LicenseName')); ?>
		<?php echo JHtml::_('rsfieldset.element', $this->form->getLabel('LicenseText'), '<span class="rs_extra">'.$this->form->getInput('LicenseText').'</span>'); ?>
		<?php echo JHtml::_('rsfieldset.end'); ?>
		
		<?php echo JHTML::_('form.token'); ?>
		<input type="hidden" name="task" value="" />
		<?php echo $this->form->getInput('IdLicense'); ?>
		</form>
	</div>
</div>