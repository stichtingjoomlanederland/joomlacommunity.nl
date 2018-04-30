<?php
/**
 * @package RSForm! Pro
 * @copyright (C) 2007-2014 www.rsjoomla.com
 * @license GPL, http://www.gnu.org/copyleft/gpl.html
 */

defined('_JEXEC') or die('Restricted access');
?>
<table class="admintable">
	<tr>
		<td valign="top" align="left">
			<fieldset>
				<h3 class="rsfp-legend"><?php echo JText::_('RSFP_FORM_INFO_BASIC'); ?></h3>
				<table width="100%" class="com-rsform-table-props">
					<tr>
						<td width="25%" align="right" nowrap="nowrap" class="key"><?php echo JText::_('RSFP_PUBLISHED'); ?></td>
						<td><?php echo $this->lists['Published']; ?></td>
					</tr>
					<tr>
						<td width="25%" align="right" nowrap="nowrap" class="key"><?php echo JText::_('JFIELD_ACCESS_LABEL'); ?></td>
						<td><?php echo JHtml::_('access.level','Access',$this->form->Access,'',array(JHtml::_('select.option', '', JText::_('RSFP_EVERYBODY')))); ?></td>
					</tr>
					<tr>
						<td width="25%" align="right" nowrap="nowrap" class="key"><?php echo RSFormProHelper::translateIcon(); ?> <?php echo JText::_('RSFP_FORM_TITLE'); ?></td>
						<td><input name="FormTitle" class="rs_inp rs_80" value="<?php echo $this->escape($this->form->FormTitle); ?>" size="105" id="FormTitle" type="text" /></td>
					</tr>
					<tr>
						<td width="25%" align="right" nowrap="nowrap" class="key"><?php echo JText::_('RSFP_SHOW_FORM_TITLE'); ?></td>
						<td><?php echo $this->lists['ShowFormTitle']; ?></td>
					</tr>
					<tr>
						<td width="25%" align="right" nowrap="nowrap" class="key"><?php echo JText::_('RSFP_FORM_NAME'); ?></td>
						<td><input name="FormName" class="rs_inp rs_80" value="<?php echo $this->escape($this->form->FormName); ?>" size="105" id="FormName" type="text" /></td>
					</tr>
				</table>
			</fieldset>

			<fieldset>
				<h3 class="rsfp-legend"><?php echo JText::_('RSFP_FORM_INFO_VALIDATION'); ?></h3>
				<table width="100%" class="com-rsform-table-props">
					<tr>
						<td width="25%" align="right" nowrap="nowrap" class="key"><?php echo JText::_('RSFP_AJAX_VALIDATION'); ?></td>
						<td><?php echo $this->lists['AjaxValidation']; ?></td>
					</tr>
					<tr>
						<td>&nbsp;</td>
						<td><?php echo JText::_('RSFP_AJAX_VALIDATION_DESC'); ?></td>
					</tr>
					<tr>
						<td width="25%" align="right" nowrap="nowrap" class="key"><?php echo JText::_('RSFP_SCROLL_TO_ERROR'); ?></td>
						<td><?php echo $this->lists['ScrollToError']; ?></td>
					</tr>
					<tr>
						<td width="25%" align="right" nowrap="nowrap" class="key"><?php echo JText::_('RSFP_REQUIRED'); ?></td>
						<td><input name="Required" class="rs_inp rs_10" value="<?php echo $this->escape($this->form->Required); ?>" size="105" id="Required" type="text" /></td>
					</tr>
					<tr>
						<td width="25%" align="right" nowrap="nowrap" class="key"><?php echo RSFormProHelper::translateIcon(); ?>  <?php echo JText::_('RSFP_ERROR_MESSAGE'); ?></td>
						<td>
							<button class="btn pull-left" onclick="openRSModal('<?php echo JRoute::_('index.php?option=com_rsform&task=richtext.show&opener=ErrorMessage&formId='.$this->form->FormId.'&tmpl=component'); ?>')" type="button"><span class="rsficon rsficon-pencil-square"></span><span class="inner-text"><?php echo JText::_('RSFP_EDIT_ERROR_MESSAGE'); ?></span></button>
							<button class="btn pull-left" onclick="openRSModal('<?php echo JRoute::_('index.php?option=com_rsform&task=richtext.preview&opener=ErrorMessage&formId='.$this->form->FormId.'&tmpl=component'); ?>', 'RichtextPreview')" type="button"><span class="rsficon rsficon-eye"></span><span class="inner-text"><?php echo JText::_('RSFP_PREVIEW'); ?></span></button>
						</td>
					</tr>
					<tr>
						<td>&nbsp;</td>
						<td><?php echo JText::_('RSFP_ERROR_MESSAGE_DESC'); ?></td>
					</tr>
					<tr>
						<td width="25%" align="right" nowrap="nowrap" class="key"><?php echo JText::_('RSFP_REMOVE_CAPTCHA_FIELDS_FOR_LOGGED_USERS'); ?></td>
						<td><?php echo $this->lists['RemoveCaptchaLogged']; ?></td>
					</tr>
				</table>
			</fieldset>

			<fieldset>
				<h3 class="rsfp-legend"><?php echo JText::_('RSFP_FORM_INFO_THANK_YOU_MESSAGE'); ?></h3>
				<table width="100%" class="com-rsform-table-props">
					<tr>
						<td width="25%" align="right" nowrap="nowrap" class="key"><?php echo JText::_('RSFP_SHOW_THANKYOU_MESSAGE'); ?></td>
						<td><?php echo $this->lists['ShowThankyou']; ?></td>
					</tr>
					<tr>
						<td width="25%" align="right" nowrap="nowrap" class="key"><?php echo JText::_('RSFP_SCROLL_TO_THANKYOU_MESSAGE'); ?></td>
						<td><?php echo $this->lists['ScrollToThankYou']; ?></td>
					</tr>
					<tr id="thankyouMessagePopupContainer"<?php echo ((!$this->form->ShowThankyou || ($this->form->ShowThankyou && $this->form->ScrollToThankYou)) ? ' style="display:none"' : '') ;?>>
						<td width="25%" align="right" nowrap="nowrap" class="key"><?php echo JText::_('RSFP_THANKYOU_MESSAGE_POPUP'); ?></td>
						<td><?php echo $this->lists['ThankYouMessagePopUp']; ?></td>
					</tr>
					<tr>
						<td width="25%" align="right" nowrap="nowrap" class="key"><?php echo RSFormProHelper::translateIcon(); ?> <?php echo JText::_('RSFP_THANKYOU'); ?></td>
						<td>
							<button class="btn pull-left" onclick="openRSModal('<?php echo JRoute::_('index.php?option=com_rsform&task=richtext.show&opener=Thankyou&formId='.$this->form->FormId.'&tmpl=component'); ?>')" type="button"><span class="rsficon rsficon-pencil-square"></span><span class="inner-text"><?php echo JText::_('RSFP_EDIT_THANKYOU'); ?></span></button>
							<button class="btn pull-left" onclick="openRSModal('<?php echo JRoute::_('index.php?option=com_rsform&task=richtext.preview&opener=Thankyou&formId='.$this->form->FormId.'&tmpl=component'); ?>', 'RichtextPreview')" type="button"><span class="rsficon rsficon-eye"></span><span class="inner-text"><?php echo JText::_('RSFP_PREVIEW'); ?></span></button>
						</td>
					</tr>
					<tr id="showContinueContainer"<?php echo (!$this->form->ShowThankyou ? ' style="display:none"' : '') ;?>>
						<td width="25%" align="right" nowrap="nowrap" class="key"><?php echo JText::_('RSFP_SHOW_CONTINUE'); ?></td>
						<td><?php echo $this->lists['ShowContinue']; ?></td>
					</tr>
				</table>
			</fieldset>

			<fieldset>
				<h3 class="rsfp-legend"><?php echo JText::_('RSFP_FORM_INFO_SUBMISSION'); ?></h3>
				<table width="100%" class="com-rsform-table-props">
					<tr id="systemMessageContainer"<?php echo $this->form->ShowThankyou ? ' style="display:none"' : ''; ?>>
						<td width="25%" align="right" nowrap="nowrap" class="key"><?php echo JText::_('RSFP_SHOW_SYSTEM_MESSAGE'); ?></td>
						<td><?php echo $this->lists['ShowSystemMessage']; ?></td>
					</tr>
					<tr>
						<td width="25%" align="right" nowrap="nowrap" class="key"><?php echo JText::_('RSFP_RETURN_URL'); ?></td>
						<td><input name="ReturnUrl" class="rs_inp rs_80" value="<?php echo $this->escape($this->form->ReturnUrl); ?>" size="105" id="ReturnUrl" type="text" /></td>
					</tr>
					<tr>
						<td>&nbsp;</td>
						<td><?php echo JText::_('RSFP_RETURN_URL_DESC'); ?></td>
					</tr>
					<tr>
						<td width="25%" align="right" nowrap="nowrap" class="key"><?php echo JText::_('RSFP_SAVE_TO_DATABASE'); ?></td>
						<td><?php echo $this->lists['keepdata']; ?></td>
					</tr>
					<tr>
						<td width="25%" align="right" nowrap="nowrap" class="key"><?php echo JText::_('RSFP_SAVE_IP_TO_DATABASE'); ?></td>
						<td><?php echo $this->lists['KeepIP']; ?></td>
					</tr>
                    <tr>
                        <td width="25%" align="right" nowrap="nowrap" class="key"><?php echo JText::_('RSFP_DELETE_SUBMISSIONS_AFTER'); ?></td>
                        <td><input name="DeleteSubmissionsAfter" class="rs_inp rs_10" value="<?php echo (int) $this->form->DeleteSubmissionsAfter; ?>" size="105" id="DeleteSubmissionsAfter" type="text" /> <?php echo JText::_('RSFP_DELETE_SUBMISSIONS_AFTER_DAYS'); ?></td>
                    </tr>
                    <tr>
                        <td>&nbsp;</td>
                        <td><?php echo JText::_('RSFP_DELETE_SUBMISSIONS_AFTER_INSTRUCTIONS'); ?></td>
                    </tr>
					<tr>
						<td width="25%" align="right" nowrap="nowrap" class="key"><?php echo JText::_('RSFP_CONFIRM_SUBMISSION'); ?></td>
						<td><?php echo $this->lists['confirmsubmission']; ?></td>
					</tr>
					<tr>
						<td width="25%" align="right" nowrap="nowrap" class="key"><?php echo JText::_('RSFP_DISABLE_SUBMIT_BUTTON'); ?></td>
						<td><?php echo $this->lists['DisableSubmitButton']; ?></td>
					</tr>
				</table>
			</fieldset>
			<fieldset>
				<h3 class="rsfp-legend"><?php echo JText::_('RSFP_FORM_INFO_MISC'); ?></h3>
				<table width="100%" class="com-rsform-table-props">
					<tr>
						<td width="25%" align="right" nowrap="nowrap" class="key"><?php echo JText::_('RSFP_MULTIPLE_SEPARATOR'); ?></td>
						<td><input name="MultipleSeparator" class="rs_inp rs_10" value="<?php echo $this->escape($this->form->MultipleSeparator); ?>" size="105" id="MultipleSeparator" type="text" /></td>
					</tr>
					<tr>
						<td>&nbsp;</td>
						<td><?php echo JText::_('RSFP_MULTIPLE_SEPARATOR_DESC'); ?></td>
					</tr>
					<tr>
						<td width="25%" align="right" nowrap="nowrap" class="key"><?php echo JText::_('RSFP_TEXTAREA_NEW_LINES'); ?></td>
						<td><?php echo $this->lists['TextareaNewLines']; ?></td>
					</tr>
					<tr>
						<td>&nbsp;</td>
						<td><?php echo JText::_('RSFP_TEXTAREA_NEW_LINES_DESC'); ?></td>
					</tr>
				</table>
			</fieldset>
		</td>
		<td valign="top" width="1%" nowrap="nowrap">
			<button type="button" class="btn" onclick="toggleQuickAdd();"><?php echo JText::_('RSFP_TOGGLE_QUICKADD'); ?></button>
			<div id="QuickAdd2">
				<h3><?php echo JText::_('RSFP_QUICK_ADD');?></h3>
				<?php echo JText::_('RSFP_QUICK_ADD_DESC');?><br/><br/>
				<?php foreach($this->quickfields as $field) {
					echo RSFormProHelper::generateQuickAdd($field, 'display');
				}?>
			</div>
		</td>
	</tr>

</table>