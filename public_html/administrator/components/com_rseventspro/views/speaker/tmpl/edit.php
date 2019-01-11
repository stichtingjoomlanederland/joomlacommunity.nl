<?php
/**
* @package RSEvents!Pro
* @copyright (C) 2015 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/
defined( '_JEXEC' ) or die( 'Restricted access' );
JHtml::_('behavior.formvalidator');
JHtml::_('behavior.keepalive'); 

$image = '';
if (!empty($this->item->image)) {
	$image = '<span id="userImage"><img src="'.JURI::root().'components/com_rseventspro/assets/images/speakers/'.$this->item->image.'?nocache='.uniqid('').'" alt="" style="vertical-align: middle;" />';
	$image .= ' '.JHtml::image('com_rseventspro/loader.gif', '', array('id' => 'rse_loader', 'style' => 'vertical-align: middle; display: none;'), true);
	$image .= ' <a href="javascript:void(0)" onclick="rsepro_delete_speaker_image('.$this->item->id.')">'.JText::_('COM_RSEVENTSPRO_GLOBAL_DELETE_BTN').'</a>';
	$image .= '<br /><br /></span><div class="clearfix"></div>';
} ?>

<script type="text/javascript">
	Joomla.submitbutton = function(task) {
		if (task == 'speaker.cancel' || document.formvalidator.isValid(document.getElementById('adminForm'))) {
			Joomla.submitform(task, document.getElementById('adminForm'));
		} else {
			alert('<?php echo $this->escape(JText::_('JGLOBAL_VALIDATION_FORM_FAILED'));?>');
		}
	}
</script>

<form action="<?php echo JRoute::_('index.php?option=com_rseventspro&view=speaker&layout=edit&id='.(int) $this->item->id); ?>" method="post" name="adminForm" id="adminForm" autocomplete="off" class="form-validate form-horizontal" enctype="multipart/form-data">
	<div class="row-fluid">
		<div class="span8">
			<?php echo JHtml::_('rsfieldset.start', 'adminform'); ?>
			<?php echo JHtml::_('rsfieldset.element', $this->form->getLabel('published'), $this->form->getInput('published')); ?>
			<?php echo JHtml::_('rsfieldset.element', $this->form->getLabel('name'), $this->form->getInput('name')); ?>
			<?php echo JHtml::_('rsfieldset.element', $this->form->getLabel('image'), $image.$this->form->getInput('image')); ?>
			<?php echo JHtml::_('rsfieldset.element', $this->form->getLabel('description'), $this->form->getInput('description')); ?>
			<?php echo JHtml::_('rsfieldset.end'); ?>
		</div>
		<div class="span4">
			<?php echo JHtml::_('rsfieldset.start', 'adminform'); ?>
			<?php echo JHtml::_('rsfieldset.element', $this->form->getLabel('email'), $this->form->getInput('email')); ?>
			<?php echo JHtml::_('rsfieldset.element', $this->form->getLabel('url'), $this->form->getInput('url')); ?>
			<?php echo JHtml::_('rsfieldset.element', $this->form->getLabel('phone'), $this->form->getInput('phone')); ?>
			<?php echo JHtml::_('rsfieldset.element', $this->form->getLabel('facebook'), $this->form->getInput('facebook')); ?>
			<?php echo JHtml::_('rsfieldset.element', $this->form->getLabel('twitter'), $this->form->getInput('twitter')); ?>
			<?php echo JHtml::_('rsfieldset.element', $this->form->getLabel('linkedin'), $this->form->getInput('linkedin')); ?>
			<?php echo JHtml::_('rsfieldset.end'); ?>
		</div>
	</div>

	<?php echo JHTML::_('form.token'); ?>
	<input type="hidden" name="task" value="" />
	<?php echo $this->form->getInput('id'); ?>
	<?php echo JHTML::_('behavior.keepalive'); ?>
</form>