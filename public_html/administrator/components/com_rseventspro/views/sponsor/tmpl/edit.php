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

<form action="<?php echo JRoute::_('index.php?option=com_rseventspro&view=sponsor&layout=edit&id='.(int) $this->item->id); ?>" method="post" name="adminForm" id="adminForm" autocomplete="off" class="form-validate form-horizontal" enctype="multipart/form-data">
	<?php echo $this->form->renderField('published'); ?>
	<?php echo $this->form->renderField('name'); ?>
	<?php echo $this->form->renderField('url'); ?>
	<?php echo RSEventsproAdapterGrid::renderField('&nbsp;',$image); ?>
	<?php echo $this->form->renderField('image'); ?>

	<?php echo JHTML::_('form.token'); ?>
	<input type="hidden" name="task" value="" />
	<?php echo $this->form->getInput('id'); ?>
	<?php echo JHTML::_('behavior.keepalive'); ?>
</form>