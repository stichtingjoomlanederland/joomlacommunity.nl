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

<?php if ($holiday->id) { ?>
<h2 class="ed-page-title"><?php echo JText::_('COM_EASYDISCUSS_EDIT_HOLIDAY'); ?></h2>
<?php } else { ?>
<h2 class="ed-page-title"><?php echo JText::_('COM_EASYDISCUSS_NEW_HOLIDAY'); ?></h2>
<?php } ?>

<form autocomplete="off" action="<?php echo JRoute::_('index.php');?>" method="post" enctype="multipart/form-data" data-ed-holiday-form>
<div class="ed-dashboard-form">
    <div class="ed-dashboard-form__hd">
        <div class="o-col-sm">
            <i class="fa fa-calendar-o"></i> <?php echo JText::_('COM_EASYDISCUSS_HOLIDAY'); ?>    
        </div>
        <div class="o-col-sm">
            <a href="<?php echo EDR::_('view=dashboard'); ?>" class="btn btn-default btn-sm pull-right"><?php echo JText::_('COM_EASYDISCUSS_BUTTON_CANCEL'); ?></a>
        </div>  
    </div>
        <div class="ed-dashboard-form__bd">
            <div class="form-group">
                <label for="exampleInputEmail1"><?php echo JText::_('COM_EASYDISCUSS_HOLIDAY_TITLE_FIELD'); ?></label>
                <input type="text" class="form-control" name="title" id="title" placeholder="" data-ed-holiday-title value="<?php echo $this->html('string.escape', $holiday->title); ?>" /> 
            </div>

            <div class="form-group">
                <label for="exampleInputEmail1"><?php echo JText::_('COM_EASYDISCUSS_HOLIDAY_DESCRIPTION_FIELD'); ?></label>
                <textarea name="description" id="description" class="form-control" rows="6" data-ed-holiday-description><?php echo $holiday->description; ?></textarea>
            </div>

            <div class="form-group">
                <div class="o-grid">
                    <div class="o-grid__cell o-grid__cell--auto-size">
                        <label for=""><?php echo JText::_('COM_EASYDISCUSS_HOLIDAY_PUBLISH_FIELD'); ?>:</label>
                    </div>
                    <div class="o-grid__cell">
                        <div class="o-switch t-lg-ml--lg t-xs-ml--no">
                            <input type="checkbox" name="published" class="o-switch__checkbox" id="published" data-ed-holiday-published <?php echo $holiday->published? 'checked':'' ?> >
                            <label class="o-switch__label" for="published">
                                <span class="o-switch__inner"></span>
                                <span class="o-switch__switch"></span>
                            </label>
                        </div>
                    </div>
                </div>
            </div>

            <div class="o-row">
                <div class="o-col o-col--top t-lg-pr--md t-xs-pr--no">
                    <div class="form-group ed-dashboard-j-calendar">
                        <div class="o-grid">
                            <div class="o-grid__cell o-grid__cell--auto-size t-lg-pr--lg">
                                <label for="start" class="t-lg-mt--md"><?php echo JText::_('COM_EASYDISCUSS_HOLIDAY_START_DATE_FIELD'); ?>:</label>
                            </div>
                            <div class="o-grid__cell">
                                <?php echo JHTML::_('calendar', $holiday->start, 'start', 'start', '%Y-%m-%d', array('data-ed-holiday-start' => '', 'class' => 'form-control')); ?>        
                            </div>
                        </div>
                    </div>
                    
                </div>
                <div class="o-col o-col--top">
                    <div class="form-group ed-dashboard-j-calendar">
                        <div class="o-grid">
                            <div class="o-grid__cell o-grid__cell--auto-size t-lg-pr--lg">
                                <label for="end" class="t-lg-mt--md"><?php echo JText::_('COM_EASYDISCUSS_HOLIDAY_END_DATE_FIELD'); ?>:</label>
                            </div>
                            <div class="o-grid__cell">
                                <?php echo JHTML::_('calendar', $holiday->end, 'end', 'end', '%Y-%m-%d', array('data-ed-holiday-end' => '', 'class' => 'form-control')); ?>
                            </div>
                        </div>
                        
                        
                    </div>
                    
                </div>
            </div>
            <div class="o-row">
                <a href="javascript:void(0);" class="btn btn-primary pull-right" data-ed-submit-button>
                    <?php echo $holiday->id? JText::_('COM_EASYDISCUSS_HOLIDAY_UPDATE_BUTTON') : JText::_('COM_EASYDISCUSS_HOLIDAY_ADD_BUTTON'); ?>
                </a>
            </div>
        </div>
        <?php echo $this->html('form.hidden', 'holiday', 'dashboard', 'save'); ?>
        <input type="hidden" name="id" id="id" value="<?php echo $holiday->id; ?>" />
</div>
</form>