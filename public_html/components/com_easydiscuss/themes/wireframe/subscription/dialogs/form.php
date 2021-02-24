<?php
/**
* @package      EasyDiscuss
* @copyright    Copyright (C) Stack Ideas Sdn Bhd. All rights reserved.
* @license      GNU/GPL, see LICENSE.php
* EasyDiscuss is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');
?>
<dialog>
	<width>500</width>
	<height><?php echo (!$this->my->guest) ? '200' : '400'; ?></height>
	<selectors type="json">
	{
		"{closeButton}" : "[data-close-button]",
		"{form}" : "[data-form-response]",
		"{submitButton}" : "[data-submit-button]",
		"{alert}": "[data-subscribe-message]",
		"{email}": "[data-subscribe-email]",
		"{name}": "[data-subscribe-name]"
	}
	</selectors>
	<bindings type="javascript">
	{
		"{closeButton} click": function() {
			this.parent.close();
		},

		"{submitButton} click": function() {

			var name = this.name().val();
			var email = this.email().val();
			var alert = this.alert();

			if (name == "" || email == "") {
				alert.removeClass('t-d--none');
				return false;
			}

			this.alert().addClass('t-d--none');

			var options = {
				"type": '<?php echo $type; ?>',
				"cid": '<?php echo $cid; ?>',
				"subscribe_email": email,
				"subscribe_name": name
			};

			EasyDiscuss.ajax('site/views/subscription/process', options)
			.done(function(message, manageLink) {
				
				// Render a new set of dialog
				EasyDiscuss.dialog({
					content: EasyDiscuss.ajax('site/views/subscription/postProcess', {
						contents: message,
						manageLink: manageLink
					})
				});
			})
			.fail(function(message) {
				alert.html(message);
				alert.removeClass('t-d--none');
			});
		}
	}
	</bindings>
	<title><?php echo JText::_('COM_EASYDISCUSS_SUBSCRIBE_TO_' . strtoupper($type)); ?></title>
	<content>
		<p class="t-mb--lg">
			<?php echo JText::_('COM_EASYDISCUSS_SUBSCRIBE_' . strtoupper($type) . '_DESCRIPTION');?>
		</p>

		<div class="t-text--danger t-mb--md t-d--none" data-subscribe-message>
			<?php echo JText::_('COM_EASYDISCUSS_SUBSCRIBE_PLEASE_ENTER_NAME_EMAIL'); ?>
		</div>

		<?php if ($this->my->id) { ?>
		<div class="t-d--flex sm:t-flex-direction--c">
			<div class="lg:t-w--25 sm:t-mb--sm">
				<label class="o-form-label"><?php echo JText::_('COM_ED_SUBSCRIBE_EMAIL_ADDRESS');?>:</label>
			</div>
			<div class="lg:t-w--75">
				<b><?php echo $this->my->email; ?></b>
			</div>
			<input type="hidden" data-subscribe-email value="<?php echo $this->my->email; ?>">
			<input type="hidden" data-subscribe-name value="<?php echo $this->my->name; ?>">
		</div>
		<?php } ?>

		<?php if (!$this->my->id) { ?>
		<div class="l-stack">
			<div class="">
				<label for="subscribe_email" class="o-form-label"><?php echo JText::_('COM_ED_SUBSCRIBE_EMAIL_ADDRESS');?></label>
				
				<input type="text" id="subscribe_email" name="subscribe_email" value="" class="o-form-control" data-subscribe-email />
				
				<div id="emailHelp" class="o-form-text"><?php echo JText::_('COM_ED_SUBSCRIBE_YOUR_EMAIL_HELP');?></div>
			</div>

			<div class="">
				<label for="subscribe_name" class="o-form-label"><?php echo JText::_('COM_ED_SUBSCRIBE_NAME');?></label>
				
				<input type="text" id="subscribe_name" name="subscribe_name" value="" class="o-form-control" data-subscribe-name />
				
				<div id="emailHelp" class="o-form-text"><?php echo JText::_('COM_ED_SUBSCRIBE_NAME_HELP');?></div>
			</div>
		</div>
		<?php } ?>
	</content>
	<buttons>
		<button data-close-button type="button" class="ed-dialog-footer-content__btn"><?php echo JText::_('COM_EASYDISCUSS_BUTTON_CANCEL'); ?></button>
		<button data-submit-button type="button" class="ed-dialog-footer-content__btn t-text--primary"><?php echo JText::_('COM_EASYDISCUSS_BUTTON_SUBSCRIBE'); ?></button>
	</buttons>
</dialog>
