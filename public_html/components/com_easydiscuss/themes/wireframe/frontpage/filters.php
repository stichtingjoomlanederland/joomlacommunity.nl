<?php
/**
* @package		EasyDiscuss
* @copyright	Copyright (C) 2010 - 2016 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasyBlog is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');
?>
<div class="ed-filter-bar t-lg-mt--lg t-lg-mb--md">

	<!-- Filter tabs -->
	<ul class="o-tabs o-tabs--ed pull-left">
		<li class="o-tabs__item <?php echo !$activeFilter || $activeFilter == 'allposts' || $activeFilter == 'all' ? ' active' : '';?>" data-filter-tab data-filter-type="allposts">
			<a class="o-tabs__link allPostsFilter" data-filter-anchor href="<?php echo EDR::_('view=index');?>">
				<?php echo JText::_('COM_EASYDISCUSS_FILTER_ALL_POSTS'); ?>
			</a>
		</li>

		<?php if($this->config->get('main_qna') && $this->config->get('layout_enablefilter_unresolved')) { ?>
		<li class="o-tabs__item <?php echo $activeFilter == 'unresolved' ? ' active' : '';?>"
			data-filter-tab
			data-filter-type="unresolved"
		>
			<a class="o-tabs__link unResolvedFilter" data-filter-anchor href="<?php echo EDR::_('view=index&filter=unresolved');?>">
				<?php echo JText::_('COM_EASYDISCUSS_FILTER_UNRESOLVED');?>
			</a>
		</li>
		<?php } ?>

		<?php if($this->config->get('main_qna') && $this->config->get('layout_enablefilter_resolved')) { ?>
		<li class="o-tabs__item <?php echo $activeFilter == 'resolved' ? ' active' : '';?>"
			data-filter-tab
			data-filter-type="resolved"
		>
			<a class="o-tabs__link resolvedFilter" data-filter-anchor href="<?php echo EDR::_('view=index&filter=resolved');?>">
				<?php echo JText::_('COM_EASYDISCUSS_FILTER_RESOLVED');?>
			</a>
		</li>
		<?php } ?>

		<?php if($this->config->get('layout_enablefilter_unanswered')){ ?>
		<li class="o-tabs__item <?php echo $activeFilter == 'unanswered' ? ' active' : '';?>"
			data-filter-tab
			data-filter-type="unanswered"
		>
			<a class="o-tabs__link unAnsweredFilter" data-filter-anchor href="<?php echo EDR::_('view=index&filter=unanswered');?>">
				<?php echo JText::_('COM_EASYDISCUSS_FILTER_UNANSWERED'); ?>
			</a>
		</li>
		<?php } ?>

		<?php if ($this->config->get('layout_enablefilter_unread')) { ?>
		<li class="o-tabs__item <?php echo $activeFilter == 'unread' ? ' active' : '';?>"
			data-filter-tab
			data-filter-type="unread"
		>
			<a class="o-tabs__link unreadFilter" data-filter-anchor href="<?php echo EDR::_('view=index&filter=unread');?>">
				<?php echo JText::_('COM_EASYDISCUSS_FILTER_UNREAD');?>
			</a>
		</li>
		<?php } ?>

	</ul>

	<!-- Sort tabs -->
	<div class="ed-filter-bar__sort-action pull-right">
		<select data-index-sort-filter>
		  <option value="latest" <?php echo $activeSort == 'latest' || $activeSort == '' ? ' selected="true"' : '';?> data-sort-tab data-sort-type="latest"><?php echo JText::_('COM_EASYDISCUSS_SORT_LATEST');?></option>
		  <?php if ($activeFilter != 'unread') { ?>
		  	<option value="popular" <?php echo $activeSort == 'popular' ? ' selected="true"' : '';?> data-sort-tab data-sort-type="popular"><?php echo JText::_('COM_EASYDISCUSS_SORT_POPULAR');?></option>
		  	<option value="title" <?php echo $activeSort == 'title' ? ' selected="true"' : '';?> data-sort-tab data-sort-type="title"><?php echo JText::_('COM_EASYDISCUSS_SORT_TITLE');?></option>
		  <?php } ?>
		</select>
	</div>

</div>
