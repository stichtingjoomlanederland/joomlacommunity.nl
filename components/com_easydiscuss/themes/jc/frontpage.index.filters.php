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
<!-- Category Filters -->
<div class="forum-filters">

	<!-- Filter tabs -->
	<div class="btn-group">
		<a class="btn btn-default allPostsFilter filterItem<?php echo !$activeFilter || $activeFilter == 'allposts' || $activeFilter == 'all' ? ' active' : '';?>" data-filter-tab data-filter-type="allpost" href="javascript:void(0);">
				<?php echo JText::_('COM_EASYDISCUSS_FILTER_ALL_POSTS'); ?>
			</a>
	
		<?php if( $system->config->get('layout_enablefilter_new') && $system->my->id != 0 && $unreadCount > 0) { ?>
		<a class="btn btn-default newPostsFilter filterItem<?php echo $activeFilter == 'unread' ? ' active' : '';?>" data-filter-tab data-filter-type="unread" href="javascript:void(0);">
				<span class="badge pull-right"><?php echo $unreadCount; ?></span>
				<?php echo JText::_( 'COM_EASYDISCUSS_NEW_STATUS' );?> 
			</a>

		<?php } ?>
		<?php if( $system->config->get('main_qna') && $system->config->get( 'layout_enablefilter_unresolved' ) ) { ?>
		<a class="btn btn-default unResolvedFilter filterItem<?php echo $activeFilter == 'unresolved' ? ' active' : '';?>" data-filter-tab data-filter-type="unresolved" href="javascript:void(0);">
				<?php if( $unresolvedCount > 0 ){ ?>
				<span class="badge pull-right"><?php echo $unresolvedCount; ?></span>
				<?php } ?>
				<?php echo JText::_( 'COM_EASYDISCUSS_FILTER_UNRESOLVED' );?>
				
			</a>

		<?php } ?>
		<?php if( $system->config->get('main_qna') && $system->config->get( 'layout_enablefilter_resolved' ) ) { ?>
		<a class="btn btn-default resolvedFilter filterItem<?php echo $activeFilter == 'resolved' ? ' active' : '';?>" data-filter-tab data-filter-type="resolved" href="javascript:void(0);">
				<?php if( $resolvedCount > 0 ){ ?>
				<span class="badge pull-right"><?php echo $resolvedCount; ?></span>
				<?php } ?>
				<?php echo JText::_( 'COM_EASYDISCUSS_FILTER_RESOLVED' );?>
				
			</a>
	
		<?php } ?>
		<?php if( $system->config->get( 'layout_enablefilter_unanswered' ) ){ ?>
		<a class="btn btn-default unAnsweredFilter filterItem<?php echo $activeFilter == 'unanswered' ? ' active' : '';?>" data-filter-tab data-filter-type="unanswered" href="javascript:void(0);">
				<?php if( $unansweredCount > 0 ){ ?>
				<span class="badge pull-right"><?php echo $unansweredCount; ?></span>
				<?php } ?>
				<?php echo JText::_('COM_EASYDISCUSS_FILTER_UNANSWERED'); ?>
				
			</a>
	
		<?php } ?>
	</div>

	<!-- Sort tabs -->
	<div class="btn-group pull-right">
		<a class="btn btn-default sortLatest filterItem<?php echo $activeSort == 'latest' || $activeSort == '' ? ' active' : '';?>" data-sort-tab data-sort-type="latest" href="javascript:void(0);">
				<?php echo JText::_( 'COM_EASYDISCUSS_SORT_LATEST' );?>
			</a>
	
		<a class="btn btn-default sortPopular filterItem<?php echo $activeSort == 'popular' ? ' active' : '';?>" data-sort-tab data-sort-type="popular" href="javascript:void(0);" <?php echo ($activeFilter == 'unread') ? 'style="display:none;"' : ''; ?> >
				<?php echo JText::_( 'COM_EASYDISCUSS_SORT_POPULAR' );?>
			</a>
	
	</div>
</div>

