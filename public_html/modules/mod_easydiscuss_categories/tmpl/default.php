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
<div id="ed" class="ed-mod ed-mod--categories <?php echo $lib->getModuleWrapperClass();?>">
	<div class="ed-mod-card">
		<div class="ed-mod-card__body">
			<?php if ($categories) { ?>
				<?php foreach ($categories as $category) { ?>
					<?php require(JModuleHelper::getLayoutPath('mod_easydiscuss_categories', 'tree_item')); ?>
				<?php } ?>
			<?php } else { ?>
				<div class="o-card t-bg--100">
					<div class="o-card__body l-stack">
						<?php echo JText::_('MOD_DISCUSSIONSCATEGORIES_NO_ENTRIES'); ?>
					</div>
				</div>
			<?php } ?>
		</div>
	</div>
</div>