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

<h2><?php echo $module->title; ?></h2>
<div class="list-group list-group-flush panel-agenda">
	<?php foreach ($list as $item) : ?>
		<?php
		$images = json_decode($item->images);
		if (isset($images->image_intro) && !empty($images->image_intro) and file_exists($images->image_intro))
		{
			$image_intro_alt = $images->image_intro_alt ? $images->image_intro_alt : $item->title;
			echo JHtml::_('image', $images->image_intro, $image_intro_alt, array('class' => 'news-image'));
		}
		?>
        <a class="list-group-item" href="<?php echo $item->link; ?>">
            <div class="date-icon">
                <span class="date-day"><?php echo JHtml::_('date', $item->publish_up, JText::_('j')); ?></span><?php echo JHtml::_('date', $item->publish_up, JText::_('M')); ?>
            </div>
            <h4 class="list-group-item-heading">
				<?php echo $item->title; ?>
            </h4>
            <p class="list-group-item-text">
				<?php echo strip_tags($item->displayIntrotext); ?>
            </p>
            <small>
                <i class="fa fa-user" aria-hidden="true"></i> <?php echo $item->displayAuthorName; ?>
            </small>
        </a>
	<?php endforeach; ?>
</div>
<a href="<?php echo JRoute::_(ContentHelperRoute::getCategoryRoute($list[0]->catslug)); ?>" class="btn btn-agenda btn-block">
    Meer verslagen
</a>