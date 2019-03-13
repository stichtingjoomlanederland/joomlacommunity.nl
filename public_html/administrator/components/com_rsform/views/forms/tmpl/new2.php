<?php
/**
* @package RSForm! Pro
* @copyright (C) 2007-2014 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/

defined('_JEXEC') or die('Restricted access');

JText::script('ERROR');
JText::script('RSFP_WHATS_FORM_TITLE_VALIDATION');
JText::script('RSFP_SUBMISSION_REDIRECT_WHERE_VALIDATION');
?>
<script type="text/javascript">
	function changeAdminEmail(value)
	{
		if (value == 1)
			document.adminForm.AdminEmailTo.disabled = false;
		else
			document.adminForm.AdminEmailTo.disabled = true;
	}
	
	function showPopupThankyou(value)
	{
		if (value == 1) {
			document.getElementById('popupThankYou').style.display = 'none';
		}
		else {
			document.getElementById('popupThankYou').style.display = 'table-row';
		}
	}
	
	function changeSubmissionAction(value)
	{
		document.getElementById('RedirectTo1').style.display = 'none';
		document.getElementById('RedirectTo2').style.display = 'none';
		document.getElementById('ThankYou1').style.display = 'none';
		document.getElementById('ThankYou2').style.display = 'none';
		
		if (value == 'redirect')
		{
			document.getElementById('RedirectTo1').style.display = '';
			document.getElementById('RedirectTo2').style.display = '';
		}
		else if (value == 'thankyou')
		{
			document.getElementById('ThankYou1').style.display = '';
			document.getElementById('ThankYou2').style.display = '';
		}
	}

    Joomla.submitbutton = function(task)
	{
		if (task == 'forms.cancel')
		{
			Joomla.submitform(task);
		}
		else
		{
			var form = document.adminForm;
			
			jQuery(form.FormTitle).removeClass('rs_error_field');
			jQuery(form.ReturnUrl).removeClass('rs_error_field');

            var messages = {"error": []};
			if (form.FormTitle.value.length == 0)
			{
                messages.error.push(Joomla.JText._('RSFP_WHATS_FORM_TITLE_VALIDATION'));
				jQuery(form.FormTitle).addClass('rs_error_field');
			}
			if (form.SubmissionAction.value == 'redirect' && form.ReturnUrl.value.length == 0)
			{
                messages.error.push(Joomla.JText._('RSFP_SUBMISSION_REDIRECT_WHERE_VALIDATION'));
				jQuery(form.ReturnUrl).addClass('rs_error_field');
			}

			if (messages.error.length > 0)
            {
                Joomla.renderMessages(messages);
                return;
            }
			
			Joomla.submitform(task);
		}
	}
</script>

<form method="post" action="index.php?option=com_rsform&amp;task=forms.new.stepthree" name="adminForm" id="adminForm">
	<fieldset>
		<h3><?php echo JText::_('RSFP_NEW_FORM_STEP_2_1'); ?></h3>
		<table class="admintable com-rsform-table-props">
			<tr>
				<td width="350" style="width: 350px;" align="right" class="key"><?php echo JText::_('RSFP_WHATS_FORM_TITLE'); ?></td>
				<td><input class="rs_inp" type="text" class="inputbox" size="55" name="FormTitle" value="" /></td>
			</tr>
			<tr>
				<td colspan="2"><?php echo JText::_('RSFP_WHATS_FORM_TITLE_DESC'); ?></td>
			</tr>
			<tr>
				<td width="350" style="width: 350px;" align="right" class="key"><?php echo JText::_('RSFP_WHATS_FORM_LAYOUT'); ?></td>
				<td class="com-rsform-css-fix">
                    <?php if ($this->layouts['html5Layouts']) { ?>
                        <?php foreach ($this->layouts['html5Layouts'] as $i => $layout) { ?>
                            <div class="rsform_layout_box">
                                <label for="formLayout<?php echo ucfirst($layout); ?>" class="radio">
                                    <input type="radio" id="formLayout<?php echo ucfirst($layout); ?>" name="FormLayout" value="<?php echo $layout; ?>" <?php if ($i == 0) { ?> checked<?php } ?>/> <?php echo JText::_('RSFP_LAYOUT_'.str_replace('-', '_', $layout)); ?>
                                </label>
                                <?php echo JHtml::image('com_rsform/admin/layouts/' . $layout . '.gif', JText::_('RSFP_LAYOUT_'.str_replace('-', '_', $layout)), 'width="175"', true); ?>
                            </div>
                        <?php } ?>
                    <?php } ?>
				</td>
			</tr>
			<tr>
				<td colspan="2"><?php echo JText::_('RSFP_WHATS_FORM_LAYOUT_DESC'); ?></td>
			</tr>
			<tr>
				<td width="350" style="width: 350px;" align="right" class="key"><?php echo JText::_('RSFP_WANT_SCROLL_TO_THANK_YOU_MESSAGE'); ?></td>
				<td class="com-rsform-css-fix"><?php echo $this->lists['ScrollToThankYou']; ?></td>
			</tr>
			<tr style="display:none" id="popupThankYou">
				<td width="350" style="width: 350px;" align="right" class="key"><?php echo JText::_('RSFP_WANT_POPUP_THANK_YOU_MESSAGE'); ?></td>
				<td class="com-rsform-css-fix"><?php echo $this->lists['ThankYouMessagePopUp']; ?></td>
			</tr>
		</table>
		
		<h3><?php echo JText::_('RSFP_NEW_FORM_STEP_2_2'); ?></h3>
		<table class="admintable com-rsform-table-props">
			<tr>
				<td width="350" style="width: 350px;" align="right" class="key"><?php echo JText::_('RSFP_WANT_ADMIN_EMAIL_RESULTS'); ?></td>
				<td class="com-rsform-css-fix"><?php echo $this->lists['AdminEmail']; ?></td>
			</tr>
			<tr>
				<td width="350" style="width: 350px;" align="right" class="key"><?php echo JText::_('RSFP_WHERE_EMAIL_RESULTS'); ?></td>
				<td><input class="rs_inp" type="text" class="inputbox" size="55" name="AdminEmailTo" value="<?php echo $this->adminEmail; ?>" /></td>
			</tr>
			<tr>
				<td colspan="2"><?php echo JText::_('RSFP_WHERE_EMAIL_RESULTS_DESC'); ?></td>
			</tr>
			<tr>
				<td width="350" style="width: 350px;" align="right" class="key"><?php echo JText::_('RSFP_WANT_SUBMITTER_EMAIL_RESULTS'); ?></td>
				<td class="com-rsform-css-fix"><?php echo $this->lists['UserEmail']; ?></td>
			</tr>
			<tr>
				<td colspan="2"><?php echo JText::_('RSFP_WANT_SUBMITTER_EMAIL_RESULTS_DESC'); ?></td>
			</tr>
		</table>
		
		<h3><?php echo JText::_('RSFP_NEW_FORM_STEP_2_3'); ?></h3>
		<table class="admintable com-rsform-table-props">
			<tr>
				<td width="350" style="width: 350px;" align="right" class="key"><?php echo JText::_('RSFP_WHAT_DO_YOU_WANT_SUBMISSION'); ?></td>
				<td class="com-rsform-css-fix"><?php echo $this->lists['SubmissionAction']; ?></td>
			</tr>
			<tr id="RedirectTo1" style="display: none;">
				<td width="350" style="width: 350px;" align="right" class="key"><?php echo JText::_('RSFP_SUBMISSION_REDIRECT_WHERE'); ?></td>
				<td><input class="rs_inp" type="text" class="inputbox" size="55" name="ReturnUrl" value="" /></td>
			</tr>
			<tr id="RedirectTo2" style="display: none;">
				<td colspan="2"><?php echo JText::_('RSFP_SUBMISSION_REDIRECT_WHERE_DESC'); ?></td>
			</tr>
			<tr id="ThankYou1" style="display: none;">
				<td width="350" style="width: 350px;" align="right" class="key"><?php echo JText::_('RSFP_SUBMISSION_WHAT_THANKYOU'); ?></td>
				<td>&nbsp;</td>
			</tr>
			<tr id="ThankYou2" style="display: none;">
				<td><?php echo JText::_('RSFP_SUBMISSION_WHAT_THANKYOU_DESC'); ?></td>
				<td><?php echo $this->editor->display('Thankyou', JText::_('RSFP_THANKYOU_DEFAULT'),500,250,70,10); ?></td>
			</tr>
		</table>
		
		<p><button class="btn pull-left btn-primary" type="button" onclick="Joomla.submitbutton('forms.new.stepthree');"><?php echo JText::_('Next'); ?></button></p>
	
	<input type="hidden" name="option" value="com_rsform" />
	<input type="hidden" name="task" value="forms.new.stepthree" />
	</fieldset>
</form>

<?php JHtml::_('behavior.keepalive'); ?>