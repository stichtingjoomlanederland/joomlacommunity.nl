<?php
/**
* @package      EasyDiscuss
* @copyright    Copyright (C) 2010 - 2018 Stack Ideas Sdn Bhd. All rights reserved.
* @license      GNU/GPL, see LICENSE.php
* EasyDiscuss is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');
?>
<div class="o-nav__item" data-ed-popbox data-ed-popbox-toggle="click" 
	data-ed-popbox-position="<?php echo JFactory::getDocument()->getDirection() == 'rtl' ? 'bottom-left' : 'bottom-right';?>" 
	data-ed-popbox-offset="2" 
	data-ed-popbox-type="navbar-support" 
	data-ed-popbox-component="popbox--navbar" 
	data-ed-popbox-target="[data-ed-support-dropdown]"
>
	<a href="javascript:void(0);" class="o-nav__link ed-toolbar__link no-active-state">
		<div class="ed-toolbar-status-indicator is-<?php echo $isOnline ? 'online' : 'offline';?>">
			<span class="ed-toolbar-status-indicator__content">
				<i class="ed-toolbar-status-indicator__icon"></i> <span class="ed-toolbar-status-indicator__text"><?php echo $label; ?></span>
			</span> 
		</div>
	</a>
	
	<div class="t-hidden" data-ed-support-dropdown>
		<div class="popbox popbox--work-status">
			<div class="ed-work-status-wrap">
				<div class="ed-work-status-wrap">
					<div class="ed-work-status is-<?php echo $isOnline ? 'online' : 'offline';?>">
						<div class="ed-work-status__info">
							<?php echo $this->output($namespace); ?>

							<div class="popbox-holiday-wrap__note t-lg-mt--md">
								<div class="popbox-holiday-wrap__note-title"><?php echo JText::_('COM_EASYDISCUSS_WORK_OFFICIAL_WORKING_HOURS'); ?></div>
								<div class="popbox-holiday-wrap__note-time">
									<?php echo $workDayLabel; ?> <?php echo ($workExceptionLabel) ? $workExceptionLabel : ''; ?><?php echo !$isEverydayWork ? '<br />' : ' '; ?>
									<?php echo $workTimeLabel; ?>
								</div>
							</div>
						</div>

						<?php if ($this->config->get('main_work_displaytimediff')) { ?>
						<div class="ed-work-status-compare">
							<div class="ed-work-status-compare__info">
								<div class="ed-work-status-compare__title">
									<?php echo JText::_('COM_ED_WORK_YOUR_TIME');?>
								</div>
								<div class="ed-work-status-compare__day" data-user-day>
									&nbsp;
								</div>
								<div class="ed-work-status-compare__time">
									<?php if ($this->config->get('main_work_hourformat') == '12') { ?>
										<span data-user-hour>&mdash;</span>:
										<span data-user-minute>&mdash;</span>:
										<span data-user-second>&mdash;</span>
										<span data-user-meridiem>&mdash;</span>
									<?php } ?>

									<?php if ($this->config->get('main_work_hourformat') == '24') { ?>
										<span data-user-hour>&mdash;</span>:
										<span data-user-minute>&mdash;</span>:
										<span data-user-second>&mdash;</span>
									<?php } ?>
								</div>
							</div>
							<div class="ed-work-status-compare__info">
								<div class="ed-work-status-compare__title">
									<?php echo JText::_('COM_ED_WORK_OUR_TIME');?>
								</div>
								<div class="ed-work-status-compare__day">
									<?php echo $date->format('l', true); ?>
								</div>
									
								<div class="ed-work-status-compare__time">
									<?php if ($this->config->get('main_work_hourformat') == '12') { ?>
										<span data-server-hour><?php echo $date->format('g', true);?></span>:
										<span data-server-minute><?php echo $date->format('i', true);?></span>:
										<span data-server-second><?php echo $date->format('s', true);?></span>
										<span data-server-meridiem><?php echo $date->format('A', true);?></span>
									<?php } ?>

									<?php if ($this->config->get('main_work_hourformat') == '24') { ?>
										<span data-server-hour><?php echo $date->format('H', true);?></span>:
										<span data-server-minute><?php echo $date->format('i', true);?></span>:
										<span data-server-second><?php echo $date->format('s', true);?></span>
									<?php } ?>
								</div>
							</div>
						</div>
						<?php } ?>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>