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
			<?php echo $this->html('panel.head', 'COM_EASYDISCUSS_SETTINGS_MAIN_COMPOSER'); ?>

			<div class="panel-body">
				<div class="form-horizontal">

					<div class="form-group">
						<div class="col-md-5 control-label">
							<?php echo $this->html('form.label', 'COM_EASYDISCUSS_DISCUSSION_EDITOR'); ?>
						</div>
						<div class="col-md-7">
							<?php echo $this->html('form.editor', 'layout_editor', $this->config->get('layout_editor', 'bbcode')); ?>
						</div>
					</div>

					<div class="form-group">
						<div class="col-md-5 control-label">
							<?php echo $this->html('form.label', 'COM_EASYDISCUSS_REDIRECTION_AFTER_POST'); ?>
						</div>
						<div class="col-md-7">
							<?php
								$options = array();
								$options[] = JHTML::_('select.option', 'default', JText::_('COM_EASYDISCUSS_REDIRECTION_DEFAULT'));
								$options[] = JHTML::_('select.option', 'home', JText::_('COM_EASYDISCUSS_REDIRECTION_HOME'));
								$options[] = JHTML::_('select.option', 'mainCategory', JText::_('COM_EASYDISCUSS_REDIRECTION_ALL_CATEGORIES'));
								$options[] = JHTML::_('select.option', 'currentCategory', JText::_('COM_EASYDISCUSS_REDIRECTION_CURRENT_CATEGORY'));
								$options[] = JHTML::_('select.option', 'myPosts', JText::_('COM_ED_REDIRECTION_USERS_POSTS'));
								
								$categorySortHTML = JHTML::_('select.genericlist', $options, 'main_post_redirection', 'class="form-control"  ', 'value', 'text', $this->config->get('main_post_redirection' , 'default'));
								echo $categorySortHTML;
							?>
						</div>
					</div>

					<?php echo $this->html('settings.toggle', 'main_private_post', 'COM_EASYDISCUSS_SETTINGS_PRIVATE_POSTINGS'); ?>

					<div class="form-group">
						<div class="col-md-5 control-label">
							<?php echo $this->html('form.label', 'COM_ED_ENFORCE_TITLE_MAX_CHARS'); ?>
						</div>
						<div class="col-md-7">
							<?php echo $this->html('form.boolean', 'main_post_title_limit', $this->config->get('main_post_title_limit'), '', 'data-max-title-option'); ?>
						</div>
					</div>

					<div class="form-group <?php echo $this->config->get('main_post_title_limit') ? '' : 't-hidden';?>" data-max-title-form>
						<div class="col-md-5 control-label">
							<?php echo $this->html('form.label', 'COM_ED_MAX_TITLE_CHARS'); ?>
						</div>
						<div class="col-md-7">
							<?php echo $this->html('form.textbox', 'main_post_title_chars', $this->config->get('main_post_title_chars'), '', 'text-center form-control-sm'); ?> <?php echo JText::_('COM_EASYDISCUSS_CHARACTERS');?>
						</div>
					</div>

					<?php echo $this->html('settings.textbox', 'antispam_minimum_title', 'COM_EASYDISCUSS_ANTI_SPAM_MINIMUM_TITLE', '', array('size' => 8, 'postfix' => 'Characters'), '', 'form-control-sm text-center'); ?>
					<?php echo $this->html('settings.textbox', 'main_post_min_length', 'COM_EASYDISCUSS_MAIN_POST_MIN_LENGTH', '', array('size' => 8, 'postfix' => 'COM_EASYDISCUSS_CHARACTERS'), '', 'form-control-sm text-center'); ?>
					<?php echo $this->html('settings.toggle', 'main_post_appendemail', 'COM_EASYDISCUSS_POST_APPEND_EMAIL_ADDRESS_IN_CONTENT'); ?>
					<?php echo $this->html('settings.toggle', 'main_anonymous_posting', 'COM_EASYDISCUSS_ENABLE_ANONYMOUS_POSTING'); ?>
					<?php echo $this->html('settings.toggle', 'main_mentions', 'COM_EASYDISCUSS_ENABLE_MENTIONS'); ?>
					<?php echo $this->html('settings.toggle', 'post_priority', 'COM_EASYDISCUSS_ENABLE_POST_PRIORITY'); ?>
					<?php echo $this->html('settings.toggle', 'layout_post_types', 'COM_EASYDISCUSS_ENABLE_POST_TYPES'); ?>
					<?php echo $this->html('settings.toggle', 'main_password_protection', 'COM_EASYDISCUSS_SETTINGS_PASSWORD_PROTECTION'); ?>
				</div>
			</div>
		</div>

		<div class="panel">
			<?php echo $this->html('panel.head', 'COM_EASYDISCUSS_SETTINGS_SITEDETAILS'); ?>

			<div class="panel-body">
				<div class="form-horizontal">
					<?php echo $this->html('settings.toggle', 'tab_site_question', 'COM_EASYDISCUSS_SITEDETAILS_ENABLE_QUESTION'); ?>
					<?php echo $this->html('settings.toggle', 'tab_site_reply', 'COM_EASYDISCUSS_SITEDETAILS_ENABLE_REPLIES'); ?>

					<div class="form-group">
						<div class="col-md-5 control-label">
							<?php echo $this->html('form.label', 'COM_EASYDISCUSS_SITEDETAILS_VIEW_ACCESS'); ?>
						</div>
						<div class="col-md-7">
							<?php
							$access = explode(',', trim($this->config->get('tab_site_access')));
							?>
							<select name="tab_site_access[]" multiple="multiple" style="height:150px;">
							<?php foreach ($joomlaGroups as $group) { ?>
								<option value="<?php echo $group->id;?>"<?php echo in_array($group->id, $access) ? ' selected="selected"' : '';?>><?php echo $group->name; ?></option>
							<?php }?>
							</select>
						</div>
					</div>
				</div>
			</div>
		</div>

	</div>

	<div class="col-md-6">
		<div class="panel">
			<?php echo $this->html('panel.head', 'COM_EASYDISCUSS_AUTOMATION'); ?>

			<div class="panel-body">
				<div class="form-horizontal">
					<?php echo $this->html('settings.toggle', 'main_lock_newpost_only', 'COM_EASYDISCUSS_AUTOLOCK_NEWPOST_ONLY'); ?>

					<?php echo $this->html('settings.textbox', 'main_daystolock_afterlastreplied', 'COM_EASYDISCUSS_DAYSTOLOCK_REPLIED', '', array('size' => 7, 'postfix' => 'Days'), '', 'form-control-sm text-center'); ?>

					<?php echo $this->html('settings.textbox', 'main_daystolock_aftercreated', 'COM_EASYDISCUSS_DAYSTOLOCK_CREATED', '', array('size' => 7, 'postfix' => 'Days'), '', 'form-control-sm text-center'); ?>
				</div>
			</div>
		</div>

		<div class="panel">
			<?php echo $this->html('panel.head', 'COM_EASYDISCUSS_SIMILAR_QUESTION'); ?>

			<div class="panel-body">
				<div class="form-horizontal">
					<?php echo $this->html('settings.toggle', 'main_similartopic', 'COM_EASYDISCUSS_SIMILAR_QUESTION_ENABLE'); ?>
					<?php echo $this->html('settings.toggle', 'main_similartopic_privatepost', 'COM_EASYDISCUSS_SIMILAR_QUESTION_INCLUDE_PRIVATE_POSTS'); ?>
					<?php echo $this->html('settings.textbox', 'main_similartopic_limit', 'COM_EASYDISCUSS_SIMILAR_QUESTION_SEARCH_LIMIT', '', array('size' => 7, 'postfix' => 'Posts'), '', 'form-control-sm text-center'); ?>
				</div>
			</div>
		</div>

		<div class="panel">
			<?php echo $this->html('panel.head', 'COM_EASYDISCUSS_FIELDS_URL'); ?>

			<div class="panel-body">
				<div class="form-horizontal">
					<?php echo $this->html('settings.toggle', 'reply_field_references', 'COM_EASYDISCUSS_FIELDS_URL_REFERENCES'); ?>
					<?php echo $this->html('settings.toggle', 'main_reference_link_new_window', 'COM_EASYDISCUSS_REFERENCE_LINK_NEW_WINDOW'); ?>
				</div>
			</div>
		</div>
		
		<div class="panel">
			<?php echo $this->html('panel.head', 'COM_EASYDISCUSS_EVENT_TRIGGERS'); ?>

			<div class="panel-body">
				<div class="form-horizontal">
					<?php echo $this->html('settings.toggle', 'main_content_trigger_posts', 'COM_EASYDISCUSS_ENABLE_TRIGGER_POSTS'); ?>
					<?php echo $this->html('settings.toggle', 'main_content_trigger_replies', 'COM_EASYDISCUSS_ENABLE_TRIGGER_REPLIES'); ?>
					<?php echo $this->html('settings.toggle', 'main_content_trigger_comments', 'COM_EASYDISCUSS_ENABLE_TRIGGER_COMMENTS'); ?>
				</div>
			</div>
		</div>
	</div>
</div>