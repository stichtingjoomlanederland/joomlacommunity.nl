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
<div class="discuss-category-header">
	<div class="discuss-subscribe pull-right">

		<?php if($system->config->get('main_ed_categorysubscription')){ ?>
			<?php if(!$category->container) { ?>
				<?php echo DiscussHelper::getSubscriptionHTML( $system->my->id , $category->id, 'category'); ?>
			<?php } ?>
		<?php } ?>
		
		<?php if( $system->config->get( 'main_rss' ) ){ ?>
		<a href="<?php echo $category->getRSSLink();?>" class="via-feed has-tip atr">
			<i class="icon-ed-rss" ></i>
			<div class="tooltip tooltip-ed top in">
				<div class="tooltip-arrow"></div>
				<div class="tooltip-inner"><?php echo JText::_( 'COM_EASYDISCUSS_SUBSCRIBE_TO_CATEGORY_DISCUSSION'); ?></div>
			</div>
		</a>
		<?php } ?>
	</div>
	<div class="fd-cf">
		<div class="pull-left">
			<a href="<?php echo DiscussRouter::getCategoryRoute( $category->id );?>">
				<h2 class="discuss-component-title">
					<?php echo $category->getTitle();?>
				</h2>
			</a>
			<?php if( $category->getParam( 'show_description')){ ?>
			<p class="category-desp"><?php echo $category->description;?></p>
			<?php } ?>
		</div>
	</div>

	<div class="discuss-category-info">

		<div class="fd-cf mb-10 discuss-category-statistic">
			<?php if( $system->config->get( 'layout_category_stats' ) ){ ?>
			<span class="pull-left"><i class="icon-columns"></i> <?php echo JText::_( 'COM_EASYDISCUSS_TOTAL_POSTS' );?>: <?php echo $category->getPostCount();?></span>
			<?php } ?>

			<?php $moderatorHTML = DiscussHelper::getHelper( 'Moderator' )->showModeratorNameHTML( $category->id ); ?>
			<?php if( $system->config->get( 'layout_show_moderators' ) && $moderatorHTML !== false && !empty( $moderatorHTML ) ) { ?>
			<span>
				<i class="icon-user"></i> <?php echo $moderatorHTML; ?>
			</span>
			<?php } ?>
			&nbsp;
		</div>


		<?php if( $category->nestedLink ) { ?>
		<div class="fd-cf mb-10">
			<span class="pull-left mr-5"><i class="icon-folder-open"></i> <?php echo JText::_( 'COM_EASYDISCUSS_SUBCATEGORY' );?>: </span>
			<ul class="unstyled nav-bar discuss-category-sublist">
				<?php echo $category->nestedLink; ?>
			</ul>
		</div>
		<?php } ?>

		<?php if( $category->canPost() && !$category->container && $acl->allowed('add_question', 0 ) ){ ?>
			<a class="btn btn-primary btn-new-post btn-small" href="<?php echo DiscussRouter::getAskRoute( $category->id );?>"><i class="icon-plus-sign"></i> <?php echo JText::_( 'COM_EASYDISCUSS_NEW_POST' );?></a>
		<?php } ?>
	</div>

</div>

<div class="tab-content">
	<div class="tab-pane active discussPostsList" data-id="<?php echo $category->id; ?>" data-view="categories">

		<?php if( !empty( $category->posts ) || !empty( $category->featured ) ){ ?>
			<?php if( !empty( $category->featured ) ){ ?>
				<h4 class="discuss-featured-title"><?php echo JText::_( 'COM_EASYDISCUSS_FEATURED_TOPICS' );?></h4>
				<ul class="unstyled discuss-list featured clearfix" itemscope itemtype="http://schema.org/ItemList">
				<?php foreach( $category->featured as $featuredPost ){ ?>
					<?php echo $this->loadTemplate( 'frontpage.post.php' , array( 'post' => $featuredPost ) ); ?>
				<?php } ?>
				</ul>
			<?php } ?>
		<?php } ?>

		<div class="categoryFilters">
			<?php echo $this->loadTemplate( 'frontpage.category.filters.php' , array( 'category' => $category ) ); ?>
		</div>
		<div class="clearfix"></div>

		<?php if( !empty( $category->posts ) || empty( $category->featured ) ){ ?>
			<ul class="unstyled discuss-list normal clearfix" itemscope itemtype="http://schema.org/ItemList">
				<div class="loading-bar loader" style="display:none;">
					<div class="discuss-loader"><?php echo JText::_( 'COM_EASYDISCUSS_LOADING'); ?></div>
				</div>

				<?php if( !empty( $category->posts) ){ ?>
					<?php foreach( $category->posts as $post ){ ?>
						<?php echo $this->loadTemplate( 'frontpage.post.php' , array( 'post' => $post ) ); ?>
					<?php } ?>
				<?php } ?>
			</ul>

			<?php if( !empty( $category->posts ) && $pagination ){ ?>
				<div class="dc-pagination">
				<?php echo $pagination->getPagesLinks();?>
				</div>

				<div class="hr-style1"></div>
			<?php } ?>

		<?php } else { ?>
			<div class="empty">
				<div><?php echo JText::_( 'COM_EASYDISCUSS_EMPTY_DISCUSSION_LIST' );?></div>
			</div>
			<div class="hr-style1"></div>
		<?php } ?>

		<?php if( empty( $category->posts ) && empty( $category->featured ) ){ ?>
			<div class="empty">
				<div><?php echo JText::_( 'COM_EASYDISCUSS_EMPTY_DISCUSSION_LIST' );?></div>
			</div>
			<div class="hr-style1"></div>
		<?php } ?>
	</div>

</div>
