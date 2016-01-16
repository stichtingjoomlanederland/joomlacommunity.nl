<?php
/**
* @package		Komento
* @copyright	Copyright (C) 2012 Stack Ideas Private Limited. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* Komento is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Restricted access');
?>
<div class="row-fluid">
	<div class="span12">
		<div class="span6">
			<fieldset class="adminform">
			<legend><?php echo JText::_( 'COM_KOMENTO_SETTINGS_ADVANCE' ); ?></legend>
			<p class="warning"><?php echo JText::_( 'COM_KOMENTO_SETTINGS_ADVANCE_WARNING' ); ?></p>
			<table class="admintable" cellspacing="1">
				<tbody>

					<!-- Enable Inline Reply -->
					<?php echo $this->renderSetting( 'COM_KOMENTO_SETTINGS_ADVANCE_ENABLE_INLINE_REPLY', 'enable_inline_reply' ); ?>

					<!-- Enable Ajax Permalink -->
					<?php echo $this->renderSetting( 'COM_KOMENTO_SETTINGS_ADVANCE_ENABLE_AJAX_PERMALINK', 'enable_ajax_permalink' ); ?>

					<!-- Enable Load List -->
					<?php echo $this->renderSetting( 'COM_KOMENTO_SETTINGS_ADVANCE_ENABLE_AJAX_LOAD_LIST', 'enable_ajax_load_list' ); ?>

					<!-- Enable Ajax Load Stickies -->
					<?php echo $this->renderSetting( 'COM_KOMENTO_SETTINGS_ADVANCE_ENABLE_AJAX_LOAD_STICKIES', 'enable_ajax_load_stickies' ); ?>

					<!-- Enforce Live Stickies -->
					<?php echo $this->renderSetting( 'COM_KOMENTO_SETTINGS_ADVANCE_ENFORCE_LIVE_STICKIES', 'enforce_live_stickies' ); ?>

					<!-- Enable Ajax Load Lovies -->
					<?php echo $this->renderSetting( 'COM_KOMENTO_SETTINGS_ADVANCE_ENABLE_AJAX_LOAD_LOVIES', 'enable_ajax_load_lovies' ); ?>

					<!-- Enforce Live Lovies -->
					<?php echo $this->renderSetting( 'COM_KOMENTO_SETTINGS_ADVANCE_ENFORCE_LIVE_LOVIES', 'enforce_live_lovies' ); ?>

					<!-- Enable Shorten Link -->
					<?php echo $this->renderSetting( 'COM_KOMENTO_SETTINGS_ADVANCE_ENABLE_SHORTEN_LINK', 'enable_shorten_link' ); ?>

					<!-- Enable Parent Preload -->
					<?php echo $this->renderSetting( 'COM_KOMENTO_SETTINGS_ADVANCE_ENABLE_PARENT_PRELOAD', 'parent_preload' ); ?>

					<!-- Enable Live Notification -->
					<?php echo $this->renderSetting( 'COM_KOMENTO_SETTINGS_ADVANCE_ENABLE_LIVE_NOTIFICATION', 'enable_live_notification' ); ?>

					<!-- Live Notification Interval -->
					<?php echo $this->renderSetting( 'COM_KOMENTO_SETTINGS_ADVANCE_LIVE_NOTIFICATION_INTERVAL', 'live_notification_interval', 'input' ); ?>

					<!-- Enable JS Form Validation -->
					<?php echo $this->renderSetting( 'COM_KOMENTO_SETTINGS_ADVANCE_ENABLE_JS_FORM_VALIDATION', 'enable_js_form_validation' ); ?>

					<!-- Enable Live Form Validation -->
					<?php echo $this->renderSetting( 'COM_KOMENTO_SETTINGS_ADVANCE_ENABLE_LIVE_FORM_VALIDATION', 'enable_live_form_validation' ); ?>

					<!-- Enable Admin Mode -->
					<?php echo $this->renderSetting( 'COM_KOMENTO_SETTINGS_ADVANCE_ENABLE_ADMIN_MODE', 'enable_admin_mode' ); ?>

					<!-- Enable ACL Warning Messages -->
					<?php echo $this->renderSetting( 'COM_KOMENTO_SETTINGS_ADVANCE_ENABLE_WARNING_MESSAGES', 'enable_warning_messages' ); ?>

					<!-- Enable English language as fallback language -->
					<?php echo $this->renderSetting( 'COM_KOMENTO_SETTINGS_ADVANCE_ENABLE_LANGUAGE_FALLBACK', 'enable_language_fallback' ); ?>

					<!-- Disable mailQ globally -->
					<?php echo $this->renderSetting( 'COM_KOMENTO_SETTINGS_ADVANCE_DISABLE_MAILQ', 'disable_mailq' ); ?>

				</tbody>
			</table>
			</fieldset>
		</div>

		<div class="span6">
		</div>
	</div>
</div>

