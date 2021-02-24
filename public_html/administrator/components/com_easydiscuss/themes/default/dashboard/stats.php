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
<a href="<?php echo $permalink;?>" class="db-post-item">
	<div class="t-flex-grow--1 t-min-width--0 t-pr--lg">
			<div class="o-media">
				<div class="o-media__image">
					<i class="<?php echo $icon;?> t-text--success"></i>
				</div>
				<div class="o-media__body">
					<div class="t-text--truncate">
						<?php echo JText::_($title);?>
					</div>
				</div>
			</div>
	</div>
	<div class="t-ml--auto sm:t-d--none lg:t-d--block">
		<div class="">
			<b><?php echo $count;?></b>
		</div>
	</div>
</a>