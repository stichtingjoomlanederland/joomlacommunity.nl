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
<div class="ed-badges-entry">
	<?php echo $this->html("card.badge", $badge); ?>

	<h4><?php echo JText::_('COM_EASYDISCUSS_BADGE_ACHIEVERS');?></h4>

	<div class="<?php echo !$users ? 'is-empty' : '';?>">
		<?php if ($users) { ?>
		<div class="ed-achievers l-cluster">
			<div>
				<?php foreach ($users as $user) { ?>
				<div class="ed-achievers__item">
					<div class="ed-achiever">
						<?php echo $this->html('user.avatar', $user, ['status' => true, 'size' => 'md']); ?>
					</div>
				</div>
				<?php } ?>
			</div>
		</div>
		<?php } ?>

		<?php echo $this->html('card.emptyCard', 'fa-id-badge', 'COM_EASYDISCUSS_BADGES_NO_USERS'); ?>
	</div>
</div>