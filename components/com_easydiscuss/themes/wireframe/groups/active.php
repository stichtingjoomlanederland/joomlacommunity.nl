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
<div class="ed-forums-cat-header t-lg-mb--xl">
    <div class="pull-left">
        <div class="ed-forums-cat-header__title">
            <a href="<?php echo $activeGroup->getPermalink(); ?>">
                <img src="<?php echo $activeGroup->getAvatar();?>" width="48" alt="<?php echo $this->html('string.escape', $activeGroup->title);?>" />
                <?php echo strtoupper($activeGroup->title);?>
            </a>
        </div>

        <ol class="g-list-inline g-list-inline--delimited ed-forums-cat-header__breadcrumb">
            <li>
                <a href="<?php echo EDR::getGroupsRoute(); ?>"><?php echo JText::_('COM_EASYDISCUSS_TOOLBAR_FORUMS'); ?></a>
            </li>
        </ol>

    </div>
    <!-- WIP - Can post or cannot post -->
    <?php if (true) { ?>
    <a class="btn btn-primary pull-right ed-forums-cat-header__btn" href="<?php echo EDR::_('view=ask&group_id=' . $activeGroup->id);?>">
        <i class="fa fa-pencil"></i>&nbsp; <?php echo JText::_('COM_EASYDISCUSS_NEW_POST');?>
    </a>
    <?php } ?>
</div>
