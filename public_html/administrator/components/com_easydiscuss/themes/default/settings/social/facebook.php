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
			<?php echo $this->html('panel.head', 'COM_EASYDISCUSS_SETTINGS_SOCIALSHARE_FACEBOOK_LIKE_TITLE'); ?>

			<div class="panel-body">
				<div class="form-horizontal">
					<div class="form-group">
						<div class="col-md-5 control-label">
							<?php echo $this->html('form.label', 'COM_EASYDISCUSS_SETTINGS_SOCIALSHARE_FACEBOOK_ADMIN_ID'); ?>
						</div>
						<div class="col-md-7">
							<?php echo $this->html('form.textbox', 'integration_facebook_like_admin', $this->config->get('integration_facebook_like_admin')); ?>
							<a href="https://stackideas.com/docs/easydiscuss/facebook/obtaining-your-facebook-account-id.html" target="_blank" style="margin-left:5px;">
								<?php echo JText::_('COM_EASYDISCUSS_WHAT_IS_THIS'); ?>
							</a>
						</div>
					</div>

					<div class="form-group">
						<div class="col-md-5 control-label">
							<?php echo $this->html('form.label', 'COM_EASYDISCUSS_SETTINGS_SOCIALSHARE_FACEBOOK_APP_ID'); ?>
						</div>
						<div class="col-md-7">
							<?php echo $this->html('form.textbox', 'integration_facebook_like_appid', $this->config->get('integration_facebook_like_appid')); ?>
							<a href="https://stackideas.com/docs/easydiscuss/facebook/obtaining-your-facebook-application-settings.html" target="_blank" style="margin-left:5px;">
								<?php echo JText::_('COM_EASYDISCUSS_WHAT_IS_THIS'); ?>
							</a>
						</div>
					</div>

					<?php echo $this->html('settings.toggle', 'integration_facebook_opengraph', 'COM_EASYDISCUSS_SETTINGS_SOCIALSHARE_FACEBOOK_LOAD_OPENGRAPH_TAGS'); ?>
					<?php echo $this->html('settings.toggle', 'integration_facebook_scripts', 'COM_EASYDISCUSS_SETTINGS_SOCIALSHARE_FACEBOOK_ENABLE_SCRIPTS'); ?>
					<?php echo $this->html('settings.toggle', 'integration_facebook_like', 'COM_EASYDISCUSS_SETTINGS_SOCIALSHARE_FACEBOOK_ENABLE_LIKES'); ?>
					<?php echo $this->html('settings.toggle', 'integration_facebook_like_send', 'COM_EASYDISCUSS_SETTINGS_SOCIALSHARE_FACEBOOK_LIKE_SHOW_SEND'); ?>
					<?php echo $this->html('settings.toggle', 'integration_facebook_like_faces', 'COM_EASYDISCUSS_SETTINGS_SOCIALSHARE_FACEBOOK_LIKE_SHOW_FACES'); ?>

					<div class="form-group">
						<div class="col-md-5 control-label">
							<?php echo $this->html('form.label', 'COM_EASYDISCUSS_SETTINGS_SOCIALSHARE_FACEBOOK_LIKE_VERB'); ?>
						</div>
						<div class="col-md-7">
							<?php echo $this->html('form.dropdown', 'integration_facebook_like_verb',
													array('like' => 'COM_EASYDISCUSS_SETTINGS_SOCIALSHARE_FACEBOOK_LIKE_VERB_LIKES', 'recommend' => 'COM_EASYDISCUSS_SETTINGS_SOCIALSHARE_FACEBOOK_LIKE_VERB_RECOMMENDS'),
													$this->config->get('integration_facebook_like_verb')); ?>
						</div>

					</div>

					<div class="form-group">
						<div class="col-md-5 control-label">
							<?php echo $this->html('form.label', 'COM_EASYDISCUSS_SETTINGS_SOCIALSHARE_FACEBOOK_LIKE_THEMES'); ?>
						</div>
						<div class="col-md-7">
							<?php echo $this->html('form.dropdown', 'integration_facebook_like_theme',
													array('light' => 'COM_EASYDISCUSS_SETTINGS_SOCIALSHARE_FACEBOOK_LIKE_THEMES_LIGHT', 'dark' => 'COM_EASYDISCUSS_SETTINGS_SOCIALSHARE_FACEBOOK_LIKE_THEMES_DARK'),
													$this->config->get('integration_facebook_like_theme')); ?>
						</div>

					</div>

				</div>
			</div>

		</div>
	</div>

	<div class="col-md-6">
		<div class="panel">
			<?php echo $this->html('panel.head', 'COM_EASYDISCUSS_JFBCONNECT_INTEGRATIONS'); ?>

			<div class="panel-body">
				<div>
					<img width="128" align="left" src="<?php echo JURI::root();?>administrator/components/com_easydiscuss/themes/default/images/integrations/sourcecoast.png" style="margin-left: 20px;margin-right:25px; float: left;">
					
					<div class="small" style="overflow:hidden;">
						<?php echo JText::_('COM_EASYDISCUSS_JFBCONNECT_INFO');?><br /><br />
						<a target="_blank" class="btn btn-primary btn-sm t-lg-mb--lg" href="http://shareasale.com/r.cfm?b=495362&u=614082&m=46720&urllink=&afftrack=">Get JFBConnect Now!</a>
					</div>
				</div>

				<div class="form-horizontal">
					<?php echo $this->html('settings.toggle', 'integrations_jfbconnect', 'COM_EASYDISCUSS_ENABLE_JFBCONNECT'); ?>
				</div>
			</div>
		</div>

	</div>
</div>
