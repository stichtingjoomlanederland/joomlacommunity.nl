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
			<?php echo $this->html('panel.head', 'COM_ED_GENERAL_FEATURES'); ?>

			<div class="panel-body">
				<div class="o-form-horizontal">
					<?php echo $this->html('settings.toggle', 'main_qna', 'COM_EASYDISCUSS_ENABLE_QNA'); ?>
					<?php echo $this->html('settings.toggle', 'main_postassignment', 'COM_ED_ENABLE_POST_ASSIGNMENTS'); ?>
					<?php echo $this->html('settings.toggle', 'main_private_post', 'COM_EASYDISCUSS_SETTINGS_PRIVATE_POSTINGS'); ?>
					<?php echo $this->html('settings.toggle', 'main_anonymous_posting', 'COM_EASYDISCUSS_ENABLE_ANONYMOUS_POSTING'); ?>
					<?php echo $this->html('settings.toggle', 'post_priority', 'COM_EASYDISCUSS_ENABLE_POST_PRIORITY'); ?>
					<?php echo $this->html('settings.toggle', 'main_labels', 'COM_ED_ENABLE_POST_LABELS'); ?>
					<?php echo $this->html('settings.toggle', 'layout_post_types', 'COM_EASYDISCUSS_ENABLE_POST_TYPES'); ?>
					<?php echo $this->html('settings.toggle', 'main_password_protection', 'COM_EASYDISCUSS_SETTINGS_PASSWORD_PROTECTION'); ?>
					<?php echo $this->html('settings.toggle', 'main_points', 'COM_EASYDISCUSS_MAIN_POINTS_ENABLE'); ?>

					<?php echo $this->html('settings.toggle', 'main_badges', 'COM_EASYDISCUSS_MAIN_BADGES_ENABLE'); ?>

					<?php echo $this->html('settings.toggle', 'main_favorite', 'COM_EASYDISCUSS_ENABLE_FAVOURITES_DISCUSSIONS'); ?>

					<?php echo $this->html('settings.toggle', 'main_ban', 'COM_EASYDISCUSS_ENABLE_BAN'); ?>

					<?php echo $this->html('settings.toggle', 'main_posts_from_blockuser', 'COM_ED_SHOW_POSTS_FROM_BLOCKED_USERS'); ?>
					<?php echo $this->html('settings.toggle', 'main_login_to_read', 'COM_ED_LOGIN_TO_READ_POST'); ?>
				</div>
			</div>
		</div>

		<div class="panel">
			<?php echo $this->html('panel.head', 'COM_ED_REPORTING'); ?>

			<div class="panel-body">
				<div class="o-form-horizontal">
					<?php echo $this->html('settings.toggle', 'main_report', 'COM_EASYDISCUSS_ENABLE_REPORT'); ?>
					<?php echo $this->html('settings.textbox', 'main_reportthreshold', 'COM_EASYDISCUSS_REPORT_THRESHOLD', '', array('size' => 7, 'postfix' => 'Reports'), '', '', 'text-center'); ?>
				</div>
			</div>
		</div>

	</div>

	<div class="col-md-6">
		<div class="panel">
			<?php echo $this->html('panel.head', 'COM_ED_TAGGING'); ?>

			<div class="panel-body">
				<div class="o-form-horizontal">
					<?php echo $this->html('settings.toggle', 'main_master_tags', 'COM_EASYDISCUSS_MAIN_TAGS_ENABLE'); ?>

					<?php echo $this->html('settings.textbox', 'max_tags_allowed', 'COM_EASYDISCUSS_MAX_TAG_ALLOWED', '', array('size' => 7, 'postfix' => 'Tags'), '', '', 'text-center'); ?>
				</div>
			</div>
		</div>
		
		<div class="panel">
			<?php echo $this->html('panel.head', 'COM_ED_RATINGS'); ?>

			<div class="panel-body">
				<div class="o-form-horizontal">
					<?php echo $this->html('settings.toggle', 'main_ratings', 'COM_EASYDISCUSS_ENABLE_RATINGS_DISCUSSIONS'); ?>

					<?php echo $this->html('settings.toggle', 'main_ratings_guests', 'COM_EASYDISCUSS_ENABLE_RATINGS_DISCUSSIONS_GUEST'); ?>
				</div>
			</div>
		</div>

		<div class="panel">
			<?php echo $this->html('panel.head', 'COM_ED_CUSTOM_FIELDS'); ?>

			<div class="panel-body">
				<div class="o-form-horizontal">
					<?php echo $this->html('settings.toggle', 'main_customfields_input', 'COM_EASYDISCUSS_SETTINGS_CUSTOMFIELDS_INPUT'); ?>

					<?php echo $this->html('settings.toggle', 'main_customfields', 'COM_EASYDISCUSS_SETTINGS_CUSTOMFIELDS'); ?>
				</div>
			</div>
		</div>

		<div class="panel">
			<?php echo $this->html('panel.head', 'COM_ED_LIKES'); ?>

			<div class="panel-body">
				<div class="o-form-horizontal">
					<?php echo $this->html('settings.toggle', 'main_likes_discussions', 'COM_EASYDISCUSS_ENABLE_LIKES_DISCUSSIONS'); ?>

					<?php echo $this->html('settings.toggle', 'main_likes_replies', 'COM_EASYDISCUSS_ENABLE_LIKES_REPLIES'); ?>
				</div>
			</div>
		</div>
	</div>
</div>
