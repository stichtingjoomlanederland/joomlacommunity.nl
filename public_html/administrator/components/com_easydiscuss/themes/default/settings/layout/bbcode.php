<?php
/**
* @package		EasyDiscuss
* @copyright	Copyright (C) 2010 Stack Ideas Private Limited. All rights reserved.
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
			<?php echo $this->html('panel.head', 'COM_EASYDISCUSS_BBCODE_FEATURES'); ?>

			<div id="option01" class="panel-body">
				<div class="form-horizontal">
					<div class="form-group">
                        <div class="col-md-5 control-label">
                            <?php echo $this->html('form.label', 'COM_EASYDISCUSS_BBCODE_SHOW_BOLD'); ?>
                        </div>
                        <div class="col-md-7">
							<?php echo $this->html('form.boolean', 'layout_bbcode_bold', $this->config->get('layout_bbcode_bold'));?>
						</div>
					</div>
					<div class="form-group">
                        <div class="col-md-5 control-label">
                            <?php echo $this->html('form.label', 'COM_EASYDISCUSS_BBCODE_SHOW_ITALIC'); ?>
                        </div>
                        <div class="col-md-7">
							<?php echo $this->html('form.boolean', 'layout_bbcode_italic', $this->config->get('layout_bbcode_italic'));?>
						</div>
					</div>
					<div class="form-group">
                        <div class="col-md-5 control-label">
                            <?php echo $this->html('form.label', 'COM_EASYDISCUSS_BBCODE_SHOW_UNDERLINE'); ?>
                        </div>
                        <div class="col-md-7">
							<?php echo $this->html('form.boolean', 'layout_bbcode_underline', $this->config->get('layout_bbcode_underline'));?>
						</div>
					</div>
					<div class="form-group">
                        <div class="col-md-5 control-label">
                            <?php echo $this->html('form.label', 'COM_EASYDISCUSS_BBCODE_SHOW_LINK'); ?>
                        </div>
                        <div class="col-md-7">
							<?php echo $this->html('form.boolean', 'layout_bbcode_link', $this->config->get('layout_bbcode_link'));?>
						</div>
					</div>
					<div class="form-group">
                        <div class="col-md-5 control-label">
                            <?php echo $this->html('form.label', 'COM_EASYDISCUSS_BBCODE_SHOW_IMAGE'); ?>
                        </div>
                        <div class="col-md-7">
							<?php echo $this->html('form.boolean', 'layout_bbcode_image', $this->config->get('layout_bbcode_image'));?>
						</div>
					</div>
					<div class="form-group">
                        <div class="col-md-5 control-label">
                            <?php echo $this->html('form.label', 'COM_EASYDISCUSS_BBCODE_SHOW_VIDEO'); ?>
                        </div>
                        <div class="col-md-7">
							<?php echo $this->html('form.boolean', 'layout_bbcode_video', $this->config->get('layout_bbcode_video'));?>
						</div>
					</div>
                    <div class="form-group">
                        <div class="col-md-5 control-label">
                            <?php echo $this->html('form.label', 'COM_EASYDISCUSS_BBCODE_SHOW_BULLET_LIST'); ?>
                        </div>
                        <div class="col-md-7">
                            <?php echo $this->html('form.boolean', 'layout_bbcode_bullets', $this->config->get('layout_bbcode_bullets'));?>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="col-md-5 control-label">
                            <?php echo $this->html('form.label', 'COM_EASYDISCUSS_BBCODE_SHOW_NUMERIC_LIST'); ?>
                        </div>
                        <div class="col-md-7">
                            <?php echo $this->html('form.boolean', 'layout_bbcode_numeric', $this->config->get('layout_bbcode_numeric'));?>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="col-md-5 control-label">
                            <?php echo $this->html('form.label', 'COM_EASYDISCUSS_BBCODE_SHOW_QUOTE'); ?>
                        </div>
                        <div class="col-md-7">
                            <?php echo $this->html('form.boolean', 'layout_bbcode_quote', $this->config->get('layout_bbcode_quote'));?>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="col-md-5 control-label">
                            <?php echo $this->html('form.label', 'COM_EASYDISCUSS_BBCODE_SHOW_CODE'); ?>
                        </div>
                        <div class="col-md-7">
                            <?php echo $this->html('form.boolean', 'layout_bbcode_code', $this->config->get('layout_bbcode_code'));?>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="col-md-5 control-label">
                            <?php echo $this->html('form.label', 'COM_EASYDISCUSS_BBCODE_SHOW_GIST'); ?>
                        </div>
                        <div class="col-md-7">
                            <?php echo $this->html('form.boolean', 'integrations_github', $this->config->get('integrations_github'));?>
                        </div>
                    </div>
				</div>
			</div>
		</div>
	</div>

	<div class="col-md-6">
        <div class="panel">
            <?php echo $this->html('panel.head', 'COM_EASYDISCUSS_BBCODE_SYNTAX_HIGHLIGHTING'); ?>

            <div class="panel-body">
                <div class="form-horizontal">
                    <div class="form-group">
                        <div class="col-md-5 control-label">
                            <?php echo $this->html('form.label', 'COM_EASYDISCUSS_SETTINGS_SYNTAX_HIGHLIGHTER'); ?>
                        </div>
                        <div class="col-md-7">
                            <?php echo $this->html('form.boolean', 'main_syntax_highlighter', $this->config->get('main_syntax_highlighter')); ?>

                            <div class="small t-mt--sm"> 
                                <?php echo JText::_('COM_EASYDISCUSS_SETTINGS_SYNTAX_HIGHLIGHTER_NOTE'); ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="panel">
            <?php echo $this->html('panel.head', 'COM_EASYDISCUSS_BBCODE_LINKS'); ?>

            <div class="panel-body">
                <div class="form-horizontal">
                    <div class="form-group">
                        <div class="col-md-5 control-label">
                            <?php echo $this->html('form.label', 'COM_EASYDISCUSS_LINK_NEW_WINDOW'); ?>
                        </div>
                        <div class="col-md-7">
                            <?php echo $this->html('form.boolean', 'main_link_new_window', $this->config->get('main_link_new_window')); ?>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="col-md-5 control-label">
                            <?php echo $this->html('form.label', 'COM_EASYDISCUSS_LINK_REL_NOFOLLOW'); ?>
                        </div>
                        <div class="col-md-7">
                            <?php echo $this->html('form.boolean', 'main_link_rel_nofollow', $this->config->get('main_link_rel_nofollow')); ?>
                        </div>
                    </div>                    
                </div>
            </div>
        </div>

        <div class="panel">
            <?php echo $this->html('panel.head', 'COM_EASYDISCUSS_VIDEO_EMBEDDING'); ?>

            <div id="option07" class="panel-body">
                <div class="form-horizontal">
                    <div class="form-group">
                        <div class="col-md-5 control-label">
                            <?php echo $this->html('form.label', 'COM_EASYDISCUSS_VIDEO_WIDTH'); ?>
                        </div>
                        <div class="col-md-7">
                            <input type="text" class="form-control form-control-sm text-center" name="bbcode_video_width" value="<?php echo $this->config->get('bbcode_video_width');?>" size="5" style="text-align:center;" />
                            <?php echo JText::_('COM_EASYDISCUSS_PIXELS'); ?>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="col-md-5 control-label">
                            <?php echo $this->html('form.label', 'COM_EASYDISCUSS_VIDEO_HEIGHT'); ?>
                        </div>
                        <div class="col-md-7">
                            <input type="text" class="form-control form-control-sm text-center" name="bbcode_video_height" value="<?php echo $this->config->get('bbcode_video_height');?>" size="5" style="text-align:center;" />
                            <?php echo JText::_('COM_EASYDISCUSS_PIXELS'); ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
	</div>
</div>