<?php
/**
 * @package    RSForm! Pro
 * @copyright  (c) 2007 - 2016 RSJoomla!
 * @link       https://www.rsjoomla.com
 * @license    GNU General Public License http://www.gnu.org/licenses/gpl-3.0.en.html
 */

defined('_JEXEC') or die('Restricted access');
?>
<table class="admintable">
	<tr>
		<td valign="top" align="left">
			<fieldset>
				<h3 class="rsfp-legend"><?php echo JText::_('RSFP_USER_EMAILS'); ?></h3>
				<div class="alert alert-info"><?php echo JText::_('RSFP_EMAILS_DESC'); ?></div>
				<h3 class="rsfp-legend"><?php echo JText::_('RSFP_EMAILS_LEGEND_SENDER'); ?></h3>
				<table width="100%" class="com-rsform-table-props">
					<tr>
						<td width="25%" align="right" nowrap="nowrap" class="key"><?php echo JText::_('RSFP_EMAILS_FROM'); ?> *</td>
						<td>
							<input name="UserEmailFrom" placeholder="<?php echo JText::_('RSFP_EMAILS_FROM_PLACEHOLDER'); ?>" class="rs_inp rs_80" id="UserEmailFrom" value="<?php echo $this->escape($this->form->UserEmailFrom); ?>" size="35"  data-delimiter=" " data-filter-type="include" data-filter="value,global" data-placeholders="display" />
						</td>
					</tr>
					<tr>
						<td width="25%" align="right" nowrap="nowrap" class="key"><?php echo RSFormProHelper::translateIcon(); ?> <?php echo JText::_('RSFP_EMAILS_FROM_NAME'); ?> *</td>
						<td>
							<input name="UserEmailFromName" placeholder="<?php echo JText::_('RSFP_EMAILS_FROM_NAME_PLACEHOLDER'); ?>" class="rs_inp rs_80" id="UserEmailFromName" value="<?php echo $this->escape($this->form->UserEmailFromName); ?>" size="35"  data-delimiter=" " data-placeholders="display" />
						</td>
					</tr>
				</table>
				<h3 class="rsfp-legend"><?php echo JText::_('RSFP_EMAILS_LEGEND_RECIPIENT'); ?></h3>
				<table width="100%" class="com-rsform-table-props">
					<tr>
						<td width="25%" align="right" nowrap="nowrap" class="key"><?php echo JText::_('RSFP_EMAILS_TO'); ?> *</td>
						<td>
							<input name="UserEmailTo" placeholder="<?php echo JText::_('RSFP_EMAILS_TO_PLACEHOLDER'); ?>" class="rs_inp rs_80" id="UserEmailTo" value="<?php echo $this->escape($this->form->UserEmailTo); ?>"  data-delimiter="," data-filter-type="include" data-filter="value,global" data-placeholders="display" />
						</td>
					</tr>
					<tr>
						<td width="25%" align="right" nowrap="nowrap" class="key"><?php echo JText::_('RSFP_EMAILS_REPLY_TO'); ?></td>
						<td>
							<input name="UserEmailReplyTo" placeholder="<?php echo JText::_('RSFP_EMAILS_REPLY_TO_PLACEHOLDER'); ?>" class="rs_inp rs_80" id="UserEmailReplyTo" value="<?php echo $this->escape($this->form->UserEmailReplyTo); ?>"  data-delimiter="," data-filter-type="include" data-filter="value,global" data-placeholders="display" />
						</td>
					</tr>
					<tr>
						<td width="25%" align="right" nowrap="nowrap" class="key"><?php echo JText::_('RSFP_EMAILS_CC'); ?></td>
						<td><input name="UserEmailCC" placeholder="<?php echo JText::_('RSFP_EMAILS_CC_PLACEHOLDER'); ?>" class="rs_inp rs_80" id="UserEmailCC" value="<?php echo $this->escape($this->form->UserEmailCC); ?>"  data-delimiter="," data-filter-type="include" data-filter="value,global" data-placeholders="display" /></td>
					</tr>
					<tr>
						<td width="25%" align="right" nowrap="nowrap" class="key"><?php echo JText::_('RSFP_EMAILS_BCC'); ?></td>
						<td><input name="UserEmailBCC" placeholder="<?php echo JText::_('RSFP_EMAILS_BCC_PLACEHOLDER'); ?>" class="rs_inp rs_80" id="UserEmailBCC" value="<?php echo $this->escape($this->form->UserEmailBCC); ?>"  data-delimiter="," data-filter-type="include" data-filter="value,global" data-placeholders="display" /></td>
					</tr>
				</table>
				<h3 class="rsfp-legend"><?php echo JText::_('RSFP_EMAILS_LEGEND_CONTENTS'); ?></h3>
				<table width="100%" class="com-rsform-table-props">
					<tr>
						<td width="25%" align="right" nowrap="nowrap" class="key"><?php echo RSFormProHelper::translateIcon(); ?> <?php echo JText::_('RSFP_EMAILS_SUBJECT'); ?> *</td>
						<td><input name="UserEmailSubject" placeholder="<?php echo JText::_('RSFP_EMAILS_SUBJECT_PLACEHOLDER'); ?>" class="rs_inp rs_80" id="UserEmailSubject" value="<?php echo $this->escape($this->form->UserEmailSubject); ?>"  data-delimiter=" " data-placeholders="display" /></td>
					</tr>
					<tr>
						<td width="25%" align="right" nowrap="nowrap" class="key"><?php echo RSFormProHelper::translateIcon(); ?> <?php echo JText::_('RSFP_EMAILS_TEXT'); ?> *</td>
						<td>
							<button class="pull-left btn" id="rsform_edit_user_email" onclick="openRSModal('<?php echo JRoute::_('index.php?option=com_rsform&task=richtext.show&opener=UserEmailText&formId='.$this->form->FormId.'&tmpl=component'.(!$this->form->UserEmailMode ? '&noEditor=1' : '')); ?>')" type="button"><span class="rsficon rsficon-pencil-square"></span><span class="inner-text"><?php echo JText::_('RSFP_EMAILS_EDIT_TEXT'); ?></span></button>
							<button class="pull-left btn" onclick="openRSModal('<?php echo JRoute::_('index.php?option=com_rsform&task=richtext.preview&opener=UserEmailText&formId='.$this->form->FormId.'&tmpl=component'); ?>', 'RichtextPreview')" type="button"><span class="rsficon rsficon-eye"></span><span class="inner-text"><?php echo JText::_('RSFP_PREVIEW'); ?></span></button>
						</td>
					</tr>
					<tr>
						<td width="25%" align="right" nowrap="nowrap" class="key"><?php echo JText::_('RSFP_EMAILS_MODE'); ?></td>
						<td><?php echo $this->lists['UserEmailMode']; ?></td>
					</tr>
				</table>
				<h3 class="rsfp-legend"><?php echo JText::_('RSFP_EMAILS_LEGEND_ATTACHMENTS'); ?></h3>
				<table width="100%" class="com-rsform-table-props">
					<tr>
						<td width="25%" align="right" nowrap="nowrap" class="key"><?php echo JText::_('RSFP_EMAILS_ATTACH_FILE'); ?></td>
						<td><?php echo $this->lists['UserEmailAttach'];?></td>
					</tr>
					<tr>
						<td width="25%" align="right" nowrap="nowrap" class="key"><?php echo JText::_('RSFP_EMAILS_ATTACH_FILE_LOCATION'); ?></td>
						<td>
							<input name="UserEmailAttachFile" class="rs_inp rs_75 pull-left" id="UserEmailAttachFile" value="<?php echo !empty($this->form->UserEmailAttachFile) ? $this->form->UserEmailAttachFile : JPATH_SITE.'/components/com_rsform/uploads'; ?>" <?php if (!$this->form->UserEmailAttach) { ?>disabled="disabled"<?php } ?> />
							<a href="index.php?option=com_rsform&amp;controller=files&amp;task=display&amp;folder=<?php echo is_file($this->form->UserEmailAttachFile) ? $this->escape(dirname($this->form->UserEmailAttachFile)) : ''; ?>&amp;tmpl=component" onclick="openRSModal(this.href); return false;" class="pull-left btn" id="rsform_select_file" <?php if (!$this->form->UserEmailAttach) { ?>style="display: none"<?php } ?>><span class="rsficon rsficon-file-text-o"></span> <?php echo JText::_('RSFP_SELECT_FILE'); ?></a>
							<?php if ($this->form->UserEmailAttach && (!file_exists($this->form->UserEmailAttachFile) || !is_file($this->form->UserEmailAttachFile))) { ?>
								<div class="alert alert-danger">
									<?php echo JText::_('RSFP_EMAILS_ATTACH_FILE_WARNING'); ?>
								</div>
							<?php } ?>
						</td>
					</tr>
				</table>
			</fieldset>
			<?php $this->triggerEvent('rsfp_bk_onAfterShowUserEmail'); ?>
		</td>
		<td valign="top" width="1%" nowrap="nowrap">
			<button class="btn" type="button" onclick="toggleQuickAdd();"><?php echo JText::_('RSFP_TOGGLE_QUICKADD'); ?></button>
			<div id="QuickAdd3">
				<h3><?php echo JText::_('RSFP_QUICK_ADD');?></h3>
				<?php echo JText::_('RSFP_QUICK_ADD_DESC');?><br/><br/>
				<?php foreach($this->quickfields as $field) {
					echo RSFormProHelper::generateQuickAdd($field, 'display');
				}?>
			</div>
		</td>
	</tr>
</table>