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
<div class="ed-forums-cat-header t-lg-mb--lg">

    <div class="pull-left">
        <div class="o-flag">
            <div class="o-flag__image o-flag--top">
                <a href="" class="o-avatar o-avatar--md">
                    <img src="<?php echo $activeCategory->getAvatar();?>" width="48" alt="<?php echo $this->html('string.escape', $activeCategory->getTitle());?>" />
                </a>
            </div>
            <div class="o-flag__body">
                <a href="" class="ed-forums-cat-header__title">
                    <?php echo JString::strtoupper($activeCategory->getTitle());?>
                </a>
                <?php if ($activeCategory->canPost()) { ?>
                <a class="btn btn-primary pull-right ed-forums-cat-header__btn" href="<?php echo EDR::_('view=ask&category=' . $activeCategory->id);?>">
                    <i class="fa fa-pencil t-lg-mr--sm"></i> <?php echo JText::_('COM_EASYDISCUSS_NEW_POST');?>
                </a>
                <?php } ?>
            </div>
        </div>

    </div>

    <ol class="g-list-inline g-list-inline--delimited ed-forums-cat-header__breadcrumb">
        <li>
            <a href="<?php echo EDR::getForumsRoute(); ?>">
                <?php echo JText::_('COM_EASYDISCUSS_TOOLBAR_FORUMS'); ?>
            </a>
        </li>

        <?php foreach ($breadcrumbs as $breadcrumb) { ?>
        <li data-breadcrumb="▸">
            <?php if ($breadcrumb->id == $activeCategory->id && !$listing) { ?>
                <?php echo $breadcrumb->title;?>
            <?php } else { ?>
                <a href="<?php echo $breadcrumb->link; ?>"><?php echo $breadcrumb->title; ?></a>
            <?php } ?>
        </li>
        <?php } ?>

        <?php if ($listing) { ?>
            <li data-breadcrumb="▸">
                <?php echo JText::_('COM_EASYDISCUSS_FORUMS_BREADCRUMB_LAYOUT'); ?>
            </li>
        <?php } ?>
    </ol>
</div>

<?php if (!$activeCategory->container && ($this->config->get('main_rss') || $this->config->get('main_ed_categorysubscription'))) { ?>
<div class="ed-subscribe t-lg-mb--lg">
    <?php if ($this->config->get('main_rss')) { ?>
    <a href="<?php echo $activeCategory->getRSSPermalink();?>" class="t-lg-mr--md" target="_blank">
        <i class="fa fa-rss-square ed-subscribe__icon t-lg-mr--sm"></i>&nbsp;<?php echo JText::_('COM_EASYDISCUSS_SUBSCRIBE_VIA_RSS'); ?>
    </a>
    <?php } ?>

    <?php if($this->config->get('main_ed_categorysubscription')) { ?>
    <?php echo ED::subscription()->html($this->my->id, $activeCategory->id, 'category'); ?>
    <?php } ?>
</div>
<?php } ?>





