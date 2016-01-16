<?php
/**
 * @package		EasyBlog
 * @copyright	Copyright (C) 2010 Stack Ideas Private Limited. All rights reserved.
 * @license		GNU/GPL, see LICENSE.php
 *
 * EasyBlog is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * See COPYRIGHT.php for copyright notices and details.
 */

// no direct access
defined('_JEXEC') or die('Restricted access');
?>
<li class="<?php echo !$category->depth ? 'parent' : 'child-' . $category->depth; ?>" 
	style="<?php echo !$category->depth ? '' : 'display: none;' ?>" 
	data-ed-parent-id="<?php echo $category->parent_id; ?>" 
	data-id="<?php echo $category->id; ?>"
>
	<div class="item"<?php echo ( $params->get( 'layouttype' ) == 'tree' ) ? ' style="padding-left: ' . $padding . 'px;"' : '';?>>

		<?php if ($params->get('showcavatar', true)) : ?>
		<a href="<?php echo DiscussRouter::_('index.php?option=com_easydiscuss&view=categories&layout=listings&category_id='.$category->id ); ?>" class="item-avatar float-l">
			<img src="<?php echo modEasydiscussCategoriesHelper::getAvatar($category); ?>" width="34" height="34" alt="<?php echo DiscussStringHelper::escape($category->title); ?>" class="avatar" />
		</a>
		<?php endif; ?>

		<div class="item-story">
			<?php if( !empty( $category->childs ) ){ ?>
			<a href="javascript:void(0);" class="show-item" data-id="<?php echo $category->id;?>" data-show-child>
				<i class="icon-sort-down"></i>
			</a>
			<a href="javascript:void(0);" class="hide-item" data-id="<?php echo $category->id;?>" data-hide-child>
				<i class="icon-sort-up"></i>
			</a>
			<?php } ?>

			<a class="item-title bold" href="<?php echo DiscussRouter::_('index.php?option=com_easydiscuss&view=categories&layout=listings&category_id='.$category->id ); ?>"><?php echo DiscussStringHelper::escape($category->title); ?></a>

			<?php if ($params->get('showpostcnt', true)) { ?>
				<div class="item-meta small"><?php echo JText::sprintf('MOD_DISCUSSIONSCATEGORIES_COUNT', (int) $category->discussioncount);?></div>
			<?php } ?>
		</div>
		<div style="clear:both;"></div>
	</div>
</li>
