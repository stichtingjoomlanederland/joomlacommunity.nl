<?php
/**
* @package      EasyDiscuss
* @copyright    Copyright (C) Stack Ideas Sdn Bhd. All rights reserved.
* @license      GNU/GPL, see LICENSE.php
* EasyDiscuss is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');
?>
<script type="text/javascript">
<?php if ($my->id > 0 && $params->get('display_notification_button')) { ?>
ed.require(['edq', 'site/src/toolbar'], function($, App) {

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
<div id="ed" class="ed-mod ed-mod--navigation <?php echo $lib->getModuleWrapperClass();?>">
	<div class="ed-mod-card">
		<div class="ed-mod-card__body">
			<div class="l-stack">
				<?php if ($my->id > 0 && $params->get('display_notification_button')) { ?>
				<div class="ed-toolbar__item--action">
					<div class="o-nav ed-toolbar__o-nav">
						<div class="o-nav__item"
							data-ed-notifications-wrapper
							data-ed-popbox="ajax://site/views/notifications/popbox"
							data-ed-popbox-position="<?php echo $params->get('popbox_position', 'bottom-right'); ?>"
							data-ed-popbox-toggle="click"
							data-ed-popbox-offset="<?php echo $params->get('popbox_offset', 32); ?>"
							data-ed-popbox-type="navbar-notifications"
							data-ed-popbox-component="popbox--navbar"
							data-ed-popbox-cache="0"
							data-ed-popbox-collision="<?php echo $params->get('popbox_collision', 'flip'); ?>"

							data-ed-provide="tooltip"
							data-placement="<?php echo $params->get('tooltip_position', 'top');?>"
							data-original-title="<?php echo JText::_('MOD_NAVIGATION_NOTIFICATIONS');?>"
						>
							<a href="javascript:void(0);" class="o-nav__link ed-toolbar__link no-active-state <?php echo $notificationsCount ? 'has-new' : '';?>">
								<i class="fa fa-bell"></i>
								<span class="ed-toolbar__link-bubble" data-ed-notifications-counter><?php echo $notificationsCount;?></span>
							</a>

						</div>
					</div>
				</div>
				<?php } ?>

				<a href="<?php echo EDR::_('view=categories');?>" class="t-font-size--02 si-link t-bg--100 t-rounded--lg t-px--md t-py--xs"
					>
					<div class="t-d--flex t-align-items--c">
						<div class="t-flex-grow--1 t-min-width--0 ">
							<div class="o-title t-text--truncate t-pr--md"><?php echo JText::_('MOD_NAVIGATION_ALL'); ?></div>
						</div>
						<div class="t-d--flex t-flex-shrink--0">
							<span class="t-text--500"></span>
						</div>
					</div>
				</a>
				<?php foreach ($categories as $category) { ?>
				<a href="<?php echo $category->getPermalink();?>" class="t-font-size--02 si-link t-bg--100 t-rounded--lg t-px--md t-py--xs">
					<div class="t-d--flex t-align-items--c">
						<div class="t-flex-grow--1 t-min-width--0 ">
							<div class="o-title t-text--truncate t-pr--md"><?php echo JText::_($category->getTitle()); ?></div>
						</div>
						<div class="t-d--flex t-flex-shrink--0"
						data-ed-provide="tooltip"
						data-original-title="<?php echo JText::_('MOD_ED_NAVIGATION_UNREAD');?>"
						>
							<span class="t-text--500"><?php echo $category->totalNew;?></span>
						</div>
					</div>
				</a>
				<?php } ?>
			</div>
		</div>
	</div>
</div>