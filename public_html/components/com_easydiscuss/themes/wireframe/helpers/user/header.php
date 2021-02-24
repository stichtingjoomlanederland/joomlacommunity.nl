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
<div class="o-card o-card--ed-user-profile">
	<div class="o-card__body">
		<div class="t-d--flex sm:t-flex-direction--c">
			<div class="t-flex-grow--1 t-min-width--0 lg:t-pr--lg sm:t-mb--md">
				<div class="o-media o-media--top">
					<div class="o-media__image">
						<?php echo $this->html('user.avatar', $user, [
							'size' => 'large'
						]); ?>
					</div>
					<div class="o-media__body l-stack">
						<a href="<?php echo $user->getPermalink();?>" class="ed-user-name t-text--700">
							<b>
								<?php echo $this->html('user.username', $user, [
									'hyperlink' => false
								]); ?>

								<div class="ed-user-rank o-label t-ml--sm" style="background-color: <?php echo $user->getRoleLabelColour();?> !important;">
									<?php echo $user->getRole(); ?>
								</div>
							</b>
						</a>
							
						<?php if ($this->config->get('main_ranking')) { ?>
						<div class="o-meta l-spaces--2xs">
							<?php echo ED::ranks()->getRank($this->profile->getId()); ?>
						</div>
						<?php } ?>

						<div class="o-meta l-spaces--">
							<?php echo JText::sprintf('COM_EASYDISCUSS_REGISTERED_ON', $user->getDateJoined());?>
						</div>
						
						<div class="o-meta l-spaces--2xs">
							<?php echo JText::sprintf('COM_EASYDISCUSS_LAST_SEEN_ON', $user->getLastOnline(true)); ?>
						</div>

						<?php if ($this->config->get('layout_profile_showsocial') || $this->config->get('main_rss')) { ?>
						<div class="l-cluster l-spaces--2xs">
							<div class="">
								<?php if ($params->get('show_facebook', false) && $user->getFacebook()) { ?>
								<div class="">
									<a href="<?php echo $this->html('string.escape', $user->getFacebook());?>" target="_blank" rel="nofollow">
										<div class="o-avatar o-avatar--sm o-avatar--icon o-avatar--rounded">
											<i class="fab fa-facebook fa-fw t-text--600"></i>
										</div>
									</a>
								</div>
								<?php } ?>

								<?php if ($params->get('show_twitter', false) && $user->getTwitter()) { ?>
								<div>
									<a href="<?php echo $this->html('string.escape', $user->getTwitter());?>" target="_blank" rel="nofollow">
										<div class="o-avatar o-avatar--sm o-avatar--icon o-avatar--rounded">
											<i class="fab fa-twitter fa-fw t-text--600"></i>
										</div>
									</a>
								</div>
								<?php } ?>

								<?php if ($params->get('show_linkedin', false) && $user->getLinkedin()) { ?>
								<div>
									<a href="<?php echo $this->html('string.escape', $user->getLinkedin());?>" target="_blank" rel="nofollow">
										<div class="o-avatar o-avatar--sm o-avatar--icon o-avatar--rounded">
											<i class="fab fa-linkedin fa-fw t-text--600"></i>
										</div>
									</a>
								</div>
								<?php } ?>

								<?php if ($params->get('show_skype', false) && $user->getSkype()) { ?>
								<div>
									<a href="skype://<?php echo $this->html('string.escape', $user->getSkype());?>" target="_blank" rel="nofollow">
										<div class="o-avatar o-avatar--sm o-avatar--icon o-avatar--rounded">
											<i class="fab fa-skype fa-fw t-text--600"></i>
										</div>
									</a>
								</div>
								<?php } ?>


								<?php if ($params->get('show_website', false) && $user->getWebsite()) { ?>
								<div>
									<a href="<?php echo $this->html('string.escape', $user->getWebsite());?>" target="_blank" rel="nofollow">
										<div class="o-avatar o-avatar--sm o-avatar--icon o-avatar--rounded">
											<i class="fa fa-link fa-fw t-text--600"></i>
										</div>
									</a>
								</div>
								<?php } ?>

								<?php if ($this->config->get('main_rss')) { ?>
								<div>
									<a href="<?php echo ED::feeds()->getFeedURL('view=profile&id='.$user->id);?>" 
										target="_blank"
										data-ed-provide="tooltip"
										data-title="<?php echo JText::_('COM_ED_SUBSCRIBE_WITH_FEED_READER');?>"
									>
										<div class="o-avatar o-avatar--sm o-avatar--icon o-avatar--rounded">
											<i class="fa fa-rss-square fa-fw t-text--600"></i>
										</div>
									</a>
								</div>
								<?php } ?>
							</div>
						</div>
						<?php } ?>
					</div>
					
				</div>
			</div>

			<div class="lg:t-ml--auto t-flex-shrink--0 sm:t-d--flex sm:t-order--first sm:t-mb--md sm:t-justify-content--fe">
				<?php echo $this->html('user.pm', $user->id); ?>
			</div>
		</div>
	</div>
