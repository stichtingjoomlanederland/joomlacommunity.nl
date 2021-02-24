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
<div class="o-popbox-content">
	<div class="o-popbox-content__bd">
		<div class="t-p--md">
			<div class="o-media o-media--rev">
				<div class="o-media__image">
					<?php echo $this->html('user.avatar', $user, ['status' => true, 'size' => 'md', 'popbox' => false], false); ?>
				</div>
				<div class="o-media__body">
					<div class="o-title t-text--truncate">
						<?php echo $this->html('user.username', $user, array('lgMarginBottom' => true)); ?>

						<?php if ($user->getRole()) { ?>
						<div class="ed-user-rank o-label t-ml--sm" style="background-color: <?php echo $user->getRoleLabelColour();?> !important;">
							<?php echo $user->getRole(); ?>
						</div>
						<?php } ?>

						<?php if ($this->config->get('main_ranking')) { ?>
						<div class="ed-user-rank t-font-size--01">
							<?php echo $this->html('string.escape', ED::ranks()->getRank($user->id)); ?>
						</div>
						<?php } ?>
					</div>
				</div>
			</div>
		</div>

		<div class="t-d--flex t-justify-content--sb t-px--md t-mb--sm">		
			<div class="t-flex-grow--1">
				<div class="t-bg--700 t-rounded--lg t-py--sm t-text--center t-text--truncate">
					<?php echo JText::sprintf('COM_ED_USER_POPOVER_POSTS', ED::formatNumbers($user->getTotalPosts())); ?>
				</div>
			</div>
			<div class="t-flex-grow--1 t-ml--md">
				<div class="t-bg--700 t-rounded--lg t-py--sm t-text--center t-text--truncate">
					<?php echo JText::sprintf('COM_ED_USER_POPOVER_REPLIES', ED::formatNumbers($user->getTotalReplies())); ?>
				</div>
			</div>

			<?php if ($this->config->get('main_points')) { ?>
			<div class="t-flex-grow--1 t-ml--md">
				<div class="t-bg--700 t-rounded--lg t-py--sm t-text--center t-text--truncate">
					<?php echo JText::sprintf('COM_ED_USER_POPOVER_POINTS', ED::formatNumbers($user->getPoints())); ?>
				</div>
			</div>
			<?php } ?>
		</div>
	</div>
		
	<?php if ($this->config->get('main_badges')) { ?>
	<div class="o-popbox-content__ft t-border--600">
		<div class="t-p--md">
			<div class="l-cluster">
				<div class="">
					<?php if ($badges) { ?>
						<?php foreach ($badges as $badge) { ?>
							<div>
								<a href="<?php echo $badge->getPermalink();?>" class="o-avatar o-avatar--sm">
									<img src="<?php echo $badge->getAvatar();?>" alt="<?php echo $this->html('string.escape', $badge->title);?>" width="24" />
								</a>
							</div>
						<?php } ?>
					<?php } else { ?>
						<div>
							<?php echo JText::_('COM_ED_NO_ACHIEVEMENTS_YET');?>
						</div>
					<?php } ?>
				</div>
			</div>
		</div>
	</div>
	<?php } ?>

	<?php echo $this->html('user.pm', $user->id, 'popbox'); ?>
</div>


