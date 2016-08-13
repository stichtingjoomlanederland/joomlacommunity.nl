<?php
/**
* @package RSForm! Pro
* @copyright (C) 2007-2014 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/

defined('_JEXEC') or die('Restricted access');
?>
<script type="text/javascript">
	function changeLayout(layout)
	{
		document.getElementById('FormLayoutImage').src = 'components/com_rsform/assets/images/layouts/' + layout + '.gif';
		document.getElementById('FormLayoutXHTML').style.display = 'none';
		
		if (layout.indexOf('xhtml') != -1 || layout == 'responsive')
			document.getElementById('FormLayoutXHTML').style.display = '';
	}
	
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
	
	function submitbutton(task)
	{
		if (task == 'forms.cancel')
		{
			submitform(task);
			return;
		}
		else
		{
			var form = document.adminForm;
			
			jQuery(form.FormTitle).removeClass('thisformError');
			jQuery(form.ReturnUrl).removeClass('thisformError');
			
			if (form.FormTitle.value.length == 0)
			{
				alert('<?php echo JText::_('RSFP_WHATS_FORM_TITLE_VALIDATION', true); ?>');
				jQuery(form.FormTitle).addClass('thisformError');
				return;
			}
			if (form.SubmissionAction.value == 'redirect' && form.ReturnUrl.value.length == 0)
			{
				alert('<?php echo JText::_('RSFP_SUBMISSION_REDIRECT_WHERE_VALIDATION', true); ?>');
				jQuery(form.ReturnUrl).addClass('thisformError');
				return;
			}
			
			submitform(task);
		}
	}
	
	Joomla.submitbutton = submitbutton;
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
					<label for="formLayoutResponsive" class="radio inline"><input type="radio" id="formLayoutResponsive" name="FormLayout" value="responsive" onclick="changeLayout(this.value)"/> <?php echo JText::_('RSFP_LAYOUT_RESPONSIVE');?></label>
					<label for="formLayoutBootstrap2" class="radio inline"><input type="radio" id="formLayoutBootstrap2" name="FormLayout" value="bootstrap2" onclick="changeLayout(this.value)" checked="checked" /> <?php echo JText::_('RSFP_LAYOUT_BOOTSTRAP2');?></label>
					<label for="formLayoutBootstrap3" class="radio inline"><input type="radio" id="formLayoutBootstrap3" name="FormLayout" value="bootstrap3" onclick="changeLayout(this.value)"/> <?php echo JText::_('RSFP_LAYOUT_BOOTSTRAP3');?></label>
					<label for="formLayoutUikit" class="radio inline"><input type="radio" id="formLayoutUikit" name="FormLayout" value="uikit" onclick="changeLayout(this.value)"/> <?php echo JText::_('RSFP_LAYOUT_UIKIT');?></label>
				</td>
			</tr>
			<tr>
				<td><?php echo JText::_('RSFP_WHATS_FORM_LAYOUT_DESC'); ?></td>
				<td><img src="components/com_rsform/assets/images/layouts/bootstrap2.gif" id="FormLayoutImage" width="175"/></td>
			</tr>
			<tr id="FormLayoutXHTML">
				<td colspan="2"><?php echo JText::_('RSFP_WHATS_FORM_LAYOUT_XHTML'); ?></td>
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
		
		<p><button class="btn pull-left btn-primary" type="button" onclick="submitbutton('forms.new.stepthree');"><?php echo JText::_('Next'); ?></button></p>
	
	<input type="hidden" name="option" value="com_rsform" />
	<input type="hidden" name="task" value="forms.new.stepthree" />
	</fieldset>
</form>

<?php JHTML::_('behavior.keepalive'); ?>