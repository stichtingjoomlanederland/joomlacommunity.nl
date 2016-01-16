<?php
/**
* @package RSEvents!Pro
* @copyright (C) 2015 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/
defined( '_JEXEC' ) or die( 'Restricted access' ); ?>

<div class="rsep-modal-header">
	<button data-dismiss="modal" class="close" type="button">×</button>
	<h3><?php echo JText::_('COM_RSEVENTSPRO_BATCH_EVENTS'); ?></h3>
</div>
<div class="rsep-modal-body rsep-modal-batch">
	<?php 
		$this->tabs->title('COM_RSEVENTSPRO_BATCH_GENERAL_TAB', 'general');
		$content = $this->loadTemplate('batch_other');
		$this->tabs->content($content);
		$this->tabs->title('COM_RSEVENTSPRO_BATCH_OPTIONS_TAB', 'options');
		$content = $this->loadTemplate('batch_options');
		$this->tabs->content($content);
		echo $this->tabs->render(); 
	?>
</div>
<div class="rsep-modal-footer">
	<div class="pull-left">
		<input type="checkbox" id="batch_all" name="batch[all]" value="1" /> <label for="batch_all" class="checkbox inline"><b><?php echo JText::_('COM_RSEVENTSPRO_APPLY_TO_ALL_EVENTS'); ?></b></label>
	</div>
	
	<button type="button" data-dismiss="modal" class="btn"><?php echo JText::_('COM_RSEVENTSPRO_GLOBAL_CANCEL_BTN'); ?></button>
	<button onclick="Joomla.submitbutton('events.batch');" type="button" class="btn btn-primary"><?php echo JText::_('COM_RSEVENTSPRO_GLOBAL_PROCESS_BTN'); ?></button>
</div>