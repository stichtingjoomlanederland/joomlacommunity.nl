<?php
/**
* @package    EasyDiscuss
* @copyright  Copyright (C) Stack Ideas Sdn Bhd. All rights reserved.
* @license    GNU/GPL, see LICENSE.php
* EasyDiscuss is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');
?>
<div class="o-dropdown" data-subscription-settings="interval" data-method="updateSubscribeCount">
	<a href="javascript:void(0);" class="o-btn o-btn--default-o sm:t-d--block sm:t-mb--md" data-ed-toggle="dropdown">
		<b><?php echo JText::_('COM_EASYDISCUSS_SUBSCRIBE_POST_COUNT'); ?></b> 

		<span data-preview>
			<?php echo $subscription->count;?>
		</span> 
		<i class="fa fa-caret-down t-ml--xs"></i>
	</a>

	<ul class="o-dropdown-menu t-mt--2xs sm:t-w--100 has-active-markers">
		<?php for ($i = 5; $i <= 30; $i += 5) { ?>
		<li class="<?php echo $subscription->count == $i ? 'active' : '';?>" 
			data-settings="<?php echo $i;?>"
		>
			<a href="javascript:void(0);" class="o-dropdown__item">
				<?php echo $i;?>
			</a>
		</li>
		<?php } ?>
	</ul>
</div>