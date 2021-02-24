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
<div class="o-card o-card--ed-badge">
	<div class="o-card__body">
		<div class="t-d--flex t-align-items--c">
			<div class="t-flex-grow--1">
				<div class="o-media">
					<div class="o-media__image">
						<a href="<?php echo $badge->getPermalink();?>" class="o-avatar o-avatar--lg">
							<img src="<?php echo $badge->getAvatar();?>" alt="<?php echo $this->html('string.escape', $badge->title);?>" />
						</a>
					</div>
					<div class="o-media__body">
						<div class="ed-badge__name">
							<a href="<?php echo $badge->getPermalink();?>" class="si-link"><?php echo JText::_($badge->title); ?></a>
						</div>
						<div class="ed-badge__meta">
							<?php echo JText::_($badge->description); ?>
						</div>
					</div>
				</div>
			</div>
			<div class="t-ml--auto sm:t-d--none lg:t-d--block">
				<?php if ($achieved) { ?>
				<a href="javascript:void(0);" 
					data-ed-provide="tooltip"
					data-title="<?php echo JText::_('COM_ED_BADGE_YOU_UNLOCKED');?>"
					class="o-btn o-btn--success"
				>
					<i class="fas fa-star"></i> <?php echo JText::_('COM_ED_EARNED');?>
				</a>
				<?php } ?>

				<a href="<?php echo $badge->getPermalink();?>" class="o-btn o-btn--default-o">
					<i class="fas fa-star"></i> <?php echo JText::sprintf('COM_EASYDISCUSS_BADGE_TOTAL_ACHIEVERS', $badge->getTotalAchievers());?>
				</a>
			</div>
		</div>
		
	</div>

	<div class="o-card__footer sm:t-d--block lg:t-d--none">
		<?php if ($achieved) { ?>
		<a href="javascript:void(0" 
			data-ed-provide="tooltip"
			data-title="<?php echo JText::_('COM_ED_BADGE_YOU_UNLOCKED');?>"
			class="o-btn o-btn--success"
		>
			<i class="fas fa-star"></i> <?php echo JText::_('COM_ED_EARNED');?>
		</a>
		<?php } ?>
		
		<a href="<?php echo $badge->getPermalink();?>" class="o-btn o-btn--default-o">
			<i class="fas fa-star"></i> <?php echo JText::sprintf('COM_EASYDISCUSS_BADGE_TOTAL_ACHIEVERS', $badge->getTotalAchievers());?>
		</a>
	</div>
</div>