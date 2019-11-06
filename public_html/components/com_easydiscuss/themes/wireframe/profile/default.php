<?php
/**
* @package      EasyDiscuss
* @copyright    Copyright (C) 2010 - 2015 Stack Ideas Sdn Bhd. All rights reserved.
* @license      GNU/GPL, see LICENSE.php
* EasyDiscuss is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');
?>
<div class="ed-profile t-lg-mt--lg" data-profile-item data-id="<?php echo $profile->id; ?>">
	<div class="ed-user-profile">
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
										<a href="<?php echo $url; ?>" class="ed-profile__bio-social-link" target="_blank" rel="nofollow">
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
					<div class="ed-profile-subscribe pull-right t-lg-mb--lg">
						<?php if ($this->config->get('main_rss')) { ?>
						<a target="_blank" class="t-lg-mr--md" href="<?php echo ED::feeds()->getFeedURL('view=profile&id='.$profile->id);?>">
							<i class="fa fa-rss-square ed-subscribe__icon t-lg-mr--sm"></i> <?php echo JText::_('COM_EASYDISCUSS_SUBSCRIBE'); ?>
						</a>
						<?php } ?>

					</div>
					<div class="ed-statistic pull-right">
						<div class="ed-statistic__item">
							<a href="<?php echo EDR::_('view=profile&viewtype=replies&id='. $profile->id); ?>">
							<span class="ed-statistic__item-count"><?php echo $profile->getTotalReplies(); ?></span>
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

						<?php echo $this->html('user.pm', $profile->id, 'list'); ?>
					</div>
				</div>
			</div>
		</div>

		<?php if ($this->config->get('main_signature_visibility') || $this->config->get('main_description_visibility')) { ?>
		<div class="ed-user-profile__bd <?php echo !$profile->getDescription() && !$profile->getSignature()  ? ' t-lg-p--no' : '';?>">

			<?php if ($this->config->get('main_description_visibility')) { ?>
			<div class="ed-profile__bio-desp">
				<?php echo $profile->getDescription(); ?>
			</div>
			<?php } ?>

			<?php if ($this->config->get('main_signature_visibility')) { ?>
			<div class="ed-profile__bio-signature">
				<?php echo $profile->getSignature(); ?>
			</div>
			<?php } ?>
			
		</div>
		<?php } ?>

		<div class="ed-user-profile__ft">
		<?php if ($profile->latitude && $profile->longitude) { ?>
			<div id="ed-user-map"> </div>
		<?php } ?>
		</div>
	</div>
	<div class="ed-profile-container">
		<div class="ed-profile-container__side">

			<div class="ed-profile-container__side-bd">
				<ul class="o-nav  o-nav--stacked ed-profile-container__side-nav">
					<li data-profile-tab data-filter-type="questions" <?php echo ($viewType == 'questions')? 'class="active"' : '' ?>><a href="javascript:void(0);"><?php echo JText::_('COM_EASYDISCUSS_PROFILE_TAB_QUESTIONS');?> (<?php echo $profile->getNumTopicPosted(); ?>)</a></li>
					<?php if ($this->config->get('main_qna')) { ?>
					<li data-profile-tab data-filter-type="unresolved" <?php echo ($viewType == 'unresolved')? 'class="active"' : '' ?>><a href="javascript:void(0);"><?php echo JText::_('COM_EASYDISCUSS_PROFILE_TAB_UNRESOLVED');?> (<?php echo $profile->getNumTopicUnresolved(); ?>)</a></li>
					<?php } ?>
					<?php if ($this->config->get('main_favorite')) { ?>
					<li data-profile-tab data-filter-type="favourites" <?php echo ($viewType == 'favourites')? 'class="active"' : '' ?>><a href="javascript:void(0);"><?php echo JText::_('COM_EASYDISCUSS_PROFILE_TAB_FAVOURITES');?> (<?php echo $profile->getTotalFavourites(); ?>)</a></li>
					<?php } ?>
					<li data-profile-tab data-filter-type="assigned" <?php echo ($viewType == 'assigned')? 'class="active"' : '' ?>><a href="javascript:void(0);"><?php echo JText::_('COM_EASYDISCUSS_PROFILE_TAB_ASSIGNED');?> (<?php echo $profile->getTotalAssigned(); ?>)</a></li>
					<li data-profile-tab data-filter-type="replies" <?php echo ($viewType == 'replies')? 'class="active"' : '' ?>><a href="javascript:void(0);"><?php echo JText::_('COM_EASYDISCUSS_PROFILE_TAB_REPLIES');?> (<?php echo $profile->getTotalReplies(); ?>)</a></li>
					<li class="ed-profile-container__side-divider"></li>
					
					<?php if ($easyblogExists) { ?>
					<li data-profile-tab data-filter-type="easyblog" <?php echo ($viewType == 'easyblog')? 'class="active"' : '' ?>><a href="javascript:void(0);"><?php echo JText::_('COM_EASYDISCUSS_PROFILE_TAB_EASYBLOG');?> (<?php echo $blogCount; ?>)</a></li>
					<?php } ?>
					
					<?php if ($this->config->get('integrations_komento_profile') && $komentoExists) { ?>
					<li data-profile-tab data-filter-type="komento" <?php echo ($viewType == 'komento')? 'class="active"' : '' ?>><a href="javascript:void(0);"><?php echo JText::_('COM_EASYDISCUSS_PROFILE_TAB_KOMENTO');?> (<?php echo $commentCount; ?>)</a></li>
					<?php } ?>
				</ul>
			</div>
		</div>

		<div class="ed-profile-container__content">

			<div class="ed-profile-container__content-hd">
				<div class="pull-left" data-ed-tabs-content-title><?php echo $tabsTitle; ?></div>
				<div class="t-hidden" data-ed-tabs-content-title-hidden><?php echo JText::_('COM_EASYDISCUSS_MY_POSTS') ?></div>
			</div>

			<div class="ed-profile-container__content-bd">
				<div class="loading-bar loader" style="display:none;">
					<div class="discuss-loader">
						<div class="test-object is-loading">
						  <div class="o-loading">
							  <div class="o-loading__content">
								  <i class="fa fa-spinner fa-spin"></i>
							  </div>
						  </div>
						</div>
					</div>
				</div>

				<div class="ed-posts-list" data-list-item>
				<?php if ($posts) { ?>
					<?php foreach ($posts as $post) { ?>
						<?php echo $this->output('site/profile/item', array('post' => $post)); ?>
					<?php } ?>
				 <?php } ?>

				<?php if ($contents) { ?>
					<?php echo $contents; ?>
				<?php } ?>
				</div>
				
				 <div class="is-empty">
					<div class="o-empty o-empty--bordered <?php echo $posts || $contents ? 't-hidden' : ''?>" data-list-empty>
						<div class="o-empty__content">
							<i class="o-empty__icon fa fa-book"></i>
							<div class="o-empty__text" data-list-empty-text>
								<?php echo JText::_('COM_EASYDISCUSS_EMPTY_DISCUSSION_LIST');?>
							</div>
						</div>
					</div>
				 </div>

				<div class="" data-profile-pagination>
					<?php echo $pagination;?>
				</div>
			</div>
		</div>
	</div>
</div>
<input id="profile-id" value="<?php echo $profile->id; ?>" type="hidden" />
