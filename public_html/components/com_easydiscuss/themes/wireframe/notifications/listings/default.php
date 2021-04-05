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
<div class="ed-noti l-stack">
	<div class="t-d--flex t-align-items--c">
		<div class="t-flex-grow--1 t-min-width--0 t-pr--md">
			<h2 class="o-title"><?php echo JText::_('COM_EASYDISCUSS_ALL_NOTIFICATIONS'); ?></h2>
		</div>

		<?php if ($notifications) { ?>
		<div class="t-flex-shrink--0">
			<a href="javascript:void(0)" data-ed-notifications-read-all class="o-btn o-btn--default-o">
				<?php echo JText::_('COM_EASYDISCUSS_MARK_AS_READ'); ?>
			</a>
		</div>
		<?php } ?>
	</div>

	<div class="<?php echo !$notifications ? 'is-empty' : '';?> ed-noti l-stack">
		<?php if ($notifications) { ?>	
			<?php foreach ($notifications as $day => $data) { ?>
				<?php echo $this->output('site/notifications/listings/item', [
					'day' => $day,
					'data' => $data
				]); ?>
			<?php } ?>
		<?php } ?>
		<?php echo $this->html('card.emptyCard', 'fa fa-bell', 'COM_EASYDISCUSS_NOTIFICATIONS_ALL_CAUGHT_UP'); ?>
	</div>
</div>

<?php if (isset($pagination)) { ?>
	<div class="ed-pagination">
		<?php echo $pagination;?>
	</div>
<?php } ?>
