<?php
/**
* @package      EasyDiscuss
* @copyright    Copyright (C) 2010 - 2016 Stack Ideas Sdn Bhd. All rights reserved.
* @license      GNU/GPL, see LICENSE.php
* EasyDiscuss is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Restricted access');
?>
<div id="ed" class="ed-mod m-similar-discussions <?php echo $params->get('moduleclass_sfx') ?>">
<?php if ($posts) { ?>
	<div class="ed-list--vertical has-dividers--bottom-space">
		<?php foreach ($posts as $post) { ?>

		<?php
		$post_title = (JString::strlen($post->title) > $params->get('max_title', 50))? substr($post->title, 0, $params->get('max_title', 50)) . '...' : $post->title;
		?>
		<div class="ed-list__item">
			<a class="m-post-title" href="<?php echo $post->permalink;?>">
				<?php echo $post->title; ?>
			</a>

			<div class="m-post-meta">
				<a href="<?php echo EDR::getCategoryRoute($post->category_id); ?>"> <?php echo $post->category_name; ?></a>
			</div>
			<div class="m-post-meta">
				<i class="fa fa-clock-o"></i> <?php echo $post->duration; ?>
			</div>
		</div>
		<?php } ?>
	</div>
<?php } else { ?>
	<div class="no-item">
		<?php echo JText::_('MOD_EASYDISCUSS_SIMILAR_DISCUSSIONS_NO_ENTRIES'); ?>
	</div>
<?php } ?>
</div>
