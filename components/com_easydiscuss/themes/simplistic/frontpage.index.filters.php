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
<div class="discuss-filter mt-20 mr-10">

	<!-- Filter tabs -->
	<ul class="nav nav-tabs">
		<li class="filterItem<?php echo !$activeFilter || $activeFilter == 'allposts' || $activeFilter == 'all' ? ' active' : '';?>" data-filter-tab data-filter-type="allpost">
			<a class="btn-small allPostsFilter" href="javascript:void(0);">
				<?php echo JText::_('COM_EASYDISCUSS_FILTER_ALL_POSTS'); ?>
			</a>
		</li>

		<?php if($system->my->id != 0) { ?>
		<li class="filterItem<?php echo $activeFilter == 'mine' ? ' active' : '';?>" data-filter-tab data-filter-type="mine">
			<a class="mineFilter" href="javascript:void(0);">
				<?php echo JText::_('COM_EASYDISCUSS_MY_POSTS' );?>
			</a>
		</li>
		<?php } ?>

		<?php if(EDC::isSiteAdmin() || EDC::isModerator()) { ?>
		<li class="filterItem<?php echo $activeFilter == 'assigned' ? ' active' : '';?>" data-filter-tab data-filter-type="assigned">
			<a class="mineFilter" href="javascript:void(0);">
				<?php echo JText::_('COM_EASYDISCUSS_ASSIGNED_POSTS' );?>
				<span class="label label-important label-notification"><?php echo $assignedCount; ?></span>
			</a>
		</li>
		<?php } ?>

		<?php if( $system->config->get('layout_enablefilter_new') && $system->my->id != 0 && $unreadCount > 0) { ?>
		<li class="filterItem<?php echo $activeFilter == 'unread' ? ' active' : '';?>" data-filter-tab data-filter-type="unread">
			<a class="newPostsFilter" href="javascript:void(0);">
				<?php echo JText::_( 'COM_EASYDISCUSS_NEW_STATUS' );?>
				<span class="label label-important label-notification"><?php echo $unreadCount; ?></span>
			</a>
		</li>
		<?php } ?>

		<?php if( $system->config->get('main_qna') && $system->config->get( 'layout_enablefilter_unresolved' ) ) { ?>
		<li class="filterItem<?php echo $activeFilter == 'unresolved' ? ' active' : '';?>" data-filter-tab data-filter-type="unresolved">
			<a class="unResolvedFilter" href="javascript:void(0);">
				<?php echo JText::_( 'COM_EASYDISCUSS_FILTER_UNRESOLVED' );?>
				<?php if( $unresolvedCount > 0 ){ ?>
				<span class="label label-important label-notification"><?php echo $unresolvedCount; ?></span>
				<?php } ?>
			</a>
		</li>
		<?php } ?>

		<?php if( $system->config->get('main_qna') && $system->config->get( 'layout_enablefilter_resolved' ) ) { ?>
		<li class="filterItem<?php echo $activeFilter == 'resolved' ? ' active' : '';?>" data-filter-tab data-filter-type="resolved">
			<a class="resolvedFilter" href="javascript:void(0);">
				<?php echo JText::_( 'COM_EASYDISCUSS_FILTER_RESOLVED' );?>
				<?php if( $resolvedCount > 0 ){ ?>
				<span class="label label-important label-notification"><?php echo $resolvedCount; ?></span>
				<?php } ?>
			</a>
		</li>
		<?php } ?>

		<?php if( $system->config->get( 'layout_enablefilter_unanswered' ) ){ ?>
		<li class="filterItem<?php echo $activeFilter == 'unanswered' ? ' active' : '';?>" data-filter-tab data-filter-type="unanswered">
			<a class="unAnsweredFilter" href="javascript:void(0);">
				<?php echo JText::_('COM_EASYDISCUSS_FILTER_UNANSWERED'); ?>
				<?php if( $unansweredCount > 0 ){ ?>
				<span class="label label-important label-notification"><?php echo $unansweredCount; ?></span>
				<?php } ?>
			</a>
		</li>
		<?php } ?>

	</ul>

	<!-- Sort tabs -->
	<ul class="nav nav-tabs nav-tabs-alt">
		<li class="filterItem secondary-nav<?php echo $activeSort == 'latest' || $activeSort == '' ? ' active' : '';?>" data-sort-tab data-sort-type="latest">
			<a class="btn-small sortLatest" href="javascript:void(0);"><?php echo JText::_( 'COM_EASYDISCUSS_SORT_LATEST' );?></a>
		</li>
		<li class="filterItem secondary-nav<?php echo $activeSort == 'popular' ? ' active' : '';?>" data-sort-tab data-sort-type="popular">
			<a class="sortPopular" href="javascript:void(0);" <?php echo ($activeFilter == 'unread') ? 'style="display:none;"' : ''; ?> ><?php echo JText::_( 'COM_EASYDISCUSS_SORT_POPULAR' );?></a>
		</li>
	</ul>
</div>
