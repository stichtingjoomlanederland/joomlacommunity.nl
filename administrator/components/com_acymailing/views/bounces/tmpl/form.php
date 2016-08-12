<?php
/**
 * @package	AcyMailing for Joomla!
 * @version	5.5.0
 * @author	acyba.com
 * @copyright	(C) 2009-2016 ACYBA S.A.R.L. All rights reserved.
 * @license	GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */
defined('_JEXEC') or die('Restricted access');
?><div id="acy_content">
	<div id="iframedoc"></div>
	<form action="<?php echo JRoute::_('index.php?option=com_acymailing&ctrl=bounces'); ?>" method="post" name="adminForm" autocomplete="off" id="adminForm">
		<div class="acyblockoptions">
			<table class="acymailing_table">
				<tr>
					<td class="acykey" valign="top">
						<label for="name">
							<?php echo JText::_('ACY_NAME'); ?>
						</label>
					</td>
					<td>
						<input type="text" name="data[rule][name]" id="name" class="inputbox" style="width:200px" value="<?php echo $this->escape(@$this->rule->name); ?>"/>
					</td>
				</tr>
				<tr>
					<td class="acykey" valign="top">
						<label>
							<?php echo JText::_('ENABLED'); ?>
						</label>
					</td>
					<td>
						<?php echo JHTML::_('acyselect.booleanlist', "data[rule][published]", '', @$this->rule->published); ?>
					</td>
				</tr>
				<tr>
					<td class="acykey" valign="top">
						<label for="regex">
							<?php echo JText::_('BOUNCE_REGEX'); ?>
						</label>
					</td>
					<td>
						#<input type="text" name="data[rule][regex]" id="regex" class="inputbox" size="100" value="<?php echo $this->escape(@$this->rule->regex); ?>"/>#ims
						<?php if(!empty($this->rule->regex)){
							preg_match('#'.$this->rule->regex.'#i', 'test');
						} ?>
					</td>
				</tr>

				<tr>
					<td class="acykey" valign="top">
						<label>
							<?php echo JText::_('REGEX_ON'); ?>
						</label>
					</td>
					<td>

						<div style="clear:both;"><input id="execon_senderinfo" <?php if(isset($this->rule->executed_on['senderinfo'])) echo 'checked="checked"' ?> type="checkbox" value="1" name="data[rule][executed_on][senderinfo]"/> <label for="execon_senderinfo"><?php echo JText::_('SENDER_INFORMATIONS'); ?></label></div>
						<div style="clear:both;"><input id="execon_subject" <?php if(isset($this->rule->executed_on['subject'])) echo 'checked="checked"' ?> type="checkbox" value="1" name="data[rule][executed_on][subject]"/> <label for="execon_subject"><?php echo JText::_('JOOMEXT_SUBJECT'); ?></label></div>
						<div style="clear:both;"><input id="execon_body" <?php if(isset($this->rule->executed_on['body'])) echo 'checked="checked"' ?> type="checkbox" value="1" name="data[rule][executed_on][body]"/> <label for="execon_body"><?php echo JText::_('ACY_BODY'); ?></label></div>
					</td>
				</tr>
				<tr>
					<td class="acykey" valign="top">
						<label>
							<?php echo JText::_('STATISTICS'); ?>
						</label>
					</td>
					<td>
						<input type="checkbox" name="data[rule][action_user][stats]" id="action_user_stats" class="checkbox" value="1" <?php if(isset($this->rule->action_user['stats'])) echo 'checked="checked"' ?> /> <label for="action_user_stats"><?php echo JText::_('BOUNCE_STATS'); ?></label>
					</td>
				</tr>
				<tr>
					<td class="acykey" valign="top">
						<label>
							<?php echo JText::_('BOUNCE_ACTION'); ?>
						</label>
					</td>
					<td>
						<?php echo JText::sprintf('BOUNCE_EXEC_MIN', '<input type="text" style="width:30px;" name="data[rule][action_user][min]" value="'.intval(@$this->rule->action_user['min']).'" />'); ?>
						<div style="clear:both;"><input id="action_user_removesub" <?php if(isset($this->rule->action_user['removesub'])) echo 'checked="checked"' ?> type="checkbox" value="1" name="data[rule][action_user][removesub]"/> <label for="action_user_removesub"><?php echo JText::_('REMOVE_SUB'); ?></label></div>

						<div style="clear:both;"><input id="action_user_unsub" <?php if(isset($this->rule->action_user['unsub'])) echo 'checked="checked"' ?> type="checkbox" value="1" name="data[rule][action_user][unsub]"/> <label for="action_user_unsub"><?php echo JText::_('UNSUB_USER'); ?></label></div>
						<div style="clear:both;"><input id="action_user_sub" <?php if(isset($this->rule->action_user['sub'])) echo 'checked="checked"' ?> type="checkbox" value="1" name="data[rule][action_user][sub]"/> <label
								for="action_user_sub"><?php echo JText::_('SUBSCRIBE_USER'); ?> </label> <?php echo $this->lists->display('data[rule][action_user][subscribeto]', @$this->rule->action_user['subscribeto'], false); ?></div>
						<div style="clear:both;"><input id="action_user_block" <?php if(isset($this->rule->action_user['block'])) echo 'checked="checked"' ?> type="checkbox" value="1" name="data[rule][action_user][block]"/> <label for="action_user_block"><?php echo JText::_('BLOCK_USER'); ?></label></div>
						<div style="clear:both;"><input id="action_user_delete" <?php if(isset($this->rule->action_user['delete'])) echo 'checked="checked"' ?> type="checkbox" value="1" name="data[rule][action_user][delete]"/> <label for="action_user_delete"><?php echo JText::_('DELETE_USER'); ?></label></div>
						<div style="clear:both;"><input id="action_user_emptyq" <?php if(isset($this->rule->action_user['emptyq'])) echo 'checked="checked"' ?> type="checkbox" value="1" name="data[rule][action_user][emptyq]"/> <label for="action_user_emptyq"><?php echo JText::_('ACY_BOUNCE_EMPTY_QUEUE'); ?></label></div>
					</td>
				</tr>
				<tr>
					<td class="acykey" valign="top">
						<label>
							<?php echo JText::_('EMAIL_ACTION'); ?>
						</label>
					</td>
					<td>
						<div style="clear:both;"><input id="action_message_save" <?php if(isset($this->rule->action_message['save'])) echo 'checked="checked"' ?> type="checkbox" value="1" name="data[rule][action_message][save]"/> <label for="action_message_save"><?php echo JText::_('BOUNCE_SAVE_MESSAGE'); ?></label></div>
						<div style="clear:both;"><input id="action_message_delete" <?php if(isset($this->rule->action_message['delete'])) echo 'checked="checked"' ?> type="checkbox" value="1" name="data[rule][action_message][delete]"/> <label for="action_message_delete"><?php echo JText::_('DELETE_EMAIL'); ?></label></div>
						<div style="clear:both;"><label for="action_message_forward" style="display:inline-block;"><?php echo JText::_('FORWARD_EMAIL'); ?> </label> <input type="text" id="action_message_forward" style="width:200px" name="data[rule][action_message][forwardto]" value="<?php echo @$this->rule->action_message['forwardto']; ?>"/></div>
					</td>
				</tr>
			</table>
		</div>

		<input type="hidden" name="cid[]" value="<?php echo @$this->rule->ruleid; ?>"/>
		<input type="hidden" name="option" value="com_acymailing"/>
		<input type="hidden" name="task" value=""/>
		<input type="hidden" name="ctrl" value="bounces"/>
		<?php echo JHTML::_('form.token'); ?>
	</form>
</div>
