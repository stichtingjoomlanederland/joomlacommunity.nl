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
<div id="ed" class="ed-backend si-theme--light" data-ed-wrapper>
	<?php if (!$browse) { ?>
		<div class="app-alert o-alert o-alert--danger t-hidden" data-outdated-banner>
			<div class="row-table">
				<div class="col-cell cell-tight">
					<i class="app-alert__icon fa fa-bolt"></i>
				</div>
				<div class="col-cell alert-message">
					<?php echo JText::_('COM_EASYDISCUSS_OUTDATED_VERSION');?>
				</div>
				<div class="col-cell cell-tight">
					<a href="<?php echo JRoute::_('index.php?option=com_easydiscuss&controller=system&task=upgrade');?>" class="o-btn o-btn--danger-o t-text--nowrap">
						<i class="fa fa-bolt"></i>&nbsp; <?php echo JText::_('COM_EASYDISCUSS_UPDATE_NOW_BUTTON');?>
					</a>
				</div>
			</div>
		</div>

		<?php if ($postOutOfSync) { ?>
		<div class="app-alert o-alert o-alert--warning">
			<div class="row-table">
				<div class="col-cell cell-tight">
					<i class="app-alert__icon fa fa-bolt"></i>
				</div>
				<div class="col-cell alert-message">
					<?php echo JText::_('COM_ED_MAINTENANCE_DATA_OUTOFSYNC');?>
				</div>
				<div class="col-cell cell-tight">
					<a href="<?php echo JRoute::_('index.php?option=com_easydiscuss&view=maintenance&layout=sync');?>" class="o-btn o-btn--default t-text--nowrap">
						<?php echo JText::_('COM_ED_MAINTENANCE_FIX_NOW');?>
					</a>
				</div>
			</div>
		</div>
		<?php } ?>

	<?php } ?>

	<div class="app">
		<?php if (!$browse) { ?>
			<?php echo $sidebar; ?>
		<?php } ?>

		<div class="app-content front">
			<div class="wrapper clearfix clear accordion">

				<?php if ($message) { ?>
				<?php $alertType = $message->type == 'error' ? 'danger' : $message->type; ?>
				<div class="discussNotice app-content__alert o-alert o-alert--<?php echo $alertType;?>">
					<?php echo $message->message;?>
				</div>
				<?php } ?>
				
				<?php if (!$browse) { ?>
					<?php if ($title) { ?>
					<div class="app-head">
						<h2><?php echo $title;?></h2>
						<p><?php echo $desc;?></p>
					</div>
					<?php } ?>
				<?php } ?>

				<div class="app-body">
					<?php echo $contents; ?>
				</div>

				<?php if ($help) { ?>
				<div id="help-button-template" style="display: none;">
					<div class="btn-wrapper" id="toolbar-help">
						<a href="<?php echo $help;?>" target="_blank" class="btn btn-small"><?php echo JText::_('JHELP'); ?></a>
					</div>
				</div>
				<?php } ?>
				
				<input type="hidden" class="easydiscuss-token" value="<?php echo ED::getToken();?>" data-ed-token />

				<input type="hidden" data-ed-ajax-url value="<?php echo $ajaxUrl;?>" />
			</div>
		</div>
	</div>
</div>
