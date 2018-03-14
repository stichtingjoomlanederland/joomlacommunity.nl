<?php
/**
* @package		EasyDiscuss
* @copyright	Copyright (C) 2010 - 2017 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasyDiscuss is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');
?>
<form name="adminForm" id="adminForm" action="index.php" method="post" class="adminForm">
	<div class="row">
		<div class="col-md-6">
			<div class="panel">
				<?php echo $this->html('panel.head', 'COM_EASYDISCUSS_TELEGRAM_INTEGRATIONS'); ?>

				<div class="panel-body">
					<?php echo $this->html('panel.info', 'COM_EASYDISCUSS_TELEGRAM_INTEGRATIONS_INFO'); ?>
					<div class="form-horizontal">
						<div class="form-group">
							<div class="col-md-5 control-label">
								<?php echo $this->html('form.label', 'COM_EASYDISCUSS_ENABLE_TELEGRAM'); ?>
							</div>

							<div class="col-md-7">
								<?php echo $this->html('form.boolean', 'integrations_telegram', $this->config->get('integrations_telegram')); ?>
							</div>
						</div>

						<div class="form-group">
							<div class="col-md-5 control-label">
								<?php echo $this->html('form.label', 'COM_EASYDISCUSS_BOT_TOKEN'); ?>
							</div>

							<div class="col-md-7">
								<?php echo $this->html('form.textbox', 'integrations_telegram_token', $this->config->get('integrations_telegram_token')); ?>
								<div class="small">
									<a href="https://stackideas.com/docs/easydiscuss/administrators/autoposting/telegram-application" target="_blank"><?php echo JText::_('COM_EASYDISCUSS_WHAT_IS_THIS');?></a>
								</div>
							</div>
						</div>

						<div class="form-group">
							<div class="col-md-5 control-label">
								<?php echo $this->html('form.label', 'COM_EASYDISCUSS_TELEGRAM_MESSAGE'); ?>
							</div>

							<div class="col-md-7">
								<?php echo $this->html('form.textarea', 'integrations_telegram_message', $this->config->get('integrations_telegram_message')); ?>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>

		<div class="col-md-6">
			<div class="panel">
				<?php echo $this->html('panel.head', 'COM_EASYDISCUSS_TELEGRAM_DISCOVER_CHATS'); ?>

				<div class="panel-body">
					<?php echo $this->html('panel.info', 'COM_EASYDISCUSS_TELEGRAM_DISCOVER_DESC');?></p>

					<div class="form-horizontal">
						<div class="form-group">
							<div class="col-md-12">
								<a href="javascript:void(0);" class="btn btn-primary btn-sm" data-ed-telegram-discover>
									<i class="fa fa-globe"></i>&nbsp; <?php echo JText::_('COM_EASYDISCUSS_TELEGRAM_DISCOVER');?>
								</a>
							</div>
						</div>

						<div class="form-group hide" data-ed-telegram-messages-wrapper>
							<div class="col-md-5 control-label">
								<?php echo $this->html('form.label', 'COM_EASYDISCUSS_TELEGRAM_CHAT'); ?>
							</div>

							<div class="col-md-7" data-ed-telegram-messages>
							</div>
						</div>
					</div>

					<?php if ($this->config->get('integrations_telegram_chat_id')) { ?>
					<div class="form-horizontal">
						<div class="form-group">
							<div class="col-md-5 control-label">
								<?php echo $this->html('form.label', 'COM_EASYDISCUSS_TELEGRAM_CHAT_ID'); ?>
							</div>

							<div class="col-md-7" data-ed-telegram-messages>
								<?php echo $this->html('form.textbox', 'integrations_telegram_chat_id', $this->config->get('integrations_telegram_chat_id')); ?>
							</div>
						</div>
					</div>
					<?php } ?>
				</div>
			</div>
		</div>
	</div>

	<input type="hidden" name="step" value="completed" />
	<input type="hidden" name="task" value="save" />
	<input type="hidden" name="type" value="telegram" />
	<input type="hidden" name="controller" value="autoposting" />
	<input type="hidden" name="option" value="com_easydiscuss" />
</form>
