<?php
/**
* @package RSEvents!Pro
* @copyright (C) 2020 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/
defined( '_JEXEC' ) or die( 'Restricted access' );
JHtml::_('behavior.formvalidator');
JHtml::_('behavior.keepalive'); ?>

<form action="<?php echo JRoute::_('index.php?option=com_rseventspro&view=group&layout=edit&id='.(int) $this->item->id); ?>" method="post" name="adminForm" id="adminForm" autocomplete="off" class="form-validate form-horizontal">
	<div class="<?php echo RSEventsproAdapterGrid::row(); ?>">
		<div class="<?php echo RSEventsproAdapterGrid::column(12); ?>">
			<?php echo $this->form->renderField('name'); ?>
			<?php echo $this->form->renderField('jgroups'); ?>
			<?php echo $this->form->renderField('jusers'); ?>
		</div>
	</div>
	
	<div class="<?php echo RSEventsproAdapterGrid::row(); ?>">
		<div class="<?php echo RSEventsproAdapterGrid::column(12); ?>">
			<?php 
				$this->tabs->addTitle('COM_RSEVENTSPRO_GROUP_PERMISSIONS', 'group');
				$this->tabs->addContent($this->loadTemplate('general'));
				
				$this->tabs->addTitle('COM_RSEVENTSPRO_EVENT_OPTIONS', 'event');
				$this->tabs->addContent($this->loadTemplate('event'));
				
				echo $this->tabs->render(); 
			?>
		</div>
	</div>

	<?php echo JHTML::_('form.token'); ?>
	<input type="hidden" name="task" value="" />
	<?php echo $this->form->getInput('id'); ?>
	<?php echo JHTML::_('behavior.keepalive'); ?>
</form>