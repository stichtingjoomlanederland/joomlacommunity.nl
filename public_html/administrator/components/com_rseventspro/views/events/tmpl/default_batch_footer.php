<?php
/**
* @package RSEvents!Pro
* @copyright (C) 2020 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/
defined( '_JEXEC' ) or die( 'Restricted access' ); ?>

<div class="<?php echo RSEventsproAdapterGrid::styles(array('pull-left')); ?>">
	<label for="batch_all" class="checkbox inline btn btn-seconday"><input type="checkbox" id="batch_all" name="batch[all]" value="1" /> <b><?php echo JText::_('COM_RSEVENTSPRO_APPLY_TO_ALL_EVENTS'); ?></b></label>
</div>

<button onclick="Joomla.submitbutton('events.batch');" type="button" class="btn btn-primary"><?php echo JText::_('COM_RSEVENTSPRO_GLOBAL_PROCESS_BTN'); ?></button>
<button type="button" data-dismiss="modal" data-bs-dismiss="modal" class="btn btn-danger"><?php echo JText::_('COM_RSEVENTSPRO_GLOBAL_CANCEL_BTN'); ?></button>