</div>

<?php if ($user->getDescription()) { ?>
<div class="o-card o-card--ed-user-profile">
	<div class="o-card__body">
		<div class="t-mb--sm">
			<b><?php echo JText::_('COM_ED_ABOUT_ME'); ?></b>
		</div>

		<p class="t-font-size--01"><?php echo $user->getDescription();?></p>
	</div>
</div>
<?php } ?>

<?php if ($displayStatistics) { ?>
<div class="ed-user-profile-stats">
	<div class="lg:o-grid lg:o-grid--gutters t-mb--no">
		<div class="lg:o-grid__cell lg:o-grid__cell--3 sm:t-mb--md">
			<div class="t-font-size--02 t-bg--200 t-rounded--lg t-px--lg t-py--xs">
				<div class="t-d--flex">
					<div class="t-flex-grow--1 t-text--truncate">
						<i class="fas fa-pen t-mr--sm t-text--600"></i> <?php echo JText::_('COM_ED_POSTS');?>
					</div>
					<div class="">
						<b><?php echo ED::formatNumbers($user->getNumTopicPosted());?></b>
					</div>
				</div>
			</div>
		</div>

		<div class="lg:o-grid__cell lg:o-grid__cell--3 sm:t-mb--md">
			<div class="t-font-size--02 t-bg--200 t-rounded--lg t-px--lg t-py--xs">
				<div class="t-d--flex">
					<div class="t-flex-grow--1 t-text--truncate">
						<i class="fas fa-comments t-mr--sm t-text--600"></i> <?php echo JText::_('COM_ED_REPLIES');?>
					</div>
					<div class="">
						<b><?php echo ED::formatNumbers($user->getTotalReplies());?></b>
					</div>
				</div>
			</div>
		</div>

		<?php if ($this->config->get('main_badges')) { ?>
		<div class="lg:o-grid__cell lg:o-grid__cell--3 sm:t-mb--md">
			<div class="t-font-size--02 t-bg--200 t-rounded--lg t-px--lg t-py--xs">
				<div class="t-d--flex">
					<div class="t-flex-grow--1 t-text--truncate">
						<i class="fas fa-trophy t-mr--sm t-text--primary"></i> <?php echo JText::_('COM_EASYDISCUSS_BADGES'); ?>
					</div>
					<div class="">
						<b><?php echo ED::formatNumbers(count($badges));?></b>
					</div>
				</div>
			</div>
		</div>
		<?php } ?>

		<?php if ($this->config->get('main_points')) { ?>
		<div class="lg:o-grid__cell lg:o-grid__cell--3 sm:t-mb--md">
			<div class="t-font-size--02 t-bg--200 t-rounded--lg t-px--lg t-py--xs">
				<div class="t-d--flex">
					<div class="t-flex-grow--1 t-text--truncate">
						<i class="fas fa-certificate t-mr--sm t-text--success"></i> <?php echo JText::_('COM_EASYDISCUSS_POINTS'); ?>
					</div>
					<div class="">
						<b><?php echo ED::formatNumbers($user->getPoints());?></b>
					</div>
				</div>
			</div>
		</div>
		<?php } ?>
	</div>
</div>
<?php } ?>