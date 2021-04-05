<?php
/**
* @package      EasyDiscuss
* @copyright    Copyright (C) Stack Ideas Sdn Bhd. All rights reserved.
* @license      GNU/GPL, see LICENSE.php
* EasyDiscuss is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');
?>
<div class="l-stack <?php echo !$holidays ? 'is-empty' : '';?>">
	<div class="t-d--flex t-align-items--c">
		<div class="t-flex-grow--1">
		</div>
		<div>
			<a href="<?php echo EDR::_('view=dashboard&layout=holidayForm'); ?>" class="o-btn o-btn--primary">
				+&nbsp; <?php echo JText::_('COM_EASYDISCUSS_NEW_HOLIDAY');?>
			</a>
		</div>
	</div>

	<?php if ($holidays) { ?>
		<?php foreach ($holidays as $holiday) { ?>
		<div class="o-card o-card--ed-dashboard-item" data-holiday-item data-id="<?php echo $holiday->id;?>">
			<div class="o-card__body l-stack">
				<div class="t-d--flex">
					<div class="t-flex-grow--1">
						<h2 class="o-title t-my--no">
							<?php echo $holiday->title; ?>
						</h2>
					</div>

					<div>
						<div class="o-dropdown open">
							<a href="javascript:void(0);" class="t-text--500 t-text--nowrap" data-ed-toggle="dropdown">
								<i class="fas fa-ellipsis-h"></i>
							</a>
							<ul class="o-dropdown-menu o-dropdown-menu--right t-mt--2xs sm:t-w--100">
								<li>
									<a href="<?php echo EDR::_('view=dashboard&layout=holidayForm&id='.$holiday->id);?>" class="o-dropdown__item ">
										<?php echo JText::_('COM_EASYDISCUSS_HOLIDAY_EDIT_DROPDOWN'); ?>
									</a>
								</li>
								<li>
									<a href="javascript:void(0);" class="o-dropdown__item" data-delete-holiday>
										<?php echo JText::_('COM_EASYDISCUSS_HOLIDAY_DELETE_DROPDOWN'); ?>
									</a>
								</li>
							</ul>
						</div>
					</div>
				</div>

				<div class="o-body t-text--500">
					<?php echo $holiday->description; ?>
				</div>
			</div>

			<div class="o-card__footer l-stack">
				<div class="t-d--flex lg:t-align-items--c sm:t-flex-direction--c">
					<div class="t-flex-grow--1 lg:t-d--flex sm:t-mb--lg">
						<div class="lg:t-mr--sm">
							<div class="o-meta">
								<b><?php echo JText::_('COM_EASYDISCUSS_HOLIDAY_STARTS') ?>:</b> <?php echo ED::date($holiday->start)->display(JText::_('DATE_FORMAT_LC1')); ?>
							</div>
						</div>
						<div class="lg:t-mr--sm">
							<div class="o-meta">
								|
							</div>
						</div>
						<div class="lg:t-mr--sm">
							<div class="o-meta">
								<b><?php echo JText::_('COM_EASYDISCUSS_HOLIDAY_ENDS') ?>:</b> <?php echo ED::date($holiday->end)->display(JText::_('DATE_FORMAT_LC1')); ?>
							</div>
						</div>
					</div>

					<div>
						<?php echo $this->html('form.boolean', 'holiday_' . $holiday->id, $holiday->published, '', 'data-holiday-toggle data-id=' . $holiday->id); ?>
					</div>
				</div>
			</div>
		</div>
		<?php } ?>
	<?php } ?>

	<?php echo $this->html('card.emptyCard', 'fa fa-calendar-o', 'COM_EASYDISCUSS_EMPTY_HOLIDAY_LIST'); ?>
</div>