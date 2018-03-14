<?php
/**
* @package RSEvents!Pro
* @copyright (C) 2015 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/
defined( '_JEXEC' ) or die( 'Restricted access' ); 

$image = '';
if (!empty($this->data->image)) {
	$image = '<span id="userImage"><img src="'.JURI::root().'components/com_rseventspro/assets/images/users/'.$this->data->image.'?nocache='.uniqid('').'" alt="" style="vertical-align: middle;" />';
	$image .= ' <a href="'.JRoute::_('index.php?option=com_rseventspro&task=rseventspro.deleteimage&id='.rseventsproHelper::sef($this->data->id, $this->data->name),false).'">'.JText::_('COM_RSEVENTSPRO_GLOBAL_DELETE').'</a>';
	$image .= '<br /><br /></span><div class="clearfix"></div>';
} ?>

<h1><?php echo JText::sprintf('COM_RSEVENTSPRO_EDIT_USER',$this->data->name); ?></h1>

<form action="<?php echo rseventsproHelper::route('index.php?option=com_rseventspro&layout=edituser'); ?>" method="post" name="edituserForm" id="edituserForm" enctype="multipart/form-data">
	<div class="control-group">
		<div class="control-label">
			<?php echo $this->form->getLabel('name'); ?>
		</div>
		<div class="controls">
			<?php echo $this->form->getInput('name'); ?>
		</div>
	</div>
	<div class="control-group">
		<div class="control-label">
			<?php echo $this->form->getLabel('image'); ?>
		</div>
		<div class="controls">
			<?php echo $image.$this->form->getInput('image'); ?>
		</div>
	</div>
	
	<div class="control-group clearfix">
		<div class="controls">
			<?php echo JEditor::getInstance(JFactory::getConfig()->get('editor'))->display('jform[description]',$this->data->description,'80%', '70%', 20, 7, rseventsproHelper::getConfig('enable_buttons','bool')); ?>
		</div>
	</div>
	
	<div class="form-actions">
		<button type="button" class="button btn btn-primary" onclick="document.edituserForm.submit();"><?php echo JText::_('COM_RSEVENTSPRO_GLOBAL_SAVE'); ?></button>
		<a class="btn" href="<?php echo JRoute::_('index.php?option=com_rseventspro&layout=user&id='.rseventsproHelper::sef($this->data->id, $this->data->name), false); ?>"><?php echo JText::_('COM_RSEVENTSPRO_GLOBAL_CANCEL'); ?></a>
	</div>
	
	<?php echo JHTML::_('form.token')."\n"; ?>
	<input type="hidden" name="option" value="com_rseventspro" />
	<input type="hidden" name="task" value="rseventspro.saveuser" />
	<input type="hidden" name="jform[id]" value="<?php echo $this->data->id; ?>" />
</form>