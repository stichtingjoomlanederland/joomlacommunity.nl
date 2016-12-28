<?php
/**
 * @package	 Perfect Sitemap
 * @subpackage  com_perfectsitemap
 *
 * @copyright   Copyright (C) 2016 Perfect Web Team. All rights reserved.
 * @license	 GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

// Current menu level
$level = 1;
?>

<div class="perfectsitemap">
	<?php if($this->params->get('show_page_heading', 1)): ?>
		<h2 itemprop="name" class="<?php echo $this->params->get('pageclass_sfx') ?>">
			<?php echo $this->params->get('page_heading', false) ?: JText::_('COM_PERFECT_SITEMAP_PAGE_TITLE') ?>
		</h2>
	<?php endif; ?>
	<ul>
		<?php foreach ($this->items as $item) : ?>
			<?php if ($level > $item->level) : ?>
				<?php for ($i = $item->level; $i < $level; $i++): ?>
					</ul>
				<?php endfor; ?>
			<?php endif; ?>

			<?php if ($level < $item->level): ?>
				<ul>
			<?php else: ?>
				</li>
			<?php endif; ?>

			<li><a href="<?php echo $item->link; ?>"><?php echo $item->title; ?></a>

			<?php $level = $item->level; ?>
		<?php endforeach; ?>
	</ul>
</div>
