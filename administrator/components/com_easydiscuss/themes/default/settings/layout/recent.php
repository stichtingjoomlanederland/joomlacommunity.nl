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
            <?php echo $this->html('panel.head', 'COM_EASYDISCUSS_LAYOUT_RECENT_VIEW'); ?>

            <div class="panel-body">
                <div class="form-horizontal">
                    <div class="form-group">
                        <div class="col-md-5 control-label">
                            <?php echo $this->html('form.label', 'COM_EASYDISCUSS_ENABLE_INTROTEXT'); ?>
                        </div>
                        <div class="col-md-7">
                            <?php echo $this->html('form.boolean', 'layout_enableintrotext', $this->config->get('layout_enableintrotext'));?>
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="col-md-5 control-label">
                            <?php echo $this->html('form.label', 'COM_EASYDISCUSS_INTROTEXT_LENGTH'); ?>
                        </div>
                        <div class="col-md-7">
                            <input type="text" name="layout_introtextlength"  class="form-control form-control-sm text-center" value="<?php echo $this->config->get('layout_introtextlength' , '200' );?>" />
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="col-md-5 control-label">
                            <?php echo $this->html('form.label', 'COM_EASYDISCUSS_ENABLE_FILTER_UNRESOLVED'); ?>
                        </div>
                        <div class="col-md-7">
                            <?php echo $this->html('form.boolean', 'layout_enablefilter_unresolved', $this->config->get('layout_enablefilter_unresolved'));?>
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="col-md-5 control-label">
                            <?php echo $this->html('form.label', 'COM_EASYDISCUSS_ENABLE_FILTER_UNANSWERED'); ?>
                        </div>
                        <div class="col-md-7">
                            <?php echo $this->html('form.boolean', 'layout_enablefilter_unanswered', $this->config->get('layout_enablefilter_unanswered'));?>
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="col-md-5 control-label">
                            <?php echo $this->html('form.label', 'COM_EASYDISCUSS_ENABLE_FILTER_RESOLVED'); ?>
                        </div>
                        <div class="col-md-7">
                            <?php echo $this->html('form.boolean', 'layout_enablefilter_resolved', $this->config->get('layout_enablefilter_resolved'));?>
                        </div>
                    </div>

                </div>
            </div>
        </div>
        
	</div>

	<div class="col-md-6">
        <div class="panel">
            <?php echo $this->html('panel.head', 'COM_EASYDISCUSS_FEATURED_FRONTPAGE_LISTING'); ?>

            <div id="option01" class="panel-body">
                <div class="form-horizontal">
                <div class="form-group">
                        <div class="col-md-5 control-label">
                            <?php echo $this->html('form.label', 'COM_EASYDISCUSS_FEATURED_POSTS_FRONTPAGE'); ?>
                        </div>
                        <div class="col-md-7">
                            <?php echo $this->html('form.boolean', 'layout_featuredpost_frontpage', $this->config->get('layout_featuredpost_frontpage'));?>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="col-md-5 control-label">
                            <?php echo $this->html('form.label', 'COM_EASYDISCUSS_FEATURED_POSTS_LIMIT'); ?>
                        </div>
                        <div class="col-md-7">
                            <input type="text" class="form-control form-control-sm text-center" name="layout_featuredpost_limit" value="<?php echo $this->config->get('layout_featuredpost_limit', '5' );?>" />
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="col-md-5 control-label">
                            <?php echo $this->html('form.label', 'COM_EASYDISCUSS_FEATURED_SORTING'); ?>
                        </div>
                        <div class="col-md-7">
                            <?php
                                $featuredOrdering = array();
                                $featuredOrdering[] = JHTML::_('select.option', 'date_latest', JText::_( 'COM_EASYDISCUSS_FEATURED_ORDER_DATE_LATEST' ) );
                                $featuredOrdering[] = JHTML::_('select.option', 'date_oldest', JText::_( 'COM_EASYDISCUSS_FEATURED_ORDER_DATE_OLDEST' ) );
                                $featuredOrdering[] = JHTML::_('select.option', 'order_asc', JText::_( 'COM_EASYDISCUSS_FEATURED_ORDER_ORDER_ASC' ) );
                                $featuredOrdering[] = JHTML::_('select.option', 'order_desc', JText::_( 'COM_EASYDISCUSS_FEATURED_ORDER_ORDER_DESC' ) );
                                $showdet = JHTML::_('select.genericlist', $featuredOrdering, 'layout_featuredpost_sort', 'class="form-control"  ', 'value', 'text', $this->config->get('layout_featuredpost_sort' , 'date_latest' ) );
                                echo $showdet;
                            ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
	</div>
</div>
