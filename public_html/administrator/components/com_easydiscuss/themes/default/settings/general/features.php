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
defined('_JEXEC') or die('Restricted access');
?>
<div class="row">
	<div class="col-md-6">
		<div class="panel">
			<?php echo $this->html('panel.head', 'COM_EASYDISCUSS_BANS'); ?>
			
			<div class="panel-body">
				<div class="form-horizontal">
					<div class="form-group">
						<div class="col-md-5 control-label">
							<?php echo $this->html('form.label', 'COM_EASYDISCUSS_ENABLE_BAN'); ?>
						</div>
						<div class="col-md-7">
							<?php echo $this->html('form.boolean', 'main_ban', $this->config->get('main_ban')); ?>
						</div>
					</div>
				</div>
			</div>
		</div>

		<div class="panel">
			<?php echo $this->html('panel.head', 'COM_EASYDISCUSS_REPORTS'); ?>
			
			<div class="panel-body">
				<div class="form-horizontal">
					<div class="form-group">
						<div class="col-md-5 control-label">
							<?php echo $this->html('form.label', 'COM_EASYDISCUSS_ENABLE_REPORT'); ?>
						</div>
						<div class="col-md-7">
							<?php echo $this->html('form.boolean', 'main_report', $this->config->get('main_report')); ?>
						</div>
					</div>
					<div class="form-group">
						<div class="col-md-5 control-label">
							<?php echo $this->html('form.label', 'COM_EASYDISCUSS_REPORT_THRESHOLD'); ?>
						</div>
						<div class="col-md-7">
							<input type="text" name="main_reportthreshold" class="form-control form-control-sm text-center" value="<?php echo $this->config->get('main_reportthreshold' , '0' );?>" />
						</div>
					</div>
				</div>
			</div>
		</div>
		
		<div class="panel">
			<?php echo $this->html('panel.head', 'COM_EASYDISCUSS_SETTINGS_QNA'); ?>

			<div class="panel-body">
				<div class="form-horizontal">
					<div class="form-group">
						<div class="col-md-5 control-label">
							<?php echo $this->html('form.label', 'COM_EASYDISCUSS_ENABLE_QNA'); ?>
						</div>
						<div class="col-md-7">
							<?php echo $this->html('form.boolean', 'main_qna', $this->config->get('main_qna')); ?>
						</div>
					</div>
				</div>
			</div>
		</div>

		<div class="panel">
			<?php echo $this->html('panel.head', 'COM_EASYDISCUSS_SETTINGS_TAGS'); ?>

			<div class="panel-body">
				<div class="form-horizontal">
					<div class="form-group">
						<div class="col-md-5 control-label">
							<?php echo $this->html('form.label', 'COM_EASYDISCUSS_MAIN_TAGS_ENABLE'); ?>
						</div>
						<div class="col-md-7">
							<?php echo $this->html('form.boolean', 'main_master_tags', $this->config->get('main_master_tags')); ?>
						</div>
					</div>
					<div class="form-group">
						<div class="col-md-5 control-label">
							<?php echo $this->html('form.label', 'COM_EASYDISCUSS_MAX_TAG_ALLOWED'); ?>
						</div>
						<div class="col-md-7">
							<input type="text" name="max_tags_allowed" class="form-control form-control-sm text-center" value="<?php echo $this->config->get('max_tags_allowed');?>" />
						</div>
					</div>
				</div>
			</div>
		</div>

		<div class="panel">
			<?php echo $this->html('panel.head', 'COM_EASYDISCUSS_RANKING'); ?>
			
			<div id="option11" class="panel-body">
				<div class="form-horizontal">
					<div class="form-group">
						<div class="col-md-5 control-label">
							<?php echo $this->html('form.label', 'COM_EASYDISCUSS_ENABLE_RANKING'); ?>
						</div>
						<div class="col-md-7">
							<?php echo $this->html('form.boolean', 'main_ranking', $this->config->get('main_ranking')); ?>
						</div>
					</div>
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
				</div>
			</div>
		</div>

		<div class="panel">
			<?php echo $this->html('panel.head', 'COM_EASYDISCUSS_SETTINGS_TNC'); ?>

			<div class="panel-body">
				<div class="form-horizontal">
					<div class="form-group">
						<div class="col-md-5 control-label">
							<?php echo $this->html('form.label', 'COM_EASYDISCUSS_TNC_QUESTION'); ?>
						</div>
						<div class="col-md-7">
							<?php echo $this->html('form.boolean', 'main_tnc_question', $this->config->get('main_tnc_question')); ?>
						</div>
					</div>
					<div class="form-group">
						<div class="col-md-5 control-label">
							<?php echo $this->html('form.label', 'COM_EASYDISCUSS_TNC_REPLY'); ?>
						</div>
						<div class="col-md-7">
							<?php echo $this->html('form.boolean', 'main_tnc_reply', $this->config->get('main_tnc_reply')); ?>
						</div>
					</div>
					<div class="form-group">
						<div class="col-md-5 control-label">
							<?php echo $this->html('form.label', 'COM_EASYDISCUSS_TNC_COMMENT'); ?>
						</div>
						<div class="col-md-7">
							<?php echo $this->html('form.boolean', 'main_tnc_comment', $this->config->get('main_tnc_comment')); ?>
						</div>
					</div>										
					<div class="form-group">
						<div class="col-md-5 control-label">
							<?php echo $this->html('form.label', 'COM_EASYDISCUSS_TNC_REMEMBER_SELECTION'); ?>
						</div>
						<div class="col-md-7">
							<?php echo $this->html('form.boolean', 'main_tnc_remember', $this->config->get('main_tnc_remember')); ?>
						</div>
					</div>
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
							<textarea name="main_tnctext" class="form-control" cols="65" rows="15"><?php echo str_replace('<br />', "\n", $this->config->get('main_tnctext')); ?></textarea>
						</div>
					</div>
				</div>
			</div>
		</div>		
		
	</div>

	<div class="col-md-6">
		<div class="panel">
			<?php echo $this->html('panel.head', 'COM_EASYDISCUSS_HITS_TRACKING'); ?>
			<div class="panel-body">
				<div class="form-horizontal">
					<div class="form-group">
						<div class="col-md-5 control-label">
							<?php echo $this->html('form.label', 'COM_EASYDISCUSS_SETTINGS_WORKFLOW_ENABLE_SESSION_TRACKING_HITS'); ?>
						</div>
						<div class="col-md-7">
							<?php echo $this->html('form.boolean', 'main_hits_session', $this->config->get('main_hits_session')); ?>
						</div>
					</div>
				</div>
			</div>
		</div>

		<div class="panel">
			<?php echo $this->html('panel.head', 'COM_EASYDISCUSS_SETTINGS_BADGES'); ?>

			<div class="panel-body">
				<div class="form-horizontal">
					<div class="form-group">
						<div class="col-md-5 control-label">
							<?php echo $this->html('form.label', 'COM_EASYDISCUSS_MAIN_BADGES_ENABLE'); ?>
						</div>
						<div class="col-md-7">
							<?php echo $this->html('form.boolean', 'main_badges', $this->config->get('main_badges'));?>
						</div>
					</div>
				</div>
			</div>
		</div>

		<div class="panel">
			<?php echo $this->html('panel.head', 'COM_EASYDISCUSS_SETTINGS_POINTS'); ?>

			<div class="panel-body">
				<div class="form-horizontal">
					<div class="form-group">
						<div class="col-md-5 control-label">
							<?php echo $this->html('form.label', 'COM_EASYDISCUSS_MAIN_POINTS_ENABLE'); ?>
						</div>
						<div class="col-md-7">
							<?php echo $this->html('form.boolean', 'main_points', $this->config->get('main_points'));?>
						</div>
					</div>
				</div>
			</div>
		</div>		

		<div class="panel">
			<?php echo $this->html('panel.head', 'COM_EASYDISCUSS_LIKES'); ?>

			<div id="option09" class="panel-body">
				<div class="form-horizontal">
					<div class="form-group">
						<div class="col-md-5 control-label">
							<?php echo $this->html('form.label', 'COM_EASYDISCUSS_ENABLE_LIKES_DISCUSSIONS'); ?>
						</div>
						<div class="col-md-7">
							<?php echo $this->html('form.boolean', 'main_likes_discussions', $this->config->get('main_likes_discussions')); ?>
						</div>
					</div>
					<div class="form-group">
						<div class="col-md-5 control-label">
							<?php echo $this->html('form.label', 'COM_EASYDISCUSS_ENABLE_LIKES_REPLIES'); ?>
						</div>
						<div class="col-md-7">
							<?php echo $this->html('form.boolean', 'main_likes_replies', $this->config->get('main_likes_replies')); ?>
						</div>
					</div>
				</div>
			</div>
		</div>

		<div class="panel">
			<?php echo $this->html('panel.head', 'COM_EASYDISCUSS_FAVOURITES'); ?>

			<div id="option10" class="panel-body">
				<div class="form-horizontal">
					<div class="form-group">
						<div class="col-md-5 control-label">
							<?php echo $this->html('form.label', 'COM_EASYDISCUSS_ENABLE_FAVOURITES_DISCUSSIONS'); ?>
						</div>
						<div class="col-md-7">
							<?php echo $this->html('form.boolean', 'main_favorite', $this->config->get('main_favorite')); ?>
						</div>
					</div>
				</div>
			</div>
		</div>

		<div class="panel">
			<?php echo $this->html('panel.head', 'COM_EASYDISCUSS_RATINGS'); ?>

			<div id="option10" class="panel-body">
				<div class="form-horizontal">
					<div class="form-group">
						<div class="col-md-5 control-label">
							<?php echo $this->html('form.label', 'COM_EASYDISCUSS_ENABLE_RATINGS_DISCUSSIONS'); ?>
						</div>
						<div class="col-md-7">
							<?php echo $this->html('form.boolean', 'main_ratings', $this->config->get('main_ratings')); ?>
						</div>
					</div>
					<div class="form-group">
						<div class="col-md-5 control-label">
							<?php echo $this->html('form.label', 'COM_EASYDISCUSS_ENABLE_RATINGS_DISCUSSIONS_GUEST'); ?>
						</div>
						<div class="col-md-7">
							<?php echo $this->html('form.boolean', 'main_ratings_guests', $this->config->get('main_ratings_guests')); ?>
						</div>
					</div>					
				</div>
			</div>
		</div>		

		<div class="panel">
			<?php echo $this->html('panel.head', 'COM_EASYDISCUSS_SETTINGS_MAIN_WORK_SCHEDULE'); ?>

			<div class="panel-body">
				<div class="form-horizontal">
					<div class="form-group">
						<div class="col-md-5 control-label">
							<?php echo $this->html('form.label', 'COM_EASYDISCUSS_ENABLE_WORK_SCHECULE'); ?>
						</div>
						<div class="col-md-7">
							<?php echo $this->html('form.boolean', 'main_work_schedule', $this->config->get('main_work_schedule')); ?>
						</div>
					</div>
					<div class="form-group">
						<div class="col-md-5 control-label">
							<?php echo $this->html('form.label', 'COM_EASYDISCUSS_ENABLE_WORK_DAYS'); ?>
						</div>
						<div class="col-md-7">
						<?php
							$days = array('mon', 'tue', 'wed', 'thu', 'fri', 'sat', 'sun');
						?>
						<?php foreach($days as $dd) { ?>
							<div class="o-checkbox">
								<input type="checkbox" id="item-checkbox-<?php echo $dd; ?>" name="main_work_<?php echo $dd; ?>" value="1"<?php echo $this->config->get('main_work_' . $dd, 0) ? ' checked="true"' : '' ?> />
								<label for="item-checkbox-<?php echo $dd; ?>">
									<?php echo JText::_('COM_EASYDISCUSS_WORK_' . strtoupper($dd)); ?>
								</label>
							</div>
						<?php } ?>
						</div>
					</div>
					<div class="form-group">
						<div class="col-md-5 control-label">
							<?php echo $this->html('form.label', 'COM_EASYDISCUSS_ENABLE_WORK_HOURS'); ?>
						</div>

						<?php
							$hours = array();
							$minutes = array();
							for ($i = 0; $i <= 23; $i++) {
								$hours[$i] = str_pad($i, 2, '0', STR_PAD_LEFT);
							}

							for ($i = 0; $i <= 59; $i++) {
								$minutes[$i] = str_pad($i, 2, '0', STR_PAD_LEFT);
							}
						?>

						<div class="col-md-7">
							<div class="o-flag t-lg-mb--md">
								<div class="o-flag__image">
									<div style="width: 40px">
										<label for="start-hours"><?php echo JText::_('COM_EASYDISCUSS_WORK_FROM'); ?></label>
									</div>
								</div>
								<div class="o-flag__body">
									<select name="main_work_starthour" class="form-control" id="start-hours" style="width:auto;display: inline-block;">
										<?php foreach($hours as $hh => $hlabel) { ?>
										<option value="<?php echo $hlabel; ?>"<?php echo ($this->config->get('main_work_starthour') == $hlabel) ? ' selected="true"' : ''; ?>><?php echo $hlabel; ?></option>
										<?php } ?>
									</select>

									<select name="main_work_startminute" class="form-control" id="start-minutes" style="width:auto;display: inline-block;">
										<?php foreach($minutes as $mm => $mlabel) { ?>
										<option value="<?php echo $mlabel; ?>"<?php echo ($this->config->get('main_work_startminute') == $mlabel) ? ' selected="true"' : ''; ?>><?php echo $mlabel; ?></option>
										<?php } ?>
									</select>
								</div>
							</div>

							<div class="o-flag t-lg-mb--md">
								<div class="o-flag__image">
									<div style="width: 40px">
										<label for="end-hours"><?php echo JText::_('COM_EASYDISCUSS_WORK_TILL'); ?></label>
									</div>
								</div>
								<div class="o-flag__body">

									<select name="main_work_endhour" class="form-control" id="end-hours" style="width:auto;display: inline-block;">
										<?php foreach($hours as $hh => $hlabel) { ?>
										<option value="<?php echo $hlabel; ?>"<?php echo ($this->config->get('main_work_endhour') == $hlabel) ? ' selected="true"' : ''; ?>><?php echo $hlabel; ?></option>
										<?php } ?>
									</select>

									<select name="main_work_endminute" class="form-control" id="end-minutes" style="width:auto;display: inline-block;">
										<?php foreach($minutes as $mm => $mlabel) { ?>
										<option value="<?php echo $mlabel; ?>"<?php echo ($this->config->get('main_work_endminute') == $mlabel) ? ' selected="true"' : ''; ?>><?php echo $mlabel; ?></option>
										<?php } ?>
									</select>
								</div>
							</div>

						</div>
					</div>

					<div class="form-group">
						<div class="col-md-5 control-label">
							<?php echo $this->html('form.label', 'COM_EASYDISCUSS_WORK_HOUR_DISPLAY_FORMAT'); ?>
						</div>
						<div class="col-md-7">

							<div class="o-radio">
								<input type="radio" id="item-radio-12h" name="main_work_hourformat" value="12"<?php echo $this->config->get('main_work_hourformat', 12) == '12' ? ' checked="true"' : ''; ?> />
								<label for="item-radio-12h">
									<?php echo JText::_('COM_EASYDISCUSS_WORK_12H'); ?>
								</label>
							</div>

							<div class="o-radio">
								<input type="radio" id="item-radio-24h" name="main_work_hourformat" value="24"<?php echo $this->config->get('main_work_hourformat', 12) == '24' ? ' checked="true"' : ''; ?> />
								<label for="item-radio-24h">
									<?php echo JText::_('COM_EASYDISCUSS_WORK_24H'); ?>
								</label>
							</div>

						</div>
					</div>

					<div class="form-group">
						<div class="col-md-12">&nbsp;</div>
						<div class="col-md-12 ml">
							<div class="o-alert o-alert--info">
								<?php echo JText::_('COM_EASYDISCUSS_WORK_NOTES');?>
							</div>
						</div>
					</div>

					<!-- end -->
				</div>
			</div>
		</div>
	</div>
</div>