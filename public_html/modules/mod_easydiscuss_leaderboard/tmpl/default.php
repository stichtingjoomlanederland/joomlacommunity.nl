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
<div id="ed" class="ed-mod ed-mod--leaderboard <?php echo $lib->getModuleWrapperClass();?>">
	<div class="ed-mod-card">
		<div class="ed-mod-card__body">
			<?php $i = 1; ?>
			<?php foreach($users as $user) { ?>
				<div class="o-card t-bg--100">
					<div class="o-card__body">
						<div class="o-media o-media--top">
							<div class="o-media__image">
								<div class="ed-mod-leaderboard-media">
									<div class="ed-mod-leaderboard-media__label">
										<div class="ed-rank-label ed-rank-label--<?php echo $i;?>">
											<b class="ed-rank-label__text"><?php echo $i;?></b>
										</div>
									</div>

									<?php if ($params->get('showavatar')) { ?>
										<div class="">
											<?php echo ED::themes()->html('user.avatar', $user, array('rank' => true, 'status' => true, 'size' => 'md')); ?>
										</div>
									<?php } ?>
								</div>
							</div>
							<div class="o-media__body t-text--truncate l-stack l-spaces--xs">
								<a href="<?php echo $user->getLink(); ?>" class="o-title-01 si-link t-text--truncate">
									<?php echo $user->getName(); ?>
								</a>

								<div class="o-meta l-cluster l-spaces--xs t-font-size--01">
									<div class="">
										<?php if ($order == 'answers') { ?>
										<div class="m-list__item">
											 <div class=""><?php echo JText::_('MOD_EASYDISCUSS_LEADERBOARD_ANSWERS'); ?>: <?php echo $user->total_answers; ?></div>
										</div>
										<?php } ?>

										<?php if ($order == 'points') { ?>
										<div class="m-list__item">
											 <div class=""><?php echo JText::_('MOD_EASYDISCUSS_LEADERBOARD_POINTS'); ?>: <?php echo $user->total_points; ?></div>
										</div>
										<?php } ?>

										<?php if ($order == 'posts') { ?>
										<div class="m-list__item">
											 <div class=""><?php echo JText::_('MOD_EASYDISCUSS_LEADERBOARD_POSTS'); ?>: <?php echo $user->total_posts; ?></div>
										</div>
										<?php } ?>
									</div>
									
								</div>
							</div>
						</div>
					</div>
					
					
					
				</div>
				<?php $i++; ?>
			<?php } ?>
			
		</div>
		<div class="ed-mod-card__footer">
			<?php if ($my->id > 0 && $params->get('showcurrentpoints')) { ?>
				<div class="o-meta">
					<?php echo JText::_('MOD_EASYDISCUSS_LEADERBOARD_CURRENT_POINTS');?>: <strong><a href="<?php echo $my->getLink();?>"><?php echo $my->getPoints();?></a></strong>
				</div>
			<?php } ?>
		</div>
	</div>
</div>