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
<div class="o-card o-card--ed-user-item">
	<div class="o-card__body">
		<div class="lg:t-d--flex t-align-items--c sm:t-flex-direction--c">
			<div class="t-flex-grow--1 t-min-width--0 lg:t-pr--lg sm:t-mb--md">
				<div class="o-media">
					<div class="o-media__image">
						<?php echo $this->html('user.avatar', $user, ['status' => true, 'size' => 'medium', 'popbox' => false]); ?>
					</div>
					<div class="o-media__body t-text--truncate">
						<a href="<?php echo $user->getPermalink();?>" class="ed-user-item__name t-text--truncate t-text--700">
							<b>
								<?php echo $this->html('user.username', $user, ['hyperlink' => false, 'popbox' => false]); ?>
							</b>
						</a>
					</div>
				</div>
			</div>
			<div class="lg:t-ml--auto t-flex-shrink--0 sm:t-d--flex">
				
				<div class="l-cluster">
					<div>
						<div class="ed-users-stat-item">
							<div class="t-font-size--02 t-bg--100 t-rounded--lg t-px--lg t-py--xs t-text--center t-text--nowrap">
								<?php echo ED::formatNumbers($user->getTotalQuestions());?>&nbsp; <span class="t-text--500"><?php echo JText::_('COM_ED_POSTS');?></span>
							</div>
						</div>
						<div class="ed-users-stat-item">
							<div class="t-font-size--02 t-bg--100 t-rounded--lg t-px--lg t-py--xs t-text--center t-text--nowrap">
								<?php echo ED::formatNumbers($user->getTotalReplies());?>&nbsp;  <span class="t-text--500"><?php echo JText::_('COM_ED_REPLIES');?></span>
							</div>
						</div>

						<?php if ($this->config->get('main_points')) { ?>
						<div class="ed-users-stat-item">
							<div class="t-font-size--02 t-bg--100 t-rounded--lg t-px--lg t-py--xs t-text--center t-text--nowrap">
								<?php echo ED::formatNumbers($user->getPoints());?>&nbsp; <span class="t-text--500"><?php echo JText::_('COM_EASYDISCUSS_POINTS');?></span>
							</div>
						</div>
						<?php } ?>

						<div class="ed-users-stat-item">
							<?php echo $this->html('user.pm', $user->id, 'list'); ?>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
	
	<?php if ($this->config->get('main_badges')) { ?>
	<div class="o-card__footer">
		<div class="l-cluster">
			<div class="">
				<?php if ($badges) { ?>
					<?php foreach ($badges as $badge) { ?>
					<div>
						<a href="<?php echo $badge->getPermalink();?>" class="o-avatar o-avatar--sm"
							data-ed-popbox="ajax://site/views/popbox/badge"
							data-ed-popbox-position="bottom-left"
							data-ed-popbox-toggle="hover"
							data-ed-popbox-offset="4"
							data-ed-popbox-type="ed-badge"
							data-ed-popbox-component="o-popbox--category"
							data-ed-popbox-cache="1"
							data-args-id="<?php echo $badge->id; ?>"
						>
							<img src="<?php echo $badge->getAvatar();?>" alt="<?php echo $this->html('string.escape', JText::_($badge->title));?>" />
						</a>
					</div>
					<?php } ?>
				<?php } else { ?>
					<div class="t-font-size--02">
						<?php echo JText::_('COM_ED_NO_ACHIEVEMENTS_YET'); ?>
					</div>
				<?php } ?>
			</div>
		</div>
	</div>
	<?php } ?>
</div>