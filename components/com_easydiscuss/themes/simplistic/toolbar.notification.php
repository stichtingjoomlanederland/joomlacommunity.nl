<?php
/**
 * @package		EasyDiscuss
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
<li class="dropdown_">
	<a data-foundry-toggle="dropdown" class="dropdown-toggle_ notificationLink" href="javascript:void(0);" rel="ed-tooltip" data-placement="top" data-original-title="<?php echo JText::_( 'COM_EASYDISCUSS_NOTIFICATIONS' );?>">
		<i class="icon-bell"></i>
		<span class="visible-phone"><?php echo JText::_( 'COM_EASYDISCUSS_NOTIFICATIONS' );?></span>
		<span class="label label-notification" id="notification-count" style="display: <?php echo $totalNotifications > 0 ? 'inline-block' : 'none';?>"><?php echo $totalNotifications; ?></span>
	</a>

	<ul class="dropdown-menu dropdown-menu-large notificationDropDown" style="display: none;">
		<li>
			<div class="discuss-notice-menu">
				<ul class="unstyled notification-result notificationResult fs-11">
					<li class="loading-indicator notificationLoader"><i><?php echo JText::_( 'COM_EASYDISCUSS_LOADING' );?></i></li>
				</ul>

				<div class="modal-footer pt-0 pb-5">
					<a class="btn btn-link small" href="<?php echo DiscussRouter::_( 'index.php?option=com_easydiscuss&view=notifications' );?>"><?php echo JText::_( 'COM_EASYDISCUSS_VIEW_ALL_NOTIFICATIONS' );?></a>
				</div>
			</div>
		</li>
	</ul>
</li>
