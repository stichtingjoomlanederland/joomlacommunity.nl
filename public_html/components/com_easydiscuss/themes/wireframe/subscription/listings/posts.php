<?php
/**
* @package		EasyDiscuss
* @copyright	Copyright (C) 2010 - 2015 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasyBlog is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');
?>
<?php if ($items) { ?>
	<?php foreach ($items as $post) { ?>
	<div class="o-card o-card--ed-subscriptions-post-item">
		<div class="o-card__body l-stack">
			<h2 class="o-title t-my--no">
				<a href="<?php echo $post->permalink;?>" class="si-link">
					<?php echo $post->bname;?>
				</a>
			</h2>

			<div class="o-meta l-cluster">
				<div class="">
					<div class="">
						<?php echo JText::_('COM_EASYDISCUSS_STAT_TOTAL_REPLIES'); ?>: <?php echo $post->repliesCount; ?>
					</div>
					
					<div class="">
						<?php echo ED::subscription()->html($post->userid, $post->cid, 'post'); ?>
					</div>
				</div>
			</div>
		</div>
	</div>
	<?php } ?>
	<div data-ed-subscription-pagination>
		<?php echo $pagination;?>
	</div>
<?php } ?>