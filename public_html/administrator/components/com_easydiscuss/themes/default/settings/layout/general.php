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
            <?php echo $this->html('panel.head', 'COM_EASYDISCUSS_DISPLAY'); ?>

            <div class="panel-body">
                <div class="form-horizontal">

                    <div class="form-group">
                        <div class="col-md-5 control-label">
                            <?php echo $this->html('form.label', 'COM_EASYDISCUSS_WRAPPERCLASS_SFX'); ?>
                        </div>
                        <div class="col-md-7">
                            <input type="text" name="layout_wrapper_sfx" class="form-control"  value="<?php echo $this->config->get('layout_wrapper_sfx' , '' );?>" />
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="col-md-5 control-label">
                            <?php echo $this->html('form.label', 'COM_EASYDISCUSS_ENABLE_RESPONSIVE'); ?>
                        </div>
                        <div class="col-md-7">
                        <?php echo $this->html('form.boolean', 'main_responsive', $this->config->get('main_responsive'));?>
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="col-md-5 control-label">
                            <?php echo $this->html('form.label', 'COM_EASYDISCUSS_ENABLE_BOARD_STATISTICS'); ?>
                        </div>
                        <div class="col-md-7">
                            <?php echo $this->html('form.boolean', 'layout_board_stats', $this->config->get('layout_board_stats'));?>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <div class="col-md-5 control-label">
                            <?php echo $this->html('form.label', 'COM_EASYDISCUSS_LIST_LIMIT'); ?>
                        </div>
                        <div class="col-md-7">
                            <input type="text" name="layout_list_limit" value="<?php echo $this->config->get('layout_list_limit' );?>" size="5" style="text-align:center;" class="form-control form-control-sm text-center" />
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="col-md-5 control-label">
                            <?php echo $this->html('form.label', 'COM_EASYDISCUSS_NUMBER_OF_DAYS_A_POST_STAY_AS_NEW'); ?>
                        </div>
                        <div class="col-md-7">
                            <input type="text" name="layout_daystostaynew"  class="form-control form-control-sm text-center" value="<?php echo $this->config->get('layout_daystostaynew' , '7' );?>" />
                        </div>
                    </div>

                </div>
            </div>
        </div>
	</div>

	<div class="col-md-6">

        <div class="panel">
            <?php echo $this->html('panel.head', 'COM_EASYDISCUSS_SETTINGS_LAYOUT_LOCALIZATION'); ?>

            <div class="panel-body">
                <div class="form-horizontal">
                    <div class="form-group">
                        <div class="col-md-5 control-label">
                            <?php echo $this->html('form.label', 'COM_EASYDISCUSS_SETTINGS_ZERO_AS_PLURAL'); ?>
                        </div>
                        <div class="col-md-7">
                            <?php echo $this->html('form.boolean', 'layout_zero_as_plural', $this->config->get('layout_zero_as_plural'));?>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="col-md-5 control-label">
                            <?php echo $this->html('form.label', 'COM_EASYDISCUSS_DATE_FORMAT'); ?>
                        </div>
                        <div class="col-md-7">
                            <input type="text" name="layout_dateformat" class="form-control" value="<?php echo $this->config->get('layout_dateformat' , ED::getDefaultConfigValue('layout_dateformat'));?>" />
                            <div class="mt-5">
                                <a href="https://stackideas.com/docs/easydiscuss/administrators/how-tos/date-format" target="_blank" class="extra_text"><?php echo JText::_('COM_EASYDISCUSS_WHAT_IS_THIS'); ?></a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>



        <div class="panel">
            <?php echo $this->html('panel.head', 'COM_EASYDISCUSS_POWERED_BY'); ?>

            <div class="panel-body">
                <div class="form-horizontal">
                    <div class="form-group">
                        <div class="col-md-5 control-label">
                            <?php echo $this->html('form.label', 'COM_EASYDISCUSS_ENABLE_POWERED_BY'); ?>
                        </div>
                        <div class="col-md-7">
                            <?php echo $this->html('form.boolean', 'main_copyright_link_back', $this->config->get('main_copyright_link_back')); ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>

	</div>
</div>
