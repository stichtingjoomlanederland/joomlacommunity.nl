<?php
/**
* @package		EasyDiscuss
* @copyright	Copyright (C) 2010 - 2019 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasyDiscuss is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');
?>

<div class="ed-badges-wrapper">
	<div class="l-stack">
		<h2 class="o-title"><?php echo $title; ?></h2>
		<?php if ($badges) { ?>	
			<?php foreach ($badges as $badge) { ?>
			<div class="ed-badges__item" style="flex-basis:33%">
				<?php echo $this->html('card.badge', $badge); ?>
			</div>
			<?php } ?>
		<?php } ?>

		<?php if (!$badges) { ?>
			<div class="is-empty">
				<div class="o-empty o-empty--bordered">
					<div class="o-empty__content">
						<i class="o-empty__icon fa fa-book"></i>
						<div class="o-empty__text">
							<?php echo JText::_('COM_EASYDISCUSS_NO_BADGES_CREATED'); ?>
						</div>
					</div>
				</div>
			</div>
		<?php } ?>
	</div>
</div>