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
<?php foreach($tagCloud as $tag) { ?>
<li>
	<div>
		<a href="<?php echo DiscussRouter::_('index.php?option=com_easydiscuss&view=tags&id=' . $tag->id);?>">
			<!-- <i class="icon-tag"></i> -->
			<b><?php echo JText::_( $tag->title ); ?></b>
		</a>

		<?php if( $system->config->get( 'main_rss') ){ ?>
		<a href="<?php echo DiscussHelper::getHelper( 'Feeds' )->getFeedURL( 'index.php?option=com_easydiscuss&view=tags&id=' . $tag->id );?>" class="float-r">
			<span class="muted"><?php echo $tag->post_count;?></span>
			<i class="i i-rss"></i>
		</a>
		<?php } else { ?>
		<span class="float-r muted"><?php echo $tag->post_count;?></span>
		<?php } ?>
	</div>
</li>
<?php } ?>
