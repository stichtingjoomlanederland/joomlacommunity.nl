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
	window.resetDefaultClassNames = function()
	{
		$('#layout_css_admin').val("<?php echo $this->config->default->layout_css_admin; ?>");
		$('#layout_css_author').val("<?php echo $this->config->default->layout_css_author; ?>");
		$('#layout_css_registered').val("<?php echo $this->config->default->layout_css_registered; ?>");
		$('#layout_css_public').val("<?php echo $this->config->default->layout_css_public; ?>");
	}
});
</script>

<div class="row">
	<div class="col-md-6">
		<fieldset class="panel form-horizontal">
			<div class="panel-head"><?php echo JText::_( 'COM_KOMENTO_SETTINGS_LAYOUT_ITEM' ); ?></div>
			<div class="panel-body">
				<p><?php echo JText::_( 'COM_KOMENTO_SETTINGS_LAYOUT_ITEM_DESC' );?></p>
				<hr>
				<!-- Enable Lapsed Time -->
				<?php echo $this->renderSetting( 'COM_KOMENTO_SETTINGS_COMMENTS_USE_LAPSED_TIME', 'enable_lapsed_time' ); ?>

				<!-- Enable Avatar -->
				<?php echo $this->renderSetting( 'COM_KOMENTO_LAYOUT_AVATAR_ENABLE', 'layout_avatar_enable' ); ?>

				<!-- Enable Guest Links -->
				<?php echo $this->renderSetting( 'COM_KOMENTO_SETTINGS_GUEST_LINK_ENABLE', 'enable_guest_link' ); ?>

				<!-- Enable Permalink -->
				<?php echo $this->renderSetting( 'COM_KOMENTO_SETTINGS_PERMALINK_ENABLE', 'enable_permalink' ); ?>

				<!-- Enable Share -->
				<?php echo $this->renderSetting( 'COM_KOMENTO_SETTINGS_SHARE_ENABLE', 'enable_share' ); ?>

				<!-- Enable Comment Likes -->
				<?php echo $this->renderSetting( 'COM_KOMENTO_SETTINGS_LIKES_ENABLE', 'enable_likes' ); ?>

				<!-- Enable Comment Replies -->
				<?php echo $this->renderSetting( 'COM_KOMENTO_SETTINGS_REPLY_ENABLE', 'enable_reply' ); ?>

				<!-- Enable Comment Reporting -->
				<?php echo $this->renderSetting( 'COM_KOMENTO_SETTINGS_REPORT_ENABLE', 'enable_report' ); ?>

				<!-- Enable Comment Location -->
				<?php echo $this->renderSetting( 'COM_KOMENTO_SETTINGS_LOCATION_ENABLE', 'enable_location' ); ?>

				<!-- Enable Comment Info -->
				<?php echo $this->renderSetting( 'COM_KOMENTO_SETTINGS_INFO_ENABLE', 'enable_info' ); ?>

				<!-- Enable Syntax Highlighting -->
				<?php echo $this->renderSetting( 'COM_KOMENTO_SETTINGS_SYNTAX_HIGHLIGHTING_ENABLE', 'enable_syntax_highlighting' ); ?>

				<!-- Enable Id -->
				<?php echo $this->renderSetting( 'COM_KOMENTO_SETTINGS_ID_ENABLE', 'enable_id' ); ?>

				<!-- Enable Reply Reference -->
				<?php echo $this->renderSetting( 'COM_KOMENTO_SETTINGS_REPLY_REFERENCE_ENABLE', 'enable_reply_reference' ); ?>

				<!-- Enable Rank Bar -->
				<?php echo $this->renderSetting( 'COM_KOMENTO_SETTINGS_RANK_BAR_ENABLE', 'enable_rank_bar' ); ?>

				<!-- Enable Live Notification -->
				<?php echo $this->renderSetting( 'COM_KOMENTO_SETTINGS_ADVANCE_ENABLE_LIVE_NOTIFICATION', 'enable_live_notification' ); ?>

				<!-- Live Notification Interval -->
				<?php echo $this->renderSetting( 'COM_KOMENTO_SETTINGS_ADVANCE_LIVE_NOTIFICATION_INTERVAL', 'live_notification_interval', 'input' ); ?>
			</div>
		</fieldset>
	</div>
</div>

