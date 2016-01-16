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


<!-- Category Filters -->
<div class="forum-filters">

	<div class="btn-group">
		<li class="btn btn-default allPostsFilter filterItem<?php echo !$category->activeFilter || $category->activeFilter == 'allposts' || $category->activeFilter == 'all' ? ' active' : '';?>" data-filter-tab data-filter-type="allpost" href="javascript:void(0);">
				<?php echo JText::_('COM_EASYDISCUSS_FILTER_ALL_POSTS'); ?>
			</a>


		<?php if( $system->config->get('layout_enablefilter_new') && $system->my->id != 0 && $catUnreadCount > 0) { ?>
		<li class="btn btn-default newPostsFilter filterItem<?php echo $category->activeFilter == 'unread' ? ' active' : '';?>" data-filter-tab data-filter-type="unread" href="javascript:void(0);">
				<span class="badge pull-right"><?php echo $catUnreadCount; ?></span>
				<?php echo JText::_( 'COM_EASYDISCUSS_NEW_STATUS' );?>
	
			</a>

		<?php } ?>

		<?php if( $system->config->get('layout_enablefilter_unresolved') && $system->config->get('main_qna') && $catUnresolvedCount > 0) { ?>
		<li class="btn btn-default unResolvedFilter filterItem<?php echo $category->activeFilter == 'unresolved' ? ' active' : '';?>" data-filter-tab data-filter-type="unresolved" href="javascript:void(0);">
				<span class="badge pull-right"><?php echo $catUnresolvedCount; ?></span>
				<?php echo JText::_( 'COM_EASYDISCUSS_FILTER_UNRESOLVED' );?>
	
			</a>

		<?php } ?>

		<?php if( $system->config->get('layout_enablefilter_unanswered') && $catUnansweredCount > 0) { ?>
		<li class="btn btn-default unAnsweredFilter filterItem<?php echo $category->activeFilter == 'unanswered' ? ' active' : '';?>" data-filter-tab data-filter-type="unanswered" href="javascript:void(0);">
				<?php if( $category->getUnansweredCount() ){ ?>
				<span class="badge pull-right"><?php echo $catUnansweredCount; ?></span>
				<?php } ?>
				<?php echo JText::_('COM_EASYDISCUSS_FILTER_UNANSWERED'); ?>
				
			</a>

		<?php } ?>
	</div>
	<div class="btn-group pull-right">
		<li class="btn btn-default sortLatest filterItem<?php echo $category->activeSort == 'latest' || $category->activeSort == '' ? ' active' : '';?> secondary-nav" data-sort-tab data-sort-type="latest" href="javascript:void(0);"><?php echo JText::_( 'COM_EASYDISCUSS_SORT_LATEST' );?></a>


		<li class="btn btn-default sortPopular filterItem<?php echo $category->activeSort == 'popular' ? ' active' : '';?> secondary-nav" data-sort-tab data-sort-type="popular" href="javascript:void(0);" <?php echo ($category->activeFilter == 'unread') ? 'style="display:none;"' : ''; ?> ><?php echo JText::_( 'COM_EASYDISCUSS_SORT_POPULAR' );?></a>

	</div>
</div>
