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
<div class="o-empty <?php echo !$fixedHeight ? 'o-empty--height-no' : '';?>">
	<div class="o-card o-card--ed-empty-section">
		<div class="o-card__body">
			<div class="">
				<?php if ($icon) { ?>
				<i class="o-empty__icon t-text--info fa <?php echo $icon;?>"></i>
				<?php } ?>
				<div class="o-empty__text"><?php echo JText::_($text); ?></div>
			</div>
		</div>
	</div>
</div>
