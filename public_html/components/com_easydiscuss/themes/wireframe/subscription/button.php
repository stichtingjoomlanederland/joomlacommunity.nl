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
<?php if ($subscriptionId && !$this->my->guest) { ?>
	<a href="javascript:void(0);" 
		class="<?php echo $customClass;?>"
		data-ed-unsubscribe 
		data-sid="<?php echo $subscriptionId; ?>" 
		data-type="<?php echo $type; ?>" 
		data-cid="<?php echo $cid;?>"
	>
		<?php if ($icon) { ?>
			<i class="far fa-bell-slash"></i>&nbsp; 
		<?php } ?>
		
		<?php echo JText::_('COM_EASYDISCUSS_UNSUBSCRIBE'); ?>
	</a>
<?php } else { ?>
	<a href="javascript:void(0);" 
		class="<?php echo $customClass;?>"
		data-ed-subscribe 
		data-type="<?php echo $type; ?>" 
		data-cid="<?php echo $cid;?>"
	>
		<?php if ($icon) { ?>
			<i class="far fa-bell"></i>&nbsp; 
		<?php } ?>
		
		<?php echo JText::_('COM_ED_SUBSCRIBE'); ?>
	</a>
<?php } ?>