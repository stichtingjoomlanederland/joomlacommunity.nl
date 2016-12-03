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
<div id="permissions" class="tab-pane">
<div class="row">
    
    <div class="col-md-3">
        <ul class="o-tabs o-tabs--side" data-behavior="sample_code">
            <li class="o-tabs__item active">
                <a data-ed-toggle="tab" href="#cat-view" class="o-tabs__link">
                    View Discussion
                </a>
            </li>
            <li class="o-tabs__item">
                <a data-ed-toggle="tab" href="#cat-select" class="o-tabs__link">
                    Create Discussion
                </a>
            </li>
            <li class="o-tabs__item">
                <a data-ed-toggle="tab" href="#cat-viewreply" class="o-tabs__link">
                    View Replies
                </a>
            </li>
            <li class="o-tabs__item">
                <a data-ed-toggle="tab" href="#cat-reply" class="o-tabs__link">
                    Reply to Discussion
                </a>
            </li>
            <li class="o-tabs__item">
                <a data-ed-toggle="tab" href="#cat-moderate" class="o-tabs__link">
                    Moderate
                </a>
            </li>
        </ul>
    </div>
    <div class="col-md-6">
        <div class="tab-content">
            <div id="cat-view" class="tab-pane active">
                <div class="panel">
                    <?php echo $this->html('panel.head', 'COM_EASYDISCUSS_CATEGORIES_ACL_VIEW'); ?>

                    <div class="panel-body">
                        <?php echo $this->html('form.usergroups', 'acl_group_view', $category->getAssignedGroups('view'));?>
                    </div>
                </div>
            </div>
            <div id="cat-select" class="tab-pane">
                <div class="panel">
                    <?php echo $this->html('panel.head', 'COM_EASYDISCUSS_CATEGORIES_ACL_SELECT'); ?>

                    <div class="panel-body">
                        <?php echo $this->html('form.usergroups', 'acl_group_select', $category->getAssignedGroups('select'));?>
                    </div>
                </div>
            </div>
            <div id="cat-viewreply" class="tab-pane">
                <div class="panel">
                    <?php echo $this->html('panel.head', 'COM_EASYDISCUSS_CATEGORIES_ACL_VIEWREPLY'); ?>

                    <div class="panel-body">
                        <?php echo $this->html('form.usergroups', 'acl_group_viewreply', $category->getAssignedGroups('viewreply'));?>
                    </div>
                </div>
            </div>
            <div id="cat-reply" class="tab-pane">
                <div class="panel">
                    <?php echo $this->html('panel.head', 'COM_EASYDISCUSS_CATEGORIES_ACL_REPLY'); ?>

                    <div class="panel-body">
                        <?php echo $this->html('form.usergroups', 'acl_group_reply', $category->getAssignedGroups('reply'));?>
                    </div>
                </div>
            </div>
            <div id="cat-moderate" class="tab-pane">
                <div class="panel">
                    <?php echo $this->html('panel.head', 'COM_EASYDISCUSS_CATEGORIES_ACL_MODERATE'); ?>

                    <div class="panel-body">
                        <?php echo $this->html('form.usergroups', 'acl_group_moderate', $category->getAssignedGroups('moderate'));?>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
</div>
</div>