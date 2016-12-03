<?php
/**
 * @package	AcyMailing for Joomla!
 * @version	5.5.0
 * @author	acyba.com
 * @copyright	(C) 2009-2016 ACYBA S.A.R.L. All rights reserved.
 * @license	GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */
defined('_JEXEC') or die('Restricted access');
?><?php
echo $this->tabs->startPanel(JText::_('ACY_INBOX_ACTIONS'), 'mail_inboxactions'); ?>
<?php
if($this->config->get('inboxactionswhitelist', 1)){
	$toggleClass = acymailing_get('helper.toggle');
	$notremind = '<small style="float:right;margin-right:30px;position:relative;">'.$toggleClass->delete('acymailing_messages_warning', 'inboxactionswhitelist_0', 'config', false, JText::_('DONT_REMIND')).'</small>';
	acymailing_display(JText::_('ACY_INBOX_ACTIONS_WHITELIST').' <a target="_blank" href="http://www.acyba.com/index.php?option=com_updateme&ctrl=redirect&page=inboxactions">'.JText::_('TELL_ME_MORE').'</a>'.$notremind, 'warning');
}
?>
	<table width="100%" class="acymailing_smalltable" id="metadatatable">
		<tr>
			<td class="paramlist_key">
				<label for="datamailparamsaction">
					<?php echo JText::_('ACY_ACTION'); ?>
				</label>
			</td>
			<td class="paramlist_value">
				<?php $ordering = array();
				$ordering[] = JHTML::_('select.option', "none", JText::_('ACY_NONE'));
				$ordering[] = JHTML::_('select.option', "confirm", JText::_('ACY_BUTTON_CONFIRM'));
				$ordering[] = JHTML::_('select.option', "save", JText::_('ACY_BUTTON_SAVE'));
				$ordering[] = JHTML::_('select.option', "goto", JText::_('ACY_GOTO'));
				echo JHTML::_('select.genericlist', $ordering, 'data[mail][params][action]', 'size="1" onchange="displayActionOptions(this.value);" style="width:150px;"', 'value', 'text', @$this->mail->params['action']); ?>
			</td>
		</tr>
		<tr class="action_option action_goto action_confirm action_save">
			<td class="paramlist_key">
				<label for="iba_actionbtntext">
					<?php echo JText::_('ACY_BUTTON_TEXT'); ?>
				</label>
			</td>
			<td class="paramlist_value">
				<input id="iba_actionbtntext" type="text" name="data[mail][params][actionbtntext]" rows="5" cols="30" value="<?php echo @$this->mail->params['actionbtntext']; ?>"/>
			</td>
		</tr>
		<tr class="action_option action_goto action_confirm action_save">
			<td class="paramlist_key">
				<label for="iba_actionurl">
					<?php echo JText::_('URL'); ?>
				</label>
			</td>
			<td class="paramlist_value">
				<input id="iba_actionurl" type="text" name="data[mail][params][actionurl]" placeholder="http://..." rows="5" cols="30" value="<?php echo @$this->mail->params['actionurl']; ?>"/>
			</td>
		</tr>
	</table>
	<script type="text/javascript">
		<!--
		function displayActionOptions(selected){
			var options = document.querySelectorAll(".action_option");
			for(var c = 0; c < options.length; c++){
				if(options[c].style){
					options[c].style.display = 'none';
				}
			}
			if(selected == "none") return;

			options = document.querySelectorAll(".action_" + selected);
			for(var c = 0; c < options.length; c++){
				if(options[c].style){
					options[c].style.display = '';
				}
			}
		}
		displayActionOptions('<?php echo empty($this->mail->params['action']) ? 'none' : $this->mail->params['action']; ?>');
		-->
	</script>
<?php echo $this->tabs->endPanel();
