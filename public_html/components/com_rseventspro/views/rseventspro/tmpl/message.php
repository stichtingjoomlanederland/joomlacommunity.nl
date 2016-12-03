<?php
/**
* @package RSEvents!Pro
* @copyright (C) 2015 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/
defined( '_JEXEC' ) or die( 'Restricted access' );
jimport('joomla.html.editor'); ?>

<form action="<?php echo rseventsproHelper::route('index.php?option=com_rseventspro'); ?>" method="post" onsubmit="return rs_send_guests();" id="adminForm" name="adminForm">
	<h3><?php echo JText::_('COM_RSEVENTSPRO_SEND_MESSAGE_TO_GUESTS'); ?></h3>
	
	<div class="form-horizontal">
		<div class="control-group">
			<div class="control-label">
				<label><?php echo JText::_('COM_RSEVENTSPRO_SEND_MESSAGE_TO'); ?></label>
			</div>
			<div class="controls">
				<div class="span4">
					<input type="checkbox" id="denied" name="jform[denied]" value="1" /> <label for="denied" id="d_option" class="checkbox inline"><?php echo JText::_('COM_RSEVENTSPRO_SEND_MESSAGE_DENIED'); ?></label> <br />
					<input type="checkbox" id="pending" name="jform[pending]" value="1" /> <label for="pending" id="p_option" class="checkbox inline"><?php echo JText::_('COM_RSEVENTSPRO_SEND_MESSAGE_PENDING'); ?></label> <br />
					<input type="checkbox" id="accepted" name="jform[accepted]" value="1" /> <label for="accepted" id="a_option" class="checkbox inline"><?php echo JText::_('COM_RSEVENTSPRO_SEND_MESSAGE_ACCEPTED'); ?></label>
				</div>
				
				<div class="span8">
					<select name="jform[subscribers][]" id="subscribers" multiple="multiple" size="6" class="span9">
						<?php echo JHtml::_('select.options', $this->subscribers); ?>
					</select>
				</div>
			</div>
		</div>
		
		<div class="control-group">
			<div class="control-label">
				<label for="subject"><?php echo JText::_('COM_RSEVENTSPRO_SEND_MESSAGE_SUBJECT'); ?></label>
			</div>
			<div class="controls">
				<input type="text" name="jform[subject]" id="subject" value="" size="50" class="span9" />
			</div>
		</div>
		<?php echo JEditor::getInstance(JFactory::getConfig()->get('editor'))->display('jform[message]','','100%', '50%', 50, 10, rseventsproHelper::getConfig('enable_buttons','bool')); ?>
	</div>
	<div class="clearfix"></div>
	<div class="form-actions">
		<button type="submit" class="button btn btn-primary" onclick="return rs_send_guests();"><?php echo JText::_('COM_RSEVENTSPRO_GLOBAL_SEND'); ?></button> <?php echo JText::_('COM_RSEVENTSPRO_GLOBAL_OR'); ?> 
		<?php echo rseventsproHelper::redirect(false,JText::_('COM_RSEVENTSPRO_GLOBAL_CANCEL'),rseventsproHelper::route('index.php?option=com_rseventspro&layout=show&id='.rseventsproHelper::sef($this->event->id,$this->event->name),false,rseventsproHelper::itemid($this->event->id))); ?>
	</div>
	
	<?php echo JHTML::_('form.token')."\n"; ?>
	<input type="hidden" name="option" value="com_rseventspro" />
	<input type="hidden" name="task" value="rseventspro.message" />
	<input type="hidden" name="jform[id]" value="<?php echo $this->event->id; ?>" />
	<input type="hidden" name="tmpl" value="component" />
</form>