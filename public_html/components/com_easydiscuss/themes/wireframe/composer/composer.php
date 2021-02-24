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
<div data-ed-composer-wrapper
	 <?php echo $editorId;?>
	 data-editortype="<?php echo $composer->editorType ?>"
	 data-operation="<?php echo $composer->operation; ?>"
	 data-replies-order="<?php echo $this->config->get('layout_replies_sorting'); ?>"
	 data-ed-editor-wrapper
	 data-ed-editor-uuid="<?php echo $editorUuid; ?>"
>
	<div role="alert" class="o-alert t-mb--md t-hidden" data-ed-composer-alert></div>
	<div role="labelalert" class="o-alert t-mb--md t-hidden" data-ed-composer-labelalert></div>

	<div class="ed-reply-form t-lg-mt--lg <?php echo $composer->classname; ?>">

		<div class="ed-reply-form__bd l-stack">
			<?php if ($operation == 'replying') { ?>
			<div class="ed-reply-form__title">
				<div class="o-media">
					<div class="o-media__body">
						<b><?php echo ($operation == 'replying') ? JText::_('COM_EASYDISCUSS_ENTRY_YOUR_RESPONSE') : JText::_('COM_EASYDISCUSS_EDIT_YOUR_RESPONSE'); ?></b>
					</div>
				</div>
			</div>
			<?php } ?>

			<form data-ed-composer-form name="dc_submit" autocomplete="off" class="l-stack" action="<?php echo JRoute::_('index.php');?>" method="post">

				<?php if ($operation == 'replying' && $this->config->get('main_anonymous_posting')) { ?>
					<?php echo $composer->renderAnonymousField();?>
				<?php } ?>

				<?php if (!$this->my->id) { ?>
					<?php echo $composer->renderNameField('reply');?>
				<?php } ?>

				<div class="ed-editor">
					<div class="ed-editor-widget t-pt--no">
						<?php echo $composer->renderEditor(); ?>
					</div>

					<?php if ($hasTabs) { ?>
					<div class="ed-editor-widget t-pt--no">
						<?php echo $composer->renderTabs($currentCatId); ?>
					</div>
					<?php } ?>

					<?php if ($captcha->enabled() && $operation != 'editing') { ?>
						<?php echo $composer->renderCaptcha($captcha); ?>
					<?php } ?>

					<div class="ed-editor__ft">

						<?php if ($this->config->get('main_tnc_reply')) { ?>
							<?php echo $composer->renderTnc('reply');?>
						<?php } ?>

						<div class="t-ml--auto">
							<?php if ($operation == 'editing') { ?>
								<button type="button" class="o-btn o-btn--link t-mr--sm" data-ed-reply-cancel><?php echo JText::_('COM_EASYDISCUSS_BUTTON_CANCEL');?></button>
							<?php } ?>

							<button type="button" class="o-btn o-btn--primary" data-ed-reply-submit>
								<?php if ($operation == 'replying') { ?>
									<?php echo JText::_('COM_EASYDISCUSS_BUTTON_SUBMIT_RESPONSE'); ?>
								<?php } else { ?>
									<?php echo JText::_('COM_EASYDISCUSS_BUTTON_UPDATE_REPLY'); ?>
								<?php } ?>
							</button>
						</div>
						
					</div>
				</div>

				<?php echo $this->html('form.honeypot'); ?>
				
				<?php if ($post->id) { ?>
					<?php echo $this->html('form.hidden', 'id', $post->id); ?>
				<?php } ?>

				<?php if ($operation == "editing") { ?>
					<?php echo $this->html('form.hidden', 'seq', $post->seq); ?>
				<?php } ?>

				<?php echo $this->html('form.hidden', 'parent_id', $parent->id); ?>
			</form>
		</div>
	</div>
</div>