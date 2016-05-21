<?php
/**
 * @package     Joomla.Site
 * @subpackage  mod_articles_category
 *
 * @copyright   Copyright (C) 2005 - 2016 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

?>
<div class="list-group list-group-flush">
	<?php foreach ($list as $item) : ?>
		<div class="list-group-item">
			<?php $images = json_decode($item->images);
			if (isset($images->image_intro) && !empty($images->image_intro) and file_exists($images->image_intro)) :
				$image_intro_alt = $images->image_intro_alt ? $images->image_intro_alt : $item->title;
				echo JHtml::_('image', $images->image_intro, $image_intro_alt, array('class' => 'news-image'));
			endif; ?>
			<div class="media-body">
				<h4 class="list-group-item-heading">
					<?php echo JHtml::_('link', $item->link, $item->title, array('class' => 'list-group-item-anchor')); ?>
					<?php if ($item->displayDate) : ?>
						<small><?php echo $item->displayDate; ?></small>
					<?php endif; ?>
				</h4>
				<?php if ($params->get('show_introtext')) : ?>
					<p class="list-group-item-text"><?php echo strip_tags($item->displayIntrotext); ?></p>
				<?php endif; ?>

			</div>
		</div>
	<?php endforeach; ?>
</div>
