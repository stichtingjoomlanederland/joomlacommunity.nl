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

$input = JFactory::getApplication()->input;
$category = $input->get('category_id', '', 'int');
?>
<div id="ed" class="ed-mod ed-mod--ask <?php echo $lib->getModuleWrapperClass();?>">
	<div class="ed-mod-card">
		<div class="ed-mod-card__body">
			<?php if ($params->get('onlinestate', 1) && $work->enabled()) { ?>
			<div class="o-title ed-mod-ask__status is-<?php echo strtolower($status); ?>">
				<?php echo JText::_('COM_EASYDISCUSS_SUPPORT_IS_CURRENTLY'); ?>
				<span class=""><?php echo $status; ?></span>
			</div>
			<?php } ?>
			<?php if ($params->get('workschedule', 1) && $work->enabled()) { ?>
				<div class="ed-mod-ask__support-msg">
					<div class=""><?php echo JText::_('COM_EASYDISCUSS_WORK_OFFICIAL_WORKING_HOURS'); ?></div>
					<div class="">
						<?php echo $options['workDayLabel']; ?> <?php echo ($options['workExceptionLabel']) ? $options['workExceptionLabel'] : ''; ?><br />
						<?php echo $options['workTimeLabel']; ?>
					</div>
				</div>
			<?php } ?>
			<div class="ed-mod-ask__action">
				<a class="o-btn o-btn--primary t-d--block" href="<?php echo EDR::_('index.php?option=com_easydiscuss&view=ask&category=' . $category);?>">
					<span><?php echo JText::_('MOD_ASK_POST_QUESTION');?></span>
				</a>
			</div>
		</div>
	</div>
</div>
