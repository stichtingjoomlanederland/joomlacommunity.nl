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
<div id="ed" class="ed-mod ed-mod--operationhour <?php echo $lib->getModuleWrapperClass();?>">
	<div class="ed-mod-card"
		data-operationhour
		data-operationhour-servertz="<?php echo $servertz;?>"
		data-operationhour-hourformat="<?php echo $lib->config->get('main_work_hourformat');?>"
	>
			<div class="ed-mod-card__body">
				<div class="l-stack l-spaces--xs t-text--center">

					<div class="o-title t-text--<?php echo $status == 'online' ? 'success': 'danger'; ?>">
						<?php echo JText::_("MOD_ED_OPERATIONHOUR_SUPPORT_" . $status); ?>
					</div>

					<div class="o-body t-text--500">
						<?php echo JText::_($params->get($status . '_message')); ?>
					</div>
					<div class="o-body">
						<div class=""><?php echo JText::_('MOD_ED_OPERATIONHOUR_OFFICIAL_WORKING_HOUR');?></div>
						<div class="t-font-weight--bold">
							<?php echo $options['workDayLabel']; ?> <?php echo ($options['workExceptionLabel']) ? $options['workExceptionLabel'] : ''; ?>
						</div>
						<div class="t-font-weight--bold">
							<?php echo $options['workTimeLabel']; ?>
						</div>
					</div>
				</div>
			</div>
			<div class="ed-mod-card__footer">
				<div class="lg:o-grid lg:o-grid--gutters t-mb--no">
					
					<?php if ($lib->config->get('main_work_displaytimediff', 0)) { ?>
					<div class="lg:o-grid__cell lg:o-grid__cell-- sm:t-mb--md">
						<div class="t-font-size--02 t-bg--100 t-rounded--lg t-px--lg t-py--xs t-text--center">
							<div class="t-font-weight--bold">
								<?php echo JText::_('MOD_ED_OPERATIONHOUR_YOUR_TIME'); ?>
							</div>
							<div class="">
								<span data-operationhour-day><?php echo $date->format('l', true); ?></span>,

								<?php if ($lib->config->get('main_work_hourformat') == '12') { ?>
									<span data-user-hour>&mdash;</span>:
									<span data-user-minute>&mdash;</span>:
									<span data-user-second>&mdash;</span>
									<span data-user-meridiem>&mdash;</span>
								<?php } ?>

								<?php if ($lib->config->get('main_work_hourformat') == '24') { ?>
									<span data-user-hour>&mdash;</span>:
									<span data-user-minute>&mdash;</span>:
									<span data-user-second>&mdash;</span>
								<?php } ?>
							</div>
						</div>
					</div>
					<?php } ?>

					<div class="lg:o-grid__cell lg:o-grid__cell-- sm:t-mb--md">
						<div class="t-font-size--02 t-bg--100 t-rounded--lg t-px--lg t-py--xs t-text--center">
							<div class="t-font-weight--bold">
								<?php echo JText::_('MOD_ED_OPERATIONHOUR_OUR_TIME'); ?>
							</div>
							<div class="">
								<span data-operationhour-day><?php echo $date->format('l', true); ?></span>,

								<?php if ($lib->config->get('main_work_hourformat') == '12') { ?>
									<span data-server-hour><?php echo $date->format('g', true);?></span>:
									<span data-server-minute><?php echo $date->format('i', true);?></span>:
									<span data-server-second><?php echo $date->format('s', true);?></span>
									<span data-server-meridiem><?php echo $date->format('A', true);?></span>
								<?php } ?>

								<?php if ($lib->config->get('main_work_hourformat') == '24') { ?>
									<span data-server-hour><?php echo $date->format('H', true);?></span>:
									<span data-server-minute><?php echo $date->format('i', true);?></span>:
									<span data-server-second><?php echo $date->format('s', true);?></span>
								<?php } ?>
							</div>
						</div>
					</div>
					
				</div>
			</div>
	</div>
</div>

<script type="text/javascript">
	ed.require(['edq', 'easydiscuss', 'site/src/operationhour'], function($, EasyDiscuss) {
		var days = [
					'<?php echo JText::_('SUNDAY', true); ?>',
					'<?php echo JText::_('MONDAY', true); ?>',
					'<?php echo JText::_('TUESDAY', true); ?>',
					'<?php echo JText::_('WEDNESDAY', true); ?>',
					'<?php echo JText::_('THURSDAY', true); ?>',
					'<?php echo JText::_('FRIDAY', true); ?>',
					'<?php echo JText::_('SATURDAY', true); ?>'
			];

		var currentTime = new Date();
		var currentDay = days[currentTime.getDay()];

		$('[data-operationhour-day]').html(currentDay);
	});
</script>