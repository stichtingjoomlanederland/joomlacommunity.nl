<?php
/**
* @package      EasyDiscuss
* @copyright    Copyright (C) 2010 - 2017 Stack Ideas Sdn Bhd. All rights reserved.
* @license      GNU/GPL, see LICENSE.php
* EasyDiscuss is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Restricted access');
?>
<h2 class="ed-page-title"><?php echo JText::_('COM_EASYDISCUSS_VIEW_MY_SUBSCRIPTIONS') ?></h2>
<div class="ed-my-subscribe" data-ed-subscription data-id="<?php echo $profile->id; ?>">
	<div class="ed-chart-my-subscribe t-lg-mb--md">
		<div class="o-row">
			<div class="o-col">
				<div class="ed-chart-wrapper">
					<canvas id="chart-area" />
				</div>
			</div>
			<div class="o-col o-col--3">
				<div class="ed-my-subscribe-chart__title"><?php echo Jtext::_('COM_EASYDISCUSS_SUBSCRIBE_TOTAL_SUBSCRIBED'); ?></div>
				<div id="js-legend" class=""></div>
			</div>
		</div>
	</div>
	<?php if (!$this->config->get('main_email_digest') && !$allInstantSubscription) { ?>
	<div role="alert" class="o-alert o-alert--danger">
		<?php echo JText::_('COM_EASYDISCUSS_MY_SUBSCRIPTIONS_NOTICE_SET_TO_INSTANT'); ?>
	</div>
	<?php } ?>
	
	<?php if ($this->config->get('main_sitesubscription')) { ?>
		<div class="ed-my-subscribe-action <?php echo $isSiteActive ? 'is-subscribe' : ''; ?>" data-ed-subscription-action>
			<form action="">
				<div class="ed-my-subscribe-action__box t-lg-mb--md">
					<div class="o-row">
						<div class="o-col--3">
							<div class="ed-my-subscribe-action__title"><?php echo JText::_('COM_EASYDISCUSS_SUBSCRIPTION_SITE') ?>:</div>
						</div>
						<div class="o-col">
							<div class="o-flag">
								<div class="o-flag__image">
									<div class="o-switch">
										<input type="checkbox" name="onoffswitch" class="o-switch__checkbox" id="myonoffswitch" data-ed-subscribe-action data-is-subscribe="<?php echo $isSiteActive; ?>" data-id=<?php echo $profile->id;?> <?php echo $isSiteActive ? 'checked' : '' ?>>
										<label class="o-switch__label" for="myonoffswitch">
											<span class="o-switch__inner"></span>
											<span class="o-switch__switch"></span>
										</label>
									</div>        
								</div>
								<div class="o-flag__body">
									<div class="ed-my-subscribe-action__">
										<span class="ed-my-subscribe-action__title" data-ed-site-active data-id=<?php echo $isSiteActive; ?>><?php echo $isSiteActive ? JText::_('COM_EASYDISCUSS_SUBSCRIBE_SITE_IS_ACTIVE') : JText::_('COM_EASYDISCUSS_SUBSCRIBE_SITE_IS_INACTIVE'); ?></span>
										<?php echo JText::_('COM_EASYDISCUSS_SUBSCRIBE_EMAIL_SENT_TO'); ?> <?php echo $profile->user->email; ?>
									</div>        
								</div>
							</div>
						</div>
					</div>
				</div>
				<?php if ($isSiteActive !== false) { ?>
					<div class="ed-my-subscribe-action__box" data-ed-subscription-settings-site  data-id=<?php echo $siteSubscribe[0]->id; ?>>
						<div class="o-row">
							<div class="o-col--3">
								<div class="ed-my-subscribe-action__title"><?php echo JText::_('COM_EASYDISCUSS_SUBSCRIBE_SETTINGS') ?></div>
							</div>
							<div class="o-col">
								<div class="o-row">
									<div class="o-col t-lg-pr--md t-xs-pr--no t-xs-pb--lg">
										<?php echo $this->output('site/subscription/subscribe.interval', array('subscribe' => $siteSubscribe[0]))?>    
									</div>
									<div class="o-col t-lg-pr--md t-xs-pr--no t-xs-pb--lg">
										<?php echo $this->output('site/subscription/subscribe.sort', array('subscribe' => $siteSubscribe[0]))?>
									</div>
									<div class="o-col">
										<?php echo $this->output('site/subscription/subscribe.count', array('subscribe' => $siteSubscribe[0]))?>
									</div>
								</div>
							</div>                    
						</div>    
					</div>
				<?php } ?>
			</form>
		</div>
	<?php } ?>
   
	<div class="ed-filters">
		<div class="ed-filter-bar t-lg-mt--lg t-lg-mb--md">
			<ul class="o-tabs o-tabs--ed pull-left">
				<li data-subscription-tab data-filter-type="post" data-filter-tab="post" class="o-tabs__item  <?php echo $filter == 'post' ? 'active' : ''; ?>">
					<a class="o-tabs__link allPostsFilter" data-filter-anchor href="<?php echo EDR::_('view=subscription&filter=post');?>">
						<?php echo JText::_('COM_EASYDISCUSS_MY_SUBSCRIPTIONS_POSTS_TAB'); ?>
					</a>
				</li>
				<li data-subscription-tab data-filter-type="category" data-filter-tab="category" class="o-tabs__item <?php echo $filter == 'category' ? 'active' : ''; ?>">
					<a class="o-tabs__link unResolvedFilter" data-filter-anchor href="<?php echo EDR::_('view=subscription&filter=category');?>">
						<?php echo JText::_('COM_EASYDISCUSS_MY_SUBSCRIPTIONS_CATEGORIES_TAB'); ?>
					</a>
				</li>
			</ul>
		</div>        
	</div>
	<div class="" id="post">
		<div class="loading-bar loader" style="display:none;" data-ed-subscription-loading>
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

		<div class="ed-list" data-ed-subscription-item>
		<?php if ($postSubscribe) { ?>
			<?php foreach ($postSubscribe as $post) { ?>
				<?php echo $this->output($namespace, array('post' => $post)); ?>
			<?php } ?>
		<?php } ?>
		</div>

		<div class="t-lg-mt--xl <?php echo $postSubscribe? '' : 'is-empty'?>" data-ed-subscription-empty>
		  <div class="o-empty o-empty--bordered">
			  <div class="o-empty__content">
				  <i class="o-empty__icon fa fa-info-circle"></i>
				  <div class="o-empty__text"><?php echo JText::_('COM_EASYDISCUSS_EMPTY_SUBSCRIPTIONS_LIST');?></div>
			  </div>
		  </div>
		</div>

		<div class="" data-ed-subscription-pagination>
			<?php echo $pagination;?>
		</div>        
	</div>
</div>