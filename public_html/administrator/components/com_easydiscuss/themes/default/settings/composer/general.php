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
			<?php echo $this->html('panel.head', 'COM_EASYDISCUSS_SETTINGS_MAIN_COMPOSER'); ?>

			<div class="panel-body">
				<div class="o-form-horizontal">

					<div class="o-form-group">
						<div class="col-md-5 o-form-label">
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
								
								$categorySortHTML = JHTML::_('select.genericlist', $options, 'main_post_redirection', 'class="o-form-select"  ', 'value', 'text', $this->config->get('main_post_redirection' , 'default'));
								echo $categorySortHTML;
							?>
						</div>
					</div>


					<?php echo $this->html('settings.toggle', 'main_mentions', 'COM_EASYDISCUSS_ENABLE_MENTIONS'); ?>
					

					<?php echo $this->html('settings.toggle', 'main_post_appendemail', 'COM_EASYDISCUSS_POST_APPEND_EMAIL_ADDRESS_IN_CONTENT'); ?>
				</div>
			</div>
		</div>

		<div class="panel">
			<?php echo $this->html('panel.head', 'COM_EASYDISCUSS_EVENT_TRIGGERS'); ?>

			<div class="panel-body">
				<div class="o-form-horizontal">
					<?php echo $this->html('settings.toggle', 'main_content_trigger_posts', 'COM_EASYDISCUSS_ENABLE_TRIGGER_POSTS'); ?>
					<?php echo $this->html('settings.toggle', 'main_content_trigger_replies', 'COM_EASYDISCUSS_ENABLE_TRIGGER_REPLIES'); ?>
					<?php echo $this->html('settings.toggle', 'main_content_trigger_comments', 'COM_EASYDISCUSS_ENABLE_TRIGGER_COMMENTS'); ?>
				</div>
			</div>
		</div>
	</div>

	<div class="col-md-6">
		<div class="panel">
			<?php echo $this->html('panel.head', 'COM_EASYDISCUSS_AUTOMATION'); ?>

			<div class="panel-body">
				<div class="o-form-horizontal">
					<?php echo $this->html('settings.toggle', 'main_lock_newpost_only', 'COM_EASYDISCUSS_AUTOLOCK_NEWPOST_ONLY'); ?>

					<?php echo $this->html('settings.textbox', 'main_daystolock_afterlastreplied', 'COM_EASYDISCUSS_DAYSTOLOCK_REPLIED', '', array('size' => 7, 'postfix' => 'Days'), '', '', 'text-center'); ?>

					<?php echo $this->html('settings.textbox', 'main_daystolock_aftercreated', 'COM_EASYDISCUSS_DAYSTOLOCK_CREATED', '', array('size' => 7, 'postfix' => 'Days'), '', '', 'text-center'); ?>
				</div>
			</div>
		</div>
	</div>
</div>