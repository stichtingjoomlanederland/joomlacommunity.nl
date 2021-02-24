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
<?php if ($items) { ?>
	<?php foreach ($items as $category) { ?>
	<div class="o-card o-card--ed-subscriptions-post-item" data-subscription-settings-wrapper data-id="<?php echo $category->id; ?>">
		<div class="o-card__body l-stack">
			<h2 class="o-title t-my--no">
				<a href="<?php echo $category->permalink;?>" class="si-link">
					<?php echo $category->bname;?>
				</a>
			</h2>

			<div class="o-meta l-cluster">
				<div class="">
					<div class="">
						<?php echo JText::sprintf('COM_EASYDISCUSS_SUBSCRIBE_CATEGORY_DISCUSSIONS_COUNT', $category->totalPosts); ?>
					</div>
					
					<div class="">
						<?php echo ED::subscription()->html($category->userid, $category->cid, 'category'); ?>
					</div>
				</div>
			</div>
		</div>
		<div class="o-card__footer l-stack">
			<div class="t-d--flex sm:t-flex-direction--c">
				<div class="lg:t-mr--sm">
					<?php echo $this->output('site/subscription/settings/interval', ['subscription' => $category])?>
				</div>
				<div class="lg:t-mr--sm">
					<?php echo $this->output('site/subscription/settings/sorting', ['subscription' => $category])?>
				</div>

				<div class="lg:t-mr--sm">
					<?php echo $this->output('site/subscription/settings/limit', ['subscription' => $category])?>
				</div>
			</div>
		</div>
	</div>
	<?php } ?>
	<div data-ed-subscription-pagination>
		<?php echo $pagination;?>
	</div>
<?php } ?>