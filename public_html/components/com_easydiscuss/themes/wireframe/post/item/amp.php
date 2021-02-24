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
<!doctype html>
<html amp lang="<?php echo $langTag; ?>" <?php echo $isRtl ? 'dir="rtl"' : ''; ?>>
<head>
	<meta charset="utf-8" />
	<base href="/"/>
	<script async src="https://cdn.ampproject.org/v0.js"></script>
	<meta name="viewport" content="width=device-width,minimum-scale=1,initial-scale=1">
	<link rel="canonical" href="<?php echo $url; ?>" />
	<?php echo $this->html('post.schema', $post, $answer, $tags); ?>
	<script async custom-element="amp-ad" src="https://cdn.ampproject.org/v0/amp-ad-0.1.js"></script>
	<script async custom-element="amp-twitter" src="https://cdn.ampproject.org/v0/amp-twitter-0.1.js"></script>

	<?php if ($post->hasGist()) { ?>
	<script async custom-element="amp-gist" src="https://cdn.ampproject.org/v0/amp-gist-0.1.js"></script>
	<?php } ?>

	<?php if ($this->config->get('social_buttons_type') == 'addthis') { ?>
	<script async custom-element="amp-addthis" src="https://cdn.ampproject.org/v0/amp-addthis-0.1.js"></script>
	<?php } ?>

	<script async custom-element="amp-iframe" src="https://cdn.ampproject.org/v0/amp-iframe-0.1.js"></script>
	<script async custom-element="amp-vimeo" src="https://cdn.ampproject.org/v0/amp-vimeo-0.1.js"></script>
	<script async custom-element="amp-dailymotion" src="https://cdn.ampproject.org/v0/amp-dailymotion-0.1.js"></script>
	<script async custom-element="amp-youtube" src="https://cdn.ampproject.org/v0/amp-youtube-0.1.js"></script>

	<?php if ($socialEnabled) { ?>
	<script async custom-element="amp-social-share" src="https://cdn.ampproject.org/v0/amp-social-share-0.1.js"></script>
	<?php } ?>

	<title><?php echo $post->getTitle(); ?></title>
	<link href="https://fonts.googleapis.com/css?family=Heebo" rel="stylesheet">
	<style amp-boilerplate>body{-webkit-animation:-amp-start 8s steps(1,end) 0s 1 normal both;-moz-animation:-amp-start 8s steps(1,end) 0s 1 normal both;-ms-animation:-amp-start 8s steps(1,end) 0s 1 normal both;animation:-amp-start 8s steps(1,end) 0s 1 normal both}@-webkit-keyframes -amp-start{from{visibility:hidden}to{visibility:visible}}@-moz-keyframes -amp-start{from{visibility:hidden}to{visibility:visible}}@-ms-keyframes -amp-start{from{visibility:hidden}to{visibility:visible}}@-o-keyframes -amp-start{from{visibility:hidden}to{visibility:visible}}@keyframes -amp-start{from{visibility:hidden}to{visibility:visible}}</style><noscript><style amp-boilerplate>body{-webkit-animation:none;-moz-animation:none;-ms-animation:none;animation:none}</style></noscript>
	<?php echo $this->output('site/post/item/amp.stylesheets'); ?>
