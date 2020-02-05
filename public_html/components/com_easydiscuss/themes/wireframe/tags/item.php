<?php
/**
* @package		EasyDiscuss
* @copyright	Copyright (C) 2010 - 2019 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasyDiscuss is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');
?>
<div class="ed-tags-item">
	<h2><?php echo JText::sprintf('COM_EASYDISCUSS_TAG', $tagTitle); ?></h2>

	<div class="ed-posts-list" data-list-item itemscope itemtype="http://schema.org/ItemList">
		<?php if ($posts) { ?>
			<?php foreach ($posts as $post) { ?>
				<?php echo $this->output('site/posts/item', array('post' => $post)); ?>
			<?php } ?>

		<?php } else { ?>
			<div class="empty">
				<div><?php echo JText::_( 'COM_EASYDISCUSS_EMPTY_DISCUSSION_LIST' );?></div>
			</div>
		<?php } ?>
	</div>

	<div class="ed-pagination">
		<?php echo $pagination->getPagesLinks();?>
	</div>

</div>