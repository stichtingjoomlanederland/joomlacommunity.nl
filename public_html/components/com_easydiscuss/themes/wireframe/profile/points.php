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
<?php foreach ($history as $group => $items) { ?>
	<div class="o-title-01 t-text--500"><?php echo $group; ?></div>

	<?php foreach ($items as $item) { ?>
	<div class="">
		<div class="t-font-size--02 t-bg--200 t-rounded--lg t-px--lg t-py--xs">
			<div class="t-d--flex">
				<div class="t-flex-grow--1 t-text--truncate">
					<i class="fas fa-certificate t-mr--sm t-text--success"></i> <?php echo $item->title; ?>
				</div>
				<div class="">
					<b><?php echo $item->points; ?></b>
				</div>
			</div>
		</div>
	</div>
	<?php } ?>
<?php } ?>