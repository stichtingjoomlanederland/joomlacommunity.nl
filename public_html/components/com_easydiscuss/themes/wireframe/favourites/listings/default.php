<?php
/**
* @package		EasyDiscuss
* @copyright	Copyright (C) Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasyDiscuss is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');
?>
<div class="ed-favourites">
	<div class="l-stack">
		<h2 class="o-title"><?php echo JText::_('COM_EASYDISCUSS_FAVOURITES_PAGE_HEADING'); ?></h2>

		<div class="ed-posts <?php echo !$posts ? 'is-empty' : '';?>" data-ed-list-wrapper>
			<div class="ed-posts-list l-stack" data-ed-list itemscope itemtype="http://schema.org/ItemList">
				<?php echo $this->output('site/posts/list', [
					'featured' => [],
					'posts' => $posts,
					'pagination' => $pagination,
					'hideTitles' => true
				]); ?>
			</div>

			<?php echo $this->html('loading.block'); ?>
			
			<?php echo $this->html('card.emptyCard', 'far fa-newspaper', 'COM_EASYDISCUSS_NO_FAVOURITE_POSTS_YET'); ?>
		</div>
	</div>
</div>
