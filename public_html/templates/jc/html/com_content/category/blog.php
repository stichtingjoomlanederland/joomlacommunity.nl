<?php
/**
 * @package     Joomla.Site
 * @subpackage  com_content
 *
 * @copyright   Copyright (C) 2005 - 2013 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

JHtml::addIncludePath(JPATH_COMPONENT . '/helpers');
?>

	<div class="nieuws">
		<?php foreach ($this->intro_items as $key => &$item) : ?>
			<?php
			$this->item = &$item;
			echo $this->loadTemplate('item');
			?>
		<?php endforeach; ?>
	</div>
<?php if (($this->params->def('show_pagination', 1) == 1 || ($this->params->get('show_pagination') == 2)) && ($this->pagination->get('pages.total') > 1)) : ?>
	<?php if ($this->params->def('show_pagination_results', 1)) : ?>
		<p class="counter pull-right"> <?php echo $this->pagination->getPagesCounter(); ?></p>
	<?php endif; ?>
	<?php echo $this->pagination->getPagesLinks(); ?>
<?php endif; ?>