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

<?php if( $system->config->get( 'layout_show_classic' ) ){ ?>
<div class="discuss-categories-front mt-5 mb-5">
	<ul class="discuss-categories-list unstyled">
		<?php foreach( $categories as $category ){ ?>
			<li class="list-item">
				<div>
					<?php if( $system->config->get( 'main_rss' ) ){ ?>
						<a href="<?php echo $category->getRSSPermalink(); ?>" class="category-rss" rel="ed-tooltip" data-placement="top" data-original-title="<?php echo JText::_( 'COM_EASYDISCUSS_SUBSCRIBE_VIA_RSS' , true );?>">
							<?php echo JText::_( 'COM_EASYDISCUSS_SUBSCRIBE_VIA_RSS' ); ?>
						</a>
					<?php } ?>
					
					<a href="<?php echo DiscussRouter::getCategoryRoute( $category->id );?>" class="category-name fsg fwb">
						<?php echo $category->title; ?>
					</a>

					<span class="small"><?php echo JText::sprintf( 'COM_EASYDISCUSS_ENTRY_COUNT_PLURAL', $category->getPostCount() ); ?></span>
				</div>
			</li>
		<?php } ?>
	</ul>
</div>
<?php } ?>

