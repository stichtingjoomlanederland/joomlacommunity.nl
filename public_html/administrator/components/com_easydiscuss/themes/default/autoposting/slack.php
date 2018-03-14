<?php
/**
* @package		EasyDiscuss
* @copyright	Copyright (C) 2010 - 2015 Stack Ideas Sdn Bhd. All rights reserved.
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
				<?php echo $this->html('panel.head', 'COM_EASYDISCUSS_SLACK_INTEGRATIONS'); ?>

				<div class="panel-body">
					<div class="form-horizontal">
						<div class="form-group">
							<div class="col-md-5 control-label">
								<?php echo $this->html('form.label', 'COM_EASYDISCUSS_ENABLE_SLACK'); ?>
							</div>

							<div class="col-md-7">
								<?php echo $this->html('form.boolean', 'integrations_slack', $this->config->get('integrations_slack')); ?>
							</div>
						</div>

						<div class="form-group">
							<div class="col-md-5 control-label">
								<?php echo $this->html('form.label', 'COM_EASYDISCUSS_SLACK_WEBHOOK_URL'); ?>
							</div>

							<div class="col-md-7">
								<?php echo $this->html('form.textbox', 'integrations_slack_webhook', $this->config->get('integrations_slack_webhook')); ?>
								<div class="small">
									<a href="https://stackideas.com/docs/easydiscuss/administrators/autoposting/slack-application" target="_blank"><?php echo JText::_('COM_EASYDISCUSS_WHAT_IS_THIS');?></a>
								</div>
							</div>
						</div>

						<div class="form-group">
							<div class="col-md-5 control-label">
								<?php echo $this->html('form.label', 'COM_EASYDISCUSS_SLACK_BOT_NAME'); ?>
							</div>

							<div class="col-md-7">
								<?php echo $this->html('form.textbox', 'integrations_slack_bot', $this->config->get('integrations_slack_bot')); ?>
							</div>
						</div>

						<div class="form-group">
							<div class="col-md-5 control-label">
								<?php echo $this->html('form.label', 'COM_EASYDISCUSS_SLACK_MESSAGE'); ?>
							</div>

							<div class="col-md-7">
								<?php echo $this->html('form.textarea', 'integrations_slack_message', $this->config->get('integrations_slack_message')); ?>
							</div>
						</div>

					</div>

				</div>
			</div>
		</div>
	</div>

	<input type="hidden" name="step" value="completed" />
	<input type="hidden" name="task" value="save" />
	<input type="hidden" name="type" value="slack" />
	<input type="hidden" name="controller" value="autoposting" />
	<input type="hidden" name="option" value="com_easydiscuss" />
</form>
