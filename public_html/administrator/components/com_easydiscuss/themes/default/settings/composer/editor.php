<?php
/**
* @package      EasyDiscuss
* @copyright    Copyright (C) 2010 - 2019 Stack Ideas Sdn Bhd. All rights reserved.
* @license      GNU/GPL, see LICENSE.php
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
			<?php echo $this->html('panel.head', 'COM_EASYDISCUSS_BBCODE_FEATURES'); ?>

			<div class="panel-body">
				<div class="form-horizontal">
					<?php echo $this->html('settings.toggle', 'layout_bbcode_bold', 'COM_EASYDISCUSS_BBCODE_SHOW_BOLD'); ?>
					<?php echo $this->html('settings.toggle', 'layout_bbcode_italic', 'COM_EASYDISCUSS_BBCODE_SHOW_ITALIC'); ?>
					<?php echo $this->html('settings.toggle', 'layout_bbcode_underline', 'COM_EASYDISCUSS_BBCODE_SHOW_UNDERLINE'); ?>
					<?php echo $this->html('settings.toggle', 'layout_bbcode_link', 'COM_EASYDISCUSS_BBCODE_SHOW_LINK'); ?>
					<?php echo $this->html('settings.toggle', 'layout_bbcode_image', 'COM_EASYDISCUSS_BBCODE_SHOW_IMAGE'); ?>
					<?php echo $this->html('settings.toggle', 'layout_bbcode_video', 'COM_EASYDISCUSS_BBCODE_SHOW_VIDEO'); ?>
					<?php echo $this->html('settings.toggle', 'layout_bbcode_bullets', 'COM_EASYDISCUSS_BBCODE_SHOW_BULLET_LIST'); ?>
					<?php echo $this->html('settings.toggle', 'layout_bbcode_numeric', 'COM_EASYDISCUSS_BBCODE_SHOW_NUMERIC_LIST'); ?>
					<?php echo $this->html('settings.toggle', 'layout_bbcode_quote', 'COM_EASYDISCUSS_BBCODE_SHOW_QUOTE'); ?>
					<?php echo $this->html('settings.toggle', 'layout_bbcode_code', 'COM_EASYDISCUSS_BBCODE_SHOW_CODE'); ?>
					<?php echo $this->html('settings.toggle', 'layout_bbcode_emoji', 'COM_EASYDISCUSS_BBCODE_SHOW_EMOJI'); ?>
					<?php echo $this->html('settings.toggle', 'integrations_github', 'COM_EASYDISCUSS_BBCODE_SHOW_GIST', '', array(), 'COM_ED_BBCODE_SHOW_GIST_NOTE'); ?>					
				</div>
			</div>
		</div>
	</div>

	<div class="col-md-6">
		<div class="panel">
			<?php echo $this->html('panel.head', 'COM_EASYDISCUSS_BBCODE_SYNTAX_HIGHLIGHTING'); ?>

			<div class="panel-body">
				<div class="form-horizontal">
					<?php echo $this->html('settings.toggle', 'main_syntax_highlighter', 'COM_EASYDISCUSS_SETTINGS_SYNTAX_HIGHLIGHTER', '', array(), 'COM_EASYDISCUSS_SETTINGS_SYNTAX_HIGHLIGHTER_NOTE'); ?>
				</div>
			</div>
		</div>

		<div class="panel">
			<?php echo $this->html('panel.head', 'COM_EASYDISCUSS_BBCODE_LINKS'); ?>

			<div class="panel-body">
				<div class="form-horizontal">
					<?php echo $this->html('settings.toggle', 'main_link_new_window', 'COM_EASYDISCUSS_LINK_NEW_WINDOW'); ?>
					<?php echo $this->html('settings.toggle', 'main_link_rel_nofollow', 'COM_EASYDISCUSS_LINK_REL_NOFOLLOW'); ?>
				</div>
			</div>
		</div>

		<div class="panel">
			<?php echo $this->html('panel.head', 'COM_EASYDISCUSS_VIDEO_EMBEDDING'); ?>

			<div class="panel-body">
				<div class="form-horizontal">
					<?php echo $this->html('settings.textbox', 'bbcode_video_width', 'COM_EASYDISCUSS_VIDEO_WIDTH', '', array('size' => 6, 'postfix' => 'px'), '', 'form-control-sm text-center'); ?>
					<?php echo $this->html('settings.textbox', 'bbcode_video_height', 'COM_EASYDISCUSS_VIDEO_HEIGHT', '', array('size' => 6, 'postfix' => 'px'), '', 'form-control-sm text-center'); ?>
				</div>
			</div>
		</div>
	</div>
</div>