<?php
/**
 * @package    RSForm! Pro
 * @copyright  (c) 2007 - 2016 RSJoomla!
 * @link       https://www.rsjoomla.com
 * @license    GNU General Public License http://www.gnu.org/licenses/gpl-3.0.en.html
 */

defined('_JEXEC') or die('Restricted access');
?>
<form method="post" action="index.php?option=com_rsform" name="adminForm" id="adminForm" class="com-rsform-padding">
<p>
	<button class="btn btn-success pull-left" type="button" onclick="Joomla.submitbutton('emails.apply');"><?php echo JText::_('JAPPLY'); ?></button>
	<button class="btn btn-success pull-left" type="button" onclick="Joomla.submitbutton('emails.save');"><?php echo JText::_('JSAVE'); ?></button>
	<button class="btn pull-left" type="button" onclick="window.close();"><?php echo JText::_('JCANCEL'); ?></button>
</p>
<span class="rsform_clear_both"></span>
<fieldset class="form-horizontal">
<h3 class="rsfp-legend"><?php echo JText::_('RSFP_FORM_EMAILS_NEW'); ?></h3>
    <?php if (!RSFormProHelper::getConfig('global.disable_multilanguage')) { ?>
        <?php if ($this->row->id) { ?>
            <span><?php echo $this->lists['Languages']; ?></span><span><?php echo JText::sprintf('RSFP_YOU_ARE_EDITING_IN', $this->lang, RSFormProHelper::translateIcon()); ?></span>
        <?php } else { ?>
            <?php echo JText::sprintf('RSFP_YOU_ARE_EDITING_IN', $this->lang, RSFormProHelper::translateIcon()); ?>
        <?php } ?>
    <?php } ?>
<h3 class="rsfp-legend"><?php echo JText::_('RSFP_EMAILS_LEGEND_SENDER'); ?></h3>
<table width="100%" class="com-rsform-table-props">
	<tr>
		<td width="25%" align="right" nowrap="nowrap" class="key"><?php echo JText::_('RSFP_EMAILS_FROM'); ?> *</td>
		<td>
			<input data-delimiter=" " data-placeholders="display" name="from" placeholder="<?php echo JText::_('RSFP_EMAILS_FROM_PLACEHOLDER'); ?>" data-filter-type="include" data-filter="value,global" class="rs_inp rs_80" id="from" value="<?php echo $this->escape($this->row->from); ?>" size="35" />
		</td>
	</tr>
	<tr>
		<td width="25%" align="right" nowrap="nowrap" class="key"><?php echo RSFormProHelper::translateIcon(); ?> <?php echo JText::_('RSFP_EMAILS_FROM_NAME'); ?> *</td>
		<td>
			<input data-delimiter=" " data-placeholders="display" name="fromname" placeholder="<?php echo JText::_('RSFP_EMAILS_FROM_NAME_PLACEHOLDER'); ?>" class="rs_inp rs_80" id="fromname" value="<?php echo $this->escape($this->row->fromname); ?>" size="35" />
		</td>
	</tr>
