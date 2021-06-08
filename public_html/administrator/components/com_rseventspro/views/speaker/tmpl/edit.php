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
	$image = '<span id="userImage"><img src="'.JURI::root().'components/com_rseventspro/assets/images/speakers/'.$this->item->image.'?nocache='.uniqid('').'" alt="" style="vertical-align: middle;" />';
	$image .= ' '.JHtml::image('com_rseventspro/loader.gif', '', array('id' => 'rse_loader', 'style' => 'vertical-align: middle; display: none;'), true);
	$image .= ' <a href="javascript:void(0)" onclick="rsepro_delete_speaker_image('.$this->item->id.')">'.JText::_('COM_RSEVENTSPRO_GLOBAL_DELETE_BTN').'</a>';
	$image .= '<br /></span>';
} ?>

<form action="<?php echo JRoute::_('index.php?option=com_rseventspro&view=speaker&layout=edit&id='.(int) $this->item->id); ?>" method="post" name="adminForm" id="adminForm" autocomplete="off" class="form-validate form-horizontal" enctype="multipart/form-data">
	<div class="<?php echo RSEventsproAdapterGrid::row(); ?>">
		<div class="<?php echo RSEventsproAdapterGrid::column(8); ?>">
			<?php echo $this->form->renderField('published'); ?>
			<?php echo $this->form->renderField('name'); ?>
			<?php echo RSEventsproAdapterGrid::renderField('&nbsp;',$image); ?>
			<?php echo $this->form->renderField('image'); ?>
			<?php echo $this->form->renderField('description'); ?>
		</div>
		<div class="<?php echo RSEventsproAdapterGrid::column(4); ?>">
			<?php echo $this->form->renderField('email'); ?>
			<?php echo $this->form->renderField('url'); ?>
			<?php echo $this->form->renderField('phone'); ?>
			<?php echo $this->form->renderField('facebook'); ?>
			<?php echo $this->form->renderField('twitter'); ?>
			<?php echo $this->form->renderField('linkedin'); ?>
			
			<fieldset class="options-form">
				<legend>
					<?php echo JText::_('COM_RSEVENTSPRO_CUSTOM_SOCIAL_LINKS'); ?>
				</legend>
				<button type="button" class="<?php echo RSEventsproAdapterGrid::styles(array('btn')); ?>" onclick="addCustomSocial()">+ <?php echo JText::_('COM_RSEVENTSPRO_ADD'); ?></button>
				
				<table class="table table-striped">
					<thead>
						<tr>
							<th width="30%"><?php echo JText::_('COM_RSEVENTSPRO_CUSTOM_SOCIAL_LINK_CLASS'); ?></th>
							<th><?php echo JText::_('COM_RSEVENTSPRO_CUSTOM_SOCIAL_LINK_URL'); ?></th>
							<th width="5%">&nbsp;</th>
						</tr>
					</thead>
					<tbody id="socialLinks">
						<?php if (!empty($this->item->custom)) { ?>
						<?php $i = 1; ?>
						<?php foreach ($this->item->custom as $custom) { ?>
						<tr id="custom00<?php echo $i; ?>">
							<td><input type="text" name="jform[custom][class][]" class="form-control" value="<?php echo $custom['class']; ?>" /></td>
							<td><input type="text" name="jform[custom][link][]" class="form-control" value="<?php echo $custom['link']; ?>" /></td>
							<td><a href="javascript:void(0)" class="btn btn-danger" onclick="removeCustomSocial('00<?php echo $i; ?>');">x</a></td>
						</tr>
						<?php $i++; ?>
						<?php } ?>
						<?php } ?>
					<tbody>
				</table>
			</fieldset>
		</div>
	</div>

	<?php echo JHTML::_('form.token'); ?>
	<input type="hidden" name="task" value="" />
	<?php echo $this->form->getInput('id'); ?>
	<?php echo JHTML::_('behavior.keepalive'); ?>
</form>