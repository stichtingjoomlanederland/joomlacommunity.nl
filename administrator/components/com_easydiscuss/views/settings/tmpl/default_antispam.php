<?php
/**
* @package		Discuss
* @copyright	Copyright (C) 2010 Stack Ideas Private Limited. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasyDiscuss is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Restricted access');
?>
<div class="row-fluid">
	<div class="span12 panel-title">
		<h2><?php echo JText::_( 'COM_EASYDISCUSS_SETTINGS_ANTI_SPAM_TITLE' );?></h2>
		<p style="margin: 0 0 15px;">
			<?php echo JText::_( 'COM_EASYDISCUSS_SETTINGS_ANTI_SPAM_DESC' );?>
		</p>
		<!-- <a href="javascript:void(0)" class="btn btn-success">Documentation</a> -->
	</div>
</div>

<div class="row-fluid ">
	<div class="span6">
		<div class="widget accordion-group">
			<div class="whead accordion-heading">
				<a href="javascript:void(0);" data-foundry-toggle="collapse" data-target="#option01">
				<h6><?php echo JText::_( 'COM_EASYDISCUSS_AKISMET_INTEGRATIONS' ); ?></h6>
				<i class="icon-chevron-down"></i>
				</a>
			</div>

			<div id="option01" class="accordion-body collapse in">
				<div class="wbody">
					<div class="si-form-row">
						<div class="span5 form-row-label">
							<label>
								<?php echo JText::_( 'COM_EASYDISCUSS_AKISMET_INTEGRATIONS' ); ?>
							</label>
						</div>
						<div class="span7"
							rel="ed-popover"
							data-placement="left"
							data-original-title="<?php echo JText::_( 'COM_EASYDISCUSS_AKISMET_INTEGRATIONS' ); ?>"
							data-content="<?php echo JText::_('COM_EASYDISCUSS_AKISMET_INTEGRATIONS_DESC'); ?>"
						>
							<?php echo $this->renderCheckbox( 'antispam_akismet' , $this->config->get( 'antispam_akismet' ) );?>
						</div>
					</div>
					<div class="si-form-row">
						<div class="span5 form-row-label">
							<label>
								<?php echo JText::_( 'COM_EASYDISCUSS_AKISMET_API_KEY' ); ?>
							</label>
						</div>
						<div class="span7"
							rel="ed-popover"
							data-placement="left"
							data-original-title="<?php echo JText::_( 'COM_EASYDISCUSS_AKISMET_API_KEY' ); ?>"
							data-content="<?php echo JText::_('COM_EASYDISCUSS_AKISMET_API_KEY_DESC'); ?>"
						>
							<input type="text" class="full-width" name="antispam_akismet_key" value="<?php echo $this->config->get('antispam_akismet_key');?>" size="60" />
						</div>
					</div>
				</div>
			</div>
		</div>
		<div class="widget accordion-group">
			<div class="whead accordion-heading">
				<a href="javascript:void(0);" data-foundry-toggle="collapse" data-target="#option02">
				<h6><?php echo JText::_( 'COM_EASYDISCUSS_FILTERING' ); ?></h6>
				<i class="icon-chevron-down"></i>
				</a>
			</div>

			<div id="option02" class="accordion-body collapse in">
				<div class="wbody">
					<div class="si-form-row">
						<div class="span5 form-row-label">
							<label>
								<?php echo JText::_( 'COM_EASYDISCUSS_ENABLE_BAD_WORDS_FILTER' ); ?>
							</label>
						</div>
						<div class="span7"
							rel="ed-popover"
							data-placement="left"
							data-original-title="<?php echo JText::_( 'COM_EASYDISCUSS_ENABLE_BAD_WORDS_FILTER' ); ?>"
							data-content="<?php echo JText::_('COM_EASYDISCUSS_ENABLE_BAD_WORDS_FILTER_DESC'); ?>"
						>
							<?php echo $this->renderCheckbox( 'main_filterbadword' , $this->config->get( 'main_filterbadword' ) );?>
						</div>
					</div>
					<div class="si-form-row">
						<div class="span5 form-row-label">
							<label>
								<?php echo JText::_( 'COM_EASYDISCUSS_BAD_WORDS' ); ?>
							</label>
						</div>
						<div class="span7"
							rel="ed-popover"
							data-placement="left"
							data-original-title="<?php echo JText::_( 'COM_EASYDISCUSS_REPLACE_BAD_WORDS' ); ?>"
							data-content="<?php echo JText::_('COM_EASYDISCUSS_REPLACE_BAD_WORDS_DESC'); ?>"
						>
							<textarea name="main_filtertext" rows="5" class="input-full" cols="35"><?php echo $this->config->get('main_filtertext');?></textarea>

							<div><?php echo JText::_( 'COM_EASYDISCUSS_REPLACE_BAD_WORDS_TIPS' ); ?></div>
						</div>
					</div>
				</div>
			</div>
		</div>

		<div class="widget accordion-group">
			<div class="whead accordion-heading">
				<a href="javascript:void(0);" data-foundry-toggle="collapse" data-target="#option02">
				<h6><?php echo JText::_( 'COM_EASYDISCUSS_CAPTCHA' ); ?></h6>
				<i class="icon-chevron-down"></i>
				</a>
			</div>

			<div id="option02" class="accordion-body collapse in">
				<div class="wbody">
					<div class="si-form-row">
						<div class="span5 form-row-label">
							<label>
								<?php echo JText::_( 'COM_EASYDISCUSS_ENABLE_EASYDISCUSS_CAPTCHA' ); ?>
							</label>
						</div>
						<div class="span7"
							rel="ed-popover"
							data-placement="left"
							data-original-title="<?php echo JText::_( 'COM_EASYDISCUSS_ENABLE_EASYDISCUSS_CAPTCHA' ); ?>"
							data-content="<?php echo JText::_('COM_EASYDISCUSS_ENABLE_EASYDISCUSS_CAPTCHA_DESC'); ?>"
						>
							<?php echo $this->renderCheckbox( 'antispam_easydiscuss_captcha' , $this->config->get( 'antispam_easydiscuss_captcha' ) );?>
						</div>
					</div>

				</div>
			</div>
			<div id="option02" class="accordion-body collapse in">
				<div class="wbody">
					<div class="si-form-row">
						<div class="span5 form-row-label">
							<label>
								<?php echo JText::_( 'COM_EASYDISCUSS_ENABLE_EASYDISCUSS_CAPTCHA_REGISTERED' ); ?>
							</label>
						</div>
						<div class="span7"
							rel="ed-popover"
							data-placement="left"
							data-original-title="<?php echo JText::_( 'COM_EASYDISCUSS_ENABLE_EASYDISCUSS_CAPTCHA_REGISTERED' ); ?>"
							data-content="<?php echo JText::_('COM_EASYDISCUSS_ENABLE_EASYDISCUSS_CAPTCHA_REGISTERED_DESC'); ?>"
						>
							<?php echo $this->renderCheckbox( 'antispam_easydiscuss_captcha_registered' , $this->config->get( 'antispam_easydiscuss_captcha_registered' ) );?>
						</div>
					</div>

				</div>
			</div>
		</div>

		<?php if( false ) { ?>
		<div class="widget accordion-group">
			<div class="whead accordion-heading">
				<a href="javascript:void(0);" data-foundry-toggle="collapse" data-target="#option04">
				<h6><?php echo JText::_( 'COM_EASYDISCUSS_HONEYPOT_INTEGRATIONS' ); ?></h6>
				<i class="icon-chevron-down"></i>
				</a>
			</div>

			<div id="option04" class="accordion-body collapse in">
				<div class="wbody">
					<div class="si-form-row">
						<?php echo JText::_( 'COM_EASYDISCUSS_HONEYPOT_INFO' ); ?>
					</div>
					<div class="si-form-row">
						<div class="span5 form-row-label">
							<label>
								<?php echo JText::_( 'COM_EASYDISCUSS_HONEYPOT_ENABLE' ); ?>
							</label>
						</div>
						<div class="span7"
							rel="ed-popover"
							data-placement="left"
							data-original-title="<?php echo JText::_( 'COM_EASYDISCUSS_HONEYPOT_ENABLE' ); ?>"
							data-content="<?php echo JText::_('COM_EASYDISCUSS_HONEYPOT_ENABLE_DESC'); ?>"
						>
							<?php echo $this->renderCheckbox( 'antispam_honeypot' , $this->config->get( 'antispam_honeypot' ) );?>
						</div>
					</div>
					<div class="si-form-row">
						<div class="span5 form-row-label">
							<label>
								<?php echo JText::_( 'COM_EASYDISCUSS_HONEYPOT_API_KEY' ); ?>
							</label>
						</div>
						<div class="span7"
							rel="ed-popover"
							data-placement="left"
							data-original-title="<?php echo JText::_( 'COM_EASYDISCUSS_HONEYPOT_API_KEY' ); ?>"
							data-content="<?php echo JText::_('COM_EASYDISCUSS_HONEYPOT_API_KEY_DESC'); ?>"
						>
							<input type="text" class="full-width" name="antispam_honeypot_key" value="<?php echo $this->config->get('antispam_honeypot_key');?>" size="60" />
						</div>
					</div>
					<div class="si-form-row">
						<div class="span5 form-row-label">
							<label>
								<?php echo JText::_( 'COM_EASYDISCUSS_HONEYPOT_BLOCK_TYPE' ); ?>
							</label>
						</div>
						<div class="span7"
							rel="ed-popover"
							data-placement="left"
							data-original-title="<?php echo JText::_( 'COM_EASYDISCUSS_HONEYPOT_BLOCK_TYPE' ); ?>"
							data-content="<?php echo JText::_('COM_EASYDISCUSS_HONEYPOT_BLOCK_TYPE_DESC'); ?>"
						>
							<select name="antispam_honeypot_block" class="full-width">
								<option value="en"<?php echo $this->config->get('antispam_honeypot_block') == 'value' ? ' selected="selected"' : ''; ?>><?php echo JText::_('COM_EASYDISCUSS_HONEYPOT_BLOCK_THREATVALUE');?></option>
								<option value="ru"<?php echo $this->config->get('antispam_honeypot_block') == 'type' ? ' selected="selected"' : ''; ?>><?php echo JText::_('COM_EASYDISCUSS_HONEYPOT_BLOCK_THREATTYPE');?></option>
								<option value="fr"<?php echo $this->config->get('antispam_honeypot_block') == 'both' ? ' selected="selected"' : ''; ?>><?php echo JText::_('COM_EASYDISCUSS_HONEYPOT_BLOCK_THREAT_BOTH');?></option>
							</select>
						</div>
					</div>
					<div class="si-form-row">
						<div class="span5 form-row-label">
							<label>
								<?php echo JText::_( 'COM_EASYDISCUSS_HONEYPOT_THREAT_VALUE' ); ?>
							</label>
						</div>
						<div class="span7"
							rel="ed-popover"
							data-placement="left"
							data-original-title="<?php echo JText::_( 'COM_EASYDISCUSS_HONEYPOT_THREAT_VALUE' ); ?>"
							data-content="<?php echo JText::_('COM_EASYDISCUSS_HONEYPOT_THREAT_VALUE_DESC'); ?>"
						>
							<input type="text" class="full-width" name="antispam_honeypot_threatvalue" value="<?php echo $this->config->get('antispam_honeypot_threatvalue');?>" size="1" />
						</div>
					</div>
					<div class="si-form-row">
						<div class="span5 form-row-label">
							<label>
								<?php echo JText::_( 'COM_EASYDISCUSS_HONEYPOT_THREAT_TYPE_SEARCHENGINE' ); ?>
							</label>
						</div>
						<div class="span7"
							rel="ed-popover"
							data-placement="left"
							data-original-title="<?php echo JText::_( 'COM_EASYDISCUSS_HONEYPOT_THREAT_TYPE_SEARCHENGINE' ); ?>"
							data-content="<?php echo JText::_('COM_EASYDISCUSS_HONEYPOT_THREAT_TYPE_SEARCHENGINE_DESC'); ?>"
						>

							<?php echo $this->renderCheckbox( 'antispam_honeypot_threat_searchengine' , $this->config->get( 'antispam_honeypot_threat_searchengine' ) );?>
						</div>
					</div>
					<div class="si-form-row">
						<div class="span5 form-row-label">
							<label>
								<?php echo JText::_( 'COM_EASYDISCUSS_HONEYPOT_THREAT_TYPE_SUSPICIOUS' ); ?>
							</label>
						</div>
						<div class="span7"
							rel="ed-popover"
							data-placement="left"
							data-original-title="<?php echo JText::_( 'COM_EASYDISCUSS_HONEYPOT_THREAT_TYPE_SUSPICIOUS' ); ?>"
							data-content="<?php echo JText::_('COM_EASYDISCUSS_HONEYPOT_THREAT_TYPE_SUSPICIOUS_DESC'); ?>"
						>

							<?php echo $this->renderCheckbox( 'antispam_honeypot_threat_suspicious' , $this->config->get( 'antispam_honeypot_threat_suspicious' ) );?>
						</div>
					</div>
					<div class="si-form-row">
						<div class="span5 form-row-label">
							<label>
								<?php echo JText::_( 'COM_EASYDISCUSS_HONEYPOT_THREAT_TYPE_HARVESTER' ); ?>
							</label>
						</div>
						<div class="span7"
							rel="ed-popover"
							data-placement="left"
							data-original-title="<?php echo JText::_( 'COM_EASYDISCUSS_HONEYPOT_THREAT_TYPE_HARVESTER' ); ?>"
							data-content="<?php echo JText::_('COM_EASYDISCUSS_HONEYPOT_THREAT_TYPE_HARVESTER_DESC'); ?>"
						>

							<?php echo $this->renderCheckbox( 'antispam_honeypot_threat_harvester' , $this->config->get( 'antispam_honeypot_threat_harvester' ) );?>
						</div>
					</div>
					<div class="si-form-row">
						<div class="span5 form-row-label">
							<label>
								<?php echo JText::_( 'COM_EASYDISCUSS_HONEYPOT_THREAT_TYPE_SPAMMER' ); ?>
							</label>
						</div>
						<div class="span7"
							rel="ed-popover"
							data-placement="left"
							data-original-title="<?php echo JText::_( 'COM_EASYDISCUSS_HONEYPOT_THREAT_TYPE_SPAMMER' ); ?>"
							data-content="<?php echo JText::_('COM_EASYDISCUSS_HONEYPOT_THREAT_TYPE_SPAMMER_DESC'); ?>"
						>

							<?php echo $this->renderCheckbox( 'antispam_honeypot_threat_spammer' , $this->config->get( 'antispam_honeypot_threat_spammer' ) );?>
						</div>
					</div>
				</div>
			</div>
		</div>
		<?php } ?>
	</div>
	<div class="span6">
		<div class="widget accordion-group">
			<div class="whead accordion-heading">
				<a href="javascript:void(0);" data-foundry-toggle="collapse" data-target="#option03">
				<h6><?php echo JText::_( 'COM_EASYDISCUSS_RECAPTCHA_INTEGRATIONS' ); ?></h6>
				<i class="icon-chevron-down"></i>
				</a>
			</div>

			<div id="option03" class="accordion-body collapse in">
				<div class="wbody">
					<p><?php echo JText::_('COM_EASYDISCUSS_RECAPTCHA_INTEGRATIONS_DESC');?></p>
					<div class="si-form-row">
						<div class="span5 form-row-label">
							<label>
								<?php echo JText::_( 'COM_EASYDISCUSS_RECAPTCHA_INTEGRATIONS' ); ?>
							</label>
						</div>
						<div class="span7"
							rel="ed-popover"
							data-placement="left"
							data-original-title="<?php echo JText::_( 'COM_EASYDISCUSS_RECAPTCHA_INTEGRATIONS' ); ?>"
							data-content="<?php echo JText::_('COM_EASYDISCUSS_ENABLE_RECAPTCHA'); ?>"
						>
							<?php echo $this->renderCheckbox( 'antispam_recaptcha' , $this->config->get( 'antispam_recaptcha' ) );?>
						</div>
					</div>
					<div class="si-form-row">
						<div class="span5 form-row-label">
							<label>
								<?php echo JText::_( 'COM_EASYDISCUSS_RECAPTCHA_REGISTERED_MEMBERS' ); ?>
							</label>
						</div>
						<div class="span7"
							rel="ed-popover"
							data-placement="left"
							data-original-title="<?php echo JText::_( 'COM_EASYDISCUSS_RECAPTCHA_REGISTERED_MEMBERS' ); ?>"
							data-content="<?php echo JText::_('COM_EASYDISCUSS_RECAPTCHA_REGISTERED_MEMBERS_DESC'); ?>"
						>
							<?php echo $this->renderCheckbox( 'antispam_recaptcha_registered_members' , $this->config->get( 'antispam_recaptcha_registered_members' ) );?>
						</div>
					</div>
					<div class="si-form-row">
						<div class="span5 form-row-label">
							<label>
								<?php echo JText::_( 'COM_EASYDISCUSS_SKIP_RECAPTCHA' ); ?>
							</label>
						</div>
						<div class="span7"
							rel="ed-popover"
							data-placement="left"
							data-original-title="<?php echo JText::_( 'COM_EASYDISCUSS_SKIP_RECAPTCHA' ); ?>"
							data-content="<?php echo JText::_('COM_EASYDISCUSS_SKIP_RECAPTCHA_DESC'); ?>"
						>
							<input type="text" name="antispam_skip_recaptcha" class="input-mini center" value="<?php echo $this->config->get('antispam_skip_recaptcha');?>" />
						</div>
					</div>
					<div class="si-form-row">
						<div class="span5 form-row-label">
							<label>
								<?php echo JText::_( 'COM_EASYDISCUSS_RECAPTCHA_USE_SSL' ); ?>
							</label>
						</div>
						<div class="span7"
							rel="ed-popover"
							data-placement="left"
							data-original-title="<?php echo JText::_( 'COM_EASYDISCUSS_RECAPTCHA_USE_SSL' ); ?>"
							data-content="<?php echo JText::_('COM_EASYDISCUSS_RECAPTCHA_USE_SSL_DESC'); ?>"
						>
							<?php echo $this->renderCheckbox( 'antispam_recaptcha_ssl' , $this->config->get( 'antispam_recaptcha_ssl' ) );?>
						</div>
					</div>
					<div class="si-form-row">
						<div class="span5 form-row-label">
							<label>
								<?php echo JText::_( 'COM_EASYDISCUSS_RECAPTCHA_PUBLIC_KEY' ); ?>
							</label>
						</div>
						<div class="span7"
							rel="ed-popover"
							data-placement="left"
							data-original-title="<?php echo JText::_( 'COM_EASYDISCUSS_RECAPTCHA_PUBLIC_KEY' ); ?>"
							data-content="<?php echo JText::_('COM_EASYDISCUSS_RECAPTCHA_PUBLIC_KEY_DESC'); ?>"
						>
							<input type="text" class="full-width" name="antispam_recaptcha_public" value="<?php echo $this->config->get('antispam_recaptcha_public');?>" size="60" />
						</div>
					</div>
					<div class="si-form-row">
						<div class="span5 form-row-label">
							<label>
								<?php echo JText::_( 'COM_EASYDISCUSS_RECAPTCHA_PRIVATE_KEY' ); ?>
							</label>
						</div>
						<div class="span7"
							rel="ed-popover"
							data-placement="left"
							data-original-title="<?php echo JText::_( 'COM_EASYDISCUSS_RECAPTCHA_PRIVATE_KEY' ); ?>"
							data-content="<?php echo JText::_('COM_EASYDISCUSS_RECAPTCHA_PRIVATE_KEY_DESC'); ?>"
						>
							<input type="text" class="full-width" name="antispam_recaptcha_private" value="<?php echo $this->config->get('antispam_recaptcha_private');?>" size="60" />
						</div>
					</div>
					<div class="si-form-row">
						<div class="span5 form-row-label">
							<label>
								<?php echo JText::_( 'COM_EASYDISCUSS_RECAPTCHA_THEME' ); ?>
							</label>
						</div>
						<div class="span7"
							rel="ed-popover"
							data-placement="left"
							data-original-title="<?php echo JText::_( 'COM_EASYDISCUSS_RECAPTCHA_THEME' ); ?>"
							data-content="<?php echo JText::_('COM_EASYDISCUSS_RECAPTCHA_THEME_DESC'); ?>"
						>
							<select name="antispam_recaptcha_theme" class="full-width">
								<option value="clean"<?php echo $this->config->get('antispam_recaptcha_theme') == 'clean' ? ' selected="selected"' : ''; ?>><?php echo JText::_('COM_EASYDISCUSS_RECAPTCHA_THEME_CLEAN');?></option>
								<option value="white"<?php echo $this->config->get('antispam_recaptcha_theme') == 'white' ? ' selected="selected"' : ''; ?>><?php echo JText::_('COM_EASYDISCUSS_RECAPTCHA_THEME_WHITE');?></option>
								<option value="red"<?php echo $this->config->get('antispam_recaptcha_theme') == 'red' ? ' selected="selected"' : ''; ?>><?php echo JText::_('COM_EASYDISCUSS_RECAPTCHA_THEME_RED');?></option>
								<option value="blackglass"<?php echo $this->config->get('antispam_recaptcha_theme') == 'blackglass' ? ' selected="selected"' : ''; ?>><?php echo JText::_('COM_EASYDISCUSS_RECAPTCHA_THEME_BLACKGLASS');?></option>
							</select>
						</div>
					</div>
					<div class="si-form-row">
						<div class="span5 form-row-label">
							<label>
								<?php echo JText::_( 'COM_EASYDISCUSS_RECAPTCHA_LANGUAGE' ); ?>
							</label>
						</div>
						<div class="span7"
							rel="ed-popover"
							data-placement="left"
							data-original-title="<?php echo JText::_( 'COM_EASYDISCUSS_RECAPTCHA_LANGUAGE' ); ?>"
							data-content="<?php echo JText::_('COM_EASYDISCUSS_RECAPTCHA_LANGUAGE_DESC'); ?>"
						>
							<select name="antispam_recaptcha_lang" class="full-width">
								<option value="en"<?php echo $this->config->get('antispam_recaptcha_lang') == 'en' ? ' selected="selected"' : ''; ?>><?php echo JText::_('COM_EASYDISCUSS_RECAPTCHA_LANGUAGE_ENGLISH');?></option>
								<option value="ru"<?php echo $this->config->get('antispam_recaptcha_lang') == 'ru' ? ' selected="selected"' : ''; ?>><?php echo JText::_('COM_EASYDISCUSS_RECAPTCHA_LANGUAGE_RUSSIAN');?></option>
								<option value="fr"<?php echo $this->config->get('antispam_recaptcha_lang') == 'fr' ? ' selected="selected"' : ''; ?>><?php echo JText::_('COM_EASYDISCUSS_RECAPTCHA_LANGUAGE_FRENCH');?></option>
								<option value="de"<?php echo $this->config->get('antispam_recaptcha_lang') == 'de' ? ' selected="selected"' : ''; ?>><?php echo JText::_('COM_EASYDISCUSS_RECAPTCHA_LANGUAGE_GERMAN');?></option>
								<option value="nl"<?php echo $this->config->get('antispam_recaptcha_lang') == 'nl' ? ' selected="selected"' : ''; ?>><?php echo JText::_('COM_EASYDISCUSS_RECAPTCHA_LANGUAGE_DUTCH');?></option>
								<option value="pt"<?php echo $this->config->get('antispam_recaptcha_lang') == 'pt' ? ' selected="selected"' : ''; ?>><?php echo JText::_('COM_EASYDISCUSS_RECAPTCHA_LANGUAGE_PORTUGUESE');?></option>
								<option value="tr"<?php echo $this->config->get('antispam_recaptcha_lang') == 'tr' ? ' selected="selected"' : ''; ?>><?php echo JText::_('COM_EASYDISCUSS_RECAPTCHA_LANGUAGE_TURKISH');?></option>
								<option value="es"<?php echo $this->config->get('antispam_recaptcha_lang') == 'es' ? ' selected="selected"' : ''; ?>><?php echo JText::_('COM_EASYDISCUSS_RECAPTCHA_LANGUAGE_SPANISH');?></option>
							</select>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
