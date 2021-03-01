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
<div class="ed-filters" 
	data-ed-filters 
	data-tag="<?php echo $activeTag->id;?>"
	data-category="<?php echo $activeCategory->id;?>"
	data-tag="<?php echo $activeTag->id;?>"
	data-baseurl="<?php echo base64_encode($baseUrl);?>"
	data-route-category="<?php echo $showCategories ? 1 : 0;?>"
	data-search="<?php echo $this->html('string.escape', $search);?>"
>
	<div class="">
		<div class="t-d--flex sm:t-flex-direction--c ">
			<div class="t-flex-grow--1 t-min-width--0">
				<div class="t-d--flex sm:t-flex-direction--c t-min-width--0">

					<?php if ($categories) { ?>	
					<div class="lg:t-mr--sm t-min-width--0 ">
						<div class="o-dropdown t-d--flex">
							<a href="javascript:void(0);" class="o-btn o-btn--default-o sm:t-d--block sm:t-mb--md t-text--truncate t-w--100" data-ed-toggle="dropdown">
								<div class="t-text--truncate">
									<b><?php echo JText::_('COM_EASYDISCUSS_CATEGORY'); ?>:</b>
									<span data-category-title class="">
										<?php if ($activeCategory->id) { ?>
											<?php echo $activeCategory->getTitle();?>
										<?php } else { ?>
											<?php echo JText::_('COM_ED_ALL_CATEGORIES'); ?>
										<?php } ?>
									</span>
									&nbsp;<i class="fa fa-caret-down"></i>
								</div>
							</a>

							<div class="o-dropdown-menu t-mt--2xs sm:t-w--100" style="width: 400px;max-height: 400px; overflow: hidden;" data-ed-category-container>

								<?php echo $this->html('loading.block');?>
								
								<?php echo $this->output('site/helpers/post/filters/category', [
									'rootLevel' => true,
									'categories' => $categories,
									'activeCategory' => $activeCategory
								]); ?>
							</div>
						</div>
					</div>
					<?php } ?>

					<div class="lg:t-mr--sm">
						<div class="o-dropdown">
							<a href="javascript:void(0);"  class="o-btn o-btn--default-o sm:t-d--block sm:t-mb--md t-text--nowrap"  data-ed-toggle="dropdown" data-main-active-filter>
								<b><?php echo JText::_('COM_ED_POSTS'); ?>:</b>
								<span data-main-filter-title>
								<?php if ($activeFilter == 'all') { ?>
									<?php echo JText::_('COM_ED_FILTERS_ALL'); ?>
								<?php } ?>

								<?php if ($activeFilter == 'mine' && $this->my->id) { ?>
									<?php echo JText::_('COM_ED_FILTERS_MY_POSTS'); ?>
								<?php } ?>

								<?php if ($this->config->get('main_postassignment') && $activeFilter == 'assign' && ED::isModerator()) { ?>
									<?php echo JText::_('COM_ED_FILTERS_ASSIGNED_TO_ME'); ?>
								<?php } ?>

								<?php if ($activeFilter == 'unresolved') { ?>
									<?php echo JText::_('COM_EASYDISCUSS_FILTER_UNRESOLVED'); ?>
								<?php } ?>

								<?php if ($activeFilter == 'unanswered') { ?>
									<?php echo JText::_('COM_EASYDISCUSS_FILTER_UNANSWERED'); ?>
								<?php } ?>

								<?php if ($activeFilter == 'resolved') { ?>
									<?php echo JText::_('COM_EASYDISCUSS_FILTER_RESOLVED'); ?>
								<?php } ?>

								<?php if ($activeFilter == 'unread' && $this->my->id) { ?>
									<?php echo JText::_('COM_EASYDISCUSS_FILTER_UNREAD'); ?>
								<?php } ?>
								</span>

								&nbsp;<i class="fa fa-caret-down"></i>
							</a>

							<ul class="o-dropdown-menu t-mt--2xs sm:t-w--100 has-active-markers" data-main-filters>
								<li class="<?php echo $activeFilter == 'all' ? 'active' : '';?>">
									<a href="javascript:void(0);" 
										class="o-dropdown__item" 
										data-ed-filter="main"
										data-id="all"
									>
										<?php echo JText::_('COM_ED_FILTERS_ALL'); ?>
									</a>
								</li>

								<?php if ($this->config->get('filters_mine') && $this->my->id) { ?>
								<li class="<?php echo $activeFilter == 'mine' ? 'active' : '';?>">
									<a href="javascript:void(0);" 
										class="o-dropdown__item" 
										data-ed-filter="main"
										data-id="mine"
									>
										<?php echo JText::_('COM_ED_FILTERS_MY_POSTS'); ?>
									</a>
								</li>
								<?php } ?>

								<?php if ($this->config->get('main_postassignment') && $this->config->get('filters_assign') && ED::isModerator()) { ?>
								<li class="<?php echo $activeFilter == 'assign' ? 'active' : '';?>">
									<a href="javascript:void(0);" 
										class="o-dropdown__item" 
										data-ed-filter="main"
										data-id="assign"
									>
										<?php echo JText::_('COM_ED_FILTERS_ASSIGNED_TO_ME'); ?>
									</a>
								</li>
								<?php } ?>

								<?php if ($this->config->get('filters_resolved') && $this->config->get('main_qna')) { ?>
								<li class="<?php echo $activeFilter == 'resolved' ? 'active' : '';?>">
									<a href="javascript:void(0);" 
										class="o-dropdown__item"
										data-ed-filter="main"
										data-id="resolved"
									>
										<?php echo JText::_('COM_EASYDISCUSS_FILTER_RESOLVED'); ?>
									</a>
								</li>
								<?php } ?>

								<?php if ($this->config->get('filters_unresolved') && $this->config->get('main_qna')) { ?>
								<li class="<?php echo $activeFilter == 'unresolved' ? 'active' : '';?>">
									<a href="javascript:void(0);" 
										class="o-dropdown__item"
										data-ed-filter="main"
										data-id="unresolved"
									>
										<?php echo JText::_('COM_EASYDISCUSS_FILTER_UNRESOLVED'); ?>
									</a>
								</li>
								<?php } ?>

								<?php if ($this->config->get('filters_unanswered')) { ?>
								<li class="<?php echo $activeFilter == 'unanswered' ? 'active' : '';?>">
									<a href="javascript:void(0);" 
										class="o-dropdown__item"
										data-ed-filter="main"
										data-id="unanswered"
									>
										<?php echo JText::_('COM_EASYDISCUSS_FILTER_UNANSWERED'); ?>
									</a>
								</li>
								<?php } ?>
									
								<?php if ($this->config->get('filters_unread') && $this->my->id) { ?>
								<li class="<?php echo $activeFilter == 'unread' ? 'active' : '';?>">
									<a href="javascript:void(0);"
										class="o-dropdown__item"
										data-ed-filter="main"
										data-id="unread"
									>
										<?php echo JText::_('COM_EASYDISCUSS_FILTER_UNREAD'); ?>
									</a>
								</li>
								<?php } ?>
							</ul>
						</div>
					</div>

					<?php if (($this->config->get('main_labels') && $labels) || ($this->config->get('layout_post_types') && $types) || ($this->config->get('post_priority') && $priorities)) { ?>
					<div class="lg:t-mr--sm" data-insert-filter>
						<div class="t-text--right o-dropdown">
							<a href="javascript:void(0);" class="o-btn o-btn--default-o sm:t-d--block sm:t-mb--md t-text--nowrap" 
								data-ed-toggle="dropdown"
							>
								<?php echo JText::_('COM_ED_INSERT_FILTER');?> &nbsp;<i class="fa fa-plus-circle t-text--primary"></i>
							</a>

							<div class="o-dropdown-menu t-mt--2xs t-px--md sm:t-w--100" style="min-width: 280px; max-width: 350px; max-height: 350px;" data-ed-filter-container>
								<div class="l-stack">
									<?php if ($labels) { ?>
									<div class="ed-filter-group">
										<div class="o-title-01 t-mb--md">
											<?php echo JText::_('COM_ED_FILTERS_POST_LABELS');?>
										</div>
										<div class="l-cluster l-spaces--xs">
											<div>
												<?php foreach ($labels as $label) { ?>
												<div class="t-min-width--0">
													<div class="t-d--flex t-text--truncate">
														<a href="javascript:void(0);" class="o-label o-label--ed-filter-label t-text--truncate <?php echo in_array($label->id, $selectedLabels) ? 'is-active' : '';?>"
															data-ed-filter="label"
															data-id="<?php echo $label->id;?>"
														>
															<?php echo $label->getTitle();?>
														</a>
													</div>
												</div>
												<?php } ?>
											</div>
										</div>
									</div>
									<?php } ?>

									<?php if ($types) { ?>
									<div class="ed-filter-group">
										<div class="o-title-01 t-mb--md">
											<?php echo JText::_('COM_ED_FILTERS_POST_TYPES'); ?>
										</div>

										<div class="l-cluster l-spaces--xs">
											<div>
												<?php foreach ($types as $type) { ?>
												<div class="t-min-width--0">
													<div class="t-d--flex t-text--truncate">
														<a href="javascript:void(0);" class="o-label o-label--ed-filter-label t-text--truncate  <?php echo in_array($type->alias, $selectedTypes) ? 'is-active' : '';?>"
															data-ed-filter="type"
															data-id="<?php echo $type->alias;?>"
														>
															<?php echo JText::_($type->title);?>
														</a>
													</div>
													
												</div>
												<?php } ?>
											</div>
										</div>
									</div>
									<?php } ?>

									<?php if ($priorities) { ?>
									<div class="ed-filter-group">
										<div class="o-title-01 t-mb--md">
											<?php echo JText::_('COM_ED_FILTERS_POST_PRIORITIES'); ?>
										</div>

										<div class="l-cluster l-spaces--xs">
											<div>
												<?php foreach ($priorities as $priority) { ?>
												<div>
													<a href="javascript:void(0);" class="o-label o-label--ed-filter-label  <?php echo in_array($priority->id, $selectedPriorities) ? 'is-active' : '';?>"
														data-ed-filter="priority"
														data-id="<?php echo $priority->id;?>"
													>
														<?php echo $priority->getTitle();?>
													</a>
												</div>
												<?php } ?>
											</div>
										</div>
									</div>
									<?php } ?>
								</div>
							</div>
						</div>
					</div>
					<?php } ?>
				</div>
			</div>

			<?php if ($showSorting) { ?>
			<div class=" t-text--right">
				<div class="o-dropdown" data-sorting-wrapper>
					<a href="javascript:void(0);" class="o-btn o-btn--default-o sm:t-d--block sm:t-mb--md t-text--nowrap" 
						data-ed-toggle="dropdown"
					>
						<span data-sorting-title>
							<?php if ($activeSort == 'latest' || !$activeSort) { ?>
								<?php echo JText::_('COM_ED_SORT_NEWEST_FIRST');?>
							<?php } ?>

							<?php if ($activeSort == 'popular') { ?>
								<?php echo JText::_('COM_ED_SORT_MOST_POPULAR');?>
							<?php } ?>

							<?php if ($activeSort == 'hits') { ?>
								<?php echo JText::_('COM_ED_SORT_MOST_VIEWED');?>
							<?php } ?>

							<?php if ($activeSort == 'title') { ?>
								<?php echo JText::_('COM_ED_SORT_POST_TITLE');?>
							<?php } ?>

							<?php if ($activeSort == 'oldest') { ?>
								<?php echo JText::_('COM_ED_SORT_OLDEST_FIRST');?>
							<?php } ?>
						</span> 
						&nbsp;<i class="fa fa-caret-down"></i>
					</a>

					<ul class="o-dropdown-menu o-dropdown-menu--right has-active-markers t-mt--2xs sm:t-w--100">
						<li class="<?php echo $activeSort == 'latest' || !$activeSort ? 'active' : '';?>" data-ed-sorting="latest">
							<a href="javascript:void(0);" class="o-dropdown__item">
								<?php echo JText::_('COM_ED_SORT_NEWEST_FIRST');?>
							</a>
						</li>
						<li data-ed-sorting="popular" class="<?php echo $activeSort == 'popular' ? 'active' : '';?>">
							<a href="javascript:void(0);" class="o-dropdown__item">
								<?php echo JText::_('COM_ED_SORT_MOST_POPULAR');?>
							</a>
						</li>
						<li data-ed-sorting="hits" class="<?php echo $activeSort == 'hits' ? 'active' : '';?>">
							<a href="javascript:void(0);" class="o-dropdown__item">
								<?php echo JText::_('COM_ED_SORT_MOST_VIEWED');?>
							</a>
						</li>
						<li data-ed-sorting="title" class="<?php echo $activeSort == 'title' ? 'active' : '';?>">
							<a href="javascript:void(0);" class="o-dropdown__item">
								<?php echo JText::_('COM_ED_SORT_POST_TITLE');?>
							</a>
						</li>
						<li data-ed-sorting="oldest" class="<?php echo $activeSort == 'oldest' ? 'active' : '';?>">
							<a href="javascript:void(0);" class="o-dropdown__item">
								<?php echo JText::_('COM_ED_SORT_OLDEST_FIRST');?>
							</a>
						</li>
					</ul>
				</div>
			</div>
			<?php } ?>
		</div>
	</div>

	<div class="ed-filters-selection <?php echo !$hasFilters ? 't-d--none' : '';?>" data-ed-selection-wrapper>
		<div class="l-cluster l-spaces--xs" >
			<div data-ed-filters-selection>
				<?php if ($activeLabels) { ?>
					<?php foreach ($activeLabels as $activeLabel) { ?>
						<?php echo $this->output('site/helpers/post/filters/active', [
							'type' => 'label',
							'id' => $activeLabel->id,
							'title' => $activeLabel->getTitle()
						]); ?>
					<?php } ?>
				<?php } ?>

				<?php if ($activePostTypes) { ?>
					<?php foreach ($activePostTypes as $activePostType) { ?>
						<?php echo $this->output('site/helpers/post/filters/active', [
							'type' => 'type',
							'id' => $activePostType->alias,
							'title' => JText::_($activePostType->title)
						]); ?>
					<?php } ?>
				<?php } ?>

				<?php if ($activePriorities) { ?>
					<?php foreach ($activePriorities as $activePriority) { ?>
						<?php echo $this->output('site/helpers/post/filters/active', [
							'type' => 'priority',
							'id' => $activePriority->id,
							'title' => $activePriority->getTitle()
						]); ?>
					<?php } ?>
				<?php } ?>
			</div>
		</div>
	</div>
</div>