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
<div class="o-card t-bg--100" data-mod-category-item>
	<div class="o-card__body l-stack">
		<div class="t-d--flex ">
			<div class="t-flex-grow--1 t-min-width--0 t-pr--lg">
				<div class="o-media o-media--top">
					<?php if ($params->get('showcavatar', true)) { ?>
					<div class="o-media__image">
						<?php echo ED::themes()->html('category.identifier', $category, 'sm'); ?>
					</div>
					<?php } ?>

					<div class="o-media__body t-text--truncate">
						
						<?php echo ED::themes()->html('category.title', $category, ['popbox' => false, 'customClass' => 'o-title si-link t-text--truncate']); ?>

						<div class="o-meta l-cluster l-spaces--xs">
							<div class="">
								<?php if ($params->get('showpostcnt', true)) { ?>
								<div class="t-text--truncate">
									<?php echo ED::themes()->getNouns('MOD_DISCUSSIONSCATEGORIES_ENTRY_COUNT', $category->getTotalPosts(), true);?>
								</div>
								<?php } ?>
							</div>
						</div>
					</div>
				</div>
			</div>
			
			<?php if ($params->get('layouttype') == 'tree' && $category->hasChildren() && !$params->get('exclude_child_categories', 0)) { ?>
			<div class="t-ml--auto">
				<a href="javascript:void(0);" class="ed-filter-menu__toggle" data-mod-category-nav>
					<i class="fa fa-angle-right"></i>
				</a>
			</div>
			<?php } ?>
		</div>
	</div>
</div>