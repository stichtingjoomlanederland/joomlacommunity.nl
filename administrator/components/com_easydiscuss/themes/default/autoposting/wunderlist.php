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
<form name="adminForm" id="adminForm" action="index.php" method="post" class="adminForm">
    <div class="row">
        <div class="col-md-6">
            <div class="panel">
                <?php echo $this->html('panel.head', 'COM_EASYDISCUSS_WUNDERLIST_INTEGRATIONS'); ?>

                <div class="panel-body">
                    <?php echo $this->html('panel.info', 'COM_EASYDISCUSS_WUNDERLIST_INFO'); ?>

                    <div class="form-horizontal">
                        <div class="form-group">
                            <div class="col-md-5 control-label">
                                <?php echo $this->html('form.label', 'COM_EASYDISCUSS_ENABLE_WUNDERLIST'); ?>
                            </div>

                            <div class="col-md-7">
                                <?php echo $this->html('form.boolean', 'main_autopost_wunderlist', $this->config->get('main_autopost_wunderlist')); ?>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="col-md-5 control-label">
                                <?php echo $this->html('form.label', 'COM_EASYDISCUSS_AUTOPOST_WUNDERLIST_CLIENT_ID'); ?>
                            </div>
                            <div class="col-md-7">
                                <?php echo $this->html('form.textbox', 'main_autopost_wunderlist_id', $this->config->get('main_autopost_wunderlist_id')); ?>
                                <div class="small">
                                    <a href="https://stackideas.com/docs/easydiscuss/administrators/autoposting/wunderlist-application" target="_blank"><?php echo JText::_('COM_EASYDISCUSS_WHAT_IS_THIS');?></a>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="col-md-5 control-label">
                                <?php echo $this->html('form.label', 'COM_EASYDISCUSS_AUTOPOST_WUNDERLIST_CLIENT_SECRET'); ?>
                            </div>
                            <div class="col-md-7">
                                <?php echo $this->html('form.textbox', 'main_autopost_wunderlist_secret', $this->config->get('main_autopost_wunderlist_secret')); ?>
                                <div class="small">
                                    <a href="https://stackideas.com/docs/easydiscuss/administrators/autoposting/wunderlist-application" target="_blank"><?php echo JText::_('COM_EASYDISCUSS_WHAT_IS_THIS');?></a>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">

                            <div class="col-md-5 control-label">
                                <?php echo $this->html('form.label', 'COM_EASYDISCUSS_AUTOPOST_WUNDERLIST_SIGN_IN'); ?>
                            </div>

                            <div class="col-md-7">
                                <?php if ($associated) { ?>
                                <a href="<?php echo JRoute::_('index.php?option=com_easydiscuss&controller=autoposting&task=revoke&type=wunderlist');?>" class="btn btn-danger">
                                    <?php echo JText::_('COM_EASYDISCUSS_AUTOPOST_REVOKE_ACCCESS');?>
                                </a>
                                <?php } else { ?>
                                <a href="javascript:void(0)" class="btn btn-primary" data-wunderlist-login>
                                    <i class="fa fa-star"></i>&nbsp; <?php echo JText::_('COM_EASYDISCUSS_SIGN_IN_WITH_WUNDERLIST');?>
                                </a>
                                <?php } ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-6">

            <div class="panel">
                <?php echo $this->html('panel.head', 'COM_EASYDISCUSS_WUNDERLIST_AUTOPOST_LISTS'); ?>

                <div class="panel-body">

                    <?php if ($associated) { ?>
                    <div class="form-horizontal">
                        <div class="form-group">                        
                            <div class="col-md-5 control-label">
                                <?php echo $this->html('form.label', 'COM_EASYDISCUSS_WUNDERLIST_LISTS'); ?>
                            </div>

                            <div class="col-md-7 control-label">
                                <?php if ($lists) { ?>
                                <select name="main_autopost_wunderlist_list_id[]" class="form-control" multiple="multiple" size="10">
                                    <?php foreach ($lists as $list) { ?>
                                    <option value="<?php echo $list->id;?>" <?php echo in_array($list->id, $storedLists) ? ' selected="selected"' : '';?>>
                                        <?php echo $list->title;?>
                                    </option>
                                    <?php } ?>
                                </select>

                                <p class="mt-5 small"><?php echo JText::_('COM_EASYDISCUSS_AUTOPOST_SELECT_MULTIPLE'); ?></p>
                                <?php } else { ?>
                                    <p><?php echo JText::_('COM_EASYDISCUSS_LINKEDIN_AUTOPOST_NO_COMPANIES_YET'); ?></p>
                                <?php } ?>
                            </div>
                        </div>
                    </div>
                    <?php } else { ?>
                        <p class="small"><?php echo JText::_('COM_EASYDISCUSS_WUNDERLIST_AUTOPOST_SIGNIN_FIRST');?></p>
                    <?php } ?>
                </div>
            </div>
        </div>
    </div>

    <input type="hidden" name="task" value="save" />
    <input type="hidden" name="type" value="wunderlist" />
    <input type="hidden" name="controller" value="autoposting" />
    <input type="hidden" name="option" value="com_easydiscuss" />
</form>