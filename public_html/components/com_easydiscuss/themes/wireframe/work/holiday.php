<?php
/**
* @package		EasyDiscuss
* @copyright	Copyright (C) 2010 - 2018 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasyDiscuss is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');
?>
<div class="popbox-holiday-wrap">
	<i class="popbox-holiday-wrap__icon fa fa-coffee"></i>

	<div class="popbox-holiday-wrap__title">
		<?php echo $holiday->title; ?>
	</div>
	<div class="popbox-holiday-wrap__subtitle">
		<?php echo JText::sprintf('COM_EASYDISCUSS_WORK_HOLIDAY_DESC', $holiday->title); ?>
	</div>
</div>