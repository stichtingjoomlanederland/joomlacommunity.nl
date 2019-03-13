<?php
/**
* @package		EasyDiscuss
* @copyright	Copyright (C) 2010 - 2018 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasyDiscuss is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');
?>
<div class="row">
	<div class="col-md-6">
		<div class="panel">
			<?php echo $this->html('panel.head', 'COM_EASYDISCUSS_CAPTCHA_OTHER'); ?>

			<div class="panel-body">
				<div class="form-horizontal">
					<div class="form-group">
						<div class="col-md-5 control-label">
							<?php echo $this->html('form.label', 'COM_EASYDISCUSS_CAPTCHA_TYPE'); ?>
						</div>
						<div class="col-md-7">
							<?php echo $this->html('form.dropdown', 'antispam_captcha',
									array('none' => 'COM_EASYDISCUSS_NO_CAPTCHA', 'recaptcha' => 'COM_EASYDISCUSS_RECAPTCHA', 'default' => 'COM_EASYDISCUSS_BUILT_IN_CAPTCHA'),
									$this->config->get('antispam_captcha'), 'data-ed-captcha-type');
							?>
						</div>
					</div>

					<?php echo $this->html('settings.textbox', 'antispam_skip_captcha', 'COM_EASYDISCUSS_SKIP_RECAPTCHA', '', array(), '', 'form-control-sm text-center'); ?>

					<?php echo $this->html('settings.toggle', 'antispam_captcha_registered', 'COM_EASYDISCUSS_ENABLE_EASYDISCUSS_CAPTCHA_REGISTERED'); ?>
				</div>
			</div>
		</div>
	</div>

	<div class="col-md-6">
		<div class="panel <?php echo $this->config->get('antispam_captcha') == 'recaptcha' ? '' : 't-hidden';?>" data-captcha-settings data-type="recaptcha">
			<?php echo $this->html('panel.head', 'COM_EASYDISCUSS_RECAPTCHA_INTEGRATIONS'); ?>

			<div class="panel-body">
				<?php echo $this->html('panel.info', 'COM_EASYDISCUSS_RECAPTCHA_INTEGRATIONS_INFO'); ?>

				<div class="form-horizontal">
					<?php echo $this->html('settings.textbox', 'antispam_recaptcha_public', 'COM_EASYDISCUSS_RECAPTCHA_PUBLIC_KEY'); ?>

					<?php echo $this->html('settings.textbox', 'antispam_recaptcha_private', 'COM_EASYDISCUSS_RECAPTCHA_PRIVATE_KEY'); ?>

					<?php echo $this->html('settings.toggle', 'antispam_recaptcha_invisible', 'COM_ED_RECAPTCHA_USE_INVISIBLE'); ?>

					<div class="form-group">
						<div class="col-md-5 control-label">
							<?php echo $this->html('form.label', 'COM_EASYDISCUSS_RECAPTCHA_THEME'); ?>
						</div>
						<div class="col-md-7">
							<select name="antispam_recaptcha_theme" class="form-control">
								<option value="light"<?php echo $this->config->get('antispam_recaptcha_theme') == 'light' ? ' selected="selected"' : ''; ?>><?php echo JText::_('COM_EASYDISCUSS_RECAPTCHA_THEME_LIGHT');?></option>
								<option value="dark"<?php echo $this->config->get('antispam_recaptcha_theme') == 'dark' ? ' selected="selected"' : ''; ?>><?php echo JText::_('COM_EASYDISCUSS_RECAPTCHA_THEME_DARK');?></option>
							</select>
						</div>
					</div>
					<div class="form-group">
						<div class="col-md-5 control-label">
							<?php echo $this->html('form.label', 'COM_EASYDISCUSS_RECAPTCHA_LANGUAGE'); ?>
						</div>
						<div class="col-md-7">
							<?php echo $this->html('form.languages', 'antispam_recaptcha_lang', '', $this->config->get('antispam_recaptcha_lang'));?>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>

</div>
