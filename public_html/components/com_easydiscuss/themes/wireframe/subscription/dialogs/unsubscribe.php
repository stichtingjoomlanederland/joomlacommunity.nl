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
	<width>400</width>
	<height>150</height>
	<selectors type="json">
	{
		"{closeButton}" : "[data-close-button]",
		"{submitButton}" : "[data-submit-button]",
		"{content}": "[data-dialog-content]"
	}
	</selectors>
	<bindings type="javascript">
	{
		"{closeButton} click": function() {
			this.parent.close();
		},

		"{submitButton} click": function(element) {
			var content = this.content();
			var button = EasyDiscuss.$(element);

			button.addClass('is-loading');

			EasyDiscuss.ajax('site/views/subscription/unsubscribe', {
				"type": '<?php echo $type; ?>',
				"cid": '<?php echo $cid; ?>',
				"sid": '<?php echo $sid; ?>'
			})
			.done(function(html) {

				content.html(html);

				// refresh the page in next second
				setTimeout(function(){
					window.location.reload(1);
				}, 1000);
			});
		}
	}
	</bindings>
	<title><?php echo JText::_('COM_EASYDISCUSS_UNSUBSCRIBE_TO_' . strtoupper($type)); ?></title>
	<content>
		<p data-dialog-content>
			<?php echo JText::_('COM_EASYDISCUSS_UNSUBSCRIBE_TO_' . strtoupper($type) . '_DESC'); ?>
		</p>
	</content>
	<buttons>
		<button data-close-button type="button" class="ed-dialog-footer-content__btn"><?php echo JText::_('COM_EASYDISCUSS_BUTTON_CANCEL'); ?></button>
		<button data-submit-button type="button" class="ed-dialog-footer-content__btn t-text--primary"><?php echo JText::_('COM_EASYDISCUSS_BUTTON_UNSUBSCRIBE'); ?></button>
	</buttons>
</dialog>
