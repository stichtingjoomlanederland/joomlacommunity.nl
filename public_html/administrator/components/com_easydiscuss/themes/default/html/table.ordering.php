<?php
/**
* @package      EasyDiscuss
* @copyright    Copyright (C) Stack Ideas Sdn Bhd. All rights reserved.
* @license      GNU/GPL, see LICENSE.php
* EasyDiscuss is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');
?>
<div class="app-order-group">
	<div class="app-order-group__item">
		<input type="text" 
			name="order[]" 
			value="<?php echo $index; ?>" 
			class="order-value input-xsmall" disabled="disabled"
		/>
	</div>

	<?php if ($allowed) { ?>
		<div class="app-order-group__item">
			<span class="order-up">
				<?php if ($index > 1) { ?>
				<a href="javascript:void(0);" class="o-btn" data-ed-ordering data-task="orderup" data-ed-provide="tooltip" data-original-title="<?php echo JText::_('Move Up');?>">
					<i class="fa fa-chevron-up"></i>
				</a>
				<?php } else { ?>
					&#160;
				<?php } ?>
			</span>

			<span class="order-down">
				<?php if ($index < $total) { ?>
				<a href="javascript:void(0);" class="o-btn" data-ed-ordering data-task="orderdown" data-ed-provide="tooltip" data-original-title="<?php echo JText::_('Move Down');?>">
					<i class="fa fa-chevron-down"></i>
				</a>
				<?php } else { ?>
					&#160;
				<?php } ?>
			</span>
		</div>
	<?php } ?>
</div>
