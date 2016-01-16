<?php
/**
* @version 1.0.0
* @package RSEvents!Pro 1.0.0
* @copyright (C) 2011 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/
defined( '_JEXEC' ) or die( 'Restricted access' );
jimport('joomla.html.editor'); ?>

<form action="<?php echo rseventsproHelper::route('index.php?option=com_rseventspro'); ?>" method="post" onsubmit="return rs_send_guests();">
	<h3><?php echo JText::_('COM_RSEVENTSPRO_SEND_MESSAGE_TO_GUESTS'); ?></h3>
	<div class="rs_clear"></div>
	
	<table class="rs_table" width="100%" cellspacing="0" cellpadding="4">
		<tr>
			<td><?php echo JText::_('COM_RSEVENTSPRO_SEND_MESSAGE_TO'); ?></td>
			<td>
				<table class="rs_table" width="100%" cellspacing="0" cellpadding="4">
					<tr>
						<td>
							<input type="checkbox" id="denied" name="jform[denied]" value="1" /> <label for="denied" id="d_option"><?php echo JText::_('COM_RSEVENTSPRO_SEND_MESSAGE_DENIED'); ?></label> <br />
							<input type="checkbox" id="pending" name="jform[pending]" value="1" /> <label for="pending" id="p_option"><?php echo JText::_('COM_RSEVENTSPRO_SEND_MESSAGE_PENDING'); ?></label> <br />
							<input type="checkbox" id="accepted" name="jform[accepted]" value="1" /> <label for="accepted" id="a_option"><?php echo JText::_('COM_RSEVENTSPRO_SEND_MESSAGE_ACCEPTED'); ?></label>
						</td>
						<td>
							<select name="jform[subscribers][]" id="subscribers" multiple="multiple" size="6" class="rs_edit_sel_small">
								<?php echo JHtml::_('select.options', $this->subscribers); ?>
							</select>
						</td>
					</tr>
				</table>
			</td>
		</tr>
		<tr>
			<td><label for="subject"><?php echo JText::_('COM_RSEVENTSPRO_SEND_MESSAGE_SUBJECT'); ?></label></td>
			<td>
				<input type="text" name="jform[subject]" id="subject" value="" size="50" class="rs_edit_inp_small" />
			</td>
		</tr>
		<tr>
			<td><label for="message"><?php echo JText::_('COM_RSEVENTSPRO_SEND_MESSAGE_BODY'); ?></label></td>
			<td>
				<?php echo JEditor::getInstance(JFactory::getConfig()->get('editor'))->display('jform[message]','','100%', '50%', 50, 10, rseventsproHelper::getConfig('enable_buttons','bool')); ?>
			</td>
		</tr>
		<tr>
			<td>&nbsp;</td>
			<td>
				<button type="submit" class="button btn btn-primary" onclick="return rs_send_guests();"><?php echo JText::_('COM_RSEVENTSPRO_GLOBAL_SEND'); ?></button> <?php echo JText::_('COM_RSEVENTSPRO_GLOBAL_OR'); ?> 
				<?php echo rseventsproHelper::redirect(false,JText::_('COM_RSEVENTSPRO_GLOBAL_CANCEL'),rseventsproHelper::route('index.php?option=com_rseventspro&layout=show&id='.rseventsproHelper::sef($this->event->id,$this->event->name))); ?>
			</td>
		</tr>
	</table>
	
	<?php echo JHTML::_('form.token')."\n"; ?>
	<input type="hidden" name="option" value="com_rseventspro" />
	<input type="hidden" name="task" value="rseventspro.message" />
	<input type="hidden" name="jform[id]" value="<?php echo $this->event->id; ?>" />
	<input type="hidden" name="tmpl" value="component" />
</form>