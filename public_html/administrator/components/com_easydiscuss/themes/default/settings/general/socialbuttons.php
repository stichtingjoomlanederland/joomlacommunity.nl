<?php
/**
* @package		EasyDiscuss
* @copyright	Copyright (C) Stack Ideas Sdn Bhd. All rights reserved.
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
			<?php echo $this->html('panel.head', 'COM_ED_SETTINGS_SOCIAL_BUTTONS'); ?>

			<div class="panel-body">
				<div class="o-form-horizontal">
					<?php echo $this->html('settings.dropdown', 'social_buttons_type', 'COM_ED_SETTINGS_SOCIAL_BUTTONS_TYPE', '', ['default' => 'Default', 'addthis' => 'AddThis', 'sharethis' => 'ShareThis'], 'data-social-button-types');?>
				</div>
			</div>
		</div>

		<div class="panel <?php echo $this->config->get('social_buttons_type') !== 'default' ? 'hidden' : ''; ?>" data-social-buttons-default-wrapper>
			<?php echo $this->html('panel.head', 'COM_EASYDISCUSS_SETTINGS_SOCIALSHARE_FACEBOOK_LIKE_TITLE', '', '/docs/easydiscuss/administrators/configuration/obtaining-facebook-id'); ?>

			<div class="panel-body">
				<div class="o-form-horizontal">
					<?php echo $this->html('settings.textbox', 'integration_facebook_like_admin', 'COM_EASYDISCUSS_SETTINGS_SOCIALSHARE_FACEBOOK_ADMIN_ID'); ?>

					<div class="o-form-group">
						<div class="col-md-5 o-form-label">
							<?php echo $this->html('form.label', 'COM_EASYDISCUSS_SETTINGS_SOCIALSHARE_FACEBOOK_APP_ID'); ?>
						</div>
						<div class="col-md-7">
							<?php echo $this->html('form.textbox', 'integration_facebook_like_appid', $this->config->get('integration_facebook_like_appid')); ?>
						</div>
					</div>

					<?php echo $this->html('settings.toggle', 'integration_facebook_opengraph', 'COM_EASYDISCUSS_SETTINGS_SOCIALSHARE_FACEBOOK_LOAD_OPENGRAPH_TAGS'); ?>
					<?php echo $this->html('settings.toggle', 'integration_facebook_scripts', 'COM_EASYDISCUSS_SETTINGS_SOCIALSHARE_FACEBOOK_ENABLE_SCRIPTS'); ?>
					<?php echo $this->html('settings.toggle', 'integration_facebook_like', 'COM_EASYDISCUSS_SETTINGS_SOCIALSHARE_FACEBOOK_ENABLE_LIKES'); ?>
					<?php echo $this->html('settings.toggle', 'integration_facebook_like_send', 'COM_EASYDISCUSS_SETTINGS_SOCIALSHARE_FACEBOOK_LIKE_SHOW_SEND'); ?>
					<?php echo $this->html('settings.toggle', 'integration_facebook_like_faces', 'COM_EASYDISCUSS_SETTINGS_SOCIALSHARE_FACEBOOK_LIKE_SHOW_FACES'); ?>

					<?php echo $this->html('settings.dropdown', 'integration_facebook_like_verb', 'COM_EASYDISCUSS_SETTINGS_SOCIALSHARE_FACEBOOK_LIKE_VERB', '',
						array('like' => 'COM_EASYDISCUSS_SETTINGS_SOCIALSHARE_FACEBOOK_LIKE_VERB_LIKES', 'recommend' => 'COM_EASYDISCUSS_SETTINGS_SOCIALSHARE_FACEBOOK_LIKE_VERB_RECOMMENDS')
					);?>

					<?php echo $this->html('settings.dropdown', 'integration_facebook_like_theme', 'COM_EASYDISCUSS_SETTINGS_SOCIALSHARE_FACEBOOK_LIKE_THEMES', '',
						array('light' => 'COM_EASYDISCUSS_SETTINGS_SOCIALSHARE_FACEBOOK_LIKE_THEMES_LIGHT', 'dark' => 'COM_EASYDISCUSS_SETTINGS_SOCIALSHARE_FACEBOOK_LIKE_THEMES_DARK')
					);?>
				</div>
			</div>

		</div>
	</div>

	<div class="col-md-6">
		<div class="panel <?php echo $this->config->get('social_buttons_type') !== 'addthis' ? 'hidden' : ''; ?>" data-social-buttons-addthis-wrapper>
			<?php echo $this->html('panel.head', 'COM_ED_SETTINGS_ADDTHIS', '', '/docs/easydiscuss/administrators/integrations/addThis-integration'); ?>

			<div class="panel-body">
				<div class="o-form-horizontal">
					<?php echo $this->html('settings.textbox', 'addthis_pub_id', 'COM_ED_SETTINGS_ADDTHIS_PUB_ID', '', [], '', '', 'text-center'); ?>
					<?php echo $this->html('settings.textbox', 'inline_widget_id', 'COM_ED_SETTINGS_ADDTHIS_INLINE_WIDGET_ID', '', [], '', '', 'text-center'); ?>
					<?php echo $this->html('settings.textbox', 'floating_widget_id', 'COM_ED_SETTINGS_ADDTHIS_FLOATING_WIDGET_ID', '', [], '', '', 'text-center'); ?>
				</div>
			</div>
		</div>

		<div class="panel <?php echo $this->config->get('social_buttons_type') !== 'sharethis' ? 'hidden' : ''; ?>" data-social-buttons-sharethis-wrapper>
			<?php echo $this->html('panel.head', 'COM_ED_SETTINGS_SHARETHIS', '', '/docs/easydiscuss/administrators/integrations/shareThis-integration'); ?>

			<div class="panel-body">
				<div class="o-form-horizontal">
					<?php echo $this->html('settings.textbox', 'sharethis_prop_id', 'COM_ED_SETTINGS_SHARETHIS_PROP_ID', '', [], '', '', 'text-center'); ?>
				</div>
			</div>
		</div>

		<div class="panel <?php echo $this->config->get('social_buttons_type') !== 'default' ? 'hidden' : ''; ?>" data-social-buttons-default-wrapper>
			<?php echo $this->html('panel.head', 'COM_EASYDISCUSS_SETTINGS_SOCIAL_TWITTER_TITLE'); ?>

			<div class="panel-body">
				<div class="o-form-horizontal">
					<?php echo $this->html('settings.toggle', 'integration_twitter_button', 'COM_EASYDISCUSS_SETTINGS_SOCIALSHARE_USE_TWITTER_BUTTON'); ?>
					<?php echo $this->html('settings.toggle', 'integration_twitter_card', 'COM_ED_TWITTER_LOAD_TWITTER_CARDS'); ?>
				</div>
			</div>
		</div>

		<div class="panel <?php echo $this->config->get('social_buttons_type') !== 'default' ? 'hidden' : ''; ?>" data-social-buttons-default-wrapper>
			<?php echo $this->html('panel.head', 'COM_EASYDISCUSS_SETTINGS_SOCIAL_LINKEDIN_TITLE'); ?>

			<div class="panel-body">
				<div class="o-form-horizontal">
					<?php echo $this->html('settings.toggle', 'integration_linkedin', 'COM_EASYDISCUSS_SETTINGS_SOCIALSHARE_LINKEDIN_ENABLE_BUTTON'); ?>
				</div>
			</div>
		</div>
	</div>
</div>
