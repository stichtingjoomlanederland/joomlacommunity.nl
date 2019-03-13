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
			<?php echo $this->html('panel.head', 'COM_EASYDISCUSS_LAYOUT_TOOLBAR'); ?>

			<div class="panel-body">
				<div class="form-horizontal">
					<?php echo $this->html('settings.toggle', 'layout_headers', 'COM_EASYDISCUSS_ENABLE_HEADERS'); ?>
					<?php echo $this->html('settings.textbox', 'main_title', 'COM_EASYDISCUSS_MAIN_TITLE'); ?>

					<div class="form-group">
						<div class="col-md-5 control-label">
							<?php echo $this->html('form.label', 'COM_EASYDISCUSS_MAIN_DESCRIPTION'); ?>
						</div>
						<div class="col-md-7">
							<textarea name="main_description" class="form-control" cols="65" rows="5"><?php echo $this->config->get('main_description'); ?></textarea>
						</div>
					</div>

					<?php echo $this->html('settings.toggle', 'main_rss', 'COM_EASYDISCUSS_SETTINGS_RSS'); ?>
					<?php echo $this->html('settings.toggle', 'layout_enabletoolbar', 'COM_EASYDISCUSS_ENABLE_TOOLBAR'); ?>
					<?php echo $this->html('settings.toggle', 'layout_toolbar_searchbar', 'COM_EASYDISCUSS_ENABLE_TOOLBAR_SEARCHBAR'); ?>
					<?php echo $this->html('settings.toggle', 'layout_toolbarhome', 'COM_ED_ENABLE_TOOLBAR_HOME'); ?>
					<?php echo $this->html('settings.toggle', 'layout_toolbardiscussion', 'COM_EASYDISCUSS_ENABLE_DISCUSSION_BUTTON'); ?>
					<?php echo $this->html('settings.toggle', 'layout_toolbartags', 'COM_EASYDISCUSS_ENABLE_TAGS_BUTTON'); ?>
					<?php echo $this->html('settings.toggle', 'layout_toolbarcategories', 'COM_EASYDISCUSS_ENABLE_CATEGORIES_BUTTON'); ?>
					<?php echo $this->html('settings.toggle', 'layout_toolbarusers', 'COM_EASYDISCUSS_ENABLE_USERS_BUTTON'); ?>
					<?php echo $this->html('settings.toggle', 'layout_toolbarbadges', 'COM_EASYDISCUSS_LAYOUT_TOOLBAR_BADGES'); ?>
					<?php echo $this->html('settings.toggle', 'layout_toolbarprofile', 'COM_EASYDISCUSS_ENABLE_PROFILE_BUTTON'); ?>
					<?php echo $this->html('settings.toggle', 'layout_toolbarlogin', 'COM_EASYDISCUSS_LAYOUT_TOOLBAR_LOGIN'); ?>
					<?php echo $this->html('settings.toggle', 'layout_toolbar_conversation', 'COM_EASYDISCUSS_TOOLBAR_CONVERSATIONS'); ?>
					<?php echo $this->html('settings.toggle', 'layout_toolbar_notification', 'COM_EASYDISCUSS_TOOLBAR_NOTIFICATIONS'); ?>
				</div>
			</div>
		</div>
	</div>

	<div class="col-md-6">
		<div class="panel">
			<?php echo $this->html('panel.heading', 'COM_ED_TOOLBAR_STYLES'); ?>

			<div class="panel-body">
				<div class="form-group">
					<div class="col-md-5 control-label">
						<?php echo $this->html('form.label', 'COM_ED_TOOLBAR_COLOR'); ?>
					</div>

					<div class="col-md-7">
						<?php echo $this->html('form.colorpicker', 'layout_toolbarcolor', $this->config->get('layout_toolbarcolor'), '#333333'); ?>
					</div>
				</div>

				<div class="form-group">
					<div class="col-md-5 control-label">
						<?php echo $this->html('form.label', 'COM_ED_TOOLBAR_ACTIVE_COLOR'); ?>
					</div>

					<div class="col-md-7">
						<?php echo $this->html('form.colorpicker', 'layout_toolbaractivecolor', $this->config->get('layout_toolbaractivecolor'), '#5C5C5C'); ?>
					</div>
				</div>

				<div class="form-group">
					<div class="col-md-5 control-label">
						<?php echo $this->html('form.label', 'COM_ED_TOOLBAR_TEXT_COLOR'); ?>
					</div>

					<div class="col-md-7">
						<?php echo $this->html('form.colorpicker', 'layout_toolbartextcolor', $this->config->get('layout_toolbartextcolor'), '#FFFFFF'); ?>
					</div>
				</div>

				<div class="form-group">
					<div class="col-md-5 control-label">
						<?php echo $this->html('form.label', 'COM_ED_TOOLBAR_BORDER_COLOR'); ?>
					</div>

					<div class="col-md-7">
						<?php echo $this->html('form.colorpicker', 'layout_toolbarbordercolor', $this->config->get('layout_toolbarbordercolor'), '#333333'); ?>
					</div>
				</div>
				
				<div class="form-group">
					<div class="col-md-5 control-label">
						<?php echo $this->html('form.label', 'COM_ED_TOOLBAR_NEW_POST_BACKGROUND_COLOR'); ?>
					</div>

					<div class="col-md-7">
						<?php echo $this->html('form.colorpicker', 'layout_toolbarcomposerbackgroundcolor', $this->config->get('layout_toolbarcomposerbackgroundcolor'), '#428bca'); ?>
					</div>
				</div>                          
			</div>
		</div>

	</div>
</div>