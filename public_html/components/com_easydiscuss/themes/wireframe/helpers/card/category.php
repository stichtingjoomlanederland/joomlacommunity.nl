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
<div class="o-card o-card--ed-category">
	<div class="o-card__body l-stack">
		<div class="t-d--flex t-align-items--fs">
			<div class="t-flex-grow--1 t-min-width--0 t-pr--lg">
				<div class="o-media t-align-items--fs">
					<div class="o-media__image">
						<a href="<?php echo $category->getPermalink();?>">
							<?php echo $this->html('category.identifier', $category, 'md'); ?>
						</a>
					</div>
					<div class="o-media__body l-stack">
						<h2 class="o-title t-text--truncate t-my--no">
							<a href="<?php echo $category->getPermalink();?>" class="si-link"><?php echo $category->getTitle(); ?></a>
						</h2>

						<?php if ($description && $category->getParams()->get('show_description')) { ?>
						<div class="o-body t-text--600">
							<?php echo $description; ?>
						</div>
						<?php } ?>

						<?php if ($category->totalSubcategories) { ?>
						<div class="ed-subcategories-wrapper">
							<div class="ed-subcategories lg:t-mr--md sm:t-mb--md">
								<?php echo JText::sprintf('COM_ED_SUBCATEGORIES_COUNT', $category->totalSubcategories);?>
							</div>
							<div class="l-cluster l-cluster--comma-list l-spaces--xs">
								<div>
									<?php foreach ($category->childs as $child) { ?>
										<div><a href="<?php echo $child->getPermalink(); ?>" class="si-link"><?php echo $child->getTitle(); ?></a></div>
									<?php } ?>
								</div>
							</div>
						</div>
						<?php } ?>
					</div>
				</div>
			</div>

			<div class="t-ml--auto sm:t-d--none lg:t-d--block t-flex-shrink--0">
				<div class="">
					<div class="l-cluster l-spaces--xs">
						<div>
							<div class="o-meta" data-ed-post-counter data-id="<?php echo $category->id; ?>">
								<div class="o-loader o-loader--sm o-loader--inline is-active">&nbsp;</div>
							</div>
							<?php if (!$category->container && $this->config->get('main_rss')) { ?>
								<div class="o-meta">|</div>
								<div class="o-meta">
									<a href="<?php echo $category->getRSSPermalink();?>" class="t-text--600" data-ed-provide="tooltip" data-placement="bottom" data-original-title="<?php echo JText::_('COM_ED_SUBSCRIBE_VIA_RSS_READER');?>">
										<i class="fas fa-rss"></i>
									</a>
								</div>
							<?php } ?>
						</div>
					</div>
				</div>
			</div>

		</div>
	</div>
</div>