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
					<a href="<?php echo $badge->getPermalink();?>">
						<img src="<?php echo $badge->getAvatar();?>" alt="<?php echo $this->html('string.escape', $badge->title);?>" width="32" height="32" />
					</a>
				</div>
				<div class="o-media__body">
					<div class="ed-badge-title t-text--truncate">
						<a href="<?php echo $badge->getPermalink();?>" class="t-text--100"><?php echo JText::_($badge->title);?></a>
					</div>

					<div class="t-font-size--01 t-mt--sm t-text--truncate"><?php echo JText::_($badge->description);?></div>
				</div>
			</div>
		</div>

		<div class="o-grid o-grid--gutters t-px--md t-mb--sm">
			<div class="o-grid__cell o-grid__cell--12">
				<div class="t-bg--700 t-rounded--lg t-py--sm t-text--center t-text--truncate">
					<i class="fas fa-star t-text--warning"></i>&nbsp; <?php echo JText::sprintf('COM_EASYDISCUSS_BADGE_TOTAL_ACHIEVERS', $badge->getTotalAchievers()); ?>
				</div>
			</div>
		</div>
	</div>
</div>
