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
<div class="o-popbox-content__ft t-border--600">
	<div class="t-p--md">
		<a href="javascript:void(0);" 
			class="o-btn o-btn--primary o-btn--block"
			onclick="<?php echo CMessaging::getPopup($targetId);?>"
		>
			<i class="fa fa-envelope"></i>&nbsp; <?php echo JText::_('COM_ED_SEND_MESSAGE');?>
		</a>	
	</div>
</div>