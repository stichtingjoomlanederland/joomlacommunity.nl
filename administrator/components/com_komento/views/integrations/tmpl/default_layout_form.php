<?php
/**
* @package      Komento
* @copyright    Copyright (C) 2010 - 2015 Stack Ideas Sdn Bhd. All rights reserved.
* @license      GNU/GPL, see LICENSE.php
* Komento is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Restricted access'); ?>
<script type="text/javascript">
Komento.ready(function($) {
	window.resetDefaultEmailRegex = function()
	{
		$('#email_regex').val(decodeURIComponent("<?php echo $this->config->default->email_regex[0]; ?>"));
	}

	window.resetDefaultWebsiteRegex = function()
	{
		$('#website_regex').val(decodeURIComponent("<?php echo $this->config->default->website_regex[0]; ?>"));
	}
});
</script>
<div class="row">
	<div class="col-md-6">
		<fieldset class="panel form-horizontal">
			<div class="panel-head"><?php echo JText::_( 'COM_KOMENTO_SETTINGS_FORM' ); ?></div>
			<div class="panel-body">
				<!-- Form position -->
				<?php $options = array();
					$options[] = array( '0', 'COM_KOMENTO_SETTINGS_FORM_POSITION_BEFORE' );
					$options[] = array( '1', 'COM_KOMENTO_SETTINGS_FORM_POSITION_AFTER' );
					echo $this->renderSetting( 'COM_KOMENTO_SETTINGS_FORM_POSITION', 'form_position', 'dropdown', $options );
				?>

				<!-- Form Toggle Button -->
				<?php echo $this->renderSetting( 'COM_KOMENTO_SETTINGS_FORM_TOGGLE_BUTTON', 'form_toggle_button' ); ?>

				<!-- Autohide Form Notification -->
				<?php echo $this->renderSetting( 'COM_KOMENTO_SETTINGS_AUTOHIDE_FORM_NOTIFICATION', 'autohide_form_notification' ); ?>

				<!-- Show moderation warning message -->
				<?php echo $this->renderSetting( 'COM_KOMENTO_SETTINGS_FORM_SHOW_MODERATE_MESSAGE', 'form_show_moderate_message' ); ?>

				<!-- Scroll to comment upon post -->
				<?php echo $this->renderSetting( 'COM_KOMENTO_SETTINGS_SCROLL_TO_COMMENT', 'scroll_to_comment' ); ?>

				<!-- Enable Inline Reply -->
				<?php // echo $this->renderSetting( 'COM_KOMENTO_SETTINGS_INLINE_REPLY_ENABLE', 'enable_inline_reply' ); ?>

				<!-- Enable Location -->
				<?php echo $this->renderSetting( 'COM_KOMENTO_SETTINGS_SHOW_LOCATION', 'show_location' ); ?>

				<!-- Enable BB Code -->
				<?php echo $this->renderSetting( 'COM_KOMENTO_SETTINGS_BBCODE_ENABLE', 'enable_bbcode' ); ?>

				<!-- Enable Subscription -->
				<?php echo $this->renderSetting( 'COM_KOMENTO_SETTINGS_SUBSCRIPTION_ENABLE', 'enable_subscription' ); ?>

				<!-- Enable TnC -->
				<?php // echo $this->renderSetting( 'COM_KOMENTO_SETTINGS_TNC_ENABLE', 'show_tnc' ); ?>
				<?php
					$usergroups = $this->getUsergroupsMultilist();
					echo $this->renderSetting( 'COM_KOMENTO_SETTINGS_TNC_ENABLE', 'show_tnc', 'multilist', $usergroups );
				?>

				<!-- TnC Text -->
				<div class="form-group">
					<label class="control-label col-md-5">
						<?php echo JText::_( 'COM_KOMENTO_SETTINGS_TNC_TEXT' ); ?>
					</label>
					<div class="col-md-7">
						<div class="has-tip">
							<div class="tip"><i></i><?php echo JText::_( 'COM_KOMENTO_SETTINGS_TNC_TEXT_DESC' ); ?></div>
							<textarea name="tnc_text" class="inputbox full-width" cols="25" rows="15"><?php echo str_replace('<br />', "\n", $this->config->get('tnc_text' )); ?></textarea>
						</div>
					</div>
				</div>
			</div>
		</fieldset>
	</div>

	<div class="col-md-6">
		<fieldset class="panel form-horizontal">
			<div class="panel-head"><?php echo JText::_( 'COM_KOMENTO_SETTINGS_FORM_FIELDS' ); ?></div>
			<div class="panel-body">
				<!-- Show Name -->
				<?php $options = array();
					$options[] = array( '0', 'COM_KOMENTO_SETTINGS_SHOW_FIELD_OFF' );
					$options[] = array( '1', 'COM_KOMENTO_SETTINGS_SHOW_FIELD_GUEST' );
					$options[] = array( '2', 'COM_KOMENTO_SETTINGS_SHOW_FIELD_ALL' );
					echo $this->renderSetting( 'COM_KOMENTO_SETTINGS_SHOW_NAME', 'show_name', 'dropdown', $options );
				?>

				<!-- Show Email -->
				<?php $options = array();
					$options[] = array( '0', 'COM_KOMENTO_SETTINGS_SHOW_FIELD_OFF' );
					$options[] = array( '1', 'COM_KOMENTO_SETTINGS_SHOW_FIELD_GUEST' );
					$options[] = array( '2', 'COM_KOMENTO_SETTINGS_SHOW_FIELD_ALL' );
					echo $this->renderSetting( 'COM_KOMENTO_SETTINGS_SHOW_EMAIL', 'show_email', 'dropdown', $options );
				?>

				<!-- Show Website -->
				<?php $options = array();
					$options[] = array( '0', 'COM_KOMENTO_SETTINGS_SHOW_FIELD_OFF' );
					$options[] = array( '1', 'COM_KOMENTO_SETTINGS_SHOW_FIELD_GUEST' );
					$options[] = array( '2', 'COM_KOMENTO_SETTINGS_SHOW_FIELD_ALL' );
					echo $this->renderSetting( 'COM_KOMENTO_SETTINGS_SHOW_WEBSITE', 'show_website', 'dropdown', $options );
				?>
			</div>
		</fieldset>

		<fieldset class="panel form-horizontal">
			<div class="panel-head"><?php echo JText::_( 'COM_KOMENTO_SETTINGS_FORM_REQUIREMENTS' ); ?></div>
			<div class="panel-body">
				<!-- Require Name -->
				<?php $options = array();
					$options[] = array( '0', 'COM_KOMENTO_SETTINGS_REQUIRE_FIELD_OFF' );
					$options[] = array( '1', 'COM_KOMENTO_SETTINGS_REQUIRE_FIELD_GUEST' );
					$options[] = array( '2', 'COM_KOMENTO_SETTINGS_REQUIRE_FIELD_ALL' );
					echo $this->renderSetting( 'COM_KOMENTO_SETTINGS_REQUIRE_NAME', 'require_name', 'dropdown', $options );
				?>

				<!-- Require Email -->
				<?php $options = array();
					$options[] = array( '0', 'COM_KOMENTO_SETTINGS_REQUIRE_FIELD_OFF' );
					$options[] = array( '1', 'COM_KOMENTO_SETTINGS_REQUIRE_FIELD_GUEST' );
					$options[] = array( '2', 'COM_KOMENTO_SETTINGS_REQUIRE_FIELD_ALL' );
					echo $this->renderSetting( 'COM_KOMENTO_SETTINGS_REQUIRE_EMAIL', 'require_email', 'dropdown', $options );
				?>

				<!-- Require Website -->
				<?php $options = array();
					$options[] = array( '0', 'COM_KOMENTO_SETTINGS_REQUIRE_FIELD_OFF' );
					$options[] = array( '1', 'COM_KOMENTO_SETTINGS_REQUIRE_FIELD_GUEST' );
					$options[] = array( '2', 'COM_KOMENTO_SETTINGS_REQUIRE_FIELD_ALL' );
					echo $this->renderSetting( 'COM_KOMENTO_SETTINGS_REQUIRE_WEBSITE', 'require_website', 'dropdown', $options );
				?>
			</div>
		</fieldset>

		<fieldset class="panel form-horizontal">
			<div class="panel-head"><?php echo JText::_( 'COM_KOMENTO_SETTINGS_FIELD_VALIDATION' ); ?></div>
			<div class="panel-body">
				<!-- Enable Email Regex -->
				<?php echo $this->renderSetting( 'COM_KOMENTO_SETTINGS_ENABLE_EMAIL_REGEX', 'enable_email_regex' ); ?>

				<!-- Email Regex Pattern -->
				<?php echo $this->renderSetting( 'COM_KOMENTO_SETTINGS_EMAIL_REGEX', 'email_regex', 'input', '50' ); ?>
				<div class="form-group">
					<div class="col-md-7 col-md-offset-5">
						<a href="javascript:void(0);" onclick="resetDefaultEmailRegex()"><?php echo JText::_( 'COM_KOMENTO_SETTINGS_RESET_TO_DEFAULT_REGEX' ); ?></a>
					</div>
				</div>

				<!-- Enable Website Regex -->
				<?php echo $this->renderSetting( 'COM_KOMENTO_SETTINGS_ENABLE_WEBSITE_REGEX', 'enable_website_regex' ); ?>

				<!-- Website Regex Pattern -->
				<?php echo $this->renderSetting( 'COM_KOMENTO_SETTINGS_WEBSITE_REGEX', 'website_regex', 'input', '50' ); ?>
				<div class="form-group">
					<div class="col-md-7 col-md-offset-5">
						<a href="javascript:void(0);" onclick="resetDefaultWebsiteRegex()"><?php echo JText::_( 'COM_KOMENTO_SETTINGS_RESET_TO_DEFAULT_REGEX' ); ?></a>
					</div>
				</div>
			</div>
		</fieldset>
	</div>
</div>
