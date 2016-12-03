<?php
/**
* @package		EasyDiscuss
* @copyright	Copyright (C) 2010 - 2015 Stack Ideas Sdn Bhd. All rights reserved.
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

                    <div class="form-group">
                        <div class="col-md-5 control-label">
                            <?php echo $this->html('form.label', 'COM_EASYDISCUSS_SKIP_RECAPTCHA'); ?>
                        </div>
                        <div class="col-md-7">
                            <?php echo $this->html('form.textbox', 'antispam_skip_captcha', $this->config->get('antispam_skip_captcha'));?>
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="col-md-5 control-label">
                            <?php echo $this->html('form.label', 'COM_EASYDISCUSS_ENABLE_EASYDISCUSS_CAPTCHA_REGISTERED'); ?>
                        </div>
                        <div class="col-md-7">
                            <?php echo $this->html('form.boolean', ' antispam_captcha_registered', $this->config->get('antispam_captcha_registered')); ?>
                        </div>
                    </div>
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
                    <div class="form-group">
                        <div class="col-md-5 control-label">
                            <?php echo $this->html('form.label', 'COM_EASYDISCUSS_RECAPTCHA_USE_SSL'); ?>
                        </div>
                        <div class="col-md-7">
                            <?php echo $this->html('form.boolean', ' antispam_recaptcha_ssl', $this->config->get('antispam_recaptcha_ssl')); ?>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="col-md-5 control-label">
                            <?php echo $this->html('form.label', 'COM_EASYDISCUSS_RECAPTCHA_PUBLIC_KEY'); ?>
                        </div>
                        <div class="col-md-7">
                            <?php echo $this->html('form.textbox', 'antispam_recaptcha_public', $this->config->get('antispam_recaptcha_public'));?>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="col-md-5 control-label">
                            <?php echo $this->html('form.label', 'COM_EASYDISCUSS_RECAPTCHA_PRIVATE_KEY'); ?>
                        </div>
                        <div class="col-md-7">
                            <?php echo $this->html('form.textbox', 'antispam_recaptcha_private', $this->config->get('antispam_recaptcha_private'));?>
                        </div>
                    </div>
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
                            <select name="antispam_recaptcha_lang" class="form-control">
                                <option value="en"<?php echo $this->config->get('antispam_recaptcha_lang') == 'en' ? ' selected="selected"' : ''; ?>><?php echo JText::_('COM_EASYDISCUSS_RECAPTCHA_LANGUAGE_ENGLISH');?></option>
                                <option value="ru"<?php echo $this->config->get('antispam_recaptcha_lang') == 'ru' ? ' selected="selected"' : ''; ?>><?php echo JText::_('COM_EASYDISCUSS_RECAPTCHA_LANGUAGE_RUSSIAN');?></option>
                                <option value="fr"<?php echo $this->config->get('antispam_recaptcha_lang') == 'fr' ? ' selected="selected"' : ''; ?>><?php echo JText::_('COM_EASYDISCUSS_RECAPTCHA_LANGUAGE_FRENCH');?></option>
                                <option value="de"<?php echo $this->config->get('antispam_recaptcha_lang') == 'de' ? ' selected="selected"' : ''; ?>><?php echo JText::_('COM_EASYDISCUSS_RECAPTCHA_LANGUAGE_GERMAN');?></option>
                                <option value="nl"<?php echo $this->config->get('antispam_recaptcha_lang') == 'nl' ? ' selected="selected"' : ''; ?>><?php echo JText::_('COM_EASYDISCUSS_RECAPTCHA_LANGUAGE_DUTCH');?></option>
                                <option value="pt"<?php echo $this->config->get('antispam_recaptcha_lang') == 'pt' ? ' selected="selected"' : ''; ?>><?php echo JText::_('COM_EASYDISCUSS_RECAPTCHA_LANGUAGE_PORTUGUESE');?></option>
                                <option value="tr"<?php echo $this->config->get('antispam_recaptcha_lang') == 'tr' ? ' selected="selected"' : ''; ?>><?php echo JText::_('COM_EASYDISCUSS_RECAPTCHA_LANGUAGE_TURKISH');?></option>
                                <option value="es"<?php echo $this->config->get('antispam_recaptcha_lang') == 'es' ? ' selected="selected"' : ''; ?>><?php echo JText::_('COM_EASYDISCUSS_RECAPTCHA_LANGUAGE_SPANISH');?></option>
                                <option value="fa"<?php echo $this->config->get('antispam_recaptcha_lang') == 'fa' ? ' selected="selected"' : ''; ?>><?php echo JText::_('COM_EASYDISCUSS_RECAPTCHA_LANGUAGE_PERSIAN');?></option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>
        </div>
	</div>

</div>
