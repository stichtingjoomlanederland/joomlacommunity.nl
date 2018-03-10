<?php
/**
* @package RSEvents!Pro
* @copyright (C) 2015 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/
defined( '_JEXEC' ) or die( 'Restricted access' ); ?>

<div class="pull-left">
	<input type="checkbox" id="batch_all" name="batch[all]" value="1" /> <label for="batch_all" class="checkbox inline"><b><?php echo JText::_('COM_RSEVENTSPRO_APPLY_TO_ALL_EVENTS'); ?></b></label>
</div>

<button type="button" data-dismiss="modal" class="btn"><?php echo JText::_('COM_RSEVENTSPRO_GLOBAL_CANCEL_BTN'); ?></button>
<button onclick="Joomla.submitbutton('events.batch');" type="button" class="btn btn-primary"><?php echo JText::_('COM_RSEVENTSPRO_GLOBAL_PROCESS_BTN'); ?></button>