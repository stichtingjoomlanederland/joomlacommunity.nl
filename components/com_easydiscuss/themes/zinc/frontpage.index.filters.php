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
		<li class="filterItem<?php echo !$activeFilter || $activeFilter == 'allposts' || $activeFilter == 'all' ? ' active' : '';?>" data-filter-tab data-filter-type="allpost">
			<a class="allPostsFilter" href="javascript:void(0);">
				<?php echo JText::_('COM_EASYDISCUSS_FILTER_ALL_POSTS'); ?>
			</a>
		</li>
		<?php if( $system->config->get('layout_enablefilter_new') && $system->my->id != 0 && $unreadCount > 0) { ?>
		<li class="filterItem<?php echo $activeFilter == 'unread' ? ' active' : '';?>" data-filter-tab data-filter-type="unread">
			<a class="newPostsFilter" href="javascript:void(0);">
				<?php echo JText::_( 'COM_EASYDISCUSS_NEW_STATUS' );?>
				<b><?php echo $unreadCount; ?></b>
			</a>
		</li>
		<?php } ?>
		<?php if( $system->config->get('main_qna') && $system->config->get( 'layout_enablefilter_unresolved' ) ) { ?>
		<li class="filterItem<?php echo $activeFilter == 'unresolved' ? ' active' : '';?>" data-filter-tab data-filter-type="unresolved">
			<a class="unResolvedFilter" href="javascript:void(0);">
				<?php echo JText::_( 'COM_EASYDISCUSS_FILTER_UNRESOLVED' );?>
				<?php if( $unresolvedCount > 0 ){ ?>
				<b><?php echo $unresolvedCount; ?></b>
				<?php } ?>
			</a>
		</li>
		<?php } ?>
		<?php if( $system->config->get('main_qna') && $system->config->get( 'layout_enablefilter_resolved' ) ) { ?>
		<li class="filterItem<?php echo $activeFilter == 'resolved' ? ' active' : '';?>" data-filter-tab data-filter-type="resolved">
			<a class="resolvedFilter" href="javascript:void(0);">
				<?php echo JText::_( 'COM_EASYDISCUSS_FILTER_RESOLVED' );?>
				<?php if( $resolvedCount > 0 ){ ?>
				<b><?php echo $resolvedCount; ?></b>
				<?php } ?>
			</a>
		</li>
		<?php } ?>
		<?php if( $system->config->get( 'layout_enablefilter_unanswered' ) ){ ?>
		<li class="filterItem<?php echo $activeFilter == 'unanswered' ? ' active' : '';?>" data-filter-tab data-filter-type="unanswered">
			<a class="unAnsweredFilter" href="javascript:void(0);">
				<?php echo JText::_('COM_EASYDISCUSS_FILTER_UNANSWERED'); ?>
				<?php if( $unansweredCount > 0 ){ ?>
				<b><?php echo $unansweredCount; ?></b>
				<?php } ?>
			</a>
		</li>
		<?php } ?>
		<!-- /Filter tabs -->
		<li class="divider"></li>
		<!-- Sort tabs -->
		<li class="filterItem<?php echo $activeSort == 'latest' || $activeSort == '' ? ' active' : '';?>" data-sort-tab data-sort-type="latest">
			<a class="sortLatest" href="javascript:void(0);">
				<?php echo JText::_( 'COM_EASYDISCUSS_SORT_LATEST' );?>
			</a>
		</li>
		<li class="filterItem<?php echo $activeSort == 'popular' ? ' active' : '';?>" data-sort-tab data-sort-type="popular">
			<a class="sortPopular" href="javascript:void(0);" <?php echo ($activeFilter == 'unread') ? 'style="display:none;"' : ''; ?> >
				<?php echo JText::_( 'COM_EASYDISCUSS_SORT_POPULAR' );?>
			</a>
		</li>
		<!-- Sort tabs -->
	</ul>
</div>

