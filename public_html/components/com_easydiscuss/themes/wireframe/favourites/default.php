<?php
/**
* @package		EasyDiscuss
* @copyright	Copyright (C) 2010 - 2015 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasyDiscuss is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');
?>
<h2 class="ed-page-title"><?php echo JText::_('COM_EASYDISCUSS_FAVOURITES_PAGE_HEADING'); ?></h2>

<div class="ed-assigned-post <?php echo !$posts ? ' is-empty' : '';?>">
	<div class="ed-user-item ed-user-item--profile t-lg-mb--lg">
		<div class="o-row">
			<div class="o-col">
				<div class="o-flag">
					<div class="o-flag__image o-flag--top">
					   <?php echo $this->html('user.avatar', $profile, array('status' => true, 'size' => 'lg')); ?>
					</div>

					<div class="o-flag__body">
						<?php echo $this->html('user.username', $profile, array('lgMarginBottom' => true)); ?>
						<div class="ed-rank-bar">
							<div class="ed-rank-bar__progress" style="width: <?php echo ED::getUserRankScore($profile->id); ?>%"></div>
						</div>
						<div class="ed-user-rank t-lg-mb--sm o-label o-label--<?php echo $profile->getRoleLabelClassname()?>"><?php echo $profile->getRole();?></div>

						<div class="ed-user-meta t-lg-mb--sm">
							<?php echo JText::sprintf('COM_EASYDISCUSS_REGISTERED_ON', $profile->getDateJoined());?>
						</div>
						<div class="ed-user-meta t-lg-mb--sm">
							<?php echo JText::sprintf('COM_EASYDISCUSS_LAST_SEEN_ON', $profile->getLastOnline(true)); ?>
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
						<span class="ed-statistic__item-count"><?php echo count($badges);?></span>
						<span><?php echo JText::_('COM_EASYDISCUSS_PROFILE_TAB_BADGES');?></span>
						</a>
					</div>
					<?php } ?>
					<?php if ($this->config->get('main_rss')) { ?>
					<div class="ed-statistic__item">
						<a href="<?php echo EDR::_('index.php?option=com_easydiscuss&view=profile&id=' . $profile->id . '&format=feed' ); ?>"
						<i class="fa fa-rss ed-statistic__item-icon"></i>
						</a>
					</div>
					<?php } ?>
				</div>
			</div>
		</div>
	</div>

	<div class="ed-list">
		<?php if ($posts) { ?>
			<?php foreach ($posts as $post) { ?>
				<div class="ed-post-item<?php echo $post->getHeaderClass(); ?>">
					<div class="ed-post-item__hd">
						<div class="o-row">
							<div class="o-col">
								<h2 class="ed-post-item__title t-lg-mt--md t-lg-mb--md">
									<a href="<?php echo $post->getPermalink();?>"><?php echo $post->getTitle();?></a>

									<div class="ed-post-item__status t-ml--sm">
										<i class="fa fa-heart t-icon--danger"></i>

										<i class="fa fa-star ed-post-item__status-icon" data-ed-provide="tooltip" data-original-title="<?php echo JText::_('COM_EASYDISCUSS_FEATURED_DESC');?>"></i>
										<i class="fa fa-lock ed-post-item__status-icon" data-ed-provide="tooltip" data-original-title="<?php echo JText::_('COM_EASYDISCUSS_LOCKED_DESC');?>"></i>
										<i class="fa fa-key ed-post-item__status-icon" data-ed-provide="tooltip" data-original-title="<?php echo JText::_('COM_EASYDISCUSS_PROTECTED_DESC');?>"></i>
										<i class="fa fa-eye ed-post-item__status-icon" data-ed-provide="tooltip" data-original-title="<?php echo JText::_('COM_EASYDISCUSS_PRIVATE_DESC');?>"></i>
									</div>
								</h2>

								<?php if ($post->getTags()) { ?>
									<ol class="g-list-inline ed-post-item__post-meta">
										<?php foreach ($post->getTags() as $tag) { ?>
											<li><a href="<?php echo EDR::getTagRoute($tag->id); ?>">#<?php echo $tag->title; ?></a></li>
										<?php } ?>
									</ol>
								<?php } ?>

								<?php if ($this->config->get('main_qna')) { ?>
								<ol class="g-list-inline ed-post-item__post-meta">
									<li>
										<span class="o-label o-label--<?php echo ($post->isresolve) ? 'success' : 'danger';?>-o">
											<?php echo ($post->isresolve) ? JText::_('COM_EASYDISCUSS_RESOLVED') : JText::_('COM_EASYDISCUSS_UNRESOLVED');?></span>
									</li>
								</ol>
								<?php } ?>
							</div>
						</div>
					</div>

					<div class="ed-post-item__ft t-bdt-no">
						<ol class="g-list-inline g-list-inline--dashed">
							<li><span class=""><?php echo $post->getDuration(); ?></span></li>
							<li><a class="" href="<?php echo EDR::getCategoryRoute($post->getCategory()->id); ?>"><?php echo JText::_($post->getCategory()->title); ?></a></li>

							<?php if ($post->getLastReplier()) { ?>
								<li class="current">
									<div class="">
										<span><?php echo JText::_('COM_EASYDISCUSS_LAST_REPLIER'); ?>: </span>
										<?php if (!$post->isLastReplyAnonymous()) { ?>
											<?php echo $this->html('user.avatar', $post->getLastReplier(), array('rank' => false, 'size' => 'sm')); ?>
										<?php } else { ?>
											<?php echo $this->html('user.anonymous', $post->getOwner(), $post->isAnonymous()); ?>
										<?php } ?> 
									</div>
								</li>
							<?php } ?>
						</ol>
					</div>
				</div>
			<?php } ?>
		<?php } ?>
	</div>    

	<div class="t-lg-mt--xl">
		<div class="o-empty">
			<div class="o-empty__content">
				<i class="o-empty__icon fa fa-star"></i><br /><br />
				<div class="o-empty__text"><?php echo JText::_('COM_EASYDISCUSS_NO_FAVOURITE_POSTS_YET');?></div>
			</div>
		</div>
	</div>

	<?php if ($pagination) { ?>
		<div class="" data-profile-pagination>
			<?php echo $pagination;?>
		</div>
	<?php } ?>    
</div>
