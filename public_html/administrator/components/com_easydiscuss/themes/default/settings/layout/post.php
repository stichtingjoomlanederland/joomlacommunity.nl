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
            <?php echo $this->html('panel.head', 'COM_EASYDISCUSS_LAYOUT_POST'); ?>

            <div class="panel-body">
                <div class="form-horizontal">
                    <div class="form-group">
                        <div class="col-md-5 control-label">
                            <?php echo $this->html('form.label', 'COM_EASYDISCUSS_REPLIES_ENABLE_PAGINATION'); ?>
                        </div>
                        <div class="col-md-7">
                            <?php echo $this->html('form.boolean', 'layout_replies_pagination', $this->config->get('layout_replies_pagination'));?>
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="col-md-5 control-label">
                            <?php echo $this->html('form.label', 'COM_EASYDISCUSS_REPLIES_LIST_LIMIT'); ?>
                        </div>
                        <div class="col-md-7">
                            <input type="text" name="layout_replies_list_limit" value="<?php echo $this->config->get('layout_replies_list_limit' );?>" size="5" style="text-align:center;" class="form-control form-control-sm text-center" />
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="col-md-5 control-label">
                            <?php echo $this->html('form.label', 'COM_EASYDISCUSS_ENABLE_PRINT_BUTTON'); ?>
                        </div>
                        <div class="col-md-7">
                            <?php echo $this->html('form.boolean', 'main_enable_print', $this->config->get('main_enable_print')); ?>
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="col-md-5 control-label">
                            <?php echo $this->html('form.label', 'COM_EASYDISCUSS_ENABLE_WHOS_VIEWING'); ?>
                        </div>
                        <div class="col-md-7">
                            <?php echo $this->html('form.boolean', 'main_viewingpage', $this->config->get('main_viewingpage')); ?>
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="col-md-5 control-label">
                            <?php echo $this->html('form.label', 'COM_EASYDISCUSS_AUTO_MINIMISE_POST_IF_HIT_MINIMUM_VOTE'); ?>
                        </div>
                        <div class="col-md-7">
                            <input type="text" name="layout_autominimisepost" class="form-control form-control-sm text-center" value="<?php echo $this->config->get('layout_autominimisepost' , '5' );?>" />
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="col-md-5 control-label">
                            <?php echo $this->html('form.label', 'COM_EASYDISCUSS_REPLIES_SORTING_TAB'); ?>
                        </div>
                        <div class="col-md-7">
                            <?php
                                $filterFormat = array();
                                $filterFormat[] = JHTML::_('select.option', 'oldest', JText::_( 'COM_EASYDISCUSS_REPLIES_SORTING_BY_OLDEST' ) );
                                $filterFormat[] = JHTML::_('select.option', 'latest', JText::_( 'COM_EASYDISCUSS_REPLIES_SORTING_BY_LATEST' ) );
                                $filterFormat[] = JHTML::_('select.option', 'voted', JText::_( 'COM_EASYDISCUSS_REPLIES_SORTING_BY_VOTED' ) );
                                $filterFormat[] = JHTML::_('select.option', 'likes', JText::_( 'COM_EASYDISCUSS_REPLIES_SORTING_BY_LIKES' ) );
                                $showdet = JHTML::_('select.genericlist', $filterFormat, 'layout_replies_sorting', 'class="form-control"  ', 'value', 'text', $this->config->get('layout_replies_sorting' , 'latest' ) );
                                echo $showdet;
                            ?>
                        </div>
                    </div>


                    <div class="form-group">
                        <div class="col-md-5 control-label">
                            <?php echo $this->html('form.label', 'COM_EASYDISCUSS_ENABLE_POST_NAVIGATION'); ?>
                        </div>
                        <div class="col-md-7">
                            <?php echo $this->html('form.boolean', 'layout_postnavigation', $this->config->get('layout_postnavigation')); ?>
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="col-md-5 control-label">
                            <?php echo $this->html('form.label', 'COM_EASYDISCUSS_ENABLE_POST_NAVIGATION_TYPE'); ?>
                        </div>
                        <div class="col-md-7">
                            <?php
                                $filterFormat = array();
                                $filterFormat[] = JHTML::_('select.option', 'sitewide', JText::_( 'COM_EASYDISCUSS_ENABLE_POST_NAVIGATION_TYPE_SITEWIDE' ) );
                                $filterFormat[] = JHTML::_('select.option', 'category', JText::_( 'COM_EASYDISCUSS_ENABLE_POST_NAVIGATION_TYPE_CATEGORY' ) );
                                $showdet = JHTML::_('select.genericlist', $filterFormat, 'layout_postnavigation_type', 'class="form-control"  ', 'value', 'text', $this->config->get('layout_postnavigation_type' , 'sitewide' ) );
                                echo $showdet;
                            ?>
                        </div>
                    </div>

                </div>
            </div>
        </div>
	</div>

	<div class="col-md-6">

	</div>
</div>
