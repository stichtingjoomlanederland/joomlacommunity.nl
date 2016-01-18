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
	<form method="post" action="<?php echo JRoute::_('index.php?option=com_rsfiles'.$this->itemid); ?>" name="adminForm" id="adminForm" autocomplete="off">		
		<div class="alert" id="rsf_alert" style="display:none;">
			<button type="button" class="close" onclick="jQuery('#rsf_alert').css('display','none');">&times;</button>
			<span id="rsf_message"></span>
		</div>
		
		<div class="well">
			<p class="center"><strong><?php echo JText::_('COM_RSFILES_CAPTCHA_VALIDATE_MESSAGE'); ?></strong></p>
			
			<div style="display: table; margin: 0 auto;">
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
			
			<div class="clearfix"></div>
			<br />
			<div class="center">
				<button type="submit" class="btn btn-primary" onclick="return rsf_validate();"><?php echo JText::_('COM_RSFILES_DOWNLOAD'); ?></button>
				<button type="button" class="btn" onclick="window.parent.SqueezeBox.close();"><?php echo JText::_('COM_RSFILES_CANCEL'); ?></button>
			</div>
		</div>

		<?php echo JHTML::_( 'form.token' ); ?>
		<input type="hidden" name="task" value="rsfiles.validate" />
		<input type="hidden" name="path" value="<?php echo $this->path; ?>" />
		<input type="hidden" name="return" value="<?php echo $this->return; ?>" />
	</form>
</div>