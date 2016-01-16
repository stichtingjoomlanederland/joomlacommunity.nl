<?php
/**
 * @package		EasyDiscuss
 * @copyright	Copyright (C) 2010 Stack Ideas Private Limited. All rights reserved.
 * @license		GNU/GPL, see LICENSE.php
 *
 * EasyDiscuss is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * See COPYRIGHT.php for copyright notices and details.
 */
defined('_JEXEC') or die('Restricted access');

$catUnreadCount 	= $category->getUnreadCount( false );

// Set TRUE to exclude featured post in the unresolved count
$catUnresolvedCount = $category->getUnresolvedCount( false );

$catUnansweredCount = $category->getUnansweredCount( false);
?>

<!-- Category Filters: Mobile View -->
<div class="discuss-filter-mobile butt-dropdown v-mobile">
	<a type="button" class="butt butt-default dropdown-toggle" data-toggle="dropdown">
		<i class="i i-angle-down muted"></i>
		&nbsp;
		Action
	</a>
	<ul class="dropdown-menu" role="menu">
		<!-- Filter tabs -->
		<li class="filterItem<?php echo !$category->activeFilter || $category->activeFilter == 'allposts' || $category->activeFilter == 'all' ? ' active' : '';?>" data-filter-tab data-filter-type="allpost">
			<a class="allPostsFilter" href="javascript:void(0);">
				<?php echo JText::_('COM_EASYDISCUSS_FILTER_ALL_POSTS'); ?>
			</a>
		</li>

		<?php if( $system->config->get('layout_enablefilter_new') && $system->my->id != 0 && $catUnreadCount > 0) { ?>
		<li class="filterItem<?php echo $category->activeFilter == 'unread' ? ' active' : '';?>" data-filter-tab data-filter-type="unread">
			<a class="newPostsFilter" href="javascript:void(0);">
				<?php echo JText::_( 'COM_EASYDISCUSS_NEW_STATUS' );?>
				<span class="label label-important label-notification"><?php echo $catUnreadCount;?></span>
			</a>
		</li>
		<?php } ?>

		<?php if( $system->config->get('layout_enablefilter_unresolved') && $system->config->get('main_qna') && $catUnresolvedCount > 0) { ?>
		<li class="filterItem<?php echo $category->activeFilter == 'unresolved' ? ' active' : '';?>" data-filter-tab data-filter-type="unresolved">
			<a class="unResolvedFilter" href="javascript:void(0);">
				<?php echo JText::_( 'COM_EASYDISCUSS_FILTER_UNRESOLVED' );?>
				<b><?php echo $catUnresolvedCount;?></b>
			</a>
		</li>
		<?php } ?>

		<?php if( $system->config->get('layout_enablefilter_unanswered') && $catUnansweredCount > 0) { ?>
		<li class="filterItem<?php echo $category->activeFilter == 'unanswered' ? ' active' : '';?>" data-filter-tab data-filter-type="unanswered">
			<a class="unAnsweredFilter" href="javascript:void(0);">
				<?php echo JText::_('COM_EASYDISCUSS_FILTER_UNANSWERED'); ?>
				<?php if( $category->getUnansweredCount() ){ ?>
				<b><?php echo $catUnansweredCount;?></b>
				<?php } ?>
			</a>
		</li>
		<?php } ?>
		<!-- /Filter tabs -->
		<li class="divider"></li>
		<!-- Sort tabs -->
		<li class="filterItem<?php echo $category->activeSort == 'latest' || $category->activeSort == '' ? ' active' : '';?> secondary-nav" data-sort-tab data-sort-type="latest">
			<a class="sortLatest" href="javascript:void(0);"><?php echo JText::_( 'COM_EASYDISCUSS_SORT_LATEST' );?></a>
		</li>
		<li class="filterItem<?php echo $category->activeSort == 'popular' ? ' active' : '';?> secondary-nav" data-sort-tab data-sort-type="popular">
			<a class="sortPopular" href="javascript:void(0);" <?php echo ($category->activeFilter == 'unread') ? 'style="display:none;"' : ''; ?> ><?php echo JText::_( 'COM_EASYDISCUSS_SORT_POPULAR' );?></a>
		</li>
		<!-- Sort tabs -->
	</ul>
</div>

<!-- Category Filters -->
<div class="discuss-filter filter-posts clearfix h-mobile">
	<ul class="nav-filter reset-ul float-li float-l">
		<li class="filterItem<?php echo !$category->activeFilter || $category->activeFilter == 'allposts' || $category->activeFilter == 'all' ? ' active' : '';?>" data-filter-tab data-filter-type="allpost">
			<a class="butt butt-default allPostsFilter" href="javascript:void(0);">
				<?php echo JText::_('COM_EASYDISCUSS_FILTER_ALL_POSTS'); ?>
			</a>
		</li>

		<?php if( $system->config->get('layout_enablefilter_new') && $system->my->id != 0 && $catUnreadCount > 0) { ?>
		<li class="filterItem<?php echo $category->activeFilter == 'unread' ? ' active' : '';?>" data-filter-tab data-filter-type="unread">
			<a class="butt butt-default newPostsFilter" href="javascript:void(0);">
				<?php echo JText::_( 'COM_EASYDISCUSS_NEW_STATUS' );?>
				<span class="label label-important label-notification"><?php echo $catUnreadCount;?></span>
			</a>
		</li>
		<?php } ?>

		<?php if( $system->config->get('layout_enablefilter_unresolved') && $system->config->get('main_qna') && $catUnresolvedCount > 0) { ?>
		<li class="filterItem<?php echo $category->activeFilter == 'unresolved' ? ' active' : '';?>" data-filter-tab data-filter-type="unresolved">
			<a class="butt butt-default unResolvedFilter" href="javascript:void(0);">
				<?php echo JText::_( 'COM_EASYDISCUSS_FILTER_UNRESOLVED' );?>
				<b><?php echo $catUnresolvedCount;?></b>
			</a>
		</li>
		<?php } ?>

		<?php if( $system->config->get('layout_enablefilter_unanswered') && $catUnansweredCount > 0) { ?>
		<li class="filterItem<?php echo $category->activeFilter == 'unanswered' ? ' active' : '';?>" data-filter-tab data-filter-type="unanswered">
			<a class="butt butt-default unAnsweredFilter" href="javascript:void(0);">
				<?php echo JText::_('COM_EASYDISCUSS_FILTER_UNANSWERED'); ?>
				<?php if( $category->getUnansweredCount() ){ ?>
				<b><?php echo $catUnansweredCount;?></b>
				<?php } ?>
			</a>
		</li>
		<?php } ?>
	</ul>
	<ul class="nav-sort reset-ul float-li float-r">
		<li class="filterItem<?php echo $category->activeSort == 'latest' || $category->activeSort == '' ? ' active' : '';?> secondary-nav" data-sort-tab data-sort-type="latest">
			<a class="butt butt-default sortLatest" href="javascript:void(0);"><?php echo JText::_( 'COM_EASYDISCUSS_SORT_LATEST' );?></a>
		</li>

		<li class="filterItem<?php echo $category->activeSort == 'popular' ? ' active' : '';?> secondary-nav" data-sort-tab data-sort-type="popular">
			<a class="butt butt-default sortPopular" href="javascript:void(0);" <?php echo ($category->activeFilter == 'unread') ? 'style="display:none;"' : ''; ?> ><?php echo JText::_( 'COM_EASYDISCUSS_SORT_POPULAR' );?></a>
		</li>
	</ul>
</div>
