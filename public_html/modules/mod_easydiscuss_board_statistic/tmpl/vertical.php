<?php
/**
* @package		EasyDiscuss
* @copyright	Copyright (C) Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasyDiscuss is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');
?>
<div id="ed" class="ed-mod ed-mod--board-statistics is-vertical <?php echo $lib->getModuleWrapperClass();?>">
	<div class="ed-mod-card">
		<div class="ed-mod-card__body">
			<div class="">

				<?php if ($params->get('show_total_posts', true)) { ?>
				<div class="t-mb--md">
					<div class="t-font-size--02 t-bg--100 t-rounded--lg t-px--lg t-py--xs">
						<div class="t-d--flex">
							<div class="t-flex-grow--1"><?php echo JText::_('COM_EASYDISCUSS_STATS_POSTS');?></div>
							<div class="">
								<b><?php echo ED::formatNumbers($totalPosts); ?></b>
							</div>
						</div>
					</div>
				</div>
				<?php } ?>

				<?php if ($config->get('main_qna') && $params->get('show_total_resolved', true)) { ?>
				<div class="t-mb--md">
					<div class="t-font-size--02 t-bg--100 t-rounded--lg t-px--lg t-py--xs">
						<div class="t-d--flex">
							<div class="t-flex-grow--1"><?php echo JText::_('COM_EASYDISCUSS_STATS_RESOLVED_POSTS'); ?></div>
							<div class="">
								<b><?php echo ED::formatNumbers($resolvedPosts);?></b>
							</div>
						</div>
					</div>
				</div>
				<?php } ?>

				<?php if ($config->get('main_qna') && $params->get('show_total_unresolved', true)) { ?>
				<div class="t-mb--md">
					<div class="t-font-size--02 t-bg--100 t-rounded--lg t-px--lg t-py--xs">
						<div class="t-d--flex">
							<div class="t-flex-grow--1">
								<?php echo JText::_('COM_EASYDISCUSS_STATS_UNRESOLVED_POSTS');?>
							</div>
							<div class="">
								<b><?php echo ED::formatNumbers($unresolvedPosts);?></b>
							</div>
						</div>
					</div>
				</div>
				<?php } ?>

				<?php if ($params->get('show_total_users', false)) { ?>
				<div class="t-mb--md">
					<div class="t-font-size--02 t-bg--100 t-rounded--lg t-px--lg t-py--xs">
						<div class="t-d--flex">
							<div class="t-flex-grow--1">
								<?php echo JText::_('COM_EASYDISCUSS_TOTAL_USERS');?>
							</div>
							<div class="">
								<b><?php echo ED::formatNumbers($totalUsers);?></b>
							</div>
						</div>
					</div>
				</div>
				<?php } ?>

				<?php if ($params->get('show_total_guests', false)) { ?>
				<div class="t-mb--md">
					<div class="t-font-size--02 t-bg--100 t-rounded--lg t-px--lg t-py--xs">
						<div class="t-d--flex">
							<div class="t-flex-grow--1">
								<?php echo JText::_('COM_ED_TOTAL_GUESTS');?>
							</div>
							<div class="">
								<b><?php echo ED::formatNumbers($totalGuests);?></b>
							</div>
						</div>
					</div>
				</div>
				<?php } ?>

				<?php if ($params->get('show_latest_member', true)) { ?>
				<div class="t-mb--md">
					<div class="t-font-size--02 t-bg--100 t-rounded--lg t-px--lg t-py--xs">
						<div class="t-d--flex">
							<div class="t-flex-grow--1">
								<?php echo JText::_('COM_EASYDISCUSS_LATEST_MEMBER');?>
							</div>
							<div class="">
								<?php echo $lib->html('user.avatar', $latestMember); ?>
							</div>
						</div>
					</div>
				</div>
				<?php } ?>
			</div>
		</div>

		<?php if ($params->get('show_online_users', true)) { ?>
		<div class="ed-mod-card__footer">
			<div class="o-title">Online Users</div>
			<div class="l-cluster">
				<div class="">
					<?php if ($onlineUsers) { ?>
						<?php foreach ($onlineUsers as $user) { ?>
						<div class="">
							<?php echo $lib->html('user.avatar', $user); ?>
						</div>
						<?php } ?>
					<?php } ?>
				</div>
			</div>
		</div>
		<?php } ?>
	</div>
</div>