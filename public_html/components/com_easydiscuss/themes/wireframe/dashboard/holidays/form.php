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
<div class="ed-holidays">
	<?php if ($holiday->id) { ?>
	<h2 class="o-title"><?php echo JText::_('COM_EASYDISCUSS_EDIT_HOLIDAY'); ?></h2>
	<?php } else { ?>
	<h2 class="o-title"><?php echo JText::_('COM_EASYDISCUSS_NEW_HOLIDAY'); ?></h2>
	<?php } ?>

	<form autocomplete="off" action="<?php echo JRoute::_('index.php');?>" method="post" enctype="multipart/form-data" data-ed-holiday-form>
		<div class="o-card o-card--ed-dashboard-form">
			<div class="o-card__body">
				<div class="ed-dashboard-form__hd">
					<div class="t-d--flex">
						<div class="t-ml--auto t-d--flex t-align-items--c">
							<label class="t-mr--sm" for=""><?php echo JText::_('COM_EASYDISCUSS_HOLIDAY_PUBLISH_FIELD'); ?>:</label>

							<?php echo $this->html('form.boolean', 'published', $holiday->published); ?>
						</div>
					</div>
					
				</div>

				<div class="ed-dashboard-form__bd">
					<div class="o-form-group">
						<label for="title"><?php echo JText::_('COM_EASYDISCUSS_HOLIDAY_TITLE_FIELD'); ?></label>
						<?php echo $this->html('form.textbox', 'title', $this->html('string.escape', $holiday->title), 'COM_ED_HOLIDAY_TITLE_PLACEHOLDER'); ?>
					</div>

					<div class="o-form-group">
						<label for="exampleInputEmail1"><?php echo JText::_('COM_EASYDISCUSS_HOLIDAY_DESCRIPTION_FIELD'); ?></label>
						<?php echo $this->html('form.textarea', 'description', $this->html('string.escape', $holiday->description), null, 'placeholder="' . JText::_('COM_ED_HOLIDAY_DESC_PLACEHOLDER') . '"'); ?>
					</div>

					<div class="lg:o-grid lg:o-grid--gutters">
						<div class="lg:o-grid__cell">
							<div class="o-form-group ed-dashboard-j-calendar">
								<div class="o-grid">
									<div class="o-grid__cell o-grid__cell--auto-size">
										<label for="start" class="t-lg-mt--md"><?php echo JText::_('COM_EASYDISCUSS_HOLIDAY_START_DATE_FIELD'); ?>:</label>
									</div>
									<div class="o-grid__cell">
										<?php echo JHTML::_('calendar', $holiday->start, 'start', 'start', '%Y-%m-%d', array('data-ed-holiday-start' => '', 'class' => 'o-form-control')); ?>
									</div>
								</div>
							</div>
						</div>
						<div class="lg:o-grid__cell">
							<div class="o-form-group ed-dashboard-j-calendar">
								<div class="o-grid">
									<div class="o-grid__cell o-grid__cell--auto-size t-lg-pr--lg">
										<label for="end" class="t-lg-mt--md"><?php echo JText::_('COM_EASYDISCUSS_HOLIDAY_END_DATE_FIELD'); ?>:</label>
									</div>
									<div class="o-grid__cell">
										<?php echo JHTML::_('calendar', $holiday->end, 'end', 'end', '%Y-%m-%d', array('data-ed-holiday-end' => '', 'class' => 'o-form-control')); ?>
									</div>
								</div>
							</div>
						</div>
					</div>

					<div class="t-d--flex ">
						<div class="t-ml--auto">
							<a href="<?php echo EDR::_('view=dashboard'); ?>" class="o-btn o-btn--default-o t-mr--sm"><?php echo JText::_('COM_EASYDISCUSS_BUTTON_CANCEL'); ?></a>

							<button class="o-btn o-btn--primary">
								<?php echo JText::_('COM_ED_SAVE_BUTTON'); ?>
							</button>
						</div>
					</div>
				</div>
			</div>
			
				
			<?php echo $this->html('form.action', 'holiday', 'dashboard', 'save'); ?>
			<input type="hidden" name="id" id="id" value="<?php echo $holiday->id; ?>" />
		</div>
	</form>
</div>