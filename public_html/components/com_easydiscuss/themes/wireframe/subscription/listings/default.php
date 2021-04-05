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
<div class="ed-my-subscribe-wrapper">
	<div class=" l-stack" data-ed-subscription data-id="<?php echo $profile->id; ?>">
		<h2 class="o-title"><?php echo JText::_('COM_EASYDISCUSS_VIEW_MY_SUBSCRIPTIONS') ?></h2>

		<?php if (!$this->config->get('main_email_digest') && !$allInstantSubscription) { ?>
		<div role="alert" class="o-alert o-alert--danger">
			<?php echo JText::_('COM_EASYDISCUSS_MY_SUBSCRIPTIONS_NOTICE_SET_TO_INSTANT'); ?>
		</div>
		<?php } ?>

		<?php if ($this->config->get('main_sitesubscription')) { ?>
		<div class="l-stack" data-site-subscription data-id="<?php echo $isSiteActive;?>" data-subscribed="<?php echo $isSiteActive ? 1 : 0;?>">
			<div class="o-card o-card--ed-subscriptions">
				<div class="o-card__body l-stack">
					<div class="t-d--flex t-align-items--c">
						<div class="t-flex-grow--1 t-min-width--0 t-pr--lg l-stack l-spaces--sm">
							<div class="o-title" data-ed-site-message data-id=<?php echo $isSiteActive; ?>>
								<?php echo $isSiteActive ? JText::_('COM_EASYDISCUSS_SUBSCRIBE_SITE_IS_ACTIVE') : JText::_('COM_EASYDISCUSS_SUBSCRIBE_SITE_IS_INACTIVE'); ?>
							</div>
							<div class="o-body">
								<?php echo JText::_('COM_EASYDISCUSS_SUBSCRIBE_EMAIL_SENT_TO'); ?>: <b><?php echo $user->email; ?></b>
							</div>
						</div>
						<div class="t-ml--auto ">
							<div class="">
								<?php echo $this->html('form.boolean', 'sitewide', $isSiteActive, '', 'data-ed-subscribe-action data-id="' . $profile->id . '"'); ?>
							</div>
						</div>
					</div>
				</div>
			</div>

			<?php if ($isSiteActive !== false) { ?>
			<div class="o-card o-card--ed-subscriptions" data-subscription-settings-wrapper data-id="<?php echo $siteSubscription->id; ?>">
				<div class="o-card__body l-stack">
					<div class="t-d--flex t-align-items--c sm:t-flex-direction--c">
						<div class="lg:t-mr--sm sm:t-mb--md">
							<div class="o-title"><?php echo JText::_('COM_EASYDISCUSS_SUBSCRIBE_SETTINGS') ?></div>
						</div>
						<div class="lg:t-mr--sm">
							<?php echo $this->output('site/subscription/settings/interval', ['subscription' => $siteSubscription])?>
						</div>
						<div class="lg:t-mr--sm">
							<?php echo $this->output('site/subscription/settings/sorting', ['subscription' => $siteSubscription])?>
						</div>

						<div class="lg:t-mr--sm">
							<?php echo $this->output('site/subscription/settings/limit', ['subscription' => $siteSubscription])?>
						</div>
					</div>
				</div>
			</div>
			<?php } ?>
		</div>
		<?php } ?>

		<div class="o-tabs o-tabs--ed">
			<div class="o-tabs__item <?php echo $filter == 'post' ? 'active' : '';?>" data-ed-tab>
				<a href="#post" class="o-tabs__link" data-ed-toggle="tab" data-filter="post" data-url="<?php echo EDR::_('view=subscription&filter=post');?>">
					<?php echo JText::_('COM_EASYDISCUSS_MY_SUBSCRIPTIONS_POSTS_TAB'); ?>
				</a>
			</div>
			<div class="o-tabs__item <?php echo $filter == 'category' ? 'active' : '';?>" data-ed-tab>
				<a href="#category" class="o-tabs__link" data-ed-toggle="tab" data-filter="category" data-url="<?php echo EDR::_('view=subscription&filter=category');?>">
					<?php echo JText::_('COM_EASYDISCUSS_MY_SUBSCRIPTIONS_CATEGORIES_TAB'); ?>
				</a>
			</div>	
		</div>

		<div class="tab-content">
			<div id="post" class="tab-pane <?php echo $filter == 'post' ? 'active' : '';?>" data-ed-list-wrapper>
				<div class="ed-posts-list l-stack" data-ed-list itemscope itemtype="http://schema.org/ItemList">
					<?php if ($filter == 'post') { ?>
						<?php echo $this->output('site/subscription/listings/posts', ['items' => $items, 'pagination' => $pagination]); ?>
					<?php } ?>
				</div>

				<?php echo $this->html('loading.block'); ?>
				
				<?php echo $this->html('card.emptyCard', 'far fa-newspaper', 'COM_EASYDISCUSS_EMPTY_DISCUSSION_LIST'); ?>
			</div>

			<div id="category" class="tab-pane <?php echo $filter == 'category' ? 'active' : '';?>" data-ed-list-wrapper>
				<div class="ed-posts-list l-stack" data-ed-list itemscope itemtype="http://schema.org/ItemList">
					<?php if ($filter == 'category') { ?>
						<?php echo $this->output('site/subscription/listings/categories', ['items' => $items, 'pagination' => $pagination]); ?>
					<?php } ?>
				</div>

				<?php echo $this->html('loading.block'); ?>
				
				<?php echo $this->html('card.emptyCard', 'far fa-folder', 'You have not subscribed to any categories yet'); ?>
			</div>
		</div>
	</div>
</div>
