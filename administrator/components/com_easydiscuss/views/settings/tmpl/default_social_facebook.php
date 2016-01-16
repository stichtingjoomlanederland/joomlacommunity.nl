<?php
/**
* @package      EasyDiscuss
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
		<h2><?php echo JText::_( 'COM_EASYDISCUSS_SETTINGS_SOCIAL_FACEBOOK_TITLE' );?></h2>
		<p style="margin: 0 0 15px;">
			<?php echo JText::_( 'COM_EASYDISCUSS_SETTINGS_SOCIAL_FACEBOOK_DESC' );?>
		</p>
	</div>
</div>

<div class="row-fluid ">
	<div class="span6">
		<div class="widget accordion-group">
			<div class="whead accordion-heading">
				<a href="javascript:void(0);" data-foundry-toggle="collapse" data-target="#option01">
				<h6><?php echo JText::_( 'COM_EASYDISCUSS_SETTINGS_SOCIALSHARE_FACEBOOK_LIKE_TITLE' ); ?></h6>
				<i class="icon-chevron-down"></i>
				</a>
			</div>

			<div id="option01" class="accordion-body collapse in">
				<div class="wbody">
					<div class="si-form-row">
						<div class="span5 form-row-label">
							<label>
								<?php echo JText::_( 'COM_EASYDISCUSS_SETTINGS_SOCIALSHARE_FACEBOOK_ADMIN_ID' ); ?>
							</label>
						</div>
						<div class="span7"
							rel="ed-popover"
							data-placement="left"
							data-original-title="<?php echo JText::_( 'COM_EASYDISCUSS_SETTINGS_SOCIALSHARE_FACEBOOK_ADMIN_ID' ); ?>"
							data-content="<?php echo JText::_('COM_EASYDISCUSS_SETTINGS_SOCIALSHARE_FACEBOOK_ADMIN_ID_DESC'); ?>"
						>
							<input type="text" name="integration_facebook_like_admin" class="full-width" value="<?php echo $this->config->get('integration_facebook_like_admin');?>" size="40" />
							<a href="http://stackideas.com/docs/easydiscuss/facebook/obtaining-your-facebook-account-id.html" target="_blank" style="margin-left:5px;"><?php echo JText::_('COM_EASYDISCUSS_WHAT_IS_THIS'); ?></a>
						</div>
					</div>
					<div class="si-form-row">
						<div class="span5 form-row-label">
							<label>
								<?php echo JText::_( 'COM_EASYDISCUSS_SETTINGS_SOCIALSHARE_FACEBOOK_APP_ID' ); ?>
							</label>
						</div>
						<div class="span7"
							rel="ed-popover"
							data-placement="left"
							data-original-title="<?php echo JText::_( 'COM_EASYDISCUSS_SETTINGS_SOCIALSHARE_FACEBOOK_APP_ID' ); ?>"
							data-content="<?php echo JText::_('COM_EASYDISCUSS_SETTINGS_SOCIALSHARE_FACEBOOK_APP_ID_DESC'); ?>"
						>
							<input type="text" name="integration_facebook_like_appid" class="full-width" value="<?php echo $this->config->get('integration_facebook_like_appid');?>" size="40" />
							<a href="http://stackideas.com/docs/easydiscuss/facebook/obtaining-your-facebook-application-settings.html" target="_blank" style="margin-left:5px;"><?php echo JText::_('COM_EASYDISCUSS_WHAT_IS_THIS'); ?></a>
						</div>
					</div>
					<div class="si-form-row">
						<div class="span5 form-row-label">
							<label>
								<?php echo JText::_( 'COM_EASYDISCUSS_SETTINGS_SOCIALSHARE_FACEBOOK_ENABLE_SCRIPTS' ); ?>
							</label>
						</div>
						<div class="span7"
							rel="ed-popover"
							data-placement="left"
							data-original-title="<?php echo JText::_( 'COM_EASYDISCUSS_SETTINGS_SOCIALSHARE_FACEBOOK_ENABLE_SCRIPTS' ); ?>"
							data-content="<?php echo JText::_('COM_EASYDISCUSS_SETTINGS_SOCIALSHARE_FACEBOOK_ENABLE_SCRIPTS_DESC'); ?>"
						>
							<?php echo $this->renderCheckbox( 'integration_facebook_scripts' , $this->config->get( 'integration_facebook_scripts' ) );?>
						</div>
					</div>
					<div class="si-form-row">
						<div class="span5 form-row-label">
							<label>
								<?php echo JText::_( 'COM_EASYDISCUSS_SETTINGS_SOCIALSHARE_FACEBOOK_ENABLE_LIKES' ); ?>
							</label>
						</div>
						<div class="span7"
							rel="ed-popover"
							data-placement="left"
							data-original-title="<?php echo JText::_( 'COM_EASYDISCUSS_SETTINGS_SOCIALSHARE_FACEBOOK_ENABLE_LIKES' ); ?>"
							data-content="<?php echo JText::_('COM_EASYDISCUSS_SETTINGS_SOCIALSHARE_FACEBOOK_ENABLE_LIKES_DESC'); ?>"
						>
							<?php echo $this->renderCheckbox( 'integration_facebook_like' , $this->config->get( 'integration_facebook_like' ) );?>
						</div>
					</div>
					<div class="si-form-row">
						<div class="span5 form-row-label">
							<label>
								<?php echo JText::_( 'COM_EASYDISCUSS_SETTINGS_SOCIALSHARE_FACEBOOK_LIKE_SHOW_SEND' ); ?>
							</label>
						</div>
						<div class="span7"
							rel="ed-popover"
							data-placement="left"
							data-original-title="<?php echo JText::_( 'COM_EASYDISCUSS_SETTINGS_SOCIALSHARE_FACEBOOK_LIKE_SHOW_SEND' ); ?>"
							data-content="<?php echo JText::_('COM_EASYDISCUSS_SETTINGS_SOCIALSHARE_FACEBOOK_LIKE_SHOW_SEND_DESC'); ?>"
						>
							<?php echo $this->renderCheckbox( 'integration_facebook_like_send' , $this->config->get( 'integration_facebook_like_send' ) );?>
						</div>
					</div>
					<div class="si-form-row">
						<div class="span5 form-row-label">
							<label>
								<?php echo JText::_( 'COM_EASYDISCUSS_SETTINGS_SOCIALSHARE_FACEBOOK_LIKE_SHOW_FACES' ); ?>
							</label>
						</div>
						<div class="span7"
							rel="ed-popover"
							data-placement="left"
							data-original-title="<?php echo JText::_( 'COM_EASYDISCUSS_SETTINGS_SOCIALSHARE_FACEBOOK_LIKE_SHOW_FACES' ); ?>"
							data-content="<?php echo JText::_('COM_EASYDISCUSS_SETTINGS_SOCIALSHARE_FACEBOOK_LIKE_SHOW_FACES_DESC'); ?>"
						>
							<?php echo $this->renderCheckbox( 'integration_facebook_like_faces' , $this->config->get( 'integration_facebook_like_faces' ) );?>
						</div>
					</div>

					<div class="si-form-row">
						<div class="span5 form-row-label">
							<label>
								<?php echo JText::_( 'COM_EASYDISCUSS_SETTINGS_SOCIALSHARE_FACEBOOK_LIKE_VERB' ); ?>
							</label>
						</div>
						<div class="span7"
							rel="ed-popover"
							data-placement="left"
							data-original-title="<?php echo JText::_( 'COM_EASYDISCUSS_SETTINGS_SOCIALSHARE_FACEBOOK_LIKE_VERB' ); ?>"
							data-content="<?php echo JText::_('COM_EASYDISCUSS_SETTINGS_SOCIALSHARE_FACEBOOK_LIKE_VERB_DESC'); ?>"
						>
							<select name="integration_facebook_like_verb" class="full-width" >
								<option<?php echo $this->config->get( 'integration_facebook_like_verb' ) == 'like' ? ' selected="selected"' : ''; ?> value="like"><?php echo JText::_('COM_EASYDISCUSS_SETTINGS_SOCIALSHARE_FACEBOOK_LIKE_VERB_LIKES');?></option>
								<option<?php echo $this->config->get( 'integration_facebook_like_verb' ) == 'recommend' ? ' selected="selected"' : ''; ?> value="recommend"><?php echo JText::_('COM_EASYDISCUSS_SETTINGS_SOCIALSHARE_FACEBOOK_LIKE_VERB_RECOMMENDS');?></option>
							</select>
						</div>
					</div>
					<div class="si-form-row">
						<div class="span5 form-row-label">
							<label>
								<?php echo JText::_( 'COM_EASYDISCUSS_SETTINGS_SOCIALSHARE_FACEBOOK_LIKE_THEMES' ); ?>
							</label>
						</div>
						<div class="span7"
							rel="ed-popover"
							data-placement="left"
							data-original-title="<?php echo JText::_( 'COM_EASYDISCUSS_SETTINGS_SOCIALSHARE_FACEBOOK_LIKE_THEMES' ); ?>"
							data-content="<?php echo JText::_('COM_EASYDISCUSS_SETTINGS_SOCIALSHARE_FACEBOOK_LIKE_THEMES_DESC'); ?>"
						>
							<select name="integration_facebook_like_theme" class="full-width">
								<option<?php echo $this->config->get( 'integration_facebook_like_theme' ) == 'light' ? ' selected="selected"' : ''; ?> value="light"><?php echo JText::_('COM_EASYDISCUSS_SETTINGS_SOCIALSHARE_FACEBOOK_LIKE_THEMES_LIGHT');?></option>
								<option<?php echo $this->config->get( 'integration_facebook_like_theme' ) == 'dark' ? ' selected="selected"' : ''; ?> value="dark"><?php echo JText::_('COM_EASYDISCUSS_SETTINGS_SOCIALSHARE_FACEBOOK_LIKE_THEMES_DARK');?></option>
							</select>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
	<div class="span6">

	</div>
</div>
