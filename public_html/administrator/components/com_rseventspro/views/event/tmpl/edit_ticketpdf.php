<?php
/**
* @package RSEvents!Pro
* @copyright (C) 2015 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/
defined( '_JEXEC' ) or die( 'Restricted access' ); ?>
<legend><?php echo JText::_('COM_RSEVENTSPRO_TICKET_PDF'); ?></legend>

<div class="control-group">
	<div class="control-label">
		<label for="jform_ticket_pdf"><?php echo JText::_('COM_RSEVENTSPRO_TICKET_PDF_ATTACH'); ?></label>
	</div>
	<div class="controls">
		<select name="jform[ticket_pdf]" id="jform_ticket_pdf" class="span1">
			<?php echo JHtml::_('select.options', $this->eventClass->yesno(), 'value', 'text', $this->item->ticket_pdf, true); ?>
		</select>
	</div>
</div>

<div class="control-group">
	<div class="controls">
		<?php echo JEditor::getInstance(JFactory::getConfig()->get('editor'))->display('jform[ticket_pdf_layout]',$this->escape($this->item->ticket_pdf_layout),'100%', '50%', 70, 10); ?>
	</div>
</div>

<div class="form-actions">
	<button class="btn btn-success rsepro-event-update" type="button"><?php echo JText::_('COM_RSEVENTSPRO_UPDATE_EVENT'); ?></button>
	<button class="btn btn-danger rsepro-event-cancel" type="button"><?php echo JText::_('COM_RSEVENTSPRO_GLOBAL_CANCEL_BTN'); ?></button>
	<button type="button" onclick="window.open('<?php echo JRoute::_('index.php?option=com_rseventspro&view=placeholders&type=pdf&tmpl=component', false); ?>', 'placeholdersWindow', 'toolbar=no, scrollbars=yes, resizable=yes, top=200, left=500, width=600, height=700');" class="btn btn-primary button"><?php echo JText::_('COM_RSEVENTSPRO_EMAIL_PLACEHOLDERS'); ?></button>
</div>