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
                                $sortingType = array();
                                $sortingType[] = JHTML::_('select.option', 'default', JText::_('COM_EASYDISCUSS_REDIRECTION_DEFAULT'));
                                $sortingType[] = JHTML::_('select.option', 'home', JText::_('COM_EASYDISCUSS_REDIRECTION_HOME'));
                                $sortingType[] = JHTML::_('select.option', 'mainCategory', JText::_('COM_EASYDISCUSS_REDIRECTION_ALL_CATEGORIES'));
                                $sortingType[] = JHTML::_('select.option', 'currentCategory', JText::_('COM_EASYDISCUSS_REDIRECTION_CURRENT_CATEGORY'));
                                $categorySortHTML = JHTML::_('select.genericlist', $sortingType, 'main_post_redirection', 'class="form-control"  ', 'value', 'text', $this->config->get('main_post_redirection' , 'default'));
                                echo $categorySortHTML;
                            ?>
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="col-md-5 control-label">
                            <?php echo $this->html('form.label', 'COM_EASYDISCUSS_SETTINGS_PRIVATE_POSTINGS'); ?>
                        </div>
                        <div class="col-md-7">
                            <?php echo $this->html('form.boolean', 'main_private_post', $this->config->get('main_private_post')); ?>
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="col-md-5 control-label">
                            <?php echo $this->html('form.label', 'COM_EASYDISCUSS_MAIN_POST_MIN_LENGTH'); ?>
                        </div>
                        <div class="col-md-7">
                            <input type="text" name="main_post_min_length" class="form-control form-control-sm text-center" value="<?php echo $this->config->get('main_post_min_length');?>" />
                        </div>
                    </div>

                </div>
            </div>
        </div>

        <div class="panel">
            <?php echo $this->html('panel.head', 'COM_EASYDISCUSS_AUTOMATION'); ?>

            <div class="panel-body">
                <div class="form-horizontal">
                    <div class="form-group">
                        <div class="col-md-5 control-label">
                            <?php echo $this->html('form.label', 'COM_EASYDISCUSS_AUTOLOCK_NEWPOST_ONLY'); ?>
                        </div>
                        <div class="col-md-7">
                            <?php echo $this->html('form.boolean', 'main_lock_newpost_only', $this->config->get('main_lock_newpost_only')); ?>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="col-md-5 control-label">
                            <?php echo $this->html('form.label', 'COM_EASYDISCUSS_DAYSTOLOCK_REPLIED'); ?>
                        </div>
                        <div class="col-md-7">
                            <input type="text" name="main_daystolock_afterlastreplied" class="form-control form-control-sm text-center" value="<?php echo $this->config->get('main_daystolock_afterlastreplied');?>" />
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="col-md-5 control-label">
                            <?php echo $this->html('form.label', 'COM_EASYDISCUSS_DAYSTOLOCK_CREATED'); ?>
                        </div>
                        <div class="col-md-7">
                            <input type="text" name="main_daystolock_aftercreated" class="form-control form-control-sm text-center" value="<?php echo $this->config->get('main_daystolock_aftercreated');?>" />
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="panel">
            <?php echo $this->html('panel.head', 'COM_EASYDISCUSS_EVENT_TRIGGERS'); ?>

            <div id="option12" class="panel-body">
                <div class="form-horizontal">
                    <div class="form-group">
                        <div class="col-md-5 control-label">
                            <?php echo $this->html('form.label', 'COM_EASYDISCUSS_ENABLE_TRIGGER_POSTS'); ?>
                        </div>
                        <div class="col-md-7">
                            <?php echo $this->html('form.boolean', 'main_content_trigger_posts', $this->config->get('main_content_trigger_posts')); ?>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="col-md-5 control-label">
                            <?php echo $this->html('form.label', 'COM_EASYDISCUSS_ENABLE_TRIGGER_REPLIES'); ?>
                        </div>
                        <div class="col-md-7">
                            <?php echo $this->html('form.boolean', 'main_content_trigger_replies', $this->config->get('main_content_trigger_replies')); ?>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="col-md-5 control-label">
                            <?php echo $this->html('form.label', 'COM_EASYDISCUSS_ENABLE_TRIGGER_COMMENTS'); ?>
                        </div>
                        <div class="col-md-7">
                            <?php echo $this->html('form.boolean', 'main_content_trigger_comments', $this->config->get('main_content_trigger_comments')); ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-6">
        <div class="panel">
            <?php echo $this->html('panel.head', 'COM_EASYDISCUSS_ANONYMOUS_POSTING'); ?>

            <div class="panel-body">
                <div class="form-horizontal">
                    <div class="form-group">
                        <div class="col-md-5 control-label">
                            <?php echo $this->html('form.label', 'COM_EASYDISCUSS_ENABLE_ANONYMOUS_POSTING'); ?>
                        </div>
                        <div class="col-md-7">
                            <?php echo $this->html('form.boolean', 'main_anonymous_posting', $this->config->get('main_anonymous_posting')); ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="panel">
            <?php echo $this->html('panel.head', 'COM_EASYDISCUSS_MENTIONS'); ?>

            <div class="panel-body">
                <div class="form-horizontal">
                    <div class="form-group">
                        <div class="col-md-5 control-label">
                            <?php echo $this->html('form.label', 'COM_EASYDISCUSS_ENABLE_MENTIONS'); ?>
                        </div>
                        <div class="col-md-7">
                            <?php echo $this->html('form.boolean', 'main_mentions', $this->config->get('main_mentions')); ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="panel">
            <?php echo $this->html('panel.head', 'COM_EASYDISCUSS_POST_PRIORITY'); ?>

            <div class="panel-body">
                <div class="form-horizontal">
                    <div class="form-group">
                        <div class="col-md-5 control-label">
                            <?php echo $this->html('form.label', 'COM_EASYDISCUSS_ENABLE_POST_PRIORITY'); ?>
                        </div>
                        <div class="col-md-7">
                            <?php echo $this->html('form.boolean', 'post_priority', $this->config->get('post_priority'));?>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="panel">
            <?php echo $this->html('panel.head', 'COM_EASYDISCUSS_POST_TYPES'); ?>

            <div class="panel-body">
                <div class="form-horizontal">
                    <div class="form-group">
                        <div class="col-md-5 control-label">
                            <?php echo $this->html('form.label', 'COM_EASYDISCUSS_ENABLE_POST_TYPES'); ?>
                        </div>
                        <div class="col-md-7">
                            <?php echo $this->html('form.boolean', 'layout_post_types', $this->config->get('layout_post_types'));?>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="panel">
            <?php echo $this->html('panel.head', 'COM_EASYDISCUSS_SIMILAR_QUESTION'); ?>

            <div class="panel-body">
                <div class="form-horizontal">
                    <div class="form-group">
                        <div class="col-md-5 control-label">
                            <?php echo $this->html('form.label', 'COM_EASYDISCUSS_SIMILAR_QUESTION_ENABLE'); ?>
                        </div>
                        <div class="col-md-7">
                            <?php echo $this->html('form.boolean', 'main_similartopic', $this->config->get('main_similartopic')); ?>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="col-md-5 control-label">
                            <?php echo $this->html('form.label', 'COM_EASYDISCUSS_SIMILAR_QUESTION_INCLUDE_PRIVATE_POSTS'); ?>
                        </div>
                        <div class="col-md-7">
                            <?php echo $this->html('form.boolean', 'main_similartopic_privatepost', $this->config->get('main_similartopic_privatepost')); ?>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="col-md-5 control-label">
                            <?php echo $this->html('form.label', 'COM_EASYDISCUSS_SIMILAR_QUESTION_SEARCH_LIMIT'); ?>
                        </div>
                        <div class="col-md-7">
                            <input type="text" name="main_similartopic_limit" class="form-control form-control-sm text-center" value="<?php echo $this->config->get('main_similartopic_limit' , '5');?>" />
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>