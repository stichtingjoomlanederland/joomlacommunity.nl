<?php
/**
* @package RSComments!
* @copyright (C) 2015 www.rsjoomla.com
* @license GPL, http://www.gnu.org/licenses/gpl-2.0.html
*/

defined('_JEXEC') or die('Restricted access'); ?>

<div class="alert" id="report-message" style="display: none;"></div>

<div <?php if (RSCommentsHelper::isJ3()) { ?>class="well"<?php } else { ?> id="mailto-window" <?php } ?>>
	<div class="row-fluid">
		<div class="control-group">
			<div class="control-label">
				<label for="name"><?php echo JText::_('COM_RSCOMMENTS_REPORT_REASON'); ?></label>
			</div>
			<div class="controls">
				<textarea id="report-reason" name="report" class="span11" cols="45" rows="7"></textarea>
			</div>
		</div>
		
		<?php if ($this->config->enable_captcha_reports) { ?>
		<div class="control-group">
			<div class="controls">
				<?php if ($this->config->captcha == 0) { ?>
				<img src="<?php echo RSCommentsHelper::route('index.php?option=com_rscomments&task=captcha&type=report'); ?>" id="report_submit_captcha_image" alt="Antispam" height="80" />
					<span class="<?php echo RSTooltip::tooltipClass(); ?>" title="<?php echo RSTooltip::tooltipText(JText::_('COM_RSCOMMENTS_REFRESH_CAPTCHA_DESC')); ?>">
						<a id="rscomments-refresh-captcha" style="border-style: none" href="javascript:void(0)" onclick="rsc_refresh_captcha('<?php echo $this->root; ?>','<?php echo RSCommentsHelper::route('index.php?option=com_rscomments&task=captcha&type=report'); ?>','report');">
							<img src="<?php echo RSCommentsHelper::ImagePath('refresh.png'); ?>" alt="<?php echo JText::_('COM_RSCOMMENTS_REFRESH_CAPTCHA'); ?>" border="0" onclick="this.blur()" align="top" />
						</a>
					</span> <br />
					<input type="text" name="captcha" id="report_submit_captcha" size="40" value="" class="inputbox <?php echo RSTooltip::tooltipClass(); ?> required" title="<?php echo RSTooltip::tooltipText($this->config->captcha_cases ? JText::_('COM_RSCOMMENTS_CAPTCHA_CASE_SENSITIVE') : JText::_('COM_RSCOMMENTS_CAPTCHA_CASE_INSENSITIVE')); ?>" />
				<?php } else if ($this->config->captcha == 1) {
					require_once(JPATH_SITE.'/components/com_rscomments/helpers/recaptcha/recaptchalib.php');
					echo RSCommentsReCAPTCHA::loadScript('rscomments-recaptcha', $this->config);
				} else { ?>
				<div id="rscomments-recaptcha"></div>
				<?php } ?>
			</div>
		</div>
		<?php } ?>
	</div>
</div>
<input type="hidden" id="commentid" name="commentid" value="<?php echo JFactory::getApplication()->input->getInt('id',0); ?>" />

<?php if ($this->config->captcha == 2) { ?>
<script type="text/javascript">
	grecaptcha.render('rscomments-recaptcha', {
		'sitekey'	: '<?php echo $this->escape($this->config->recaptcha_new_site_key); ?>',
		'theme'		: '<?php echo $this->escape($this->config->recaptcha_new_theme); ?>',
		'type'		: '<?php echo $this->escape($this->config->recaptcha_new_type); ?>'
	});
</script>
<?php } ?>