</head>
<body>
	<header>
		<span class="brand-logo">
			<amp-img
				src="<?php echo ED::getLogo('amp'); ?>"
				width="32"
				height="32"
			>
			</amp-img>
			<div class="brand-logo__text">
				<?php echo $jConfig->get('sitename'); ?>
			</div>
		</span>
	</header>
	<div class="ed-main-wrapper">
		<section class="ed-post-content">
			<div class="ed-post-content__body">
				<div class="ed-post-heading">
					<div class="o-title">
						<a href="<?php echo $post->getPermalink(); ?>"><?php echo $post->getTitle(); ?></a>
					</div>
					<div class="o-meta">
						<?php echo JText::_('COM_ED_AMP_BY'); ?> <a href="<?php echo $post->getAuthor()->getPermalink(); ?>"><?php echo $post->getAuthor()->getName();?></a>

						<?php echo JText::_('COM_ED_AMP_ON'); ?> <?php echo $post->date;?>
					</div>

					<div class="o-meta">
						<?php echo JText::_('COM_ED_AMP_POSTED_IN'); ?> <a href="<?php echo $post->getCategory()->getPermalink();?>"><?php echo $post->getCategory()->getTitle();?></a>
					</div>

					<div><?php echo $adsense->header; ?></div>
				</div>
			</div>

			<div class="ed-post-content__body">
				<div class="ed-assignee">
					<?php if (ED::isModerator($post->category_id) && isset($post->assignee)) { ?>
						<?php echo JText::_('COM_ED_MODERATOR');?>:&nbsp;<a href="<?php echo $post->assignee->getPermalink(); ?>"><?php echo $post->assignee->getName();?></a>
					<?php } ?>
				</div>
				<div class="ed-admin-bar">
					<?php if ((!$post->isLocked() || ED::isModerator($post->category_id)) && $post->getCategory()->canViewReplies()) { ?>
					<div class="ed-reply-counter">
						<span class="ed-counter-title"><?php echo JText::_('COM_ED_REPLIES');?></span> <?php echo ED::formatNumbers($post->getTotalReplies());?>
					</div>
					<?php } ?>

					<div class="ed-like-counter">
						<span class="ed-counter-title"><?php echo JText::_('COM_ED_LIKES');?></span> <?php echo ED::formatNumbers($post->getTotalLikes());?>
					</div>

					<div class="ed-view-counter">
						<span class="ed-counter-title"><?php echo JText::_('COM_ED_VIEWS');?></span> <?php echo ED::formatNumbers($post->getHits());?>
					</div>

					<div class="ed-total-counter">
						<span class="ed-counter-title"><?php echo JText::_('COM_ED_VOTES');?></span> <?php echo ED::formatNumbers($post->getTotalVotes()); ?>
					</div>
				</div>
			</div>
			<div class="ed-post-content__body">
				<div class="ed-amp-content">
					<?php echo $ampContent; ?>
				</div>
			</div>
			<?php if ($socialEnabled) { ?>
				<div class="ed-post-content__body">
					<div class="ed-social">
						<?php if ($this->config->get('social_buttons_type') == 'default') { ?>
							<?php if ($this->config->get('integration_facebook_like_send') && $this->config->get('integration_facebook_like_appid')) { ?>
								<amp-social-share type="facebook" data-param-app_id="<?php echo $this->config->get('integration_facebook_like_appid'); ?>" width="30" height="30"></amp-social-share>
							<?php } ?>

							<?php if ($this->config->get('integration_twitter_button')) { ?>
								<amp-social-share type="twitter" width="30" height="30"></amp-social-share>
							<?php } ?>

							<?php if ($this->config->get('integration_linkedin')) { ?>
								<amp-social-share type="linkedin" width="30" height="30"></amp-social-share>
							<?php } ?>
						<?php } ?>

						<?php if ($this->config->get('social_buttons_type') == 'addthis') { ?>
							<amp-addthis width="320" height="92" data-pub-id="<?php echo $this->config->get('addthis_pub_id'); ?>" data-widget-id="<?php echo $this->config->get('inline_widget_id'); ?>" data-widget-type="inline" data-url="<?php echo $post->getPermalink(true, false); ?>" data-title="<?php echo $this->html('string.escape', $post->getTitle()); ?>"></amp-addthis>
						<?php } ?>
					</div>
				</div>
			<?php } ?>
			<div class="ed-post-content__body">
				<?php echo $this->output('site/post/item/amp.comments', ['post' => $post]); ?>
			</div>
		</section>

		<div><?php echo $adsense->beforereplies; ?></div>

		<div class="ed-reply-list">
			<?php if ($answer && $answer !== true) { ?>
				<?php echo $this->output('site/post/item/amp.replies', ['reply' => $answer]); ?>
			<?php } ?>
			<?php if ($replies) { ?>
				<?php foreach ($replies as $reply) { ?>
					<?php echo $this->output('site/post/item/amp.replies', ['reply' => $reply]); ?>
				<?php } ?>
			<?php } ?>
		</div>

		<a class="btn-ed btn-ed-view-more" href="<?php echo $post->getPermalink(); ?>"><?php echo JText::_('COM_ED_AMP_VIEW_FULL_BUTTON'); ?></a>

		<?php echo $adsense->footer; ?>
	</div>

	<?php if ($this->config->get('social_buttons_type') == 'addthis') { ?>
	<amp-addthis width="320" height="92" layout="responsive" data-pub-id="<?php echo $this->config->get('addthis_pub_id'); ?>" data-widget-id="<?php echo $this->config->get('floating_widget_id'); ?>" data-widget-type="floating" data-url="<?php echo $post->getPermalink(true, false); ?>" data-title="<?php echo $this->html('string.escape', $post->getTitle()); ?>"></amp-addthis>
	<?php } ?>
</body>
</html>