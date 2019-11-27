<?php
/**
* @package      EasyDiscuss
* @copyright    Copyright (C) 2010 - 2019 Stack Ideas Sdn Bhd. All rights reserved.
* @license      GNU/GPL, see LICENSE.php
* EasyDiscuss is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');
?>
<h2 class="ed-page-title"><?php echo JText::_('COM_EASYDISCUSS_MY_POSTS'); ?></h2>
<div class="ed-assigned-post">
	<div class="ed-user-profile t-lg-mb--lg">
		<div class="ed-user-profile__hd">
			<div class="o-row">
				<div class="o-col">
					<div class="o-flag">
						<div class="o-flag__image o-flag--top t-pr--lg">
							<?php echo $this->html('user.avatar', $profile, array('status' => true, 'size' => 'lg')); ?>       
						</div>

						<div class="o-flag__body">
							<?php echo $this->html('user.username', $profile, array('lgMarginBottom' => true)); ?>

							<?php if( $this->config->get('main_ranking')){ ?>
							<div class="ed-rank-bar t-lg-mb--sm">
								<div class="ed-rank-bar__progress" style="width: <?php echo ED::getUserRankScore($profile->id); ?>%"></div>
							</div>
							<?php } ?>

							<div class="ed-user-rank t-lg-mb--sm o-label o-label--<?php echo $profile->getRoleLabelClassname()?>">
								<?php echo $profile->getRole(); ?>
							</div>
							
							<div class="ed-user-meta">
								<?php echo JText::sprintf('COM_EASYDISCUSS_REGISTERED_ON', $profile->getDateJoined());?>
							</div>
							
							<div class="ed-user-meta t-lg-mb--sm">
								<?php echo JText::sprintf('COM_EASYDISCUSS_LAST_SEEN_ON', $profile->getLastOnline(true)); ?>
							</div>

							<div class="ed-profile__bio-social">
								<ol class="g-list-inline">
								<?php if (!empty($socialUrls)) { ?>
									<?php foreach ($socialUrls as $key => $url) { ?>
									<li>
										<a href="<?php echo $url; ?>" class="ed-profile__bio-social-link">
											<i class="fa fa-<?php echo $key; ?>"></i>
											<span class="ed-profile__bio-social-txt"><?php echo ucfirst($key); ?></span>
										</a>
									</li>
									<?php }?>
								<?php } ?>
								</ol>
							</div>
							
						</div>
					</div>        
				</div>
				<div class="o-col">
					<div class="ed-statistic pull-right">
						<div class="ed-statistic__item">
							<a href="<?php echo EDR::_('view=profile&viewtype=replies&id='. $profile->id); ?>">
							<span class="ed-statistic__item-count"><?php echo $profile->getNumTopicAnswered(); ?></span>
							<span><?php echo JText::_('COM_EASYDISCUSS_PROFILE_TAB_REPLIES');?></span>
							</a>
						</div>
						<div class="ed-statistic__item">
							<a href="<?php echo EDR::_('view=profile&viewtype=questions&id='.$profile->id); ?>">
							<span class="ed-statistic__item-count"><?php echo $profile->getNumTopicPosted(); ?></span>
							<span><?php echo JText::_('COM_EASYDISCUSS_PROFILE_TAB_QUESTIONS');?></span>
							</a>
						</div>
						<?php if ($this->config->get('main_badges')) { ?>
						<div class="ed-statistic__item">
							<a href="<?php echo EDR::_('view=badges&userid='.$profile->id); ?>">
							<span class="ed-statistic__item-count"><?php echo count($badges);?></span>
							<span><?php echo JText::_('COM_EASYDISCUSS_PROFILE_TAB_BADGES');?></span>
							</a>
						</div>
						<?php } ?>

						<?php if ($this->config->get('main_points')) { ?>
						<div class="ed-statistic__item">
							<a href="<?php echo EDR::_('view=points&id='.$profile->id); ?>">
							<span class="ed-statistic__item-count"> <?php echo $profile->getPoints(); ?></span>
							<span><?php echo JText::_('COM_EASYDISCUSS_POINTS'); ?></span>
							</a>
						</div>
						<?php } ?>
						
					</div>        
				</div>
			</div>
		</div>

		<div class="ed-user-profile__chart">
			<canvas id="chart-area" />
		</div>

		<!-- <div id="js-legend" class="chart-legend"></div> -->
	</div>

	<div class="ed-list">
		<?php if ($posts) { ?>
			<?php foreach ($posts as $post) { ?>
				<?php echo $this->output('site/mypost/default.item', array('post' => $post)); ?>
			<?php } ?>
		<?php } else { ?>
			<div class="empty"><?php echo JText::_('COM_EASYDISCUSS_ASSIGNED_NOT_FOUND');?></div>
		<?php } ?>
	</div>
	<?php if ($pagination) { ?>
		<div data-profile-pagination>
			<?php echo $pagination;?>
		</div>
	<?php } ?>
</div>
