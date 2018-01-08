<?php
/**
* @package      EasyDiscuss
* @copyright    Copyright (C) 2010 - 2016 Stack Ideas Sdn Bhd. All rights reserved.
* @license      GNU/GPL, see LICENSE.php
* Komento is free software. This version may have been modified pursuant
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
				<?php echo $this->html('panel.head', 'COM_EASYDISCUSS_MIGRATORS_KUNENA_DETAILS'); ?>

				<div id="option01" class="panel-body">
					<div class="">
						<?php if ($exists) { ?>
						<div class="form-group">
							<p><?php echo JText::_('COM_EASYDISCUSS_MIGRATORS_NOTICE');?></p>
							
							<ul>
								<li><?php echo JText::_('COM_EASYDISCUSS_MIGRATORS_NOTICE_BACKUP'); ?></li>
								<li><?php echo JText::_('COM_EASYDISCUSS_MIGRATORS_NOTICE_OFFLINE'); ?></li>
							</ul>
						</div>

						<div class="form-group">
							<div class="col-md-5 control-label">
								<?php echo $this->html('form.label', 'COM_EASYDISCUSS_MIGRATORS_RESET_HITS'); ?>
							</div>
							<div class="col-md-7">
								<?php echo $this->html('form.boolean', 'migrator_reset_points', $this->config->get('migrator_reset_points'), '', 'data-migrator-kunena-hits');?>
							</div>
						</div>

						<div class="form-group">
							<div class="col-md-5 control-label">
								<?php echo $this->html('form.label', 'COM_EASYDISCUSS_MIGRATORS_USER_SIGNATURE'); ?>
							</div>
							<div class="col-md-7">
								<?php echo $this->html('form.boolean', 'migrator_user_signature', $this->config->get('migrator_user_signature'), '', 'data-migrator-kunena-signature');?>
							</div>
						</div>

						<div class="form-group">
							<div class="col-md-5 control-label">
								<?php echo $this->html('form.label', 'COM_EASYDISCUSS_MIGRATORS_USER_AVATAR'); ?>
							</div>
							<div class="col-md-7">
								<?php echo $this->html('form.boolean', 'migrator_user_avatar', $this->config->get('migrator_user_avatar'), '', 'data-migrator-kunena-avatar');?>
							</div>
						</div>

						<button class="btn btn-success" data-ed-migrate>
							<?php echo JText::_('COM_EASYDISCUSS_MIGRATOR_MIGRATE'); ?>
						</button>

						<?php } else { ?>
							<p><?php echo JText::_('COM_EASYDISCUSS_MIGRATORS_KUNENA_NOT_INSTALLED'); ?></p>
						<?php } ?>
					</div>
				</div>
			</div>
		</div>

		<div class="col-md-6">
			<div class="panel">
				<div class="panel-head">
					<b><?php echo JText::_('COM_EASYDISCUSS_PROGRESS');?></b>
					<span data-progress-loading class="eb-loader-o size-sm hide"></span>
				</div>

				<div class="panel-body">
					<div data-progress-empty><?php echo JText::_('COM_EASYDISCUSS_MIGRATOR_NO_PROGRESS_YET'); ?></div>
					<div data-progress-status style="overflow:auto; height:98%;max-height: 300px;"></div>
				</div>
			</div>
		</div>
	</div>

