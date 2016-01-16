<?php
/**
 * @package     Joomla.Site
 * @subpackage  mod_articles_latest
 *
 * @copyright   Copyright (C) 2005 - 2013 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;
?>
<div class="list-group list-group-flush <?php echo $moduleclass_sfx; ?>">
<?php foreach ($list as $item) :?>
	<a href="<?php echo $item->link; ?>" class="list-group-item">
      	<div class="date-icon">
      		<span class="date-day"><?php echo JHtml::_('date', $item->publish_up, JText::_('j')); ?></span><?php echo JHtml::_('date', $item->publish_up, JText::_('M')); ?>
      	</div>
		<p class="list-group-item-text"><?php echo $item->title; ?></p>
	</a>
<?php endforeach; ?>
</div>