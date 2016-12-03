<?php
/**
* @package RSFiles!
* @copyright (C) 2010-2014 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/
defined( '_JEXEC' ) or die( 'Restricted access' ); ?>

<script type="text/javascript">
function rsfl_refresh_captcha() {
	jQuery('#submit_captcha_image').prop('src', '<?php echo JRoute::_('index.php?option=com_rsfiles&task=captcha'.$this->itemid.'&sid='); ?>' + Math.random());
	return false;
}
</script>

<div class="rsfiles-layout">
	<form method="post" action="<?php echo JRoute::_('index.php?option=com_rsfiles'.$this->itemid); ?>" name="adminForm" id="adminForm" autocomplete="off" class="form-validate form-horizontal">
		<div class="alert" id="rsf_alert" style="display:none;">
			<button type="button" class="close" onclick="jQuery('#rsf_alert').css('display','none');">&times;</button>
			<span id="rsf_message"></span>
		</div>
		<div class="row-fluid">
			<div class="well">
				<p><b><?php echo JText::_('COM_RSFILES_EMAIL_DOWNLOAD_MESSAGE'); ?></b></p>
				<div class="control-group">
					<div class="control-label">
						<label for="jform_name"><?php echo JText::_('COM_RSFILES_NAME'); ?></label>
					</div>
					<div class="controls">
						<input type="text" size="45" class="input-large" value="<?php echo $this->session->get('rsfiles_name'); ?>" id="jform_name" name="name" />
					</div>
				</div>
				<div class="control-group">
					<div class="control-label">
						<label for="jform_email"><?php echo JText::_('COM_RSFILES_EMAIL'); ?></label>
					</div>
					<div class="controls">
						<input type="text" size="45" class="input-large" value="<?php echo $this->session->get('rsfiles_email'); ?>" id="jform_email" name="email" />
					</div>
				</div>
				
				<?php if ($this->item->IdLicense) { ?>
				<div class="control-group">
					<div class="control-label">
						<label for="jform_email">&nbsp;</label>
					</div>
					<div class="controls">
						<input type="checkbox" name="agreement" id="agreement" value="1" />
						<label for="agreement" class="checkbox inline"><a href="<?php echo JRoute::_($this->item->filelicense,false); ?>" onclick="window.open(this.href,'agreement', 'toolbar=no,scrollbars=yes,resizable=yes,top=200,left=200,width=600,height=400'); return false;"><?php echo JText::_('COM_RSFILES_I_AGREE_TO_THE_LICENSE'); ?></a></label>
					</div>
				</div>
				<?php } ?>
				
				<?php if ($this->config->captcha_enabled) { ?>
				<div class="control-group">
					<div class="control-label">
						<label for="jform_name"></label>
					</div>
					<div class="controls">
						<?php if ($this->config->captcha_enabled == 1) { ?>
						<img src="<?php echo JRoute::_('index.php?option=com_rsfiles&task=captcha&sid='.mt_rand()); ?>" id="submit_captcha_image" alt="" class="<?php echo rsfilesHelper::tooltipClass(); ?>" title="<?php echo rsfilesHelper::tooltipText(JText::_('COM_RSFILES_CAPTHA_CASE_'.$this->config->captcha_case_sensitive)); ?>" />
						<a href="javascript:void(0)" onclick="rsfl_refresh_captcha();" class="<?php echo rsfilesHelper::tooltipClass(); ?>" title="<?php echo rsfilesHelper::tooltipText(JText::_('COM_RSFILES_CAPTCHA_REFRESH')); ?>"><i class="rsicon-refresh"></i></a>
						<br />
						<input type="text" name="captcha" id="submit_captcha" size="40" value="" class="" />
						<?php } else if ($this->config->captcha_enabled == 2) { ?>
						<?php echo RSFilesJReCAPTCHA::getHtml(); ?>
						<?php } else if ($this->config->captcha_enabled == 3) { ?>
						<div class="g-recaptcha" data-sitekey="<?php echo $this->config->recaptcha_new_site_key; ?>" data-theme="<?php echo $this->config->recaptcha_new_theme; ?>" data-type="<?php echo $this->config->recaptcha_new_type; ?>"></div>
						<?php } ?>
					</div>
				</div>
				<?php } ?>
				<div class="control-group">
					<div class="control-label">
						<label for="jform_name"></label>
					</div>
					<div class="controls">
						<button type="submit" class="btn btn-primary" onclick="return rsf_validate_email();"><?php echo JText::_('COM_RSFILES_DOWNLOAD'); ?></button>
						<button type="button" class="btn" onclick="window.parent.SqueezeBox.close();"><?php echo JText::_('COM_RSFILES_CANCEL'); ?></button>
					</div>
				</div>
			</div>
		</div>
		
		<?php echo JHTML::_( 'form.token' ); ?>
		<input type="hidden" name="task" value="rsfiles.emaildownload" />
		<input type="hidden" name="path" value="<?php echo $this->path; ?>" />
		<input type="hidden" name="return" value="<?php echo base64_encode(JURI::getInstance()); ?>" />
		<input type="hidden" name="from" value="<?php echo $this->app->input->getString('from'); ?>" />
		<input type="hidden" name="hash" value="<?php echo $this->app->input->getString('hash'); ?>" />
	</form>
</div>