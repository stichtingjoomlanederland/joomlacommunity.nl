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
<style type="text/css">
.fields-list .fields-option + .fields-option {
    margin-top: 5px;
}

.fields-list .fields-option:first-child .input-group {
    width: 100%;
}

.fields-list .fields-option:first-child .input-group .form-control{
    border-bottom-right-radius: 2px !important;
    border-top-right-radius: 2px !important;
}

.fields-list .fields-option:first-child .input-group-btn {
    display: none !important;
}
</style>

<div class="panel">
    <?php echo $this->html('panel.head', 'COM_EASYDISCUSS_CUSTOMFIELDS_ADVANCE'); ?>

    <div class="panel-body">
        <div class="form-horizontal">
            <div class="form-group">
                <div class="col-md-5 control-label advanceOptionsTitle form-row-label">
                    <?php echo $this->html('form.label', 'COM_EASYDISCUSS_CUSTOMFIELDS_TOOLTIPS'); ?>
                </div>

                <div class="col-md-7">
                    <input type="text" name="tooltips" class="form-control" placeholder="<?php echo JText::_('COM_EASYDISCUSS_CUSTOMFIELDS_TOOLTIPS_PLACEHOLDER');?>" value="<?php echo $field->tooltips;?>" />
                </div>
            </div>

            <?php if ($field->hasOptions()) { ?>
            <div class="form-group">
                <div class="col-md-5 control-label advanceOptionsTitle form-row-label">
                    <?php echo $this->html('form.label', 'COM_EASYDISCUSS_CUSTOMFIELDS_OPTIONS'); ?>
                </div>

                <div class="col-md-7">
                    <div class="fields-list" data-ed-field-options>
                        <?php if ($field->getOptions()) { ?>
                            <?php $i = 0; ?>
                            <?php foreach ($field->getOptions() as $option) { ?>
                            <div class="fields-option" data-ed-field-option <?php echo $i == 0 ? ' data-ed-field-option-initial' : '';?>>
                                <div class="input-group">
                                    <input type="text" name="options[]" class="form-control" placeholder="<?php echo JText::_('COM_EASYDISCUSS_CUSTOMFIELDS_OPTIONS_PLACEHOLDER', true);?>" value="<?php echo $this->html('string.escape', $option);?>" />

                                    <span class="input-group-btn">
                                        <a href="javascript:void(0);" class="btn btn-danger" data-ed-field-remove-option><i class="fa fa-times"></i></a>
                                    </span>
                                </div>
                            </div>
                            <?php $i++; ?>
                            <?php } ?>
                        <?php } else { ?>
                            <div class="fields-option" data-ed-field-option-initial data-ed-field-option>
                                <div class="input-group">
                                    <input type="text" name="options[]" class="form-control" placeholder="<?php echo JText::_('COM_EASYDISCUSS_CUSTOMFIELDS_OPTIONS_PLACEHOLDER', true);?>" />

                                    <span class="input-group-btn">
                                        <a href="javascript:void(0);" class="btn btn-danger" data-ed-field-remove-option><i class="fa fa-times"></i></a>
                                    </span>
                                </div>
                            </div>
                        <?php } ?>
                    </div>

                    <button type="button" class="btn btn-default t-lg-mt--xl" data-ed-field-add-option><?php echo JText::_('COM_EASYDISCUSS_CUSTOMFIELDS_ADD_OPTION');?></button>
                </div>
            </div>

            <?php } ?>
        </div>
    </div>
</div>

