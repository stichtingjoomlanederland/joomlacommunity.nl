<?php
/**
* @package      EasyDiscuss
* @copyright    Copyright (C) 2010 - 2015 Stack Ideas Sdn Bhd. All rights reserved.
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
			<?php echo $this->html('panel.head', 'COM_EASYDISCUSS_LAYOUT_TOOLBAR'); ?>

			<div class="panel-body">
				<div class="form-horizontal">
                    <div class="form-group">
                        <div class="col-md-5 control-label">
                            <?php echo $this->html('form.label', 'COM_EASYDISCUSS_ENABLE_HEADERS'); ?>
                        </div>
                        <div class="col-md-7">
                            <?php echo $this->html('form.boolean', 'layout_headers', $this->config->get('layout_headers'));?>
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="col-md-5 control-label">
                            <?php echo $this->html('form.label', 'COM_EASYDISCUSS_MAIN_TITLE'); ?>
                        </div>
                        <div class="col-md-7">
                            <input type="text" name="main_title" class="form-control" size="60" value="<?php echo $this->config->get('main_title');?>" />
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="col-md-5 control-label">
                            <?php echo $this->html('form.label', 'COM_EASYDISCUSS_MAIN_DESCRIPTION'); ?>
                        </div>
                        <div class="col-md-7">
                            <textarea name="main_description" class="form-control" cols="65" rows="5"><?php echo $this->config->get('main_description'); ?></textarea>
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="col-md-5 control-label">
                            <?php echo $this->html('form.label', 'COM_EASYDISCUSS_SETTINGS_RSS'); ?>
                        </div>
                        <div class="col-md-7">
                            <?php echo $this->html('form.boolean', 'main_rss', $this->config->get('main_rss')); ?>
                        </div>
                    </div>
                    
				</div>
			</div>
		</div>

        <div class="panel">
            <?php echo $this->html('panel.head', "COM_EASYDISCUSS_LAYOUT_TOOLBAR_NOTIFICATIONS"); ?>

            <div class="panel-body">
                <div class="form-group">
                    <div class="col-md-5 control-label">
                        <?php echo $this->html('form.label', 'COM_EASYDISCUSS_TOOLBAR_CONVERSATIONS'); ?>
                    </div>
                    <div class="col-md-7">
                        <?php echo $this->html('form.boolean', 'layout_toolbar_conversation', $this->config->get('layout_toolbar_conversation'));?>
                    </div>
                </div>

                <div class="form-group">
                    <div class="col-md-5 control-label">
                        <?php echo $this->html('form.label', 'COM_EASYDISCUSS_TOOLBAR_NOTIFICATIONS'); ?>
                    </div>
                    <div class="col-md-7">
                        <?php echo $this->html('form.boolean', 'layout_toolbar_notification', $this->config->get('layout_toolbar_notification'));?>
                    </div>
                </div>
            </div>
        </div>
	</div>

	<div class="col-md-6">
        <div class="panel">
            <?php echo $this->html('panel.head', "COM_EASYDISCUSS_LAYOUT_TOOLBAR_FEATURES"); ?>

            <div class="panel-body">
                <div class="form-horizontal">
                    <div class="form-group">
                        <div class="col-md-5 control-label">
                            <?php echo $this->html('form.label', 'COM_EASYDISCUSS_ENABLE_TOOLBAR'); ?>
                        </div>
                        <div class="col-md-7">
                            <?php echo $this->html('form.boolean', 'layout_enabletoolbar', $this->config->get('layout_enabletoolbar'));?>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="col-md-5 control-label">
                            <?php echo $this->html('form.label', 'COM_EASYDISCUSS_ENABLE_TOOLBAR_SEARCHBAR'); ?>
                        </div>
                        <div class="col-md-7">
                            <?php echo $this->html('form.boolean', 'layout_toolbar_searchbar', $this->config->get('layout_toolbar_searchbar'));?>
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="col-md-5 control-label">
                            <?php echo $this->html('form.label', 'COM_EASYDISCUSS_ENABLE_DISCUSSION_BUTTON'); ?>
                        </div>
                        <div class="col-md-7">
                            <?php echo $this->html('form.boolean', 'layout_toolbardiscussion', $this->config->get('layout_toolbardiscussion'));?>
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="col-md-5 control-label">
                            <?php echo $this->html('form.label', 'COM_EASYDISCUSS_ENABLE_TAGS_BUTTON'); ?>
                        </div>
                        <div class="col-md-7">
                            <?php echo $this->html('form.boolean', 'layout_toolbartags', $this->config->get('layout_toolbartags'));?>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="col-md-5 control-label">
                            <?php echo $this->html('form.label', 'COM_EASYDISCUSS_ENABLE_CATEGORIES_BUTTON'); ?>
                        </div>
                        <div class="col-md-7">
                            <?php echo $this->html('form.boolean', 'layout_toolbarcategories', $this->config->get('layout_toolbarcategories'));?>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="col-md-5 control-label">
                            <?php echo $this->html('form.label', 'COM_EASYDISCUSS_ENABLE_USERS_BUTTON'); ?>
                        </div>
                        <div class="col-md-7">
                            <?php echo $this->html('form.boolean', 'layout_toolbarusers', $this->config->get('layout_toolbarusers'));?>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="col-md-5 control-label">
                            <?php echo $this->html('form.label', 'COM_EASYDISCUSS_LAYOUT_TOOLBAR_BADGES'); ?>
                        </div>
                        <div class="col-md-7">
                            <?php echo $this->html('form.boolean', 'layout_toolbarbadges', $this->config->get('layout_toolbarbadges'));?>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="col-md-5 control-label">
                            <?php echo $this->html('form.label', 'COM_EASYDISCUSS_ENABLE_PROFILE_BUTTON'); ?>
                        </div>
                        <div class="col-md-7">
                            <?php echo $this->html('form.boolean', 'layout_toolbarprofile', $this->config->get('layout_toolbarprofile'));?>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="col-md-5 control-label">
                            <?php echo $this->html('form.label', 'COM_EASYDISCUSS_LAYOUT_TOOLBAR_LOGIN'); ?>
                        </div>
                        <div class="col-md-7">
                            <?php echo $this->html('form.boolean', 'layout_toolbarlogin', $this->config->get('layout_toolbarlogin'));?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
	</div>
</div>