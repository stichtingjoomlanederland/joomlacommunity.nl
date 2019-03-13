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
				<h3 class="rsfp-legend"><?php echo JText::_('COM_RSFORM_DELETION_EMAIL'); ?></h3>
				<div class="alert alert-info"><?php echo JText::_('COM_RSFORM_DELETION_EMAIL_DESC'); ?></div>
				<h3 class="rsfp-legend"><?php echo JText::_('RSFP_EMAILS_LEGEND_SENDER'); ?></h3>
				<table width="100%" class="com-rsform-table-props">
					<tr>
						<td width="25%" align="right" nowrap="nowrap" class="key"><?php echo JText::_('RSFP_EMAILS_FROM'); ?> *</td>
						<td>
							<input name="DeletionEmailFrom" placeholder="<?php echo JText::_('RSFP_EMAILS_FROM_PLACEHOLDER'); ?>" class="rs_inp rs_80" id="DeletionEmailFrom" value="<?php echo $this->escape($this->form->DeletionEmailFrom); ?>" size="35"  data-delimiter=" " data-filter-type="include" data-filter="value,global" data-placeholders="display" />
						</td>
					</tr>
					<tr>
						<td width="25%" align="right" nowrap="nowrap" class="key"><?php echo RSFormProHelper::translateIcon(); ?> <?php echo JText::_('RSFP_EMAILS_FROM_NAME'); ?> *</td>
						<td>
							<input name="DeletionEmailFromName" placeholder="<?php echo JText::_('RSFP_EMAILS_FROM_NAME_PLACEHOLDER'); ?>" class="rs_inp rs_80" id="DeletionEmailFromName" value="<?php echo $this->escape($this->form->DeletionEmailFromName); ?>" size="35"  data-delimiter=" " data-placeholders="display" />
						</td>
					</tr>
				</table>
				<h3 class="rsfp-legend"><?php echo JText::_('RSFP_EMAILS_LEGEND_RECIPIENT'); ?></h3>
				<table width="100%" class="com-rsform-table-props">
					<tr>
						<td width="25%" align="right" nowrap="nowrap" class="key"><?php echo JText::_('RSFP_EMAILS_TO'); ?> *</td>
						<td>
							<input name="DeletionEmailTo" placeholder="<?php echo JText::_('RSFP_EMAILS_TO_PLACEHOLDER'); ?>" class="rs_inp rs_80" id="DeletionEmailTo" value="<?php echo $this->escape($this->form->DeletionEmailTo); ?>"  data-delimiter="," data-filter-type="include" data-filter="value,global" data-placeholders="display" />
						</td>
					</tr>
					<tr>
						<td width="25%" align="right" nowrap="nowrap" class="key"><?php echo JText::_('RSFP_EMAILS_REPLY_TO'); ?></td>
						<td>
							<input name="DeletionEmailReplyTo" placeholder="<?php echo JText::_('RSFP_EMAILS_REPLY_TO_PLACEHOLDER'); ?>" class="rs_inp rs_80" id="DeletionEmailReplyTo" value="<?php echo $this->escape($this->form->DeletionEmailReplyTo); ?>"  data-delimiter="," data-filter-type="include" data-filter="value,global" data-placeholders="display" />
						</td>
					</tr>
					<tr>
						<td width="25%" align="right" nowrap="nowrap" class="key"><?php echo JText::_('RSFP_EMAILS_CC'); ?></td>
						<td><input name="DeletionEmailCC" placeholder="<?php echo JText::_('RSFP_EMAILS_CC_PLACEHOLDER'); ?>" class="rs_inp rs_80" id="DeletionEmailCC" value="<?php echo $this->escape($this->form->DeletionEmailCC); ?>"  data-delimiter="," data-filter-type="include" data-filter="value,global" data-placeholders="display" /></td>
					</tr>
					<tr>
						<td width="25%" align="right" nowrap="nowrap" class="key"><?php echo JText::_('RSFP_EMAILS_BCC'); ?></td>
						<td><input name="DeletionEmailBCC" placeholder="<?php echo JText::_('RSFP_EMAILS_BCC_PLACEHOLDER'); ?>" class="rs_inp rs_80" id="DeletionEmailBCC" value="<?php echo $this->escape($this->form->DeletionEmailBCC); ?>"  data-delimiter="," data-filter-type="include" data-filter="value,global" data-placeholders="display" /></td>
					</tr>
				</table>
				<h3 class="rsfp-legend"><?php echo JText::_('RSFP_EMAILS_LEGEND_CONTENTS'); ?></h3>
				<table width="100%" class="com-rsform-table-props">
					<tr>
						<td width="25%" align="right" nowrap="nowrap" class="key"><?php echo RSFormProHelper::translateIcon(); ?> <?php echo JText::_('RSFP_EMAILS_SUBJECT'); ?> *</td>
						<td><input name="DeletionEmailSubject" placeholder="<?php echo JText::_('RSFP_EMAILS_SUBJECT_PLACEHOLDER'); ?>" class="rs_inp rs_80" id="DeletionEmailSubject" value="<?php echo $this->escape($this->form->DeletionEmailSubject); ?>"  data-delimiter=" " data-placeholders="display" /></td>
					</tr>
					<tr>
						<td width="25%" align="right" nowrap="nowrap" class="key"><?php echo RSFormProHelper::translateIcon(); ?> <?php echo JText::_('RSFP_EMAILS_TEXT'); ?> *</td>
						<td>
							<button class="pull-left btn" id="rsform_edit_user_email" onclick="openRSModal('<?php echo JRoute::_('index.php?option=com_rsform&task=richtext.show&opener=DeletionEmailText&formId='.$this->form->FormId.'&tmpl=component'.(!$this->form->DeletionEmailMode ? '&noEditor=1' : '')); ?>')" type="button"><span class="rsficon rsficon-pencil-square"></span><span class="inner-text"><?php echo JText::_('RSFP_EMAILS_EDIT_TEXT'); ?></span></button>
							<button class="pull-left btn" onclick="openRSModal('<?php echo JRoute::_('index.php?option=com_rsform&task=richtext.preview&opener=DeletionEmailText&formId='.$this->form->FormId.'&tmpl=component'); ?>', 'RichtextPreview')" type="button"><span class="rsficon rsficon-eye"></span><span class="inner-text"><?php echo JText::_('RSFP_PREVIEW'); ?></span></button>
						</td>
					</tr>
					<tr>
						<td width="25%" align="right" nowrap="nowrap" class="key"><?php echo JText::_('RSFP_EMAILS_MODE'); ?></td>
						<td><?php echo $this->lists['DeletionEmailMode']; ?></td>
					</tr>
				</table>
			</fieldset>
			<?php $this->triggerEvent('rsfp_bk_onAfterShowDeletionEmail'); ?>
		</td>
		<td valign="top" width="1%" nowrap="nowrap">
			<button class="btn" type="button" onclick="toggleQuickAdd();"><?php echo JText::_('RSFP_TOGGLE_QUICKADD'); ?></button>
			<div id="QuickAdd5">
				<h3><?php echo JText::_('RSFP_QUICK_ADD');?></h3>
				<?php echo JText::_('RSFP_QUICK_ADD_DESC');?><br/><br/>
				<?php foreach($this->quickfields as $field) {
					echo RSFormProHelper::generateQuickAdd($field, 'display');
				}?>
			</div>
		</td>
	</tr>
</table>