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
				<h3 class="rsfp-legend"><?php echo JText::_('RSFP_ADMIN_EMAILS'); ?></h3>
				<div class="alert alert-info"><?php echo JText::_('RSFP_EMAILS_DESC'); ?></div>
				<h3 class="rsfp-legend"><?php echo JText::_('RSFP_EMAILS_LEGEND_SENDER'); ?></h3>
				<table width="100%" class="com-rsform-table-props">
					<tr>
						<td width="25%" align="right" nowrap="nowrap" class="key"><?php echo JText::_('RSFP_EMAILS_FROM'); ?> *</td>
						<td>
							<input name="AdminEmailFrom" placeholder="<?php echo JText::_('RSFP_EMAILS_FROM_PLACEHOLDER'); ?>" class="rs_inp rs_80" id="AdminEmailFrom" value="<?php echo $this->escape($this->form->AdminEmailFrom); ?>" size="35" data-filter-type="include" data-filter="value,global"  data-delimiter=" "  data-placeholders="display"/>
						</td>
					</tr>
					<tr>
						<td width="25%" align="right" nowrap="nowrap" class="key"><?php echo RSFormProHelper::translateIcon(); ?> <?php echo JText::_('RSFP_EMAILS_FROM_NAME'); ?> *</td>
						<td>
							<input name="AdminEmailFromName" placeholder="<?php echo JText::_('RSFP_EMAILS_FROM_NAME_PLACEHOLDER'); ?>" class="rs_inp rs_80" id="AdminEmailFromName" value="<?php echo $this->escape($this->form->AdminEmailFromName); ?>" size="35"  data-delimiter=" " data-placeholders="display"/>
						</td>
					</tr>
				</table>
				<h3 class="rsfp-legend"><?php echo JText::_('RSFP_EMAILS_LEGEND_RECIPIENT'); ?></h3>
				<table width="100%" class="com-rsform-table-props">
					<tr>
						<td width="25%" align="right" nowrap="nowrap" class="key"><?php echo JText::_('RSFP_EMAILS_TO'); ?> *</td>
						<td><input name="AdminEmailTo" placeholder="<?php echo JText::_('RSFP_EMAILS_TO_PLACEHOLDER'); ?>" class="rs_inp rs_80" id="AdminEmailTo" value="<?php echo $this->escape($this->form->AdminEmailTo); ?>" data-delimiter="," data-filter-type="include" data-filter="value,global" data-placeholders="display" /></td>
					</tr>
					<tr>
						<td width="25%" align="right" nowrap="nowrap" class="key"><?php echo JText::_('RSFP_EMAILS_REPLY_TO'); ?></td>
						<td><input name="AdminEmailReplyTo" placeholder="<?php echo JText::_('RSFP_EMAILS_REPLY_TO_PLACEHOLDER'); ?>" class="rs_inp rs_80" id="AdminEmailReplyTo" value="<?php echo $this->escape($this->form->AdminEmailReplyTo); ?>" data-delimiter="," data-filter-type="include" data-filter="value,global" data-placeholders="display"/></td>
					</tr>
					<tr>
						<td width="25%" align="right" nowrap="nowrap" class="key"><?php echo JText::_('RSFP_EMAILS_CC'); ?></td>
						<td><input name="AdminEmailCC" placeholder="<?php echo JText::_('RSFP_EMAILS_CC_PLACEHOLDER'); ?>" class="rs_inp rs_80" id="AdminEmailCC" value="<?php echo $this->escape($this->form->AdminEmailCC); ?>" data-delimiter="," data-filter-type="include" data-filter="value,global" data-placeholders="display" /></td>
					</tr>
					<tr>
						<td width="25%" align="right" nowrap="nowrap" class="key"><?php echo JText::_('RSFP_EMAILS_BCC'); ?></td>
						<td><input name="AdminEmailBCC" placeholder="<?php echo JText::_('RSFP_EMAILS_BCC_PLACEHOLDER'); ?>" class="rs_inp rs_80" id="AdminEmailBCC" value="<?php echo $this->escape($this->form->AdminEmailBCC); ?>" data-delimiter="," data-filter-type="include" data-filter="value,global" data-placeholders="display" /></td>
					</tr>
					</table>
					<h3 class="rsfp-legend"><?php echo JText::_('RSFP_EMAILS_LEGEND_CONTENTS'); ?></h3>
					<table width="100%" class="com-rsform-table-props">
					<tr>
						<td width="25%" align="right" nowrap="nowrap" class="key"><?php echo RSFormProHelper::translateIcon(); ?> <?php echo JText::_('RSFP_EMAILS_SUBJECT'); ?> *</td>
						<td><input name="AdminEmailSubject" placeholder="<?php echo JText::_('RSFP_EMAILS_SUBJECT_PLACEHOLDER'); ?>" class="rs_inp rs_80" id="AdminEmailSubject" value="<?php echo $this->escape($this->form->AdminEmailSubject); ?>"  data-delimiter=" " data-placeholders="display"/></td>
					</tr>
					<tr>
						<td width="25%" align="right" nowrap="nowrap" class="key"><?php echo RSFormProHelper::translateIcon(); ?> <?php echo JText::_('RSFP_EMAILS_TEXT'); ?> *</td>
						<td>
							<button class="pull-left btn" id="rsform_edit_admin_email" onclick="openRSModal('<?php echo JRoute::_('index.php?option=com_rsform&task=richtext.show&opener=AdminEmailText&formId='.$this->form->FormId.'&tmpl=component'.(!$this->form->AdminEmailMode ? '&noEditor=1' : '')); ?>')" type="button"><span class="rsficon rsficon-pencil-square"></span><span class="inner-text"><?php echo JText::_('RSFP_EMAILS_EDIT_TEXT'); ?></span></button>
							<button class="pull-left btn" onclick="openRSModal('<?php echo JRoute::_('index.php?option=com_rsform&task=richtext.preview&opener=AdminEmailText&formId='.$this->form->FormId.'&tmpl=component'); ?>', 'RichtextPreview')" type="button"><span class="rsficon rsficon-eye"></span><span class="inner-text"><?php echo JText::_('RSFP_PREVIEW'); ?></span></button>
						</td>
					</tr>
					<tr>
						<td width="25%" align="right" nowrap="nowrap" class="key"><?php echo JText::_('RSFP_EMAILS_MODE'); ?></td>
						<td><?php echo $this->lists['AdminEmailMode'];?></td>
					</tr>
				</table>
			</fieldset>
			<?php $this->triggerEvent('rsfp_bk_onAfterShowAdminEmail'); ?>
		</td>
		<td valign="top" width="1%" nowrap="nowrap">
			<button class="btn" type="button" onclick="toggleQuickAdd();"><?php echo JText::_('RSFP_TOGGLE_QUICKADD'); ?></button>
			<div id="QuickAdd4">
				<h3><?php echo JText::_('RSFP_QUICK_ADD');?></h3>
				<?php echo JText::_('RSFP_QUICK_ADD_DESC');?><br/><br/>
				<?php foreach($this->quickfields as $field) {
					echo RSFormProHelper::generateQuickAdd($field, 'display');
				}?>
			</div>
		</td>
	</tr>
</table>