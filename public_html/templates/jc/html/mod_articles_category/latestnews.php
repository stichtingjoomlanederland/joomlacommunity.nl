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

<h3><?php echo $module->title; ?></h3>
<div class="list-group list-group-flush <?php echo $moduleclass_sfx; ?>">
	<?php foreach ($list as $item) : ?>
		<?php $images = json_decode($item->images); ?>
        <a class="list-group-item" href="<?php echo $item->link; ?>">
			<?php if (isset($images->image_intro) && !empty($images->image_intro) and file_exists($images->image_intro)): ?>
                <div class="news-image">
                    <img src="<?php echo $images->image_intro; ?>"/>
                </div>
			<?php endif; ?>
            <p class="list-group-item-meta">
                <strong><?php echo JHtml::_('date', $item->publish_up, JText::_('j M Y')); ?></strong> door <?php echo $item->displayAuthorName; ?>
            </p>
            <h4 class="list-group-item-heading">
				<?php echo $item->title; ?>
            </h4>
            <p class="list-group-item-text">
				<?php echo strip_tags($item->displayIntrotext); ?>
            </p>
        </a>
	<?php endforeach; ?>
</div>
<a href="<?php echo JRoute::_('index.php?Itemid=240'); ?>" class="btn btn-nieuws btn-block">
    Meer Joomla-nieuws
</a>