</table>
<h3 class="rsfp-legend"><?php echo JText::_('RSFP_EMAILS_LEGEND_RECIPIENT'); ?></h3>
<table width="100%" class="com-rsform-table-props">
	<tr>
		<td width="25%" align="right" nowrap="nowrap" class="key"><?php echo JText::_('RSFP_EMAILS_TO'); ?> *</td>
		<td><input data-delimiter="," data-placeholders="display" data-filter-type="include" data-filter="value,global" name="to" placeholder="<?php echo JText::_('RSFP_EMAILS_TO_PLACEHOLDER'); ?>" class="rs_inp rs_80" id="to" value="<?php echo $this->escape($this->row->to); ?>" /></td>
	</tr>
	<tr>
		<td width="25%" align="right" nowrap="nowrap" class="key"><?php echo JText::_('RSFP_EMAILS_REPLY_TO'); ?>:</td>
		<td><input data-delimiter="," data-placeholders="display" data-filter-type="include" data-filter="value,global" name="replyto" placeholder="<?php echo JText::_('RSFP_EMAILS_REPLY_TO_PLACEHOLDER'); ?>" class="rs_inp rs_80" id="replyto" value="<?php echo $this->escape($this->row->replyto); ?>" /></td>
	</tr>
	<tr>
		<td width="25%" align="right" nowrap="nowrap" class="key"><?php echo JText::_('RSFP_EMAILS_CC'); ?></td>
		<td><input data-delimiter="," data-placeholders="display" data-filter-type="include" data-filter="value,global" name="cc" placeholder="<?php echo JText::_('RSFP_EMAILS_CC_PLACEHOLDER'); ?>" class="rs_inp rs_80" id="cc" value="<?php echo $this->escape($this->row->cc); ?>" /></td>
	</tr>
	<tr>
		<td width="25%" align="right" nowrap="nowrap" class="key"><?php echo JText::_('RSFP_EMAILS_BCC'); ?></td>
		<td><input data-delimiter="," data-placeholders="display" data-filter-type="include" data-filter="value,global" name="bcc" placeholder="<?php echo JText::_('RSFP_EMAILS_BCC_PLACEHOLDER'); ?>" class="rs_inp rs_80" id="bcc" value="<?php echo $this->escape($this->row->bcc); ?>" /></td>
	</tr>
</table>
<h3 class="rsfp-legend"><?php echo JText::_('RSFP_EMAILS_LEGEND_CONTENTS'); ?></h3>
<table width="100%" class="com-rsform-table-props">
	<tr>
		<td width="25%" align="right" nowrap="nowrap" class="key"><?php echo RSFormProHelper::translateIcon(); ?> <?php echo JText::_('RSFP_EMAILS_SUBJECT'); ?> *</td>
		<td><input data-delimiter=" " data-placeholders="display" name="subject" placeholder="<?php echo JText::_('RSFP_EMAILS_SUBJECT_PLACEHOLDER'); ?>" class="rs_inp rs_80" id="subject" value="<?php echo $this->escape($this->row->subject); ?>" /></td>
	</tr>
	<tr>
		<td width="25%" align="right" nowrap="nowrap" class="key"><?php echo RSFormProHelper::translateIcon(); ?> <?php echo JText::_('RSFP_EMAILS_TEXT'); ?> *</td>
		<td>
			<?php if ($this->row->mode) { ?>
				<?php echo $this->editor->display('message', $this->escape($this->row->message), 500, 320, 70, 10); ?>
			<?php } else { ?>
				<textarea id="message" name="message" style="width: 500px; height: 320px;" rows="10" cols="70"><?php echo $this->escape($this->row->message); ?></textarea>
			<?php } ?>
		</td>
	</tr>
	<tr>
		<td width="25%" align="right" nowrap="nowrap" class="key"><?php echo JText::_('RSFP_EMAILS_MODE'); ?></td>
		<td class="com-rsform-css-fix"><?php echo $this->lists['mode'];?></td>
	</tr>
</table>
</fieldset>

	<input type="hidden" name="option" value="com_rsform" />
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="id" value="<?php echo $this->row->id; ?>" />
	<input type="hidden" name="formId" value="<?php echo $this->row->formId; ?>" />
	<input type="hidden" name="type" value="<?php echo JFactory::getApplication()->input->getCmd('type','additional'); ?>" />
</form>

<script type="text/javascript">
<?php $update = JFactory::getApplication()->input->getInt('update',0); ?>
<?php if ($update) echo 'window.opener.updateemails('.JFactory::getApplication()->input->getInt('formId',0).',\''.JFactory::getApplication()->input->getCmd('type','additional').'\')'; ?>
</script>

<style type="text/css">
body {
	padding: 20px !important;
}
</style>

<?php JHtml::_('behavior.keepalive'); ?>