<!-- Category Filters -->
<div class="discuss-filter filter-posts clearfix h-mobile">
	<!-- Filter tabs -->
	<ul class="nav-filter reset-ul float-li float-l">
		<li class="filterItem<?php echo !$activeFilter || $activeFilter == 'allposts' || $activeFilter == 'all' ? ' active' : '';?>" data-filter-tab data-filter-type="allpost">
			<a class="butt butt-default allPostsFilter" href="javascript:void(0);">
				<?php echo JText::_('COM_EASYDISCUSS_FILTER_ALL_POSTS'); ?>
			</a>
		</li>
		<?php if( $system->config->get('layout_enablefilter_new') && $system->my->id != 0 && $unreadCount > 0) { ?>
		<li class="filterItem<?php echo $activeFilter == 'unread' ? ' active' : '';?>" data-filter-tab data-filter-type="unread">
			<a class="butt butt-default newPostsFilter" href="javascript:void(0);">
				<?php echo JText::_( 'COM_EASYDISCUSS_NEW_STATUS' );?>
				<b><?php echo $unreadCount; ?></b>
			</a>
		</li>
		<?php } ?>
		<?php if( $system->config->get('main_qna') && $system->config->get( 'layout_enablefilter_unresolved' ) ) { ?>
		<li class="filterItem<?php echo $activeFilter == 'unresolved' ? ' active' : '';?>" data-filter-tab data-filter-type="unresolved">
			<a class="butt butt-default unResolvedFilter" href="javascript:void(0);">
				<?php echo JText::_( 'COM_EASYDISCUSS_FILTER_UNRESOLVED' );?>
				<?php if( $unresolvedCount > 0 ){ ?>
				<b><?php echo $unresolvedCount; ?></b>
				<?php } ?>
			</a>
		</li>
		<?php } ?>
		<?php if( $system->config->get('main_qna') && $system->config->get( 'layout_enablefilter_resolved' ) ) { ?>
		<li class="filterItem<?php echo $activeFilter == 'resolved' ? ' active' : '';?>" data-filter-tab data-filter-type="resolved">
			<a class="butt butt-default resolvedFilter" href="javascript:void(0);">
				<?php echo JText::_( 'COM_EASYDISCUSS_FILTER_RESOLVED' );?>
				<?php if( $resolvedCount > 0 ){ ?>
				<b><?php echo $resolvedCount; ?></b>
				<?php } ?>
			</a>
		</li>
		<?php } ?>
		<?php if( $system->config->get( 'layout_enablefilter_unanswered' ) ){ ?>
		<li class="filterItem<?php echo $activeFilter == 'unanswered' ? ' active' : '';?>" data-filter-tab data-filter-type="unanswered">
			<a class="butt butt-default unAnsweredFilter" href="javascript:void(0);">
				<?php echo JText::_('COM_EASYDISCUSS_FILTER_UNANSWERED'); ?>
				<?php if( $unansweredCount > 0 ){ ?>
				<b><?php echo $unansweredCount; ?></b>
				<?php } ?>
			</a>
		</li>
		<?php } ?>
	</ul>

	<!-- Sort tabs -->
	<ul class="nav-sort reset-ul float-li float-r">
		<li class="filterItem<?php echo $activeSort == 'latest' || $activeSort == '' ? ' active' : '';?>" data-sort-tab data-sort-type="latest">
			<a class="butt butt-default sortLatest" href="javascript:void(0);">
				<?php echo JText::_( 'COM_EASYDISCUSS_SORT_LATEST' );?>
			</a>
		</li>
		<li class="filterItem<?php echo $activeSort == 'popular' ? ' active' : '';?>" data-sort-tab data-sort-type="popular">
			<a class="butt butt-default sortPopular" href="javascript:void(0);" <?php echo ($activeFilter == 'unread') ? 'style="display:none;"' : ''; ?> >
				<?php echo JText::_( 'COM_EASYDISCUSS_SORT_POPULAR' );?>
			</a>
		</li>
	</ul>
</div>
