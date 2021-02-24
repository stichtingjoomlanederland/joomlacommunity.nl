<?php
/**
* @package		EasyDiscuss
* @copyright	Copyright (C) Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasyDiscuss is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');
?>
<div id="ed" class="t-px--lg t-py--md <?php echo $lib->getModuleWrapperClass();?>" 
	data-ed-filter 
	data-type="category"
>
	<div class="ed-mod t-bg--200">
		<div class="ed-side-menu">
			<?php foreach ($filters as $filter) { ?>
			<div class="ed-side-menu__item" data-filter="<?php echo $filter->id;?>">
				<a href="javascript:void(0);" class="ed-side-menu__link">
					<?php echo $lib->html('category.identifier', $filter); ?>&nbsp; <?php echo $filter->title;?>
				</a>

				<?php if ($filter->hasChildren()) { ?>
				<a href="javascript:void(0);" class="ed-side-menu__toggle">
					<i class="fas fa-angle-right"></i>
				</a>
				<?php } ?>
			</div>
			<?php } ?>
		</div>
	</div>
</div>