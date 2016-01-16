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
<script type="text/javascript">
EasyDiscuss
.require()
.script( 'categories' )
.done(function($){

	$( '.toggleCategories' ).implement( EasyDiscuss.Controller.Toggle.Categories );
});

</script>
<header>
	<h2><?php echo JText::_('COM_EASYDISCUSS_CATEGORIES_PAGE_HEADING'); ?></h2>
</header>

<article id="dc_categories">
<?php if( $categories ){ ?>
<ul class="discuss-categories reset-ul toggleCategories">

	<?php foreach( $categories as $category ){ ?>

	<?php if( $system->config->get( 'layout_category_toggle' ) ){ ?>
	<li class="<?php echo !$category->depth ? 'parent' : 'child child-' . $category->depth; ?>" style="<?php echo !$category->depth ? '' : 'display: none;' ?>" data-item data-parent-id="<?php echo $category->parent_id; ?>" data-id="<?php echo $category->id; ?>" >
	<?php }else{ ?>
	<li class="<?php echo !$category->depth ? 'parent' : 'child child-' . $category->depth; ?>" data-item data-parent-id="<?php echo $category->parent_id; ?>" data-id="<?php echo $category->id; ?>" >
	<?php } ?>
		<div class="media">
			<a href="<?php echo DiscussRouter::getCategoryRoute( $category->id );?>" class="discuss-avatar float-l">
				<img alt="<?php echo $this->escape( $category->getTitle() );?>" src="<?php echo $category->getAvatar();?>" class="avatar" />
			</a>

			<?php if( $system->config->get( 'layout_category_toggle' ) ){ ?>
				<!-- If got child then add dropdown icon-->
				<?php if( $category->getChildCount() != 0 ){ ?>
					<a class="icon-sort-down showChild btn btn-mini float-l" data-id="<?php echo $category->id; ?>"></a>
				<?php } ?>
			<?php } ?>

			<div class="media-body">
				<h3 class="reset-h">
					<a href="<?php echo DiscussRouter::getCategoryRoute( $category->id );?>"><?php echo $category->getTitle();?></a>
				</h3>

				<?php if( $category->getParam( 'show_description') && !$system->config->get( 'layout_category_description_hidden' ) && $category->description ) { ?>
					<p class="category-desc"><?php echo $category->description;?></p>
				<?php } ?>

				<?php if( !$category->container ){ ?>
					<p class="category-meta">
						<span class="category-entry-count">
							<?php echo $this->getNouns('COM_EASYDISCUSS_ENTRY_COUNT' , $category->getPostCount() , true );?>
						</span>
						<?php if( $system->config->get( 'layout_show_moderators' ) ) { ?>
						&nbsp; <b>&middot;</b> &nbsp;
						<span class="category-moderator">
							<?php echo DiscussHelper::getHelper( 'Moderator' )->showModeratorNameHTML( $category->id ); ?>
						</span>
						<?php } ?>
					</p>
					<?php if( $system->config->get( 'main_rss' ) ){ ?>
					<a href="<?php echo $category->getRSSPermalink();?>" class="butt butt-default butt-s">
						<?php echo JText::_( 'COM_EASYDISCUSS_CATEGORY_RSS_FEED');?>
					</a>
					<?php } ?>
				<?php } ?>
			</div><!--/.media-body-->
		</div><!--/.media-->
	</li>
	<?php } ?>
</ul>
<?php } else { ?>
<div class="discuss-empty">
	<?php echo JText::_('COM_EASYDISCUSS_NO_RECORDS_FOUND'); ?>
</div>
<?php } ?>
</article>