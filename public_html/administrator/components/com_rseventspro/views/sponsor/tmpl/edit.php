<?php
/**
* @package RSEvents!Pro
* @copyright (C) 2020 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/
defined( '_JEXEC' ) or die( 'Restricted access' );
JHtml::_('behavior.formvalidator');
JHtml::_('behavior.keepalive'); 

$image = '';
if (!empty($this->item->image)) {
	$image = '<span id="userImage"><img src="'.JURI::root().'components/com_rseventspro/assets/images/sponsors/'.$this->item->image.'?nocache='.uniqid('').'" alt="" style="vertical-align: middle;" />';
	$image .= ' '.JHtml::image('com_rseventspro/loader.gif', '', array('id' => 'rse_loader', 'style' => 'vertical-align: middle; display: none;'), true);
	$image .= ' <a href="javascript:void(0)" onclick="rsepro_delete_sponsor_image('.$this->item->id.')">'.JText::_('COM_RSEVENTSPRO_GLOBAL_DELETE_BTN').'</a>';
	$image .= '<br /><br /></span><div class="clearfix"></div>';
} ?>

<script type="text/javascript">
	Joomla.submitbutton = function(task) {
		if (task == 'sponsor.cancel' || document.formvalidator.isValid(document.getElementById('adminForm'))) {
			Joomla.submitform(task, document.getElementById('adminForm'));
		} else {
			alert('<?php echo $this->escape(JText::_('JGLOBAL_VALIDATION_FORM_FAILED'));?>');
		}
	}
</script>

<form action="<?php echo JRoute::_('index.php?option=com_rseventspro&view=sponsor&layout=edit&id='.(int) $this->item->id); ?>" method="post" name="adminForm" id="adminForm" autocomplete="off" class="form-validate form-horizontal" enctype="multipart/form-data">
	<div class="row-fluid">
		<?php echo JHtml::_('rsfieldset.start', 'adminform'); ?>
		<?php echo JHtml::_('rsfieldset.element', $this->form->getLabel('published'), $this->form->getInput('published')); ?>
		<?php echo JHtml::_('rsfieldset.element', $this->form->getLabel('name'), $this->form->getInput('name')); ?>
		<?php echo JHtml::_('rsfieldset.element', $this->form->getLabel('url'), $this->form->getInput('url')); ?>
		<?php echo JHtml::_('rsfieldset.element', $this->form->getLabel('image'), $image.$this->form->getInput('image')); ?>
		<?php echo JHtml::_('rsfieldset.end'); ?>
	</div>

	<?php echo JHTML::_('form.token'); ?>
	<input type="hidden" name="task" value="" />
	<?php echo $this->form->getInput('id'); ?>
	<?php echo JHTML::_('behavior.keepalive'); ?>
</form>