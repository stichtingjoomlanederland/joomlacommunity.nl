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
<div class="ed-noti-list l-stack l-spaces--lg">
	<div class="ed-noti-date t-text--500"><?php echo $day; ?></div>

	<?php foreach ($data as $item) { ?>
		<div class="o-card o-card--ed-notification-item l-spaces--sm is-<?php echo $item->state == DISCUSS_NOTIFICATION_READ ? 'read' : 'unread';?>" data-ed-notifications-item>
			<div class="o-card__body">
				<div class="t-d--flex">
					<div class="t-flex-grow--1 t-min-width--0">
						<div class="o-media o-media--top">
							<div class="o-media__image">
								<?php echo $this->html('user.avatar', $item->authorProfile, array('size' => 'sm')); ?>
							</div>

							<div class="o-media__body l-stack">
								<div class="ed-user-name  t-text--700 t-text--wrap">
									<?php echo $item->title;?>
								</div>

								<div class="o-meta l-cluster l-spaces--sm">
									<div>
										<div>
											<a href="<?php echo $item->permalink;?>">
												<?php echo $item->touched; ?>
											</a>
										</div>

										<?php if ($item->state != DISCUSS_NOTIFICATION_READ) { ?>
											<div class="t-font-size--03 t-font-weight--bold t-text--500">·</div>
											<div>
												<a href="<?php echo EDR::_('controller=notification&task=markread&id=' . $item->id); ?>">
													<?php echo JText::_('COM_EASYDISCUSS_MARK_AS_READ'); ?>
												</a>
											</div>
										<?php } ?>
									</div>
								</div>
							</div>
						</div>
					</div>
					<div class="t-flex-shrink--0">
						<div class="ed-state-unread">
							<i></i>
						</div>
					</div>
				</div>			
			</div>
		</div>
	<?php } ?>
</div>