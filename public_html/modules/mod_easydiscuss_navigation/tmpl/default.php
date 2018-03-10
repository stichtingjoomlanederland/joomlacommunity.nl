<?php
/**
 * @package		mod_easydiscuss_navigation
 * @copyright	Copyright (C) 2010 Stack Ideas Private Limited. All rights reserved.
 * @license		GNU/GPL, see LICENSE.php
 *
 * EasyDiscuss is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * See COPYRIGHT.php for copyright notices and details.
 */
defined('_JEXEC') or die('Restricted access');
?>
<script type="text/javascript">
<?php if ($my->id > 0 && $params->get('display_notification_button')) { ?>
ed.require(['edq', 'site/src/toolbar', 'site/src/discuss'], function($, App, discuss) {

    var toolbarSelector = '[data-mod-navigation]';

    // Implement the abstract
    App.execute(toolbarSelector, {
        "notifications": {
            "interval": <?php echo $config->get('main_notifications_interval') * 1000; ?>,
            "enabled": <?php echo $my->id && $config->get('main_notifications') ? 'true' : 'false';?>
        }
    });
});
<?php } ?>
</script>
<div id="ed" class="ed-mod m-navigation ">
    <div class="o-grid t-lg-mb--md">
        <div class="o-grid__cell">
            <div class="o-flag">
                <div class="o-flag__image o-flag--top">
                    <div class="m-navigation-title"><?php echo JText::_('MOD_NAVIGATION_FORUMS');?>:</div>
                </div>
                <div class="o-flag__body">
                    <ol class="g-list-inline g-list-inline--dashed ed-navbar__footer-submenu" data-ed-navbar-submenu>
                        <li>
                            <a href="<?php echo EDR::_('view=categories'); ?>"><?php echo JText::_('MOD_NAVIGATION_ALL'); ?></a>
                        </li>

                        <?php foreach($categories as $category) { ?>
                            <?php
                                $totalNew = ($my->id > 0) ? $category->getUnreadCount() : '0';
                                $postCount = $category->getTotalPosts();

                                if (!$params->get('display_empty_category') && $postCount <= 0) {
                                    continue;
                                }
                            ?>
                                <li class="<?php echo ($totalNew) ? 'has-counter' : ''; ?> <?php echo ($active == $category->id) ? ' is-active' : ''; ?>">
                                    <a href="<?php echo $category->getPermalink();?>" class="m-navigation__link">
                                        <?php echo JText::_($category->getTitle()); ?>
                                        <span class="m-navigation__link-bubble"><?php echo $totalNew;?></span>
                                    </a>
                                </li>
                        <?php } ?>
                    </ol>
                </div>
            </div>
        </div>
        <?php if ($my->id > 0 && $params->get('display_notification_button')) { ?>
            <div class="o-grid__cell o-grid__cell--auto-size">
                <a href="javascript:void(0);" class="btn btn-primary btn-xs pull-right <?php echo $notificationsCount ? 'has-new' : '';?>"
                    data-ed-notifications-wrapper
                    data-ed-popbox="ajax://site/views/notifications/popbox"
                    data-ed-popbox-position="<?php echo JFactory::getDocument()->getDirection() == 'rtl' ? 'bottom-left' : 'bottom-right';?>"
                    data-ed-popbox-toggle="click"
                    data-ed-popbox-offset="4"
                    data-ed-popbox-type="navbar-notifications"
                    data-ed-popbox-component="popbox--navbar"
                    data-ed-popbox-cache="0"
                    data-ed-provide="tooltip"
                    data-original-title="<?php echo JText::_('MOD_NAVIGATION_NOTIFICATIONS');?>"
                >
                <i class="fa fa-bell"></i></a>
            </div>
        <?php } ?>
    </div>
</div>