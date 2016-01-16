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
<section class="category-header">
	<header>
		<h2 class="discuss-component-title">
			<a href="<?php echo DiscussRouter::getCategoryRoute( $category->id );?>">
			<?php echo $category->getTitle();?>
			</a>
		</h2>
		<?php if( $category->getParam( 'show_description') && $category->description ){ ?>
		<div><?php echo $category->description;?></div>
		<?php } ?>
	</header>

	<article>
		<p>
			<?php if( $system->config->get( 'layout_category_stats' ) ){ ?>
			<span>
				<i class="i i-leaf muted"></i> 
				&nbsp;
				<?php echo JText::_( 'COM_EASYDISCUSS_TOTAL_POSTS' );?>: <b><?php echo $category->getPostCount();?></b>
			</span>
			<?php } ?>

			<?php $moderatorHTML = DiscussHelper::getHelper( 'Moderator' )->showModeratorNameHTML( $category->id ); ?>
			<?php if( $system->config->get( 'layout_show_moderators' ) && $moderatorHTML !== false && !empty( $moderatorHTML ) ) { ?>
			<span>
				<i class="i i-shield muted"></i> 
				&nbsp;
				<?php echo $moderatorHTML; ?>
			</span>
			<?php } ?>
			<?php if( $category->nestedLink ) { ?>
			<span>
				<i class="i i-folder-open-o muted"></i> 
				&nbsp;
				<?php echo JText::_( 'COM_EASYDISCUSS_SUBCATEGORY' );?>: 
				<?php echo $category->nestedLink; ?>
			</span>
			<?php } ?>
		</p>
	</article>

	<footer class="clearfix">
		<?php if( $category->canPost() && !$category->container && $acl->allowed('add_question', 0 ) ){ ?>
			<a class="butt butt-primary float-r" href="<?php echo DiscussRouter::getAskRoute( $category->id );?>">
				<i class="i i-plus muted"></i> 
				&nbsp;
				<?php echo JText::_( 'COM_EASYDISCUSS_NEW_POST' );?></a>
		<?php } ?>

		<?php echo DiscussHelper::getSubscriptionHTML( $system->my->id , $category->id, 'category'); ?>

		<?php if( $system->config->get( 'main_rss' ) ){ ?>
		<a href="<?php echo $category->getRSSLink();?>" class="butt butt-default">
			<i class="i i-rss muted"></i>
			&nbsp;
			<?php echo JText::_( 'COM_EASYDISCUSS_SUBSCRIBE_TO_CATEGORY_DISCUSSION'); ?>
		</a>
		<?php } ?>
	</footer>
</section>

<hr>

<div class="discussPostsList" data-view="categories" data-id="<?php echo $category->id; ?>">
	<?php if( !empty( $category->posts ) || !empty( $category->featured ) ){ ?>
		<?php if( !empty( $category->featured ) ){ ?>
			<h4 class="discuss-featured-title"><?php echo JText::_( 'COM_EASYDISCUSS_FEATURED_TOPICS' );?></h4>
			<ul class="discuss-list list-featured reset-ul" itemscope itemtype="http://schema.org/ItemList">
			<?php foreach( $category->featured as $featuredPost ){ ?>
				<?php echo $this->loadTemplate( 'frontpage.post.php' , array( 'post' => $featuredPost ) ); ?>
			<?php } ?>
			</ul>
		<?php } ?>
	<?php } ?>

	<div class="categoryFilters">
		<?php echo $this->loadTemplate( 'frontpage.category.filters.php' , array( 'category' => $category ) ); ?>
	</div>

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
		<div class="discuss-pagination">
		<?php echo $pagination->getPagesLinks();?>
		</div>
	<?php } ?>

	<?php } else { ?>
	<div class="discuss-empty">
		<?php echo JText::_( 'COM_EASYDISCUSS_EMPTY_DISCUSSION_LIST' );?>
	</div>
	<?php } ?>

	<?php if( empty( $category->posts ) && empty( $category->featured ) ){ ?>
	<div class="discuss-empty">
		<?php echo JText::_( 'COM_EASYDISCUSS_EMPTY_DISCUSSION_LIST' );?>
	</div>
	<?php } ?>
</div>