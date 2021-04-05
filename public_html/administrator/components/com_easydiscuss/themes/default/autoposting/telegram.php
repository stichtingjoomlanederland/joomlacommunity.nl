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
<form name="adminForm" id="adminForm" action="index.php" method="post" class="adminForm">
	<div class="row">
		<div class="col-md-6">
			<div class="panel">
				<?php echo $this->html('panel.head', 'COM_EASYDISCUSS_TELEGRAM_INTEGRATIONS'); ?>

				<div class="panel-body">
					<?php echo $this->html('panel.info', 'COM_EASYDISCUSS_TELEGRAM_INTEGRATIONS_INFO'); ?>
					<div class="o-form-horizontal">
						<?php echo $this->html('settings.toggle', 'integrations_telegram', 'COM_EASYDISCUSS_ENABLE_TELEGRAM'); ?>
						<?php echo $this->html('settings.textbox', 'integrations_telegram_token', 'COM_EASYDISCUSS_BOT_TOKEN', '', array(), '<a href="https://stackideas.com/docs/easydiscuss/administrators/autoposting/telegram-application" target="_blank">' . JText::_('COM_EASYDISCUSS_WHAT_IS_THIS') . '</a>'); ?>
						<?php echo $this->html('settings.toggle', 'integrations_telegram_only_post_notify', 'COM_ED_TELEGRAM_ONLY_POST_NOTIFY'); ?>
						<?php echo $this->html('settings.textarea', 'integrations_telegram_message', 'COM_EASYDISCUSS_TELEGRAM_MESSAGE'); ?>
						<?php echo $this->html('settings.textarea', 'integrations_telegram_reply_message', 'COM_ED_TELEGRAM_REPLY_MESSAGE'); ?>
					</div>
				</div>
			</div>
		</div>

		<div class="col-md-6">
			<div class="panel">
				<?php echo $this->html('panel.head', 'COM_EASYDISCUSS_TELEGRAM_DISCOVER_CHATS'); ?>

				<div class="panel-body">
					<?php echo $this->html('panel.info', 'COM_EASYDISCUSS_TELEGRAM_DISCOVER_DESC');?></p>

					<div class="o-form-horizontal">
						<div class="o-form-group">
							<div class="col-md-12">
								<a href="javascript:void(0);" class="o-btn o-btn--primary" data-ed-telegram-discover>
									<i class="fab fa-telegram"></i>&nbsp; <?php echo JText::_('COM_EASYDISCUSS_TELEGRAM_DISCOVER');?>
								</a>
							</div>
						</div>

						<?php if ($this->config->get('integrations_telegram_chat_id')) { ?>
						<div class="o-form-group">
							<div class="col-md-12" data-ed-telegram-test-wrapper>
								<a href="javascript:void(0);" class="o-btn o-btn--primary-o" data-ed-telegram-test>
									<i class="fab fa-telegram"></i>&nbsp; <?php echo JText::_('COM_ED_TELEGRAM_SEND_TEST_CHAT');?>
								</a>
							</div>
						</div>
						<?php } ?>

						<div class="o-form-group t-hidden" data-ed-telegram-messages-wrapper>
							<div class="col-md-5 o-form-label">
								<?php echo $this->html('form.label', 'COM_EASYDISCUSS_TELEGRAM_CHAT'); ?>
							</div>

							<div class="col-md-7" data-ed-telegram-messages>
							</div>
						</div>

						<?php if ($this->config->get('integrations_telegram_chat_id')) { ?>
							<?php echo $this->html('settings.textbox', 'integrations_telegram_chat_id', 'COM_EASYDISCUSS_TELEGRAM_CHAT_ID', '', ['wrapperAttributes' => 'data-ed-integration-chat-id']); ?>
						<?php } ?>
					</div>
				</div>
			</div>
		</div>
	</div>

	<?php echo $this->html('form.action', 'autoposting', '', 'save'); ?>
	<input type="hidden" name="type" value="telegram" />
</form>
