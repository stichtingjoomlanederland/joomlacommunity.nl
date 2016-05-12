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
<h2 class="ed-page-title"><?php echo JText::_('COM_EASYDISCUSS_NEW_HOLIDAY'); ?></h2>
<form autocomplete="off" action="<?php echo JRoute::_('index.php');?>" method="post" enctype="multipart/form-data" data-ed-holiday-form>
<div class="ed-dashboard-form">
    <div class="ed-dashboard-form__hd">
        <div class="o-col-sm">
            <i class="fa fa-calendar-o"></i> <?php echo JText::_('COM_EASYDISCUSS_HOLIDAY'); ?>    
        </div>
        <div class="o-col-sm">
            <a href="<?php echo EDR::_('view=dashboard'); ?>" class="btn btn-text pull-right"><?php echo JText::_('COM_EASYDISCUSS_BUTTON_CANCEL'); ?></a>
        </div>  
    </div>
        <div class="ed-dashboard-form__bd">
            <div class="form-group">
                <label for="exampleInputEmail1"><?php echo JText::_('COM_EASYDISCUSS_HOLIDAY_TITLE_FIELD'); ?></label>
                <input type="text" class="form-control" name="title" id="title" placeholder="" data-ed-holiday-title> 
            </div>

            <div class="form-group">
                <label for="exampleInputEmail1"><?php echo JText::_('COM_EASYDISCUSS_HOLIDAY_DESCRIPTION_FIELD'); ?></label>
                <textarea name="description" id="description" class="form-control" rows="6" data-ed-holiday-description></textarea>
            </div>

            <div class="form-group">
                <div class="o-grid">
                    <div class="o-grid__cell o-grid__cell--auto-size">
                        <label for=""><?php echo JText::_('COM_EASYDISCUSS_HOLIDAY_NOTIFICATION_FIELD'); ?>:</label>
                    </div>
                    <div class="o-grid__cell">
                        <div class="o-switch t-lg-ml--lg t-xs-ml--no">
                            <input type="checkbox" name="notification" class="o-switch__checkbox" id="notification" data-ed-holiday-notification checked>
                            <label class="o-switch__label" for="notification">
                                <span class="o-switch__inner"></span>
                                <span class="o-switch__switch"></span>
                            </label>
                        </div>
                    </div>
                </div>
            </div>

            <div class="o-row">
                <div class="o-col o-col--top t-lg-pr--lg t-xs-pr--no">
                    <div class="form-group">
                        <label for="startDate"><?php echo JText::_('COM_EASYDISCUSS_HOLIDAY_START_DATE_FIELD'); ?>:</label>

                        <?php echo JHTML::_('calendar', '', 'startDate', 'startDate', '%Y-%m-%d', array('data-ed-holiday-start' => '', 'class' => 'form-control')); ?>
                        <input type="text" class="form-control" name="startDate" id="startDate" placeholder="" data-ed-holiday-start> 
                    </div>
                    
                </div>
                <div class="o-col o-col--top">
                    <div class="form-group">
                        <label for="exampleInputEmail1"><?php echo JText::_('COM_EASYDISCUSS_HOLIDAY_END_DATE_FIELD'); ?>:</label>
                        <input type="text" class="form-control" name="endDate" id="endDate" placeholder="" data-ed-holiday-end> 
                    </div>
                    
                </div>
            </div>
            <div class="o-row">
                <a href="javascript:void(0);" class="btn btn-primary pull-right" data-ed-submit-button><?php echo JText::_('COM_EASYDISCUSS_HOLIDAY_CREATE_BUTTON'); ?></a>
            </div>
        </div>
        <?php echo $this->html('form.hidden', 'holiday', 'dashboard', 'save'); ?>
</div>
</form>