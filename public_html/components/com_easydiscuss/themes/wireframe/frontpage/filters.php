<?php
/**
* @package      EasyDiscuss
* @copyright    Copyright (C) 2010 - 2019 Stack Ideas Sdn Bhd. All rights reserved.
* @license      GNU/GPL, see LICENSE.php
* EasyDiscuss is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Restricted access');
?>
<div class="ed-filter-bar t-lg-mb--md">
	
	<div class="ed-filter-bar__sort-tabs">
		<!-- Filter tabs -->
		<ul class="o-tabs o-tabs--ed">
			<li class="o-tabs__item <?php echo !$activeFilter || $activeFilter == 'allposts' || $activeFilter == 'all' ? ' active' : '';?>"
				data-filter-tab
				data-filter-type="allposts"
				data-filter-catid="<?php echo $menuCatId; ?>"
			>
				<a class="o-tabs__link allPostsFilter" data-filter-anchor href="<?php echo EDR::_($baseUrl . '&filter=');?>">
					<?php echo JText::_('COM_EASYDISCUSS_FILTER_ALL_POSTS'); ?>
				</a>
			</li>

			<?php if($this->config->get('main_qna') && $this->config->get('layout_enablefilter_unresolved')) { ?>
			<li class="o-tabs__item <?php echo $activeFilter == 'unresolved' ? ' active' : '';?>"
				data-filter-tab
				data-filter-type="unresolved"
				data-filter-catid="<?php echo $menuCatId; ?>"
			>
				<a class="o-tabs__link unResolvedFilter" data-filter-anchor href="<?php echo EDR::_($baseUrl . '&filter=unresolved');?>">
					<?php echo JText::_('COM_EASYDISCUSS_FILTER_UNRESOLVED');?>
				</a>
			</li>
			<?php } ?>

			<?php if($this->config->get('main_qna') && $this->config->get('layout_enablefilter_resolved')) { ?>
			<li class="o-tabs__item <?php echo $activeFilter == 'resolved' ? ' active' : '';?>"
				data-filter-tab
				data-filter-type="resolved"
				data-filter-catid="<?php echo $menuCatId; ?>"
			>
				<a class="o-tabs__link resolvedFilter" data-filter-anchor href="<?php echo EDR::_($baseUrl . '&filter=resolved');?>">
					<?php echo JText::_('COM_EASYDISCUSS_FILTER_RESOLVED');?>
				</a>
			</li>
			<?php } ?>

			<?php if($this->config->get('layout_enablefilter_unanswered')){ ?>
			<li class="o-tabs__item <?php echo $activeFilter == 'unanswered' ? ' active' : '';?>"
				data-filter-tab
				data-filter-type="unanswered"
				data-filter-catid="<?php echo $menuCatId; ?>"
			>
				<a class="o-tabs__link unAnsweredFilter" data-filter-anchor href="<?php echo EDR::_($baseUrl . '&filter=unanswered');?>">
					<?php echo JText::_('COM_EASYDISCUSS_FILTER_UNANSWERED'); ?>
				</a>
			</li>
			<?php } ?>

			<?php if ($this->config->get('layout_enablefilter_unread') && ED::isLoggedIn()) { ?>
			<li class="o-tabs__item t-xs-hidden <?php echo $activeFilter == 'unread' ? ' active' : '';?>"
				data-filter-tab
				data-filter-type="unread"
				data-filter-catid="<?php echo $menuCatId; ?>"
			>
				<a class="o-tabs__link unreadFilter" data-filter-anchor href="<?php echo EDR::_($baseUrl . '&filter=unread');?>">
					<?php echo JText::_('COM_EASYDISCUSS_FILTER_UNREAD');?>
				</a>
			</li>
			<?php } ?>

		</ul>
	</div>
	<?php $sortBaseUrl = $activeFilter ? $baseUrl . '&filter=' . $activeFilter : $baseUrl; ?>

	<div class="ed-filter-bar__sort-action" data-sort-wrapper>
		<?php echo $this->output('site/frontpage/sorting', array('activeSort' => $activeSort, 'sortBaseUrl' => $sortBaseUrl, 'activeStatus' => $activeStatus, 'view' => $view)); ?>
	</div>

</div>
