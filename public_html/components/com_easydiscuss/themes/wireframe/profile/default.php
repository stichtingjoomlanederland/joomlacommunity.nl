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
<div class="ed-profile" data-profile-item data-id="<?php echo $profile->id; ?>">

	<div class="l-stack">
		<?php echo $this->html('user.header', $profile, [
			'displayStatistics' => true
		]); ?>

		<div class="o-tabs o-tabs--ed">
			<div class="o-tabs__item <?php echo $filter == 'posts' ? 'active' : '';?>" data-ed-tab>
				<a href="#posts" class="o-tabs__link" data-ed-toggle="tab" data-filter="posts">
					<?php echo JText::_('COM_ED_POSTS');?>
				</a>
			</div>

			<?php if ($this->config->get('main_postassignment')) { ?>
			<div class="o-tabs__item <?php echo $filter == 'assigned' ? 'active' : '';?>" data-ed-tab>
				<a href="#assigned" class="o-tabs__link" data-ed-toggle="tab" data-filter="assigned">
					<?php echo JText::_('COM_ED_ASSIGNED');?>
				</a>
			</div>
			<?php } ?>

			<div class="o-tabs__item <?php echo $filter == 'replies' ? 'active' : '';?>" data-ed-tab>
				<a href="#replies" class="o-tabs__link" data-ed-toggle="tab" data-filter="replies">
					<?php echo JText::_('COM_ED_REPLIES');?>
				</a>
			</div>
			
			<?php if ($this->config->get('main_badges')) { ?>
			<div class="o-tabs__item <?php echo $filter == 'badges' ? 'active' : '';?>" data-ed-tab>
				<a href="#badges" class="o-tabs__link" data-ed-toggle="tab" data-filter="badges">
					<?php echo JText::_('COM_EASYDISCUSS_BADGES');?>
				</a>
			</div>
			<?php } ?>

			<?php if ($this->config->get('main_favorite')) { ?>
			<div class="o-tabs__item <?php echo $filter == 'favourites' ? 'active' : '';?>" data-ed-tab>
				<a href="#favourites" class="o-tabs__link" data-ed-toggle="tab" data-filter="favourites">
					<?php echo JText::_('COM_EASYDISCUSS_FAVOURITES');?>
				</a>
			</div>
			<?php } ?>

			<?php if ($this->config->get('main_points')) { ?>
			<div class="o-tabs__item <?php echo $filter == 'points' ? 'active' : '';?>" data-ed-tab>
				<a href="#points" class="o-tabs__link" data-ed-toggle="tab" data-filter="points">
					<?php echo JText::_('COM_EASYDISCUSS_POINTS_HISTORY');?>
				</a>
			</div>
			<?php } ?>
		</div>
			
		<div class="tab-content">
			<div id="posts" class="tab-pane <?php echo $filter == 'posts' ? 'active' : '';?> <?php echo !$posts ? 'is-empty' : '';?>" data-ed-list-wrapper>
				<div class="ed-posts-list l-stack" data-ed-list itemscope itemtype="http://schema.org/ItemList">
					<?php if ($filter == 'posts') { ?>
						<?php echo $this->output('site/posts/list', [
							'featured' => [],
							'posts' => $posts,
							'pagination' => $pagination,
							'hideTitles' => true
						]); ?>
					<?php } ?>
				</div>

				<?php echo $this->html('loading.block'); ?>
				
				<?php echo $this->html('card.emptyCard', 'far fa-newspaper', 'COM_EASYDISCUSS_EMPTY_DISCUSSION_LIST'); ?>
			</div>

			<?php if ($this->config->get('main_postassignment')) { ?>
			<div id="assigned" class="tab-pane <?php echo $filter == 'assigned' ? 'active' : '';?>">
				<div class="ed-posts-list l-stack" data-ed-list itemscope itemtype="http://schema.org/ItemList">
					<?php if ($filter == 'assigned') { ?>
						<?php echo $this->output('site/posts/list', [
							'featured' => [],
							'posts' => $posts,
							'pagination' => $pagination,
							'hideTitles' => true
						]); ?>
					<?php } ?>
				</div>

				<?php echo $this->html('loading.block');?>
				<?php echo $this->html('card.emptyCard', 'far fa-newspaper', 'COM_EASYDISCUSS_EMPTY_DISCUSSION_LIST'); ?>
			</div>
			<?php } ?>

			<div id="replies" class="tab-pane <?php echo $filter == 'replies' ? 'active' : '';?>">
				<div class="ed-posts-list l-stack" data-ed-list itemscope itemtype="http://schema.org/ItemList">
					<?php if ($filter == 'replies') { ?>
						<?php echo $this->output('site/posts/list', [
							'featured' => [],
							'posts' => $posts,
							'pagination' => $pagination,
							'hideTitles' => true
						]); ?>
					<?php } ?>
				</div>
				<?php echo $this->html('loading.block');?>
				<?php echo $this->html('card.emptyCard', 'far fa-newspaper', 'COM_EASYDISCUSS_EMPTY_DISCUSSION_LIST'); ?>
			</div>

			<?php if ($this->config->get('main_badges')) { ?>
			<div id="badges" class="tab-pane <?php echo !$badges ? 'is-empty' : '';?>">
				<div class="l-stack">
					<?php foreach ($badges as $badge) { ?>
						<?php echo $this->html('card.badge', $badge); ?>
					<?php } ?>
				</div>

				<?php echo $this->html('card.emptyCard', 'fas fa-certificate', 'COM_ED_NO_ACHIEVEMENTS_YET'); ?>
			</div>
			<?php } ?>

			<?php if ($this->config->get('main_favorite')) { ?>
			<div id="favourites" class="tab-pane">
				<div class="ed-posts-list l-stack" data-ed-list itemscope itemtype="http://schema.org/ItemList">
					<?php if ($filter == 'favourites') { ?>
						<?php echo $this->output('site/posts/list', [
							'featured' => [],
							'posts' => $posts,
							'pagination' => $pagination,
							'hideTitles' => true
						]); ?>
					<?php } ?>
				</div>

				<?php echo $this->html('loading.block');?>
				<?php echo $this->html('card.emptyCard', 'far fa-newspaper', 'COM_EASYDISCUSS_EMPTY_DISCUSSION_LIST'); ?>
			</div>
			<?php } ?>

			<?php if ($this->config->get('main_points')) { ?>
			<div id="points" class="tab-pane">
				<div class="ed-point-listing l-stack" data-ed-list>
				</div>

				<?php echo $this->html('loading.block');?>
				<?php echo $this->html('card.emptyCard', 'fas fa-certificate', 'COM_EASYDISCUSS_USER_NO_POINTS_HISTORY'); ?>
			</div>
			<?php } ?>
		</div>
	</div>
</div>