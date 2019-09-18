<?php
/**
* @package		EasyDiscuss
* @copyright	Copyright (C) 2010 - 2019 Stack Ideas Sdn Bhd. All rights reserved.
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
				<div class="form-horizontal">
					<?php echo $this->html('settings.toggle', 'main_qna', 'COM_EASYDISCUSS_ENABLE_QNA'); ?>

					<?php echo $this->html('settings.toggle', 'main_points', 'COM_EASYDISCUSS_MAIN_POINTS_ENABLE'); ?>

					<?php echo $this->html('settings.toggle', 'main_badges', 'COM_EASYDISCUSS_MAIN_BADGES_ENABLE'); ?>

					<?php echo $this->html('settings.toggle', 'main_likes_discussions', 'COM_EASYDISCUSS_ENABLE_LIKES_DISCUSSIONS'); ?>

					<?php echo $this->html('settings.toggle', 'main_likes_replies', 'COM_EASYDISCUSS_ENABLE_LIKES_REPLIES'); ?>

					<?php echo $this->html('settings.toggle', 'main_favorite', 'COM_EASYDISCUSS_ENABLE_FAVOURITES_DISCUSSIONS'); ?>

					<?php echo $this->html('settings.toggle', 'main_ratings', 'COM_EASYDISCUSS_ENABLE_RATINGS_DISCUSSIONS'); ?>

					<?php echo $this->html('settings.toggle', 'main_ratings_guests', 'COM_EASYDISCUSS_ENABLE_RATINGS_DISCUSSIONS_GUEST'); ?>

					<?php echo $this->html('settings.toggle', 'main_ranking', 'COM_EASYDISCUSS_ENABLE_RANKING'); ?>

					<?php echo $this->html('settings.toggle', 'main_master_tags', 'COM_EASYDISCUSS_MAIN_TAGS_ENABLE'); ?>

					<?php echo $this->html('settings.textbox', 'max_tags_allowed', 'COM_EASYDISCUSS_MAX_TAG_ALLOWED', '', array('size' => 7, 'postfix' => 'Tags'), '', 'form-control-sm text-center'); ?>

					<div class="form-group">
						<div class="col-md-5 control-label">
							<?php echo $this->html('form.label', 'COM_EASYDISCUSS_RANKING_CALCULATION'); ?>
						</div>
						<div class="col-md-7">
							<select name="main_ranking_calc_type" id="main_ranking_calc_type" class="form-control">
								<option value="posts" <?php echo ($this->config->get('main_ranking_calc_type') == 'posts') ? 'selected="selected"' : '' ?> ><?php echo JText::_('COM_EASYDISCUSS_RANKING_TYPE_POSTS'); ?></option>
								<option value="points" <?php echo ($this->config->get('main_ranking_calc_type') == 'points') ? 'selected="selected"' : '' ?>><?php echo JText::_('COM_EASYDISCUSS_RANKING_TYPE_POINTS'); ?></option>
							</select>
						</div>
					</div>

					<?php echo $this->html('settings.toggle', 'main_customfields_input', 'COM_EASYDISCUSS_SETTINGS_CUSTOMFIELDS_INPUT'); ?>

					<?php echo $this->html('settings.toggle', 'main_customfields', 'COM_EASYDISCUSS_SETTINGS_CUSTOMFIELDS'); ?>

					<?php echo $this->html('settings.toggle', 'main_report', 'COM_EASYDISCUSS_ENABLE_REPORT'); ?>

					<?php echo $this->html('settings.textbox', 'main_reportthreshold', 'COM_EASYDISCUSS_REPORT_THRESHOLD', '', array('size' => 7, 'postfix' => 'Reports'), '', 'form-control-sm text-center'); ?>

					<?php echo $this->html('settings.toggle', 'main_ban', 'COM_EASYDISCUSS_ENABLE_BAN'); ?>
				</div>
			</div>
		</div>

		<div class="panel">
			<?php echo $this->html('panel.head', 'COM_EASYDISCUSS_HITS_TRACKING'); ?>
			<div class="panel-body">
				<div class="form-horizontal">
					<?php echo $this->html('settings.toggle', 'main_hits_session', 'COM_EASYDISCUSS_SETTINGS_WORKFLOW_ENABLE_SESSION_TRACKING_HITS'); ?>
				</div>
			</div>
		</div>

		<div class="panel">
			<?php echo $this->html('panel.head', 'COM_ED_USER_DOWNLOAD'); ?>
			<div class="panel-body">
				<div class="form-horizontal">
					<?php echo $this->html('settings.toggle', 'main_userdownload', 'COM_ED_USER_ALLOW_DOWNLOAD'); ?>

					<?php echo $this->html('settings.textbox', 'main_userdownload_expiry', 'COM_ED_USER_DOWNLOAD_EXPIRY', '', array('size' => 7, 'postfix' => 'Days'), '', 'form-control-sm text-center'); ?>
				</div>
			</div>
		</div>
	</div>

	<div class="col-md-6">
		<div class="panel">
			<?php echo $this->html('panel.head', 'COM_EASYDISCUSS_SETTINGS_COMMENT'); ?>

			<div class="panel-body">
				<div class="form-horizontal">
					<?php echo $this->html('settings.toggle', 'main_commentpost', 'COM_EASYDISCUSS_ENABLE_COMMENT_POST'); ?>

					<?php echo $this->html('settings.toggle', 'main_comment', 'COM_EASYDISCUSS_ENABLE_COMMENT'); ?>

					<?php echo $this->html('settings.toggle', 'main_comment_permalink', 'COM_ED_COMMENTS_SHOW_PERMALINK'); ?>

					<div class="form-group">
						<div class="col-md-5 control-label">
							<?php echo $this->html('form.label', 'COM_ED_COMMENT_LIST_SETTING_ORDER'); ?>
						</div>
						<div class="col-md-7">
							<select name="main_comment_ordering" id="main_comment_ordering" class="form-control">
								<option value="desc" <?php echo ($this->config->get('main_comment_ordering') == 'desc') ? 'selected="selected"' : '' ?> ><?php echo JText::_('COM_ED_COMMENT_LIST_SETTING_DESC'); ?></option>
								<option value="asc" <?php echo ($this->config->get('main_comment_ordering') == 'asc') ? 'selected="selected"' : '' ?>><?php echo JText::_('COM_ED_COMMENT_LIST_SETTING_ASC'); ?></option>
							</select>
						</div>
					</div>	

					<?php echo $this->html('settings.textbox', 'main_comment_first_sight_count', 'COM_ED_COMMENT_FIRST_SIGHT_COUNT', '', array('size' => 8, 'postfix' => 'Comments'), '', 'form-control-sm text-center'); ?>				

					<?php echo $this->html('settings.toggle', 'main_comment_pagination', 'COM_EASYDISCUSS_COMMENT_PAGINATION'); ?>
					
					<?php echo $this->html('settings.textbox', 'main_comment_pagination_count', 'COM_EASYDISCUSS_COMMENT_PAGINATION_COUNT', '', array('size' => 8, 'postfix' => 'Comments'), '', 'form-control-sm text-center'); ?>
				</div>
			</div>
		</div>

		<div class="panel">
			<?php echo $this->html('panel.head', 'COM_EASYDISCUSS_VOTING'); ?>

			<div class="panel-body">
				<div class="form-horizontal">
					<?php echo $this->html('settings.toggle', 'main_allowselfvote', 'COM_EASYDISCUSS_ENABLE_SELF_POST_VOTE'); ?>

					<?php echo $this->html('settings.toggle', 'main_allowvote', 'COM_EASYDISCUSS_ENABLE_POST_VOTE'); ?>

					<?php echo $this->html('settings.toggle', 'main_allowquestionvote', 'COM_EASYDISCUSS_ENABLE_QUESTION_POST_VOTE'); ?>

					<?php echo $this->html('settings.toggle', 'main_allowguestview_whovoted', 'COM_EASYDISCUSS_ALLOW_GUEST_TO_VIEW_WHO_VOTED'); ?>

					<?php echo $this->html('settings.toggle', 'main_allowguest_vote_question', 'COM_EASYDISCUSS_ALLOW_GUEST_TO_VOTE_QUESTION'); ?>

					<?php echo $this->html('settings.toggle', 'main_allowguest_vote_reply', 'COM_EASYDISCUSS_ALLOW_GUEST_TO_VOTE_REPLY'); ?>

					<div class="form-group">
						<div class="col-md-5 control-label">
							<?php echo $this->html('form.label', 'COM_ED_VOTING_BEHAVIOR'); ?>
						</div>
						<div class="col-md-7">
							<select name="main_voting_behavior_type" id="main_voting_behavior_type" class="form-control">
								<option value="default" <?php echo ($this->config->get('main_voting_behavior_type') == 'default') ? 'selected="selected"' : '' ?> ><?php echo JText::_('COM_ED_VOTING_BEHAVIOR_TYPE_DEFAULT'); ?></option>
								<option value="contribution" <?php echo ($this->config->get('main_voting_behavior_type') == 'contribution') ? 'selected="selected"' : '' ?>><?php echo JText::_('COM_ED_VOTING_BEHAVIOR_TYPE_CONTRIBUTION'); ?></option>
							</select>

							<p class="small mt-10">
								<?php echo JText::_('COM_ED_VOTING_BEHAVIOR_TYPE_FOOTNOTE'); ?>
							</p>							
						</div>
					</div>
				</div>
			</div>
		</div>

		<div class="panel">
			<?php echo $this->html('panel.head', 'COM_EASYDISCUSS_SETTINGS_TNC'); ?>

			<div class="panel-body">
				<div class="form-horizontal">
					<?php echo $this->html('settings.toggle', 'main_tnc_question', 'COM_EASYDISCUSS_TNC_QUESTION'); ?>
					<?php echo $this->html('settings.toggle', 'main_tnc_reply', 'COM_EASYDISCUSS_TNC_REPLY'); ?>
					<?php echo $this->html('settings.toggle', 'main_tnc_comment', 'COM_EASYDISCUSS_TNC_COMMENT'); ?>
					<?php echo $this->html('settings.toggle', 'main_tnc_remember', 'COM_EASYDISCUSS_TNC_REMEMBER_SELECTION'); ?>

					<div class="form-group">
						<div class="col-md-5 control-label">
							<?php echo $this->html('form.label', 'COM_EASYDISCUSS_TNC_REMEMBER_SELECTION_TYPE'); ?>
						</div>
						<div class="col-md-7">
							<select name="main_tnc_remember_type" id="main_tnc_remember_type" class="form-control">
								<option value="global" <?php echo ($this->config->get('main_tnc_remember_type') == 'global') ? 'selected="selected"' : '' ?> ><?php echo JText::_('COM_EASYDISCUSS_TNC_REMEMBER_SELECTION_TYPE_GLOBAL'); ?></option>
								<option value="follow_type" <?php echo ($this->config->get('main_tnc_remember_type') == 'follow_type') ? 'selected="selected"' : '' ?>><?php echo JText::_('COM_EASYDISCUSS_TNC_REMEMBER_SELECTION_TYPE_FOLLOW_TYPE'); ?></option>
							</select>
						</div>
					</div>
					<div class="form-group">
						<div class="col-md-5 control-label">
							<?php echo $this->html('form.label', 'COM_EASYDISCUSS_TNC_TITLE'); ?>
						</div>
						<div class="col-md-7">
							<textarea name="main_tnctext" class="form-control" cols="65" rows="5"><?php echo str_replace('<br />', "\n", $this->config->get('main_tnctext')); ?></textarea>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
