<?php
/**
* @package		EasyDiscuss
* @copyright	Copyright (C) 2010 - 2018 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasyDiscuss is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');
?>
<div class="row">
	<div class="col-md-6">
		<div class="panel">
			<?php echo $this->html('panel.head', 'COM_EASYDISCUSS_SETTINGS_MAIN_WORK_SCHEDULE'); ?>

			<div class="panel-body">
				<div class="form-horizontal">
					<?php echo $this->html('settings.toggle', 'main_work_schedule', 'COM_EASYDISCUSS_ENABLE_WORK_SCHECULE'); ?>

					<?php echo $this->html('settings.toggle', 'main_work_displaytimediff', 'COM_ED_WORK_DISPLAY_TIME_DIFF'); ?>

					<div class="form-group">
						<div class="col-md-5 control-label">
							<?php echo $this->html('form.label', 'COM_EASYDISCUSS_ENABLE_WORK_DAYS'); ?>
						</div>
						<div class="col-md-7">
						<?php
							$days = array('mon', 'tue', 'wed', 'thu', 'fri', 'sat', 'sun');
						?>
						<?php foreach($days as $dd) { ?>
							<div class="o-checkbox">
								<input type="checkbox" id="item-checkbox-<?php echo $dd; ?>" name="main_work_<?php echo $dd; ?>" value="1"<?php echo $this->config->get('main_work_' . $dd, 0) ? ' checked="true"' : '' ?> />
								<label for="item-checkbox-<?php echo $dd; ?>">
									<?php echo JText::_('COM_EASYDISCUSS_WORK_' . strtoupper($dd)); ?>
								</label>
							</div>
						<?php } ?>
						</div>
					</div>
					<div class="form-group">
						<div class="col-md-5 control-label">
							<?php echo $this->html('form.label', 'COM_EASYDISCUSS_ENABLE_WORK_HOURS'); ?>
						</div>

						<?php
							$hours = array();
							$minutes = array();
							for ($i = 0; $i <= 23; $i++) {
								$hours[$i] = str_pad($i, 2, '0', STR_PAD_LEFT);
							}

							for ($i = 0; $i <= 59; $i++) {
								$minutes[$i] = str_pad($i, 2, '0', STR_PAD_LEFT);
							}
						?>

						<div class="col-md-7">
							<div class="o-flag t-lg-mb--md">
								<div class="o-flag__image">
									<div style="width: 40px">
										<label for="start-hours"><?php echo JText::_('COM_EASYDISCUSS_WORK_FROM'); ?></label>
									</div>
								</div>
								<div class="o-flag__body">
									<select name="main_work_starthour" class="form-control" id="start-hours" style="width:auto;display: inline-block;">
										<?php foreach($hours as $hh => $hlabel) { ?>
										<option value="<?php echo $hlabel; ?>"<?php echo ($this->config->get('main_work_starthour') == $hlabel) ? ' selected="true"' : ''; ?>><?php echo $hlabel; ?></option>
										<?php } ?>
									</select>

									<select name="main_work_startminute" class="form-control" id="start-minutes" style="width:auto;display: inline-block;">
										<?php foreach($minutes as $mm => $mlabel) { ?>
										<option value="<?php echo $mlabel; ?>"<?php echo ($this->config->get('main_work_startminute') == $mlabel) ? ' selected="true"' : ''; ?>><?php echo $mlabel; ?></option>
										<?php } ?>
									</select>
								</div>
							</div>

							<div class="o-flag t-lg-mb--md">
								<div class="o-flag__image">
									<div style="width: 40px">
										<label for="end-hours"><?php echo JText::_('COM_EASYDISCUSS_WORK_TILL'); ?></label>
									</div>
								</div>
								<div class="o-flag__body">

									<select name="main_work_endhour" class="form-control" id="end-hours" style="width:auto;display: inline-block;">
										<?php foreach($hours as $hh => $hlabel) { ?>
										<option value="<?php echo $hlabel; ?>"<?php echo ($this->config->get('main_work_endhour') == $hlabel) ? ' selected="true"' : ''; ?>><?php echo $hlabel; ?></option>
										<?php } ?>
									</select>

									<select name="main_work_endminute" class="form-control" id="end-minutes" style="width:auto;display: inline-block;">
										<?php foreach($minutes as $mm => $mlabel) { ?>
										<option value="<?php echo $mlabel; ?>"<?php echo ($this->config->get('main_work_endminute') == $mlabel) ? ' selected="true"' : ''; ?>><?php echo $mlabel; ?></option>
										<?php } ?>
									</select>
								</div>
							</div>

						</div>
					</div>

					<div class="form-group">
						<div class="col-md-5 control-label">
							<?php echo $this->html('form.label', 'COM_EASYDISCUSS_WORK_HOUR_DISPLAY_FORMAT'); ?>
						</div>
						<div class="col-md-7">

							<div class="o-radio">
								<input type="radio" id="item-radio-12h" name="main_work_hourformat" value="12"<?php echo $this->config->get('main_work_hourformat', 12) == '12' ? ' checked="true"' : ''; ?> />
								<label for="item-radio-12h">
									<?php echo JText::_('COM_EASYDISCUSS_WORK_12H'); ?>
								</label>
							</div>

							<div class="o-radio">
								<input type="radio" id="item-radio-24h" name="main_work_hourformat" value="24"<?php echo $this->config->get('main_work_hourformat', 12) == '24' ? ' checked="true"' : ''; ?> />
								<label for="item-radio-24h">
									<?php echo JText::_('COM_EASYDISCUSS_WORK_24H'); ?>
								</label>
							</div>

						</div>
					</div>

					<div class="form-group">
						<div class="col-md-12">&nbsp;</div>
						<div class="col-md-12 ml">
							<div class="o-alert o-alert--info">
								<?php echo JText::_('COM_EASYDISCUSS_WORK_NOTES');?>
							</div>
						</div>
					</div>

					<!-- end -->
				</div>
			</div>
		</div>
	</div>

	<div class="col-md-6">

	</div>
</div>