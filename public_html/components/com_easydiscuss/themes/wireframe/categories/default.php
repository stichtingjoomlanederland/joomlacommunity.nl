<?php
/**
* @package      EasyDiscuss
* @copyright    Copyright (C) 2010 - 2017 Stack Ideas Sdn Bhd. All rights reserved.
* @license      GNU/GPL, see LICENSE.php
* EasyDiscuss is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');
?>
<h2 class="ed-page-title"><?php echo JText::_('COM_EASYDISCUSS_CATEGORIES'); ?></h2>

<div class="ed-categories">
	<div class="ed-list">
	<?php if ($categories) { ?>
		<?php foreach ($categories as $category) { ?>
			<div class="ed-cat-panel">
				<div class="ed-cat-panel__hd">
					<?php echo $this->output('site/categories/default.item', array('category' => $category)); ?>

					<?php if ($category->totalSubcategories && $this->config->get('layout_show_all_subcategories')) { ?>
					<a data-ed-toggle="collapse" href="#collapse-cat-<?php echo $category->id; ?>" class="ed-cat-panel__toggle">
						<i class="fa fa-chevron-down"></i>
					</a>
					<?php } ?>
				</div>

				<?php if ($category->totalSubcategories && $this->config->get('layout_show_all_subcategories')) { ?>
					<div class="ed-cat-panel__bd collapse in" id="collapse-cat-<?php echo $category->id; ?>">
						<div class="ed-tree">
							<?php echo $category->printTrees($category->childs, $category->id); ?>
						</div>
					</div>
				<?php } ?>
			</div>
		<?php } ?>
	<?php } ?>
	</div>
</div>
